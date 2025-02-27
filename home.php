<?php

include 'components/connect.php';

session_start();

if (isset($_SESSION['user_id'])) {
   $user_id = $_SESSION['user_id'];
} else {
   $user_id = '';
};

include 'components/like_post.php';

function isImage($filename)
{
   $allowed = array('gif', 'png', 'jpg', 'jpeg','webp');
   $ext = pathinfo($filename, PATHINFO_EXTENSION);
   return in_array($ext, $allowed);
}
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

   <link rel="stylesheet" href="css/style.css">

</head>

<body>

   <?php include 'components/user_header.php'; ?>

   <section class="home-grid">

      <div class="box-container">

         <div class="box">
            <?php
            $select_profile = $conn->prepare("SELECT * FROM `users` WHERE id = ?");
            $select_profile->execute([$user_id]);
            if ($select_profile->rowCount() > 0) {
               $fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);
               $count_user_comments = $conn->prepare("SELECT * FROM `comments` WHERE commenter_id = ?");
               $count_user_comments->execute([$user_id]);
               $total_user_comments = $count_user_comments->rowCount();
               $count_user_likes = $conn->prepare("SELECT * FROM `likes` WHERE user_id = ?");
               $count_user_likes->execute([$user_id]);
               $total_user_likes = $count_user_likes->rowCount();
            ?>
               <p> Bienvenido: <span><?= $fetch_profile['name']; ?></span></p>
               <p>Total de comentarios: <span><?= $total_user_comments; ?></span></p>
               <p>Post me gusta : <span><?= $total_user_likes; ?></span></p>
               <a href="update.php" class="btn btn-success btn-lg btn-block">Actualizar perfil</a>
               <div class="flex-btn">
                  <a href="user_likes.php" class="option-btn">Me gusta</a>
                  <a href="user_comments.php" class="option-btn">Comentarios</a>
               </div>
            <?php
            } else {
            ?>
               <p class="name">Iniciar sesión o registrarse</p>
               <div class="flex-btn">
                  <a href="../public/login_public.php" class="option-btn">Iniciar Sesión</a>
                  <a href="../public/register_public.php" class="option-btn">Registrarse</a>
               </div>
            <?php
            }
            ?>
         </div>

         <div class="box">
            <p>Categorias</p>
            <div class="flex-box">
               <a href="category.php?category=Naturaleza" class="links">Naturaleza</a>
               <a href="category.php?category=Viajes" class="links">Viajes</a>
               <a href="category.php?category=Juegos" class="links">Juegos</a>
               <a href="category.php?category=Deportes" class="links">Deportes</a>
               <a href="category.php?category=Moda" class="links">Moda</a>
               <a href="category.php?category=Diseño y Desarrollo" class="links">Diseño y Desarrollo</a>
               <a href="category.php?category=Personal" class="links">Personal</a>

               <a href="all_category.php" class="btn btn-success btn-lg btn-block">Visualizar todo</a>
            </div>
         </div>

         <div class="box">
            <p>Autores</p>
            <div class="flex-box">
               <?php
               $select_authors = $conn->prepare("SELECT * FROM `users` WHERE id IN (SELECT DISTINCT user_id FROM `posts` WHERE status = 'active') LIMIT 10");
               $select_authors->execute();
               if ($select_authors->rowCount() > 0) {
                  while ($fetch_authors = $select_authors->fetch(PDO::FETCH_ASSOC)) {
               ?>
                     <a href="author_posts.php?author=<?= $fetch_authors['name']; ?>" class="links"><?= $fetch_authors['name']; ?></a>
               <?php
                  }
               } else {
                  echo '<p class="empty">No hay posts publicados!</p>';
               }
               ?>
               <a href="authors.php" class="btn btn-success btn-lg btn-block">Visualizar todo</a>
            </div>
         </div>

      </div>

   </section>

   <section class="posts-container">

      <h1 class="heading">Ultimas publicaciones</h1>

      <div class="box-container">

         <?php
         $select_posts = $conn->prepare("SELECT * FROM `posts` WHERE status = ? LIMIT 3 ");
         $select_posts->execute(['active']);
         if ($select_posts->rowCount() > 0) {
            while ($fetch_posts = $select_posts->fetch(PDO::FETCH_ASSOC)) {

               $post_id = $fetch_posts['id'];

               $count_post_comments = $conn->prepare("SELECT * FROM `comments` WHERE post_id = ?");
               $count_post_comments->execute([$post_id]);
               $total_post_comments = $count_post_comments->rowCount();

               $count_post_likes = $conn->prepare("SELECT * FROM `likes` WHERE post_id = ?");
               $count_post_likes->execute([$post_id]);
               $total_post_likes = $count_post_likes->rowCount();

               $confirm_likes = $conn->prepare("SELECT * FROM `likes` WHERE user_id = ? AND post_id = ?");
               $confirm_likes->execute([$user_id, $post_id]);
         ?>
               <form class="box" method="post">
                  <input type="hidden" name="post_id" value="<?= $post_id; ?>">
                  <input type="hidden" name="admin_id" value="<?= $fetch_posts['user_id']; ?>">
                  <div class="post-admin">
                     <i class="fas fa-user"></i>
                     <div>
                        <a href="author_posts.php?author=<?= $fetch_posts['name']; ?>"><?= $fetch_posts['name']; ?></a>
                        <div><?= $fetch_posts['date']; ?></div>
                     </div>
                  </div>

                  <?php
                  if ($fetch_posts['image'] != '') {
                     $imageSrc = isImage($fetch_posts['image']) ? 'uploaded_img/' . $fetch_posts['image'] : 'img/folder-png-folder-icon-1600.png';
                  ?>
                     <img src="<?= $imageSrc; ?>" class="post-image" alt="">
                  <?php
                  }
                  ?>

                  <?php
                  if ($fetch_posts['image'] != '') {
                  ?>
                     <div class="attachment">
                        <a class="attachment-link" href="uploaded_img/<?= $fetch_posts['image']; ?>" download>
                            <i class="fas fa-download"></i>
                        </a>
                    </div>
                  <?php
                  }
                  ?>
                  <div class="post-title"><?= $fetch_posts['title']; ?></div>
                  <div class="post-content content-150"><?= $fetch_posts['content']; ?></div>
                  <a href="view_post.php?post_id=<?= $post_id; ?>" class="inline-btn">Leer más</a>
                  <a href="category.php?category=<?= $fetch_posts['category']; ?>" class="post-cat"> <i class="fas fa-tag"></i> <span><?= $fetch_posts['category']; ?></span></a>
                  <div class="icons">
                     <a href="view_post.php?post_id=<?= $post_id; ?>"><i class="fas fa-comment"></i><span>(<?= $total_post_comments; ?>)</span></a>
                     <button type="submit" name="like_post"><i class="fas fa-heart" style="<?php if ($confirm_likes->rowCount() > 0) {
                                                                                                echo 'color:var(--red);';
                                                                                             } ?>  "></i><span>(<?= $total_post_likes; ?>)</span></button>
                  </div>

               </form>
         <?php
            }
         } else {
            echo '<p class="empty" style="color:black;">no se han añadido publicaciones!</p>';
         }
         ?>
      </div>

      <div class="more-btn" style="text-align: center; margin-top:1rem;">
         <a href="posts.php" class="inline-btn">Ver todas las publicaciones</a>
      </div>

   </section>

   <script src="js/script.js"></script>

</body>

</html>