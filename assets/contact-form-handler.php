<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $firstName = strip_tags($_POST['firstName']);
    $lastName  = strip_tags($_POST['lastName']);
    $email     = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $phone     = strip_tags($_POST['phone']);
    $message   = strip_tags($_POST['message']);

    $to = "info@codreconcile.com";  
    $subject = "New Contact Form Submission";

    $body = "You have a new contact form submission:\n\n";
    $body .= "Name: $firstName $lastName\n";
    $body .= "Email: $email\n";
    $body .= "Phone: $phone\n\n";
    $body .= "Message:\n$message\n";

    $headers = "From: info@codreconcile.com\r\n";
    $headers .= "Reply-To: $email\r\n";  
    $headers .= "X-Mailer: PHP/" . phpversion();

    if (mail($to, $subject, $body, $headers)) {
        echo "sucess";
    } else {
        echo "error";
    }
}
?>
