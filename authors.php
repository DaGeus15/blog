<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Autores</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>

<body>

   <?php
   include 'components/connect.php';
   session_start();

   if (isset($_SESSION['user_id'])) {
      // Usuario registrado
      $user_id = $_SESSION['user_id'];
      include 'components/user_header.php';
   } else {
      // Usuario invitado
      include 'guest/guest_header.php';
      $user_id = '';
      
   }

   include 'components/like_post.php';
   ?>

   <section class="authors">

      <h1 class="heading">Autores</h1>

      <div class="box-container">

         <?php
         $select_author = $conn->prepare("SELECT * FROM `users` WHERE id IN (SELECT DISTINCT user_id FROM `posts` WHERE status = 'active')");
         $select_author->execute();
         if ($select_author->rowCount() > 0) {
         ?>
            <div class="box-container">
               <?php
               while ($fetch_authors = $select_author->fetch(PDO::FETCH_ASSOC)) {

                  $count_admin_posts = $conn->prepare("SELECT * FROM `posts` WHERE user_id = ? AND status = ?");
                  $count_admin_posts->execute([$fetch_authors['id'], 'active']);
                  $total_admin_posts = $count_admin_posts->rowCount();

                  $count_admin_likes = $conn->prepare("SELECT * FROM `likes` WHERE user_id = ?");
                  $count_admin_likes->execute([$fetch_authors['id']]);
                  $total_admin_likes = $count_admin_likes->rowCount();

                  $count_admin_comments = $conn->prepare("SELECT * FROM `comments` WHERE commenter_id = ?");
                  $count_admin_comments->execute([$fetch_authors['id']]);
                  $total_admin_comments = $count_admin_comments->rowCount();
               ?>
                  <div class="box">
                     <p>Autor : <span><?= $fetch_authors['name']; ?></span></p>
                     <p>Total de publicaciones : <span><?= $total_admin_posts; ?></span></p>
                     <p>Publicaciones con me gusta : <span><?= $total_admin_likes; ?></span></p>
                     <p>Publicaciones comentadas : <span><?= $total_admin_comments; ?></span></p>
                     <a href="author_posts.php?author=<?= $fetch_authors['name']; ?>" class="btn btn-success btn-lg btn-block">Ver publicaciones</a>
                  </div>
               <?php
               }
               ?>
            </div>
         <?php
         } else {
            echo '<p class="empty">Autor no encontrado</p>';
         }
         ?>

      </div>

   </section>

   <!-- custom js file link  -->
   <script src="js/script.js"></script>

</body>

</html>