<?php
	include("../../../configuracion/conectar.php");
	include("../../../configuracion/define_variables.php");
    include("../../../misc/MyGrilla/class.MyGrilla.php");

    //----------------------------------------------NUEVA----------------------------------------------------


    //    INICIALIZACION DE LA CLASE

            $grilla = new MyGrilla();


    //CONFIGURACION//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

        //NOMBRE DE LA GRILLA
            $grilla->GrillaName         = 'Experiencia_laboral';      //NOMBRE DE LA GRILLA (DEBE SER UNICO POR CADA GRILLA DE LA APLICACION)
        //QUERY
            $grilla->TableName          = 'empleados_experiencia_laboral';      //NOMBRE DE LA TABLA EN LA BASE DE DATOS
            $grilla->MyWhere            = "activo=1 AND id_empleado='".$id_empleado."'";     //WHERE DE LA CONSULTA A LA TABLA "$TableName"
            $grilla->MySqlLimit         = '0,50';           //LIMITE DE LA CONSULTA
        //TAMANO DE LA GRILLA
            $grilla->AutoResize         = 'true';           //SI LA GRILLA ES AUTORESIZABLE (LIQUIDA) -> "true" SI NO -> "false"
            $grilla->Ancho              = 800;              //ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'
            $grilla->Alto               = 220;              //ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'
            $grilla->QuitarAncho        = 70;               //AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'true'
            $grilla->QuitarAlto         = 270;              //AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'true'
        //TOOLBAR Y CAMPO DE BUSQUEDA
            $grilla->Gtoolbar           = 'true';           //SI LA GRILLA LLEVA EL TOOLBAR DE BUSQUEDA
            $grilla->CamposBusqueda     = 'nombre_empresa,ciudad,cargo';       //VARIABLE QUE DEFINE LOS CAMPOS DE LA BD DONDE SE BUSCARA
            $grilla->DivActualiBusqueda = '' ;              //VARIABLE QUE DEFINE LA CAPA DONDE SE ACTUALIZA LA GRILLA DESPUES DE UNA BUSQUEDA

        //CONFIGURACION DE CAMPOS EN LA GRILLA
            $grilla->AddRow('Empresa','empresa',150);//(Nombre Columna, Nombre del Campo, Largo de la Celda, Funcion PHP definida dentro de la clase para dar formato)
            $grilla->AddRow('Nombre Empresa','nombre_empresa',150);
            $grilla->AddRow('Cargo','cargo',150);
            $grilla->AddRow('Jefe Inmediato','jefe_inmediato',150);
            $grilla->AddRow('Telefono','telefono',150);
            $grilla->AddRow('Salario','salario_mensual',150);
            $grilla->AddRow('Ciudad','ciudad',150);

        //CONFIGURACION FORMULARIO
            $grilla->FContenedorAncho       = 350;
            $grilla->FColumnaGeneralAncho   = 330;
            $grilla->FColumnaGeneralAlto    = 25;
            $grilla->FColumnaLabelAncho     = 120;
            $grilla->FColumnaFieldAncho     = 200;

        //CONFIGURACION DE LA VENTANA DE INSERT, UPDATE Y DELETE
            $grilla->VentanaAuto        = 'true';          //SI LA VENTANA DE INSERT, UPDATE Y DELETE ES AUTOMATICA O MANUAL
            $grilla->TituloVentana      = 'Experiencia Laboral'; //NOMBRE DE LA VENTANA DE INSER, UPDATE Y DELETE
            $grilla->VBarraBotones      = 'true';          //SI HAY O NO BARRA DE BOTONES
            $grilla->VBotonNuevo        = 'true';          //SI LLEVA EL BOTON DE AGREGAR REGISTRO
            $grilla->VBotonNText        = 'Agregar Experiencia<br>Laboral'; //TEXTO DEL BOTON DE NUEVO REGISTRO
            $grilla->VBotonNImage       = 'documentadd';            //IMAGEN CSS DEL BOTON
            //$grilla->AddBotton('Nuevo','documentadd','BloqBtn(this); cargaDocumentoEmpleado();');
            $grilla->VAutoResize        = 'true';           //SI LA VENTANA ES AUTORESIZABLE (LIQUIDA) -> "true" SI NO -> "false"
            $grilla->VAncho             = 330;              //ANCHO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'false'
            $grilla->VAlto              = 430;              //ALTO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'false'
            $grilla->VQuitarAncho       = 10;               //AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ANCHO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'true'
            $grilla->VQuitarAlto        = 150;              //AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ALTO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'true'
            $grilla->VAutoScroll        = 'false';          //SI LA VENTANA TIENE O NO AUTOSCROLL
            $grilla->VBotonEliminar     = 'true';           //SI MUESTRA BOTON DE ELIMINAR EN LA VENTANA DE EDICION
            $grilla->VComporEliminar    = 'true';           //COMPORTAMIENTO DEL BOTON DE ELIMINAR ("true" ES CAMPO ACTIVO DE 1 A 0) ("false" -> ELIMINA EL REGISTRO DE LA BASE DE DATOS)

            //ARRAY EN MODO BD -> 'NOMBRE DE LA TABLA, NOMBRE DEL CAMPO INDEX, NOMBRE DEL CAMPO TEXTO ,SI MUESTRA SOLO LOS ACTIVOS(boolenao)'
            //ARRAY EN MODO MANUAL ' INDEX : TEXTO , INDEX : TEXTO, INDEX : TEXTO, INDEX : TEXTO '
            //$label,$field,$largo,$obligatorio,$bd='true',$array='',$where=''
            //$grilla->AddComboBox('Empresa','empresa',160,'false','false','Actual:Actual,Anterior 1:Anterior 1,Anterior 2:Anterior 2,Anterior 3:Anterior 3,Anterior 4:Anterior 4');//estatico
            $grilla->AddComboBox('Empresa','empresa',160,'true','false','Actual:Actual,Anterior:Anterior');//estatico
            $grilla->AddTextField('Empleado','id_empleado',160,'true','true',"$id_empleado");
            $grilla->AddTextField('Nombre Empresa','nombre_empresa',160,'true');//obligatorio
            $grilla->AddTextField('Ciudad','ciudad',160,'true');//obligatorio
            $grilla->AddTextField('Cargo','cargo',160,'true');//obligatorio
            //$grilla->AddTextField('Actividad','actividad',160,'false');//obligatorio
            $grilla->AddTextArea('Actividad','actividad',160,50,'true');//obligatorio
            $grilla->AddTextField('Fecha Inicio','fecha_inicio',160,'true');
            $grilla->AddTextField('Fecha Fin','fecha_fin',160,'false');
            $grilla->AddTextField('Jefe Inmediato','jefe_inmediato',160,'true');//obligatorio
            $grilla->AddTextField('Telefono','telefono',160,'true');//obligatorio
            $grilla->AddComboBox('Tipo Salario','salario',160,'false','false','Integral:Integral,Otro:Otro');//estatico
            $grilla->AddTextField('Valor Salario Mensual $','salario_mensual',160,'false');
            $grilla->AddTextField('Otros ingresos','otros_ingresos',160,'false');
            //$grilla->AddTextField('Mensual $','mensual',160,'false');
            $grilla->AddValidation('salario_mensual','numero');
            $grilla->AddValidation('otros_ingresos','numero');
            //$grilla->AddValidation('mensual','numero');
            //$grilla->AddComboBox('Actividad','actividad',160,'false','true','tipo_documento,id,detalle','activo=1');//dinamico

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


    //              INICIALIZACION DE LA GRILLA

        $grilla->Link = $link;      //Conexion a la BD
        $grilla->inicializa($_POST);//variables POST
        $grilla->GeneraGrilla();    // Inicializa la Grilla

    /////////////////////////////////////////////////////////
 if($opcion == 'Vupdate' || $opcion == 'Vagregar'){ ?>

    <script>

        document.getElementById('Experiencia_laboral_nombre_empresa').setAttribute("onkeyup","mayus()");
        var mayus=function(){
            var x = document.getElementById("Experiencia_laboral_nombre_empresa");
            x.value = x.value.toUpperCase();
        }


        new Ext.form.DateField({
            format     : 'Y-m-d',
            width      : 130,
            allowBlank : false,
            showToday  : false,
            applyTo    : 'Experiencia_laboral_fecha_inicio',
            editable   : false,
            listeners  : { select: function() {   } }
        });

        new Ext.form.DateField({
            format     : 'Y-m-d',
            width      : 130,
            allowBlank : false,
            showToday  : false,
            applyTo    : 'Experiencia_laboral_fecha_fin',
            editable   : false,
            listeners  : { select: function() {   } }
        });

        var empresa=document.getElementById('Experiencia_laboral_empresa').value;
        Ext.get('DIV_Experiencia_laboral_empresa').load({
            url     : 'experiencia_laboral/bd/bd.php',
            scripts : true,
            nocache : true,
            params  :
            {
                opc : 'select_empresa',
                empresa : empresa,
                id_empleado : '<?php echo $id_empleado ?>'
            }
        });

    </script>

<?php
}

if(!isset($opcion)){ ?>

    <script>
        document.getElementById('ContenedorPrincipal_Experiencia_laboral').style.marginTop = 20;
    </script>

<?php
} ?>


