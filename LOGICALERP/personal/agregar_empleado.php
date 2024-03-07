<?php
    include("../../configuracion/conectar.php");
    include("../../configuracion/define_variables.php");
    include("bd/functions_bd.php");

    $nombre_empresa  = mysql_result(mysql_query("SELECT nombre FROM empresas WHERE id = '$empresa'",$link),0,'nombre');
    $nombre_sucursal = mysql_result(mysql_query("SELECT nombre FROM empresas_sucursales WHERE id = '$sucursal'",$link),0,'nombre');

    $permiso_contrato  = (user_permisos(193,'false') <> 'true')? 'false' : 'true';

    $permiso_eliminar  = (user_permisos(192,'false') <> 'true')? 'false' : 'true';

    $btn_eliminar = ($permiso_eliminar=='true')? "Ext.getCmp('BtnEliminaEmpleado').enable();" : '' ;

    $id_pais = $_SESSION['PAIS'];

    if(!isset($rolvalor) || $rolvalor = ''){$rolvalor = 100;}
    if ($ID > 0) {

        $valores     = true; //INDICA QUE SE ESTA CARGANDO PARA EDICION
        $id_empleado = $ID; //VARIABLE REQUERIDA CUANDO EL FORMULARIO SEA PARA MODIFICACION

        cargaEmpleado($ID); //FUNCION QUE TRAE LOS DATOS DEL EMPLEADO A MODIFICAR
        echo"<script>
                var opcion_guardar = '$ID';
                var IDEMPRESA      = '$empresa';
                var IDSUCURSAL     = '$sucursal';
                var IDPERSONA      = '$ID';

                $btn_eliminar
                Ext.getCmp('btnTrasladarEmpleado').enable();
                Ext.getCmp('btnHVEmpleado').enable();

                //Ext.getCmp('BtnEmpleados5').enable();
            </script>";
    }
    else{ echo '<script> var opcion_guardar = "false";</script>'; }     //DEFINE LA VARIABLE JAVASCRIPT QUE INDICA QUE ES NUEVO


?>

<style>
    .EmpConte   { float:left; margin:0 0 0 0; width:340px; height:25px; }
    .EmpLabel   { float:left; width:150px; }
    .EmpField   { float:left; width:170px; }
    .EmpConte2  { float:left; margin:0 0 0 0; width:220px; height:25px; }
    .EmpLabel2  { float:left; width:70px; }
    .EmpField2  { float:left; width:140px; }
    .myfield    { width:170px; }
    .myfieldObligatorio { width:170px; }
    .EmpContDoc{ float:left; width:80px; }
    .EmpTituDoc{ float:left; width:60px; height:30px; text-align:center;}
    .EmpImgeDoc{ float:left; width:60px; }
    .EmpFindDoc{ float:left; width:60px; text-align:right; margin:3 0 0 0;}

    @media screen and (max-width: 5000px) {
        .LargoDivEmpleados{width:680px; }
    }
    @media screen and (max-width: 1024px) {
        .LargoDivEmpleados{width:340px; }
    }

</style>
<link rel="stylesheet" type="text/css" href="contratos/index.css">

<div id="Tabs_Empleados"></div>

<div id="Datos_Empleado" style >
    <form name="FormularioEmpleados" id="FormularioEmpleados">
        <div class="LargoDivEmpleados" style="float:left;">

            <div class="LargoDivEmpleados" style="float:left; margin:0 0 0 0;">
                <fieldset class="LargoDivEmpleados" style="padding:5px 0 10px 15px; margin:10 0 0 10; border:1px solid #999">
                    <legend><b>Datos del Empleado</b></legend>

                        <div class="EmpConte">
                            <div class="EmpLabel">
                                <b>Empresa:</b>
                            </div>
                            <div class="EmpField">
                                <input class="myfield" name="empresa1" type="text" id="empresa1" value="<?php echo $nombre_empresa; ?>" style="font-size:9px; font-weight:bold" readonly/>
                                <input class="myfield" name="id_empresa1"  type="hidden" id="id_empresa1" value="<?php echo $empresa; ?>"/>
                            </div>
                        </div>

                        <div class="EmpConte">
                            <div class="EmpLabel">
                                <b>Sucursal:</b>
                            </div>
                            <div class="EmpField">
                                <input class="myfield" name="sucursal1" type="text" id="sucursal1" value="<?php echo $nombre_sucursal; ?>" style="font-size:9px; font-weight:bold" readonly/>
                                <input class="myfield" name="id_sucursal1"  type="hidden" id="id_sucursal1" value="<?php echo $sucursal; ?>"/>
                            </div>
                        </div>

                        <div class="EmpConte">
                            <div class="EmpLabel">
                                Tipo Documento:
                            </div>
                            <div class="EmpField">
                                <select class="myfield" name="tipo_id1" id="tipo_id1" style="width:170px">
                                    <?php
                                        cargaOption("tipo_documento","id","nombre",false,$tipo_id1);
                                    ?>
                                </select>
                            </div>
                        </div>

                        <div class="EmpConte">
                            <div class="EmpLabel">
                                No. Documento:
                            </div>
                            <div class="EmpField">
                                <input class="myfieldObligatorio" name="id1" type="text" id="id1" <?php if ($valores) echo "value='$id1'"; else echo 'onblur="checkIdentificacion(); ValidarFieldVacio(this)"';?> onkeypress="return ValidarN(event)" />
                            </div>
                        </div>

                        <div class="EmpConte">
                            <div class="EmpLabel">
                                Primer Nombre:
                            </div>
                            <div class="EmpField">
                                <input class="myfieldObligatorio" onBlur="ValidarFieldVacio(this)" name="nombre1" type="text" id="nombre1" onkeypress="return ValidarNL(event);" <?php if ($valores) echo "value='$nombre1'";?>/>
                            </div>
                        </div>

                        <div class="EmpConte">
                            <div class="EmpLabel">
                                Segundo Nombre:
                            </div>
                            <div class="EmpField">
                                <input class="myfield" name="nombre2" type="text" id="nombre2" <?php if ($valores) echo "value='$nombre2'";?> />
                            </div>
                        </div>

                        <div class="EmpConte">
                            <div class="EmpLabel">
                                Primer Apellido:
                            </div>
                            <div class="EmpField">
                                <input class="myfieldObligatorio" onBlur="ValidarFieldVacio(this)" name="apellido1" type="text" id="apellido1" onkeypress="return ValidarNL(event)" <?php if ($valores) echo "value='$apellido1'";?>/>
                            </div>
                        </div>

                        <div class="EmpConte">
                            <div class="EmpLabel">
                                Segundo Apellido:
                            </div>
                            <div class="EmpField">
                                <input class="myfield" name="apellido2" type="text" id="apellido2" <?php if ($valores) echo "value='$apellido2'";?> />
                            </div>
                        </div>

                        <div class="EmpConte">
                            <div class="EmpLabel">
                                Cargo:
                            </div>
                            <div class="EmpField">
                                <select class="myfieldObligatorio" onBlur="ValidarFieldVacio(this)" name="cargo" id="cargo">
                                    <option value="" selected>Seleccione...</option>
                                    <?php
                                        cargaOption("empleados_cargos","id","nombre",false,$cargo);
                                    ?>
                                </select>
                            </div>
                        </div>
                </fieldset>
            </div>


            <div class="LargoDivEmpleados" style="float:left; margin:0 0 0 0">
                <fieldset class="LargoDivEmpleados" style="padding:5px 0 10px 15px; margin:10 0 0 10; border:1px solid #999">
                    <legend><b>Opciones y Configuraciones</b></legend>

                    <div class="EmpConte" style="">
                        <div class="EmpLabel">
                            Acceso al Sistema:
                        </div>
                        <div class="EmpField">
                            <select class="myfieldObligatorio" id="acceso_sistema" onchange="verificaAcceso(this.value)">
                                <option value="false">No</option>
                                <option value="true">Si</option>
                            </select>
                        </div>
                    </div>

                    <div class="EmpConte" style="" id="divParentUsuario">
                        <div class="EmpLabel">
                            Usuario (Correo electronico):
                        </div>
                        <div class="EmpField">
                            <input  class="myfieldObligatorio" name="username1" type="text" id="username1" onBlur="checkUsuario(); ValidarFieldVacio(this)" <?php if ($valores) echo "value='$username1'";?>/>
                        </div>
                    </div>

                    <div class="EmpConte" id="divParentClave">
                        <div class="EmpLabel">
                            Clave:
                        </div>
                        <div class="EmpField" style="width:140px">
                            <input style="width:140px" class="myfield" name="contrasena" type="password" id="contrasena" value="000000000000000" readonly />
                        </div>
                        <div class="EmpField" style="width:28px; margin:0 0 0 5px">
                            <img src="images/restart_password.png" width="28" height="20" style="cursor:pointer;" onClick="ResetContrasena()" title="Restablecer Contrasena">
                        </div>
                    </div>

                    <div class="EmpConte" id="divParentRol">
                        <div class="EmpLabel">
                            Rol de Seguridad:
                        </div>
                        <div class="EmpField">
                            <select class="myfieldObligatorio" onBlur="ValidarFieldVacio(this)" name="rol" id="rol" >
                                <option value="" selected>Seleccione...</option>
                                <?php
                                    $where     = ($_SESSION['ROLVALOR'] == 0)? "OR id = 1": "";
                                    $ConsulRol = mysql_query("SELECT * FROM empleados_roles WHERE activo = 1 AND id_empresa= $_SESSION[EMPRESA] AND (valor >= $_SESSION[ROLVALOR] $where) ORDER BY nombre ASC",$link);
                                    while($RowRol = mysql_fetch_array($ConsulRol)){
                                        $selected = ($RowRol['id'] == $rol)? 'selected' : '';
                                        echo '<option value="'.$RowRol['id'].'" '.$selected.'>'.$RowRol['nombre'].'</option>';
                                    }
                                ?>
                            </select>

                        </div>
                    </div>
                    <div class="EmpConte" id="divParentToken" style="width: 100%;margin-top: 10px;">
                        <div class="EmpLabel">
                            Token de Seguridad:
                        </div>
                        <div class="EmpField" style="width: calc(100% - 60px - 140px);">
                            <input style="width:100%" class="myfield" name="token" type="text" id="token" value="<?php echo $token; ?>" readonly />
                        </div>
                        <div class="EmpField" style="width:28px; margin:0 0 0 5px">
                            <img src="images/restart_password.png" width="28" height="20" style="cursor:pointer;" onClick="updateToken()" title="Actualizar Token de seguridad">
                        </div>
                    </div>
                </fieldset>
            </div>
        </div>

        <div style="float:left; width:220px">
            <div style="float:left; width:220px; margin:0 0 0 20px">
                <fieldset style="width:220px; height:170px; padding:0px 0 0 0; margin:10px 0 0 20px; border:1px solid #999">
                    <legend><b>Foto del Usuario</b></legend>
                    <iframe id="MANEJO_FOTOGRAFIA" name="MANEJO_FOTOGRAFIA" src="foto.php?ID=<?php echo $id_empleado ?>" scrolling="no" width="230" height="150" frameborder="0" style="margin:0 0 0 0;"></iframe>
                </fieldset>
            </div>
        </div>
    </form>
</div>
<div id="divLoad" style="display: none;"></div>
<script>
    // optionDepartamento('<?php echo $id_pais ?>', '<?php echo $ID ?>');
    function optionDepartamento(id_pais, id_empleado){
        Ext.get('contenedor_departamento').load({
            url     : 'bd/bd.php',
            scripts : true,
            nocache : true,
            params  :
            {
                op          : 'option_departamento',
                id_pais     : id_pais,
                id_empleado : '<?php echo $ID ?>'
            }
        });
    }

    function optionCiudad(id_departamento, id_empleado){
        Ext.get('contenedor_ciudad').load({
            url     : 'bd/bd.php',
            scripts : true,
            nocache : true,
            params  :
            {
                op              : 'option_ciudad',
                id_departamento : id_departamento,
                id_empleado     : id_empleado
            }
        });
    }

    id_empleado_adjuntar_equipo = '<?php echo $ID ?>';

    var myalto  = Ext.getBody().getHeight();
    var myancho = Ext.getBody().getWidth();

    if(opcion_guardar == "false"){
        var habilita_tab = true;
        var habilita_tab_contrato = true;
    }
    else{
        var habilita_tab  = false;
        var habilita_tab_contrato = ('<?php echo $permiso_contrato; ?>' == 'true')? false : true ;
    }
    new Ext.TabPanel({
        deferredRender  : true,
        border          : false,
        renderTo        : 'Tabs_Empleados',
        activeTab       : 0,
        style           : 'margin: 10px 0 0 0',
        //bodyStyle         : 'background-color:<?php echo $_SESSION['COLOR_FONDO'] ?>;',
        items           :
        [
            {
                closable    : false,
                autoScroll  : true,
                id          : 'PanelDatosDeEmpleados',
                height      : myalto - 144,
                title       : 'Datos del Empleado',
                contentEl   : 'Datos_Empleado',
                iconCls     : 'user16',
                bodyStyle   : 'background-color:<?php echo $_SESSION['COLOR_CONTRASTE'] ?>;'
            }
            ,
            // {
            //     closable   : false,
            //     autoScroll : false,
            //     title      : 'Informacion de Contacto',
            //     iconCls    : 'user16',
            //     id         : 'contactos_empleado',
            //     disabled   :  habilita_tab,
            //     bodyStyle  : 'background-color:<?php echo $_SESSION['COLOR_CONTRASTE'] ?>;',
            //     autoLoad   :
            //     {
            //         url : 'agregar_empleado_contactos.php',
            //         nocache : true,
            //         scripts : true,
            //         params  : { ID : '<?php echo $ID ?>' }
            //     }
            // },
            {
                closable   : false,
                autoScroll : false,
                title      : 'Documentos del Empleado',
                iconCls    : 'user16',
                id         : 'documento_empleado',
                disabled   : habilita_tab,
                bodyStyle  : 'background-color:<?php echo $_SESSION['COLOR_CONTRASTE'] ?>;',
                autoLoad   :
                {
                    url     : 'agregar_empleado_documentos.php',
                    nocache : true,
                    scripts : true,
                    params  : { ID : '<?php echo $ID ?>' }
                }
            },
            {
                closable   : false,
                autoScroll : false,
                title      : 'Contrato',
                iconCls    : 'doc',
                id         : 'contrato_empleado',
                disabled   : habilita_tab_contrato,
                bodyStyle  : 'background-color:<?php echo $_SESSION['COLOR_CONTRASTE'] ?>;',
                autoLoad   :
                {
                    url : 'contratos/agregar_empleado_contratos.php',
                    nocache : true,
                    scripts : true,
                    params  :
                    {
                        ID       : '<?php echo $ID ?>',
                        sucursal : '<?php echo $sucursal ?>',
                    }
                }
            }
        ]
    });

    ValidaFormularioEnCarga('FormularioEmpleados');

    <?php

        if (isset($ID) && $ID != 'false') { ?>
            var MIROLVALOR = '<?php echo $_SESSION['ROLVALOR'] ?>';
            var ROLVALOR   = '<?php echo $rolvalor ?>';

            if(ROLVALOR == ''){ ROLVALOR = 100; }
            if(MIROLVALOR != 0 && MIROLVALOR >= ROLVALOR){ document.getElementById('rol').disabled = 'disabled'; }     //SI NO SOY SUPERUSUARIO

    <?php } ?>

    function cargaDocumentoEmpleado(){
        if(opcion_guardar == 'false'){ alert('Primero debe Guardar el Usuario y despues subir los Documentos!'); }
        else{
            Win_Documentos_Empleados = new Ext.Window({
                //id            : 'Win_Documentos_Empleados',
                width       : 320,
                height      : 140,
                title       : 'Subir Documento',
                modal       : true,
                autoScroll  : false,
                autoDestroy : true,
                autoLoad    :
                {
                    url     : 'sele_tipo_documento.php',
                    scripts : true,
                    nocache : true,
                    params  : { id : opcion_guardar }
                }
            }).show();
        }
    }

    function horasExtras(){
        var id = document.getElementById('id1').value;

        if(opcion_guardar == 'false'){ alert('Primero debe Guardar el Usuario y despues verificar el informe!'); }
        else{
            Win_Horas_Extras = new Ext.Window({
                //id            : 'Win_Documentos_Empleados',
                width       : 750,
                height      : 500,
                title       : 'Consulta Horas Extras',
                modal       : true,
                autoScroll  : false,
                autoDestroy : true,
                autoLoad    :
                {
                    url     : 'horas_extras.php',
                    scripts : true,
                    nocache : true,
                    params  : { id : id }
                }
            }).show();
        }
    }

    function SiguienteDocumento0(id,td){
        Win_Documentos_Empleados.load({
            url     : 'upload/documento_empleados.php',
            scripts : true,
            nocache : true,
            params  :
            {
                id : id,
                td : td
            }
        });
        Win_Documentos_Empleados.setSize(320,200);
    }


    function VerDocumento(id,id_empleado){ window.open('ver_documento.php?id0='+id+'&id1='+id_empleado); }

    function EliminarDocumento(id,id_empleado,documento,archivo){
        var continuar = confirm('Desea Eliminar el Documento " '+documento+' " ?');

        if(continuar==true ){
            Ext.Ajax.request({
                url     : 'eliminar_documento.php',
                method  : 'post',
                timeout : 180000,
                params  :
                {
                    id          : id,
                    id_empleado : id_empleado,
                    archivo     : archivo
                },
                success: function (result, request)
                {
                    var resultado =  result.responseText.split("{.}");
                    var respuesta = resultado[0];
                    var elid      = resultado[1];

                    if(respuesta == 'false'){ alert('Error Eliminando el documento!'); }
                    if(respuesta == 'true'){
                        MyLoading();
                        Elimina_Div_Documentos_Empleado(id);
                    }
                }
            });
        }
    }

    function Elimina_Div_Documentos_Empleado(elid){
        document.getElementById("item_documentos_"+elid).parentNode.removeChild(document.getElementById("item_documentos_"+elid));
                // Ext.get("item_documentos_"+elid).load(
                //  {
                //      url     : 'eliminar_documento.php',
                //      timeout : 180000,
                //      scripts : true,
                //      nocache : true,
                //      params  :
                //          {
                //              elid    :   elid,
                //              opcion  :   "muestra_grilla_eliminado"
                //          }
                //  }
                // );
    }

    function agregarEmpleado(){
        if(opcion_guardar == "false"){ var op = "agregarEmpleado"; }
        else{ var op = "actualizarEmpleado"; }

        var id_sucursal1   = document.getElementById('id_sucursal1').value
        ,   tipo_id1       = document.getElementById('tipo_id1').value
        ,   id1            = document.getElementById('id1').value
        ,   nombre1        = document.getElementById('nombre1').value.toUpperCase()
        ,   nombre2        = document.getElementById('nombre2').value.toUpperCase()
        ,   apellido1      = document.getElementById('apellido1').value.toUpperCase()
        ,   apellido2      = document.getElementById('apellido2').value.toUpperCase()
        ,   cargo          = document.getElementById('cargo').value
        ,   rol            = document.getElementById('rol').value
        ,   acceso_sistema = document.getElementById("acceso_sistema").value
        ,   username1      =(acceso_sistema=='true')? document.getElementById('username1').value : ''
        ,   contrasena     = document.getElementById('contrasena').value

        // var nacimiento1    = document.getElementById('nacimiento1').value;
        // var direccion1     = document.getElementById('direccion1').value;
        // var telefono1      = document.getElementById('telefono1').value;
        // var telefono2      = document.getElementById('telefono2').value;
        // var celular1       = document.getElementById('celular1').value;
        // var celular2       = document.getElementById('celular2').value;
        // var mail1          = document.getElementById('mail1').value;
        // var mail2          = document.getElementById('mail2').value;
        // var pais           = document.getElementById('pais').value;
        // var departamento   = document.getElementById('departamento').value;
        // var ciudad         = document.getElementById('ciudad').value;

        if (acceso_sistema=='true') {

            if (username1==""||rol=="") {
                alert("Si el empleado tiene acceso al sistema debe seleccionar el rol de seguridad y el usuario");
                return;
            }

            // if(!validarEmail(username1)){
            //     alert('"'+username1+'" es un e-mail invalido');
            //     return;
            // }
        }

        if(id1==""||nombre1==""||apellido1==""|| cargo=="" ){
            alert("Faltan alguno de los campos obligatorios por diligenciar!");
        }
        else{

            Ext.Ajax.request({
                url     : 'bd/bd.php',
                method  : 'post',
                timeout : 180000,
                params  :
                {
                    op             :    op,
                    ID             :    opcion_guardar,
                    id_sucursal1   :    id_sucursal1,
                    tipo_id1       :    tipo_id1,
                    id1            :    id1,
                    nombre1        :    nombre1,
                    nombre2        :    nombre2,
                    apellido1      :    apellido1,
                    apellido2      :    apellido2,
                    rol            :    rol,
                    cargo          :    cargo,
                    username1      :    username1,
                    contrasena     :    contrasena,
                    acceso_sistema : acceso_sistema,
                },
                success: function (result, request){
                    var resultado   = result.responseText.split("{.}");
                    var respuesta   = resultado[0];
                    var observacion = resultado[1];
                    var opcion      = resultado[2];

                    if(respuesta == 'false'){ alert(observacion); }
                    if(respuesta == 'true'){

                        opcion_guardar=observacion;
                        if(op == "agregarEmpleado"){
                            MyLoading();

                            Inserta_Div_Empleados(observacion);
                            Habilita_Frame_Webcam(observacion);

                            Ext.getCmp("BtnEliminaEmpleado").enable();
                            Ext.getCmp("documento_empleado").enable();

                            //Ext.getCmp("BtnEmpleados3").enable();
                            //Ext.getCmp("BtnEmpleados4").enable();
                            //Ext.getCmp("BtnEmpleados5").enable();
                        }
                        if(op == "actualizarEmpleado"){
                            MyLoading();
                            Actualiza_Div_Empleados(observacion);
                        }
                    }
                }
            });
        }
    }

    function ResetContrasena(){
        var op        = "resetPass";
        var continuar = confirm("Desea resetear la contraseña a la predeterminada?")
        if(continuar==true ){

            if(opcion_guardar == "false" ){ alert("No existe empleado"); }
            else{

                Ext.Ajax.request({
                    url     : 'bd/bd.php',
                    method  : 'post',
                    timeout : 180000,
                    params  :
                    {
                        op  : op,
                        id  : opcion_guardar
                    },
                    success : function (result, request)
                    {
                        var resultado  =  result.responseText.split("{.}");
                        var respuesta = resultado[0];
                        var observacion = resultado[1];
                        if(respuesta == 'false'){ alert('Error Enviando la Solicitud!\n\n'+observacion); }
                        else if(respuesta == 'true'){ alert ('La contraseña ha sido restaurada a la predeterminada "12345678"'); }
                    }
                });
            }
        }
    }

    function updateToken(){
        if(opcion_guardar == "false" ){ alert("No existe empleado"); }
        else{
            MyLoading2('on');
            Ext.get('divLoad').load({
                url     : 'bd/bd.php',
                scripts : true,
                nocache : true,
                params  :
                {
                    op : 'updateToken',
                    id : '<?php echo $ID ?>',
                }
            });
        }
    }

    function checkIdentificacion(){

        var op          = "checkIdentificacion";
        var box         = document.getElementById('id1');
        var field       = box.value.toUpperCase();
        var lasucursal  = document.getElementById('id_sucursal1').value;

        Ext.Ajax.request({
            url     : 'bd/bd.php',
            method  : 'post',
            timeout : 180000,
            params  :
            {
                op       : op,
                id1      : field,
                sucursal : lasucursal
            },
            success: function (result, request)
                {
                    var resultado     = result.responseText.split("{.}");
                    var Existe        = resultado[0];
                    var EstaActivo    = resultado[1];
                    var MismaEmpresa  = resultado[2];
                    var MismaSucursal = resultado[3];
                    var id            = resultado[4];
                    var nombre        = resultado[5];
                    var Empresa       = resultado[6];
                    var Sucursal      = resultado[7];

                    if(Existe == 'true'){
                        if(EstaActivo == 'true'){
                            if(MismaEmpresa == 'false'){
                                document.getElementById('id1').value="";
                                alert('El Empleado "'+nombre+'"\nesta activo en la Empresa "'+Empresa+' - '+Sucursal+'"\n\nDeben de eliminarlo antes de crearlo en esta Empresa!');return false;

                                if(MismaSucursal == 'false'){
                                    alert('El Empleado "'+nombre+'"\nesta activo en la Sucursal "'+Empresa+' - '+Sucursal+'"\n\nDeben de eliminarlo antes de crearlo en esta Sucursal!');return false;
                                }
                                else{
                                    MyLoading();
                                    Win_Agregar_Empleado.close();
                                    Agregar_Empleado(id);
                                }
                            }
                            else{
                                if(MismaSucursal == 'false'){
                                    document.getElementById('id1').value="";
                                    alert('El Empleado "'+nombre+'"\nesta activo en la Sucursal "'+Empresa+' - '+Sucursal+'"\n\nDeben de eliminarlo antes de crearlo en esta Sucursal!');return false;
                                }
                                else{
                                    MyLoading();
                                    Win_Agregar_Empleado.close();
                                    Agregar_Empleado(id);
                                }
                            }
                        }else{
                            function termina(btn){
                                if(btn == 'yes'){
                                    var empresa = document.getElementById('id_empresa1').value;
                                    var sucursal= document.getElementById('id_sucursal1').value;

                                    Ext.Ajax.request({
                                    url     : 'bd/bd.php',
                                    method  : 'post',
                                    timeout : 180000,
                                    params  :
                                    {
                                        op       : 'RestaurarEmpleado',
                                        id       : id,
                                        empresa  : empresa,
                                        sucursal : sucursal
                                    },
                                    success: function (result, request)
                                        {
                                            var resultado =  result.responseText.split("{.}");
                                            var respuesta = resultado[0];
                                            var id        = resultado[1];

                                            if(respuesta == 'true'){
                                                Win_Agregar_Empleado.close();
                                                Agregar_Empleado(id);
                                                setTimeout('Inserta_Div_Empleados('+id+')',2000);
                                                MyLoading();
                                            }
                                            else{ alert('Error Activando Empleado!'); }
                                        }
                                    });
                                }
                            }

                            Ext.MessageBox.buttonText.yes = "Si";
                            Ext.MessageBox.buttonText.no  = "No";

                            if(MismaEmpresa == 'false'){
                                Ext.MessageBox.confirm('Activar Empleado', 'El Empleado "'+nombre+'"<br />Fue Eliminado en la Empresa "'+Empresa+' - '+Sucursal+'<br /><br />Desea Activarlo de nuevo en esta Empresa?', termina);
                            }
                            else{
                                if(MismaSucursal == 'false'){
                                    Ext.MessageBox.confirm('Activar Empleado', 'El Empleado "'+nombre+'"<br />Fue Eliminado en la Sucursal "'+Empresa+' - '+Sucursal+'<br /><br />Desea Activarlo de nuevo en esta Sucursal?', termina);
                                }
                                else{
                                    Ext.MessageBox.confirm('Activar Empleado', 'El Empleado "'+nombre+'"<br />Fue Eliminado con anterioridad!<br /><br />Desea Activarlo de nuevo?', termina);
                                }
                            }
                        }
                    }
                }
            });
    }

    function checkMail(){
        var mail=document.getElementById('mail2').value;
        if(mail!=""){
            // if(!validarEmail(mail)){
            //     alert('"'+mail+'" es un e-mail invalido');
            //     document.getElementById('mail2').value="";
            // }
        }
    }

    function validarUsuario(){

        var username1 = document.getElementById('username1').value;
        var nombre1   = document.getElementById('nombre1').value;
        var apellido1 = document.getElementById('apellido1').value;

        // document.getElementById('username1').value = "";

        if(nombre1!="" && apellido1!=""){
            var user = nombre1+"."+apellido1;
            user = user.replace(/ /g,"").toLowerCase();; //ELIMINA ESPACIOS EN BLANCO Y COLOCA MINUSCULAS
            user = user.replace(/ñ/g, "n");
            user = user.replace(/á/g, "a");
            user = user.replace(/é/g, "e");
            user = user.replace(/í/g, "i");
            user = user.replace(/ó/g, "o");
            user = user.replace(/ú/g, "u");

            // document.getElementById('username1').value = user;
        }
    }

    function checkUsuario(){

        var user = document.getElementById('username1').value;
        var op   = "checkUsername";

        Ext.Ajax.request({
            url     : 'bd/bd.php',
            method  : 'post',
            timeout : 180000,
            params  :
            {
                op   : op,
                id   : opcion_guardar,
                user : user
            },
            success: function (result, request)
            {
                var resultado  =  result.responseText.split("{.}");
                var respuesta = resultado[0];
                var observacion = resultado[1];

                if(respuesta == 'false'){ alert('Error Enviando la Solicitud!\n\n'+observacion); }
                if(respuesta == 'true'&& user!=""){
                    if(observacion!="false"){
                        user= prompt("El usuario "+user+" ya existe, por favor ingresa otro:",user);
                        document.getElementById('username1').value=user;
                            checkUsuario();
                    }
                    else{ if(document.getElementById('username1')){ document.getElementById('username1').value=user; }}
                }
            }
        });
    }

    function Habilita_Frame_Webcam(ID){ document.getElementById('MANEJO_FOTOGRAFIA').src='foto.php?ID='+ID; }

    function cargaFotoEmpleado(ID){
        if(typeof(ID) == "undefined"){ alert('Primero debe Guardar el Empleado y despues subir la Fotografia!.'); }
        else{

            Win_Fotos_Empleados = new Ext.Window({
                width       : 300,
                height      : 200,
                title       : 'Subir Imagen',
                modal       : true,
                autoScroll  : false,
                autoDestroy : true,
                autoLoad    :
                {
                    url     : 'upload/foto_empleados.php',
                    scripts : true,
                    nocache : true,
                    params  : { id : ID }
                }
            }).show();
        }
    }

    function configura_cuentas_contabilidad(){
        if(PermisoConfigContabilidad == false){

            Win_configura_cuentas_contabilidad = new Ext.Window({
                width       : 700,
                id          : 'Win_configura_cuentas_contabilidad',
                height      : 400,
                title       : 'Configurar Cuentas de Contabilidad',
                modal       : true,
                autoScroll  : true,
                autoDestroy : true,
                autoLoad    :
                {
                    url     : 'extragerto/config_cuentas_contabilidad.php',
                    scripts : true,
                    nocache : true,
                    params  : { }
                },
                tbar        :
                [
                    {
                        xtype       : 'button',
                        text        : 'Roles de Usuario<br />Contabilidad',
                        scale       : 'large',
                        iconCls     : 'roles33',
                        iconAlign   : 'left',
                        handler     : function(){ BloqBtn(this); Roles_Usuarios_Contabilidad(); }
                    }/*,
                    {
                        xtype       : 'button',
                        id          : '__BtnEliminaEmpleado',
                        text        : 'Eliminar<br />Empleado',
                        scale       : 'large',
                        iconCls     : 'eliminaruser',
                        iconAlign   : 'left',
                        disabled    : true,
                        handler     : function(){ BloqBtn(this); }
                    }*/
                ]
            }).show();
        }
        else{ alert('No tiene Privilegios para ejecutar esta Opción!'); }
    }

    function mail(){
        if(PermisoConfigCorreo == false){
            Win_Config_Correo = new Ext.Window
            (
                {
                    width       : 370,
                    id          : 'Win_Config_Correo',
                    height      : 90,
                    title       : 'Configuracion Correo',
                    border      : false,
                    modal       : true,
                    autoScroll  : true,
                    autoDestroy : true,
                    autoLoad    :
                    {
                        url     : 'mail.php',
                        scripts : true,
                        nocache : true,
                        /*params    :
                                {
                                    ID      :   id,
                                    empresa :   empresa,
                                    sucursal:   sucursal
                                }*/
                    },
                    tbar        :
                    [

                        {
                            xtype       : 'button',
                            width       : 112,
                            height      : 75,
                            id          : 'BtnCorreoCrear',
                            text        : 'Crear Correo',
                            scale       : 'large',
                            iconCls     : 'addmail',
                            iconAlign   : 'top',
                            disabled    : true,
                            handler     : function(){ BloqBtn(this); creaCorreo(); }
                        },'-',
                        {
                            xtype       : 'button',
                            width       : 112,
                            height      : 75,
                            id          : 'BtnCorreoEliminar',
                            text        : 'Eliminar Correo',
                            scale       : 'large',
                            iconCls     : 'eliminarmail',
                            iconAlign   : 'top',
                            disabled    : true,
                            handler     : function(){ BloqBtn(this); eliminaCorreo(); }
                        },'-',/*' ',' ',' ',' ','-',' ',' ',' ',' ',
                        {
                            xtype       : 'button',
                            id          : 'BtnCorreoActivo',
                            text        : 'Desactivar/Activar<br />Correo',
                            scale       : 'large',
                            iconCls     : 'mail1',
                            iconAlign   : 'top',
                            disabled    : true,
                            handler     : function(){ BloqBtn(this); }
                        },  */
                        {
                            xtype       : 'button',
                            width       : 112,
                            height      : 75,
                            id          : 'BtnCorreoReset',
                            text        : 'Resetear Contrase&ntilde;a',
                            scale       : 'large',
                            iconCls     : 'resetpassword',
                            iconAlign   : 'top',
                            disabled    : true,
                            handler     : function(){ BloqBtn(this); resetPassword(); }
                        }
                    ]
                }
            ).show();
        }
        else{ alert('No tiene Privilegios para ejecutar esta Opción!'); }
    }

    // =========== FUNCION PARA VERIFICAR EL ACCESO AL SISTEMA ===========//
    verificaAcceso('<?php echo $acceso_sistema; ?>');
    function verificaAcceso(acceso) {
        if (acceso=='true') {
            document.getElementById("acceso_sistema").value           = "true";
            document.getElementById("divParentUsuario").style.display = "inline";
            document.getElementById("divParentClave").style.display   = "inline";
            document.getElementById("divParentRol").style.display     = "inline";
            validarUsuario();
        }
        else if (acceso=='false') {
            document.getElementById("acceso_sistema").value           = "false";
            document.getElementById("divParentUsuario").style.display = "none";
            document.getElementById("divParentClave").style.display   = "none";
            document.getElementById("divParentRol").style.display     = "none";
            document.getElementById('username1').value                = "";
        }
        else{
            document.getElementById("acceso_sistema").value           = "true";
            document.getElementById("divParentUsuario").style.display = "inline";
            document.getElementById("divParentClave").style.display   = "inline";
            document.getElementById("divParentRol").style.display     = "inline";
        }
            // console.log(document.getElementById('username1').value);

    }

    function ventana_traslado(){
        var id = document.getElementById('id1').value;

        Win_Ventana_traslado = new Ext.Window({
            width       : 550,
            height      : 500,
            id          : 'Win_Ventana_traslado',
            title       : 'Trasladar de Sucursal a <?php echo $nombre1; ?> <?php echo $nombre2; ?> <?php echo $apellido1; ?> <?php echo $apellido2; ?>',
            modal       : true,
            autoScroll  : false,
            closable    : false,
            autoDestroy : true,
            autoLoad    :
            {
                url     : 'traslado_sucursal.php',
                scripts : true,
                nocache : true,
                params  :
                {
                    documento_empleado : id,
                    var2 : 'var2',
                }
            },
            tbar        :
            [
                {
                    xtype   : 'buttongroup',
                    columns : 3,
                    title   : 'Opciones',
                    style   : 'border-right:none;',
                    items   :
                    [
                        {
                            xtype       : 'button',
                            width       : 60,
                            height      : 56,
                            text        : 'Regresar',
                            scale       : 'large',
                            iconCls     : 'regresar',
                            iconAlign   : 'top',
                            hidden      : false,
                            handler     : function(){ BloqBtn(this); Win_Ventana_traslado.close(id) }
                        }
                    ]
                }
            ]
        }).show();

    }

</script>
