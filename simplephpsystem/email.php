<?php
// Email details
$to = "akashsajixyz@gmail.com";
$subject = "Test Email";
$message = "Hello, this is a test email.";

// Sender details
$from = "mynameisakashsaji@gmail.com"; // Replace with your Gmail address
$password = "xmzk armn simh aclv"; // Replace with your Gmail password

// Email headers
$headers = [
    "MIME-Version: 1.0",
    "Content-type: text/html;charset=UTF-8",
    "From: $from",
    "Reply-To: $from"
];

// Additional headers
$headers[] = "Cc: another@example.com";
$headers[] = "Bcc: hidden@example.com";

// Email content
$content = implode("\r\n", $headers) . "\r\n\r\n" . $message;

// SMTP server configuration
$smtpServer = "ssl://smtp.gmail.com";
$smtpPort = 465;

// Connect to the SMTP server
$smtpConnect = stream_socket_client($smtpServer . ":" . $smtpPort, $errno, $errstr, 30);

if ($smtpConnect) {
    // Connection established, send the EHLO command to identify ourselves to the server
    fputs($smtpConnect, "EHLO localhost\r\n");

    // Read server response
    $serverResponse = fgets($smtpConnect);

    if (strpos($serverResponse, "250") !== false) {
        // Server supports STARTTLS, initiate TLS encryption
        fputs($smtpConnect, "STARTTLS\r\n");
        $tlsResponse = fgets($smtpConnect);

        if (strpos($tlsResponse, "220") !== false) {
            // TLS handshake successful, authenticate
            fputs($smtpConnect, "AUTH LOGIN\r\n");
            $authResponse = fgets($smtpConnect);

            if (strpos($authResponse, "334") !== false) {
                // Send username
                fputs($smtpConnect, base64_encode($from) . "\r\n");
                $userResponse = fgets($smtpConnect);

                if (strpos($userResponse, "334") !== false) {
                    // Send password
                    fputs($smtpConnect, base64_encode($password) . "\r\n");
                    $passResponse = fgets($smtpConnect);

                    if (strpos($passResponse, "235") !== false) {
                        // Authentication successful, send email
                        fputs($smtpConnect, "MAIL FROM: <$from>\r\n");
                        $fromResponse = fgets($smtpConnect);

                        if (strpos($fromResponse, "250") !== false) {
                            // Specify recipient
                            fputs($smtpConnect, "RCPT TO: <$to>\r\n");
                            $toResponse = fgets($smtpConnect);

                            if (strpos($toResponse, "250") !== false) {
                                // Send email data
                                fputs($smtpConnect, "DATA\r\n");
                                $dataResponse = fgets($smtpConnect);

                                if (strpos($dataResponse, "354") !== false) {
                                    // Send email content
                                    fputs($smtpConnect, "Subject: $subject\r\n");
                                    foreach ($headers as $header) {
                                        fputs($smtpConnect, "$header\r\n");
                                    }
                                    fputs($smtpConnect, "\r\n$message\r\n.\r\n");

                                    // End email data
                                    fputs($smtpConnect, "\r\n.\r\n");
                                    $messageResponse = fgets($smtpConnect);

                                    if (strpos($messageResponse, "250") !== false) {
                                        echo "Mail sent successfully";
                                    } else {
                                        echo "Error sending mail (Message response)";
                                    }
                                } else {
                                    echo "Error sending mail (Data response)";
                                }
                            } else {
                                echo "Error sending mail (Recipient response)";
                            }
                        } else {
                            echo "Error sending mail (From response)";
                        }
                    } else {
                        echo "Error sending mail (Password response)";
                    }
                } else {
                    echo "Error sending mail (Username response)";
                }
            } else {
                echo "Error sending mail (Auth response)";
            }
        } else {
            echo "Error sending mail (TLS response)";
        }
    } else {
        echo "Error sending mail (EHLO response)";
    }

    // Quit session
    fputs($smtpConnect, "QUIT\r\n");

    // Close connection
    fclose($smtpConnect);
} else {
    echo "Error connecting to SMTP server";

}

error_reporting(E_ALL);
ini_set('display_errors', 1);

?>
