@component('mail::message')
# {{ $isInstall ? 'New App Installation' : 'App Uninstallation' }} Notification

{{ $isInstall ? "We're excited to inform you that a new installation of your app has been recorded!" : "An app has been uninstalled from a location." }}

## Event Details:
- **Event Type**: {{ $data['type'] }}
- **Location ID**: {{ $data['locationId'] }}
@if($data['companyId'] ??null)
- **Location Name**: {{ $data['name'] }}
@endif
@if($data['companyId'] ??null)
- **Company ID**: {{ $data['companyId'] }}
@endif
- **App ID**: {{ $data['appId'] }}

{{ $isInstall ? 'Thank you for growing our platform!' : 'We hope to see them back soon!' }}

Best regards,<br>
{{ config('app.name') }}
@endcomponent