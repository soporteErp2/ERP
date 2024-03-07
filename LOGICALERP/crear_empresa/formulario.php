<?php
    include("../../configuracion/conectar.php");
    header('Content-Type: text/html; charset=UTF-8');
    $optionDepartamento = '';

    $sql   = "SELECT id,departamento FROM ubicacion_departamento WHERE pais='Colombia'";
    $query = mysql_query($sql,$link);
    while ($row=mysql_fetch_array($query)){ $optionDepartamento .= '<option value="'.$row['id'].'">'.$row['departamento'].'</option>'; }
    echo '<div style="display:none">'.$_SESSION['ID_HOST'].'</div>';

    $optionPais = '';
    $sql_pais   = "SELECT id,pais FROM ubicacion_pais ";
    $query_pais = mysql_query($sql_pais,$link);
    while ($row1=mysql_fetch_array($query_pais)){ $optionPais .= '<option value="'.$row1['id'].'">'.$row1['pais'].'</option>'; }
    // echo $optionPais;
?>

<!DOCTYPE html>
<!--[if lt IE 7]> <html class="lt-ie9 lt-ie8 lt-ie7" lang="en"> <![endif]-->
<!--[if IE 7]> <html class="lt-ie9 lt-ie8" lang="en"> <![endif]-->
<!--[if IE 8]> <html class="lt-ie9" lang="en"> <![endif]-->
<!--[if gt IE 8]><!--> <html lang="es"> <!--<![endif]-->
<head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="themes/alertify.core.css" />
    <link rel="stylesheet" href="themes/alertify.default.css" id="toggleCSS" />
    <!-- <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" /> -->
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title>Registre Su empresa</title>

    <style type="text/css">
        #div_upload_file > div { margin: 0px !important; }
        #div_upload_file:before {
            content    : 'Arrastre el archivo excel o csv.';
            width      : 100%;
            float      : left;
            font-size  : 23px;
            margin-top : 130px;
            text-align : center;
            color      : #bcbcbc;
        }
        .qq-uploader { position:relative; width: 100%; height:100%; }
        .qq-upload-button { display:block; position:fixed !important; width:100px; height:34px; margin:5px; background-image:url(img/uploading.png); background-size: 100px; }
        .qq-upload-button-hover { background-image:url(img/uploading_blue.png) }
        .qq-upload-button-focus { }
        .qq-upload-drop-area { position:absolute; top:40; left:0; width:100%; height:100%; min-height: 70px; background:none; text-align:center; display:none;  }
        .qq-upload-drop-area span {  display:block; position:absolute; top: 50%; width:100%; margin-top:-8px; font-size:16px; }
        .qq-upload-drop-area-active { background:#FF0000; opacity: 0.3; filter:alpha(opacity=30); -moz-opacity:0.3; -khtml-opacity: 0.3; }
        .qq-upload-list { height:100%; list-style:none; color:#333; text-align:center; }
        .qq-upload-list li {  margin:0; padding:0; line-height:15px; font-size:12px; }
        .qq-upload-file, .qq-upload-spinner, .qq-upload-size, .qq-upload-cancel, .qq-upload-failed-text {  margin-right: 7px; }
        .qq-upload-file {  }
        .qq-upload-spinner {
            display             : inline-block;
            background-image    : url("img/loading.gif");
            width               : 400px;
            height              : 400px;
            vertical-align      : text-bottom;
            position            : absolute;
            top                 : 0;
            left                : 0;
            background-repeat   : no-repeat;
            background-position : 100px 50px;
            background-color    : #fff;
        }
        .qq-upload-size,.qq-upload-cancel { font-size:11px; }
        .qq-upload-failed-text { display:none; }
        .qq-upload-fail .qq-upload-failed-text { display:inline; }
    </style>

    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" type="text/css" href="../../misc/extjs3/resources/css/ext-all.php"/>
    <link rel="stylesheet" type="text/css" href="../../misc/MyGrilla/MyGrilla.css"/>
    <link rel="stylesheet" type="text/css" href="../../temas/clasico/estilo.php"/>
    <script type="text/javascript" src="../../misc/extjs3/ext-base.js?v4.0.0.12-05-2013"></script>
    <script type="text/javascript" src="../../misc/extjs3/ext-all.js?v4.0.0.12-05-2013"></script>
    <script type="text/javascript" src="../../misc/lib.js?v4.0.0.12-05-2013"></script>
    <script type="text/javascript" src="../../misc/ckeditor/ckeditor.js?v4.0.0.12-05-2013"></script>
    <script type="text/javascript" src="../../misc/MyFunctions.js?v4.0.0.12-05-2013"></script>
    <script type="text/javascript" src="../../misc/dragresize/dragresize.js"></script>

    <!--##########################  File Upload Ajax ###############################-->
    <script src="../../misc/upload2/fileuploader.js" type="text/javascript"></script>
    <!--############################################################################-->
</head>
<body>
    <section class="container">
        <div style="text-align:center;width:100%;"><img src="img/logo.png"></div>
        <div class="login">
            <h1>Registre su empresa</h1>
            <form method="post" action="bd.php" id="frm1" style="overflow:hidden;">
                <input type="text" id="nombre" name="nombre" required placeholder="Nombre de la Empresa">

                <select name="tipo_documento" id="tipo_documento" style="width:300px;padding-right: 0 !important;" onchange="verifica_documento(this.value)">
                   <option value="C.C">C.C</option>
                   <option value="T.I">T.I</option>
                   <option value="PASAPORTE">PASAPORTE</option>
                   <option value="C.E">C.E</option>
                   <option value="NIT" selected >NIT</option>
                   <option value="RUC">RUC</option>
                   <option value="C.I">C.I</option>
                </select>

                <input type="text" id="numero_documento" name="numero_documento"  value="" placeholder="Numero documento Empresa" required style="float: left; width:224px;">
                <input type="text" id="digito_verificacion" name="digito_verificacion" value="" placeholder="Digito Verificacion" style="float: left;width: 66px !important;" title="Digito de Verificacion">

                <input type="text" id="razon_social" name="razon_social"  value="" placeholder="Razon Social" required>

                <select id="tipo_regimen" style="width:300px;padding-right: 0 !important;">
                    <option value="0">Seleccione...</option>
                    <option value="REGIMEN SIMPLIFICADO">REGIMEN SIMPLIFICADO</option>
                    <option value="REGIMEN COMUN">REGIMEN COMUN</option>
                </select>

                <select id="origen_empresa" onchange="mostrar_pais()" style="width:300px;padding-right: 0 !important;">
                    <option value="0">Seleccione...</option>
                    <option value="empresa_nacional">EMPRESA NACIONAL</option>
                    <option value="empresa_exterior">EMPRESA DEL EXTERIOR</option>
                </select>

                <input type="text" id="actividad_economica" name="actividad_economica" placeholder="Actividad Economica"  >

                <input type="text" id="direccion" name="direccion" placeholder="Direccion" >
                <input type="text" id="telefono" name="telefono" placeholder="Telefono Fijo" >
                <input type="text" id="celular" name="celular" placeholder="Telefono Celular" >

                <div id="content" style="display:none">
                    <select name="pais" id="pais" style="width:300px;padding-right: 0 !important;" onchange="buscaDep(this.value)" >
                      <option value="0">Seleccione Pais...</option>
                      <?php echo $optionPais ?>
                    </select>
                </div>

                <div id="divDep">
                    <select name="departamento" id="departamento" style="width:300px;padding-right: 0 !important;" onchange="buscaCiudad(this.value)">
                        <option value="0" selected>Seleccione el Departamento...</option>
                        <!--  <?php //echo $optionDepartamento ?> -->
                    </select>
                </div>

                <div id="divCiudad">
                    <select name="ciudad" id="ciudad" style="width:300px;padding-right: 0 !important;">
                        <option value="0" selected>Seleccione la Ciudad...</option>
                    </select>
                </div>

                <input type="text" id="sucursal" name="sucursal" value="Sucursal Principal" placeholder="Nombre sucursal" required>
                <input type="text" id="bodega" name="bodega" value="Bodega Principal" placeholder="Nombre bodega" required>

                <h1 style="margin-top:15px;">Crear usuario</h1>
                <p>Tendra todos los privilegios sobre el sistema</p>

                <input type="text" id="numero_identificacion" name="numero_identificacion"  placeholder="Numero de identificacion" required>
                <input type="text" id="nombre1" name="nombre1" placeholder="Primer nombre" onKeypress="return event.keyCode!=32" required>
                <input type="text" id="nombre2" name="nombre2" placeholder="Segundo Nombre" onKeypress="return event.keyCode!=32">
                <input type="text" id="apellido1" name="apellido1" placeholder="Primer Apellido" onKeypress="return event.keyCode!=32" required>
                <input type="text" id="apellido2" name="apellido2" placeholder="Segundo Apellido" onKeypress="return event.keyCode!=32">

                <h1 style="margin-top:15px;">Grupo Empresarial</h1>
                <input type="text" id="grupo_empresarial" name="grupo_empresarial" placeholder="Nombre"/>

                <h1 style="margin-top:15px;">Upload plan de cuentas</h1>
                <input type="text" id="nombre_excel" name="nombre_excel" style="float:left;"  placeholder="Upload documento" onclick="windows_upload_excel();" readonly/>
                <div id="btn_cancel_doc_upload" onclick="cancelUploadFile()" title="Eliminar Archivo">X</div>

            </div>
            <p class="submit" style="width:100%;text-align:center;">
                <input type="button" name="commit" onclick="ajaxCreaEmpresa()" value="Crear Empresa">
            </p>
        </form>
    </section>

    <section class="about">
        <p class="about-author">
            &copy; <!-- 2013&ndash; -->2014 LogicalSoft ERP</br>
            <a href="http://logicalsoft.co/" target="_blank">LogicalSoft S.A.S</a><br>
            Todos los derechos reservados
        </p>
    </section>
    <div id="divPadreModalUploadFile" class="fondo_modal_upload_file">
        <div>
            <div>
                <div>
                    <div id="div_upload_file">
                        <div>Arrastre el archivo excel o csv.</div>
                    </div>
                    <!-- <div class="btn_div_upload_file2" onclick="close_ventana_upload_file()">X</div> -->
                    <div class="btn_div_upload_file2" style="margin-left:350px;" onclick="imagenAyudaModal()" title="Como debe ser el archivo" >?</div>
                    <div class="btn_div_upload_file2" onclick="close_ventana_upload_file()">X</div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-1.9.1.js"></script>
    <script src="lib/alertify.min.js"></script>
    <script>

        function verifica_documento() {
            opc = document.getElementById('tipo_documento').value;

            var numero_documento = document.getElementById('numero_documento');
            var digito_verificacion = document.getElementById('digito_verificacion');

            if (opc!='NIT') {
                digito_verificacion.style.display='none';
                numero_documento.style.width='278px ';
            }
            else{
                digito_verificacion.style.display='block';
                numero_documento.style.width='180px ';
            }

        }

        var globalNameFileUpload = '';

        //LISTENER
        document.getElementById('nombre').onkeyup    = function(){ this.value = (this.value).toUpperCase(); }
        document.getElementById('nombre1').onkeyup   = function(){ this.value = (this.value).toUpperCase(); }
        document.getElementById('nombre2').onkeyup   = function(){ this.value = (this.value).toUpperCase(); }
        document.getElementById('apellido1').onkeyup = function(){ this.value = (this.value).toUpperCase(); }
        document.getElementById('apellido2').onkeyup = function(){ this.value = (this.value).toUpperCase(); }

        document.getElementById('razon_social').onkeyup      = function(){ this.value = (this.value).toUpperCase(); }
        document.getElementById('grupo_empresarial').onkeyup = function(){ this.value = (this.value).toUpperCase(); }

        function cancelUploadFile(){
            var xhr     = new XMLHttpRequest()
            ,   bodyXhr = 'bd.php?nameFileUpload='+globalNameFileUpload+'&opc=cancelUploadFile';

            xhr.open('POST',bodyXhr, true);
            xhr.onreadystatechange=function(){
                if(xhr.readyState==4){
                    var responseError = xhr.responseText;
                    if (responseError=='true') {
                        globalNameFileUpload = '';
                        document.getElementById('nombre_excel').value = '';
                        document.getElementById('btn_cancel_doc_upload').style.display = 'none';
                        return;
                    }
                    alertify.error(responseError);
                }
                else return;
            }
            xhr.send(null);
        }


        function windows_upload_excel(){
            if(globalNameFileUpload != ''){ alert('Elimine el archivo anterior antes de subir uno nuevo!'); return; }
            document.getElementById('divPadreModalUploadFile').setAttribute('style','display:block;');
        }

        function close_ventana_upload_file(){ document.getElementById('divPadreModalUploadFile').setAttribute('style',''); }

        function buscaDep(id){
            document.getElementById("divDep").innerHTML='<img src="img/cargando20.gif"> Cargando...';

            $.ajax({
                type : 'POST',
                url  : 'bd.php',
                data : { opc: 'consultarPais', id_pais: id }
            }).done(function( msg ) {
                document.getElementById("divDep").innerHTML='<select name="departamento" id="departamento" onchange="buscaCiudad(this.value)" style="width:300px;padding-right: 0 !important;"><option value="0" selected>Seleccione la Departamento...</option>'+msg+'</select>';
            }).fail(function (jqXHR, textStatus, errorThrown){
                document.getElementById("divDep").innerHTML='<select name="departamento" id="departamento" onchange="buscaCiudad(this.value)" style="width:300px;padding-right: 0 !important;"><option value="0" selected>Seleccione la Departamento...</option></select>';
            });
        }

        function buscaCiudad(id){

            document.getElementById("divCiudad").innerHTML='<img src="img/cargando20.gif"> Cargando...';

            $.ajax({
                type : 'POST',
                url  : 'bd.php',
                data : { opc: 'consultarCiudad', id_departamento: id }
            }).done(function( msg ) {
                document.getElementById("divCiudad").innerHTML='<select name="ciudad" id="ciudad" style="width:300px;padding-right: 0 !important;"><option value="0" selected>Seleccione la Ciudad...</option>'+msg+'</select>';
            }).fail(function (jqXHR, textStatus, errorThrown){
                document.getElementById("divCiudad").innerHTML='<select name="ciudad" id="ciudad" style="width:300px;padding-right: 0 !important;"><option value="0" selected>Seleccione la Ciudad...</option></select>';
            });
        }

        function validaFormulario(){

            //CAPTURAR LOS DATOS DEL FORMULARIO
            nombre              = document.getElementById('nombre');
            nit                 = document.getElementById('numero_documento');
            digito_verificacion = document.getElementById('digito_verificacion');
            tipo_documento      = document.getElementById('tipo_documento');
            razon_social        = document.getElementById('razon_social');
            tipo_regimen        = document.getElementById('tipo_regimen');
            origen_empresa      = document.getElementById('origen_empresa');
            act_economica       = document.getElementById('actividad_economica');
            direccion           = document.getElementById('direccion');
            telefono            = document.getElementById('telefono');
            celular             = document.getElementById('celular');
            pais                = document.getElementById('pais');
            departamento        = document.getElementById('departamento');
            ciudad              = document.getElementById('ciudad');
            sucursal            = document.getElementById('sucursal');
            bodega              = document.getElementById('bodega');
            cedula              = document.getElementById('numero_identificacion');
            nombre1             = document.getElementById('nombre1');
            nombre2             = document.getElementById('nombre2');
            apellido1           = document.getElementById('apellido1');
            apellido2           = document.getElementById('apellido2');

            //VALIDAR QUE LOS CAMPOS OBLIGATORIOS TENGAN INFORMACION
            if (nombre.value =='') {alertify.error("DIGITE EL NOMBRE DE LA EMPRESA");                  nombre.focus();          return false; }
            if (nit.value =='') {alertify.error("DIGITE EL DOCUMENTO DE LA EMPRESA");               nit.focus();             return false; }

            //VALIDAR SI EL DOCUMENTO ES NIT, ENTONCES QUE HAYA INGRESADO EL DIGITO DE VERIFICACION
            if (tipo_documento.value=='NIT') {
                if (digito_verificacion.value == '') {alertify.error("INGRESE EL DIGITO DE VERIFICACION"); digito_verificacion.focus(); return false; }
            }

            if (razon_social.value   =='') {alertify.error("DIGITE LA RAZON SOCIAL DE LA EMPRESA");           razon_social.focus();  return false; }
            if (tipo_regimen.value   =='0') {alertify.error("SELECCIONE EL TIPO DE REGIMEN");                 tipo_regimen.focus();  return false; }
            if (origen_empresa.value =='0') {alertify.error("SELECCIONE EL TIPO DE REGIMEN");                 origen_empresa.focus();return false; }
            if (act_economica.value  =='') {alertify.error("DIGITE LA ACTIVIDAD ECONOMICA DE LA EMPRESA");    act_economica.focus(); return false; }
            if (direccion.value      =='') {alertify.error("DIGITE LA DIRECCION DE LA EMPRESA");              direccion.focus();     return false; }
            if (pais.value           =='0') {alertify.error("SELECCIONE EL PAIS");                            pais.focus();          return false; }
            if (departamento.value   =='0') {alertify.error("SELECCIONE EL DEPARTAMENTO");                    departamento.focus();  return false; }
            if (ciudad.value         =='0') {alertify.error("SELECCIONE LA CIUDAD");                          ciudad.focus();        return false; }
            if (sucursal.value       =='') {alertify.error("DIGITE LA SUCURSAL DE LA EMPRESA");               sucursal.focus();      return false; }
            if (bodega.value         =='') {alertify.error("DIGITE LA BODEGA PARA LA SUCURSAL");              bodega.focus();        return false; }
            if (cedula.value         =='') {alertify.error("DIGITE EL NUMERO DE IDENTIFICACION DEL USUARIO"); cedula.focus();        return false; }
            if (nombre1.value        =='') {alertify.error("DIGITE EL NOMBRE DEL USUARIO");                   nombre1.focus();       return false; }
            if (apellido1.value      =='') {alertify.error("DIGITE EL APELLIDO DEL USUARIO");                 apellido1.focus();     return false; }

        }

        function ajaxCreaEmpresa() {
            // if(validaFormulario()==false){ return; }

            parentModal = document.createElement("div");
            parentModal.innerHTML = bodyLoadingCrearEmpresa();
            parentModal.setAttribute("id", "divPadreModal");
            document.body.appendChild(parentModal);
            document.getElementById("divPadreModal").className = "fondo_modal";

            var xhr=new XMLHttpRequest();

            //CAPTURAR LOS DATOS DEL FORMULARIO
            nombre              = document.getElementById('nombre').value;
            nit                 = document.getElementById('numero_documento').value;
            digito_verificacion = document.getElementById('digito_verificacion').value;
            tipo_documento      = document.getElementById('tipo_documento').value;
            razon_social        = document.getElementById('razon_social').value;
            tipo_regimen        = document.getElementById('tipo_regimen').value;
            origen_empresa      = document.getElementById('origen_empresa').value;
            act_economica       = document.getElementById('actividad_economica').value;
            direccion           = document.getElementById('direccion').value;
            telefono            = document.getElementById('telefono').value;
            celular             = document.getElementById('celular').value;
            pais                = document.getElementById('pais').value;
            departamento        = document.getElementById('departamento').value;
            ciudad              = document.getElementById('ciudad').value;
            sucursal            = document.getElementById('sucursal').value;
            bodega              = document.getElementById('bodega').value;
            cedula              = document.getElementById('numero_identificacion').value;
            nombre1             = document.getElementById('nombre1').value;
            nombre2             = document.getElementById('nombre2').value;
            apellido1           = document.getElementById('apellido1').value;
            apellido2           = document.getElementById('apellido2').value;
            grupo_empresarial   = document.getElementById('grupo_empresarial').value;

            direccion = direccion.replace(/[\#"]/g, 'No.');

            nombre       = nombre.toUpperCase();
            razon_social = razon_social.toUpperCase();
            nombre1      = nombre1.toUpperCase();
            nombre1      = nombre1.toUpperCase();
            nombre2      = nombre2.toUpperCase();
            apellido1    = apellido1.toUpperCase();
            apellido2    = apellido2.toUpperCase();

            var bodyXhr =  'bd.php?opc=crearEmpresa'
                            +'&nombre='+nombre
                            +'&numero_documento='+nit
                            +'&digito_verificacion='+digito_verificacion
                            +'&tipo_documento='+tipo_documento
                            +'&razon_social='+razon_social
                            +'&tipo_regimen='+tipo_regimen
                            +'&origen_empresa='+origen_empresa
                            +'&actividad_economica='+act_economica
                            +'&direccion='+direccion
                            +'&telefono='+telefono
                            +'&celular='+celular
                            +'&pais='+pais
                            +'&departamento='+departamento
                            +'&ciudad='+ciudad
                            +'&sucursal='+sucursal
                            +'&bodega='+bodega
                            +'&numero_identificacion='+cedula
                            +'&nombre1='+nombre1
                            +'&nombre2='+nombre2
                            +'&apellido1='+apellido1
                            +'&apellido2='+apellido2
                            +'&nameFileUpload='+globalNameFileUpload
                            +'&grupoEmpresarial='+grupo_empresarial;

            xhr.open('POST',bodyXhr, true);
            xhr.onreadystatechange=function(){
                if(xhr.readyState==4){
                    // alertify.success("You've clicked OK");
                    // alertify.error("You've clicked Cancel");

                    // Parsear Response en Json --->
                    // var responseError = JSON.parse(xhr.responseText);
                    var responseError = xhr.responseText;
                    if (responseError=='true') {
                        globalNameFileUpload = '';
                        document.getElementById('nombre_excel').value = '';
                        document.getElementById('btn_cancel_doc_upload').style.display = 'none';
                        parentModal.parentNode.removeChild(parentModal); alertify.success("SE CREO LA EMPRESA "+nombre); document.getElementById("frm1").reset(); verifica_documento(); return;
                    }

                    parentModal.parentNode.removeChild(parentModal);
                    alertify.error(responseError);
                }
                else return;
            }
            xhr.send(null);
        }

        function bodyLoadingCrearEmpresa(){ return body  = '<div id="modal"><div id="contenedor_calendario"><div class="image_loading"><img src="img/cargando.gif"></div></div></div>'; }

        function createUploader(){
            var uploader = new qq.FileUploader({
                element : document.getElementById('div_upload_file'),
                action  : 'configuraciones/upload_file.php',
                debug   : false,
                params  : { },
                button            : null,
                multiple          : false,
                maxConnections    : 3,
                allowedExtensions : ['xls', 'csv'],
                sizeLimit         : 10*1024*1024,
                minSizeLimit      : 0,
                onSubmit          : function(id, fileName){},
                onProgress        : function(id, fileName, loaded, total){},
                onComplete        : function(id, fileName, responseJSON){
                                        document.getElementById('div_upload_file').querySelector('.qq-upload-list').innerHTML='';
                                        globalNameFileUpload = responseJSON.nameFileUpload;
                                        document.getElementById('nombre_excel').value= fileName;
                                        document.getElementById('btn_cancel_doc_upload').style.display = 'block';
                                        document.getElementById('divPadreModalUploadFile').setAttribute('style','');
                                    },
                onCancel : function(fileName){},
                messages :
                {
                    typeError    : "{file}\nArchivo no permitido.\n\n Solo se permiten los siguientes archivo:\n\n'xls', 'xlsx', 'csv'",
                    sizeError    : "\"{file}\"  Archivo muy grande, Tamano Maximo Permitido ( {sizeLimit} ).",
                    minSizeError : "{file} is too small, minimum file size is {minSizeLimit}.",
                    emptyError   : "{file} is empty, please select files again without it.",
                    onLeave      : "Cargando Archivo."
                }
            });
        }

        createUploader();

        //VENTANA MODAL CON LA IMAGEN DE AYUDA PARA CARGAR EL EXCEL
        function imagenAyudaModal() {


            var contenido = '<div style="margin: 0px auto;width:610px;" >'+
                            '<img src="img/puc.png"  >'+
                            '<br><spam style="color:#FFF;font-weight:bold;font-size:9px;">HAGA CLICK PARA CERRAR</spam>'+
                            '</div>';

            parentModal = document.createElement("div");
            parentModal.innerHTML = '<div id="modal">'+contenido+'</div>';
            parentModal.setAttribute("id", "divPadreModal");
            parentModal.setAttribute("onclick", "cerrarVentanaModal()");
            document.body.appendChild(parentModal);
            document.getElementById("divPadreModal").className = "fondo_modal_saldos";


        }

        function cerrarVentanaModal(){
            document.getElementById('divPadreModal').parentNode.removeChild(document.getElementById('divPadreModal'));
        }
    /* function hide and show select pais*/
        function mostrar_pais() {
            element = document.getElementById("content");
            select = document.getElementById("origen_empresa").value;

            if (select==='empresa_exterior') {
                document.getElementById("pais").selectedIndex = 0;
                buscaCiudad();
                buscaDep();
                element.style.display='block';
            }
            else if(select==='empresa_nacional') {
                buscaDep(49);
                buscaCiudad();
 /*warning! this index count from the 0 until value, it dont take db values, like  buscaDep(49) where 49 is the original value of db
  49 es el id pais : colombia por defecto en bd y 48 el valor del option en el html */
                document.getElementById("pais").selectedIndex = 48;//
                element.style.display='none';
            }
        }
    /*end */

    </script>

</body>
</html>
