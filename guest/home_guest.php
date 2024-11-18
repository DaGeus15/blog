<?php

include '../components/connect.php';

session_start();

$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : '';
function isImage($filename)
{
   $allowed = array('gif', 'png', 'jpg', 'jpeg');
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

   <link rel="stylesheet" href="../css/style.css">

</head>

<body>

   <?php include '../guest/guest_header.php'; ?>

   <section class="home-grid">

      <div class="box-container">

         <div class="box">
            <p class="name">Iniciar sesión o registrarse</p>
            <div class="flex-btn">
               <a href="../public/login_public.php" class="option-btn">Iniciar Sesión</a>
               <a href="../public/register_public.php" class="option-btn">Registrarse</a>
            </div>

         </div>

         <div class="box">
            <p>Categorias</p>
            <div class="flex-box">
               <a href="../category.php?category=Naturaleza" class="links">Naturaleza</a>
               <a href="../category.php?category=Viajes" class="links">Viajes</a>
               <a href="../category.php?category=Juegos" class="links">Juegos</a>
               <a href="../category.php?category=Deportes" class="links">Deportes</a>
               <a href="../category.php?category=Moda" class="links">Moda</a>
               <a href="../category.php?category=Diseño y Desarrollo" class="links">Diseño y Desarrollo</a>
               <a href="../category.php?category=Personal" class="links">Personal</a>

               <a href="../all_category.php" class="btn btn-success btn-lg btn-block">Visualizar todo</a>
            </div>
         </div>

         <div class="box">
            <p>Autores</p>
            <div class="flex-box">
               <!-- Lista de autores con nombres inventados -->
               <a href="../author_posts.php?author=Juan_Perez" class="links">Juan Pérez</a>
               <a href="../author_posts.php?author=Maria_Gomez" class="links">María Gómez</a>
               <a href="../author_posts.php?author=Luis_Martinez" class="links">Luis Martínez</a>
               <a href="../author_posts.php?author=Ana_Rodriguez" class="links">Ana Rodríguez</a>

               <!-- Botón para visualizar todos los autores -->
               <a href="../authors.php" class="btn btn-success btn-lg btn-block">Visualizar todo</a>
            </div>
         </div>

      </div>

   </section>

   <section class="posts-container">

      <h1 class="heading">Ultimas publicaciones</h1>

      <div class="box-container">
         <!-- Post 1 -->
         <form class="box" method="post">
            <input type="hidden" name="post_id" value="1">
            <input type="hidden" name="admin_id" value="1">
            <div class="post-admin">
               <i class="fas fa-user"></i>
               <div>
                  <a href="../author_posts.php?author=Juan_Perez">Juan Pérez</a>
                  <div>2024-11-10</div>
               </div>
            </div>

            <div class="post-title">Cómo mejorar tus habilidades de programación</div>
            <div class="post-content content-150">En este post, exploramos las mejores técnicas para mejorar tus habilidades de programación y convertirte en un experto en el campo.</div>
            <a href="../view_post.php?post_id=1" class="inline-btn">Leer más</a>
            <a href="../category.php?category=Educación" class="post-cat">
               <i class="fas fa-tag"></i> <span>Educación</span>
            </a>
            <div class="icons">
               <a href="../view_post.php?post_id=1"><i class="fas fa-comment"></i><span>(12)</span></a>
               <button type="submit" name="like_post" disabled>
                  <i class="fas fa-heart" style="color:var(--red);"></i>
                  <span>(50)</span>
               </button>
            </div>
         </form>

         <!-- Post 2 -->
         <form class="box" method="post">
            <input type="hidden" name="post_id" value="2">
            <input type="hidden" name="admin_id" value="2">
            <div class="post-admin">
               <i class="fas fa-user"></i>
               <div>
                  <a href="../author_posts.php?author=Maria_Gomez">María Gómez</a>
                  <div>2024-11-09</div>
               </div>
            </div>

            <div class="post-title">5 consejos para un estilo de vida saludable</div>
            <div class="post-content content-150">Aquí te dejo algunos consejos prácticos para mantener una vida saludable y llena de energía a lo largo del día.</div>
            <a href="../view_post.php?post_id=2" class="inline-btn">Leer más</a>
            <a href="../category.php?category=Salud" class="post-cat">
               <i class="fas fa-tag"></i> <span>Salud</span>
            </a>
            <div class="icons">
               <a href="../view_post.php?post_id=2"><i class="fas fa-comment"></i><span>(8)</span></a>
               <button type="submit" name="like_post" disabled>
                  <i class="fas fa-heart"></i>
                  <span>(30)</span>
               </button>
            </div>
         </form>

         <!-- Post 3 -->
         <form class="box" method="post">
            <input type="hidden" name="post_id" value="3">
            <input type="hidden" name="admin_id" value="3">
            <div class="post-admin">
               <i class="fas fa-user"></i>
               <div>
                  <a href="../author_posts.php?author=Luis_Martinez">Luis Martínez</a>
                  <div>2024-11-08</div>
               </div>
            </div>

            <div class="post-title">Guía para viajar por el mundo con bajo presupuesto</div>
            <div class="post-content content-150">Si sueñas con viajar pero no tienes mucho dinero, este post te dará algunos consejos sobre cómo hacerlo de manera económica.</div>
            <a href="../view_post.php?post_id=3" class="inline-btn">Leer más</a>
            <a href="../category.php?category=Viajes" class="post-cat">
               <i class="fas fa-tag"></i> <span>Viajes</span>
            </a>
            <div class="icons">
               <a href="../view_post.php?post_id=3"><i class="fas fa-comment"></i><span>(25)</span></a>
               <button type="submit" name="like_post" disabled>
                  <i class="fas fa-heart" style="color:var(--red);"></i>
                  <span>(100)</span>
               </button>
            </div>
         </form>
      </div>


      <div class="more-btn" style="text-align: center; margin-top:1rem;">
         <a href="../posts.php" class="inline-btn">Ver todas las publicaciones</a>
      </div>

   </section>


   <script src="../js/script.js"></script>

</body>

</html>