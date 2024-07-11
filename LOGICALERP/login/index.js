
//funciones 
function show_hide(){
    document.getElementById("modal-content").classList.toggle("z-50");
}

async function get_companies(){
    let request = await fetch('LOGICALERP/login/Controller.php?method=get_companies');
    let companies = await request.json();
    let companies_ul = document.getElementById("companies")
    let companies_li = companies.map(element=>{
        return `<li class="group" onclick="set_company(${element.nit})">
                    <label class="inline-flex items-center justify-between w-full p-5 text-gray-900 bg-white border border-gray-200 rounded-lg cursor-pointer dark:hover:text-gray-300 dark:border-gray-500 dark:peer-checked:text-blue-500 peer-checked:border-blue-600 peer-checked:text-blue-600 hover:text-gray-900 hover:bg-gray-100 dark:text-white dark:bg-gray-600 dark:hover:bg-gray-500">                           
                        <div class="block">
                            <div class="w-full text-base font-bold">${element.nombre} </div>
                            <div class="w-full text-sm text-gray-500 dark:text-gray-400">(${element.bd}) vence ${element.fecha_vencimiento_plan}</div>
                        </div>
                        <svg class="group-hover:animate-pulse w-4 h-4 ms-3 rtl:rotate-180 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 10"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 5h12m0 0L9 1m4 4L9 9"/></svg>
                    </label>
                </li>`;
    })
    companies_ul.innerHTML = companies_li.join("")
}

function set_company(n_documento){
    document.getElementById("n_documento").value = n_documento;
    show_hide();
    document.getElementById("n_documento").focus();
}


async function load_company(){
    

    document.getElementById("sucursal").classList.toggle("hidden");
    document.getElementById("branch-skeleton").classList.toggle("hidden");
    let n_documento = document.getElementById("n_documento").value
    let request = await fetch(`LOGICALERP/login/Controller.php?method=load_company&n_documento=${ n_documento }`);
    let response = await request.json();
    let select = document.getElementById('sucursal');
    let options = response.map(option => `<option value="${option.id}">${option.nombre}</option>`).join('');
    select.innerHTML = `<option>Sucursal...</option> ${options}`
    document.getElementById("sucursal").classList.toggle("hidden");
    document.getElementById("branch-skeleton").classList.toggle("hidden");
}

async function login(){
    let btn_submit = document.getElementById("btn_submit")

    

    let n_documento = document.getElementById("n_documento").value
      , sucursal    = document.getElementById("sucursal").value
      , usuario     = document.getElementById("usuario").value
      , password    = document.getElementById("password").value

    if(n_documento=='' ||
        sucursal=='' ||
        usuario=='' ||
        password==''){
            alert("todos los campos son obligatorios")
            return;
    }
    btn_submit.disabled = true;
    btn_submit.innerHTML = `<div class="animate-spin border-4 border-orange-light border-r-orange-dark rounded-full w-6 h-6"></div>Procesando...`

    let request = await fetch(`LOGICALERP/login/Controller.php?method=login&n_documento=${ n_documento }&sucursal=${sucursal}&usuario=${usuario}&password=${password}`);
    let response = await request.json();

    if (response.error) {
        alert(response.error);
        btn_submit.disabled = false;
        btn_submit.innerHTML = `Continue 
                                <svg width="1em" height="1em" viewBox="0 0 40 40" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M36.3428 20C36.3428 20.3637 36.1983 20.7126 35.9411 20.9698L24.9697 31.9412C24.4341 32.4768 23.5657 32.4768 23.0302 31.9412C22.4946 31.4056 22.4946 30.5373 23.0302 30.0017L31.6604 21.3715L5.7142 21.3715C4.95678 21.3715 4.34277 20.7574 4.34277 20C4.34277 19.2426 4.95678 18.6286 5.7142 18.6286L31.6604 18.6286L23.0302 9.99834C22.4946 9.46276 22.4946 8.59442 23.0302 8.05885C23.5657 7.52327 24.4341 7.52327 24.9697 8.05885L35.9411 19.0303C36.1983 19.2875 36.3428 19.6363 36.3428 20Z" fill="currentColor"></path>
                                </svg>`
    
        return;        
    }

    if (response.success) {
        document.getElementById("login-loader").classList.toggle("hidden")
        window.location = "app.php";    
    }
}

//listeners
document.addEventListener("DOMContentLoaded", function() {
    document.getElementById("n_documento").focus();

    document.addEventListener('keydown', function(event) {
        if (event.ctrlKey && event.key === 'F2') {
            show_hide();
            get_companies();
        }
    });

    document.getElementById('n_documento').addEventListener('keydown', function(event) {
        if (event.key === 'Enter') {
            load_company();
        }
    });

    document.getElementById('n_documento').addEventListener('blur', function(event) {
        load_company();
    });

    document.getElementById('close_modal').addEventListener('click', function() {
        show_hide();
    });


    document.getElementById('btn_submit').addEventListener('click', function() {
        login();
    });

    
    

    
});
