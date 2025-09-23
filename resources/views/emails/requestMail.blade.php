<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Archive Access Request</title>
</head>
<body style="margin:0; padding:0; font-family: Arial, sans-serif; background-color:#f4f6f8;">

    <table align="center" cellpadding="0" cellspacing="0" width="100%" style="max-width:600px; margin:20px auto; background-color:#ffffff; border-radius:10px; box-shadow:0 2px 6px rgba(0,0,0,0.1); overflow:hidden;">
        <!-- Header -->
        <tr>
            <td align="center" style="background-color:#004080; padding:20px;">
                <img src="https://cdn-icons-png.flaticon.com/512/732/732200.png" alt="Logo" width="80" style="display:block; margin-bottom:10px;">
                <h1 style="color:#ffffff; margin:0; font-size:20px;">Archive Access Notification</h1>
            </td>
        </tr>

        <!-- Body -->
        <tr>
            <td style="padding:30px; color:#333333; font-size:16px; line-height:1.5;">
                <p>Hi <strong>{{ $request->user->name }}</strong>,</p>

                @if($status == 'approved')
                    <p>Your request to access the archive 
                        <strong>{{ $request->archive->title }}</strong> has been 
                        <span style="color:green; font-weight:bold;">approved</span>.
                    </p>
                @else
                    <p>Your request to access the archive 
                        <strong>{{ $request->archive->title }}</strong> has been 
                        <span style="color:red; font-weight:bold;">rejected</span>.
                    </p>
                @endif

                <p style="margin-top:20px;">Thank you,<br> The Archive Team</p>
            </td>
        </tr>

        <!-- Footer -->
        <tr>
            <td align="center" style="background-color:#f1f1f1; padding:15px; font-size:12px; color:#777;">
                © {{ date('Y') }} Archive System. All rights reserved.
            </td>
        </tr>
    </table>

</body>
</html>