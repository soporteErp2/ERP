<?php
	include("../../../../configuracion/conectar.php");
	include("../../../../configuracion/define_variables.php");
	include("../../../../misc/MyGrilla/class.MyGrilla.php");

	$filtro_empresa  = $_SESSION['EMPRESA'];

	/**//////////////////////////////////////////////**/
	/**///		 INICIALIZACION DE LA CLASE  	  ///**/
	/**/											/**/
	/**/	$grilla = new MyGrilla();				/**/
	/**/											/**/
	/**//////////////////////////////////////////////**/

	// echo $filtro_sucursal;

	if ($tipo_documento_cruce == 'FC') {

		$whereTercero = "";
		if($idTercero !=  ""){
			$whereTercero = " AND id_proveedor = ".$idTercero;
		}

		$tablaBuscar    = 'compras_facturas';
		$whereDocumento = " AND estado=1 AND total_factura_sin_abono>0 AND id_empresa='$filtro_empresa' AND id_sucursal= '$filtro_sucursal' ".$whereTercero;

	}
	else{

		$whereTercero = "";
		if($idTercero !=  ""){

			$sql       = "SELECT numero_identificacion FROM terceros WHERE id = '$idTercero' AND activo = 1 AND tercero = 1";
			$query     = mysql_query($sql,$link);
			$documento = mysql_result($query,0,'numero_identificacion');

			$whereTercero = " AND id_usuario = ".$documento;
		}

		$sql     = "SELECT id_planilla FROM nomina_planillas_empleados_contabilizacion
						WHERE activo=1 AND id_empresa=$filtro_empresa AND tipo_planilla='$tipo_documento_cruce' AND total_sin_abono>0 GROUP BY id_planilla";
		$query   = mysql_query($sql,$link);
		$whereId = ' id=0 ';

		while ($row=mysql_fetch_array($query)) { $whereId.=' OR id='.$row['id_planilla']; }
		$whereDocumento = "AND (estado=1 OR estado=2) AND id_empresa='$filtro_empresa' AND id_sucursal= '$filtro_sucursal' AND ($whereId) ".$whereTercero;
	}

	if ($tipo_documento_cruce == 'LN') { $tablaBuscar    = 'nomina_planillas'; }
	else if ($tipo_documento_cruce == 'LE') { $tablaBuscar    = 'nomina_planillas_liquidacion'; }
	else if ($tipo_documento_cruce == 'PA') { $tablaBuscar    = 'nomina_planillas_ajuste';}
	else if ($tipo_documento_cruce == 'PCP') { $tablaBuscar    = 'nomina_planillas_consolidacion_provision';	}

	//CONFIGURACION//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

		//NOMBRE DE LA GRILLA
			$grilla->GrillaName	 		= $opcGrillaContable;  	//NOMBRE DE LA GRILLA (DEBE SER UNICO POR CADA GRILLA DE LA APLICACION)
		//QUERY
			$grilla->TableName			= $tablaBuscar;			//NOMBRE DE LA TABLA EN LA BASE DE DATOS

			$grilla->MyWhere			= "activo = 1 $whereDocumento ";

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

	 	if ($tipo_documento_cruce == 'FC') {
	 		$grilla->CamposBusqueda		= 'nit,proveedor,prefijo_factura,numero_factura,consecutivo';

	 		$grilla->AddRowImage('Estado','<center><img src="img/estado_doc/[estado].png" style="cursor:pointer" width="16" height="16" id="imgEstadoFacturaCompra_[id]" /></center><div id="id_tercero_[id]" style="display:none;">[id_proveedor]</div><div id="id_cuenta_[id]" style="display:none;" >[id_cuenta_pago]</div><div id="cuenta_pago_[id]" style="display:none;" >[cuenta_pago]</div><div id="total_factura_sin_abono_[id]" style="display:none;" >[total_factura_sin_abono]</div>','40');
			$grilla->AddRow('Prefijo','prefijo_factura',70);
			$grilla->AddRow('Factura','numero_factura',100);
			$grilla->AddRow('Consecutivo','consecutivo',100);
			$grilla->AddRow('Nit','nit',100);
			$grilla->AddRow('Proveedor','proveedor',200);
			$grilla->AddRow('Saldo','total_factura_sin_abono',100);
			$grilla->AddRow('Fecha','fecha_inicio',160,'fecha');

			$grilla->AddColStyle('total_factura_sin_abono','text-align:right; width:95px !important; margin-right:5px;');   //ALINEAR NUMEROS LA DERECHA
			$grilla->AddColStyle('numero_factura','text-align:right; width:95px !important; margin-right:5px;');   //ALINEAR NUMEROS LA DERECHA
			$grilla->AddColStyle('nit','text-align:right; width:95px !important; margin-right:5px;');   //ALINEAR NUMEROS LA DERECHA

		}
		else{
		// else  if ($tipo_documento_cruce == 'LN') {
			$grilla->CamposBusqueda		= 'fecha_inicio,fecha_final,consecutivo';

			$grilla->AddRowImage('Estado','<center><img src="img/estado_doc/[estado].png" style="cursor:pointer" width="16" height="16" id="imgEstadoFacturaCompra_[id]" /></center><div id="id_tercero_[id]" style="display:none;">[id_cliente]</div><div id="cuenta_pago_[id]" style="display:none;" >[cuenta_pago]</div><div id="total_factura_sin_abono_[id]" style="display:none;" >[total_factura_sin_abono]</div>','40');
			$grilla->AddRow('Consecutivo','consecutivo',80);
			$grilla->AddRow('Fecha Inicio','fecha_inicio',100);
			$grilla->AddRow('Fecha Final','fecha_final',100);
			$grilla->AddRow('Usuario','usuario',200);
			$grilla->AddRow('Sucursal','sucursal',120);
		}

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

		// SI YA SE INERTO EL DOCUMENTO EN LA FILA, SE ASGINA CONT A LA ULTIMA FILA DE LA GRILLA
		if (document.getElementById('idInsertCuenta<?php echo $opcGrillaContable; ?>_<?php echo $cont;?>').value>0) {cont=contArticulos<?php echo $opcGrillaContable; ?>;}
		// SINO SE HA INSERTADO AUN UN REGISTRO, ENTONCES CORRESPONDE A LA FILA DONDE SE HIZO CLICK
		else{cont = <?php echo $cont; ?>;}
		
		function Editar_<?php echo $opcGrillaContable; ?>(id){
			var total_sin_abono=0;
			// if ('<?php echo $tipo_documento_cruce ?>'=='LN') {
			// 	ventanaCuentasPlanillaNomina(id);
			// 	return;
			// }

			if(document.getElementById('idInsertCuenta<?php echo $opcGrillaContable; ?>_'+cont).value > 0){
        	    document.getElementById('divImageDeshacer<?php echo $opcGrillaContable; ?>_'+cont).style.display = 'block';
        	    document.getElementById("divImageSave<?php echo $opcGrillaContable; ?>_<?php echo $cont; ?>").style.display     = 'inline';
        	}

			if ('<?php echo $tipo_documento_cruce ?>'=='LE' || '<?php echo $tipo_documento_cruce ?>'=='PA' || '<?php echo $tipo_documento_cruce ?>'=='LN' || '<?php echo $tipo_documento_cruce ?>'=='PCP') {
				ventanaCuentasPlanillas(id);
				return;
			}
			else{

				prefijo_factura = document.getElementById('div_<?php echo $opcGrillaContable; ?>_prefijo_factura_'+id).innerHTML;
				numero_factura  = document.getElementById('div_<?php echo $opcGrillaContable; ?>_numero_factura_'+id).innerHTML;
				total_sin_abono = document.getElementById('total_factura_sin_abono_'+id).innerHTML;
				tercero         = document.getElementById('div_<?php echo $opcGrillaContable; ?>_proveedor_'+id).innerHTML;
			}

			id_cuenta  = document.getElementById('id_cuenta_'+id).innerHTML;
			cuenta     = document.getElementById('cuenta_pago_'+id).innerHTML;
			id_tercero = document.getElementById('id_tercero_'+id).innerHTML;

			document.getElementById('idCuenta<?php echo $opcGrillaContable; ?>_'+cont).value = id_cuenta;
			document.getElementById('cuenta<?php echo $opcGrillaContable; ?>_'+cont).value   = cuenta;

			document.getElementById('idDocumentoCruce<?php echo $opcGrillaContable; ?>_'+cont).value=id;
			document.getElementById('documentoCruce<?php echo $opcGrillaContable; ?>_'+cont).value='<?php echo $tipo_documento_cruce; ?>';
			document.getElementById('prefijoDocumentoCruce<?php echo $opcGrillaContable; ?>_'+cont).value = prefijo_factura;
			document.getElementById('numeroDocumentoCruce<?php echo $opcGrillaContable; ?>_'+cont).value  = numero_factura;
			document.getElementById('debito<?php echo $opcGrillaContable; ?>_'+cont).value= total_sin_abono;
			document.getElementById('debito<?php echo $opcGrillaContable; ?>_'+cont).focus();

			if (document.getElementById('numeroDocumentoCruce<?php echo $opcGrillaContable; ?>_'+cont).value != '' ) {
				//CAMBIAR LOS ATRIBUTOS DE LA IMAGEN PARA QUE ELIMINE UN TERCERO
        		document.getElementById('imgBuscarDocumentoCruce_'+cont).setAttribute('src','img/eliminar.png');
        		document.getElementById('imgBuscarDocumentoCruce_'+cont).setAttribute('title','Eliminar Documento Cruce');
        		document.getElementById('imgBuscarDocumentoCruce_'+cont).setAttribute('onclick'," eliminaDocumentoCruce<?php echo $opcGrillaContable; ?>("+cont+")");
			}

			if (id_cliente_<?php echo $opcGrillaContable; ?>!=id_tercero) {
				document.getElementById('tercero<?php echo $opcGrillaContable; ?>_'+cont).value   = tercero;
				document.getElementById('idTercero<?php echo $opcGrillaContable; ?>_'+cont).value = id_tercero;
				//CAMBIAR LOS ATRIBUTOS DE LA IMAGEN PARA QUE ELIMINE UN TERCERO
        		document.getElementById('imgBuscarTercero_'+cont).setAttribute('src','img/eliminar.png');
        		document.getElementById('imgBuscarTercero_'+cont).setAttribute('title','Eliminar Tercero');
        		document.getElementById('imgBuscarTercero_'+cont).setAttribute('onclick'," eliminaTercero<?php echo $opcGrillaContable; ?>("+cont+")");
			}
			else{
				document.getElementById('tercero<?php echo $opcGrillaContable; ?>_'+cont).value="";
				document.getElementById('idTercero<?php echo $opcGrillaContable; ?>_'+cont).value="";
				//CAMBIAR LOS ATRIBUTOS DE LA IMAGEN PARA QUE ELIMINE UN TERCERO
        		document.getElementById('imgBuscarTercero_'+cont).setAttribute('src','img/buscar20.png');
        		document.getElementById('imgBuscarTercero_'+cont).setAttribute('title','Buscar Tercero');
        		document.getElementById('imgBuscarTercero_'+cont).setAttribute('onclick',"buscarVentanaTercero<?php echo $opcGrillaContable; ?>("+cont+")");
			}
			guardarNewCuenta<?php echo $opcGrillaContable; ?>(cont);
			cont=contArticulos<?php echo $opcGrillaContable; ?>;

			//Win_Ventana_buscar_documento_cruce<?php echo $opcGrillaContable; ?>.close();
		}

		// function ventanaCuentasPlanillaNomina(id) {
		// 	var myalto      = Ext.getBody().getHeight();
		// 	var myancho     = Ext.getBody().getWidth();
		// 	var consecutivo = document.getElementById('div_ComprobanteEgreso_consecutivo_'+id).innerHTML;

		// 	Win_Ventana_cuentas_planilla = new Ext.Window({
		// 	    width       : myancho-100,
		// 	    height      : myalto-50,
		// 	    id          : 'Win_Ventana_cuentas_planilla',
		// 	    title       : 'Cuentas por pagar de la planilla de nomina N.'+consecutivo,
		// 	    modal       : true,
		// 	    autoScroll  : false,
		// 	    closable    : false,
		// 	    autoDestroy : true,
		// 	    autoLoad    :
		// 	    {
		// 	        url     : 'comprobante_egreso/bd/grillaCuentasPlanillaNomina.php',
		// 	        scripts : true,
		// 	        nocache : true,
		// 	        params  :
		// 	        {
		// 				id_planilla           : id,
		// 				consecutivo           : consecutivo,
		// 				opcGrillaContable     : '<?php echo $opcGrillaContable; ?>',
		// 				cont                  : ''+cont,
		// 				id_comprobante_egreso : <?php echo $id_comprobante_egreso; ?>,
		// 	        }
		// 	    },
		// 	    tbar        :
		// 	    [
		// 	        {
		// 	            xtype   : 'buttongroup',
		// 	            columns : 3,
		// 	            title   : 'Opciones',
		// 	            style   : 'border-right:none;',
		// 	            items   :
		// 	            [
		// 	                {
		// 	                    xtype       : 'button',
		// 	                    width       : 60,
		// 	                    height      : 56,
		// 	                    text        : 'Regresar',
		// 	                    scale       : 'large',
		// 	                    iconCls     : 'regresar',
		// 	                    iconAlign   : 'top',
		// 	                    hidden      : false,
		// 	                    handler     : function(){ BloqBtn(this); Win_Ventana_cuentas_planilla.close(id) }
		// 	                }
		// 	            ]
		// 	        }
		// 	    ]
		// 	}).show();
		// }

		function ventanaCuentasPlanillas(id) {

			var myalto      = Ext.getBody().getHeight();
			var myancho     = Ext.getBody().getWidth();
			var consecutivo = document.getElementById('div_ComprobanteEgreso_consecutivo_'+id).innerHTML;
			var titulo='';

			if ('<?php echo $tipo_documento_cruce ?>'=='LN') { titulo='Planilla de Nomina'; }
			else if ('<?php echo $tipo_documento_cruce ?>'=='LE') { titulo='Liquidacion'; }
			else if ('<?php echo $tipo_documento_cruce ?>'=='PA') { titulo='Planilla Ajuste Nomina'; }
			else if ('<?php echo $tipo_documento_cruce ?>'=='PCP') { titulo='Planilla Consolidacion'; }

			Win_Ventana_cuentas_planilla = new Ext.Window({
			    width       : myancho-100,
			    height      : myalto-50,
			    id          : 'Win_Ventana_cuentas_planilla',
			    title       : 'Cuentas por pagar de la planilla de '+titulo+' N.'+consecutivo,
			    modal       : true,
			    autoScroll  : false,
			    closable    : false,
			    autoDestroy : true,
			    autoLoad    :
			    {
			        url     : 'comprobante_egreso/bd/grillaCuentasPlanillas.php',
			        scripts : true,
			        nocache : true,
			        params  :
			        {
						id_planilla           : id,
						consecutivo           : consecutivo,
						opcGrillaContable     : '<?php echo $opcGrillaContable; ?>',
						cont                  : ''+cont,
						id_comprobante_egreso : <?php echo $id_comprobante_egreso; ?>,
						tipo_documento_cruce  : '<?php echo $tipo_documento_cruce ?>',
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
		}

	</script>

<?php
} ?>

