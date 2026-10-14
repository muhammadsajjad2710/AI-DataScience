from django import forms 
from django.forms import Form
from .models import Contract, Performer, CompassCredentials


class DateInput(forms.DateInput):
    input_type = "date"


class ContractForm(forms.ModelForm):
    class Meta:
        model = Contract
        fields = ['Contract_ID', 'Commissioner', 'ContractType', 'Start_Date', 'End_Date', 'Status', 'Contract_UDAs']
        widgets = {
            'Start_Date': DateInput(),
            'End_Date': DateInput(),
        }


class PerformerForm(forms.ModelForm):
    class Meta:
        model = Performer
        fields = ['Performer_ID', 'Surname', 'Forename', 'GDC_Number']


class CompassCredentialsForm(forms.ModelForm):
    class Meta:
        model = CompassCredentials
        fields = ['compass_username', 'compass_password', 'compass_memorableword']
        widgets = {
            'compass_password': forms.PasswordInput(),
        }

