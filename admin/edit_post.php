<?php

include '../components/connect.php';

session_start();

$user_id = $_SESSION['user_id'];

if (!isset($user_id)) {
   header('location:../public/login_public.php');
}

function isImage($filename) {
   $allowed = array('gif', 'png', 'jpg', 'jpeg');
   $ext = pathinfo($filename, PATHINFO_EXTENSION);
   return in_array($ext, $allowed);
}

   function resize_image($file, $target, $max_width, $max_height)
{
    list($original_width, $original_height) = getimagesize($file);
    $ratio = min($max_width / $original_width, $max_height / $original_height);
    $new_width = $original_width * $ratio;
    $new_height = $original_height * $ratio;
    $src = imagecreatefromstring(file_get_contents($file));
    $dst = imagecreatetruecolor($new_width, $new_height);
    imagecopyresampled($dst, $src, 0, 0, 0, 0, $new_width, $new_height, $original_width, $original_height);
    $result = imagejpeg($dst, $target);
    imagedestroy($src);
    imagedestroy($dst);

    return $result;
}

if (isset($_POST['save'])) {
    $post_id = $_GET['id'];
    $title = $_POST['title'];
    $title = filter_var($title, FILTER_SANITIZE_STRING);
    $content = $_POST['content'];
    $content = filter_var($content, FILTER_SANITIZE_STRING);
    $category = $_POST['category'];
    $category = filter_var($category, FILTER_SANITIZE_STRING);
    $status = $_POST['status'];
    $status = filter_var($status, FILTER_SANITIZE_STRING);

    $update_post = $conn->prepare("UPDATE `posts` SET title = ?, content = ?, category = ?, status = ? WHERE id = ?");
    $update_post->execute([$title, $content, $category, $status, $post_id]);

    $message[] = 'Post updated!';

    $old_image = $_POST['old_image'];
    $image = $_FILES['image']['name'];
    $image = filter_var($image, FILTER_SANITIZE_STRING);
    $image_tmp_name = $_FILES['image']['tmp_name'];

    if (!empty($image)) {
        $image_folder = '../uploaded_img/' . $image;

        if (!move_uploaded_file($image_tmp_name, $image_folder)) {
            $message[] = 'Failed to move uploaded file!';
        } else {
            if(isset($image) && getimagesize($image_tmp_name)){
                resize_image($image_tmp_name, $image_folder, 500, 500);
                $message[] = 'Imagen redimensionada!';
            }
        $update_image = $conn->prepare("UPDATE `posts` SET image = ? WHERE id = ?");
        $update_image->execute([$image, $post_id]);

        if ($old_image != $image && $old_image != '') {
            unlink('../uploaded_img/' . $old_image);
        }

        $message[] = 'Image updated!';
}
    } else {
        // No se subió una nueva imagen, mantener la imagen existente
        $image = $old_image;
        $update_image = $conn->prepare("UPDATE `posts` SET image = ? WHERE id = ?");
        $update_image->execute([$image, $post_id]);
        $message[] = 'No image uploaded, existing image retained!';
    }
}

if (isset($_POST['delete_post'])) {

   $post_id = $_POST['post_id'];
   $post_id = filter_var($post_id, FILTER_SANITIZE_STRING);
   $delete_image = $conn->prepare("SELECT * FROM `posts` WHERE id = ?");
   $delete_image->execute([$post_id]);
   $fetch_delete_image = $delete_image->fetch(PDO::FETCH_ASSOC);
   if ($fetch_delete_image['image'] != '') {
      unlink('../uploaded_img/' . $fetch_delete_image['image']);
   }
   $delete_post = $conn->prepare("DELETE FROM `posts` WHERE id = ?");
   $delete_post->execute([$post_id]);
   $delete_comments = $conn->prepare("DELETE FROM `comments` WHERE post_id = ?");
   $delete_comments->execute([$post_id]);
   $message[] = 'Blog eliminado exitosamente!';
}

if (isset($_POST['delete_image'])) {

   $empty_image = '';
   $post_id = $_POST['post_id'];
   $post_id = filter_var($post_id, FILTER_SANITIZE_STRING);
   $delete_image = $conn->prepare("SELECT * FROM `posts` WHERE id = ?");
   $delete_image->execute([$post_id]);
   $fetch_delete_image = $delete_image->fetch(PDO::FETCH_ASSOC);
   if ($fetch_delete_image['image'] != '') {
      unlink('../uploaded_img/' . $fetch_delete_image['image']);
   }
   $unset_image = $conn->prepare("UPDATE `posts` SET image = ? WHERE id = ?");
   $unset_image->execute([$empty_image, $post_id]);
   $message[] = 'Imagen eliminada exitosamente!';
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>posts</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="../css/admin_style.css">

</head>

<body>

   <?php include '../components/admin_header.php' ?>

   <section class="post-editor">

      <h1 class="heading">Editar Blog</h1>

      <?php
      $post_id = $_GET['id'];
      $select_posts = $conn->prepare("SELECT * FROM `posts` WHERE id = ?");
      $select_posts->execute([$post_id]);
      if ($select_posts->rowCount() > 0) {
         while ($fetch_posts = $select_posts->fetch(PDO::FETCH_ASSOC)) {
      ?>
            <form action="" method="post" enctype="multipart/form-data">
               <input type="hidden" name="old_image" value="<?= $fetch_posts['image']; ?>">
               <input type="hidden" name="post_id" value="<?= $fetch_posts['id']; ?>">
               <p>Estado del post <span>*</span></p>
               <select name="status" class="box" required>
                  <option value="<?= $fetch_posts['status']; ?>" selected><?= $fetch_posts['status']; ?></option>
                  <option value="active">active</option>
                  <option value="deactive">deactive</option>
               </select>
               <p>Título de blog <span>*</span></p>
               <input type="text" name="title" maxlength="100" required placeholder="add post title" class="box" value="<?= $fetch_posts['title']; ?>">
               <p>Contenido del blog <span>*</span></p>
               <textarea name="content" class="box" required maxlength="10000" placeholder="write your content..." cols="30" rows="10"><?= $fetch_posts['content']; ?></textarea>
               <p>Categoría del Blog <span>*</span></p>
               <select name="category" class="box" required>
                  <option value="<?= $fetch_posts['category']; ?>" selected><?= $fetch_posts['category']; ?></option>

                  <option value="" selected disabled>-- Seleccionar categoría* </option>
                  <option value="Naturaleza">Naturaleza</option>
                  <option value="Moda">Moda</option>
                  <option value="Juegos">Juegos</option>
                  <option value="Deportes">Deportes</option>
                  <option value="Viajes">Viajes</option>
                  <option value="Diseño y Desarrollo">Diseño y Desarrollo</option>
                  <option value="Personal">Personal</option>

               </select>
               <p>Archivo de Blog</p>
               <input type="file" name="image" class="box">
               <?php if ($fetch_posts['image'] != '') { ?>
                  <?php $imageSrc = isImage($fetch_posts['image']) ? '../uploaded_img/' . $fetch_posts['image'] : '../img/folder-png-folder-icon-1600.png'; ?>
                  <img src="<?= $imageSrc ?>" class="image" alt="">
                  <input type="submit" value="Eliminar Archivo" class="inline-delete-btn" name="delete_image">
               <?php } ?>
               <div class="flex-btn">
                  <input type="submit" value="Guardar Blog" name="save" class="btn">
                  <a href="view_posts.php" class="option-btn">Volver</a>
                  <input type="submit" value="Eliminar Blog" class="delete-btn" name="delete_post">
               </div>
            </form>

         <?php
         }
      } else {
         echo '<p class="empty">Blog no encontrado</p>';
         ?>
         <div class="flex-btn">
            <a href="view_posts.php" class="option-btn">Ver blog</a>
            <a href="add_posts.php" class="option-btn">Agregar Blog</a>
         </div>
      <?php
      }
      ?>

   </section>
   <!-- custom js file link  -->
   <script src="../js/admin_script.js"></script>

</body>

</html><?php
