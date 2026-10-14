from django.db import models
from django_tenants.models import TenantMixin, DomainMixin
from tenant_users.tenants.models import TenantBase


class Client(TenantBase):
    name = models.CharField(max_length=100)
    description = models.TextField(max_length=200)
    on_trial = models.BooleanField(default=True)
    created_on = models.DateField(auto_now_add=True)

    def __str__(self):
        return self.schema_name


class Domain(DomainMixin):
    pass
