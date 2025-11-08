<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Archive Request Notification</title>
    <style>
      body {
        font-family: Arial, sans-serif;
        background-color: #f5f7fa;
        color: #333;
        margin: 0;
        padding: 0;
      }

      .container {
        max-width: 550px;
        margin: 40px auto;
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        padding: 25px 30px;
      }

      h2 {
        color: #2c3e50;
        margin-bottom: 10px;
      }

      p {
        line-height: 1.6;
        font-size: 15px;
        margin: 10px 0;
      }

      .btn {
        display: inline-block;
        background-color: #4f46e5;
        color: #fff !important;
        text-decoration: none;
        padding: 10px 18px;
        border-radius: 5px;
        margin-top: 15px;
      }

      .footer {
        text-align: center;
        font-size: 13px;
        color: #888;
        margin-top: 30px;
      }

      .status {
        font-weight: bold;
      }
    </style>
  </head>
  <body>
    <div class="container">
      <h2>Hello {{ $name }},</h2>

      @if ($status === 'approved')
        <p>
          🎉 Good news! Your <strong>archive request</strong> has been 
          <span class="status" style="color: green;">approved</span>.
        </p>
        <p>
          You can now view or download your requested archive directly from your dashboard.
        </p>
        <a href="{{ url('/dashboard') }}" class="btn">View Archive</a>
      @else
        <p>
          We’re sorry to inform you that your <strong>archive request</strong> was 
          <span class="status" style="color: red;">rejected</span>.
        </p>
        <p>
          If you believe this was a mistake or need further assistance, please contact the administrator.
        </p>
      @endif

      <p>Thank you for using our service!</p>

      <div class="footer">
        <p>— The Admin Team</p>
        <p style="margin-top: 5px;">© {{ date('Y') }} TDCI</p>
      </div>
    </div>
  </body>
</html>
