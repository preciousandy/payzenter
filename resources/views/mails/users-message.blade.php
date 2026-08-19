<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  <meta name="color-scheme" content="light">
  <meta name="supported-color-schemes" content="light">
  <link rel="stylesheet" href="{{asset('tailwind-mail.css')}}">

  <style>
    /* Reset styles for email clients */
    body,
    table,
    td,
    div,
    p {
      -webkit-text-size-adjust: 100%;
      -ms-text-size-adjust: 100%;
      margin: 0;
      padding: 0;
      font-family: Arial, sans-serif;
    }

    table,
    td {
      mso-table-lspace: 0pt;
      mso-table-rspace: 0pt;
    }

    img {
      -ms-interpolation-mode: bicubic;
      border: 0;
    }

    .wrapper {
      width: 100%;
      table-layout: fixed;
      background-color: #f2f2f2;
      padding-bottom: 60px;
    }

    .main {
      width: 100%;
      max-width: 800px;
      margin: 0 auto;
      background-color: transparent;
      padding: 10px;
    }

    .header {
      background-color: #99b3ff !important;
      padding: 20px !important;
      text-align: center !important;
      border-radius: 10px !important;
    }

    .header img {
      max-height: 50px !important;
      max-width: 200px !important;
      margin: 0 auto !important;
      display: block;
    }

    .content {
      background-color: white !important;
      padding: 20px !important;
      border-radius: 10px !important;
      margin-top: 12px !important;
      width: 100% !important;
      word-break: break-word;
    }

    .footer {
      background-color: #1a1a1a;
      padding: 20px !important;
      text-align: center;
      color: white;
      border-radius: 10px;
      margin-top: 16px !important;
      width: 100% !important;
    }

    /* List styles */
    ol,
    ul {
      list-style-position: inside;
      margin-left: 1.5rem !important;
      margin-bottom: 1rem !important;
      padding-left: 1rem !important;
    }

    ul {
      list-style-type: disc !important;
    }

    ol {
      list-style-type: decimal !important;
    }

    li {
      margin-bottom: 0.5rem !important;
      padding-left: 0.5rem !important;
    }

    ol ol,
    ul ul {
      margin-top: 0.5rem !important;
      margin-bottom: 0.5rem !important;
    }

    /* Links */
    .footer a {
      color: white;
      text-decoration: underline;
    }

    @media screen and (max-width: 600px) {
      .main {
        width: 100% !important;
        padding: 10px !important;
      }

      .content,
      .header,
      .footer {
        padding: 15px !important;
        margin-left: 0 !important;
        margin-right: 0 !important;
      }

      .header img {
        max-height: 40px !important;
      }

      ol,
      ul {
        margin-left: 0.5rem !important;
        padding-left: 0.5rem !important;
      }
    }
  </style>
</head>

<body>
  <div class="wrapper">
    <div class="main">
      <div class="header">
        <img src="http://127.0.0.1:8000/logo-welcome.png" alt="{{ config('app.name') }}">
      </div>

      <div class="content">
        {!! $the_message !!}
      </div>

      <div class="footer">
        <p style="text-align: center; margin: 0;">© {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>

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