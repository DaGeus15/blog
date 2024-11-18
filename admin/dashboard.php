<?php

include '../components/connect.php';

session_start();

$user_id = $_SESSION['user_id'];

if(!isset($user_id)){
   header('location:../public/login_public.php');
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>ShadowWilds-Autor</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="../css/admin_style.css">

</head>
<body>

<?php include '../components/admin_header.php' ?>

<!-- admin dashboard section starts  -->

<section class="dashboard">

   <h1 class="heading">Panel de Autor</h1>

   <div class="box-container">

   

   <div class="box">
      <?php
         $select_posts = $conn->prepare("SELECT * FROM `posts` WHERE user_id = ?");
         $select_posts->execute([$user_id]);
         $numbers_of_posts = $select_posts->rowCount();
      ?>
      <h3><?= $numbers_of_posts; ?></h3>
      <p>Publicación añadida</p>
      <a href="add_posts.php" class="btn">Añadir nueva publicación</a>
   </div>

   <div class="box">
      <?php
         $select_active_posts = $conn->prepare("SELECT * FROM `posts` WHERE user_id = ? AND status = ?");
         $select_active_posts->execute([$user_id, 'active']);
         $numbers_of_active_posts = $select_active_posts->rowCount();
      ?>
      <h3><?= $numbers_of_active_posts; ?></h3>
      <p>publicación activa</p>
      <a href="view_posts.php" class="btn">Ver publicación</a>
   </div>

   <div class="box">
      <?php
         $select_deactive_posts = $conn->prepare("SELECT * FROM `posts` WHERE user_id = ? AND status = ?");
         $select_deactive_posts->execute([$user_id, 'deactive']);
         $numbers_of_deactive_posts = $select_deactive_posts->rowCount();
      ?>
      <h3><?= $numbers_of_deactive_posts; ?></h3>
      <p>Publicación desactiva</p>
      <a href="view_posts.php" class="btn">Ver publicación</a>
   </div>

   <div class="box">
      <?php
         $select_comments = $conn->prepare("SELECT * FROM `comments` WHERE commenter_id = ?");
         $select_comments->execute([$user_id]);
         $numbers_of_comments = $select_comments->rowCount();
      ?>
      <h3><?= $numbers_of_comments; ?></h3>
      <p>Comentario añadido</p>
      <a href="comments.php" class="btn">Ver comentarios</a>
   </div>

   <div class="box">
      <?php
         $select_likes = $conn->prepare("SELECT * FROM `likes` WHERE user_id = ?");
         $select_likes->execute([$user_id]);
         $numbers_of_likes = $select_likes->rowCount();
      ?>
      <h3><?= $numbers_of_likes; ?></h3>
      <p>Total de me gusta</p>
      <a href="view_posts.php" class="btn">Ver publicación</a>
   </div>

   </div>

</section>

<script src="../js/admin_script.js"></script>

</body>
</html>
