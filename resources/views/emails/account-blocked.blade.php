<!DOCTYPE html>
<html>

  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Account Blocked</title>
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
        background: #f8d7da;
        border: 1px solid #f5c6cb;
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
        <h2>Account Blocked</h2>

        <p>Hello {{ $user->name }},</p>

        <p>We regret to inform you that your {{ config('app.name') }} account has been blocked.</p>

        <div class="alert">
          <h3>Reason:</h3>
          <p>{{ $blockReason }}</p>
        </div>

        <p>Your account has been permanently blocked and you will no longer be able to access any {{ config('app.name') }} services, including:</p>
        <ul>
          <li>Wallet access</li>
          <li>Transactions</li>
          <li>All other platform features</li>
        </ul>

        <p>If you believe this action is in error or you have any questions, please contact our support team immediately.</p>
      </div>

      <div class="footer">
        <p>Thanks,<br>{{ config('app.name') }} Team</p>
      </div>
    </div>
  </body>

</html>
