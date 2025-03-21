
document.addEventListener("DOMContentLoaded", () => {
    const sections = document.querySelectorAll("div[id^='card-']");
    let currentIndex = 0;

    function scrollToNext() {
        if (currentIndex < sections.length - 1) {
            currentIndex++;
            sections[currentIndex].scrollIntoView({ behavior: "smooth" });
        }
    }

    function scrollToPrevious() {
        if (currentIndex > 0) {
            currentIndex--;
            sections[currentIndex].scrollIntoView({ behavior: "smooth" });
        }
    }

    // Agregar evento para teclas de flecha
    document.addEventListener("keydown", (event) => {
        if (event.key === "ArrowDown") {
            scrollToNext();
        } else if (event.key === "ArrowUp") {
            scrollToPrevious();
        }
    });

    // Exponer la función globalmente para que los botones la usen
    window.scrollToNext = scrollToNext;
});

// modal functions
var modal = document.getElementById("modal");
Object.assign(modal, {
    show() {
        this.classList.toggle("hidden");
    },
    close() {
        this.classList.toggle("hidden");
    },
    toggle() {
        this.style.display = this.style.display === "none" ? "block" : "none";
    }
});

document.addEventListener("DOMContentLoaded", function () {
    modal.close();
});


async function verify_db() {
    let licence = document.getElementById('support-licence')
    let company = document.getElementById('company-doc')

    if (licence.value=="" || company.value=="") {
        alert("los campos licencia y nit son obligatorios");
        licence.focus();
        return;
    }

    try {
        modal.show();
        const respuesta = await fetch('backend/index.php?method=verify_db',{
            method: 'POST', 
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                "licence" : licence.value,
                "company" : company.value
            })
        });
        
        if (!respuesta.ok) {
            throw new Error(`Error HTTP: ${respuesta.status}`);
        }

        const datos = await respuesta.json();
        console.log( datos);
        modal.close();
        scrollToNext();
    } catch (error) {
        modal.close();
        console.error('Error al obtener datos:', error);
    }
}

async function create_company()
{
    let licence = document.getElementById('support-licence')
    let company = document.getElementById('company-doc')
    let company_dv = document.getElementById('company-doc-dv')
    let company_name = document.getElementById('company-name')
    let company_rs = document.getElementById('company-social-name')

    if (licence.value=="" || company.value=="" || company_dv.value=='') {
        alert("los campos licencia y nit son obligatorios");
        // licence.focus();
        return;
    }

    try {
        modal.show();
        const respuesta = await fetch('backend/index.php?method=create_company',{
            method: 'POST', 
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                "licence" : licence.value,
                "company" : company.value,
                "company_dv" : company_dv.value,
                "company_name" : company_name.value,
                "company_rs" : company_rs.value,
            })
        });
        
        if (!respuesta.ok) {
            throw new Error(`Error HTTP: ${respuesta.status}`);
        }

        const datos = await respuesta.json();
        console.log( datos);
        modal.close();
        scrollToNext();
    } catch (error) {
        modal.close();
        console.error('Error al obtener datos:', error);
    }
}

async function create_user()
{
    let licence = document.getElementById('support-licence')
    let user_doc = document.getElementById('user-doc').value
    let user_firstname = document.getElementById('user-firstname').value
    let user_secondname = document.getElementById('user-secondname').value
    let user_firstlastname = document.getElementById('user-firstlastname').value
    let usersecondlastename = document.getElementById('user-econdlastename').value

    if (licence.value=="") {
        alert("los campos licencia");
        // licence.focus();
        return;
    }

    try {
        modal.show();
        const respuesta = await fetch('backend/index.php?method=create_user',{
            method: 'POST', 
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                "licence" : licence.value,
                user_doc,
                user_firstname,
                user_secondname,
                user_firstlastname,
                usersecondlastename
            })
        });
        
        if (!respuesta.ok) {
            throw new Error(`Error HTTP: ${respuesta.status}`);
        }

        const datos = await respuesta.json();
        console.log( datos);
        modal.close();
        scrollToNext();
    } catch (error) {
        modal.close();
        console.error('Error al obtener datos:', error);
    }
}
