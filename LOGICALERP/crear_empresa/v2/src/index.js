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
    } catch (error) {
        modal.close();
        console.error('Error al obtener datos:', error);
    }
}
