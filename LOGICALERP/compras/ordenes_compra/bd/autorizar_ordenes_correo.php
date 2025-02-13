<?php
$idOrden = isset($_GET['idOrden']) ? $_GET['idOrden'] : null;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AUTORIZACION DE LA OC <?php echo $idOrden; ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 100%;
            max-width: 400px;
            margin: 50px auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        h1 {
            font-size: 24px;
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            display: block;
            font-weight: bold;
            color: #555;
        }

        input {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        button {
            width: 100%;
            padding: 10px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }

        button:hover {
            background-color: #45a049;
        }

        /* Estilos para la Modal */
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            overflow: auto;
        }

        .modal-content {
            background-color: #fff;
            margin: 15% auto;
            padding: 20px;
            border-radius: 8px;
            width: 50%;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }

    </style>
</head>
<body>
    <div class="container">
        <h1>AUTORIZACION DE LA OC <?php echo $idOrden; ?></h1>
        <form id="authForm">
            <div class="form-group">
                <label for="usuario">Usuario:</label>
                <input type="text" id="usuario" name="usuario" required>
            </div>
            <div class="form-group">
                <label for="contrasena">Contraseña:</label>
                <input type="password" id="contrasena" name="contrasena" required>
            </div>
            <button type="submit">Autorizar</button>
        </form>
    </div>

    <!-- Modal -->
    <div id="responseModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <p id="modalMessage"></p>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Obtener el formulario, la modal y el botón de cierre
            var authForm = document.getElementById('authForm');
            var modal = document.getElementById('responseModal');
            var modalMessage = document.getElementById('modalMessage');
            var closeModal = document.querySelector('.close');

            // Cuando el formulario se envíe
            authForm.addEventListener('submit', function(event) {
                event.preventDefault(); // Evita el envío del formulario

                // Obtener los datos del formulario
                var usuario = document.getElementById('usuario').value;
                var contrasena = document.getElementById('contrasena').value;
                var idOrden = <?php echo $oc_numero; ?>;
                // Crear una solicitud AJAX 
                var ajax = new XMLHttpRequest();
                ajax.open('POST', 'bd.php', true);
                ajax.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

                // Enviar los datos al servidor
                ajax.send('usuario=' + encodeURIComponent(usuario) + '&contrasena=' + encodeURIComponent(contrasena)+'&idOrden=' + idOrden);

                // Cuando se recibe la respuesta del servidor
                ajax.onload = function () {
                    if (ajax.status === 200) {
                        // Mostrar la respuesta en la modal
                        modalMessage.textContent = ajax.responseText;
                        modal.style.display = 'block';
                    } else {
                        // Si hubo un error, mostrar un mensaje de error
                        modalMessage.textContent = 'Hubo un error al procesar la solicitud.';
                        modal.style.display = 'block';
                    }
                };
            });

            // Cerrar la modal cuando se hace clic en la "x"
            closeModal.addEventListener('click', function () {
                modal.style.display = 'none';
            });

            // Cerrar la modal si se hace clic fuera de la modal
            window.addEventListener('click', function (event) {
                if (event.target === modal) {
                    modal.style.display = 'none';
                }
            });
        });
    </script>
</body>
</html>
