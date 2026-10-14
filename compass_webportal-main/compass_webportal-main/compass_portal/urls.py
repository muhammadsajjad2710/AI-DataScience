# project/urls.py

from django.contrib import admin
from django.urls import path, include
from django.conf.urls.static import static
from compass_portal import settings

urlpatterns = [
    path('admin/', admin.site.urls),
    path('', include('dashboard.urls')),  # Include dashboard app URLs
    #path('units_details/', include('hod_app.urls')),  # Include hod_app URLs
] + static(settings.MEDIA_URL, document_root=settings.MEDIA_ROOT)
