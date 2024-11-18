<?php
if (isset($message)) {
   foreach ($message as $message) {
      echo '
      <div class="message alert alert-danger" role="alert">
      <span>' . $message . '</span>
      <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
      </div>
      ';
   }
}
?>

<header class="header">
   

   <section class="flex">
      <a href="../index.php" class="logo">ShadowWilds</a>

      <form action="../search.php" method="POST" class="search-form">
         <input type="text" name="search_box" class="box" maxlength="100" placeholder="Buscar blogs" required>
         <button type="submit" class="fas fa-search" name="search_btn"></button>
      </form>

      <nav class="navbar">
         <a href="../guest/home_guest.php"><i class="fas fa-home"></i> Inicio</a>
         <a href="../posts.php"><i class="fas fa-pen"></i> Publicaciones</a>
         <a href="../all_category.php"><i class="fas fa-list"></i> Categorias</a>
         <a href="../authors.php"><i class="fas fa-user"></i> Autores</a>
         <a href="../public/login_public.php"><i class="fas fa-sign-in-alt"></i> Inicio sesión</a>
         <a href="../public/register_public.php"><i class="fas fa-user-plus"></i> Registro</a>
      </nav>

      <div class="profile">
         <p class="name">Iniciar sesión</p>
         <a href="../public/login_public.php" class="option-btn">Inicio sesión</a>
      </div>

      
   </section>
</header>

<div id="menu-btn" class="fas fa-bars"></div>