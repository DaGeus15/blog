<?php
include '../components/connect.php';
include '../vendor/autoload.php';
include '../config.php';

session_start();

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
} else {
    $user_id = '';
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
    $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $pass = sha1($_POST['pass']);
    $pass = filter_var($pass, FILTER_SANITIZE_STRING);
    $cpass = sha1($_POST['cpass']);
    $cpass = filter_var($cpass, FILTER_SANITIZE_STRING);

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
            if ($pass != $cpass) {
                $message[] = 'Contraseña incorrecta!';
            } else {
                $activationHash = md5(uniqid(rand(), true));

                $select_user = $conn->prepare("SELECT * FROM `users` WHERE email = ?");
                $select_user->execute([$email]);
                if ($select_user->rowCount() > 0) {
                    $message[] = 'El correo ya existe!';
                } else {
                   // $pass_hashed = password_hash($pass, PASSWORD_DEFAULT);
                    $insert_user = $conn->prepare("INSERT INTO `users` (name, email, password, activation_hash, is_active) VALUES (?, ?, ?, ?, 0)");
                    if ($insert_user->execute([$name, $email, $cpass, $activationHash])) {
                        $user_id = $conn->lastInsertId();
                        $_SESSION['user_id'] = $user_id;

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
                            $mail->addAddress($email, $name);

                            $mail->Subject = "Account Activation";
                            $mail->Body = <<<EOT
Gracias por registrarse!
Tu cuenta ha sido creada. Actívelo utilizando el siguiente enlace.

------------------------
Nombre: {$name}
Correo electrónico: {$email}
------------------------

Por favor, clic aquí para activar su cuenta:
http://shadowwilds.great-site.net/activar.php?email={$email}&activation_hash={$activationHash};

-------------------------------

EOT;

                            $mail->send();

                            header('location: ../public/login_public.php');
                            exit;
                        } catch (Exception $e) {
                            $message[] = "Error al enviar el correo.: " . $mail->ErrorInfo;
                        }
                    } else {
                        $message[] = 'Error al registrar usuario.';
                    }
                }
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
    <title>Register</title>
    <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="../css/style.css">

    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</head>
<body>

<?php include '../guest/guest_header.php'; ?>

<section class="form-container">
    <form action="" method="post">
        <h3>Registrarse</h3>
        <?php if(isset($message)) { ?>
        <div class="alert alert-danger" role="alert">
            <?php foreach($message as $msg) { ?>
            <p><?php echo $msg; ?></p>
            <?php } ?>
        </div>
        <?php } ?>
        <input type="text" name="name" id="name" required placeholder="Nombre" class="box form-control" maxlength="50">
        <input type="email" name="email" id="email" required placeholder="Correo Electrónico" class="box form-control" maxlength="50" oninput="this.value = this.value.replace(/\s/g, '')">
        <input type="password" name="pass" id="pass" required placeholder="Contraseña " class="box form-control" minlength="8" maxlength="50" pattern="(?=.*\d)(?=.*[a-zA-Z]).{8,}" oninput="this.value = this.value.replace(/\s/g, '' ">
        
        <input type="password" name="cpass" id="cpass" required placeholder="Confirmar contraseña" class="box form-control" maxlength="50" oninput="this.value = this.value.replace(/\s/g, '')">
        <div class="password-requirements">
            <p>La contraseña debe tener al menos 8 caracteres, incluir al menos un número y una letra.</p>
        </div>
        <div class="g-recaptcha" data-sitekey="<?= CONTACTFORM_RECAPTCHA_SITE_KEY ?>"></div>
        <button type="submit" name="submit" class="btn btn-block">Registrarse</button>
        <p>Ya tienes una cuenta? <a href="login_public.php">Inicio Sesión</a></p>
    </form>
</section>
<script src="../js/script.js"></script>
</body>
</html>

