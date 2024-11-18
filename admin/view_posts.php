<?php

include '../components/connect.php';

session_start();

$user_id = $_SESSION['user_id'];

if(!isset($user_id)){
   header('location:../public/login_public.php');
}

if(isset($_POST['delete'])){

   $p_id = $_POST['post_id'];
   $p_id = filter_var($p_id, FILTER_SANITIZE_STRING);
   $delete_image = $conn->prepare("SELECT * FROM `posts` WHERE id = ?");
   $delete_image->execute([$p_id]);
   $fetch_delete_image = $delete_image->fetch(PDO::FETCH_ASSOC);
   if($fetch_delete_image['image'] != ''){
      unlink('../uploaded_img/'.$fetch_delete_image['image']);
   }
   $delete_post = $conn->prepare("DELETE FROM `posts` WHERE id = ?");
   $delete_post->execute([$p_id]);
   $delete_comments = $conn->prepare("DELETE FROM `comments` WHERE post_id = ?");
   $delete_comments->execute([$p_id]);
   $message[] = 'Blog eliminado!';

}

// Verificar si el archivo cargado es una imagen
function isImage($filename) {
    $allowed = array('gif', 'png', 'jpg', 'jpeg','webp');
    $ext = pathinfo($filename, PATHINFO_EXTENSION);
    return in_array($ext, $allowed);
}

// Lógica para mostrar la imagen predeterminada si no es una imagen válida
function getImage($image) {
    if (isImage($image)) {
        return '../uploaded_img/' . $image;
    } else {
        return '../img/folder-png-folder-icon-1600.png'; 
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Blogs</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="../css/admin_style.css">

</head>
<body>

<?php include '../components/admin_header.php' ?>

<section class="show-posts">

   <h1 class="heading">Tus Blogs</h1>

   <div class="box-container">

      <?php
         $select_posts = $conn->prepare("SELECT * FROM `posts` WHERE user_id = ?");
         $select_posts->execute([$user_id]);
         if($select_posts->rowCount() > 0){
            while($fetch_posts = $select_posts->fetch(PDO::FETCH_ASSOC)){
               $post_id = $fetch_posts['id'];

               $count_post_comments = $conn->prepare("SELECT * FROM `comments` WHERE post_id = ?");
               $count_post_comments->execute([$post_id]);
               $total_post_comments = $count_post_comments->rowCount();

               $count_post_likes = $conn->prepare("SELECT * FROM `likes` WHERE post_id = ?");
               $count_post_likes->execute([$post_id]);
               $total_post_likes = $count_post_likes->rowCount();

      ?>
      <form method="post" class="box">
         <input type="hidden" name="post_id" value="<?= $post_id; ?>">
         <?php if($fetch_posts['image'] != ''){ ?>
            <img src="<?= getImage($fetch_posts['image']); ?>" class="image" alt="">
         <?php } ?>
         <div class="status" style="background-color:<?php if($fetch_posts['status'] == 'active'){echo 'limegreen'; }else{echo 'coral';}; ?>;"><?= $fetch_posts['status']; ?></div>
            <div class="title"><?= $fetch_posts['title']; ?></div>
         <div class="posts-content"><?= $fetch_posts['content']; ?></div>
         <div class="icons">
            <div class="likes"><i class="fas fa-heart"></i><span><?= $total_post_likes; ?></span></div>
            <div class="comments"><i class="fas fa-comment"></i><span><?= $total_post_comments; ?></span></div>
         </div>
         <div class="flex-btn">
            <a href="edit_post.php?id=<?= $post_id; ?>" class="option-btn">Editar</a>
            <button type="submit" name="delete" class="delete-btn" onclick="return confirm('¿Eliminar Blog?');">Eliminar</button>
         </div>
         <a href="read_post.php?post_id=<?= $post_id; ?>" class="btn">Ver Blog</a>
      </form>
      <?php
            }
         }else{
            echo '<p class="empty">¡No hay Blogs agregados! <a href="add_posts.php" class="btn" style="margin-top:1.5rem;">Añadir Blog</a></p>';
         }
      ?>

   </div>


</section>

<!-- custom js file link  -->
<script src="../js/admin_script.js"></script>

</body>
</html>