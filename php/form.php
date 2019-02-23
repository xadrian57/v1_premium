<?php
// hide the errors
error_reporting( 0 );

// Get the values from html form
$name    = $_POST['app_name'];
$email   = $_POST['app_email'];
$number  = $_POST['app_phone'];
$website = $_POST['app_website'];
$message = $_POST['app_message'];

// Email Address where you want to received the mails
$to = "example@gmail.com";

// Mail Subject
$sub = "Enquiry From Your Productions Website";

// Output table
$email_message = '<html>
<head>
    <title>Enquiry From Your Productions Website</title>
</head>
<body>
<table>
    <tr>
        <th align="left">EMAIL:</th>
        <td align="left">' . $email . '</td>
    </tr>
    <tr>
        <th align="left">NAME:</th>
        <td align="left">' . $name . '</td>
    </tr>
    <tr>
        <th align="left">PHONE:</th>
        <td align="left">' . $number . '</td>
    </tr>
    <tr>
        <th align="left">WEBSITE:</th>
        <td align="left">' . $website . '</td>
    </tr>
    <tr>
        <th align="left">MESSAGE:</th>
        <td align="left">' . $message . '</td>
    </tr>
</table>
</body>
</html>
';

//Headers
$headers = "MIME-Version: 1.0\r\n";
$headers .= "Content-type: text/html; charset=UTF-8\r\n";
$headers .= "From: <" . $email . ">";

//send mail
$mail = mail( $to, $sub, $email_message, $headers );

if ( $mail ) {

	// Success message
	echo 'Your mail was sent successfully';
} else {

	// Error message
	echo 'Error';
}