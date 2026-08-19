<!DOCTYPE html>
<html>

  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Account Suspended</title>
    <style>
      body {
        font-family: Arial, sans-serif;
        line-height: 1.6;
        color: #333;
      }

      .container {
        max-width: 600px;
        margin: 0 auto;
        padding: 20px;
      }

      .header {
        background: #f8f9fa;
        padding: 20px;
        text-align: center;
      }

      .content {
        padding: 20px;
      }

      .footer {
        background: #f8f9fa;
        padding: 20px;
        text-align: center;
        font-size: 12px;
      }

      .alert {
        background: #fff3cd;
        border: 1px solid #ffeaa7;
        padding: 15px;
        border-radius: 5px;
        margin: 20px 0;
      }
    </style>
  </head>

  <body>
    <div class="container">
      <div class="header">
        <h1>{{ config('app.name') }}</h1>
      </div>

      <div class="content">
        <h2>Account Suspended</h2>

        <p>Hello {{ $user->name }},</p>

        <p>We regret to inform you that your {{ config('app.name') }} account has been temporarily suspended.</p>

        <div class="alert">
          <h3>Suspension Details:</h3>
          <ul>
            <li><strong>Duration:</strong> {{ $suspensionDuration }} day{{ $suspensionDuration > 1 ? 's' : '' }}</li>
            <li><strong>Suspended Until:</strong> {{ $suspendedUntil->format('F j, Y at g:i A') }}</li>
            <li><strong>Reason:</strong> {{ $suspensionReason }}</li>
          </ul>
        </div>

        <p>Your account will be automatically reactivated on <strong>{{ $suspendedUntil->format('F j, Y at g:i A') }}</strong>. You will receive an email notification when your account is reactivated.</p>

        <p>During this suspension period, you will not be able to:</p>
        <ul>
          <li>Access your account</li>
          <li>Make transactions</li>
          <li>Use any {{ config('app.name') }} services</li>
        </ul>

        <p>If you believe this suspension is in error or you have any questions, please contact our support team.</p>
      </div>

      <div class="footer">
        <p>Thanks,<br>{{ config('app.name') }} Team</p>
      </div>
    </div>
  </body>

</html>
