<!DOCTYPE html>
<html>

  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Account Reactivated</title>
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
        background: #d1edff;
        border: 1px solid #bee5eb;
        padding: 15px;
        border-radius: 5px;
        margin: 20px 0;
      }

      .button {
        background: #007bff;
        color: white;
        padding: 12px 24px;
        text-decoration: none;
        border-radius: 5px;
        display: inline-block;
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
        <h2>Account Reactivated</h2>

        <p>Hello {{ $user->name }},</p>

        <div class="alert">
          <p><strong>Great news!</strong> Your {{ config('app.name') }} account has been reactivated and you now have full access to all our services.</p>
        </div>

        <p>Your account suspension period has ended and you can now:</p>
        <ul>
          <li>Access your wallet</li>
          <li>Make transactions</li>
          <li>Use all {{ config('app.name') }} features</li>
        </ul>

        <p>Welcome back! We're glad to have you as part of the {{ config('app.name') }} community.</p>

        <div style="text-align: center;">
          <a href="{{ config('app.url') }}" class="button">Login to Your Account</a>
        </div>
      </div>

      <div class="footer">
        <p>Thanks,<br>{{ config('app.name') }} Team</p>
      </div>
    </div>
  </body>

</html>
