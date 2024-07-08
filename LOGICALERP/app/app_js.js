// functions
function toggle_nav(){
    let nav = document.getElementById("nav")
    nav.classList.toggle("w-0")
}

function toggle_perfil(){
    let profile = document.getElementById("profile-content")
    profile.classList.toggle("opacity-0")
    profile.classList.toggle("z-50")
}

async function load_modules(){
    let request = await fetch(`LOGICALERP/app/Controller.php?method=load_modules`);
    let response = await request.json();
}


//listeners
document.addEventListener("DOMContentLoaded", function() {
    document.getElementById('nav-btn').addEventListener('click', function() {
        toggle_nav();
    });
    document.getElementById('profile-btn').addEventListener('click', function() {
        toggle_perfil();
    });
});

