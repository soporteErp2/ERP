
async function verify_db() {
    let dialog = document.getElementById('dialog')
    try {
        dialog.show();
        const respuesta = await fetch('backend/index.php?method=verify_db');
        
        if (!respuesta.ok) {
            throw new Error(`Error HTTP: ${respuesta.status}`);
        }

        const datos = await respuesta.json();
        console.log('Datos recibidos:', datos);
        dialog.close();
    } catch (error) {
        console.error('Error al obtener datos:', error);
    }
}
