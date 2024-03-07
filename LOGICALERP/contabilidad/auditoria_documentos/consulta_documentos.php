<?php
	include("../../../configuracion/conectar.php");
	include("../../../configuracion/define_variables.php");
	include("../../../misc/MyGrilla/class.MyGrilla.php");

	/**//////////////////////////////////////////////**/
	/**///		    INICIALIZACION DE LA CLASE  	  ///**/
	/**/											                      /**/
	/**/	         $grilla = new MyGrilla();				/**/
	/**/											                      /**/
	/**//////////////////////////////////////////////**/

	$id_empresa  = $_SESSION['EMPRESA'];
	$id_sucursal = $_SESSION['SUCURSAL'];

	$whereSucursal = '';
	if($filtro_sucursal > 0 && $filtro_sucursal!='global') $whereSucursal="AND id_sucursal='$filtro_sucursal'";

	switch ($tipo_documento_cruce) {
		case 'FC':
			$tabla_documento      = 'compras_facturas';
			$CamposBusquedaGrilla = 'fecha_inicio,fecha_final,prefijo_factura,numero_factura,consecutivo,nit,proveedor';
			$whereConsecutivos    = 'numero_factura>0 OR consecutivo>0';
			$orderBy              = 'consecutivo DESC';
			$whereFechas          = ($fecha_inicial != "" && $fecha_final != "")? " AND fecha_inicio BETWEEN '$fecha_inicial' AND '$fecha_final' " : "";
			break;

		case 'CE':
			$tabla_documento      = 'comprobante_egreso';
			$CamposBusquedaGrilla = 'fecha_comprobante,consecutivo,nit_tercero,tercero';
			$whereConsecutivos    = 'consecutivo>0';
			$orderBy              = 'consecutivo DESC';
			$whereFechas          = ($fecha_inicial != "" && $fecha_final != "")? " AND fecha_comprobante BETWEEN '$fecha_inicial' AND '$fecha_final' " : "";
			break;

		case 'RV':
			$tabla_documento      = 'ventas_remisiones';
			$CamposBusquedaGrilla = 'fecha_inicio,fecha_finalizacion,consecutivo,nit,cliente,bodega';
			$whereConsecutivos    = 'consecutivo>0';
			$orderBy              = 'consecutivo DESC';
			$whereFechas          = ($fecha_inicial != "" && $fecha_final != "")? " AND fecha_inicio BETWEEN '$fecha_inicial' AND '$fecha_final' " : "";
			break;

		case 'FV':
			$tabla_documento      = 'ventas_facturas';
			$CamposBusquedaGrilla = 'fecha_inicio,fecha_vencimiento,prefijo,numero_factura,nit,cliente,numero_factura_completo,bodega';
			$whereConsecutivos    = "numero_factura>0 ";
			$orderBy              = 'fecha_inicio DESC';
			$whereFechas          = ($fecha_inicial != "" && $fecha_final != "")? " AND fecha_inicio BETWEEN '$fecha_inicial' AND '$fecha_final' " : "";
			break;

		case 'RC':
			$tabla_documento      = 'recibo_caja';
			$CamposBusquedaGrilla = 'fecha_recibo,consecutivo,nit_tercero,tercero';
			$whereConsecutivos    = 'consecutivo>0';
			$orderBy              = 'consecutivo DESC';
			$whereFechas          = ($fecha_inicial != "" && $fecha_final != "")? " AND fecha_recibo BETWEEN '$fecha_inicial' AND '$fecha_final' " : "";
			break;

		case 'LN':
			$tabla_documento      = 'nomina_planillas';
			$CamposBusquedaGrilla = 'fecha_documento,consecutivo,usuario';
			$whereConsecutivos    = 'consecutivo>0';
			$orderBy              = 'consecutivo DESC';
			$whereFechas          = ($fecha_inicial != "" && $fecha_final != "")? " AND fecha_documento BETWEEN '$fecha_inicial' AND '$fecha_final' " : "";
			break;

		case 'LE':
			$tabla_documento      = 'nomina_planillas_liquidacion';
			$CamposBusquedaGrilla = 'fecha_documento,consecutivo,usuario';
			$whereConsecutivos    = 'consecutivo>0';
			$orderBy              = 'consecutivo DESC';
			$whereFechas          = ($fecha_inicial != "" && $fecha_final != "")? " AND fecha_documento BETWEEN '$fecha_inicial' AND '$fecha_final' " : "";
			break;

		case 'PA':
			$tabla_documento      = 'nomina_planillas_ajuste';
			$CamposBusquedaGrilla = 'fecha_documento,consecutivo,usuario';
			$whereConsecutivos    = 'consecutivo>0';
			$orderBy              = 'consecutivo DESC';
			$whereFechas          = ($fecha_inicial != "" && $fecha_final != "")? " AND fecha_documento BETWEEN '$fecha_inicial' AND '$fecha_final' " : "";
			break;

		case 'NCG':
			$tabla_documento      = 'nota_contable_general';
			$CamposBusquedaGrilla = 'fecha_nota,consecutivo,consecutivo_niif,sucursal,tipo_nota,numero_identificacion_tercero,tercero,tipo_nota';
			$whereConsecutivos    = 'consecutivo>0';
			$orderBy              = 'consecutivo DESC';
			$whereFechas          = ($fecha_inicial != "" && $fecha_final != "")? " AND fecha_nota BETWEEN '$fecha_inicial' AND '$fecha_final' " : "";
			break;

		default:
			$tipo_documento_cruce = 'FC';
			$tabla_documento      = 'compras_facturas';
			$CamposBusquedaGrilla = 'fecha_inicio,fecha_final,prefijo_factura,numero_factura,consecutivo,nit,proveedor';
			$whereConsecutivos    = 'numero_factura>0 OR consecutivo>0';
			$orderBy              = 'consecutivo DESC';
			$whereFechas          = ($fecha_inicial != "" && $fecha_final != "")? " AND fecha_inicio BETWEEN '$fecha_inicial' AND '$fecha_final' " : "";
			break;
	}

	if($filtro_estado == ""){
		$filtro_estado = "global";
	}

	if($filtro_estado != "global" && isset($filtro_estado)){
		$sql = "SELECT id_documento
						FROM documentos_auditados
						WHERE activo = 1
						AND id_empresa = '$id_empresa'
						AND tipo_documento = '$tipo_documento_cruce'
						$whereSucursal";
		$query = $mysql->query($sql,$mysql->link);

		if($mysql->num_rows($query) > 0){
			if($filtro_estado == "auditados"){
				$operador = "=";
				$sentencia = "OR";
			} else{
				$operador = "!=";
				$sentencia = "AND";
			}

			$whereIdDocumentosAuditados = "";
			while($row = $mysql->fetch_array($query)){
				if($whereIdDocumentosAuditados == ""){
					$whereIdDocumentosAuditados .= " id $operador $row[id_documento]";
				} else{
					$whereIdDocumentosAuditados .= " $sentencia id $operador $row[id_documento]";
				}
			}

			$whereIdDocumentosAuditados = " AND ($whereIdDocumentosAuditados)";
		} else{
			$whereIdDocumentosAuditados = "";
		}
	}

	//CONFIGURACION//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

		//NOMBRE DE LA GRILLA
			$grilla->GrillaName	 		= 'auditoriaDocumentos';  	//NOMBRE DE LA GRILLA (DEBE SER UNICO POR CADA GRILLA DE LA APLICACION)
		//QUERY
			$grilla->TableName			= $tabla_documento;		//NOMBRE DE LA TABLA EN LA BASE DE DATOS
			$grilla->MyWhere			  = "activo = 1 AND ($whereConsecutivos) AND id_empresa = '$id_empresa' $whereSucursal $whereFechas $whereIdDocumentosAuditados";  //WHERE DE LA CONSULTA A LA TABLA "$TableName"
			$grilla->MySqlLimit			= '0,100';			//LIMITE DE LA CONSULTA
			$grilla->GroupBy 			  = '';
			$grilla->OrderBy 			  = $orderBy;
		//TAMANO DE LA GRILLA
			$grilla->AutoResize	 		= 'false';	//SI LA GRILLA ES AUTORESIZABLE (LIQUIDA) -> "true" SI NO -> "false"
			$grilla->QuitarAncho		= 85;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'true'
			$grilla->QuitarAlto			= 160;			//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'true'
		//TOOLBAR Y CAMPO DE BUSQUEDA
			$grilla->Gtoolbar		   	    = 'true';			//SI LA GRILLA LLEVA EL TOOLBAR DE BUSQUEDA
			$grilla->CamposBusqueda		  = $CamposBusquedaGrilla;		//VARIABLE QUE DEFINE LOS CAMPOS DE LA BD DONDE SE BUSCARA
			$grilla->DivActualiBusqueda = '' ;				//VARIABLE QUE DEFINE LA CAPA DONDE SE ACTUALIZA LA GRILLA DESPUES DE UNA BUSQUEDA
		//CONFIGURACION DE CAMPOS EN LA GRILLA
			if ($tipo_documento_cruce=='FC') {
				$grilla->AddRow('Fecha','fecha_inicio',80);
				$grilla->AddRow('Vencimiento','fecha_final',80);
				$grilla->AddRowImage('Numero','<center title="[prefijo_factura] [numero_factura]">[prefijo_factura] [numero_factura]</center>',80);
				$grilla->AddRowImage('Consecutivo','<input id="div_auditoriaDocumentos_id_sucursal_[id]" type="hidden" value="[id_sucursal]"><center id="div_auditoriaDocumentos_consecutivo_[id]" title="[consecutivo]">[consecutivo]</center>',80);
				$grilla->AddRow('Nit','nit',80);
				$grilla->AddRowImage('Proveedor','<center id="div_auditoriaDocumentos_proveedor_[id]" title="[proveedor]">[proveedor]</center>',150);
				$grilla->AddRow('Sucursal','sucursal',100);
				$grilla->AddRowImage('Bodega','<center id="div_auditoriaDocumentos_bodega_[id]" title="[bodega]">[bodega]</center>',100);
			}
			else if ($tipo_documento_cruce=='CE') {
				$grilla->AddRow('Fecha','fecha_comprobante',80);
				$grilla->AddRowImage('Consecutivo','<input id="div_auditoriaDocumentos_id_sucursal_[id]" type="hidden" value="[id_sucursal]"><center id="div_auditoriaDocumentos_consecutivo_[id]" title="[consecutivo]">[consecutivo]</center>',80);
				$grilla->AddRow('Nit','nit_tercero',80);
				$grilla->AddRowImage('Tercero','<center id="div_auditoriaDocumentos_tercero_[id]" title="[tercero]">[tercero]</center>',150);
				$grilla->AddRow('Sucursal','sucursal',100);
			}
			else if ($tipo_documento_cruce=='RV') {
				$grilla->AddRow('Fecha','fecha_inicio',80);
				$grilla->AddRow('Vencimiento','fecha_finalizacion',80);
				$grilla->AddRowImage('Consecutivo','<input id="div_auditoriaDocumentos_id_sucursal_[id]" type="hidden" value="[id_sucursal]"><center id="div_auditoriaDocumentos_consecutivo_[id]" title="[consecutivo]">[consecutivo]</center>',80);
				$grilla->AddRow('Nit','nit',80);
				$grilla->AddRowImage('Cliente','<center id="div_auditoriaDocumentos_cliente_[id]" title="[cliente]">[cliente]</center>',150);
				$grilla->AddRow('Sucursal','sucursal',100);
				$grilla->AddRowImage('Bodega','<center id="div_auditoriaDocumentos_bodega_[id]" title="[bodega]">[bodega]</center>',100);
			}
			else if ($tipo_documento_cruce=='FV') {
				$grilla->AddRow('Fecha','fecha_inicio',80);
				$grilla->AddRow('Vencimiento','fecha_vencimiento',80);
				$grilla->AddRowImage('Consecutivo','<input id="div_auditoriaDocumentos_id_sucursal_[id]" type="hidden" value="[id_sucursal]"><center id="div_auditoriaDocumentos_numero_factura_completo_[id]" title="[numero_factura_completo]">[numero_factura_completo]</center>',80);
				$grilla->AddRow('Nit','nit',80);
				$grilla->AddRowImage('Cliente','<center id="div_auditoriaDocumentos_cliente_[id]" title="[cliente]">[cliente]</center>',150);
				$grilla->AddRow('Sucursal','sucursal',100);
				$grilla->AddRowImage('Bodega','<center id="div_auditoriaDocumentos_bodega_[id]" title="[bodega]">[bodega]</center>',100);
			}
			else if ($tipo_documento_cruce=='RC') {
				$grilla->AddRow('Fecha','fecha_recibo',80);
				$grilla->AddRowImage('Consecutivo','<input id="div_auditoriaDocumentos_id_sucursal_[id]" type="hidden" value="[id_sucursal]"><center id="div_auditoriaDocumentos_consecutivo_[id]" title="[consecutivo]">[consecutivo]</center>',80);
				$grilla->AddRow('Nit','nit_tercero',80);
				$grilla->AddRowImage('Tercero','<center id="div_auditoriaDocumentos_tercero_[id]" title="[tercero]">[tercero]</center>',150);
				$grilla->AddRow('Sucursal','sucursal',100);
			}
			else if ($tipo_documento_cruce=='LN' || $tipo_documento_cruce=='LE' || $tipo_documento_cruce=='PA') {
				$grilla->AddRow('Fecha','fecha_documento',80);
				$grilla->AddRowImage('Consecutivo','<input id="div_auditoriaDocumentos_id_sucursal_[id]" type="hidden" value="[id_sucursal]"><center id="div_auditoriaDocumentos_consecutivo_[id]" title="[consecutivo]">[consecutivo]</center>',80);
				$grilla->AddRowImage('Usuario','<center id="div_auditoriaDocumentos_usuario_[id]" title="[usuario]">[usuario]</center>',250);
				$grilla->AddRow('Sucursal','sucursal',100);
			}
			else if ($tipo_documento_cruce=='NCG') {
				$grilla->AddRow('Fecha','fecha_nota',80);
				$grilla->AddRowImage('Consecutivo Colgaap','<input id="div_auditoriaDocumentos_id_sucursal_[id]" type="hidden" value="[id_sucursal]"><center id="div_auditoriaDocumentos_consecutivo_[id]" title="[consecutivo]">[consecutivo]</center>',110);
				$grilla->AddRowImage('Consecutivo Niif','<center id="div_auditoriaDocumentos_consecutivo_[id]" title="[consecutivo]">[consecutivo]</center>',100);
				$grilla->AddRow('Nit','numero_identificacion_tercero',80);
				$grilla->AddRowImage('Tercero','<center id="div_auditoriaDocumentos_tercero_[id]" title="[tercero]">[tercero]</center>',150);
				$grilla->AddRow('Tipo','tipo_nota',150);
				$grilla->AddRow('Sucursal','sucursal',100);
			}
			$grilla->AddRow('Auditado/Autorizado','check',120);
		//CONFIGURACION CSS X COLUMNA
			$grilla->AddColStyle('consecutivo_documento','text-align:right; width:75px !important; padding-right:5px');
		//CONFIGURACION FORMULARIO
			$grilla->FContenedorAncho		  = 300;
			$grilla->FColumnaGeneralAncho	= 280;
			$grilla->FColumnaGeneralAlto	= 25;
			$grilla->FColumnaLabelAncho		= 80;
			$grilla->FColumnaFieldAncho		= 200;
		//CONFIGURACION DE LA VENTANA DE INSERT, UPDATE Y DELETE
			$grilla->VentanaAuto		 = 'false';			//SI LA VENTANA DE INSERT, UPDATE Y DELETE ES AUTOMATICA O MANUAL
			$grilla->TituloVentana	 = 'Ventana Familia Items '.$subtitulo; //NOMBRE DE LA VENTANA DE INSER, UPDATE Y DELETE
			$grilla->VBarraBotones	 = 'false';			    //SI HAY O NO BARRA DE BOTONES
			$grilla->VBotonNuevo		 = 'false';			    //SI LLEVA EL BOTON DE AGREGAR REGISTRO
			$grilla->VBotonNText		 = 'Nueva Familia'; //TEXTO DEL BOTON DE NUEVO REGISTRO
			$grilla->VBotonNImage		 = 'cubos_add';			//IMAGEN CSS DEL BOTON
			$grilla->VAutoResize		 = 'true';			    //SI LA VENTANA ES AUTORESIZABLE (LIQUIDA) -> "true" SI NO -> "false"
			$grilla->VAncho		 		   = 340;				      //ANCHO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'false'
			$grilla->VAlto		 		   = 130;				      //ALTO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'false'
			$grilla->VQuitarAncho		 = 70;				      //AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ANCHO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'true'
			$grilla->VQuitarAlto		 = 160;				      //AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ALTO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'true'
			$grilla->VAutoScroll		 = 'false';			    //SI LA VENTANA TIENE O NO AUTOSCROLL
			$grilla->VBotonEliminar	 = 'true';			    //SI MUESTRA BOTON DE ELIMINAR EN LA VENTANA DE EDICION
			$grilla->VComporEliminar = 'true';			    //COMPORTAMIENTO DEL BOTON DE ELIMINAR ("true" ES CAMPO ACTIVO DE 1 A 0) ("false" -> ELIMINA EL REGISTRO DE LA BASE DE DATOS)

	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	/**//////////////////////////////////////////////////////////////**/
	/**///				        INICIALIZACION DE LA GRILLA 	  			  ///**/
	/**/															                              /**/
	/**/	   $grilla->Link = $link;  	    //Conexion a la BD		    /**/
	/**/	   $grilla->inicializa($_POST); //Variables POST			    /**/
	/**/	   $grilla->GeneraGrilla(); 	  //Inicializa la Grilla		/**/
	/**/															                              /**/
	/**//////////////////////////////////////////////////////////////**/

if(!isset($opcion)){ ?>
	<script>
		var filtroBusqueda = '';
		<?php if(isset($MyFiltroBusqueda))echo 'filtroBusqueda = "'.$MyFiltroBusqueda.'"'; ?>

		function Editar_auditoriaDocumentos(id){
			return;
			var documento=document.getElementById('filtro_tipo_documento').value;
			var titulo      = ''
			,	consecutivo = '';

			if (documento=='FC') {
				consecutivo=document.getElementById('div_auditoriaDocumentos_consecutivo_'+id).innerHTML;
				titulo='Factura de Compra<br>Nr. '+consecutivo;
			}
			else if (documento=='CE') {
				consecutivo=document.getElementById('div_auditoriaDocumentos_consecutivo_'+id).innerHTML;
				titulo='Comprobante de Egreso<br>Nr.'+consecutivo;
			}
			else if (documento=='RV') {
				consecutivo=document.getElementById('div_auditoriaDocumentos_consecutivo_'+id).innerHTML;
				titulo='Remision de Venta<br>Nr.'+consecutivo;
			}
			else if (documento=='FV') {
				consecutivo=document.getElementById('div_auditoriaDocumentos_numero_factura_completo_'+id).innerHTML;
				titulo='Factura de Venta<br>Nr.'+consecutivo;
			}
			else if (documento=='RC') {
				consecutivo=document.getElementById('div_auditoriaDocumentos_consecutivo_'+id).innerHTML;
				titulo='Recibo de Caja<br>Nr.'+consecutivo;
			}
			else if (documento=='LN') {
				consecutivo=document.getElementById('div_auditoriaDocumentos_consecutivo_'+id).innerHTML;
				titulo='Planilla de Nomina<br>Nr.'+consecutivo;
			}
			else if (documento=='LE') {
				consecutivo=document.getElementById('div_auditoriaDocumentos_consecutivo_'+id).innerHTML;
				titulo='Liquidacion de Empleado<br>Nr.'+consecutivo;
			}
			else if (documento=='NCG') {
				consecutivo=document.getElementById('div_auditoriaDocumentos_consecutivo_'+id).innerHTML;
				titulo='Nota Contable General<br>Nr.'+consecutivo;
			}


		 	var myalto2  = Ext.getBody().getHeight();
	        var myancho2 = Ext.getBody().getWidth();

	        WinAlto = myalto2-20;
	        WinAncho = myancho2-30;

	        Win_Panel_buscar_documento = new Ext.Window({
	            width       : WinAncho,
	            height      : WinAlto,
	            title       : 'Informacion detallada del documento',
	            modal       : true,
	            autoScroll  : true,
	            closable    : false,
	            autoDestroy : false,
	            bodyStyle   : 'background-color:<?php echo $_SESSION['COLOR_FONDO'] ?>',
	            items       :
	            [
	                {
	                    closable    : false,
	                    border      : false,
	                    autoScroll  : true,
	                    iconCls     : '',
	                    bodyStyle   : 'background-color:<?php echo $_SESSION['COLOR_FONDO'] ?>;',
	                    items       :
	                    [
	                        {
	                            xtype       : "panel",
	                            id          : 'contenedor_Win_Panel_buscar_documento',
	                            border      : false,
	                            autoLoad    :
	                                    {
	                                        url     : 'consulta_documentos/grillaDocumento.php',
	                                        scripts : true,
	                                        nocache : true,
	                                        params  :
	                                                {
														documento    : documento,
														consecutivo  : consecutivo,
														id_documento : id,
	                                                }
	                                    }
	                        }
	                    ],
	                    tbar        :
	                    [

	                        {
	                            xtype   : 'buttongroup',
	                            columns : 3,
	                            title   : 'Opciones',
	                            items   :
	                            [

	                                {
	                                    xtype       : 'button',
	                                    width       : 60,
	                                    height      : 56,
	                                    text        : 'Log de eventos',
	                                    scale       : 'large',
	                                    iconCls     : 'busca_doc',
	                                    iconAlign   : 'top',
	                                    handler     : function(){ ventana_log_documento() }
	                                },
	                                {
	                                    xtype       : 'button',
	                                    width       : 60,
	                                    height      : 56,
	                                    text        : 'Regresar',
	                                    scale       : 'large',
	                                    iconCls     : 'regresar',
	                                    iconAlign   : 'top',
	                                    handler     : function(){ Win_Panel_buscar_documento.close() }
	                                },
		                            {
		                                xtype       : 'button',
		                                width       : 60,
		                                height      : 56,
		                                text        : 'Imprimir',
		                                scale       : 'large',
		                                iconCls     : 'pdf32_new',
		                                iconAlign   : 'top',
		                                handler     : function(){ imprimir_bitacora() }
		                            }

	                            ]
	                        },'->',
                {
                    xtype       : "tbtext",
                    text        : '<div id="divContenedor_" style="font-weight:bold;font-size:18px;"><div>',
                    scale       : "large",
                }
	                    ]
	                }
	            ]
	        }).show();
		}

		var contenedor       = document.getElementById("DIV_listado_auditoriaDocumentos").childNodes
		,	jsonId           = []
		,	index            = 0
		,	divRow = ''
		// jsonId['documentos'] = {}

		contenedor.forEach(function(element) {
		if(!element.id || typeof(element.id)=='undefined'){return}
		 	id=element.id.split("_")[2];
		 	divRow = document.getElementById("div_auditoriaDocumentos_check_"+id);
		 	if (!divRow) {return;}
			divIdSucursal = document.getElementById("div_auditoriaDocumentos_id_sucursal_" + id).value;
			divRow.innerHTML = `<center>
														<img onclick='changeCheck(${id},${divIdSucursal});' id='checkAudit_${id}' data-value='false' src='img/checkox_false.png' style="cursor:pointer" >
													</center>`;
			jsonId[index] = id
			index++;
		});

		Ext.Ajax.request({
            url     : 'auditoria_documentos/bd/bd.php',
            params  :
            {
				opc            : 'getAuditDocs',
				id_sucursal    : document.getElementById('filtro_sucursal_').value,
				tipo_documento : document.getElementById('filtro_tipo_documento').value,
				jsonId         : JSON.stringify(jsonId),
            },
            success :function (result, request){
            		renderCheck(result.responseText);
                    },
            failure : function(error){
                        console.log(error);
                    }
        });

		var renderCheck = (jsonCheck) => {
			jsonResponse = JSON.parse(jsonCheck);
			if (!jsonResponse.auditDocs) {return;}
			jsonResponse.auditDocs.forEach(function(element) {
				document.getElementById(`checkAudit_${element.id_documento}`).dataset.value = 'true';
				document.getElementById(`checkAudit_${element.id_documento}`).src           = "img/checkox_true.png";
			});
		}

		var changeCheck = (id_documento,id_sucursal) => {
			checkValue = document.getElementById(`checkAudit_${id_documento}`)
			tipo_documento = document.getElementById('filtro_tipo_documento').value

			if(tipo_documento == 'FV'){
				consecutivo = document.getElementById(`div_auditoriaDocumentos_numero_factura_completo_${id_documento}`).innerHTML;
			}
			else{
				consecutivo = document.getElementById(`div_auditoriaDocumentos_consecutivo_${id_documento}`).innerHTML;
			}

			if (checkValue.dataset.value=='true'){return}
			MyLoading2("on");
			Ext.Ajax.request({
	            url     : 'auditoria_documentos/bd/bd.php',
	            params  :
	            {
					opc            : 'setAuditDoc',
					id_sucursal    : id_sucursal,
					tipo_documento : tipo_documento,
					id_documento   : id_documento,
					consecutivo    : consecutivo,
					checkValue     : checkValue.dataset.value,
	            },
	            success :function (result, request){

	            		var response = JSON.parse(result.responseText);
	            		if (response.status=='success') {
	            			if (checkValue.dataset.value=='false'){
	            				checkValue.dataset.value = 'true'
	            				checkValue.src = "img/checkox_true.png";
	            			}
	            			else{
	            				checkValue.dataset.value = 'false'
	            				checkValue.src = "img/checkox_false.png";
	            			}
	            			// checkValue
	            			MyLoading2("off");
	            		}
	            		else{
	            			MyLoading2("off",{icono:'fail',texto:"Se produjo un error, intentelo de nuevo"});
	            		}
	                    },
	            failure : function(error){
	                        console.log(error);
	                        MyLoading2("off",{icono:'fail',texto:"Se produjo un error en la conexion"});
	                    }
	        });
		}

    </script>
<?php } ?>
