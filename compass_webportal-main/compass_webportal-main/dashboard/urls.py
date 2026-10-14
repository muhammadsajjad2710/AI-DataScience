
from django.urls import path, include
from . import views
from .import HodViews
from .HodViews import units_details_api



urlpatterns = [
    path('', views.loginPage, name="login"),
    # path('accounts/', include('django.contrib.auth.urls')),
    path('doLogin/', views.doLogin, name="doLogin"),
    path('get_user_details/', views.get_user_details, name="get_user_details"),
    path('logout_user/', views.logout_user, name="logout_user"),
  
    path('admin_home/', HodViews.admin_home, name="admin_home"),
    path('get_performer_data/', HodViews.get_performer_data, name="get_performer_data"),
    path('activity_details/', HodViews.activity_details, name="activity_details"),
    path('performer_details/', HodViews.performer_details, name="performer_details"),
    path('contract_details_template/', HodViews.contract_details_template, name="contract_details_template"),
    path('add_contract/', HodViews.add_contract, name="add_contract"),
    path('add_contract_save/', HodViews.add_contract_save, name='add_contract_save'),
    path('edit_contract/<int:contract_id>/', HodViews.edit_contract, name='edit_contract'), 
    path('delete_contract/<int:contract_id>/', HodViews.delete_contract, name='delete_contract'),
    
    
    # Compass Credentials URLs
    path('compass_credential/', HodViews.compass_credential, name='compass_credential'),
    path('add_credential/', HodViews.add_credential, name='add_credential'),
    path('add_save_credential/', HodViews.add_save_credential, name='add_save_credential'),
    path('edit_credential/<int:credential_id>/', HodViews.edit_credential, name='edit_credential'),
    path('delete_credential/<int:credential_id>/', HodViews.delete_credential, name='delete_credential'),
    
    path('check_email_exist/', HodViews.check_email_exist, name="check_email_exist"),
    path('check_username_exist/', HodViews.check_username_exist, name="check_username_exist"),
   
    path('admin_profile/', HodViews.admin_profile, name="admin_profile"),
    path('admin_profile_update/', HodViews.admin_profile_update, name="admin_profile_update"),
    
    path('contract_details/', HodViews.contract_details, name='contract_details'),
    path('contract_details_api/', HodViews.contract_details_api, name='contract_details_api'),
    path('superannuation_details/', HodViews.superannuation_details, name='superannuation_details'),
    path('superannuation_details_api/', HodViews.superannuation_details_api, name='superannuation_details_api'),
    path('units_details/', HodViews.units_details, name='units_details'),
    path('units_details_api/', HodViews.units_details_api, name='units_details_api'),
    path('non_superannuation_details/', HodViews.non_superannuation_details, name='non_superannuation_details'),
    path('non_superannuation_details_api/', HodViews.non_superannuation_details_api, name='non_superannuation_details_api'),
    path('dashboard/', HodViews.dashboard, name='dashboard'),
    path('sum_units_by_contract/', HodViews.sum_units_by_contract, name='sum_units_by_contract'),
    path('calculate_udas/', HodViews.calculate_udas, name='calculate_udas'),
    path('dashboard_yearly/', HodViews.dashboard_yearly, name='dashboard_yearly'),
    path('sum_units_by_contract_yearly/', HodViews.sum_units_by_contract_yearly, name='sum_units_by_contract_yearly'),
    path('calculate_udas_yearly/', HodViews.calculate_udas, name='calculate_udas_yearly'),
]

