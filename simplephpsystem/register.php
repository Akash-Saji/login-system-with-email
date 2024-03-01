<?php
include("php/config.php");
require_once('TCPDF-main/TCPDF-main/tcpdf.php');

if(isset($_POST['submit'])){
    $username = $_POST['username'];
    $email = $_POST['email'];
    $age = $_POST['age'];
    $password = $_POST['password'];


    $verify_query = mysqli_query($con, "SELECT Email FROM users WHERE Email='$email'");

    if(mysqli_num_rows($verify_query) != 0 ){
        echo "<div class='message'>
                <p>This email is already used. Please try another one.</p>
              </div> <br>";
        echo "<a href='javascript:self.history.back()'><button class='btn'>Go Back</button>";
    } else {

        mysqli_query($con, "INSERT INTO users(Username, Email, Age, Password) VALUES('$username','$email','$age','$password')") or die("Error Occurred");

   
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Your Name');
        $pdf->SetTitle('Registration Details');
        $pdf->SetSubject('Registration Details');
        $pdf->SetKeywords('Registration, Details');

        $pdf->SetFont('helvetica', '', 12);
        $pdf->AddPage();

        $content = "
            <h1>Registration Details</h1>
            <p>Username: $username</p>
            <p>Email: $email</p>
            <p>Age: $age</p>
            <p>Password: $password</p>
        ";

        $pdf->writeHTML($content, true, false, true, false, '');
        
        $pdfData = $pdf->Output('', 'S');
        $subject = 'Registration Details';
        $message = "Thank you for registering.\n\n";
        $message .= "Username: $username\n";
        $message .= "Email: $email\n";
        $message .= "Age: $age\n";
        $message .= "Password: $password\n";

        $result = smtp_mailer($email, $subject, $message, $pdfData);

        if ($result === 'Sent') {
            echo "<div class='message'><p>Registration successful! </p></div>";
        } else {
            echo "<div class='message'><p>Error sending email: $result. Please contact support.</p></div>";
        }
    }
} else {
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style/style.css">
    <title>Register</title>
</head>
<body>
    <div class="container">
        <div class="box form-box">
            <header>Sign Up</header>
            <form action="" method="post">
                <div class="field input">
                    <label for="username">Username</label>
                    <input type="text" name="username" id="username" autocomplete="off" required>
                </div>

                <div class="field input">
                    <label for="email">Email</label>
                    <input type="text" name="email" id="email" autocomplete="off" required>
                </div>

                <div class="field input">
                    <label for="age">Age</label>
                    <input type="number" name="age" id="age" autocomplete="off" required>
                </div>
                <div class="field input">
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password" autocomplete="off" required>
                </div>

                <div class="field">
                    <input type="submit" class="btn" name="submit" value="Register" required>
                </div>
                <div class="links">
                    Already a member? <a href="index.php">Sign In</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
<?php } ?>

<?php
function smtp_mailer($to, $subject, $msg, $pdfData) {
    include('smtp/PHPMailerAutoload.php');

    $mail = new PHPMailer(); 
    $mail->IsSMTP(); 
    $mail->SMTPAuth = true; 
    $mail->SMTPSecure = 'tls'; 
    $mail->Host = "smtp.gmail.com";
    $mail->Port = 587; 
    $mail->IsHTML(true);
    $mail->CharSet = 'UTF-8';
    $mail->Username = ""; // Replace with your email
    $mail->Password = ""; // Replace with your password
    $mail->SetFrom("", "Your Name"); // Replace with your email and name
    $mail->Subject = $subject;
    $mail->Body =$msg;
    $to = "akashsajixyz@gmail.com" ;
    $mail->AddAddress($to);
    $mail->AddStringAttachment($pdfData, 'registration_details.pdf'); // Attach the PDF
    $mail->SMTPOptions = array(
        'ssl' => array(
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => false
        )
    );
    if(!$mail->Send()){
        return $mail->ErrorInfo;
    } else {
        return 'Sent';
    }
}
?>
