<?php
	include("../../../../configuracion/conectar.php");
	include("../../../../configuracion/define_variables.php");
	include("../../../../misc/MyGrilla/class.MyGrilla.php");

	$filtro_empresa  = $_SESSION['EMPRESA'];

	$where=" AND ( id=0";
	$sql="SELECT id_planilla FROM nomina_planillas_empleados_contabilizacion WHERE activo=1 AND id_empresa=$filtro_empresa AND total_sin_abono_provision>0 AND id_concepto=$id_concepto GROUP BY id_planilla";
	$query=mysql_query($sql,$link);
	while ($row=mysql_fetch_array($query)) {
		$where.=" OR id=".$row['id_planilla'];
	}
	$where.=' )';
	/**//////////////////////////////////////////////**/
	/**///		 INICIALIZACION DE LA CLASE  	  ///**/
	/**/											/**/
	/**/	$grilla = new MyGrilla();				/**/
	/**/											/**/
	/**//////////////////////////////////////////////**/

	$where.=" AND (fecha_inicio>=$fecha_inicio OR fecha_final<=$fecha_final)";
	//CONFIGURACION//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

		//NOMBRE DE LA GRILLA
			$grilla->GrillaName	 		= 'nomina_planillas';  	//NOMBRE DE LA GRILLA (DEBE SER UNICO POR CADA GRILLA DE LA APLICACION)
			$grilla->TableName			= 'nomina_planillas';			//NOMBRE DE LA TABLA EN LA BASE DE DATOS
			$grilla->MyWhere			= "activo = 1 AND estado=1 AND id_empresa='$filtro_empresa' $where";

			$grilla->OrderBy			= 'id DESC';			//LIMITE DE LA CONSULTA
			$grilla->MySqlLimit			= '0,100';			//LIMITE DE LA CONSULTA
		//TAMANO DE LA GRILLA
			$grilla->AutoResize 		= 'true';
			// $grilla->Ancho		 	    = $CualAncho;				//ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'
			// $grilla->Alto		 		= $CualAlto;				//ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'
			$grilla->QuitarAncho		= 150;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'true'
			$grilla->QuitarAlto			= 190;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'true'
		//TOOLBAR Y CAMPO DE BUSQUEDA Y FILTROS
			$grilla->Gtoolbar			= 'true';			//SI LA GRILLA LLEVA EL TOOLBAR DE BUSQUEDA
			$grilla->DivActualiBusqueda = '' ;				//VARIABLE QUE DEFINE LA CAPA DONDE SE ACTUALIZA LA GRILLA DESPUES DE UNA BUSQUEDA
			$grilla->Gfilters			= 'false';
			$grilla->GfiltersAutoOpen	= 'false';
	 		$grilla->AddFilter('Estado de la Factura','estado','estado');

		//CONFIGURACION DE CAMPOS EN LA GRILLA


			$grilla->CamposBusqueda		= 'usuario,consecutivo,sucursal';

			// $grilla->AddRowImage('Estado','<center><img src="img/estado_doc/[estado].png" style="cursor:pointer" width="16" height="16" id="imgEstadoFacturaCompra_[id]" /></center><div id="id_tercero_[id]" style="display:none;">[id_cliente]</div><div id="cuenta_pago_[id]" style="display:none;" >[cuenta_pago]</div><div id="total_factura_sin_abono_[id]" style="display:none;" >[total_factura_sin_abono]</div>','40');
			$grilla->AddRow('Consecutivo','consecutivo',80);
			$grilla->AddRow('Fecha Inicio','fecha_inicio',80);
			$grilla->AddRow('Fecha Final','fecha_final',80);
			$grilla->AddRow('Generado','fecha_generacion',80);
			$grilla->AddRow('Usuario','usuario',150);
			$grilla->AddRow('Sucursal','sucursal',150);
			// $grilla->AddRow('Saldo','total_factura_sin_abono',100);
			// $grilla->AddRow('Fecha','fecha_inicio',250,'fecha');


		//CONFIGURACION FORMULARIO
			$grilla->FContenedorAncho		= 760;
			$grilla->FColumnaGeneralAncho	= 380;
			$grilla->FColumnaGeneralAlto	= 25;
			$grilla->FColumnaLabelAncho		= 130;
			$grilla->FColumnaFieldAncho		= 150;

		//CONFIGURACION DE LA VENTANA DE INSERT, UPDATE Y DELETE
			$grilla->VentanaAuto		= 'false';			//SI LA VENTANA DE INSERT, UPDATE Y DELETE ES AUTOMATICA O MANUAL
			$grilla->TituloVentana		= 'Ventana Reuniones Coope'; //NOMBRE DE LA VENTANA DE INSER, UPDATE Y DELETE
			$grilla->VBarraBotones		= 'false';			//SI HAY O NO BARRA DE BOTONES
			$grilla->VBotonNuevo		= 'false';			//SI LLEVA EL BOTON DE AGREGAR REGISTRO
			$grilla->VBotonNText		= 'Nueva Reunion'; //TEXTO DEL BOTON DE NUEVO REGISTRO
			$grilla->VBotonNImage		= 'addcontactos';	//IMAGEN CSS DEL BOTON
			$grilla->VAutoResize		= 'true';			//SI LA VENTANA ES AUTORESIZABLE (LIQUIDA) -> "true" SI NO -> "false"
			$grilla->VAncho		 		= 400;				//ANCHO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'false'
			$grilla->VAlto		 		= 200;				//ALTO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'false'
			$grilla->VQuitarAncho		= 70;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ANCHO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'true'
			$grilla->VQuitarAlto		= 160;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ALTO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'true'
			$grilla->VAutoScroll		= 'true';			//SI LA VENTANA TIENE O NO AUTOSCROLL
			$grilla->VBotonEliminar		= 'true';			//SI MUESTRA BOTON DE ELIMINAR EN LA VENTANA DE EDICION
			$grilla->VComporEliminar	= 'true';			//COMPORTAMIENTO DEL BOTON DE ELIMINAR ("true" ES CAMPO ACTIVO DE 1 A 0) ("false" -> ELIMINA EL REGISTRO DE LA BASE DE DATOS)

 		//CONFIGURACION DEL MENU CONTEXTUAL
 			// $grilla->MenuContext		= 'true';		//MENU CONTEXTUAL
	 		// $grilla->MenuContextEliminar= 'true';

		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


		/**//////////////////////////////////////////////////////////////**/
		/**///				INICIALIZACION DE LA GRILLA	  			  ///**/
		/**/															/**/
		/**/	$grilla->Link = $link;  	//Conexion a la BD			/**/
		/**/	$grilla->inicializa($_POST);//variables POST			/**/
		/**/	$grilla->GeneraGrilla(); 	// Inicializa la Grilla		/**/
		/**/															/**/
		/**//////////////////////////////////////////////////////////////**/

if(!isset($opcion)){  ?>
	<script>

		function Editar_nomina_planillas(id){

			var myalto  = Ext.getBody().getHeight();
			var myancho = Ext.getBody().getWidth();
			var consecutivo = document.getElementById('div_nomina_planillas_consecutivo_'+id).innerHTML;

			Win_Ventana_cuentas_planilla = new Ext.Window({
			    width       : myancho-100,
			    height      : myalto-50,
			    id          : 'Win_Ventana_cuentas_planilla',
			    title       : 'Cuentas provisionadas de la planilla de nomina N.'+consecutivo,
			    modal       : true,
			    autoScroll  : false,
			    closable    : false,
			    autoDestroy : true,
			    autoLoad    :
			    {
			        url     : 'liquidacion_provision/bd/grillaCuentasPlanillaNomina.php',
			        scripts : true,
			        nocache : true,
			        params  :
			        {
						id_planilla       : id,
						consecutivo       : consecutivo,
						opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
						cont              : '<?php echo $cont; ?>',
						id_nota           : '<?php echo $id_nota; ?>',
						cont              : '<?php echo $cont; ?>',
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
			                    handler     : function(){ BloqBtn(this); Win_Ventana_cuentas_planilla.close(id) }
			                }
			            ]
			        }
			    ]
			}).show();

			// (cuenta_pago=document.getElementById('cuenta_pago_'+id).innerHTML)*1;
			// total_factura_sin_abono = document.getElementById('total_factura_sin_abono_'+id).innerHTML;

			// // arrayCuentaPago[<?php echo $cont; ?>]=cuenta_pago;

			// arrayTemp= new Array();

			// arrayTemp[cuenta_pago]=total_factura_sin_abono;

			// arraySaldoCuentaPago[<?php echo $cont; ?>]=arrayTemp[cuenta_pago];

			// if(document.getElementById('idInsertCuenta<?php echo $opcGrillaContable; ?>_<?php echo $cont; ?>').value > 0){
   //      	    document.getElementById('divImageDeshacer<?php echo $opcGrillaContable; ?>_<?php echo $cont; ?>').style.display = 'block';
   //      	    document.getElementById("divImageSave<?php echo $opcGrillaContable; ?>_<?php echo $cont; ?>").style.display     = 'inline';
   //      	}

			// prefijo_factura = '';
			// numero_factura  = '';
			// tercero         = '';

   //      	if ('<?php echo $tipo_documento_cruce; ?>'=='FC') {
			// 	prefijo_factura = document.getElementById('div_<?php echo $opcGrillaContable; ?>_prefijo_factura_'+id).innerHTML
			// 	tercero         = document.getElementById('div_<?php echo $opcGrillaContable; ?>_proveedor_'+id).innerHTML
   //      	}
   //      	else if ('<?php echo $tipo_documento_cruce; ?>'=='FV') {
   //      		prefijo_factura = document.getElementById('div_<?php echo $opcGrillaContable; ?>_prefijo_'+id).innerHTML
			// 	tercero         = document.getElementById('div_<?php echo $opcGrillaContable; ?>_cliente_'+id).innerHTML
   //      	}

			// document.getElementById('idDocumentoCruce<?php echo $opcGrillaContable; ?>_<?php echo $cont; ?>').value=id;
			// document.getElementById('documentoCruce<?php echo $opcGrillaContable; ?>_<?php echo $cont; ?>').value='<?php echo $tipo_documento_cruce; ?>';
			// document.getElementById('prefijoDocumentoCruce<?php echo $opcGrillaContable; ?>_<?php echo $cont; ?>').value = prefijo_factura;
			// document.getElementById('numeroDocumentoCruce<?php echo $opcGrillaContable; ?>_<?php echo $cont; ?>').value  = document.getElementById('div_<?php echo $opcGrillaContable; ?>_numero_factura_'+id).innerHTML;
			// document.getElementById('debito<?php echo $opcGrillaContable; ?>_<?php echo $cont; ?>').focus();

			// if (document.getElementById('numeroDocumentoCruce<?php echo $opcGrillaContable; ?>_<?php echo $cont; ?>').value != '' ) {
			// 	//CAMBIAR LOS ATRIBUTOS DE LA IMAGEN PARA QUE ELIMINE UN TERCERO
   //      		document.getElementById('imgBuscarDocumentoCruce_<?php echo $cont; ?>').setAttribute('src','img/eliminar.png');
   //      		document.getElementById('imgBuscarDocumentoCruce_<?php echo $cont; ?>').setAttribute('title','Eliminar Documento Cruce');
   //      		document.getElementById('imgBuscarDocumentoCruce_<?php echo $cont; ?>').setAttribute('onclick'," eliminaDocumentoCruce<?php echo $opcGrillaContable; ?>('<?php echo $cont; ?>')");
			// }

			// id_tercero=document.getElementById('id_tercero_'+id).innerHTML;

			// document.getElementById('tercero<?php echo $opcGrillaContable; ?>_<?php echo $cont; ?>').value   = tercero;
			// document.getElementById('idTercero<?php echo $opcGrillaContable; ?>_<?php echo $cont; ?>').value = id_tercero;
			// //CAMBIAR LOS ATRIBUTOS DE LA IMAGEN PARA QUE ELIMINE UN TERCERO
   //  		document.getElementById('imgBuscarTercero<?php echo $opcGrillaContable; ?>_<?php echo $cont; ?>').setAttribute('src','img/eliminar.png');
   //  		document.getElementById('imgBuscarTercero<?php echo $opcGrillaContable; ?>_<?php echo $cont; ?>').setAttribute('title','Eliminar Tercero');
   //  		document.getElementById('imgBuscarTercero<?php echo $opcGrillaContable; ?>_<?php echo $cont; ?>').setAttribute('onclick'," eliminaTercero<?php echo $opcGrillaContable; ?>(<?php echo $cont; ?>)");

			// Win_Ventana_buscar_documento_cruce<?php echo $opcGrillaContable; ?>.close();
		}

	</script>

<?php
} ?>

