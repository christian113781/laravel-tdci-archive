<div style="font-family: Arial, sans-serif; color: #333;">
    <p>Hello {{ $userName }},</p>

    @if ($status === 'approved')
        <p style="color: #28a745; font-weight: bold;">✓ Your archive access request has been <strong>APPROVED</strong>!
        </p>
        <p>Archive: <strong>{{ $archiveTitle }}</strong></p>
        <p>You now have access to this archive and can view its contents.</p>
    @elseif($status === 'rejected')
        <p style="color: #dc3545; font-weight: bold;">✗ Your archive access request has been <strong>REJECTED</strong>.
        </p>
        <p>Archive: <strong>{{ $archiveTitle }}</strong></p>
        <p>Your request for access to this archive could not be approved at this time.</p>
    @else
        <p>{{ $message ?? 'Archive Access Request Update' }}</p>
    @endif

    <p>If you have any questions, please contact our support team.</p>
    <p>Best regards,<br><strong>TDCI Archive Team</strong></p>
</div>
