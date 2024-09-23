// functions
function toggle_nav(){
    let nav = document.getElementById("nav")
    nav.classList.toggle("w-64")
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
    response = typeof response == 'object' ? Object.values(response) : response ;
    let modules = response.map(module=>{
        return `<li data-src="${module.src}" data-id="${module.id}" class="module-nav rounded-lg hover:border-l-4 hover:border-nav-dark hover:bg-nav-light hover:text-nav-dark hover:font-medium py-2 px-4 flex content-center gap-2 cursor-pointer  transition-all ease-in-out duration-100 ">
                    <svg class="  h-6 w-6" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path fill="currentColor" fill-rule="evenodd" clip-rule="evenodd" d="${module.icon}" />
                    </svg>             
                    <span>${module.name}</span>
                </li>`
    }).join('')
    document.getElementById("modules-nav").innerHTML=modules;
    render_modules();
}

function render_modules() {
    let module_items = document.querySelectorAll('.module-nav');
    let modules_content = document.getElementById('modules-content');
    let loader = document.getElementById('iframe-loading');

    module_items.forEach(item => {
        item.addEventListener('click', () => {
            const src = item.getAttribute('data-src')+"?autorizado=true";
            const id = item.getAttribute('data-id');
            let iframe = document.getElementById(`iframe-${id}`);

            if (!iframe) {
                // Mostrar el loader al hacer click
                loader.classList.remove('hidden');
                iframe = document.createElement('iframe');
                iframe.id = `iframe-${id}`;
                iframe.src = src;
                iframe.style.display = 'none';
                iframe.classList.add("w-full");
                iframe.classList.add("h-full");
                iframe.addEventListener('load', function() {
                    loader.classList.add('hidden'); // Ocultar el loader cuando el iframe haya cargado
                    iframe.style.display = 'block';
                });
                modules_content.appendChild(iframe);
            }
            // Si el iframe ya existe y está cargado, no recargar
            // if (iframe.src !== src) {
            //     iframe.src = src;
            // } else {
            //     loader.classList.add('hidden'); // Ocultar el loader si el iframe ya está cargado
            //     iframe.style.display = 'block';
            // }

            const iframes = modules_content.querySelectorAll('iframe');
            iframes.forEach(iframe => {
                iframe.style.display = 'none';
            });

            const list = document.querySelectorAll('li');
            list.forEach(item => {                
                item.classList.remove("border-l-4");
                item.classList.remove("border-nav-dark");
                item.classList.remove("bg-nav-light");
                item.classList.remove("text-nav-dark");
                item.classList.remove("font-medium");
            });

            iframe.style.display = 'block';
            item.classList.add("border-l-4");
            item.classList.add("border-nav-dark");
            item.classList.add("bg-nav-light");
            item.classList.add("text-nav-dark");
            item.classList.add("font-medium");



        });
    });
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

