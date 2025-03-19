<?php
$datos = isset($_GET['data']) ? base64_decode($_GET['data']) : null;
if ($datos) {
    list($idOrden, $consecutivo, $sucursal, $nitEmpresa, $idEmpresa, $nameBd) = explode('|', $datos);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AUTORIZACION DE ORDEN DE COMPRA</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen p-4">
    <div class="w-full max-w-sm md:max-w-md lg:max-w-lg bg-white p-6 rounded-lg shadow-lg">
        <h1 class="text-2xl font-bold text-center text-gray-800 mb-4">
            AUTORIZACIÓN DE LA OC <?php echo $consecutivo; ?> DE <?php echo $sucursal; ?>
        </h1>
        <form id="authForm" class="space-y-4">
            <div>
                <label for="usuario" class="block font-semibold text-gray-700">Usuario:</label>
                <input type="text" id="usuario" name="usuario" required 
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label for="contrasena" class="block font-semibold text-gray-700">Contraseña:</label>
                <input type="password" id="contrasena" name="contrasena" required 
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <!-- Botones de acción -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-2">
                <button type="button" class="bg-green-500 text-white py-2 rounded-md hover:bg-green-600 transition"
                    onclick="enviarFormulario('Autorizada')">Autorizar</button>
                <button type="button" class="bg-yellow-500 text-white py-2 rounded-md hover:bg-yellow-600 transition"
                    onclick="enviarFormulario('Aplazada')">Aplazar</button>
                <button type="button" class="bg-red-500 text-white py-2 rounded-md hover:bg-red-600 transition"
                    onclick="enviarFormulario('Rechazada')">Rechazar</button>
            </div>
        </form>
    </div>

    <!-- Modal -->
    <div id="responseModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 hidden">
        <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-sm md:max-w-md lg:max-w-lg">
            <span id="closeModal" class="text-gray-600 text-xl font-bold cursor-pointer float-right">&times;</span>
            <p id="modalMessage" class="mt-4 text-gray-800"></p>
        </div>
    </div>
<!-- Contenedor de la pantalla de carga -->
<div id="loadingOverlay" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 hidden">
    <div class="flex flex-col items-center">
        <div class="w-12 h-12 border-t-4 border-blue-500 border-solid rounded-full animate-spin"></div>
        <p class="mt-3 text-white">Procesando...</p>
    </div>
</div>

<script>
async function enviarFormulario(tipoAutorizacion) {
    var usuario = document.getElementById("usuario").value.trim();
    var contrasena = document.getElementById("contrasena").value.trim();
    var idOrden = <?php echo json_encode($idOrden); ?>;
    var nitEmpresa = <?php echo json_encode($nitEmpresa); ?>;
    var idEmpresa = <?php echo json_encode($idEmpresa); ?>;
    var nameBd = <?php echo json_encode($nameBd); ?>;


    if (!usuario || !contrasena) {
        mostrarModal("Usuario y contraseña son obligatorios.");
        return;
    }

    let data = `${usuario}|${contrasena}|${nitEmpresa}|${idEmpresa}|${nameBd}`;
    let dataBase64 = btoa(data);

    const isLocal = window.location.origin.includes("localhost") || 
                    window.location.origin.includes("127.0.0.1");
    const resourceUrl = isLocal ? 
        "ERP/LOGICALERP/compras/ordenes_compra/bd/autorizacion_ordenes_correo/AutorizarOrdenesCompra.php" : 
        "LOGICALERP/compras/ordenes_compra/bd/autorizacion_ordenes_correo/AutorizarOrdenesCompra.php";

    let url = new URL(resourceUrl, window.location.origin);
    url.searchParams.append("data", dataBase64);
    url.searchParams.append("id_documento", idOrden);
    url.searchParams.append("tipo_autorizacion", tipoAutorizacion);

    // Mostrar pantalla de carga
    mostrarCargando(true);
    // Deshabilitar botones mientras se procesa la solicitud
    toggleBotones(false);

    try {
        mostrarCargando(true);
        toggleBotones(false);

        const response = await fetch(url, { method: "GET", cache: "no-cache" });
        const data = await response.json();

        if (data.success && data.responseEnvioCorreo.success) {
            mostrarModal(`✅ Éxito: ${data.responseOrden.message}`);
        } else if(data.success && !data.responseEnvioCorreo.success){
            mostrarModal(`⚠️ Alerta: ${data.responseEnvioCorreo.message}`);
        } 
        else {
            mostrarModal(`❌ Error: ${data.message}`);
        }
    } catch (error) {
        console.error("Error en la petición:", error);
        mostrarModal("❌ Error: Hubo un error al procesar la solicitud.");
    } finally {
        // Ocultar pantalla de carga y reactivar botones
        mostrarCargando(false);
        toggleBotones(true);
    }
}

/**
 * Muestra u oculta la pantalla de carga.
 * @param {boolean} mostrar - `true` para mostrar la pantalla de carga, `false` para ocultarla.
 */
function mostrarCargando(mostrar) {
    document.getElementById("loadingOverlay").classList.toggle("hidden", !mostrar);
}

/**
 * Muestra un modal con un mensaje personalizado.
 * @param {string} mensaje - Texto que se mostrará en el modal.
 */
function mostrarModal(mensaje) {
    var modal = document.getElementById("responseModal");
    var modalMessage = document.getElementById("modalMessage");
    modalMessage.textContent = mensaje;
    modal.classList.remove("hidden");
}

/**
 * Habilita o deshabilita los botones de acción.
 * @param {boolean} habilitar - `true` para habilitarlos, `false` para deshabilitarlos.
 */
function toggleBotones(habilitar) {
    document.querySelectorAll("button").forEach(btn => {
        btn.disabled = !habilitar;

        if (habilitar) {
            btn.classList.remove("opacity-50");
        } else {
            btn.classList.add("opacity-50");
        }
    });
}

document.addEventListener("DOMContentLoaded", function () {
    var modal = document.getElementById("responseModal");
    var closeModal = document.getElementById("closeModal");

    // Cerrar el modal al hacer clic en la 'X'
    closeModal.addEventListener("click", function () {
        modal.classList.add("hidden");
    });

    // Cerrar el modal al hacer clic fuera del contenido del modal
    modal.addEventListener("click", function (event) {
        if (event.target === modal) {
            modal.classList.add("hidden");
        }
    });
});

</script>
