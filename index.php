<?php
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;  
    use PHPMailer\PHPMailer\SMTP;
    use Dotenv\Dotenv;

    require 'vendor/autoload.php';

    $dotenv = Dotenv::createImmutable(__DIR__);
    $dotenv->load();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $name = htmlspecialchars($_POST['name']);
        $email = htmlspecialchars($_POST['email']);
        $message = htmlspecialchars($_POST['message']);

        $mail = new PHPMailer(true);

        try {
            //Server settings
            $mail->SMTPDebug = SMTP::DEBUG_OFF;                     
            $mail->isSMTP();                                        // Send using SMTP
            $mail->Host       = $_ENV['SMTP_HOST'];                 // Set the SMTP server to send through
            $mail->SMTPAuth   = true;                               // Enable SMTP authentication
            $mail->Username   = $_ENV['SMTP_USERNAME'];             // SMTP username
            $mail->Password   = $_ENV['SMTP_PASSWORD'];             // SMTP password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;     // Enable implicit TLS encryption
            $mail->Port       = $_ENV['SMTP_PORT'];                 // TCP port to connect to
        
            //Recipients
            $mail->setFrom($_ENV['SMTP_FROM_EMAIL'], $_ENV['SMTP_FROM_NAME']);
            $mail->addAddress($_ENV['SMTP_FROM_EMAIL'], $_ENV['SMTP_FROM_NAME']); 
        
            //Content
            $mail->isHTML(true);                                    // Set email format to HTML
            $mail->Subject = "New Message from $name";
            $mail->Body    = "
                <h1>New Message</h1>
                <p><strong>Name:</strong> $name</p>
                <p><strong>Email:</strong> $email</p>
                <p><strong>Message:</strong></p>
                <p>$message</p>
            ";
            $mail->AltBody = "Name: $name\nEmail: $email\nMessage: $message";
        
            $mail->send();
            header("Location: index.php?message=Message has been sent");
            exit();
        } catch (Exception $e) {
            header("Location: index.php?message=Message could not be sent. Mailer Error: {$mail->ErrorInfo}");
            exit();
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Form</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        form {
            background-color: #ffffff;
            padding: 40px; 
            border-radius: 10px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
            width: 100%;
            max-width: 400px; 
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        form h2 {
            margin-bottom: 20px;
            font-size: 24px;
            color: #333333;
            text-align: center;
        }

        form input, form textarea, form button {
            width: 100%;
            margin-bottom: 20px;
            padding: 12px;
            border: 1px solid #ced4da;
            border-radius: 5px;
            font-size: 16px;
            color: #495057;
        }

        form input:focus, form textarea:focus {
            border-color: #007BFF;
            outline: none;
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
        }

        form textarea {
            resize: none;
            height: 120px;
        }

        form button {
            background-color: #007BFF;
            color: #ffffff;
            border: none;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }

        form button:hover {
            background-color: #0056b3;
        }

        form button:active {
            background-color: #004085;
        }

        .toast {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background-color: #007BFF;
            color: #ffffff;
            padding: 15px 20px;
            border-radius: 5px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            font-size: 14px;
            opacity: 0;
            transform: translateY(20px);
            transition: opacity 0.3s ease, transform 0.3s ease;
        }

        .toast.show {
            opacity: 1;
            transform: translateY(0);
        }
    </style>
</head>
<body>
    <form action="index.php" method="POST">
        <h2>Report Issue</h2>
        <input type="text" name="name" placeholder="Your Name" required>
        <input type="email" name="email" placeholder="Your Email" required>
        <textarea name="message" placeholder="Your Message" required></textarea>
        <button type="submit">Send</button>
    </form>

    <div id="toast" class="toast"></div>

    <script>
        const toast = document.getElementById('toast');
        const urlParams = new URLSearchParams(window.location.search);
        const message = urlParams.get('message');

        if (message) {
            toast.textContent = message;
            toast.classList.add('show');
            setTimeout(() => {
                toast.classList.remove('show');
            }, 3000);
        }
    </script>
</body>
</html>

