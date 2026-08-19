@component('mail::message')
# Hello {{ $user->name }},

We detected a new login to your account.

**Login Details:**

- **Time:** {{ $loginData['time'] }}
- **IP Address:** {{ $loginData['ip'] }}
- **Device:** {{ $loginData['device'] }}
- **Browser:** {{ $loginData['browser'] }}
- **Location:** {{ $loginData['location'] }}

If this was you, you can safely ignore this email. If you did not login recently, please secure your account immediately.

Thanks,<br>
{{ config('app.name') }}
@endcomponent