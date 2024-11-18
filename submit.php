    <?php

    require_once __DIR__.'/vendor/autoload.php';
    require_once __DIR__.'/functions.php';
    require_once __DIR__.'/config.php';
    include '../public_register.php';
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST" && $_POST["submit"] {
        redirectWithError("The form must be submitted with POST data.");
    }

    // Do some validation, check to make sure the name, email and message are valid.
    if (empty($_POST['g-recaptcha-response'])) {
        redirectWithError("Please complete the CAPTCHA.");
    }

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
        redirectWithError("Please retry the CAPTCHA: ".$errorMessage);
    }

    // Everything seems OK, time to send the email.
    $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
    try {
        // Server settings
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

        // Recipients
        $mail->setFrom(CONTACTFORM_FROM_ADDRESS, CONTACTFORM_FROM_NAME);
        $mail->addAddress(CONTACTFORM_TO_ADDRESS, CONTACTFORM_TO_NAME);

        // Content
        $mail->Subject = "[Contact Form] ";
        $mail->Body = <<<EOT
        Gracias por registrarte!
        Tu cuenta ha sido creada, actívala utilizando el enlace de la parte inferior.

        ------------------------
        Name: {$_POST['name']}
        Email: {$_POST['email']}
        Password: {$_POST['pass']}
        ------------------------

        Por favor haz clic en este enlace para activar tu cuenta:

        http://shadowwilds.infinityfreeapp.com/activar.php?email={$_POST['email']}&activation_hash={$activationHash};

        -------------------------------

        EOT;

        $mail->send();

        // Redirect to success page
        redirectSuccess();
    } catch (Exception $e) {
        redirectWithError("An error occurred while trying to send your message: ".$mail->ErrorInfo);
    }
    ?>
