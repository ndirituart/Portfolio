<?php

// Enable error reporting for debugging (remove in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);


// 1. Database Connection Details
$servername = "localhost";
$username = "skrnchst_contacts"; // From DirectAdmin MySQL Management
$password = "skrnchst_contacts";
$dbname = "k5wHWuDQtWMc2nky5ztj";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    error_log("Connection failed: " . $conn->connect_error);
    header("Location: contact.html?status=error&message=connection_failed");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 2. Validate required fields
    $errors = array();
    
    if (empty($_POST['user_email'])) {
        $errors[] = "Email is required";
    }
    if (empty($_POST['message'])) {
        $errors[] = "Message is required";
    }
    
    if (!empty($errors)) {
        header("Location: contact.html?status=error&message=missing_fields");
        exit();
    }


    // 2. Capture and Sanitize Data
    $first_name = mysqli_real_escape_string($conn, $_POST['first_name']);
    $last_name  = mysqli_real_escape_string($conn, $_POST['last_name']);
    $email      = mysqli_real_escape_string($conn, $_POST['user_email']);
    $country    = mysqli_real_escape_string($conn, $_POST['country']);
    $county     = mysqli_real_escape_string($conn, $_POST['county']);
    $town       = mysqli_real_escape_string($conn, $_POST['town']);
    $subject    = mysqli_real_escape_string($conn, $_POST['subject']);
    $message    = mysqli_real_escape_string($conn, $_POST['message']);

    // 3. Save to Database (Contacts Table)
    $sql = "INSERT INTO subscribers (first_name, last_name, email, country, county, town) 
            VALUES ('$first_name', '$last_name', '$email', '$country', '$county', '$town')";
    
    $conn->query($sql);

    // 4. Send Email to Admin (YOU)
    $to = "ndiritupatience002@gmail.com"; // Your professional email
    $email_subject = "New SKRNCH Enquiry: $subject";
    
    $email_body = "You have received a new message.\n\n".
                  "Name: $first_name $last_name\n".
                  "Email: $email\n".
                  "Location: $town, $county\n".
                  "Message:\n$message";

    $headers = "From: ndiritupatience002@gmail.com\r\n";
    $headers .= "Reply-To: $email\r\n";

    mail($to, $email_subject, $email_body, $headers);

    // 5. Send Auto-Reply to Customer (THE BADDIE)
    $auto_subject = "We got you, Baddie! ✨";
    $auto_body = "Hi $first_name,\n\nThanks for reaching out to SKRNCH! We've received your message and our team will get back to you shortly.\n\nStay Pretty,\nSKRNCH Team";
    
    mail($email, $auto_subject, $auto_body, "From: ndiritupatience002@gmail.com");

    // 6. Redirect back to contact page with success message
    header("Location: contact.html?status=success");
    exit();
}
?>