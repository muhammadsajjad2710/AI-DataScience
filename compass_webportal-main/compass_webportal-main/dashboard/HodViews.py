from django.shortcuts import render, redirect
from django.http import HttpResponse, HttpResponseRedirect, JsonResponse, HttpResponseNotAllowed
from django.contrib import messages
from django.core.files.storage import FileSystemStorage #To upload Profile Picture
from django.urls import reverse
from django.views.decorators.csrf import csrf_exempt
from django.core import serializers
from .models import CompassCredentials
from .models import Contract_Details
from .models import SuperannuationDetails

from .models import Units_Details
from .models import NonSuperannuable
from .models import DataReport, ContractedUDAsDetails
from django.db.models import Sum
import json
from django.views.decorators.http import require_http_methods
from dashboard.models import CustomUser, Staffs, Contract, Performer, DataReport
# import pyodbc
import sys



from datetime import datetime
from math import isnan
from django.db.models import Q
from django.http import JsonResponse
from django.views.decorators.http import require_POST
from django.shortcuts import get_object_or_404
from datetime import date, timedelta
def admin_home(request):
    all_contracts_count = Contract.objects.all().count()
    all_performers_count = Performer.objects.all().count()
    all_actvity_count = DataReport.objects.all().count()
    all_failed_count = DataReport.objects.filter(Q(Status='Deleted') | Q(Status='Failed Validation')).count()

    context={
        "all_contracts_count": all_contracts_count,
        "all_performers_count": all_performers_count,
        "all_actvity_count": all_actvity_count,
        "all_failed_count": all_failed_count,
    }
    return render(request, "hod_template/home_content.html", context)




@require_POST
def get_performer_data(request):
    performer_id = request.POST.get('performer_id')
    data_reports = DataReport.objects.filter(PerformerID=performer_id)[:10]
    serialized_data = [
        {
            'id': data_report.id,
            'CRN_IN': data_report.CRN_IN,
            'ContractID': data_report.ContractID,
            'PerformerID': data_report.PerformerID,
            'Errors': data_report.Errors,
            'PatientSurname': data_report.PatientSurname,
            'PatientForename': data_report.PatientForename,
            'DateOfBirth': data_report.DateOfBirth,
            'TreatmentStartDate': data_report.TreatmentStartDate,
            'TreatmentEndDate': data_report.TreatmentEndDate,
            'PatientCharge': data_report.PatientCharge,
            'Period': data_report.Period,
            'Units': data_report.Units,
            'ContraPeriod_Units_PCharge': data_report.ContraPeriod_Units_PCharge,
            'NHSNumber': data_report.NHSNumber,
            'ProcessingDate': data_report.ProcessingDate,
            'Status': data_report.Status,
            'FormType': data_report.FormType,
            'CreatedAmendedDeletedby': data_report.CreatedAmendedDeletedby,
            'CreationAmendDeletionDate': data_report.CreationAmendDeletionDate,
        }
        for data_report in data_reports
    ]

    return JsonResponse({'data_reports': serialized_data})



#migratedata()
def activity_details(request):
    #migratedata()
    all_performers = Performer.objects.all()
    start_date = request.POST.get('start_date', '')
    end_date = request.POST.get('end_date', '')
    contract_id = request.POST.get('contract_id', '')

    if request.method == 'POST':
        # Process the POST request data if needed
        start_date_str = start_date
        end_date_str = end_date

        if start_date_str and end_date_str:
            try:
                # Convert string inputs to datetime objects
                start_date = datetime.strptime(start_date_str, '%Y-%m-%d')
                end_date = datetime.strptime(end_date_str, '%Y-%m-%d')

                # Filter data using the date range and Contract ID
                data_reports = DataReport.objects.filter(
                    ProcessingDate__range=[start_date, end_date],
                    ContractID=contract_id
                )[:10]
            except ValueError:
                # Handle invalid date format
                data_reports = None
        else:
            # If no date range provided, handle as needed
            data_reports = None  # For example, you might set data_reports to some default value or display an error message
    else:
        # If it's not a POST request, fetch the top 10 rows
        data_reports = DataReport.objects.all()[:10]

    context = {
        "all_performers" : all_performers,
        "data_reports": data_reports,
        "start_date": start_date,
        "end_date": end_date,
        "contract_id": contract_id,
    }
    return render(request, "hod_template/activity_details_template.html", context)


def performer_details(request):
    Performers = Performer.objects.all()
    context = {
        "performers": Performers
    }
    return render(request, "hod_template/performer_details_template.html", context)

def contract_details_template(request):
    contracts = Contract.objects.all()
    context = {"contracts": contracts}
    return render(request, "hod_template/contract_details_template.html", context)

def add_contract(request):
    return render(request, "hod_template/add_contract.html")

def add_contract_save(request):
    if request.method == 'POST':
        contract_data = request.POST
        try:
            Contract.objects.create(
                Contract_ID=contract_data['Contract_ID'],
                Contract_UDAs=contract_data['Contract_UDAs'],
                Commissioner=contract_data['Commissioner'],
                ContractType=contract_data['ContractType'],
                Start_Date=contract_data['Start_Date'],
                End_Date=contract_data['End_Date'],
                Status=contract_data['Status']
            )
            messages.success(request, "Contract Added Successfully!")
            return redirect('contract_details_template')

        except Exception as e:
            messages.error(request, f"Failed to Add Contract: {str(e)}")
    return render(request, "hod_template/add_contract.html")

def edit_contract(request, contract_id):
    contract = get_object_or_404(Contract, id=contract_id)
    if request.method == "POST":
        contract_data = request.POST
        try:
            contract.Contract_ID = contract_data['Contract_ID']
            contract.Contract_UDAs = contract_data['Contract_UDAs']
            contract.Commissioner = contract_data['Commissioner']
            contract.ContractType = contract_data['ContractType']
            contract.Start_Date = contract_data['Start_Date']
            contract.End_Date = contract_data['End_Date']
            contract.Status = contract_data['Status']
            contract.save()
            messages.success(request, "Contract details updated successfully!")
            # Redirect to the appropriate URL or view name
            return redirect('contract_details_template')
        except Exception as e:
            messages.error(request, f"Failed to update contract details: {str(e)}")
    return render(request, "hod_template/edit_contract_details.html", {'contract': contract})


def delete_contract(request, contract_id):
    if request.method == "POST" or request.method == "GET":
        contract = get_object_or_404(Contract, id=contract_id)
        contract.delete()
        messages.success(request, "Contract deleted successfully!")
    return redirect('contract_details_template')  # Redirect to contract details page

def compass_credential(request):
    credentials = CompassCredentials.objects.all()
    return render(request, 'hod_template/compass_credential.html', {'credentials': credentials})

def add_credential(request):
    if request.method == "POST":
        credential_data = request.POST
        try:
            credential = CompassCredentials.objects.create(
                compass_username=credential_data['compass_username'],
                compass_password=credential_data['compass_password'],
                compass_memorableword=credential_data['compass_memorableword']
            )
            messages.success(request, "Credentials Added Successfully!")
            return redirect('compass_credential')  # Redirect to compass_credential view
        except Exception as e:
            messages.error(request, f"Failed to Add Credentials: {str(e)}")
    return render(request, "hod_template/add_credential.html")

def add_save_credential(request):
    if request.method == "POST":
        credential_data = request.POST
        try:
            credential = CompassCredentials.objects.create(
                compass_username=credential_data['compass_username'],
                compass_password=credential_data['compass_password'],
                compass_memorableword=credential_data['compass_memorableword']
            )
            messages.success(request, "Credentials Added Successfully!")
            return redirect('compass_credential')  # Redirect to compass_credential view
        except Exception as e:
            messages.error(request, f"Failed to Add Credentials: {str(e)}")
    return render(request, "hod_template/add_credential.html")


def edit_credential(request, credential_id):
    credential = get_object_or_404(CompassCredentials, id=credential_id)
    if request.method == "POST":
        credential_data = request.POST
        try:
            credential.compass_username = credential_data['compass_username']
            credential.compass_password = credential_data['compass_password']
            credential.compass_memorableword = credential_data['compass_memorableword']
            credential.save()
            messages.success(request, "Credentials updated successfully!")
            return redirect('compass_credential')
        except Exception as e:
            messages.error(request, f"Failed to update credentials: {str(e)}")
    return render(request, "hod_template/edit_credential.html", {'credential': credential})


def delete_credential(request, credential_id):
    if request.method == "POST" or request.method == "GET":
        credential = get_object_or_404(CompassCredentials, id=credential_id)
        credential.delete()
        messages.success(request, "Credentials deleted successfully!")
        return redirect('compass_credential')  # Redirect to compass_credential page
    else:
        # If the request method is not POST or GET, return a response indicating that the method is not allowed
        return HttpResponseNotAllowed(['POST', 'GET']) # type: ignore

@csrf_exempt
def check_email_exist(request):
    email = request.POST.get("email")
    user_obj = CustomUser.objects.filter(email=email).exists()
    if user_obj:
        return HttpResponse(True)
    else:
        return HttpResponse(False)


@csrf_exempt
def check_username_exist(request):
    username = request.POST.get("username")
    user_obj = CustomUser.objects.filter(username=username).exists()
    if user_obj:
        return HttpResponse(True)
    else:
        return HttpResponse(False)




def admin_profile(request):
    user = CustomUser.objects.get(id=request.user.id)

    context={
        "user": user
    }
    return render(request, 'hod_template/admin_profile.html', context)


def admin_profile_update(request):
    if request.method != "POST":
        messages.error(request, "Invalid Method!")
        return redirect('admin_profile')
    else:
        first_name = request.POST.get('first_name')
        last_name = request.POST.get('last_name')
        password = request.POST.get('password')

        try:
            customuser = CustomUser.objects.get(id=request.user.id)
            customuser.first_name = first_name
            customuser.last_name = last_name
            if password != None and password != "":
                customuser.set_password(password)
            customuser.save()
            messages.success(request, "Profile Updated Successfully")
            return redirect('admin_profile')
        except:
            messages.error(request, "Failed to Update Profile")
            return redirect('admin_profile')
    






# SQL Server connection details with Windows authentication
# Database connection variables (commented out since we're using SQLite)
# server_name = 'IMRANKHAN\\SQLEXPRESS'  # Note the double backslash
# database_name = 'postgres'
# conn_str = f'DRIVER={{SQL Server}};SERVER={server_name};DATABASE={database_name};Trusted_Connection=yes;'




# def migratedata():
#     # Connect to SQL Server
#     sql_server_connection = pyodbc.connect(conn_str)
#     sql_server_cursor = sql_server_connection.cursor()
#     # Fetch data from data_report table
#     sql_server_cursor.execute('SELECT * FROM data_report')
#     data_report_rows = sql_server_cursor.fetchall()

#     # Insert data into DataReport model
#     for row in data_report_rows:
#         print(row)
#         data_report_instance = DataReport(
#         CRN_IN=str(row[0]),  # Update with the actual index of CRN_IN column
#         ContractID=str(row[1]),  # Update with the actual index of ContractID column
#         PerformerID=str(row[2]),  # Update with the actual index of PerformerID column
#         Errors=str(row[3]),  # Update with the actual index of Errors column
#         PatientSurname=str(row[4]),  # Update with the actual index of PatientSurname column
#         PatientForename=str(row[5]),  # Update with the actual index of PatientForename column
#         DateOfBirth=datetime.strptime(row[6], '%d/%m/%Y') if row[6] and row[6] != 'nan' else None,
#         TreatmentStartDate=datetime.strptime(row[7], '%d/%m/%Y') if row[7] and row[7] != 'nan' else None,
#         TreatmentEndDate=datetime.strptime(row[8], '%d/%m/%Y') if row[8] and row[8] != 'nan' else None,
#         PatientCharge=str(row[9]),  # Update with the actual index of PatientCharge column
#         Period=str(row[10]),  # Update with the actual index of Period column
#         Units=str(row[11]),  # Update with the actual index of Units column
#         ContraPeriod_Units_PCharge=str(row[12]),  # Update with the actual index
#         NHSNumber=str(row[13]),  # Update with the actual index of NHSNumber column
#         ProcessingDate=datetime.strptime(row[14], '%d/%m/%Y') if row[14] and row[14] != 'nan' else None,
#         Status=str(row[15]),  # Update with the actual index of Status column
#         FormType=str(row[16]),  # Update with the actual index of FormType column
#         CreatedAmendedDeletedby=str(row[17]),
#         CreationAmendDeletionDate=datetime.strptime(row[18], '%d/%m/%Y %H:%M:%S') if row[18] and row[18] != 'nan' else None,
#     )
#         data_report_instance.save()

#     sql_server_cursor.execute('SELECT * FROM Performers')
#     performers_rows = sql_server_cursor.fetchall()

#             # Insert data into Performer model
#     for row in performers_rows:
#         performer_instance = Performer(
#                     Performer_ID=row[0],  # Update with the actual index of Performer_ID column
#                     Surname=row[1],  # Update with the actual index of Surname column
#                     Forename=row[2],  # Update with the actual index of Forename column
#                     GDC_Number=row[3],  # Update with the actual index of GDC_Number column
#                 )
#         performer_instance.save()
      
#             # Fetch data from contract_id table
#     sql_server_cursor.execute('SELECT * FROM contract_id')
#     contract_rows = sql_server_cursor.fetchall()

#             # Insert data into Contract model
#     for row in contract_rows:
#         print(row)
#         contract_instance = Contract(
#                     Contract_ID=row[0],  # Update with the actual index of Contract_ID column
#                     Commissioner=row[1],  # Update with the actual index of Commissioner column
#                     ContractType=row[2],  # Update with the actual index of Provider column
#                     Start_Date=row[3],  # Update with the actual index of Provider_Type column
#                     End_Date=row[4],  # Update with the actual index of Start_Date column
#                     Status=row[5],  # Update with the actual index of End_Date column
#                 )
#         contract_instance.save()
#     # Close connections
#     sql_server_cursor.close()
#     sql_server_connection.close()
    


def sum_units_by_contract(request):
    if request.method == 'POST' and request.headers.get('x-requested-with') == 'XMLHttpRequest':
        contract_id = request.POST.get('contract_id')
        if contract_id:
            # Define the date range
            start_date = datetime(2024, 4, 1)
            end_date = datetime(2025, 3, 31)

            # Sum the units within the date range and with Status 'Validated'
            total_units = DataReport.objects.filter(
                ContractID=contract_id,
                Status='Validated',
                ProcessingDate__range=[start_date, end_date]
            ).aggregate(total_units=Sum('Units'))['total_units'] or 0

            # Format the total_units with commas
            formatted_units = "{:,}".format(total_units)

            # Debug statement to print the sum of units
            print(f"Sum of validated units for contract_id {contract_id} between {start_date} and {end_date}: {formatted_units}")

            return JsonResponse({'total_units': formatted_units})
    return JsonResponse({'error': 'Invalid request'}, status=400)

def calculate_udas(contract_info, current_date):
    total_days = 365  # Total days in the period from 1st April 2024 to 31st March 2025
    off_days = 38  # Total OFF days in the period
    remaining_days = total_days - off_days  # Remaining working days
    workdays_per_week = 4  # Workdays per week
    available_workdays = remaining_days // 7 * workdays_per_week  # Total available workdays
    
    total_udas = float(contract_info.get('target_udas', 0))
    uda_target_per_working_day = total_udas / available_workdays

    labels = []
    data_uda_target_curve = []
    data_today_line = []
    
    cumulative_udas = 0
    start_date = datetime(2024, 4, 1)

    while start_date < datetime(2025, 4, 1):
        day_of_week = start_date.weekday()
        if day_of_week < 4:  # Monday to Thursday are working days
            cumulative_udas += uda_target_per_working_day

        labels.append(start_date.strftime("%d/%m/%Y"))
        data_uda_target_curve.append(round(cumulative_udas, 2))
        
        if start_date.date() == current_date.date():
            data_today_line = [cumulative_udas] * len(labels)  # 'Today' line is flat at today's cumulative value

        start_date += timedelta(days=1)
    
    return {
        'labels': labels,
        'data_uda_target_curve': data_uda_target_curve,
        
    }

def dashboard(request):
    today = datetime.today()
    start_date = today - timedelta(days=14)
    end_date = today + timedelta(days=6)

    labels = [(start_date + timedelta(days=i)).strftime('%d/%m/%Y') for i in range(21)]

    contract_ids = ContractedUDAsDetails.objects.values_list('contract_id', flat=True).distinct()

    contracted_udas_details = ContractedUDAsDetails.objects.all()

    contract_data = {
        detail.contract_id: {
            'contracted_udas': int(detail.contracted_udas),
            'carry_over_udas': int(detail.carry_over_udas),
            'target_udas': int(detail.contracted_udas) + int(detail.carry_over_udas),
        }
        for detail in contracted_udas_details
    }

    total_days = 365
    off_days = 38
    remaining_days = total_days - off_days
    workdays_per_week = 4
    available_workdays = remaining_days // 7 * workdays_per_week

    if request.method == 'POST' and request.headers.get('x-requested-with') == 'XMLHttpRequest':
        data = json.loads(request.body)
        contract_id = data.get('contract_id')
        contract_info = contract_data.get(contract_id, {})

        total_udas = float(contract_info.get('target_udas', 0))
        uda_target_per_working_day = total_udas / available_workdays

        target_data = []
        for i in range(21):
            current_date = start_date + timedelta(days=i)
            if current_date.weekday() < workdays_per_week:
                target_data.append(uda_target_per_working_day)
            else:
                target_data.append(0)  # For weekends, show zero target UDAs

        predicted_data = [10, 20, 30, 40, 50, 60, 70, 80, 90, 100, 110, 120, 130, 140, 150, 160, 170, 180, 190, 200, 210]

        return JsonResponse({
            'predicted_data': predicted_data,
            'target_data': target_data
        })

    else:
        contract_info = contract_data.get(contract_ids[0], {}) if contract_ids else {}

        total_udas = float(contract_info.get('target_udas', 0))
        uda_target_per_working_day = total_udas / available_workdays

        target_data = []
        for i in range(21):
            current_date = start_date + timedelta(days=i)
            if current_date.weekday() < workdays_per_week:
                target_data.append(uda_target_per_working_day)
            else:
                target_data.append(0)  # For weekends, show zero target UDAs

        predicted_data = [0, 10, 20, 40, 60, 80, 90, 110, 130, 150, 160, 170, 190, 200, 210, 220, 230, 240, 250, 260, 270]

    today_index = (today - start_date).days
    if today_index < 0 or today_index >= len(labels):
        today_index = None

    context = {
        'labels': labels,
        'predicted_data': predicted_data,
        'target_data': target_data,
        'today_index': today_index,
        'today_label': today.strftime('%d/%m/%Y'),
        'contract_ids': contract_ids,
    }

    return render(request, 'hod_template/dashboard.html', context)



def sum_units_by_contract_yearly(request):
    if request.method == 'POST' and request.headers.get('x-requested-with') == 'XMLHttpRequest':
        contract_id = request.POST.get('contract_id')
        if contract_id:
            # Define the date range
            start_date = datetime(2024, 4, 1)
            end_date = datetime(2025, 3, 31)

            # Sum the units within the date range and with Status 'Validated'
            total_units = DataReport.objects.filter(
                ContractID=contract_id,
                Status='Validated',
                ProcessingDate__range=[start_date, end_date]
            ).aggregate(total_units=Sum('Units'))['total_units'] or 0

            # Format the total_units with commas
            formatted_units = "{:,}".format(total_units)

            # Debug statement to print the sum of units
            print(f"Sum of validated units for contract_id {contract_id} between {start_date} and {end_date}: {formatted_units}")

            return JsonResponse({'total_units': formatted_units})
    return JsonResponse({'error': 'Invalid request'}, status=400)

def calculate_udas_yearly(contract_info):
    total_days = 365  # Total days in the period from 1st April 2024 to 31st March 2025
    off_days = 38  # Total OFF days in the period
    remaining_days = total_days - off_days  # Remaining working days
    remaining_weeks = remaining_days // 7  # Remaining weeks
    workdays_per_week = 4  # Workdays per week
    available_workdays = workdays_per_week * remaining_weeks  # Total available workdays
    
    contracted_udas = float(contract_info.get('contracted_udas', 0))
    carry_over_udas = float(contract_info.get('carry_over_udas', 0))
    total_udas = float(contract_info.get('target_udas', 0))
    
    uda_target_per_working_day = total_udas / available_workdays

    # Create a cumulative UDA target curve for the entire year
    labels = []
    data_uda_target_curve = []
    
    start_date = datetime(2024, 4, 1)
    cumulative_udas = 0
    current_date = start_date

    while current_date < datetime(2025, 4, 1):
        day_of_week = current_date.weekday()  # 0 = Monday, 6 = Sunday
        if day_of_week < 4:  # Monday to Thursday are working days
            cumulative_udas += uda_target_per_working_day
        
        labels.append(current_date.strftime("%Y-%m-%d"))
        data_uda_target_curve.append(round(cumulative_udas, 2))
        
        current_date += timedelta(days=1)

    return {
        'labels': labels,
        'data_uda_target_curve': data_uda_target_curve,
        'target_udas': total_udas,  # Pass total UDAs to the frontend
        'uda_target_per_working_day': uda_target_per_working_day,
    }

def dashboard_yearly(request):
    contracted_udas_details = ContractedUDAsDetails.objects.all()

    contract_data = {
        detail.contract_id: {
            'contracted_udas': int(detail.contracted_udas),
            'carry_over_udas': int(detail.carry_over_udas),
            'target_udas': int(detail.contracted_udas) + int(detail.carry_over_udas),
        }
        for detail in contracted_udas_details
    }

    start_date = datetime(2024, 4, 1)  # Start date is 1st April 2024
    end_date = datetime(2025, 3, 31)   # End date is 31st March 2025

    if request.method == 'POST' and request.headers.get('x-requested-with') == 'XMLHttpRequest':
        contract_id = request.POST.get('contract_id')
        contract_info = contract_data.get(contract_id, {})

        # Calculate data for the entire year
        yearly_data = calculate_udas_yearly(contract_info)

        # Get the total units for the contract within the date range
        total_units = DataReport.objects.filter(
            ContractID=contract_id,
            Status='Validated',
            ProcessingDate__range=[start_date, end_date]
        ).aggregate(total_units=Sum('Units'))['total_units'] or 0

        # Calculate remaining UDAs
        remaining_udas = float(yearly_data['target_udas']) - float(total_units)
        
        return JsonResponse({
            'yearly_data': yearly_data,
            'total_units': "{:,}".format(total_units),  # Add total_units to the response
            'remaining_udas': "{:,}".format(remaining_udas),  # Add remaining_udas to the response
            'target_udas': yearly_data['target_udas'],  # Pass total UDAs to the frontend
        })

    selected_contract_id = list(contract_data.keys())[0] if contract_data else None
    selected_contract_info = contract_data.get(selected_contract_id, {})

    yearly_data = calculate_udas_yearly(selected_contract_info) if selected_contract_info else {}

    total_units = DataReport.objects.filter(
        ContractID=selected_contract_id,
        Status='Validated',
        ProcessingDate__range=[start_date, end_date]
    ).aggregate(total_units=Sum('Units'))['total_units'] or 0

    remaining_udas = float(yearly_data['target_udas']) - float(total_units)

    contract_data_json = json.dumps(contract_data)
    calculated_data_json = json.dumps({
        'yearly_data': yearly_data,
        'remaining_udas': remaining_udas  # Include remaining_udas in the JSON data
    })

    context = {
        'contract_data_json': contract_data_json,
        'calculated_data_json': calculated_data_json,
        'contract_ids': list(contract_data.keys()),
        'selected_contract_id': selected_contract_id,
        'target_udas': selected_contract_info.get('target_udas', 0),
        'remaining_udas': remaining_udas,  # Pass remaining_udas to the frontend
    }

    return render(request, 'hod_template/contractId_yearly_plot.html', context)

def contract_details(request):
    # Get unique Contract IDs
    contract_ids = Contract_Details.objects.values_list('Contract_ID', flat=True).distinct()
    contracts = Contract_Details.objects.all()  # Fetch all Contract_Details objects

    return render(request, 'hod_template/contract_details.html', {
        'contracts': contracts,
        'contract_ids': contract_ids
    })

@require_http_methods(["GET", "POST"])
def contract_details_api(request):
    print("Reached contract_details_api view")
    
    # Get query parameters from request based on method
    if request.method == 'GET':
        contract_id = request.GET.get('contract_id')
    elif request.method == 'POST':
        contract_id = request.POST.get('contract_id')

    # Debugging: Print values for inspection
    print("Contract ID:", contract_id)

    # Validate Contract ID parameter
    if not contract_id:
        return JsonResponse({"error": "Contract ID is required."}, status=400)

    # Query the Contract_Details model based on the provided Contract ID
    contracts = Contract_Details.objects.filter(Contract_ID=contract_id.strip())

    # Check if any contract matching the Contract ID exists
    if contracts.exists():
        # Serialize contract details to JSON data
        data = {
            "contract_details": []
        }

        for contract in contracts:
            contract_data = {
                "id": contract.pk,  # Include the primary key (id) of the contract
                "contract_id": contract.Contract_ID,
                "contract_name": contract.Contract_Name,
                "address": contract.Address,
                "vat_no": contract.VAT_No,
                # Add more fields as needed
            }
            data["contract_details"].append(contract_data)

        # Return JSON response with the fetched data
        return JsonResponse(data)
    else:
        return JsonResponse({"error": "Contract not found."}, status=404)
def superannuation_details(request):
    # Get unique Contract IDs and Statement References from SuperannuationDetails model
    contract_ids = SuperannuationDetails.objects.values_list('contract_id', flat=True).distinct()
    statement_references = SuperannuationDetails.objects.values_list('statement_reference', flat=True).distinct()

    # Prepare context data to pass to the template
    context = {
        'contract_ids': contract_ids,
        'statement_references': statement_references
    }

    # Render the template with the context data
    return render(request, 'hod_template/superannuation.html', context)

@require_http_methods(["GET", "POST"])
def superannuation_details_api(request):
    # Get query parameters from request based on method
    if request.method == 'GET':
        contract_id = request.GET.get('contract_id')
        statement_reference = request.GET.get('statement_reference')
    elif request.method == 'POST':
        contract_id = request.POST.get('contract_id')
        statement_reference = request.POST.get('statement_reference')

    # Debugging: Print values for inspection
    print("Contract ID:", contract_id)
    print("Statement Reference:", statement_reference)

    # Validate Contract ID parameter
    if not contract_id:
        return JsonResponse({"error": "Contract ID is required."}, status=400)

    # Filter SuperannuationDetails objects based on Contract ID
    superannuation_data = SuperannuationDetails.objects.filter(contract_id=contract_id)

    # Optionally filter further by Statement Reference if provided
    if statement_reference:
        superannuation_data = superannuation_data.filter(statement_reference=statement_reference)

    # Serialize queryset to JSON data
    data = list(superannuation_data.values())

    # Return JSON response with the fetched data
    return JsonResponse(data, safe=False)



def units_details(request):
    # Get unique Contract IDs and Statement References
    contract_ids = Units_Details.objects.values_list('Contract_ID', flat=True).distinct()
    statement_references = Units_Details.objects.values_list('Statement_Reference', flat=True).distinct()

    return render(request, 'hod_template/units_activity.html', {
        'contract_ids': contract_ids,
        'statement_references': statement_references
    })

@require_http_methods(["GET", "POST"])
def units_details_api(request):
    print("Reached units_details_api view")
    
    # Get query parameters from request based on method
    if request.method == 'GET':
        contract_id = request.GET.get('contract_id')
        statement_reference = request.GET.get('statement_reference')
    elif request.method == 'POST':
        contract_id = request.POST.get('contract_id')
        statement_reference = request.POST.get('statement_reference')

    # Debugging: Print values for inspection
    print("Contract ID:", contract_id)
    print("Statement Reference:", statement_reference)

    # Validate Contract ID parameter
    if not contract_id:
        return JsonResponse({"error": "Contract ID is required."}, status=400)

    # Query the Units_Details model based on the provided Contract ID
    units_data = Units_Details.objects.filter(Contract_ID=contract_id)

    # Optionally filter further by Statement Reference if provided
    if statement_reference:
        units_data = units_data.filter(Statement_Reference=statement_reference)

    # Serialize queryset to JSON data
    data = list(units_data.values())

    # Return JSON response with the fetched data
    return JsonResponse(data, safe=False)

def non_superannuation_details(request):
    # Get unique Contract IDs and Statement References
    unique_contract_ids = NonSuperannuable.objects.values_list('Contract_ID', flat=True).distinct()
    unique_statement_references = NonSuperannuable.objects.values_list('Statement_Reference', flat=True).distinct()

    context = {
        'unique_contract_ids': unique_contract_ids,
        'unique_statement_references': unique_statement_references,
    }
    return render(request, 'hod_template/non_superannuation.html', context)

@require_http_methods(["GET", "POST"])
def non_superannuation_details_api(request):
    # Get query parameters from request based on method
    if request.method == 'GET':
        contract_id = request.GET.get('contract_id')
        statement_reference = request.GET.get('statement_reference')
    elif request.method == 'POST':
        contract_id = request.POST.get('contract_id')
        statement_reference = request.POST.get('statement_reference')

    # Validate Contract ID and Statement Reference parameters
    if not contract_id or not statement_reference:
        return JsonResponse({"error": "Contract ID and Statement Reference are required."}, status=400)

    # Filter NonSuperannuable objects based on Contract ID and Statement Reference
    non_superannuable_data = NonSuperannuable.objects.filter(Contract_ID=contract_id, Statement_Reference=statement_reference)

    # Calculate total sum of Amount column
    total_amount_sum = non_superannuable_data.aggregate(total_sum=Sum('Amount'))['total_sum'] or 0

    # Debugging: Print values for inspection
    print("Contract ID:", contract_id)
    print("Statement Reference:", statement_reference)
    print("Queryset:", non_superannuable_data)
    print("Total Amount Sum:", total_amount_sum)
    
    data = list(non_superannuable_data.values())

    # Return JSON response with the fetched data and total sum
    return JsonResponse({'data': data, 'total_amount_sum': total_amount_sum}, safe=False)

