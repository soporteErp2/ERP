<?php

	include("../../../../../../configuracion/conectar.php");
	include("../../../../../../configuracion/define_variables.php");
	include("../../../../../../misc/Win/bd/class.Grilla.php");
	$arrayEmpresa = explode("-", $_SESSION['NITEMPRESA']);
	// phpinfo();
	$whereGrilla = '';
	// var_dump($mysqlLi);
	// if ($id_ambiente<>'Todos') {
	// 	$whereGrilla .= " AND M.id_seccion=$id_ambiente";
	// }
	$whereGrilla .= " AND T1.fecha_documento>= '$fecha_inicio' AND T1.fecha_documento<='$fecha_final' ";
	######################################
	$grilla = new Grilla($mysql, $_POST);//INICIALIZA LA CLASE GRILLA
	######################################

	## CONFIGURACION GENERAL DATAVIEW
		$grilla->Name       = 'AnularFacturas';
		$grilla->Table      = 'ventas_pos';
		$grilla->SqlLimit   = '0,100';
		$grilla->SqlWhere   = "T1.activo=1 $whereGrilla";
		$grilla->SqlOrder   = 'id DESC';
		// $grilla->SqlDebug = 'true';
		// $grilla->SqlJoin("INNER JOIN ventas_pos_mesas_cuenta AS CM ON(CM.id = T1.id_cuenta)");
		// $grilla->SqlJoin("INNER JOIN ventas_pos_mesas AS M ON(M.id = CM.id_mesa)");

	## CONFIGURACION TOOLBAR DATAVIEW
		$grilla->Tbar = false;

	## CONFIGURACION SEARCHBAR DATAVIEW
		$grilla->Toolbar      = false;
		$grilla->FieldToolbar = 'T1.consecutivo,T1.documento_cliente,T1.cliente,T1.seccion';

	## CONFIGURACION CAMPOS DATAVIEW (COLUMNAS)
		$btnAnular = "";
		// $grilla->AddCol('Tipo Documento','TP.nombre AS tipo',150);
		$grilla->AddCol('Fecha','T1.fecha_documento AS fecha',100);
		$grilla->AddCol('Numero','T1.consecutivo AS consecutivo',90);
		$grilla->AddCol('Cliente','T1.documento_cliente AS documento_cliente',100);
		$grilla->AddCol('Cliente','T1.cliente AS cliente',100);
		$grilla->AddCol('Ambiente','T1.seccion AS ambiente',100);
		$grilla->AddCol('Mesa','T1.mesa AS mesa',100);
		$grilla->AddColHtml('Print','<i class="material-icons" style="font-size: 20px;margin-top: -10px;color:#8c8c8c;cursor:pointer;" title="Imprimir Factura #[T1.consecutivo AS consecutivo]" onClick="printFactura([T1.id AS id_factura])" >print</i>',60);
		$grilla->AddColHtml('Estado','<i class="material-icons" style="font-size: 22px;margin-top: -10px;color:[IF(T1.estado=1,"#45af55",IF(T1.estado=3,"#db5957","#8c8c8c")) AS color_estado]" title="[IF(T1.estado=1,"Activa",IF(T1.estado=3,"Anulada","Bloqueada")) AS title_estado]" >[IF(T1.estado=1,"done",IF(T1.estado=3,"remove_circle_outline","lock")) AS estado]</i>',60);
		$grilla->AddColHtml('Anular','<i class="material-icons" style="font-size: 22px;margin-top: -10px;color:#db5957;cursor:pointer;" title="Anular Factura [T1.id AS numero_comanda]" onclick="anularFactura({id:\'[T1.id AS id]\',estado:\'[T1.estado AS num_estado]\'})">clear</i>',60);
		// $grilla->AddColHtml('Estado','<i class="material-icons" style="font-size: 22px;margin-top: -10px;color:[IF(T1.estado=1,"#45af55",IF(T1.estado=3,"#db5957","#8c8c8c")) AS color_estado]" title="[IF(T1.estado=1,"Activa",IF(T1.estado=3,"Anulada","Facturada")) AS title_estado]" >[IF(T1.estado=1,"done",IF(T1.estado=3,"remove_circle_outline","lock")) AS estado]</i>',60);
		// $grilla->AddColHtml('Anular','<i class="material-icons" style="font-size: 22px;margin-top: -10px;color:#db5957;cursor:pointer;" title="Anular comanda [T1.id AS numero_comanda]" onclick="anularFactura({id:\'[T1.id AS numero_comanda]\',estado:\'[T1.estado AS num_estado]\'})">clear</i>',60);
		// $grilla->AddCol('Estado','IF(T1.estado=1,"done","lock") AS estado',100);
		// $grilla->AddCol('Edad','T1.edad',100);

	## CONFIGURACION ASIDE DE FILTROS DE BUSQUEDA
		// $grilla->AddFilterAsideJoin('Tipo Documento','tipo_documento','tipo_documento','id','nombre');


	## CONFIGURACION VENTANA AUTOMATICA DEL FORMULARIO
		$grilla->FormMaterial = false;
		$grilla->FTitle       = 'Formulario';
		$grilla->Vwidth       = 430;
		$grilla->Vheight      = 'calc(100% - 150px)';
		$grilla->FDivAncho    = 400;

		$grilla->EventUpdate = false;
	## CONFIGURACION DE LOS CAMPOS DEL FORMULARIO
		// $grilla->AddSeparator('Informacion Personal');
		// $grilla->AddComboBox('Tipo Documento','tipo_documento',180,'true','true','tipo_documento,id,nombre,true','activo=1');
		// $grilla->AddTextField('Documento','documento',180,false,false);
		// $grilla->AddTextField('Nombre','nombre',180,true,false);
		// $grilla->AddSeparator('Otra Informacion');
		// $grilla->AddTextField('Edad','edad',180,true,false);
		// $grilla->AddTextField('Direccion','direccion',180,true,false);
		// $grilla->AddTextField('Nacimiento','nacimiento',180,true,false);
		// $grilla->AddTextField('Hora Registro','hora',180,true,false);
		// $grilla->AddTextArea('Observaciones','observaciones',180,100,true,true);

	## VALIDACIONES
		// $grilla->AddValidation('nacimiento','date');
		// $grilla->AddValidation('nombres','uppercase');
		// $grilla->AddValidation('hora','time');


	######################################
	$grilla->IniClass(); //INICIALIZA EL OBJETO DE LA CLASE GRILLA
	######################################

	##JAVASCRIPT
		##if(isset($opcionClass) && $opcionClass == 'vInsert'){echo '<script>function(){}</script>'}
		##if(isset($opcionClass) && $opcionClass == 'vUpdate'){echo '<script>function(){}</script>'}
		##if(isset($opcionClass) && ($opcionClass == 'vInsert' || $opcionClass == 'vUpdate')){echo '<script>function(){}</script>'}
		##if(isset($opcionClass) && $opcionClass == 'fInsert'){echo '<script>function(){}</script>'}
		##if(isset($opcionClass) && $opcionClass == 'fUpdate'){echo '<script>function(){}</script>'}
		##if(isset($opcionClass) && ($opcionClass == 'fInsert' || $opcionClass == 'fUpdate')){echo '<script>function(){}</script>'}
		if(!isset($opcionClass)){
		?>
			<script>
				var anularFactura = (params) =>{

				// $permiso_cotizacion  = (user_permisos(5,'false') == 'true')? 'false' : 'true';

					if (params.estado==2 && "<?=(user_permisos(255) == 'true' ? "true" : "false" )?>" != "true" ){
						alert("No tiene el permiso para eliminar documentos bloqueados\ncontacte al usuario administrador para que le asigne el permiso desde el panel de control");
						return;
					}
					if (params.estado==3){
						alert("No se puede anular una factura ya anulada");
						return;
					}

					if (confirm("Si anula la factura se creara una nueva comanda con los cargos para que se facturen y se reversara inventario y contabilidad, desea continuar?")) {
						let observacion = prompt("Por favor ingrese una observacion de la anulacion");
						$W.Loading()
						$W.Ajax({
							url     : "../../../backend/pos_admin/Controller.php",
							params :  {
								method       : "anularFactura",
								id_documento : params.id,
								observacion  : observacion,
								nit          : "<?= $arrayEmpresa[0] ?>"
							},
							timeout : 2000,
							success : function(result,xhr){
								console.log(result.responseText); //lee respuesta como texto
								console.log(JSON.parse(result.responseText)); //lee respuesta como json
								let response = JSON.parse(result.responseText);
								$W.Loading();
								if (response.status=='success'){ loadGrilla(); }
								else{ alert(response.message) }
							},
							failure : function(xhr){
								alert("Error de conexion");
								$W.Loading();
							}
						})
						console.log(params);
					}
					// alert()
				}

				var printFactura = (id_factura) => {
    				window.open(`../../../backend/pos/Controller.php?method=printTiquet&id_documento=${id_factura}&nit=<?= $arrayEmpresa[0] ?>`, '_blank');
    				// window.open(`../../../backend/pos/Controller.php?method=printComanda&id_comanda=${id_comanda}`, '_blank');
				}

			</script>
		<?php
			// echo '<script>function(){}</script>'
		}
?>