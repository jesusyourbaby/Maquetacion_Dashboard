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

