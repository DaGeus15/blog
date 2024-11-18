<?php
include 'components/connect.php';
include 'vendor/autoload.php';
include 'config.php';

session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);

    if (empty($_POST['g-recaptcha-response'])) {
        $message[] = 'Por favor completar el CAPTCHA.';
    } else {
        $recaptcha = new \ReCaptcha\ReCaptcha(CONTACTFORM_RECAPTCHA_SECRET_KEY);
        $resp = $recaptcha->verify($_POST['g-recaptcha-response'], $_SERVER['REMOTE_ADDR']);

        if (!$resp->isSuccess()) {
            $errors = $resp->getErrorCodes();
            $error = $errors[0];
            $recaptchaErrorMapping = [
                'missing-input-secret' => 'No reCAPTCHA secret key was submitted.',
                'invalid-input-secret' => 'The submitted reCAPTCHA secret key was invalid.',
                'missing-input-response' => 'No reCAPTCHA response was submitted.',
                'invalid-input-response' => 'The submitted reCAPTCHA response was invalid.',
                'bad-request' => 'An unknown error occurred while trying to validate your response.',
                'timeout-or-duplicate' => 'The request is no longer valid. Please try again.',
            ];

            $errorMessage = $recaptchaErrorMapping[$error];
            $message[] = "Por favor, repetir el CAPTCHA: " . $errorMessage;
        } else {
            $select_user = $conn->prepare("SELECT * FROM `users` WHERE email = ?");
            $select_user->execute([$email]);

            if ($select_user->rowCount() > 0) {

                function generateToken($length = 5)
                {
                    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                    return substr(str_shuffle($characters), 0, $length);
                }

                $token = generateToken(5);


                $update_token = $conn->prepare("UPDATE `users` SET reset_token = ? WHERE email = ?");
                $update_token->execute([$token, $email]);


                $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
                try {
                    $mail->setLanguage(CONTACTFORM_LANGUAGE);
                    $mail->SMTPDebug = CONTACTFORM_PHPMAILER_DEBUG_LEVEL;
                    $mail->isSMTP();
                    $mail->Host = CONTACTFORM_SMTP_HOSTNAME;
                    $mail->SMTPAuth = true;
                    $mail->Username = CONTACTFORM_SMTP_USERNAME;
                    $mail->Password = CONTACTFORM_SMTP_PASSWORD;
                    $mail->SMTPSecure = CONTACTFORM_SMTP_ENCRYPTION;
                    $mail->Port = CONTACTFORM_SMTP_PORT;
                    $mail->CharSet = CONTACTFORM_MAIL_CHARSET;
                    $mail->Encoding = CONTACTFORM_MAIL_ENCODING;

                    $mail->setFrom(CONTACTFORM_FROM_ADDRESS, CONTACTFORM_FROM_NAME);
                    $mail->addAddress($email);

                    $mail->Subject = "Recuperación de Cuenta";
                    $mail->Body = <<<EOT
Recuperacion de cuenta solicitada.

Tu token de recuperación es: {$token}

Utiliza este token para recuperar tu cuenta.

EOT;

                    $mail->send();
                    header('Location: actualizar_contrasenia.php');
                    exit();
                    
                } catch (Exception $e) {
                    $message[] = "Error al enviar el correo: " . $mail->ErrorInfo;
                }
            } else {
                $message[] = 'Email no registrado.';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Cuenta</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link rel="stylesheet" href="/css/style.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</head>

<body>

    <?php include 'components/public_header.php'; ?>

    <section class="form-container">
        <form action="" method="post">
            <h3>Recuperación de Cuenta</h3>
            <?php if (isset($message)) { ?>
                <div class="alert alert-danger" role="alert">
                    <?php foreach ($message as $msg) { ?>
                        <p><?php echo $msg; ?></p>
                    <?php } ?>
                </div>
            <?php } ?>
            <input type="email" name="email" id="email" required placeholder="Correo Electrónico" class="box form-control" maxlength="50" oninput="this.value = this.value.replace(/\s/g, '')">
            <div class="g-recaptcha" data-sitekey="<?= CONTACTFORM_RECAPTCHA_SITE_KEY ?>"></div>
            <button type="submit" name="submit" class="btn btn-primary btn-lg btn-block">Recuperar</button>
        </form>
    </section>

</body>

</html>