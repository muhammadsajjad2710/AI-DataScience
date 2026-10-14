from django.contrib import admin
from django.contrib.auth.admin import UserAdmin
from .models import CustomUser, AdminHOD, Contract, Performer, DataReport, CompassCredentials, Contract_Details, SuperannuationDetails, Units_Details, NonSuperannuable, ContractedUDAsDetails
# Register your models here.
class UserModel(UserAdmin):
    pass


admin.site.register(CustomUser, UserModel)

admin.site.register(AdminHOD)

@admin.register(Contract)
class ContractAdmin(admin.ModelAdmin):
    list_display = ['id', 'Contract_ID', 'Commissioner', 'ContractType', 'Start_Date', 'End_Date', 'Status']
    search_fields = ['Commissioner', 'Provider']

@admin.register(Performer)
class PerformerAdmin(admin.ModelAdmin):
    list_display = ['id', 'Performer_ID', 'Surname', 'Forename', 'GDC_Number']
    search_fields = ['Performer_ID', 'Surname', 'Forename', 'GDC_Number']



@admin.register(DataReport)
class DataReportAdmin(admin.ModelAdmin):
    list_display = ['id', 'CRN_IN', 'ContractID', 'PerformerID', 'Errors', 'PatientSurname', 'PatientForename', 'DateOfBirth', 'TreatmentStartDate', 'TreatmentEndDate', 'PatientCharge', 'Period', 'Units', 'ContraPeriod_Units_PCharge', 'NHSNumber', 'ProcessingDate', 'Status', 'FormType', 'CreatedAmendedDeletedby', 'CreationAmendDeletionDate']
    search_fields = ['CRN_IN', 'ContractID', 'PerformerID', 'Errors', 'PatientSurname', 'PatientForename', 'NHSNumber', 'Status', 'FormType']

@admin.register(CompassCredentials)
class CompassCredentialsAdmin(admin.ModelAdmin):
    list_display = ['id', 'compass_username', 'compass_memorableword']
    search_fields = ['compass_username']

@admin.register(Contract_Details)
class ContractDetailsAdmin(admin.ModelAdmin):
    list_display = ['id', 'Contract_ID', 'Compass_Credential_ID']
    search_fields = ['Contract_ID']

@admin.register(SuperannuationDetails)
class SuperannuationDetailsAdmin(admin.ModelAdmin):
    list_display = ['id', 'contract_id', 'statement_reference', 'name', 'percentage', 'amount', 'performer_id']
    search_fields = ['contract_id', 'name', 'performer_id']

@admin.register(Units_Details)
class UnitsDetailsAdmin(admin.ModelAdmin):
    list_display = ['id', 'Contract_ID', 'Statement_Reference', 'Performer_ID', 'Name', 'Current_Financial_Year', 'Current_Financial_Year_Amount']
    search_fields = ['Contract_ID', 'Performer_ID', 'Name']

@admin.register(NonSuperannuable)
class NonSuperannuableAdmin(admin.ModelAdmin):
    list_display = ['id', 'Contract_ID', 'Statement_Reference', 'Performer_ID', 'Performer_Name', 'Amount']
    search_fields = ['Contract_ID', 'Performer_ID', 'Performer_Name']

@admin.register(ContractedUDAsDetails)
class ContractedUDAsDetailsAdmin(admin.ModelAdmin):
    list_display = ['id', 'contract_id', 'contracted_udas', 'carry_over_udas']
    search_fields = ['contract_id']
