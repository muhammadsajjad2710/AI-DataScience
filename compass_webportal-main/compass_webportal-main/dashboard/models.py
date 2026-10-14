from django.contrib.auth.models import AbstractUser
from django.db import models
from django.db.models.signals import post_save
from django.dispatch import receiver







# Overriding the Default Django Auth User and adding One More Field (user_type)
class CustomUser(AbstractUser):
    user_type_data = ((1, "HOD"), (2, "Staff"), (3, "Student"))
    user_type = models.CharField(default=1, choices=user_type_data, max_length=10)



class AdminHOD(models.Model):
    id = models.AutoField(primary_key=True)
    admin = models.OneToOneField(CustomUser, on_delete = models.CASCADE)
    created_at = models.DateTimeField(auto_now_add=True)
    updated_at = models.DateTimeField(auto_now=True)
    objects = models.Manager()


class Staffs(models.Model):
    id = models.AutoField(primary_key=True)
    admin = models.OneToOneField(CustomUser, on_delete = models.CASCADE)
    address = models.TextField()
    created_at = models.DateTimeField(auto_now_add=True)
    updated_at = models.DateTimeField(auto_now=True)
    objects = models.Manager()








class Contract(models.Model):
    id = models.AutoField(primary_key=True)
    Contract_ID = models.FloatField(null=True)
    Commissioner = models.CharField(max_length=500, null=True)
    ContractType = models.CharField(max_length=200, null=True)
    Start_Date = models.DateTimeField(null=True)
    End_Date = models.DateTimeField(null=True)
    Status = models.CharField(max_length=200, null=True)
    Contract_UDAs = models.IntegerField(null=True)  

    class Meta:
        db_table = 'contract_id'

class CompassCredentials(models.Model):
    id = models.AutoField(primary_key=True)
    compass_username = models.CharField(max_length=255)
    compass_password = models.CharField(max_length=255)
    compass_memorableword = models.CharField(max_length=255)



class Performer(models.Model):
    id = models.AutoField(primary_key=True)
    Performer_ID = models.IntegerField(unique=True)
    Surname = models.CharField(max_length=50)
    Forename = models.CharField(max_length=50)
    GDC_Number = models.IntegerField()

    class Meta:
        db_table = 'Performers'

class Contract_Details(models.Model):
    id = models.AutoField(primary_key=True)
    Contract_ID = models.CharField(max_length=100)  # Assuming Contract ID is a string
    Compass_Credential_ID = models.ForeignKey(CompassCredentials, on_delete=models.CASCADE, default=None, null=True)


    def __str__(self):
        return self.Contract_ID  # Display Contract ID in admin panel

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

class NonSuperannuable(models.Model):
    id = models.AutoField(primary_key=True)
    Contract_ID = models.CharField(max_length=255)
    Statement_Reference = models.CharField(max_length=255)
    Performer_ID = models.CharField(max_length=255)
    Performer_Name = models.CharField(max_length=255)
    Amount = models.DecimalField(max_digits=10, decimal_places=2)

    def __str__(self):
        return f"Non_Superannuable {self.id}"



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
    Units = models.DecimalField(max_digits=10, decimal_places=2, null=True)
    ContraPeriod_Units_PCharge = models.CharField(max_length=50, null=True)
    NHSNumber = models.CharField(max_length=50, null=True)
    ProcessingDate = models.DateTimeField(null=True)
    Status = models.CharField(max_length=50, null=True)
    FormType = models.CharField(max_length=50, null=True)
    CreatedAmendedDeletedby =models.CharField(max_length=50, null=True)
    CreationAmendDeletionDate = models.DateTimeField(null=True)

    class Meta:
        db_table = 'data_report'


class ContractedUDAsDetails(models.Model):
    id = models.AutoField(primary_key=True)
    contract_id = models.CharField(max_length=255)
    contracted_udas = models.CharField(max_length=255)
    carry_over_udas = models.CharField(max_length=255, default='default_value')
    def __str__(self):
        return f"{self.contract_id} - {self.contracted_udas}"


#Creating Django Signals

# It's like trigger in database. It will run only when Data is Added in CustomUser model

@receiver(post_save, sender=CustomUser)
# Now Creating a Function which will automatically insert data in HOD, Staff or Student
def create_user_profile(sender, instance, created, **kwargs):
    # if Created is true (Means Data Inserted)
    if created:
        # Check the user_type and insert the data in respective tables
        if instance.user_type == 1:
            AdminHOD.objects.create(admin=instance)
        if instance.user_type == 2:
            Staffs.objects.create(admin=instance)
       
@receiver(post_save, sender=CustomUser)
def save_user_profile(sender, instance, **kwargs):
    if instance.user_type == 1:
        instance.adminhod.save()
    if instance.user_type == 2:
        instance.staffs.save()
 
    


