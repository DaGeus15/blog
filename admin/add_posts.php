<?php
include '../components/connect.php';

session_start();

$user_id = $_SESSION['user_id'];

if (!isset($user_id)) {
   header('location:../public/login_public.php');
}

if (isset($_POST['publish']) || isset($_POST['draft'])) {

   $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
   $title = filter_var($_POST['title'], FILTER_SANITIZE_STRING);
   $content = filter_var($_POST['content'], FILTER_SANITIZE_STRING);
   $category = filter_var($_POST['category'], FILTER_SANITIZE_STRING);
   $status = isset($_POST['publish']) ? 'active' : 'deactive';

   $image = $_FILES['image']['name'];
   $image = filter_var($image, FILTER_SANITIZE_STRING);
   $image_size = $_FILES['image']['size'];
   $image_tmp_name = $_FILES['image']['tmp_name'];
   $image_folder = '../uploaded_img/' . $image;

   $select_image = $conn->prepare("SELECT * FROM `posts` WHERE image = ? AND user_id = ?");
   $select_image->execute([$image, $user_id]);

   if (isset($image) && getimagesize($image_tmp_name)) { // Verifica si el archivo es una imagen
      if ($select_image->rowCount() > 0 && $image != '') {
         $message[] = '¡Nombre de archivo existente!';
      } elseif ($image_size > 2000000) {
         $message[] = '¡El archivo es muy grande!';
      } else {
         // Redimensionar la imagen
         resize_image($image_tmp_name, $image_folder, 500, 500);
      }
   } else {
      $image = '';
   }

   if ($select_image->rowCount() > 0 && $image != '') {
      $message[] = '¡Porfavor, renombra tu archivo!';
   } else {
      $insert_post = $conn->prepare("INSERT INTO `posts`(user_id, name, title, content, category, image, status) VALUES(?,?,?,?,?,?,?)");
      $insert_post->execute([$user_id, $name, $title, $content, $category, $image, $status]);
      $message[] = isset($_POST['publish']) ? '¡Post Publicado!' : '¡Borrador guardado!';
   }
}
function resize_image($file, $target, $max_width, $max_height) {
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
      <h1 class="heading">Agregar Blog</h1>
      <form action="" method="post" enctype="multipart/form-data">
         <input type="hidden" name="name" value="<?= $fetch_profile['name']; ?>">
         <p>Título de blog <span>*</span></p>
         <input type="text" name="title" maxlength="100" required placeholder="Título del Blog" class="box">
         <p>Contenido de blog <span>*</span></p>
         <textarea name="content" class="box" required maxlength="10000" placeholder="Escribe el contenido..." cols="30" rows="10"></textarea>
         <p>Categoría de blog <span>*</span></p>
         <select name="category" class="box" required>
            <option value="" selected disabled>-- Seleccionar categoría* </option>
            <option value="Naturaleza">Naturaleza</option>
            <option value="Moda">Moda</option>
            <option value="Juegos">Juegos</option>
            <option value="Deportes">Deportes</option>
            <option value="Viajes">Viajes</option>
            <option value="Diseño y Desarrollo">Diseño y Desarrollo</option>
            <option value="Personal">Personal</option>
         </select>
         <p>Publicar Archivo</p>
         <input type="file" name="image" class="box">
         <div class="flex-btn">
            <input type="submit" value="Publicar Blog" name="publish" class="btn">
            <input type="submit" value="Guardar Borrador" name="draft" class="option-btn">
         </div>
      </form>
   </section>
   <script src="../js/admin_script.js"></script>
</body>
</html>