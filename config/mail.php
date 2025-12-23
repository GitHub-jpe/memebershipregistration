<?php
function sendApprovalMail($email, $name, $membershipNo) {
    $subject = "Membership Approved";
    $message = "
        Hello $name,

        Your membership has been approved.

        Membership Number: $membershipNo
        Login to your portal to view your QR code.

        Regards,
        Membership Office
    ";

    $headers = "From: noreply@membership.org";
    mail($email, $subject, $message, $headers);
}
