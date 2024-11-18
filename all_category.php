<?php

include 'components/connect.php';

session_start();
if(isset($_SESSION['user_id'])){
   // Usuario registrado
   $user_id = $_SESSION['user_id'];
   include 'components/user_header.php'; 
} else {
   // Usuario invitado
   include 'guest/guest_header.php';
   $user_id='';
   // Puedes realizar cualquier acción adicional para usuarios invitados aquí
}

include 'components/like_post.php';

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>category</title>

   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <link rel="stylesheet" href="css/style.css">

</head>
<body>

<section class="categories">

   <h1 class="heading">Categorías de publicaciones</h1>

   <div class="box-container">
      <div class="box"><span>01</span><a href="category.php?category=Naturaleza">Naturaleza</a></div>
      <div class="box"><span>02</span><a href="category.php?category=Moda">Moda</a></div>
      <div class="box"><span>03</span><a href="category.php?category=Juegos">Juegos</a></div>
      <div class="box"><span>04</span><a href="category.php?category=Deportes">Deportes</a></div>
      <div class="box"><span>05</span><a href="category.php?category=Viajes">Viajes</a></div>
      <div class="box"><span>06</span><a href="category.php?category=Diseño y Desarrollo">Diseño y desarrollo</a></div>
      <div class="box"><span>07</span><a href="category.php?category=Personal">Personal</a></div>
   </div>

</section>



<!-- custom js file link  -->
<script src="js/script.js"></script>

</body>
</html>