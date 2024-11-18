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
   // Puedes realizar cualquier acción adicional para usuarios invitados aquí
}

if (isset($_GET['author'])) {
   $author = $_GET['author'];
} else {
   $author = '';
}
function isImage($filename)
{
   $allowed = array('gif', 'png', 'jpg', 'jpeg','webp');
   $ext = pathinfo($filename, PATHINFO_EXTENSION);
   return in_array($ext, $allowed);
}
include 'components/like_post.php';

?>

<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>author</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>

<body>



   <section class="posts-container">

      <div class="box-container">

         <?php
         $select_posts = $conn->prepare("SELECT * FROM `posts` WHERE name = ? and status = ?");
         $select_posts->execute([$author, 'active']);
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

                  
                    <?php if ($fetch_posts['image'] != '') {
                        $imageSrc = isImage($fetch_posts['image']) ? 'uploaded_img/' . $fetch_posts['image'] : 'img/folder-png-folder-icon-1600.png';
                    ?>
                    <div class="image-container">
                    <img src="<?= $imageSrc; ?>" class="image" alt="">
                    </div>
                    <?php } ?>
                  <?php
                  if ($fetch_posts['image'] != '') {
                  ?>
                     <div class="attachment">
                        <a href="uploaded_img/<?= $fetch_posts['image']; ?>" download>
                           <i class="fas fa-download"></i>
                        </a>
                     </div>
                  <?php
                  }
                  ?>
                  <div class="post-title"><?= $fetch_posts['title']; ?></div>
                  <div class="post-content content-150"><?= $fetch_posts['content']; ?></div>
                  <a href="view_post.php?post_id=<?= $post_id; ?>" class="inline-btn">read more</a>
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
            echo '<p class="empty">no posts found for this author!</p>';
         }
         ?>
      </div>

   </section>

   <!-- custom js file link  -->
   <script src="js/script.js"></script>

</body>

</html>