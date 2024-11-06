const enlacesMenu = document.querySelectorAll(".enlace");

enlacesMenu.forEach(enlace => {
    enlace.addEventListener("click", () => {
        document.querySelector(".menu-dashboard").classList.add("open");
    });
});

function showDashboard(){
    document.getElementById("tablero").style.display="";
    document.getElementById("login").style.display="none";
}

function showLogin(){
    document.getElementById("tablero").style.display="none";
    document.getElementById("login").style.display="";
}

function toggleDropdown() {
    var dropdownMenu = document.querySelector('.user-menu .dropdown-menu');

    // Cambiar el estilo de display entre block y none para mostrar/ocultar el menú
    if (dropdownMenu.style.display === "block") {
        dropdownMenu.style.display = "none";
    } else {
        dropdownMenu.style.display = "block";
    }
}


