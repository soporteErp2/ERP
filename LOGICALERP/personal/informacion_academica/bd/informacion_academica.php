<?php
	include("../../../configuracion/conectar.php");
	include("../../../configuracion/define_variables.php");
    include("../../../misc/MyGrilla/class.MyGrilla.php");

    //INICIALIZACION DE LA CLASE
        $grilla = new MyGrilla();


    //CONFIGURACION//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

        //NOMBRE DE LA GRILLA
            $grilla->GrillaName         = 'Informacion_academica';      //NOMBRE DE LA GRILLA (DEBE SER UNICO POR CADA GRILLA DE LA APLICACION)
        //QUERY
            $grilla->TableName          = 'empleados_estudios';      //NOMBRE DE LA TABLA EN LA BASE DE DATOS

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
            $grilla->CamposBusqueda     = 'institucion,ciudad,tipo_estudio';       //VARIABLE QUE DEFINE LOS CAMPOS DE LA BD DONDE SE BUSCARA
            $grilla->DivActualiBusqueda = '' ;              //VARIABLE QUE DEFINE LA CAPA DONDE SE ACTUALIZA LA GRILLA DESPUES DE UNA BUSQUEDA
        //CONFIGURACION DE CAMPOS EN LA GRILLA
            //(Nombre Columna, Nombre del Campo, Largo de la Celda, Funcion PHP definida dentro de la clase para dar formato)
            $grilla->AddRow('Tipo de estudio','tipo_estudio',150);
            $grilla->AddRow('Grado','grado',150);
            $grilla->AddRow('Institucion','institucion',150);
            $grilla->AddRow('Ciudad','ciudad',150);

        //CONFIGURACION FORMULARIO
            $grilla->FContenedorAncho       = 350;
            $grilla->FColumnaGeneralAncho   = 330;
            $grilla->FColumnaGeneralAlto    = 25;
            $grilla->FColumnaLabelAncho     = 120;
            $grilla->FColumnaFieldAncho     = 200;

        //CONFIGURACION DE LA VENTANA DE INSERT, UPDATE Y DELETE
            $grilla->VentanaAuto        = 'true';          //SI LA VENTANA DE INSERT, UPDATE Y DELETE ES AUTOMATICA O MANUAL
            $grilla->TituloVentana      = 'Informacion Acad&eacute;mica'; //NOMBRE DE LA VENTANA DE INSER, UPDATE Y DELETE
            $grilla->VBarraBotones      = 'true';          //SI HAY O NO BARRA DE BOTONES
            $grilla->VBotonNuevo        = 'true';          //SI LLEVA EL BOTON DE AGREGAR REGISTRO
            $grilla->VBotonNText        = 'Agregar Informacion<br>Academica'; //TEXTO DEL BOTON DE NUEVO REGISTRO
            $grilla->VBotonNImage       = 'documentadd';            //IMAGEN CSS DEL BOTON
            //$grilla->AddBotton('Nuevo','documentadd','BloqBtn(this); cargaDocumentoEmpleado();');
            $grilla->VAutoResize        = 'true';           //SI LA VENTANA ES AUTORESIZABLE (LIQUIDA) -> "true" SI NO -> "false"
            $grilla->VAncho             = 350;              //ANCHO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'false'
            $grilla->VAlto              = 380;              //ALTO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'false'
            $grilla->VQuitarAncho       = 70;               //AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ANCHO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'true'
            $grilla->VQuitarAlto        = 50;              //AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ALTO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'true'
            $grilla->VAutoScroll        = 'false';          //SI LA VENTANA TIENE O NO AUTOSCROLL
            $grilla->VBotonEliminar     = 'true';           //SI MUESTRA BOTON DE ELIMINAR EN LA VENTANA DE EDICION
            $grilla->VComporEliminar    = 'true';           //COMPORTAMIENTO DEL BOTON DE ELIMINAR ("true" ES CAMPO ACTIVO DE 1 A 0) ("false" -> ELIMINA EL REGISTRO DE LA BASE DE DATOS)

            //ARRAY EN MODO BD -> 'NOMBRE DE LA TABLA, NOMBRE DEL CAMPO INDEX, NOMBRE DEL CAMPO TEXTO ,SI MUESTRA SOLO LOS ACTIVOS(boolenao)'
            //ARRAY EN MODO MANUAL ' INDEX : TEXTO , INDEX : TEXTO, INDEX : TEXTO, INDEX : TEXTO '
            //$label,$field,$largo,$obligatorio,$bd='true',$array='',$where=''
            $grilla->AddComboBox('Tipo de Estudio','tipo_estudio',160,'true','false','Primaria:Primaria,Secundaria:Secundaria,Universitario Pregrado:Universitario Pregrado,Universitario Diplomado:Universitario Diplomado,Universitario Especializacion:Universitario Especializacion,Universitario Maestrias:Universitario Maestrias,Otro:Otro');//estatico
            $grilla->AddTextField('Describalo','otro',160,'false');
            $grilla->AddTextField('Titulo Obtenido','grado',160,'true');
            $grilla->AddTextField('Institucion','institucion',160,'true');
            $grilla->AddTextField('Empleado','id_empleado',160,'true','true',"$id_empleado");
            //$grilla->AddComboBox('Tipo Identificacion','id_tipo_identificacion',160,'false','true','tipo_documento,id,detalle','activo=1');//dinamico
            $grilla->AddTextField('Fecha de Inicio','fecha_inicio',160,'false');
            $grilla->AddTextField('Fecha de Terminacion','fecha_fin',160,'false');
            $grilla->AddComboBox('Ciclo','ciclo',160,'false','false','Diurno:Diurno,Nocturno:Nocturno');
            //$grilla->AddTextField('Modalidad Presencial','modalidad_presencial',160,'false');
            $grilla->AddComboBox('Modalidad','modalidad_presencial',160,'true','false','Presencial:Presencial,Online:Online,Otro:Otro');
            $grilla->AddTextField('Describa modalidad','otra_modalidad',160,'true');//modalidad
            $grilla->AddTextField('Tarjeta Profesional','tarjeta_profesional',160,'false');
            $grilla->AddTextField('Ciudad','ciudad',160,'true');


    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


    //              INICIALIZACION DE LA GRILLA

        $grilla->Link = $link;      //Conexion a la BD
        $grilla->inicializa($_POST);//variables POST
        $grilla->GeneraGrilla();    // Inicializa la Grilla

    /////////////////////////////////////////////////////////
 if($opcion == 'Vupdate' || $opcion == 'Vagregar'){ ?>

    <script>

        document.getElementById('Informacion_academica_tipo_estudio').setAttribute("onchange","cambia()");

        function cambia(){

            var tipo_estudio=document.getElementById('Informacion_academica_tipo_estudio').value;
            if(tipo_estudio == 'Otro'){ document.getElementById('EmpConte_Informacion_academica_otro').style.display = "inline"; }
            else{ document.getElementById('EmpConte_Informacion_academica_otro').style.display = "none"; document.getElementById('Informacion_academica_otro').value=' '; }

            if(tipo_estudio=='Universitario Pregrado'){ document.getElementById('EmpConte_Informacion_academica_tarjeta_profesional').style.display = "inline"; }
            else{ document.getElementById('EmpConte_Informacion_academica_tarjeta_profesional').style.display = "none"; }
        }


        document.getElementById('Informacion_academica_modalidad_presencial').setAttribute("onchange","otra_modalidad()");

        var otra_modalidad=function(){

            var modalidad=document.getElementById('Informacion_academica_modalidad_presencial').value;

            if(modalidad=='Otro'){
                document.getElementById('EmpConte_Informacion_academica_otra_modalidad').style.display = "inline";

            }else{
                document.getElementById('EmpConte_Informacion_academica_otra_modalidad').style.display = "none";
                document.getElementById('Informacion_academica_otra_modalidad').value=' ';
            }
        }

        <?php if($opcion == 'Vupdate'){ ?>

                    if(document.getElementById('Informacion_academica_tipo_estudio').value=="Universitario Pregrado"){
                        document.getElementById('EmpConte_Informacion_academica_tarjeta_profesional').style.display = "inline";
                    }else{ document.getElementById('EmpConte_Informacion_academica_tarjeta_profesional').style.display = "none"; }

                    if(document.getElementById('Informacion_academica_tipo_estudio').value=="Otro"){
                        document.getElementById('EmpConte_Informacion_academica_otro').style.display = "inline";
                    }else{ document.getElementById('EmpConte_Informacion_academica_otro').style.display = "none"; }

                    if(document.getElementById('Informacion_academica_modalidad_presencial').value=='Otro'){
                        document.getElementById('EmpConte_Informacion_academica_otra_modalidad').style.display = "inline";
                    }else{ document.getElementById('EmpConte_Informacion_academica_otra_modalidad').style.display = "none"; }//oculta el campo describa modalidad

        <?php }else{ ?>
                        document.getElementById('EmpConte_Informacion_academica_otra_modalidad').style.display = "none";//oculta el campo describa modalidad
                        document.getElementById('EmpConte_Informacion_academica_otro').style.display = "none";
                        document.getElementById('EmpConte_Informacion_academica_tarjeta_profesional').style.display = "none";
        <?php
        }
        ?>


        var tipo=document.getElementById('Informacion_academica_tipo_estudio').value;
        Ext.get('DIV_Informacion_academica_tipo_estudio').load({
            url     : 'informacion_academica/bd/bd.php',
            scripts : true,
            nocache : true,
            params  :
            {
                opc : 'select_informacion_tipo_estudio',
                tipo : tipo,
                id_empleado : '<?php echo $id_empleado ?>'
            }
        });

        new Ext.form.DateField({
            format     : 'Y-m-d',
            width      : 130,
            allowBlank : false,
            showToday  : false,
            applyTo    : 'Informacion_academica_fecha_inicio',
            editable   : false,
            listeners  : { select: function() {   } }
        });

        new Ext.form.DateField({
            format     : 'Y-m-d',
            width      : 130,
            allowBlank : false,
            showToday  : false,
            applyTo    : 'Informacion_academica_fecha_fin',
            editable   : false,
            listeners  : { select: function() {   } }
        });

    </script>

<?php
}

if(!isset($opcion)){ ?>

    <script>
        document.getElementById('ContenedorPrincipal_Informacion_academica').style.marginTop = 20;
    </script>

<?php
} ?>


