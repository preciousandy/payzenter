<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
  <title>{{ config('app.name') }}</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  <meta name="color-scheme" content="light">
  <meta name="supported-color-schemes" content="light">

  {{-- tailwind css --}}
  {{-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@3.4.15/base.min.css"> --}}

  <style>
    .body {
      min-height: 100vh;
      background-color: gray;
    }

    .footer {
      background-color: #1a1a1a;
      padding: 20px !important;
      text-align: center;
      color: white;
      padding: 15px;
      border-radius: 10px;
      margin-top: 16px !important;
      width: 100% !important;
    }

    .content {
      background-color: white !important;
      padding: 20px !important;
      border-radius: 10px !important;
      margin-top: 12px !important;
    }

    @media only screen and (max-width: 600px) {
      .inner-body {
        width: 100% !important;
      }


    }

    @media only screen and (max-width: 500px) {
      .button {
        width: 100% !important;
      }
    }
  </style>
</head>

<body class="body">

  <div style="padding: 10px; max-width: 800px; width: 100%; margin: 0 auto;">
    <div>
      <div style="background-color: #99b3ff; padding: 20px; text-align: center; width: 100%; border-radius: 10px">
        <img src="http://127.0.0.1:8000/logo-welcome.png" alt="{{ config('app.name') }}" style="max-height: 50px; max-width: 200px;">
      </div>
    </div>

    <div class="content">
      {{ Illuminate\Mail\Markdown::parse($slot) }}

      {{ $subcopy ?? '' }}
    </div>




    <div>
      <div class="footer">
        <p style="text-align: center">© {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>

        <div style="margin-top: 15px;">
          <a href="{{route('contact-us')}}" target="_blank">
            Contact Us
          </a>
          <a href="{{route('terms-and-conditions')}}" target="_blank" style="margin-left: 10px">
            Terms & Conditions
          </a>
        </div>

        <p style="margin-top: 15px; font-size: 12px; color: #6b7280; text-align: center">
          If you're having trouble clicking the buttons, copy and paste the URL below into your web browser:
        </p>
      </div>
    </div>


  </div>


</body>

</html>