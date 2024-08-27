<?php
	include('../../configuracion/conectar.php');
	// include('../../configuracion/define_variables_debug.php');

	$permiso_devolucion_compra           = (user_permisos(82,'false') == 'true')? 'false' : 'true';
	$permiso_devolucion_venta            = (user_permisos(83,'false') == 'true')? 'false' : 'true';
	$permiso_nota_contable               = (user_permisos(84,'false') == 'true')? 'false' : 'true';
	$permiso_facturas_saldos             = (user_permisos(91,'false') == 'true')? 'false' : 'true';

	$permiso_consulta_pos                = (user_permisos(85,'false') == 'true')? 'false' : 'true';
	$permiso_consulta_asientos_documento = (user_permisos(86,'false') == 'true')? 'false' : 'true';
	$permiso_consulta_documento          = (user_permisos(86,'false') == 'true')? 'false' : 'true';
	$permiso_consulta_cuentas            = (user_permisos(87,'false') == 'true')? 'false' : 'true';
	$permiso_consulta_asientos           = (user_permisos(88,'false') == 'true')? 'false' : 'true';

	$permiso_cierre_anual                = (user_permisos(183,'false') == 'true')? 'false' : 'true';
	$permiso_cierre_por_periodo          = (user_permisos(198,'false') == 'true')? 'false' : 'true';

	$permiso_cierre_anual_cancelar       = (user_permisos(186,'false') == 'true')? 'false' : 'true';
	$permiso_amortizacion                = (user_permisos(209,'false') == 'true')? 'false' : 'true';
	$permiso_auditoria                   = (user_permisos(245,'false') == 'true')? 'false' : 'true';

	$modulos                             = '';
	$id_empresa                          = $_SESSION['EMPRESA'];

	$sqlModulos   = "SELECT id,software,icono FROM web_service_software WHERE id_empresa='$id_empresa' AND activo=1";
	$queryModulos = mysql_query($sqlModulos,$link);
	while ($rowModulos = mysql_fetch_assoc($queryModulos)) {
		if($rowModulos['icono'] == '') continue;
		$modulos .= '<div class="IconoPanelControl" onClick="AbreVentanaPanelModulosWs('.$rowModulos['id'].',\''.$rowModulos['software'].'\');">
						<div class="IconoPanelControlimg"><img src="img/'.$rowModulos['icono'].'" width="44" height="44"></div>
						<div class="IconoPanelControltxt">'.$rowModulos['software'].'</div>
					</div>';
	}

	// CONSULTAR LAS APIS EXTERNAS DEL ERP
	$sql="SELECT
				id,
				request_url AS url,
				request_method AS method,
				authorization AS auth,
				titulo,
				window_height AS height,
				window_width AS width,
				tipo,
				id_software,
				software,
				icono,
				archivo
			FROM api_conections WHERE activo=1 ";
	$query=$mysql->query($sql);
	while ($row=$mysql->fetch_array($query)) {
		$modulosApi .= "<div class='IconoPanelControl' onClick=\"AbreVentanaApi({id: '$row[id]',archivo: '$row[archivo]',height: $row[height],width: $row[width],titulo:'$row[titulo]' })\">
							<div class='IconoPanelControlimg'><img src='img/$row[icono]' width='44' height='44'></div>
							<div class='IconoPanelControltxt'>$row[titulo]</div>
						</div>";
	}
?>

<div style="float:left; padding: 15px 0px 0px 15px; width:100%;">
	<!-- NOTAS CONTABLES -->

<?php if($permiso_devolucion_compra=='false' || $permiso_devolucion_venta =='false' || $permiso_nota_contable=='false'){ ?>
	<div class="ContenedorGrupoPanelControl">
		<div class="TituloPanelControl">
			Documentos
		</div>
		<div style="width:100%; float:left">
			<?php if ($permiso_devolucion_compra=='false') { ?>
				<div class="IconoPanelControl" onClick="AbreVentanaPanelGlobalContabilidadCompras('notas_inventario/notas_devolucion/devolucion_compra/grillaContable','Devolucion Modulo de Compra',0,0,0,56,'DevolucionCompra');">
					<div class="IconoPanelControlimg"><img src="../../temas/clasico/images/iconos/ventas44.png" width="44" height="44"></div>
					<div class="IconoPanelControltxt">Devoluciones de Compra</div>
				</div>
			<?php }
			if($permiso_devolucion_venta =='false'){ ?>
				<div class="IconoPanelControl" onClick="AbreVentanaPanelGlobalContabilidadVentas('sucursales/sucursales','Devolucion Modulo de Venta',0,0,0,70,'DevolucionVenta');">
					<div class="IconoPanelControlimg"><img src="../../temas/clasico/images/iconos/compras44.png" width="44" height="44"></div>
					<div class="IconoPanelControltxt">Devoluciones de Venta</div>
				</div>
			<?php }
			if($permiso_nota_contable=='false'){ ?>
				<div class="IconoPanelControl" onClick="AbreVentanaPanelGlobalContabilidadNotaGeneral('notas_inventario/notas_devolucion/devolucion_compra/grillaContable','Nota Contable',0,0,0,70,'NotaGeneral');">
					<div class="IconoPanelControlimg"><img src="img/nota_general.png" width="44" height="45"></div>
					<div class="IconoPanelControltxt">Nota Contable (General)</div>
				</div>
			<?php
			} ?>
			<div id="divPadreModalUploadFile" class="fondo_modal_upload_file">
				<div>
					<div>
						<div>
							<div id="div_upload_file">
								<div></div>
							</div>
							<div class="btn_div_upload_file2" style="margin-left:350px;margin-top: 3px;" title="Descargar Formato" onclick="window.open('nota_general/bd/formato_cuentas_nota.xlsx')">&darr;</div>
							<div class="btn_div_upload_file2" onclick="close_ventana_upload_file()">X</div>
						</div>
					</div>
				</div>
			</div>

			<div id="divPadreModalUploadFile2" class="fondo_modal_upload_file">
				<div>
					<div>
						<div>
							<div id="div_upload_file2">
								<div></div>
							</div>
							<div class="btn_div_upload_file2" style="margin-left:350px;margin-top: 3px;" title="Descargar Formato" onclick="window.open('facturas_saldos_iniciales/bd/formato_facturas_iniciales.xlsx')">&darr;</div>
							<div class="btn_div_upload_file2" onclick="close_ventana_upload_file('SF')">X</div>
						</div>
					</div>
				</div>
			</div>

			<?php }
			if($permiso_facturas_saldos =='false'){ ?>
				<div class="IconoPanelControl" onClick="AbreVentanaPanelGlobalContabilidadFacturasSaldos('facturas_saldos_iniciales/encabezado_facturas','Saldos iniciales de facturacion',0,0,0,70,'FacturasSaldosIniciales');">
					<div class="IconoPanelControlimg"><img src="../../temas/clasico/images/PanelControl/saldos_iniciales.png" width="44" height="44"></div>
					<div class="IconoPanelControltxt">Facturas Saldos Iniciales</div>
				</div>

			<?php }

				// if($permiso_facturas_saldos =='false'){ ?>
				<div class="IconoPanelControl" onClick="panelContabilidad_conciliacionBancos('conciliacion_bancos/conciliacion_bancos','Conciliacion Bancos',0,0,0,70,'conciliacionBancos');" style="display:none;">
					<div class="IconoPanelControlimg"><img src="../../temas/clasico/images/PanelControl/pagos.png" width="44" height="44"></div>
					<div class="IconoPanelControltxt">Conciliacion Bancos</div>
				</div>
			<?php
			// }
			if($permiso_amortizacion =='false'){?>
				<div class="IconoPanelControl" onClick="panelContabilidad_amortizaciones('amortizaciones/amortizaciones','Agregar Registro a Amortizar',0,0,0,70,'amortizaciones');">
					<div class="IconoPanelControlimg"><img src="../../temas/clasico/images/PanelControl/saldos_iniciales.png" width="44" height="44"></div>
					<div class="IconoPanelControltxt">Amortizaciones</div>
				</div>
			<?php
			} ?>
			<div class="IconoPanelControl" onClick="AbreVentanaPanelGlobalArchivosPlanosBancos('archivos_planos_bancos/archivos_planos_bancos','Archivos Planos Bancos',0,0);" style="display:none;">
				<div class="IconoPanelControlimg"><img src="../../temas/clasico/images/PanelControl/file-text44.png" width="44" height="44"></div>
				<div class="IconoPanelControltxt">Archivos Planos Bancos</div>
			</div>
		</div>
	</div>

<?php
	if($permiso_consulta_pos =='false' || $permiso_consulta_asientos_documento=='false' || $permiso_consulta_cuentas  =='false' || $permiso_consulta_asientos=='false' ){
	?>

	<!-- ---------------------------------- CONSULTAR ASIENTOS COLGAAP ---------------------------------------- -->
	<div class="ContenedorGrupoPanelControl">
		<div class="TituloPanelControl">
			Consultas
		</div>
		<div style="width:100%; float:left">
			<?php if ($permiso_consulta_pos=='false') { ?>
			<div class="IconoPanelControl" onClick="AbreVentanaPanelGlobalContabilidadDocumentosPos('consulta_pos_cuentas_colgaap/consulta_documentos','Consulta Tickets',745,485);">
				<div class="IconoPanelControlimg"><img src="img/consultar_pos.png" width="44" height="44"></div>
				<div class="IconoPanelControltxt">Consulta Tickets (POS)</div>
			</div>
			<?php }
			if($permiso_consulta_asientos_documento=='false'){
			 ?>
			<div class="IconoPanelControl" onClick="AbreVentanaPanelGlobalContabilidadDocumentos('consulta_documentos_cuentas_colgaap/consulta_documentos','Consulta Documentos Contabilizados',0,0);">
				<div class="IconoPanelControlimg"><img src="img/consulta_doc.png" width="44" height="44"></div>
				<div class="IconoPanelControltxt">Consulta Asientos Documentos</div>
			</div>
			<?php }
			if($permiso_consulta_cuentas=='false'){
			 ?>
			<div class="IconoPanelControl" onClick="AbreVentanaPanelGlobalContabilidadCuentasColgaap('consulta_cuentas_colgaap/consulta_cuentas_colgaap','Consulta Cuentas Colgaap',645,485,0,56);">
				<div class="IconoPanelControlimg"><img src="img/asientos_contables.png" width="44" height="44"></div>
				<div class="IconoPanelControltxt">Consulta Cuentas Contables</div>
			</div>
			<?php }
			if($permiso_consulta_cuentas=='false'){
			 ?>
			 <div class="IconoPanelControl" onClick="AbreVentanaPanelGlobalContabilidad('consulta_terceros_cuentas_colgaap/consulta_terceros','Consulta Asientos por Tercero',645,485);">
				<div class="IconoPanelControlimg"><img src="img/asientos_por_terceros.png" width="44" height="44"></div>
				<div class="IconoPanelControltxt">Consulta Asientos por tercero</div>
			</div>
			<?php }
			if($permiso_consulta_documento=='false'){
			 ?>
			<div class="IconoPanelControl" onClick="AbreVentanaPanelGlobalDocumentos('consulta_documentos/consulta_documentos','Consulta Documentos',0,0);">
				<div class="IconoPanelControlimg"><img src="img/consulta_doc.png" width="44" height="44"></div>
				<div class="IconoPanelControltxt">Consulta Documentos</div>
			</div>

			<div class="IconoPanelControl" onClick="prueba('new_grilla/new_grilla','Consulta Asientos por Tercero',645,485);" style="display:none">
				<div class="IconoPanelControlimg"><img src="img/asientos_por_terceros.png" width="44" height="44"></div>
				<div class="IconoPanelControltxt">PRUEBA NEW GRILLA</div>
			</div>

			<div class="IconoPanelControl" onClick="kanban('kanban/kanban','Kanban',645,485);" style="display:none;">
				<div class="IconoPanelControlimg"><img src="img/asientos_por_terceros.png" width="44" height="44"></div>
				<div class="IconoPanelControltxt">KANBAN</div>
			</div>
			<?php  } ?>
			<div class="IconoPanelControl" onClick="AbreVentanaPanelGlobalExportarExcel('consulta_documentos/consulta_documentos','Consulta Documentos',0,0);">
				<div class="IconoPanelControlimg"><img src="img/excel.png" width="44" height="44"></div>
				<div class="IconoPanelControltxt">Exportar asientos a Excel</div>
			</div>

		</div>

	</div>
<?php }

	if ($permiso_cierre_anual=='false' || $permiso_cierre_por_periodo=='false' || $permiso_auditoria=='false') {

 ?>

<!-- ---------------------------------- CONSULTAR ASIENTOS COLGAAP ---------------------------------------- -->
	<div class="ContenedorGrupoPanelControl">
		<div class="TituloPanelControl" style="width:96%;">
			Procesos Contables
		</div>

		<?php
		if($permiso_cierre_por_periodo=='false'){
	 	?>
		<div class="IconoPanelControl" onClick="ventanaCierrePeriodo()">
			<div class="IconoPanelControlimg"><img src="img/cierre_por_periodo.png" width="44" height="44"></div>
			<div class="IconoPanelControltxt">Cierre por periodo</div>
		</div>
		<?php }
		if($permiso_cierre_anual=='false'){
		?>
		<div class="IconoPanelControl" onClick="AbreVentanaPanelGlobalContabilidadNotaCierre('nota_cierre/notas_devolucion/devolucion_compra/grillaContable','Nota Cierre Fin de A&ntilde;o',0,0,0,70,'NotaCierre');">
			<div class="IconoPanelControlimg"><img src="img/nota_cierre.png" width="44" height="44"></div>
			<div class="IconoPanelControltxt">Nota Cierre Fin de A&ntilde;o</div>
		</div>
		<?php  }
		if($permiso_cierre_anual=='false'){ ?>
		<div class="IconoPanelControl" onClick="AbreVentanaPanelGlobalContabilidadNotaCierre('nota_cierre/notas_devolucion/devolucion_compra/grillaContable','Nota Cierre Fin de A&ntilde;o',0,0,0,70,'NotaCierre');" style="display:none;">
			<div class="IconoPanelControlimg"><img src="img/nota_cierre.png" width="44" height="44"></div>
			<div class="IconoPanelControltxt">Nota Cierre Fin de A&ntilde;o NIIF</div>
		</div>
		<?php }
		if ($permiso_auditoria=='false') {
		 ?>
		<div class="IconoPanelControl" onClick="ventanaAuditoria('auditoria_documentos/consulta_documentos','Auditoria Documentos',0,0);">
			<div class="IconoPanelControlimg"><img src="img/consulta_doc.png" width="44" height="44"></div>
			<div class="IconoPanelControltxt">Auditoria Documentos</div>
		</div>
		<?php } ?>
	</div>
<?php
}
 ?>
 	<div class="ContenedorGrupoPanelControl">
		<div class="TituloPanelControl" style="width:96%;">
			Documentos NIIF
		</div>
		<div class="IconoPanelControl" onClick="AbreVentanaPanelGlobalContabilidadDocumentosNiif('deterioro_cartera_clientes/deterioros','Consulta Documentos Contabilizados',0,0,'DeterioroCarteraClientes');">
			<div class="IconoPanelControlimg"><img src="img/deterioro.png" width="44" height="44"></div>
			<div class="IconoPanelControltxt">Deterioro Cartera Clientes</div>
		</div>
	</div>

	<?php if($modulosApi != ''){
		?>
		<div class="ContenedorGrupoPanelControl">
			<div class="TituloPanelControl" style="width:96%;">
				Conexiones Externas
			</div>
		<?php
		echo $modulosApi;
		?>
		</div>
	<?php
		}
	?>

	<div class="ContenedorGrupoPanelControl">
		<div class="TituloPanelControl" style="width:96%;">
			Herramientas Contables
		</div>

		<?php if($modulos != ''){ echo $modulos; } ?>
		<!--<div class="IconoPanelControl" onClick="AbreVentanaTrasladoSaldoTercero();">
			<div class="IconoPanelControlimg"><img src="img/traslado_terceros.png" width="44" height="44"></div>
			<div class="IconoPanelControltxt">Trasladar Saldo entre terceros</div>
		</div>
	</div> -->
</div>

<script>

	var globalNameFileUpload = '';

	function AbreVentanaPanelGlobalContabilidadCompras(archivo,titulo,ancho,alto,color,ancho_campo_carga,opcGrillaContable){
		if (color==0) {color='background-color:#fff;';}

		var myalto2  = Ext.getBody().getHeight();
		var myancho2 = Ext.getBody().getWidth();

		if(alto == 0){ WinAlto = myalto2-20; }
		else{ WinAlto = alto; }

		if(ancho == 0){ WinAncho = myancho2-30 }
		else{ WinAncho = ancho }

		Win_Panel_Global = new Ext.Window({
			width       : WinAncho,
			height      : WinAlto,
			title       : titulo,
			modal       : true,
			autoScroll  : true,
			autoDestroy : true,
			bodyStyle   : color,
			id          : 'contenedorPadreVentana'+opcGrillaContable,
			items       :
			[
				{
					closable    : false,
					border      : false,
					autoScroll  : true,
					iconCls     : '',
					bodyStyle   : 'background-image:url(\'img/MyInformesFondo.png\');',
					items       :
					[
						{
							xtype       : "panel",
							id          : 'contenedor_'+opcGrillaContable,
							border      : false,
							bodyStyle   : 'background-color:<?php echo $_SESSION['COLOR_FONDO'] ?>;',
						}
					],
					tbar        :
					[
						{
							xtype   : 'buttongroup',
							columns : 3,
							title   : 'Filtro Bodega',
							items   :
							[
								{
									xtype       : 'panel',
									border      : false,
									width       : 160,
									height      : 56,
									bodyStyle   : 'background-color:rgba(255,255,255,0);',
									autoLoad    :
									{
										url     : '../funciones_globales/filtros/filtro_unico_bodega.php',
										scripts : true,
										nocache : true,
										params  :
										{
											opc           : opcGrillaContable,
											imprimeVarPhp : 'opcGrillaContable : "'+opcGrillaContable+'"',
											renderizaBody : 'true',
											url_render    : 'notas_inventario/notas_devolucion/default.php',
										}
									}
								}
							]
						},
						{
							xtype   : 'buttongroup',
							columns : 3,
							title   : 'Cargar Factura',
							items   :
							[
								{
									xtype       : 'panel',
									border      : false,
									width       : 130,
									height      : ancho_campo_carga,
									bodyStyle   : 'background-color:rgba(255,255,255,0);',
									autoLoad    :
									{
										url     : 'notas_inventario/notas_devolucion/bd/bd.php',
										scripts : true,
										nocache : true,
										params  :
										{
											opc               : 'cargarCampoFacturaCompra',
											opcGrillaContable : opcGrillaContable
										}
									}
								}
							]
						},
						{
							xtype   : 'buttongroup',
							id      : 'BtnGroup_Guardar_DevolucionCompra',
							height  : 80,
							style   : 'border:none;',
							columns : 1,
							title   : 'Contabilizar',
							items   :
							[
								{
									xtype       : 'button',
									width       : 60,
									height      : 56,
									text        : 'Guardar',
									tooltip     : 'Generar Nota de Devolucion',
									id          : 'Btn_guardar_DevolucionCompra',
									scale       : 'large',
									iconCls     : 'guardar',
									iconAlign   : 'top',
									disabled    : true,
									handler     : function(){ BloqBtn(this); guardarDevolucionCompra() }
								}
							]
						},
						{
							xtype   : 'buttongroup',
							height  : 80,
							style   : 'border: none;',
							columns : 5,
							title   : 'Opciones',
							items   :
							[
								{
									xtype       : 'button',
									width       : 60,
									height      : 56,
									text        : 'Buscar',
									tooltip     : 'Buscar Nota de Devolucion',
									scale       : 'large',
									iconCls     : 'buscar_doc_new',
									iconAlign   : 'top',
									handler     : function(){ BloqBtn(this); buscarDevolucionCompra() }
								},
								{
									xtype       : 'button',
									width       : 60,
									height      : 56,
									id          : 'Btn_cancelar_DevolucionCompra',
									text        : 'Cancelar',
									tooltip     : 'Cancelar Nota de Devolucion',
									scale       : 'large',
									iconCls     : 'cancel',
									iconAlign   : 'top',
									handler     : function(){ BloqBtn(this); cancelarDevolucionCompra() }
								}
							]
						},'-',
						{
							xtype   : 'buttongroup',
							height  : 80,
							id      : 'BtnGroup_Estado1_DevolucionCompra',
							columns : 4,
							title   : 'Documento Generado',
							items   :
							[
								{
									xtype       : 'button',
									width       : 60,
									height      : 56,
									text        : 'Imprimir',
									id          : 'Btn_exportar_DevolucionCompra',
									tooltip     : 'Imprimir en un documento PDF',
									scale       : 'large',
									iconCls     : 'pdf32_new',
									iconAlign   : 'top',
									disabled    : true,
									handler     : function(){ BloqBtn(this); imprimirDevolucionCompra(); },
								},
								{
									xtype       : 'button',
									id          : 'Btn_editar_DevolucionCompra',
									width       : 60,
									height      : 56,
									text        : 'Editar',
									tooltip     : 'Editar Recibo',
									scale       : 'large',
									iconCls     : 'edit',
									iconAlign   : 'top',
									disabled    : true,
									handler     : function(){ BloqBtn(this); modificarDocumentoDevolucionCompra(); }
								},
								{
									xtype       : 'button',
									id          : 'Btn_restaurar_DevolucionCompra',
									width       : 60,
									height      : 56,
									text        : 'Restaurar',
									tooltip     : 'Restaurar Recibo',
									scale       : 'large',
									iconCls     : 'restaurar32',
									iconAlign   : 'top',
									disabled    : true,
									handler     : function(){ BloqBtn(this); restaurarDevolucionCompra(); }
								},
								{
									xtype       : 'button',
									id          : 'Btn_enviar_devolucion_electronica_DevolucionCompra',
									width       : 60,
									height      : 56,
									text        : 'Enviar a la DIAN',
									tooltip     : 'Enviar Devolucion de Compra a la DIAN',
									scale       : 'large',
									iconCls     : 'envia_doc',
									iconAlign   : 'top',
									disabled    : false,
									handler     : function(){ BloqBtn(this); enviarDIANDevolucionCompra(); }
								}
							]
						},'->',
						{
							xtype : "tbtext",
							text  : '<div id="titleDocumentoDevolucionCompra" style="text-align:center; font-size:18px; font-weight:bold;"></div>',
							scale : "large",
						}
					]
				}
			],

		}).show();
	}

	function AbreVentanaPanelGlobalContabilidadVentas(archivo,titulo,ancho,alto,color,ancho_campo_carga,opcGrillaContable){
		if (color==0) {color='background-color:#fff;';}

		var myalto2  = Ext.getBody().getHeight();
		var myancho2 = Ext.getBody().getWidth();

		if(alto == 0){ WinAlto = myalto2-20; }
		else{ WinAlto = alto; }

		if(ancho == 0){ WinAncho = myancho2-30 }
		else{ WinAncho = ancho }

		Win_Panel_Global = new Ext.Window({
			width       : WinAncho,
			height      : WinAlto,
			title       : titulo,
			modal       : true,
			autoScroll  : true,
			autoDestroy : true,
			id          : 'contenedorPadreVentana'+opcGrillaContable,
			// bodyStyle   : 'background-color:'+color,
			items       :
			[
				{
					closable    : false,
					border      : false,
					autoScroll  : true,
					iconCls     : '',
					bodyStyle   : 'background-image:url(\'img/MyInformesFondo.png\');',
					items       :
					[
						{
							xtype       : "panel",
							id          : 'contenedor_'+opcGrillaContable,
							border      : false,
							bodyStyle   : 'background-image:url(\'img/MyInformesFondo.png\');',
						}
					],
					tbar        :
					[
						{
							xtype   : 'buttongroup',
							height  : 80,
							columns : 3,
							title   : 'Filtro Bodega',
							items   :
							[
								{
									xtype       : 'panel',
									border      : false,
									width       : 160,
									height      : 56,
									bodyStyle   : 'background-color:rgba(255,255,255,0);',
									autoLoad    :
									{
										url     : '../funciones_globales/filtros/filtro_unico_bodega.php',
										scripts : true,
										nocache : true,
										params  :
										{
											opc           : opcGrillaContable,
											imprimeVarPhp : 'opcGrillaContable : "'+opcGrillaContable+'"',
											renderizaBody : 'true',
											url_render    : 'notas_inventario/notas_devolucion/default.php',
										}
									}
								}
							]
						},
						{
							xtype   : 'buttongroup',
							height  : 80,
							columns : 3,
							title   : 'Cargar Documento',
							items   :
							[
								{
									xtype       : 'panel',
									border      : false,
									width       : 130,
									height      : ancho_campo_carga,
									bodyStyle   : 'background-color:rgba(255,255,255,0);',
									autoLoad    :
									{
										url     : 'notas_inventario/notas_devolucion/bd/bd.php',
										scripts : true,
										nocache : true,
										params  :
										{
											opc               : 'cargarCampoFacturaCompra',
											opcGrillaContable : opcGrillaContable
										}
									}
								}
							]
						},
						{
							xtype   : 'buttongroup',
							id      : 'BtnGroup_Guardar_DevolucionVenta',
							height  : 80,
							style   : 'border:none;',
							columns : 1,
							title   : 'Contabilizar',
							items   :
							[
								{
									xtype       : 'button',
									width       : 60,
									height      : 56,
									text        : 'Guardar',
									tooltip     : 'Generar Nota de Devolucion',
									id          : 'Btn_guardar_DevolucionVenta',
									scale       : 'large',
									iconCls     : 'guardar',
									iconAlign   : 'top',
									disabled    : true,
									handler     : function(){ BloqBtn(this); guardarDevolucionVenta() }
								}
							]
						},
						{
							xtype   : 'buttongroup',
							height  : 84,
							style   : 'border: none;',
							columns : 5,
							title   : 'Opciones',
							items   :
							[
								{
									xtype       : 'button',
									width       : 60,
									height      : 56,
									text        : 'Buscar',
									tooltip     : 'Buscar Nota de Devolucion',
									scale       : 'large',
									iconCls     : 'buscar_doc_new',
									iconAlign   : 'top',
									handler     : function(){ BloqBtn(this); buscarDevolucionVenta() }
								},
				     //            {
				     //                xtype       : 'button',
				     //                id 			: 'Btn_itemsGrupos_DevolucionVenta',
				     //                width		: 60,
									// height		: 60,
				     //                text        : 'Nuevo Grupo <br>de Items',
				     //                tooltip		: 'Agregar Grupos de Items',
				     //                scale       : 'large',
				     //                iconCls     : 'btnGroups',
				     //                iconAlign   : 'top',
				     //                handler     : function(){ BloqBtn(this); ventanaAgregarAgrupacionItems() }
				     //            },
								{
									xtype       : 'button',
									width       : 60,
									height      : 56,
									id          : 'Btn_cancelar_DevolucionVenta',
									text        : 'Cancelar',
									tooltip     : 'Cancelar Nota de Devolucion',
									scale       : 'large',
									iconCls     : 'cancel',
									iconAlign   : 'top',
									handler     : function(){ BloqBtn(this); cancelarDevolucionVenta() }
								}
							]
						},'-',
						{
							xtype   : 'buttongroup',
							height  : 80,
							id      : 'BtnGroup_Estado1_DevolucionVenta',
							columns : 4,
							title   : 'Documento Generado',
							items   :
							[
								{
									xtype       : 'button',
									width       : 60,
									height      : 56,
									text        : 'Imprimir',
									id          : 'Btn_exportar_DevolucionVenta',
									tooltip     : 'Imprimir en un documento PDF',
									scale       : 'large',
									iconCls     : 'pdf32_new',
									iconAlign   : 'top',
									disabled    : true,
									handler     : function(){ BloqBtn(this); imprimirDevolucionVenta(); },
								},
								{
									xtype       : 'button',
									id          : 'Btn_editar_DevolucionVenta',
									width       : 60,
									height      : 56,
									text        : 'Editar',
									tooltip     : 'Editar Recibo',
									scale       : 'large',
									iconCls     : 'edit',
									iconAlign   : 'top',
									disabled    : true,
									handler     : function(){ BloqBtn(this); modificarDocumentoDevolucionVenta(); }
								},
								{
									xtype       : 'button',
									id          : 'Btn_restaurar_DevolucionVenta',
									width       : 60,
									height      : 56,
									text        : 'Restaurar',
									tooltip     : 'Restaurar Recibo',
									scale       : 'large',
									iconCls     : 'restaurar32',
									iconAlign   : 'top',
									disabled    : true,
									handler     : function(){ BloqBtn(this); restaurarDevolucionVenta(); }
								},
								{
									xtype       : 'button',
									id          : 'Btn_enviar_devolucion_electronica_DevolucionVenta',
									width       : 60,
									height      : 56,
									text        : 'Enviar a la DIAN',
									tooltip     : 'Enviar Devolucion de Venta a la DIAN',
									scale       : 'large',
									iconCls     : 'envia_doc',
									iconAlign   : 'top',
									disabled    : false,
									handler     : function(){ BloqBtn(this); enviarDIANDevolucionVenta(); }
								}
							]
						},'->',
						{
							xtype : "tbtext",
							text  : '<div id="titleDocumentoDevolucionVenta" style="text-align:center; font-size:18px; font-weight:bold;"></div>',
							scale : "large",
						}
					]
				}
			],

		}).show();
	}

	function AbreVentanaPanelGlobalContabilidadNotaGeneral(archivo,titulo,ancho,alto,color,ancho_campo_carga,opcGrillaContable){
		if (color==0) {color='background-color:#fff;';}

		var myalto2  = Ext.getBody().getHeight();
		var myancho2 = Ext.getBody().getWidth();

		url_render_colgaap        = 'nota_general/grilla/grillaContable.php';
		url_render_niif           = 'nota_general_niif/grilla/grillaContable.php';
		opcGrillaContable_colgaap = 'NotaGeneral';
		opcGrillaContable_niif    = 'NotaGeneralNiif';

		if(alto == 0){ WinAlto = myalto2-20; }
		else{ WinAlto = alto; }

		if(ancho == 0){ WinAncho = myancho2-30 }
		else{ WinAncho = ancho }

		Win_Panel_Global = new Ext.Window({
			width       : WinAncho,
			height      : WinAlto,
			title       : titulo,
			modal       : true,
			autoScroll  : false,
			autoDestroy : true,
			bodyStyle   : color,
			items       :
			[
				{
					closable    : false,
					border      : false,
					autoScroll  : true,
					iconCls     : '',
					bodyStyle   : 'background-color:#FFF;',
					items       :
					[
						{
							xtype       : "panel",
							id          : 'contenedor_'+opcGrillaContable,
							border      : false,
							bodyStyle   : 'background-color:#FFF;',
							// autoLoad    :
							// {
							//     url     : 'nota_general/grilla/grillaContable.php',
							//     scripts : true,
							//     nocache : true,
							//     params  : { opcGrillaContable : opcGrillaContable }
							// }
						}
					],
					tbar        :
					[

						{
							xtype   : 'buttongroup',
							columns : 3,
							title   : 'Filtro Contabilidad',
							items   :
							[
								{
									xtype       : 'panel',
									border      : false,
									width       : 160,
									height      : 56,
									bodyStyle   : 'background-color:rgba(255,255,255,0)',
									autoLoad    :
									{
										url     : '../funciones_globales/filtros/filtro_tipo_contabilidad.php',
										scripts : true,
										nocache : true,
										params  :
										{
											opc                       : "NotaGeneral",
											// contenedor             : 'contenedor_'+opcGrillaContable,
											imprimeScriptPhp          : 'document.getElementById("titleDocumentoNotaGeneral").innerHTML="";',
											renderizaBody             : 'true',
											url_render                : 'nota_general/grilla/grillaContable.php',
											opcGrillaContable         : "NotaGeneral",
											url_render_colgaap        : url_render_colgaap,
											url_render_niif           : url_render_niif,
											opcGrillaContable_colgaap : opcGrillaContable_colgaap,
											opcGrillaContable_niif    : opcGrillaContable_niif,
										}
									}
								}
							]
						},
						{
							xtype   : 'buttongroup',
							id      : 'BtnGroup_Guardar_NotaGeneral',
							height  : 80,
							style   : 'border:none;',
							columns : 1,
							title   : 'Generar',
							items   :
							[
								{
									xtype       : 'button',
									width       : 60,
									height      : 56,
									text        : 'Guardar',
									tooltip     : 'Generar Nota',
									id          : 'Btn_guardar_NotaGeneral',
									scale       : 'large',
									iconCls     : 'guardar',
									iconAlign   : 'top',
									disabled    : true,
									handler     : function(){ BloqBtn(this); validarNotaGeneral() }
								}
							]
						},
						{
							xtype   : 'buttongroup',
							height  : 80,
							style   : 'border:none;',
							columns : 9,
							title   : 'Opciones',
							items   :
							[
								{
									xtype       : 'button',
									width       : 60,
									height      : 56,
									text        : 'Nueva',
									tooltip     : 'Nueva Nota',
									scale       : 'large',
									iconCls     : 'add_new',
									id          : 'Btn_nueva_NotaGeneral',
									iconAlign   : 'top',
									disabled    : true,
									handler     : function(){ BloqBtn(this); nuevaNotaGeneral() }
								},
								{
									xtype       : 'button',
									width       : 60,
									height      : 56,
									text        : 'Buscar',
									tooltip     : 'Buscar Nota Creada',
									scale       : 'large',
									iconCls     : 'buscar_doc_new',
									iconAlign   : 'top',
									handler     : function(){ BloqBtn(this); buscarNotaGeneral() }
								},
								{
									xtype       : 'button',
									width       : 60,
									height      : 56,
									id          : 'Btn_cancelar_NotaGeneral',
									text        : 'Cancelar',
									tooltip     : 'Cancelar Nota',
									scale       : 'large',
									iconCls     : 'cancel',
									iconAlign   : 'top',
									handler     : function(){ BloqBtn(this); cancelarNotaGeneral() }
								},
								{
									xtype     : "splitbutton",
									id        : 'Btn_load_excel_body_nota',
									tooltip   : 'Cargar Excel',
									iconCls   : "upload_file32",
									scale     : "large",
									iconAlign : 'top',
									text      : 'Cargar Excel',
									handler   : function(){  BloqBtn(this); windows_upload_excel(); },
						            menu:
						            [
					            		{
											text    : "Descargar Formato",
											iconCls : "xls16",
											handler : function(){ BloqBtn(this); window.open('nota_general/bd/formato_cuentas_nota.xlsx');  }
					            		}
						          	]
						        },
								// {
								// 	xtype       : 'button',
								// 	width       : 60,
								// 	height      : 56,
								// 	id          : 'Btn_load_excel_body_nota',
								// 	text        : 'Cargar Excel',
								// 	tooltip     : 'Cargar Excel',
								// 	scale       : 'large',
								// 	iconCls     : 'upload_file32',
								// 	iconAlign   : 'top',
								// 	handler     : function(){ BloqBtn(this); windows_upload_excel(); }
								// }
							]
						},'-',
						{
							xtype   : 'buttongroup',
							height  : 80,
							id      : 'BtnGroup_Estado1_NotaGeneral',
							columns : 4,
							title   : 'Documento Generado',
							items   :
							[
								{
									xtype       : 'button',
									width       : 60,
									height      : 56,
									text        : 'Inventario',
									id          : 'Btn_articulos_Relacionados',
									tooltip     : 'Consultar Inventario Relacionado',
									scale       : 'large',
									iconCls     : 'inventario32',
									iconAlign   : 'top',
									disabled    : true,
									handler     : function(){BloqBtn(this); ventanaArticulosRelacionados(); },
								},
								{
									xtype       : 'splitbutton',
									id          : 'Btn_exportar_NotaGeneral',
									width       : 60,
									height      : 56,
									text        : 'Imprimir',
									tooltip     : 'Imprimir en un documento PDF',
									scale       : 'large',
									iconCls     : 'pdf32_new',
									iconAlign   : 'top',
									handler     : function(){ BloqBtn(this); imprimirNotaGeneral(); },
									 menu       :
									[
										{
											text    : '<b>Imprimir Cuentas Niif</b>',
											iconCls : 'pdf16',
											handler : function(){ BloqBtn(this); imprimirNotaGeneral('niif'); }
										}
									]

								},
								{
									xtype       : 'button',
									id          : 'Btn_editar_NotaGeneral',
									width       : 60,
									height      : 56,
									text        : 'Editar',
									tooltip     : 'Editar Nota General',
									scale       : 'large',
									iconCls     : 'edit',
									iconAlign   : 'top',
									handler     : function(){ BloqBtn(this); modificarDocumentoNotaGeneral(); }
								},
								{
									xtype       : 'button',
									id          : 'Btn_restaurar_NotaGeneral',
									width       : 60,
									height      : 56,
									text        : 'Restaurar',
									tooltip     : 'Restaurar Nota General',
									scale       : 'large',
									iconCls     : 'restaurar32',
									iconAlign   : 'top',
									handler     : function(){ BloqBtn(this); restaurarNotaGeneral(); }
								}
							]
						},
						{
							xtype   : 'buttongroup',
							id      : 'GroupBtnSync',
							height  : 93,
							style   : 'border:none;',
							columns : 1,
							title   : 'Contabilizar',
							items   :
							[
								{
									xtype       : 'button',
									width       : 60,
									height      : 56,
									text        : 'Mover<br>Cuentas Niif',
									tooltip     : 'Generar Nota con asientos niif automaticamente',
									id          : 'BtnSync',
									scale       : 'large',
									iconCls     : 'sync',
									iconAlign   : 'top',
									handler     : function(){ BloqBtn(this); cambiaSyncNota('colgaap_niif') }
								}
							]
						},
						{
							xtype   : 'buttongroup',
							id      : 'GroupBtnNoSync',
							height  : 93,
							style   : 'border:none;',
							columns : 1,
							title   : 'Contabilizar',
							items   :
							[
								{
									xtype       : 'button',
									width       : 60,
									height      : 56,
									text        : 'No Mover <br>Cuentas Niif',
									tooltip     : 'Generar Nota solo con asientos colgaap',
									id          : 'BtnNoSync',
									scale       : 'large',
									iconCls     : 'no_sync',
									iconAlign   : 'top',
									handler     : function(){ BloqBtn(this); cambiaSyncNota('colgaap') }
								}
							]
						},
						'->',
						{
							xtype : "tbtext",
							text  : '<div id="titleDocumentoNotaGeneral" style="text-align:center; font-size:18px; font-weight:bold;"></div>',
							scale : "large",
						}
					]
				}
			],
		}).show();
	}

	function AbreVentanaPanelGlobalContabilidadNotaCierre(archivo,titulo,ancho,alto,color,ancho_campo_carga,opcGrillaContable){
		if (color==0) {color='background-color:#fff;';}

		var myalto2  = Ext.getBody().getHeight();
		var myancho2 = Ext.getBody().getWidth();

		// url_render_colgaap        = 'nota_cierre/grilla/grillaContable.php';
		// url_render_niif           = 'nota_general_niif/grilla/grillaContable.php';
		// opcGrillaContable_colgaap = 'NotaGeneral';
		// opcGrillaContable_niif    = 'NotaGeneralNiif';

		if(alto == 0){ WinAlto = myalto2-20; }
		else{ WinAlto = alto; }

		if(ancho == 0){ WinAncho = myancho2-30 }
		else{ WinAncho = ancho }

		Win_Panel_Global = new Ext.Window({
			width       : WinAncho,
			height      : WinAlto,
			title       : titulo,
			modal       : true,
			autoScroll  : false,
			autoDestroy : true,
			bodyStyle   : color,
			items       :
			[
				{
					closable    : false,
					border      : false,
					autoScroll  : true,
					iconCls     : '',
					bodyStyle   : 'background-color:#FFF;',
					items       :
					[
						{
							xtype       : "panel",
							id          : 'contenedor_'+opcGrillaContable,
							border      : false,
							bodyStyle   : 'background-color:#FFF;',
							autoLoad    :
							{
								url     : 'nota_cierre/grilla/grillaContable.php',
								scripts : true,
								nocache : true,
								params  : { opcGrillaContable : opcGrillaContable }
							}
						}
					],
					tbar        :
					[

						// {
						//     xtype   : 'buttongroup',
						//     columns : 3,
						//     title   : 'Filtro Contabilidad',
						//     items   :
						//     [
						//         {
						//             xtype       : 'panel',
						//             border      : false,
						//             width       : 160,
						//             height      : 56,
						//             bodyStyle   : 'background-color:rgba(255,255,255,0)',
						//             autoLoad    :
						//             {
						//                 url     : '../funciones_globales/filtros/filtro_tipo_contabilidad.php',
						//                 scripts : true,
						//                 nocache : true,
						//                 params  :
						//                 {
						//                     opc                       : "NotaGeneral",
						//                     // contenedor             : 'contenedor_'+opcGrillaContable,
						//                     imprimeScriptPhp          : 'document.getElementById("titleDocumentoNotaGeneral").innerHTML="";',
						//                     renderizaBody             : 'true',
						//                     url_render                : 'nota_general/grilla/grillaContable.php',
						//                     opcGrillaContable         : "NotaGeneral",
						//                     url_render_colgaap        : url_render_colgaap,
						//                     url_render_niif           : url_render_niif,
						//                     opcGrillaContable_colgaap : opcGrillaContable_colgaap,
						//                     opcGrillaContable_niif    : opcGrillaContable_niif,
						//                 }
						//             }
						//         }
						//     ]
						// },
						{
							xtype   : 'buttongroup',
							id      : 'BtnGroup_Guardar_NotaCierre',
							height  : 80,
							style   : 'border:none;',
							columns : 1,
							title   : 'Generar',
							items   :
							[
								{
									xtype       : 'button',
									width       : 60,
									height      : 56,
									text        : 'Guardar',
									tooltip     : 'Generar Nota',
									id          : 'Btn_guardar_NotaCierre',
									scale       : 'large',
									iconCls     : 'guardar',
									iconAlign   : 'top',
									disabled    : true,
									handler     : function(){ BloqBtn(this); validarNotaCierre() }
								}
							]
						},
						{
							xtype   : 'buttongroup',
							height  : 80,
							style   : 'border:none;',
							columns : 9,
							title   : 'Opciones',
							items   :
							[
								{
									xtype       : 'button',
									width       : 60,
									height      : 56,
									text        : 'Nueva',
									tooltip     : 'Nueva Nota',
									scale       : 'large',
									iconCls     : 'add_new',
									id          : 'Btn_nueva_NotaCierre',
									iconAlign   : 'top',
									disabled    : true,
									handler     : function(){ BloqBtn(this); nuevaNotaCierre() }
								},
								{
									xtype       : 'button',
									width       : 60,
									height      : 56,
									text        : 'Buscar',
									tooltip     : 'Buscar Nota Creada',
									scale       : 'large',
									iconCls     : 'buscar_doc_new',
									iconAlign   : 'top',
									handler     : function(){ BloqBtn(this); buscarNotaCierre() }
								},
								{
									xtype       : 'button',
									width       : 60,
									height      : 56,
									id          : 'Btn_cancelar_NotaCierre',
									text        : 'Cancelar',
									tooltip     : 'Cancelar Nota',
									scale       : 'large',
									iconCls     : 'cancel',
									iconAlign   : 'top',
									disabled    : <?php echo $permiso_cierre_anual_cancelar; ?>,
									handler     : function(){ BloqBtn(this); cancelarNotaCierre() }
								},
								// {
								//     xtype       : 'button',
								//     width       : 60,
								//     height      : 56,
								//     id          : 'Btn_cargar_cuentas_NotaCierre',
								//     text        : 'Cargar Cuentas<br>de Cierre',
								//     tooltip     : 'Cargar cuentas para el Cierre',
								//     scale       : 'large',
								//     iconCls     : 'carga_doc',
								//     iconAlign   : 'top',
								//     handler     : function(){ BloqBtn(this); }
								// }
								// ,
								// {
								//     xtype       : 'button',
								//     width       : 60,
								//     height      : 56,
								//     id          : 'Btn_load_excel_body_nota',
								//     text        : 'Cargar Excel',
								//     tooltip     : 'Cargar Excel',
								//     scale       : 'large',
								//     iconCls     : 'upload_file32',
								//     iconAlign   : 'top',
								//     handler     : function(){ BloqBtn(this); windows_upload_excel(); }
								// }
							]
						},'-',
						{
							xtype   : 'buttongroup',
							height  : 80,
							id      : 'BtnGroup_Estado1_NotaCierre',
							columns : 4,
							title   : 'Documento Generado',
							items   :
							[
								// {
								//     xtype       : 'button',
								//     width       : 60,
								//     height      : 56,
								//     text        : 'Inventario',
								//     id          : 'Btn_articulos_Relacionados',
								//     tooltip     : 'Consultar Inventario Relacionado',
								//     scale       : 'large',
								//     iconCls     : 'inventario32',
								//     iconAlign   : 'top',
								//     disabled    : true,
								//     handler     : function(){BloqBtn(this); ventanaArticulosRelacionados(); },
								// },
								{
									xtype       : 'splitbutton',
									id          : 'Btn_exportar_NotaCierre',
									width       : 60,
									height      : 56,
									text        : 'Imprimir',
									tooltip     : 'Imprimir en un documento PDF',
									scale       : 'large',
									iconCls     : 'pdf32_new',
									iconAlign   : 'top',
									handler     : function(){ BloqBtn(this); imprimirNotaCierre(); },
									 menu       :
									[
										{
											text    : '<b>Imprimir Cuentas Niif</b>',
											iconCls : 'pdf16',
											handler : function(){ BloqBtn(this); imprimirNotaCierre('niif'); }
										}
									]

								},
								{
									xtype       : 'button',
									id          : 'Btn_editar_NotaCierre',
									width       : 60,
									height      : 56,
									text        : 'Editar',
									tooltip     : 'Editar Nota General',
									scale       : 'large',
									iconCls     : 'edit',
									iconAlign   : 'top',
									handler     : function(){ BloqBtn(this); modificarDocumentoNotaCierre(); }
								},
								{
									xtype       : 'button',
									id          : 'Btn_restaurar_NotaCierre',
									width       : 60,
									height      : 56,
									text        : 'Restaurar',
									tooltip     : 'Restaurar Nota General',
									scale       : 'large',
									iconCls     : 'restaurar32',
									iconAlign   : 'top',
									handler     : function(){ BloqBtn(this); restaurarNotaCierre(); }
								}
							]
						},
						'->',
						{
							xtype : "tbtext",
							text  : '<div id="titleDocumentoNotaCierre" style="text-align:center; font-size:18px; font-weight:bold;"></div>',
							scale : "large",
						}
					]
				}
			],
		}).show();
	}

	function panelContabilidad_conciliacionBancos(){

		Win_Ventana_conciliacion_bancaria = new Ext.Window({
			width       : 300,
			height      : 150,
			id          : 'Win_Ventana_conciliacion_bancaria',
			title       : 'Conciliacion Bancaria ',
			modal       : true,
			autoScroll  : false,
			closable    : true,
			autoDestroy : true,
			autoLoad    :
			{
				url     : 'conciliacion_bancaria/panel_conciliacion_bancaria.php',
				scripts : true,
				nocache : true,
			}
		}).show();
	}

	function panelContabilidad_amortizaciones(){

		Win_Ventana_amortizaciones = new Ext.Window({
			width       : 300,
			height      : 150,
			id          : 'Win_Ventana_amortizaciones',
			title       : 'Amortizaciones',
			modal       : true,
			autoScroll  : false,
			closable    : true,
			autoDestroy : true,
			autoLoad    :
			{
				url     : 'amortizaciones/panel_amortizaciones.php',
				scripts : true,
				nocache : true,
			}
		}).show();
	}

	function AbreVentanaPanelGlobalArchivosPlanosBancos(){
		var myalto  = Ext.getBody().getHeight();
		var myancho = Ext.getBody().getWidth();

		Win_Ventana_panel_archivos_planos_bancos = new Ext.Window({
			width       : 290,
			height      : 295,
			id          : 'Win_Ventana_panel_archivos_planos_bancos',
			title       : 'Generar Archivo Plano',
			modal       : true,
			autoScroll  : false,
			closable    : false,
			autoDestroy : true,
			autoLoad    : {
											url     : 'archivos_planos_bancos/archivos_planos_bancos.php',
											scripts : true,
											nocache : true,
											params  : {}
										},
			tbar        : [
											{
												xtype   : 'buttongroup',
												columns : 2,
												title   : 'Exportar',
												style   : 'border-right:none;',
												items   :	[
																		{
																			xtype       : 'button',
																			width       : 60,
																			height      : 56,
																			text        : 'Generar',
																			scale       : 'large',
																			iconCls     : 'genera_informe',
																			iconAlign   : 'top',
																			hidden      : false,
																			handler     : function(){ BloqBtn(this); genera_archivo_plano('false'); }
																		},
																		{
																			xtype       : 'button',
																			width       : 60,
																			height      : 56,
																			text        : 'Regenerar',
																			scale       : 'large',
																			iconCls     : 'genera_informe',
																			iconAlign   : 'top',
																			hidden      : false,
																			handler     : function(){ BloqBtn(this); genera_archivo_plano('true'); }
																		}
																	]
											},'-',
											{
												xtype   : 'buttongroup',
												columns : 1,
												title   : 'Opciones',
												style   : 'border-right:none;',
												items   :	[
																		{
																			xtype       : 'button',
																			width       : 60,
																			height      : 56,
																			text        : 'Regresar',
																			scale       : 'large',
																			iconCls     : 'regresar',
																			iconAlign   : 'top',
																			hidden      : false,
																			handler     : function(){ BloqBtn(this); Win_Ventana_panel_archivos_planos_bancos.close(id) }
																		}
																	]
											},
										]
		}).show();
	}

	function nuevaNotaGeneral(){
		document.getElementById('titleDocumentoNotaGeneral').innerHTML='';
		var filtro_tipo_contabilidad_NotaGeneral = document.getElementById('filtro_tipo_contabilidad_NotaGeneral').value;
		var url_render = '';

		if (filtro_tipo_contabilidad_NotaGeneral=='colgaap') { url_render='nota_general/grilla/grillaContable.php'; }
		else{ url_render='nota_general_niif/grilla/grillaContable.php'; }

		Ext.getCmp("Btn_nueva_NotaGeneral").disable();
		Ext.get("contenedor_NotaGeneral").load({
			url     : url_render,
			scripts : true,
			nocache : true,
			params  :
			{
				opcGrillaContable : "NotaGeneral",
			}
		});
	}

	function nuevaNotaCierre(){
		document.getElementById('titleDocumentoNotaCierre').innerHTML='';
		// var filtro_tipo_contabilidad_NotaGeneral = document.getElementById('filtro_tipo_contabilidad_NotaGeneral').value;
		var url_render = '';

		// if (filtro_tipo_contabilidad_NotaGeneral=='colgaap') { url_render='nota_general/grilla/grillaContable.php'; }
		// else{ url_render='nota_general_niif/grilla/grillaContable.php'; }

		Ext.getCmp("Btn_nueva_NotaCierre").disable();
		Ext.get("contenedor_NotaCierre").load({
			url     : 'nota_cierre/grilla/grillaContable.php',
			scripts : true,
			nocache : true,
			params  :
			{
				opcGrillaContable : "NotaCierre",
			}
		});
	}

	//CONSULTA ASIENTOS POR TERCERO
	function AbreVentanaPanelGlobalContabilidad(archivo,titulo,ancho,alto){
		var myalto2  = Ext.getBody().getHeight();
		var myancho2 = Ext.getBody().getWidth();

		if(alto == 0){ WinAlto = myalto2-20; }
		else{ WinAlto = alto; }

		if(ancho == 0){ WinAncho = myancho2-30 }
		else{ WinAncho = ancho }

		Win_Panel_Global = new Ext.Window({
			width       : WinAncho,
			height      : WinAlto,
			title       : titulo,
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
							id          : 'contenedor_Win_Panel_Global',
							border      : false,
							bodyStyle   : 'background-color:<?php echo $_SESSION['COLOR_FONDO'] ?>;',
							autoLoad    :
							{
								url     : archivo+'.php',
								scripts : true,
								nocache : true,
								params  : { fecha_inicial : '', fecha_final : '' }
							},
						}
					],
					tbar        :
					[
						{
							xtype   : 'buttongroup',
							columns : 3,
							title   : 'Filtro Fechas',
							items   :
							[
								{
									xtype       : 'panel',
									border      : false,
									width       : 200,
									height      : 56,
									bodyStyle   : 'background-color:rgba(255,255,255,0);',
									autoLoad    :
									{
										url     : 'bd/bd.php',
										scripts : true,
										nocache : true,
										params  : { opc  : 'panel_filtro_fechas' }
									}
								}
							]
						},
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
									text        : 'Consultar',
									scale       : 'large',
									iconCls     : 'genera_informe',
									iconAlign   : 'top',
									handler     : function(){ BloqBtn(this); generarBusquedaGlobal(archivo+'.php', 'panel_filtro_fechas') }
								},
								{
									xtype       : 'button',
									width       : 60,
									height      : 56,
									text        : 'Imprimir',
									scale       : 'large',
									iconCls     : 'genera_pdf',
									iconAlign   : 'top',
									handler     : function(){ BloqBtn(this); imprimirBusquedaPricipal(); }
								},
								{
									xtype       : 'button',
									width       : 60,
									height      : 56,
									text        : 'Regresar',
									scale       : 'large',
									iconCls     : 'regresar',
									iconAlign   : 'top',
									handler     : function(){ Win_Panel_Global.close() }
								}
							]
						}
					]
				}
			]
		}).show();
	}

	//CONSULTA ASIENTOS POR TERCERO
	function AbreVentanaPanelGlobalContabilidad(archivo,titulo,ancho,alto){
		var myalto2  = Ext.getBody().getHeight();
		var myancho2 = Ext.getBody().getWidth();

		if(alto == 0){ WinAlto = myalto2-20; }
		else{ WinAlto = alto; }

		if(ancho == 0){ WinAncho = myancho2-30 }
		else{ WinAncho = ancho }

		Win_Panel_Global = new Ext.Window({
			width       : WinAncho,
			height      : WinAlto,
			title       : titulo,
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
							id          : 'contenedor_Win_Panel_Global',
							border      : false,
							bodyStyle   : 'background-color:<?php echo $_SESSION['COLOR_FONDO'] ?>;',
							autoLoad    :
							{
								url     : archivo+'.php',
								scripts : true,
								nocache : true,
								params  : { fecha_inicial : '', fecha_final : '' }
							},
						}
					],
					tbar        :
					[
						{
							xtype   : 'buttongroup',
							columns : 3,
							title   : 'Filtro Fechas',
							items   :
							[
								{
									xtype       : 'panel',
									border      : false,
									width       : 200,
									height      : 56,
									bodyStyle   : 'background-color:rgba(255,255,255,0);',
									autoLoad    :
									{
										url     : 'bd/bd.php',
										scripts : true,
										nocache : true,
										params  : { opc  : 'panel_filtro_fechas' }
									}
								}
							]
						},
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
									text        : 'Consultar',
									scale       : 'large',
									iconCls     : 'genera_informe',
									iconAlign   : 'top',
									handler     : function(){ BloqBtn(this); generarBusquedaGlobal(archivo+'.php', 'panel_filtro_fechas') }
								},
								{
									xtype       : 'button',
									width       : 60,
									height      : 56,
									text        : 'Imprimir',
									scale       : 'large',
									iconCls     : 'genera_pdf',
									iconAlign   : 'top',
									handler     : function(){ BloqBtn(this); imprimirBusquedaPricipal(); }
								},
								{
									xtype       : 'button',
									width       : 60,
									height      : 56,
									text        : 'Regresar',
									scale       : 'large',
									iconCls     : 'regresar',
									iconAlign   : 'top',
									handler     : function(){ Win_Panel_Global.close() }
								}
							]
						}
					]
				}
			]
		}).show();
	}

	//CONSULTA DOCUMENTOS Y TICKETS
	function AbreVentanaPanelGlobalContabilidadDocumentosPos(archivo,titulo,ancho,alto){
		var myalto2  = Ext.getBody().getHeight();
		var myancho2 = Ext.getBody().getWidth();

		if(alto == 0){ WinAlto = myalto2-20; }
		else{ WinAlto = alto; }

		if(ancho == 0){ WinAncho = myancho2-30 }
		else{ WinAncho = ancho }

		Win_Panel_Global = new Ext.Window({
			width       : 800,
			height      : WinAlto,
			title       : titulo,
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
							id          : 'contenedor_Win_Panel_Global',
							border      : false,
							bodyStyle   : 'background-color:<?php echo $_SESSION['COLOR_FONDO'] ?>;',
							autoLoad    :
							{
								url     : archivo+'.php',
								scripts : true,
								nocache : true,
								params  : { fecha_inicial : '', fecha_final : '', filtro_sucursal : "<?php echo $_SESSION['SUCURSAL']; ?>" }
							},
						}
					],
					tbar        :
					[
						{
							xtype   : 'buttongroup',
							columns : 3,
							title   : 'Filtro Fechas',
							items   :
							[
								{
									xtype       : 'panel',
									border      : false,
									width       : 200,
									height      : 56,
									bodyStyle   : 'background-color:rgba(255,255,255,0);',
									autoLoad    :
									{
										url     : 'bd/bd.php',
										scripts : true,
										nocache : true,
										params  : { opc  : 'panel_filtro_fechas' }
									}
								}
							]
						},
						{
							xtype   : 'buttongroup',
							columns : 3,
							title   : 'Filtro Sucursal',
							items   :
							[
								{
									xtype       : 'panel',
									border      : false,
									width       : 160,
									height      : 56,
									bodyStyle   : 'background-color:rgba(255,255,255,0);',
									autoLoad    :
									{
										url     : '../funciones_globales/filtros/filtro_unico_sucursal_contabilidad_documentos.php',
										scripts : true,
										nocache : true,
										params  : { opc  : 'panel_filtro_sucursal' }
									}
								}
							]
						},
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
									text        : 'Consultar',
									scale       : 'large',
									iconCls     : 'genera_informe',
									iconAlign   : 'top',
									handler     : function(){ BloqBtn(this); generarBusquedaGlobal(archivo+'.php', 'panel_filtro_sucursal') }
								},
								{
									xtype       : 'button',
									width       : 60,
									height      : 56,
									text        : 'Imprimir',
									scale       : 'large',
									iconCls     : 'genera_pdf',
									iconAlign   : 'top',
									handler     : function(){ BloqBtn(this); imprimirBusquedaPricipal(); }
								},
								{
									xtype       : 'button',
									width       : 60,
									height      : 56,
									text        : 'Regresar',
									scale       : 'large',
									iconCls     : 'regresar',
									iconAlign   : 'top',
									handler     : function(){ Win_Panel_Global.close() }
								}
							]
						}
					]
				}
			]
		}).show();
	}

	//CONSULTA DOCUMENTOS Y TICKETS
	function AbreVentanaPanelGlobalContabilidadDocumentos(archivo,titulo,ancho,alto){
		var myalto  = Ext.getBody().getHeight();
		var myancho = Ext.getBody().getWidth();

		if(alto == 0){ WinAlto = myalto-110; }
		else{ WinAlto = alto; }

		if(ancho == 0){ WinAncho = myancho-30; }
		else{ WinAncho = ancho }

		Win_Panel_Global = new Ext.Window({
			width       : WinAncho,
			height      : WinAlto,
			title       : titulo,
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
							id          : 'contenedor_Win_Panel_Global',
							border      : false,
							bodyStyle   : 'background-color:<?php echo $_SESSION['COLOR_FONDO'] ?>;',
							autoLoad    :
							{
								url     : archivo+'.php',
								scripts : true,
								nocache : true,
								params  : { 
									fecha_inicial : "<?php 
										      if(isset($_SESSION['TIMEZONE'])){
												date_default_timezone_set($_SESSION['TIMEZONE']);
											  }
											  else{
												date_default_timezone_set("America/Bogota");
											  }
										echo date("Y-m-d"); ?>", 
									fecha_final : "<?php 
										      if(isset($_SESSION['TIMEZONE'])){
												date_default_timezone_set($_SESSION['TIMEZONE']);
											  }
											  else{
												date_default_timezone_set("America/Bogota");
											  }
										echo date("Y-m-d"); ?>", 
									filtro_sucursal : "<?php echo $_SESSION['SUCURSAL']; ?>" 
								}
							},
						}
					],
					tbar        :
					[
						{
							xtype   : 'buttongroup',
							columns : 5,
							title   : 'Filtro',
							items   :
							[
								{
									xtype       : 'panel',
									border      : false,
									width       : 240,
									height      : 56,
									bodyStyle   : 'background-color:rgba(255,255,255,0)',
									autoLoad    :
									{
										url     : 'bd/bd.php',
										scripts : true,
										nocache : true,
										params  : { opc : 'panel_filtro_contabilidad' }
									}
								}
							]
						},
						{
							xtype   : 'buttongroup',
							columns : 3,
							title   : 'Filtro Fechas',
							items   :
							[
								{
									xtype       : 'panel',
									border      : false,
									width       : 200,
									height      : 56,
									bodyStyle   : 'background-color:rgba(255,255,255,0);',
									autoLoad    :
									{
										url     : 'bd/bd.php',
										scripts : true,
										nocache : true,
										params  : { opc : 'panel_filtro_fechas' }
									}
								}
							]
						},
						{
							xtype   : 'buttongroup',
							columns : 3,
							title   : 'Filtro Sucursal',
							items   :
							[
								{
									xtype       : 'panel',
									border      : false,
									width       : 160,
									height      : 56,
									bodyStyle   : 'background-color:rgba(255,255,255,0);',
									autoLoad    :
									{
										url     : '../funciones_globales/filtros/filtro_unico_sucursal_contabilidad_documentos.php',
										scripts : true,
										nocache : true,
										params  : { opc : 'panel_filtro_sucursal' }
									}
								}
							]
						},
						// {
						//     xtype   : 'buttongroup',
						//     columns : 3,
						//     title   : 'Tipo de Cuentas',
						//     items   :
						//     [
						//         {
						//             xtype       : 'panel',
						//             border      : false,
						//             width       : 160,
						//             height      : 56,
						//             bodyStyle   : 'background-color:rgba(255,255,255,0);',
						//             autoLoad    :
						//             {
						//                 url     : 'bd/bd.php',
						//                 scripts : true,
						//                 nocache : true,
						//                 params  : { opc : 'panel_tipo_cuentas' }
						//             }
						//         }
						//     ]
						// },
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
									text        : 'Consultar',
									scale       : 'large',
									iconCls     : 'genera_informe',
									iconAlign   : 'top',
									handler     : function(){ BloqBtn(this); generarBusquedaGlobal(archivo+'.php', 'panel_filtro_sucursal') }
								},
								{
									xtype       : 'button',
									width       : 60,
									height      : 56,
									text        : 'Imprimir',
									scale       : 'large',
									iconCls     : 'genera_pdf',
									iconAlign   : 'top',
									handler     : function(){ BloqBtn(this); imprimirBusquedaPricipal(); }
								},
								{
									xtype       : 'button',
									width       : 60,
									height      : 56,
									text        : 'Regresar',
									scale       : 'large',
									iconCls     : 'regresar',
									iconAlign   : 'top',
									handler     : function(){ Win_Panel_Global.close() }
								}
							]
						}
					]
				}
			]
		}).show();
	}

	//ABRE LA VENTANA DE LAS CONSULTAS POR CUENTAS
	function AbreVentanaPanelGlobalContabilidadCuentasColgaap(archivo,titulo,ancho,alto,color){

		var myalto2  = Ext.getBody().getHeight();
		var myancho2 = Ext.getBody().getWidth();

		if(alto == 0){ WinAlto = myalto2-100; }
		else{ WinAlto = alto; }

		if(ancho == 0){ WinAncho = myancho2-30 }
		else{ WinAncho = ancho }

		Win_Panel_Global = new Ext.Window({
			width       : 650,
			height      : 490,
			title       : titulo,
			modal       : true,
			autoScroll  : true,
			closable    : false,
			autoDestroy : false,
			bodyStyle   : 'background-color:<?php echo $_SESSION['COLOR_FONDO'] ?>',
			items       :
			[
				{
					closable    : false,
					autoScroll  : true,
					border      : false,
					iconCls     : '',
					bodyStyle   : 'background-color:<?php echo $_SESSION['COLOR_FONDO'] ?>;',
					items       :
					[
						{
							xtype       : "panel",
							id          : 'contenedor_Win_Panel_Global',
							border      : false,
							bodyStyle   : 'background-color:<?php echo $_SESSION['COLOR_FONDO'] ?>;',

						}
					],
					tbar        :
					[
						{
							xtype   : 'buttongroup',
							columns : 2,
							title   : 'Filtro Contabilildad',
							items   :
							[
								{
									xtype       : 'panel',
									border      : false,
									width       : 150,
									height      : 56,
									bodyStyle   : 'background-color:rgba(255,255,255,0);',
									autoLoad    :
									{
										url     : '../funciones_globales/filtros/filtro_niif.php',
										scripts : true,
										nocache : true,
										params  :
										{
											opc           : "Win_Panel_Global",
											tabla         : 'asientos_colgaap',
											imprimeVarPhp : '',
											renderizaBody : 'true',
											newUrlRender  : archivo+'.php',
										}
									}
								}
							]
						},
						{
							xtype   : 'buttongroup',
							columns : 3,
							title   : 'Filtro Fechas',
							items   :
							[
								{
									xtype       : 'panel',
									border      : false,
									width       : 200,
									height      : 56,
									bodyStyle   : 'background-color:rgba(255,255,255,0);',
									autoLoad    :
									{
										url     : 'bd/bd.php',
										scripts : true,
										nocache : true,
										params  : { opc  : 'panel_filtro_fechas' }
									}
								}
							]
						},
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
									text        : 'Consultar',
									scale       : 'large',
									iconCls     : 'genera_informe',
									iconAlign   : 'top',
									handler     : function(){ BloqBtn(this); generarBusquedaGlobal(archivo+'.php', 'panel_filtro_fechas') }
								},
								{
									xtype       : 'button',
									width       : 60,
									height      : 56,
									text        : 'Imprimir',
									scale       : 'large',
									iconCls     : 'genera_pdf',
									iconAlign   : 'top',
									handler     : function(){ BloqBtn(this); imprimirBusquedaPricipal('Consulta Por Cuentas'); }
								},
								{
									xtype       : 'button',
									width       : 60,
									height      : 56,
									text        : 'Regresar',
									scale       : 'large',
									iconCls     : 'regresar',
									iconAlign   : 'top',
									handler     : function(){ Win_Panel_Global.close() }
								}
							]
						}
					]
				}
			]
		}).show();
	}

	//BUSQUEDA GLOBAL
	function generarBusquedaGlobal(direccionArchivo,opc){
		var tabla_asiento   = ''
		,   filtro_sucursal = ''
		,   tipo_documento  = ''
		,   contabilidad    = (document.getElementById('filtro_contabilidad'))? document.getElementById('filtro_contabilidad').value : ''
		,   fecha_inicial   = document.getElementById('filtroFechaInicial').value
		,   fecha_final     = document.getElementById('filtroFechaFinal').value;

		if (document.getElementById('filtro_asiento_Win_Panel_Global')) { tabla_asiento = document.getElementById('filtro_asiento_Win_Panel_Global').value; }
		if (document.getElementById('filtro_sucursal_'+opc)) { filtro_sucursal = document.getElementById('filtro_sucursal_'+opc).value; }
		if (direccionArchivo == 'consulta_documentos_cuentas_colgaap/consulta_documentos.php') { tipo_documento = document.getElementById('filtro_documento').value; }

		Ext.get('contenedor_Win_Panel_Global').load({
			url     : direccionArchivo,
			scripts : true,
			nocache : true,
			params  :
			{
				tipo_documento  : tipo_documento,
				fecha_inicial   : fecha_inicial,
				fecha_final     : fecha_final,
				tabla_asiento   : tabla_asiento,
				filtro_sucursal : filtro_sucursal,
				contabilidad    : contabilidad
			}
		});
	}

	function load_excel_nota(){
		var tipo_nota = document.getElementById('filtro_tipo_contabilidad_NotaGeneral').value;
	}

	function AbreVentanaPanelGlobalContabilidadFacturasSaldos(archivo,titulo,ancho,alto,color,ancho_campo_carga,opcGrillaContable){
		if (color==0) {color='background-color:#fff;';}

		var myalto2  = Ext.getBody().getHeight();
		var myancho2 = Ext.getBody().getWidth();

		if(alto == 0){ WinAlto = myalto2-20; }
		else{ WinAlto = alto; }

		if(ancho == 0){ WinAncho = myancho2-30 }
		else{ WinAncho = ancho }

		Win_Panel_Global = new Ext.Window({
			width       : WinAncho,
			height      : WinAlto,
			title       : titulo,
			modal       : true,
			autoScroll  : true,
			autoDestroy : true,
			id          : 'contenedorPadreVentana'+opcGrillaContable,
			bodyStyle   : 'background-color: <?php echo $_SESSION['COLOR_FONDO'] ?>',
			items       :
			[
				{
					closable    : false,
					border      : false,
					autoScroll  : true,
					iconCls     : '',
					bodyStyle   : 'background-image:url(\'img/MyInformesFondo.png\');',
					items       :
					[
						{
							xtype       : "panel",
							id          : 'contenedor_'+opcGrillaContable,
							border      : false,
							bodyStyle   : 'background-color:<?php echo $_SESSION['COLOR_FONDO'] ?>;',
							autoLoad    :
							{
								url     : archivo+'.php',
								scripts : true,
								nocache : true,
								params  :
								{
									opc           : opcGrillaContable,
									imprimeVarPhp : 'opcGrillaContable : "'+opcGrillaContable+'"',
									renderizaBody : 'true',
									url_render    : 'notas_inventario/notas_devolucion/default.php',
								}
							}
						}
					],
				}
			],
		}).show();
	}

	function windows_upload_excel(opc){
		if(globalNameFileUpload != ''){ alert('Elimine el archivo anterior antes de subir uno nuevo!'); return; }
		if (opc=='SF') {
			document.getElementById('divPadreModalUploadFile2').setAttribute('style','display:block;');
		}
		else{
			document.getElementById('divPadreModalUploadFile').setAttribute('style','display:block;');
		}
	}

	function close_ventana_upload_file(opc){
		if (opc=='SF') {
			document.getElementById('divPadreModalUploadFile2').setAttribute('style','');
		}
		else{
			document.getElementById('divPadreModalUploadFile').setAttribute('style','');
		}
	}

	function cancelUploadFile(){
		var xhr     = new XMLHttpRequest()
		,   bodyXhr = 'bd.php?nameFileUpload='+globalNameFileUpload+'&opc=cancelUploadFile';

		xhr.open('POST',bodyXhr, true);
		xhr.onreadystatechange=function(){
			if(xhr.readyState==4){
				var responseError = xhr.responseText;
				if (responseError=='true') {
					globalNameFileUpload = '';
					document.getElementById('nombre_excel').value = '';
					document.getElementById('btn_cancel_doc_upload').style.display = 'none';
					return;
				}
				alert(responseError);
			}
			else return;
		}
		xhr.send(null);
	}

	//CONSULTA DOCUMENTOS
	function AbreVentanaPanelGlobalDocumentos(archivo,titulo,ancho,alto){

		var myalto2  = Ext.getBody().getHeight();
		var myancho2 = Ext.getBody().getWidth();

		if(alto == 0){ WinAlto = myalto2-20; }
		else{ WinAlto = alto; }

		if(ancho == 0){ WinAncho = myancho2-30 }
		else{ WinAncho = ancho }

		Win_Panel_Global = new Ext.Window({
			width       : WinAncho,
			height      : WinAlto,
			title       : titulo,
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
							id          : 'contenedor_Win_Panel_Global',
							border      : false,
							bodyStyle   : 'background-color:<?php echo $_SESSION['COLOR_FONDO'] ?>;',
						}
					],
					tbar        :
					[
						{
							xtype   : 'buttongroup',
							columns : 3,
							title   : 'Filtro Sucursal',
							items   :
							[
								{
									xtype       : 'panel',
									border      : false,
									width       : 160,
									height      : 46,
									bodyStyle   : 'background-color:rgba(255,255,255,0);',
									autoLoad    :
									{
										url     : '../funciones_globales/filtros/filtro_unico_sucursal_consulta_documentos.php',
										scripts : true,
										nocache : true,
										params  :
										{
											contenedor : 'contenedor_Win_Panel_Global',
											url_render : archivo+'.php'
										}
									}
								}
							]
						},
						{
							xtype   : 'buttongroup',
							columns : 3,
							title   : 'Tipo Documento',
							items   :
							[
								{
									xtype     : 'panel',
									border    : false,
									width     : 140,
									height    : 46,
									bodyStyle : 'background-color:rgba(255,255,255,0);',
									autoLoad    :
									{
										url     : 'consulta_documentos//bd/bd.php',
										scripts : true,
										nocache : true,
										params  :
										{
											opc               : 'ventana_buscar_documento_cruce',
											opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
											documento_cruce   : 'FC',
										}
									}
								}
							]
						},
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
									text        : 'Regresar',
									scale       : 'large',
									iconCls     : 'regresar',
									iconAlign   : 'top',
									handler     : function(){ Win_Panel_Global.close() }
								}
							]
						}
					]
				}
			]
		}).show();
	}

	// AUDITORIA DE DOCUMENTOS
	var ventanaAuditoria = (archivo,titulo,ancho,alto) =>{


		var myalto2  = Ext.getBody().getHeight();
		var myancho2 = Ext.getBody().getWidth();

		if(alto == 0){ WinAlto = myalto2-20; }
		else{ WinAlto = alto; }

		if(ancho == 0){ WinAncho = myancho2-30 }
		else{ WinAncho = ancho }

		Win_Panel_Global = new Ext.Window({
			width       : WinAncho,
			height      : WinAlto,
			title       : titulo,
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
							id          : 'contenedor_Win_Panel_Global',
							border      : false,
							bodyStyle   : 'background-color:<?php echo $_SESSION['COLOR_FONDO'] ?>;',
						}
					],
					tbar        :
					[
						{
							xtype   : 'buttongroup',
							columns : 3,
							title   : 'Filtro Sucursal',
							items   :
							[
								{
									xtype       : 'panel',
									border      : false,
									width       : 160,
									height      : 46,
									bodyStyle   : 'background-color:rgba(255,255,255,0);',
									autoLoad    :
									{
										url     : '../funciones_globales/filtros/filtro_unico_sucursal_consulta_documentos.php',
										scripts : true,
										nocache : true,
										params  :
										{
											contenedor : 'contenedor_Win_Panel_Global',
											url_render : archivo+'.php'
										}
									}
								}
							]
						},
						{
							xtype   : 'buttongroup',
							columns : 3,
							title   : 'Tipo Documento',
							items   :
							[
								{
									xtype     : 'panel',
									border    : false,
									width     : 140,
									height    : 46,
									bodyStyle : 'background-color:rgba(255,255,255,0);',
									autoLoad    :
									{
										url     : 'auditoria_documentos/bd/bd.php',
										scripts : true,
										nocache : true,
										params  :
										{
											opc               : "getDOMDocsCruce",
											opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
											documento_cruce   : 'FC',
										}
									}
								}
							]
						},
						{
							xtype   : 'buttongroup',
							columns : 3,
							title   : 'Filtro Fechas',
							items   :
							[
								{
									xtype     : 'panel',
									border    : false,
									width     : 190,
									height    : 46,
									bodyStyle : 'background-color:rgba(255,255,255,0);',
									autoLoad    :
									{
										url     : 'auditoria_documentos/bd/bd.php',
										scripts : true,
										nocache : true,
										params  :
										{
											opc               : "getDates",
											opcGrillaContable : '<?php echo $opcGrillaContable; ?>'
										}
									}
								}
							]
						},
						{
							xtype   : 'buttongroup',
							columns : 3,
							title   : 'Filtro Estado',
							items   :
							[
								{
									xtype     : 'panel',
									border    : false,
									width     : 145,
									height    : 46,
									bodyStyle : 'background-color:rgba(255,255,255,0);',
									autoLoad    :
									{
										url     : 'auditoria_documentos/bd/bd.php',
										scripts : true,
										nocache : true,
										params  :
										{
											opc               : "getState",
											opcGrillaContable : '<?php echo $opcGrillaContable; ?>'
										}
									}
								}
							]
						},
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
									text        : 'Regresar',
									scale       : 'large',
									iconCls     : 'regresar',
									iconAlign   : 'top',
									handler     : function(){ Win_Panel_Global.close() }
								}
							]
						}
					]
				}
			]
		}).show();
	}

	// EXPORTAR A EXCEL
	function AbreVentanaPanelGlobalExportarExcel(){
		var myalto  = Ext.getBody().getHeight();
		var myancho = Ext.getBody().getWidth();

		Win_Ventana_panel_contabilidad = new Ext.Window({
			width       : 290,
			height      : 295,
			id          : 'Win_Ventana_panel_contabilidad',
			title       : 'Exportar Asientos a Excel',
			modal       : true,
			autoScroll  : false,
			closable    : false,
			autoDestroy : true,
			autoLoad    :
			{
				url     : 'exportar_asientos/exportar_asientos.php',
				scripts : true,
				nocache : true,
				params  : { }
			},
			tbar        :
			[
				{
					xtype   : 'buttongroup',
					columns : 3,
					title   : 'Exportar',
					style   : 'border-right:none;',
					items   :
					[
						{
							xtype       : 'button',
							width       : 60,
							height      : 56,
							text        : 'Excel',
							scale       : 'large',
							iconCls     : 'excel32',
							iconAlign   : 'top',
							hidden      : false,
							handler     : function(){ BloqBtn(this); genera_excel('XLS'); }
						},
						{
							xtype       : 'button',
							width       : 60,
							height      : 56,
							text        : 'Texto Csv',
							scale       : 'large',
							iconCls     : 'genera_informe',
							iconAlign   : 'top',
							hidden      : false,
							handler     : function(){ BloqBtn(this); genera_excel('CSV'); }
						}
					]
				},'-',
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
							handler     : function(){ BloqBtn(this); Win_Ventana_panel_contabilidad.close(id) }
						}
					]
				},
			]
		}).show();
	}

	var AbreVentanaApi = (params) => {
		Win_Ventana_apis = new Ext.Window({
			width       : params.width,
			height      : params.height,
			id          : 'Win_Ventana_apis',
			title       : `Interfaz  ${params.titulo}`,
			modal       : true,
			autoScroll  : false,
			closable    : true,
			autoDestroy : true,
			autoLoad    :
			{
				url     : `../external_apis/${params.archivo}`,
				scripts : true,
				nocache : true,
				params  : { id_api : params.id }
			}
		}).show();
	}

	function AbreVentanaPanelModulosWs(id_software,software){

		Win_Ventana_conectividad_ws = new Ext.Window({
			width       : 300,
			height      : 300,
			id          : 'Win_Ventana_conectividad_ws',
			title       : 'Interfaz Software '+software,
			modal       : true,
			autoScroll  : false,
			closable    : true,
			autoDestroy : true,
			autoLoad    :
			{
				url     : 'conectividad/ventana_metodos.php',
				scripts : true,
				nocache : true,
				params  : { id_software : id_software }
			}
		}).show();
	}

	function AbreVentanaTrasladoSaldoTercero(){
		Win_Ventana_traslado_saldo_tercero = new Ext.Window({
			width       : 500,
			height      : 350,
			id          : 'Win_Ventana_traslado_saldo_tercero',
			title       : 'Trasladar Saldo de un tercero',
			modal       : true,
			autoScroll  : false,
			closable    : true,
			autoDestroy : true,
			autoLoad    :
			{
				url     : 'traslado_saldo_terceros/traslado.php',
				scripts : true,
				nocache : true,
				params  : { }
			}
		}).show();
	}

	function ventanaCierrePeriodo() {

		Win_Ventana_cierre_por_periodo = new Ext.Window({
		    width       : 550,
		    height      : 450,
		    id          : 'Win_Ventana_cierre_por_periodo',
		    title       : 'Cierre Por Periodo',
		    modal       : true,
		    autoScroll  : false,
		    closable    : false,
		    autoDestroy : true,
		    autoLoad    :
		    {
		        url     : 'cierre_por_periodo/cierre_por_periodo.php',
		        scripts : true,
		        nocache : true,
		        params  :
		        {
		            var1 : 'var1',
		            var2 : 'var2',
		        }
		    },
		    // tbar        :
		    // [
		    //     {
		    //         xtype   : 'buttongroup',
		    //         columns : 3,
		    //         title   : 'Opciones',
		    //         style   : 'border-right:none;',
		    //         items   :
		    //         [
		    //             {
		    //                 xtype       : 'button',
		    //                 width       : 60,
		    //                 height      : 56,
		    //                 text        : 'Regresar',
		    //                 scale       : 'large',
		    //                 iconCls     : 'regresar',
		    //                 iconAlign   : 'top',
		    //                 hidden      : false,
		    //                 handler     : function(){ BloqBtn(this); Win_Ventana_cierre_por_periodo.close(id) }
		    //             }
		    //         ]
		    //     }
		    // ]
		}).show();
	}

	//CONSULTA DOCUMENTOS Y TICKETS
	function AbreVentanaPanelGlobalContabilidadDocumentosNiif(archivo,titulo,ancho,alto,opcGrillaContable){

		Win_Panel_Global = new Ext.Window({
			width       : 650,
			height      : 520,
			modal       : true,
			autoScroll  : true,
			closable    : true,
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
							id          : 'contenedor_Win_Panel_Global',
							border      : false,
							bodyStyle   : 'background-color:<?php echo $_SESSION['COLOR_FONDO'] ?>;',
							autoLoad    :
							{
								url     : archivo+'.php',
								scripts : true,
								nocache : true,
								params  : { filtro_sucursal : "<?php echo $_SESSION['SUCURSAL']; ?>",opcGrillaContable : opcGrillaContable }
							},
						}
					]
				}
			]
		}).show();
	}

</script>
