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
    //  Validate required fields
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
    $first_name = isset($_POST['first_name']) ? mysqli_real_escape_string($conn, $_POST['first_name']) : '';
    $last_name  = isset($_POST['last_name']) ? mysqli_real_escape_string($conn, $_POST['last_name']) : '';
    $email      = mysqli_real_escape_string($conn, $_POST['user_email']);
    $country    = isset($_POST['country']) ? mysqli_real_escape_string($conn, $_POST['country']) : 'kenya';
    $county     = isset($_POST['county']) ? mysqli_real_escape_string($conn, $_POST['county']) : '';
    $town       = isset($_POST['town']) ? mysqli_real_escape_string($conn, $_POST['town']) : '';
    $subject    = isset($_POST['subject']) ? mysqli_real_escape_string($conn, $_POST['subject']) : 'No Subject';
    $message    = mysqli_real_escape_string($conn, $_POST['message']);



    // 3. Save to Database (Contacts Table)
     // 4. Create tables if they don't exist
    $create_subscribers_table = "CREATE TABLE IF NOT EXISTS subscribers (
        id INT AUTO_INCREMENT PRIMARY KEY,
        first_name VARCHAR(100),
        last_name VARCHAR(100),
        email VARCHAR(100) NOT NULL,
        country VARCHAR(50),
        county VARCHAR(100),
        town VARCHAR(100),
        subscribed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    
    $conn->query($create_subscribers_table);
    
    $create_contacts_table = "CREATE TABLE IF NOT EXISTS contacts (
        id INT AUTO_INCREMENT PRIMARY KEY,
        first_name VARCHAR(100),
        last_name VARCHAR(100),
        email VARCHAR(100) NOT NULL,
        subject VARCHAR(255),
        message TEXT,
        country VARCHAR(50),
        county VARCHAR(100),
        town VARCHAR(100),
        submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    
    $conn->query($create_contacts_table);

    // 5. Save to Database (Subscribers Table)
    $sql = "INSERT INTO subscribers (first_name, last_name, email, country, county, town) 
            VALUES ('$first_name', '$last_name', '$email', '$country', '$county', '$town')";
    
    if (!$conn->query($sql)) {
        error_log("Subscriber insert error: " . $conn->error);
    }

    // 6. Save to Contacts Table (for messages)
    $sql2 = "INSERT INTO contacts (first_name, last_name, email, subject, message, country, county, town) 
             VALUES ('$first_name', '$last_name', '$email', '$subject', '$message', '$country', '$county', '$town')";
    
    if (!$conn->query($sql2)) {
        error_log("Contact insert error: " . $conn->error);
    }

    // 7. Send Email to Admin (YOU) - Using DirectAdmin's mail function
    $to = "admin@skrnchstudio.com"; // Your professional email
    $email_subject = "New SKRNCH Enquiry: $subject";
    
    $email_body = "You have received a new message.\n\n" .
                  "Name: $first_name $last_name\n" .
                  "Email: $email\n" .
                  "Location: $town, $county, $country\n" .
                  "Subject: $subject\n" .
                  "Message:\n$message\n\n" .
                  "Submitted: " . date('Y-m-d H:i:s');

    $headers = "From: SKRNCH Studio <ndiritupatience002@gmail.com>\r\n";
    $headers .= "Reply-To: $email\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion();

    if (!mail($to, $email_subject, $email_body, $headers)) {
        error_log("Admin email sending failed");
    }

    // 8. Send Auto-Reply to Customer (THE BADDIE)
    if (!empty($email)) {
        $auto_subject = "We got you, Baddie! ✨ SKRNCH";
        $auto_body = "Hi " . (!empty($first_name) ? $first_name : "there") . ",\n\n" .
                     "Thanks for reaching out to SKRNCH! We've received your message and our team will get back to you within 24-48 hours.\n\n" .
                     "Your message: \n" .
                     "Subject: $subject\n" .
                     "Message: $message\n\n" .
                     "Stay Pretty,\n" .
                     "The SKRNCH Team 💖\n" .
                     "www.skrnchstudio.com";

        $auto_headers = "From: SKRNCH Studio <ndiritupatience002@gmail.com>\r\n";
        $auto_headers .= "Reply-To: ndiritupatience002@gmail.com\r\n";

        if (!mail($email, $auto_subject, $auto_body, $auto_headers)) {
            error_log("Auto-reply email sending failed");
        }
    }

    // 9. Close connection
    $conn->close();

    // 10. Redirect back to contact page with success message
    header("Location: contact.html?status=success");
    exit();
} else {
    // If someone tries to access this file directly without POST
    header("Location: contact.html");
    exit();
}
?>
    
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