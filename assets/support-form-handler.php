<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $firstName = strip_tags($_POST['firstName']);
    $lastName  = strip_tags($_POST['lastName']);
    $email     = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $issueType = strip_tags($_POST['issueType']);
    $subject   = strip_tags($_POST['subject']);
    $message   = strip_tags($_POST['message']);

    $to = "support@codreconcile.com";
    $email_subject = "Support Request: $subject";
    
    // Build email content
    $email_body = "
    <html>
    <head>
        <title>Support Request</title>
    </head>
    <body>
        <h2>New Support Request</h2>
        <p><strong>Name:</strong> $firstName $lastName</p>
        <p><strong>Email:</strong> $email</p>
        <p><strong>Type:</strong> $issueType</p>
        <p><strong>Subject:</strong> $subject</p>
        <p><strong>Message:</strong><br>" . nl2br($message) . "</p>
    </body>
    </html>
    ";
    
    // Set headers for HTML email
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "From: support@codreconcile.com" . "\r\n";
    $headers .= "Reply-To: $email" . "\r\n";
    
    // Handle file attachment if exists
    if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] == UPLOAD_ERR_OK) {
        $file_tmp_path = $_FILES['attachment']['tmp_name'];
        $file_name = $_FILES['attachment']['name'];
        $file_size = $_FILES['attachment']['size'];
        $file_type = $_FILES['attachment']['type'];
        
        // Read the file content
        $file_content = chunk_split(base64_encode(file_get_contents($file_tmp_path)));
        
        // Set boundary
        $boundary = md5(time());
        
        // Change headers for multipart
        $headers = "MIME-Version: 1.0\r\n";
        $headers .= "From: support@codreconcile.com\r\n";
        $headers .= "Reply-To: $email\r\n";
        $headers .= "Content-Type: multipart/mixed; boundary = \"$boundary\"\r\n\r\n";
        
        // Plain text version for non-HTML email clients
        $body = "--$boundary\r\n";
        $body .= "Content-Type: text/html; charset=UTF-8\r\n";
        $body .= "Content-Transfer-Encoding: base64\r\n\r\n";
        $body .= chunk_split(base64_encode($email_body));
        
        // Attachment
        $body .= "--$boundary\r\n";
        $body .= "Content-Type: $file_type; name=\"$file_name\"\r\n";
        $body .= "Content-Disposition: attachment; filename=\"$file_name\"\r\n";
        $body .= "Content-Transfer-Encoding: base64\r\n\r\n";
        $body .= $file_content;
        $body .= "--$boundary--";
    } else {
        $body = $email_body;
    }
    
    // Send email
    if (mail($to, $email_subject, $body, $headers)) {
        echo "success";
    } else {
        echo "error";
    }
} else {
    echo "Invalid request method.";
}
?>