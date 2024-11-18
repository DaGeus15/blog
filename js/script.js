// Declaración de las variables al principio del script
let navbar = document.querySelector('.header .flex .navbar');
let profile = document.querySelector('.header .flex .profile');
let searchForm = document.querySelector('.header .flex .search-form');
let header = document.querySelector('.header');

// Función de validación de contraseña
function validarContraseña() {
    var contraseña = document.getElementById("pass").value;
    var mensajeError = "";

    // Verificar longitud mínima
    if (contraseña.length < 8) {
        mensajeError += "La contraseña debe tener al menos 8 caracteres.\n";
    }

    // Verificar si incluye al menos una mayúscula y una minúscula
    if (!/[a-z]/.test(contraseña) || !/[A-Z]/.test(contraseña)) {
        mensajeError += "La contraseña debe incluir al menos una letra mayúscula y una minúscula.\n";
    }

    if (mensajeError !== "") {
        alert(mensajeError);
        return false;
    }

    return true;
}

// Funciones de clic del botón y el menú
document.querySelector('#menu-btn').onclick = () => {
   navbar.classList.toggle('active');
   searchForm.classList.remove('active');
   profile.classList.remove('active');
   header.classList.toggle('active');
}

document.querySelector('#user-btn').onclick = () => {
   profile.classList.toggle('active');
   searchForm.classList.remove('active');
   navbar.classList.remove('active');
   header.classList.remove('active');
}

document.querySelector('#search-btn').onclick = () => {
   searchForm.classList.toggle('active');
   navbar.classList.remove('active');
   profile.classList.remove('active');
   header.classList.remove('active');
}

// Manejar el evento 'scroll'
window.onscroll = () => {
   profile.classList.remove('active');
   navbar.classList.remove('active');
   searchForm.classList.remove('active');
   header.classList.remove('active');
}

// Agrega el evento 'click' para el botón del menú
document.querySelector('#menu-btn').addEventListener('click', function() {
   header.classList.toggle('active');
});

