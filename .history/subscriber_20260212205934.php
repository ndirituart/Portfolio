<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $subscriber = $_POST['baddie_email'];
    
    // 1. Your DirectAdmin Mailing List Request Address
    // Replace 'newsletter' with your actual list name
    $to = "newsletter-request@skrnchstudio.com"; 
    
    // 2. The command Majordomo needs to see
    $subject = "subscribe";
    $message = "subscribe $subscriber";
    
    // 3. The "From" must be the subscriber so the server knows who to add
    $headers = "From: $subscriber" . "\r\n" .
               "Reply-To: $subscriber" . "\r\n" .
               "X-Mailer: PHP/" . phpversion();

    // Send the command to DirectAdmin
    if(mail($to, $subject, $message, $headers)) {
        // Redirect back to home with a success message
        header("Location: index.html?subscribed=true");
    } else {
        header("Location: index.html?subscribed=false");
    }
    exit();
}
?>