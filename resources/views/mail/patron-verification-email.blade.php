<div style="font-family: Arial, sans-serif; color: #333; max-width: 600px; margin: 0 auto;">
    <div style="background-color: #f8f9fa; padding: 20px; border-radius: 5px;">
        <h2 style="color: #333; margin-top: 0;">Account Verification Notification</h2>

        <p>Hello {{ $userName }},</p>

        @if ($status === 'verified')
            <div
                style="background-color: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 15px; border-radius: 4px; margin: 20px 0;">
                <h3 style="margin-top: 0; color: #155724;">
                    <i style="color: #28a745;">✓</i> Account Verification - APPROVED
                </h3>
                <p><strong>Great news!</strong> Your account has been verified by our administration team.</p>
                <p>You now have full access to the TDCI Archive system. You can:</p>
                <ul>
                    <li>Browse and search archives</li>
                    <li>Request access to restricted archives</li>
                    <li>Download available documents</li>
                    <li>View detailed archive information</li>
                </ul>
            </div>
        @elseif($status === 'rejected')
            <div
                style="background-color: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; padding: 15px; border-radius: 4px; margin: 20px 0;">
                <h3 style="margin-top: 0; color: #721c24;">
                    <i style="color: #dc3545;">✗</i> Account Verification - REJECTED
                </h3>
                <p><strong>Your account verification has been rejected.</strong></p>
                @if ($message)
                    <p><strong>Reason:</strong> {{ $message }}</p>
                @else
                    <p>Your account could not be verified at this time. Please contact our support team for more
                        information.</p>
                @endif
            </div>
        @endif

        <hr style="border: none; border-top: 1px solid #ddd; margin: 30px 0;">

        <p style="color: #666; font-size: 14px;">
            If you have any questions or need assistance, please contact our support team at
            <strong>support@tdci-archive.com</strong>
        </p>

        <p style="color: #666; font-size: 14px; margin-bottom: 0;">
            Best regards,<br>
            <strong>TDCI Archive Team</strong>
        </p>
    </div>

    <div
        style="text-align: center; color: #999; font-size: 12px; margin-top: 20px; padding-top: 20px; border-top: 1px solid #eee;">
        <p>© {{ date('Y') }} TDCI Archive System. All rights reserved.</p>
    </div>
</div>
