<?php

include 'components/connect.php';

// Inicia una nueva sesión o continúa la sesión existente
session_start();

// Elimina todas las variables de sesión
$_SESSION = array();

if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// Finalmente, destruye la sesión
session_destroy();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ShadowWilds</title>

    <!-- font awesome cdn link  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <!-- custom css file link  -->
    <link rel="stylesheet" href="css/style.css">
    
</head>

<body>

    <div class="master" id="master">
        <div class="row">
            <div class="custom-box">
                <h2>Bienvenido a ShadowWilds</h2>
                <p>Selecciona una opción para continuar</p>
            </div>
            <div class="col-md-12"> 
                <button id="login" type="button" class="btn btn-outline-light">Iniciar Sesion</button>
            </div>
            <div class="col-md-12"> 
                <button id="guest" type="button" class="btn btn-outline-light">Ingresar Como Invitado</button>
            </div>
        </div>
    </div>

    <?php include 'components/footer.php'; ?>

    <!-- custom js file link  -->

    <script src="master.js"></script>
</body>

</html>