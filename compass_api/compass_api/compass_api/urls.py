from django.contrib import admin
from django.urls import path
from apis.views import DataReportApiView
from apis.views import ContractDetailsView
from apis.views import SuperannuationDetailsView
from apis.views import NonSuperannuableView
from apis.views import UnitsDetailsView
from apis.views import NonSuperannuableDetailsView
from apis.views import SuperannuationView
from apis.views import UnitsView


urlpatterns = [
    path('admin/', admin.site.urls),
    path('DataReportList/', DataReportApiView.as_view(), name='DataReportList'),
    path('contract_details/', ContractDetailsView.as_view(), name='contract_details'),
    path('superannuation/', SuperannuationView.as_view(), name='superannuation'),
    path('non_superannuable/', NonSuperannuableView.as_view(), name='non_superannuable'),
    path('units/', UnitsView.as_view(), name='units'),
    path('non_superannuable_details/', NonSuperannuableDetailsView.as_view(), name='non-superannuable-details'),
    path('superannuation_details/', SuperannuationDetailsView.as_view(), name='superannuation-details'),
    path('units-details/', UnitsDetailsView.as_view(), name='units_details'),
]

