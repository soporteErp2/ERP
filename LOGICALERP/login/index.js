
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
    let n_documento = document.getElementById("n_documento").value
    let request = await fetch(`LOGICALERP/login/Controller.php?method=load_company&n_documento=${ n_documento }`);
    let response = await request.json();
    let select = document.getElementById('sucursal');
    let options = response.map(option => `<option value="${option.id}">${option.nombre}</option>`).join('');
    select.innerHTML = `<option>Sucursal...</option> ${options}`
}

async function login(){
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

    let request = await fetch(`LOGICALERP/login/Controller.php?method=login&n_documento=${ n_documento }&sucursal=${sucursal}&usuario=${usuario}&password=${password}`);
    let response = await request.json();

    if (response.error) {
        alert(response.error);
        return;        
    }

    if (response.success) {
        window.location = "app.php";    
    }
    console.log(response)
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
