<?php
	include("../../../configuracion/conectar.php");
	include("../../../configuracion/define_variables.php");
    include("../../../misc/MyGrilla/class.MyGrilla.php");

    //INICIALIZACION DE LA CLASE
        $grilla = new MyGrilla();


    //CONFIGURACION//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

        //NOMBRE DE LA GRILLA
            $grilla->GrillaName         = 'Idiomas_empleado';      //NOMBRE DE LA GRILLA (DEBE SER UNICO POR CADA GRILLA DE LA APLICACION)
        //QUERY
            $grilla->TableName          = 'empleados_idiomas';      //NOMBRE DE LA TABLA EN LA BASE DE DATOS

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
            $grilla->AddRow('Idioma','idioma',150);
            $grilla->AddRow('Lee','lectura',150);
            $grilla->AddRow('Escribe','escritura',150);
            $grilla->AddRow('Habla','habla',150);
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
            $grilla->TituloVentana      = 'Idiomas'; //NOMBRE DE LA VENTANA DE INSER, UPDATE Y DELETE
            $grilla->VBarraBotones      = 'true';          //SI HAY O NO BARRA DE BOTONES
            $grilla->VBotonNuevo        = 'true';          //SI LLEVA EL BOTON DE AGREGAR REGISTRO
            $grilla->VBotonNText        = 'Agregar Idiomas'; //TEXTO DEL BOTON DE NUEVO REGISTRO
            $grilla->VBotonNImage       = 'documentadd';            //IMAGEN CSS DEL BOTON
            //$grilla->AddBotton('Nuevo','documentadd','BloqBtn(this); cargaDocumentoEmpleado();');
            $grilla->VAutoResize        = 'true';           //SI LA VENTANA ES AUTORESIZABLE (LIQUIDA) -> "true" SI NO -> "false"
            $grilla->VAncho             = 330;              //ANCHO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'false'
            $grilla->VAlto              = 260;              //ALTO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'false'
            $grilla->VQuitarAncho       = 70;               //AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ANCHO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'true'
            $grilla->VQuitarAlto        = 50;              //AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ALTO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'true'
            $grilla->VAutoScroll        = 'false';          //SI LA VENTANA TIENE O NO AUTOSCROLL
            $grilla->VBotonEliminar     = 'true';           //SI MUESTRA BOTON DE ELIMINAR EN LA VENTANA DE EDICION
            $grilla->VComporEliminar    = 'true';           //COMPORTAMIENTO DEL BOTON DE ELIMINAR ("true" ES CAMPO ACTIVO DE 1 A 0) ("false" -> ELIMINA EL REGISTRO DE LA BASE DE DATOS)

            //ARRAY EN MODO BD -> 'NOMBRE DE LA TABLA, NOMBRE DEL CAMPO INDEX, NOMBRE DEL CAMPO TEXTO ,SI MUESTRA SOLO LOS ACTIVOS(boolenao)'
            //ARRAY EN MODO MANUAL ' INDEX : TEXTO , INDEX : TEXTO, INDEX : TEXTO, INDEX : TEXTO '
            //$label,$field,$largo,$obligatorio,$bd='true',$array='',$where=''
            $grilla->AddTextField('Empleado','id_empleado',160,'true','true',"$id_empleado");
            $grilla->AddComboBox('Idioma','idioma',160,'true','false','Aleman:Aleman,Chino:Chino,Español:Espa&ntilde;ol,Frances:Frances,Holandes:Holandes,Ingles:Ingles,Italiano:Italiano,Japones:Japones,Mandarin:Mandarin,Portugues:Portugues');//estatico
            $grilla->AddComboBox('Nativo','nativo',160,'true','false','si:Si,no:No');
            $grilla->AddTextField('Institucion','institucion',160,'true');
            $grilla->AddTextField('Ciudad','ciudad',160,'true');
            $grilla->AddComboBox('Lectura','lectura',160,'true','false','si:Si,no:No');
            $grilla->AddComboBox('Escritura','escritura',160,'true','false','si:Si,no:No');
            $grilla->AddComboBox('Habla','habla',160,'true','false','si:Si,no:No');




            // $grilla->AddTextField('Titulo Obtenido','grado',160,'true');
            // $grilla->AddTextField('Institucion','institucion',160,'true');
            // $grilla->AddTextField('Empleado','id_empleado',160,'true','true',"$id_empleado");
            // //$grilla->AddComboBox('Tipo Identificacion','id_tipo_identificacion',160,'false','true','tipo_documento,id,detalle','activo=1');//dinamico
            // $grilla->AddTextField('Fecha de Inicio','fecha_inicio',160,'false');
            // $grilla->AddTextField('Fecha de Terminacion','fecha_fin',160,'false');
            // $grilla->AddComboBox('Ciclo','ciclo',160,'false','false','Diurno:Diurno,Nocturno:Nocturno');
            // //$grilla->AddTextField('Modalidad Presencial','modalidad_presencial',160,'false');
            // $grilla->AddComboBox('Modalidad','modalidad_presencial',160,'true','false','Presencial:Presencial,Online:Online,Otro:Otro');
            // $grilla->AddTextField('Describa modalidad','otra_modalidad',160,'true');//modalidad
            // $grilla->AddTextField('Tarjeta Profesional','tarjeta_profesional',160,'false');
            // $grilla->AddTextField('Ciudad','ciudad',160,'true');


    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


    //              INICIALIZACION DE LA GRILLA

        $grilla->Link = $link;      //Conexion a la BD
        $grilla->inicializa($_POST);//variables POST
        $grilla->GeneraGrilla();    // Inicializa la Grilla

    /////////////////////////////////////////////////////////
 if($opcion == 'Vupdate' || $opcion == 'Vagregar'){

    if($opcion == 'Vupdate'){
        $scriptNo = 'Win_Editar_Idiomas_empleado.setHeight(245);';
        $scriptSi = 'Win_Editar_Idiomas_empleado.setHeight(300);';
    }
    else if($opcion == 'Vagregar'){
        $scriptNo = 'Win_Agregar_Idiomas_empleado.setHeight(245);';
        $scriptSi = 'Win_Agregar_Idiomas_empleado.setHeight(300);';
    }
?>

    <script>

        document.getElementById('Idiomas_empleado_nativo').setAttribute("onchange","cambia()");

        function cambia(){

            var es_nativo=document.getElementById('Idiomas_empleado_nativo').value;
            if(es_nativo == 'si'){
                document.getElementById('EmpConte_Idiomas_empleado_institucion').style.display = "none";
                document.getElementById('EmpConte_Idiomas_empleado_ciudad').style.display      = "none";

                document.getElementById('Idiomas_empleado_institucion').value = "nativo";
                document.getElementById('Idiomas_empleado_ciudad').value      = "nativo";
                <?php echo $scriptNo; ?>

            }
            else{
                document.getElementById('EmpConte_Idiomas_empleado_institucion').style.display = "inline";
                document.getElementById('EmpConte_Idiomas_empleado_ciudad').style.display      = "inline";

                document.getElementById('Idiomas_empleado_institucion').value = "";
                document.getElementById('Idiomas_empleado_ciudad').value      = "";
                <?php echo $scriptSi; ?>
            }

        }

        cambia();

    </script>

<?php
}

if(!isset($opcion)){ ?>

    <script>
        document.getElementById('ContenedorPrincipal_Informacion_academica').style.marginTop = 20;
    </script>

<?php
}
 ?>