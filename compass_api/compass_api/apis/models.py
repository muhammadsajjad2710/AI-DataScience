from django.db import models
from django.contrib.auth.models import AbstractUser

# Create your models here.
class CustomUser(AbstractUser):
    name = models.CharField(null=True, blank=True, max_length=100)

class DataReport(models.Model):
    id = models.AutoField(primary_key=True)
    CRN_IN = models.CharField(max_length=50)
    ContractID = models.CharField(max_length=50, null=True)
    PerformerID = models.CharField(max_length=50, null=True)
    Errors = models.CharField(max_length=50, null=True)
    PatientSurname = models.CharField(max_length=50, null=True)
    PatientForename = models.CharField(max_length=50, null=True)
    DateOfBirth = models.DateTimeField(null=True)
    TreatmentStartDate = models.DateTimeField(null=True)
    TreatmentEndDate = models.DateTimeField(null=True)
    PatientCharge = models.CharField(max_length=50, null=True)
    Period = models.CharField(max_length=50, null=True)
    Units = models.CharField(max_length=50, null=True)
    ContraPeriod_Units_PCharge = models.CharField(max_length=50, null=True)
    NHSNumber = models.CharField(max_length=50, null=True)
    ProcessingDate = models.DateTimeField(null=True)
    Status = models.CharField(max_length=50, null=True)
    FormType = models.CharField(max_length=50, null=True)
    CreatedAmendedDeletedby =models.CharField(max_length=50, null=True)
    CreationAmendDeletionDate = models.DateTimeField(null=True)

    class Meta:
        db_table = 'data_report'


class Contract_Details(models.Model):
    id = models.AutoField(primary_key=True)
    Contract_ID = models.CharField(max_length=100) 
    Contract_Name = models.CharField(max_length=255)
    Address = models.CharField(max_length=500)
    VAT_No = models.CharField(max_length=100)

    def __str__(self):
        return self.Contract_Name  

class SuperannuationDetails(models.Model):
    id = models.AutoField(primary_key=True)
    contract_id = models.CharField(max_length=100)
    statement_reference = models.CharField(max_length=100)
    name = models.CharField(max_length=255)
    percentage = models.DecimalField(max_digits=10, decimal_places=2)
    amount = models.DecimalField(max_digits=10, decimal_places=2)
    performer_id = models.CharField(max_length=100)

    def __str__(self):
        return f"SuperannuationDetails - {self.id}"

class NonSuperannuable(models.Model):
    id = models.AutoField(primary_key=True)
    Contract_ID = models.CharField(max_length=255)
    Statement_Reference = models.CharField(max_length=255)
    Performer_ID = models.CharField(max_length=255)
    Performer_Name = models.CharField(max_length=255)
    Amount = models.DecimalField(max_digits=10, decimal_places=2)

    def __str__(self):
        return f"Non_Superannuable {self.id}"
    
class Units_Details(models.Model):
    id = models.AutoField(primary_key=True)
    Contract_ID = models.CharField(max_length=255)
    Statement_Reference = models.CharField(max_length=255)
    Performer_ID = models.CharField(max_length=255)
    Name = models.CharField(max_length=255)
    Current_Financial_Year = models.CharField(max_length=50)
    Current_Financial_Year_Amount = models.DecimalField(max_digits=10, decimal_places=2)
    Last_Financial_Year = models.CharField(max_length=50)
    Last_Financial_Year_Amount = models.DecimalField(max_digits=10, decimal_places=2)
    Other_Finacial_Years = models.CharField(max_length=255)
    Other_Finacial_Year_Amount = models.DecimalField(max_digits=10, decimal_places=2)



