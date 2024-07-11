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
    let modules = response.map(module=>{
        return `<li class="rounded-lg hover:border-l-4 hover:border-nav-dark hover:bg-nav-light hover:text-nav-dark hover:font-medium py-2 px-4 flex content-center gap-2 cursor-pointer  transition-all ease-in-out duration-100 ">
                    <svg class="  h-6 w-6" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path fill="currentColor" fill-rule="evenodd" clip-rule="evenodd" d="${module.icon}" />
                    </svg>             
                    <span>${module.name}</span>
                </li>`
    }).join('')
    document.getElementById("modules-content").innerHTML=modules;
}


//listeners
document.addEventListener("DOMContentLoaded", function() {
    // document.getElementById("app-loader").remove();

    document.getElementById('nav-btn').addEventListener('click', function() {
        toggle_nav();
    });
    document.getElementById('profile-btn').addEventListener('click', function() {
        toggle_perfil();
    });

    load_modules();
});

