<?php
include 'components/connect.php';
include 'vendor/autoload.php';
include 'config.php';

session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
    $token = filter_var($_POST['token'], FILTER_SANITIZE_STRING);
    $pass = sha1($_POST['pass']);
    $pass = filter_var($pass, FILTER_SANITIZE_STRING);
    $cpass = sha1($_POST['cpass']);
    $cpass = filter_var($cpass, FILTER_SANITIZE_STRING);

    if ($pass != $cpass) {
        $message[] = 'Las contraseñas no coinciden!';
    } else {
        $select_user = $conn->prepare("SELECT * FROM `users` WHERE reset_token = ?");
        $select_user->execute([$token]);

        if ($select_user->rowCount() > 0) {
            $update_user = $conn->prepare("UPDATE `users` SET password = ?, reset_token = NULL WHERE reset_token = ?");
            if ($update_user->execute([$pass, $token])) {
                $message[] = 'Contraseña actualizada exitosamente!';
            } else {
                $message[] = 'Error al actualizar la contraseña.';
            }
        } else {
            $message[] = 'Token inválido!';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Actualizar Contraseña</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link rel="stylesheet" href="/css/style.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
</head>
<body>

<?php include 'components/public_header.php'; ?>

<section class="form-container">
    <form action="" method="post">
        <h3>Actualizar Contraseña</h3>
        <?php if(isset($message)) { ?>
        <div class="alert alert-danger" role="alert">
            <?php foreach($message as $msg) { ?>
            <p><?php echo $msg; ?></p>
            <?php } ?>
        </div>
        <?php } ?>
        <input type="text" name="token" id="token" required placeholder="Token" class="box form-control" maxlength="5">
        <input type="password" name="pass" id="pass" required placeholder="Contraseña " class="box form-control" minlength="8" maxlength="50" pattern="(?=.*\d)(?=.*[a-zA-Z]).{8,}" oninput="this.value = this.value.replace(/\s/g, '' ">
        
        <input type="password" name="cpass" id="cpass" required placeholder="Confirmar contraseña" class="box form-control" maxlength="50" oninput="this.value = this.value.replace(/\s/g, '')">
        <div class="password-requirements">
            <p>La contraseña debe tener al menos 8 caracteres, incluir al menos un número y una letra.</p>
        </div>
        <button type="submit" name="submit" class="btn btn-primary btn-lg btn-block">Actualizar</button>
    </form>
</section>

</body>
<?php include 'components/footer.php'; ?>
</html>

