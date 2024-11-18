<?php

include '../components/connect.php';

session_start();

if(isset($_SESSION['user_id'])){
   $user_id = $_SESSION['user_id'];
}else{
   $user_id = '';
};

if(isset($_POST['submit'])){

   $email = $_POST['email'];
   $email = filter_var($email, FILTER_SANITIZE_STRING);
   $pass = sha1($_POST['pass']);
   $pass = filter_var($pass, FILTER_SANITIZE_STRING);

   $select_user = $conn->prepare("SELECT * FROM `users` WHERE email = ? AND password = ?");
   $select_user->execute([$email, $pass]);
   $row = $select_user->fetch(PDO::FETCH_ASSOC);

   if($select_user->rowCount() > 0){
      if($row['is_active'] == 1){
         $_SESSION['user_id'] = $row['id'];
         header('location:../home.php');
      } else {
         $message[] = 'La cuenta no está activada. Por favor, activa tu cuenta antes de iniciar sesión.';
      }
   }else{
      $message[] = 'Usuario o contraseña incorrecta';
   }

}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Login</title>

   
   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="../css/style.css">

</head>
<body>
<?php include '../guest/guest_header.php'; ?>
<section class="form-container ">

   <form action="" method="post" style="backdrop-filter: blur(15px);" >
      <h3>Iniciar sesión</h3>
      <input type="email" name="email" required placeholder="Correo Electrónico" class="box" maxlength="50" oninput="this.value = this.value.replace(/\s/g, '')">
      <input type="password" name="pass" required placeholder="Contraseña" class="box" maxlength="50" oninput="this.value = this.value.replace(/\s/g, '')">
      <input type="submit" value="Acceder" name="submit" class="btn btn-block">
      <p><a style="color: red;" href="../recuperar_cuenta.php">Recuperar cuenta</a></p>
      <p> ¿No tiene cuenta? <a style="color: red;" href="register_public.php">Registrar Ahora</a></p>
      
   </form>

</section>
<script src="../js/script.js"></script>
</body>
</html>