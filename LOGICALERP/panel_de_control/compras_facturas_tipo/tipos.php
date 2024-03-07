
<?php
    include("../../../configuracion/conectar.php");
    include("../../../configuracion/define_variables.php");
    include("../../../misc/MyGrilla/class.MyGrilla.php");

    /**//////////////////////////////////////////////**/
    /**///       INICIALIZACION DE LA CLASE       ///**/
    /**/                                            /**/
    /**/    $grilla = new MyGrilla();              /**/
    /**/                                            /**/
    /**//////////////////////////////////////////////**/

    $id_empresa  = $_SESSION['EMPRESA'];
	$id_sucursal = $filtro_sucursal;

    //CONFIGURACION//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

        //NOMBRE DE LA GRILLA
            $grilla->GrillaName         = 'tipos_facturas';      //NOMBRE DE LA GRILLA (DEBE SER UNICO POR CADA GRILLA DE LA APLICACION)
        //QUERY
            $grilla->TableName          = 'compras_facturas_tipos';      //NOMBRE DE LA TABLA EN LA BASE DE DATOS
            $grilla->MyWhere            = "activo=1 AND id_empresa=$id_empresa";     //WHERE DE LA CONSULTA A LA TABLA ""
            $grilla->OrderBy            = 'id ASC';           //LIMITE DE LA CONSULTA
            $grilla->MySqlLimit         = '0,50';           //LIMITE DE LA CONSULTA
        //TAMANO DE LA GRILLA
            $grilla->Ancho              = 320;              //ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'
            $grilla->Alto               = 220;              //ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'
        //TOOLBAR Y CAMPO DE BUSQUEDA
            $grilla->Gtoolbar           = 'true';           //SI LA GRILLA LLEVA EL TOOLBAR DE BUSQUEDA
            $grilla->CamposBusqueda     = 'nombre';       //VARIABLE QUE DEFINE LOS CAMPOS DE LA BD DONDE SE BUSCARA
            $grilla->DivActualiBusqueda = '' ;              //VARIABLE QUE DEFINE LA CAPA DONDE SE ACTUALIZA LA GRILLA DESPUES DE UNA BUSQUEDA
        //CONFIGURACION DE CAMPOS EN LA GRILLA
            $grilla->AddRow('Nombre','nombre',200);

        //CONFIGURACION FORMULARIO
            $grilla->FContenedorAncho       = 310;
            $grilla->FColumnaGeneralAncho   = 350;
            $grilla->FColumnaGeneralAlto    = 25;
            $grilla->FColumnaLabelAncho     = 70;
            $grilla->FColumnaFieldAncho     = 200;

        //CONFIGURACION DE LA VENTANA DE INSERT, UPDATE Y DELETE
            $grilla->VentanaAuto        = 'true';          //SI LA VENTANA DE INSERT, UPDATE Y DELETE ES AUTOMATICA O MANUAL
            $grilla->TituloVentana      = 'Tipo'; //NOMBRE DE LA VENTANA DE INSER, UPDATE Y DELETE
            $grilla->VBarraBotones      = 'true';          //SI HAY O NO BARRA DE BOTONES
            $grilla->VBotonNuevo        = 'true';          //SI LLEVA EL BOTON DE AGREGAR REGISTRO
            $grilla->VBotonNText        = 'Nuevo'; //TEXTO DEL BOTON DE NUEVO REGISTRO
            $grilla->VBotonNImage       = 'documentadd';            //IMAGEN CSS DEL BOTON
            // $grilla->AddBotton('Nuevo','documentadd',' Insertar_ventas_pos_configuracion();');

            $grilla->VAutoResize        = 'false';           //SI LA VENTANA ES AUTORESIZABLE (LIQUIDA) -> "true" SI NO -> "false"
            $grilla->VAncho             = 280;              //ANCHO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'false'
            $grilla->VAlto             	= 150;              //ANCHO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'false'
            $grilla->VAutoScroll        = 'false';          //SI LA VENTANA TIENE O NO AUTOSCROLL
            $grilla->VBotonEliminar     = 'true';           //SI MUESTRA BOTON DE ELIMINAR EN LA VENTANA DE EDICION
            $grilla->VComporEliminar    = 'true';           //COMPORTAMIENTO DEL BOTON DE ELIMINAR ("true" ES CAMPO ACTIVO DE 1 A 0) ("false" -> ELIMINA EL REGISTRO DE LA BASE DE DATOS)

        //CONFIGURACION DE LO FORMULARIOS DE CAPTURA Y EDICION

			$grilla->AddTextField('Nombre','nombre',150,'true','false');
			$grilla->AddTextField('','id_empresa',150,'true','true',$id_empresa);

    /**////////////////////////////////////////////////////////////**/
    /**///              INICIALIZACION DE LA GRILLA             ///**/
    /**/                                                          /**/
    /**/    $grilla->Link = $link;      //Conexion a la BD        /**/
    /**/    $grilla->inicializa($_POST);//variables POST          /**/
    /**/    $grilla->GeneraGrilla();    // Inicializa la Grilla   /**/
    /**/                                                          /**/
    /**////////////////////////////////////////////////////////////**/

	 if($opcion == 'Vupdate' || $opcion == 'Vagregar'){ ?>

	    <script></script>

	<?php
	}

	if(!isset($opcion)){ ?>
	    <script>
	    </script>
	<?php
	}

?>