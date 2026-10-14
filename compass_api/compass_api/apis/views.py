from django.shortcuts import render
from rest_framework import generics
from rest_framework.response import Response
from rest_framework.views import APIView
from .models import DataReport
from .models import Contract_Details  
from .models import SuperannuationDetails
from .models import NonSuperannuable
from rest_framework import status
from .models import Units_Details

class DataReportApiView(APIView):

    def post(self, request):
        required_fields = [
            "PerformerID", "Errors", "PatientSurname", "PatientForename",
            "TreatmentStartDate", "TreatmentEndDate", "PatientCharge",
            "Period", "Units", "ContraPeriod_Units_PCharge", "NHSNumber",
            "ProcessingDate", "Status", "FormType", "CreatedAmendedDeletedby",
            "CreationAmendDeletionDate"
        ]
        for field in required_fields:
            if field not in request.data:
                return Response({"Message": f"Missing field: {field}"}, status=status.HTTP_400_BAD_REQUEST)

        data_report = DataReport.objects.create(
            PerformerID=request.data["PerformerID"],
            Errors=request.data["Errors"],
            PatientSurname=request.data["PatientSurname"],
            PatientForename=request.data["PatientForename"],
            TreatmentStartDate=request.data["TreatmentStartDate"],
            TreatmentEndDate=request.data["TreatmentEndDate"],
            PatientCharge=request.data["PatientCharge"],
            Period=request.data["Period"],
            Units=request.data["Units"],
            ContraPeriod_Units_PCharge=request.data["ContraPeriod_Units_PCharge"],
            NHSNumber=request.data["NHSNumber"],
            ProcessingDate=request.data["ProcessingDate"],
            Status=request.data["Status"],
            FormType=request.data["FormType"],
            CreatedAmendedDeletedby=request.data["CreatedAmendedDeletedby"],
            CreationAmendDeletionDate=request.data["CreationAmendDeletionDate"]
        )

        # Fetch the newly created object to return in the response
        report_details = DataReport.objects.filter(id=data_report.id).values()

        return Response({"Message": "New Data Report Added!", "Data Report": report_details}, status=status.HTTP_201_CREATED)

class ContractDetailsView(APIView):

    def post(self, request):
        Contract_Details.objects.create(
            id=request.data["id"],
            Contract_ID=request.data["Contract_ID"],
            Contract_Name=request.data["Contract_Name"],
            Address=request.data["Address"],
            VAT_No=request.data["VAT_No"]
        )
        contract_detail = Contract_Details.objects.filter(id=request.data["id"]).values()
        return Response({"Message": "New contract detail Added!", "Contract_Details": contract_detail})

class SuperannuationView(APIView):

    def post(self, request):
        required_fields = ["contract_id", "statement_reference", "name", "percentage", "amount", "performer_id"]
        for field in required_fields:
            if field not in request.data:
                return Response({"Message": f"Missing field: {field}"}, status=status.HTTP_400_BAD_REQUEST)

        superannuation_detail = SuperannuationDetails.objects.create(
            contract_id=request.data["contract_id"],
            statement_reference=request.data["statement_reference"],
            name=request.data["name"],
            percentage=request.data["percentage"],
            amount=request.data["amount"],
            performer_id=request.data["performer_id"]
        )

        # Fetch the newly created object to return in the response
        superannuation_details = SuperannuationDetails.objects.filter(id=superannuation_detail.id).values()

        return Response({"Message": "New Superannuation Details Added!", "Superannuation_Details": superannuation_details}, status=status.HTTP_201_CREATED)

class NonSuperannuableView(APIView):

    def post(self, request):
        required_fields = ["Contract_ID", "Statement_Reference", "Performer_ID", "Performer_Name", "Amount"]
        for field in required_fields:
            if field not in request.data:
                return Response({"Message": f"Missing field: {field}"}, status=status.HTTP_400_BAD_REQUEST)

        non_superannuable = NonSuperannuable.objects.create(
            Contract_ID=request.data["Contract_ID"],
            Statement_Reference=request.data["Statement_Reference"],
            Performer_ID=request.data["Performer_ID"],
            Performer_Name=request.data["Performer_Name"],
            Amount=request.data["Amount"]
        )

        # Fetch the newly created object to return in the response
        non_superannuable_details = NonSuperannuable.objects.filter(id=non_superannuable.id).values()

        return Response({"Message": "New NonSuperannuable Details Added!", "NonSuperannuable_Details": non_superannuable_details}, status=status.HTTP_201_CREATED)
    
class UnitsView(APIView):

    def post(self, request):
        required_fields = [
            "Contract_ID", "Statement_Reference", "Performer_ID", "Name",
            "Current_Financial_Year", "Current_Financial_Year_Amount",
            "Last_Financial_Year", "Last_Financial_Year_Amount",
            "Other_Finacial_Years", "Other_Finacial_Year_Amount"
        ]
        for field in required_fields:
            if field not in request.data:
                return Response({"Message": f"Missing field: {field}"}, status=status.HTTP_400_BAD_REQUEST)

        units_details = Units_Details.objects.create(
            Contract_ID=request.data["Contract_ID"],
            Statement_Reference=request.data["Statement_Reference"],
            Performer_ID=request.data["Performer_ID"],
            Name=request.data["Name"],
            Current_Financial_Year=request.data["Current_Financial_Year"],
            Current_Financial_Year_Amount=request.data["Current_Financial_Year_Amount"],
            Last_Financial_Year=request.data["Last_Financial_Year"],
            Last_Financial_Year_Amount=request.data["Last_Financial_Year_Amount"],
            Other_Finacial_Years=request.data["Other_Finacial_Years"],
            Other_Finacial_Year_Amount=request.data["Other_Finacial_Year_Amount"]
        )

        # Fetch the newly created object to return in the response
        units_details_info = Units_Details.objects.filter(id=units_details.id).values()

        return Response({"Message": "New Units Details Added!", "Units_Details": units_details_info}, status=status.HTTP_201_CREATED)

class SuperannuationDetailsView(APIView):

    def get(self, request):
        # Get query parameters from request
        contract_id = request.GET.get('contract_id')
        statement_reference = request.GET.get('statement_reference')

        # Validate Contract ID and Statement Reference parameters
        if not contract_id or not statement_reference:
            return Response({"Message": "Contract ID and Statement Reference are required."}, status=status.HTTP_400_BAD_REQUEST)

        # Filter SuperannuationDetails objects based on Contract ID and Statement Reference
        superannuation_data = SuperannuationDetails.objects.filter(contract_id=contract_id, statement_reference=statement_reference)

        # Debugging: Print values for inspection
        print("Contract ID:", contract_id)
        print("Statement Reference:", statement_reference)
        print("Queryset:", superannuation_data)
        
        data = list(superannuation_data.values())

        # Return JSON response with the fetched data
        return Response({'data': data}, status=status.HTTP_200_OK)

class NonSuperannuableDetailsView(APIView):

    def get(self, request):
        # Get query parameters from request
        contract_id = request.GET.get('contract_id')
        statement_reference = request.GET.get('statement_reference')

        # Validate Contract ID and Statement Reference parameters
        if not contract_id or not statement_reference:
            return Response({"Message": "Contract ID and Statement Reference are required."}, status=status.HTTP_400_BAD_REQUEST)

        # Filter NonSuperannuable objects based on Contract ID and Statement Reference
        non_superannuable_data = NonSuperannuable.objects.filter(Contract_ID=contract_id, Statement_Reference=statement_reference)

        # Debugging: Print values for inspection
        print("Contract ID:", contract_id)
        print("Statement Reference:", statement_reference)
        print("Queryset:", non_superannuable_data)
        
        data = list(non_superannuable_data.values())

        # Return JSON response with the fetched data
        return Response({'data': data}, status=status.HTTP_200_OK)

class UnitsDetailsView(APIView):

    def get(self, request):
        # Get query parameters from request
        contract_id = request.GET.get('contract_id')
        statement_reference = request.GET.get('statement_reference')

        # Validate Contract ID and Statement Reference parameters
        if not contract_id or not statement_reference:
            return Response({"Message": "Contract ID and Statement Reference are required."}, status=status.HTTP_400_BAD_REQUEST)

        # Filter Units_Details objects based on Contract ID and Statement Reference
        units_details_data = Units_Details.objects.filter(Contract_ID=contract_id, Statement_Reference=statement_reference)

        # Debugging: Print values for inspection
        print("Contract ID:", contract_id)
        print("Statement Reference:", statement_reference)
        print("Queryset:", units_details_data)
        
        data = list(units_details_data.values())

        # Return JSON response with the fetched data
        return Response({'data': data}, status=status.HTTP_200_OK)