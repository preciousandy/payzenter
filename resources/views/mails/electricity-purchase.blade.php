@component('mail::message')
# Hello {{ $user->name }},

Your Electricity purchase was successful!

**Purchase details:**

- **Customer Name:** {{ $data['customer_name'] }}
- **Provider:** {{ $data['service_name'] }}
- **Meter Number:** {{ $data['customer_id'] }}
- **Token:** {{ $data['token'] }}
- **Amount:** {{ $data['amount'] }}

{{-- thansk message --}}
Thank you for using our service. We appreciate your business and look forward to serving you again.
<br>
{{ config('app.name') }}
@endcomponent