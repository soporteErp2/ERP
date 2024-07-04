
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
            $grilla->GrillaName         = 'ventas_pos_configuracion';      //NOMBRE DE LA GRILLA (DEBE SER UNICO POR CADA GRILLA DE LA APLICACION)
        //QUERY
            $grilla->TableName          = 'ventas_pos_configuracion';      //NOMBRE DE LA TABLA EN LA BASE DE DATOS
            $grilla->MyWhere            = "activo=1 AND id_empresa=$id_empresa AND id_sucursal=$id_sucursal";     //WHERE DE LA CONSULTA A LA TABLA ""
            $grilla->OrderBy            = 'id ASC';           //LIMITE DE LA CONSULTA
            $grilla->MySqlLimit         = '0,50';           //LIMITE DE LA CONSULTA
        //TAMANO DE LA GRILLA
            // $grilla->AutoResize         = 'true';           //SI LA GRILLA ES AUTORESIZABLE (LIQUIDA) -> "true" SI NO -> "false"
            $grilla->Ancho              = 895;              //ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'
            $grilla->Alto               = 330;              //ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'
            // $grilla->QuitarAncho        = 510;               //AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'true'
            // $grilla->QuitarAlto         = 550;              //AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'true'
        //TOOLBAR Y CAMPO DE BUSQUEDA
            $grilla->Gtoolbar           = 'true';           //SI LA GRILLA LLEVA EL TOOLBAR DE BUSQUEDA
            $grilla->CamposBusqueda     = 'numero_resolucion_dian,fecha_resolucion_dian,numero_inicial,numero_final,documento_tercero,tercero';       //VARIABLE QUE DEFINE LOS CAMPOS DE LA BD DONDE SE BUSCARA
            $grilla->DivActualiBusqueda = '' ;              //VARIABLE QUE DEFINE LA CAPA DONDE SE ACTUALIZA LA GRILLA DESPUES DE UNA BUSQUEDA
        //CONFIGURACION DE CAMPOS EN LA GRILLA
            $grilla->AddRow('Resolucion','numero_resolucion_dian',70);
            $grilla->AddRow('Fecha','fecha_resolucion_dian',70);
            $grilla->AddRow('Prefijo','prefijo',60);
            $grilla->AddRow('N. Inicial','numero_inicial',50);
            $grilla->AddRow('N. Final','numero_final',50);
            $grilla->AddRow('Cons. por caja','cantidad_consecutivos',80);
            $grilla->AddRow('Cuenta','cuenta_por_cobrar_colgaap',80);
            $grilla->AddRow('Documento','documento_tercero',70);
            $grilla->AddRow('Tercero','tercero',200);
            $grilla->AddRowImage('Estado','<center><img src="img/[estado].png" onerror="src=\'img/true.png\'"></center>',40);

            // $grilla->AddColStyle('campoBd','text-align:right; width:95px !important; margin-right:5px;');   //ALINEAR NUMEROS LA DERECHA

        //CONFIGURACION FORMULARIO
            $grilla->FContenedorAncho       = 310;
            $grilla->FColumnaGeneralAncho   = 350;
            $grilla->FColumnaGeneralAlto    = 25;
            $grilla->FColumnaLabelAncho     = 130;
            $grilla->FColumnaFieldAncho     = 200;

        //CONFIGURACION DE LA VENTANA DE INSERT, UPDATE Y DELETE
            $grilla->VentanaAuto        = 'false';          //SI LA VENTANA DE INSERT, UPDATE Y DELETE ES AUTOMATICA O MANUAL
            $grilla->TituloVentana      = 'false'; //NOMBRE DE LA VENTANA DE INSER, UPDATE Y DELETE
            $grilla->VBarraBotones      = 'true';          //SI HAY O NO BARRA DE BOTONES
            $grilla->VBotonNuevo        = 'false';          //SI LLEVA EL BOTON DE AGREGAR REGISTRO
            $grilla->VBotonNText        = 'Nuevo'; //TEXTO DEL BOTON DE NUEVO REGISTRO
            $grilla->VBotonNImage       = 'documentadd';            //IMAGEN CSS DEL BOTON
            $grilla->AddBotton('Nuevo','documentadd',' Insertar_ventas_pos_configuracion();');

            $grilla->VAutoResize        = 'true';           //SI LA VENTANA ES AUTORESIZABLE (LIQUIDA) -> "true" SI NO -> "false"
            $grilla->VAncho             = 350;              //ANCHO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'false'
            $grilla->VAlto              = 430;              //ALTO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'false'
            $grilla->VQuitarAncho       = 200;               //AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ANCHO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'true'
            $grilla->VQuitarAlto        = 150;              //AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ALTO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'true'
            $grilla->VAutoScroll        = 'false';          //SI LA VENTANA TIENE O NO AUTOSCROLL
            $grilla->VBotonEliminar     = 'true';           //SI MUESTRA BOTON DE ELIMINAR EN LA VENTANA DE EDICION
            $grilla->VComporEliminar    = 'true';           //COMPORTAMIENTO DEL BOTON DE ELIMINAR ("true" ES CAMPO ACTIVO DE 1 A 0) ("false" -> ELIMINA EL REGISTRO DE LA BASE DE DATOS)

        //CONFIGURACION DE LO FORMULARIOS DE CAPTURA Y EDICION
			$grilla->AddSeparator('Configuraci&oacute;n Contable');
				$grilla->AddTextField('Cuenta de Pago','cuenta_por_cobrar_colgaap',150,'true','false');
				$grilla->AddTextField('Tercero','tercero',150,'true','false');

			$grilla->AddSeparator('Configuraci&oacute;n Tributaria');
				$grilla->AddTextField('N. Resolucion','numero_resolucion_dian',150,'true','false');
				$grilla->AddTextField('Fecha','fecha',150,'true','false');
				$grilla->AddTextField('Prefijo','prefijo',150,'true','false');
				$grilla->AddTextField('N. Inicial','fecha',150,'true','false');
				$grilla->AddTextField('N. Final','fecha',150,'true','false');

			$grilla->AddSeparator('Configuraci&oacute;n Caja');
				$grilla->AddTextField('Consecutivos por caja','cantidad_consecutivos',150,'true','false');

			// $grilla->AddSeparator('Configuraci&oacute;n Contable');
//
			// $grilla->AddTextField('Nombre','retencion',150,'true','false');
			// $grilla->AddTextField('% Valor','valor',150,'true','false');
			// $grilla->AddTextField('Base','base',150,'true','false');
			// $grilla->AddComboBox('Modulo','modulo',150,'true','false','Compra:Compra,Venta:Venta');
			// $grilla->AddComboBox('Tipo Retencion','tipo_retencion',150,'true','false','ReteFuente:Retencion en la fuente,ReteIva:Retencion Iva,ReteIca:Retencion ICA,AutoRetencion:AutoRetencion');
			// $grilla->AddComboBox('Carga Automatica','factura_auto',150,'true','false','true:Si,false:No');
			// $grilla->AddComboBox('Departamento','id_departamento',150,'false','true','ubicacion_departamento,id,departamento,true','activo=1 AND id_pais='.$id_pais);
			// $grilla->AddComboBox('Ciudad','id_ciudad',150,'false','true','0:Problema al Cargar la base de datos');
//
			// $grilla->AddSeparator('Contabilidad');
			// $grilla->AddTextField('Cuenta Colgaap','cuenta',150,'true','false');
			// $grilla->AddTextField('Cuenta Niif','cuenta_niif',150,'true','false');
			// $grilla->AddTextField('Cuenta AutoRetencion','cuenta_autoretencion',150,'true','false');
			// $grilla->AddTextField('Cuenta AutoRetencion Niif','cuenta_autoretencion_niif',150,'true','false');
//
			// $grilla->AddTextField('','id_empresa',150,'true','true',$id_empresa);
			// $grilla->AddTextField('','grupo_empresarial',150,'true','true',$grupo_empresa);

		//VALIDACIONES
			// $grilla->AddValidation('retencion','mayuscula');
			// $grilla->AddValidation('valor','numero-real');
			// $grilla->AddValidation('base','numero-real');
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


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
	    	function Insertar_ventas_pos_configuracion() {
	    		ventana_insert_update('');
	    	}

	    	function Editar_ventas_pos_configuracion(id) {
	    		ventana_insert_update(id)
	    	}

	    	function ventana_insert_update(id) {
	    		var title = (id>0)? 'Actualizar Registro' : 'Guardar Registro' ;
	    		var opc = (id>0)? 'update' : 'insert' ;

	    		Win_Ventana_insert_update = new Ext.Window({
	    		    width       : 350,
	    		    height      : 470,
	    		    id          : 'Win_Ventana_insert_update',
	    		    title       : title,
	    		    modal       : true,
	    		    autoScroll  : false,
	    		    closable    : true,
	    		    autoDestroy : true,
	    		    autoLoad    :
	    		    {
	    		        url     : 'configuracion_pos/pos_insert_update.php',
	    		        scripts : true,
	    		        nocache : true,
	    		        params  :
	    		        {
							opc             : opc,
							filtro_sucursal : '<?php echo $filtro_sucursal; ?>',
							id              : id,
	    		        }
	    		    }

	    		}).show();
	    	}

	    </script>
	<?php
	}

?>