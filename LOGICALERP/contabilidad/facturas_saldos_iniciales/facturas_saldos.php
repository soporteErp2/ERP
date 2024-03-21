<?php
	include("../../../configuracion/conectar.php");
	include("../../../configuracion/define_variables.php");

	$id_empresa = $_SESSION['EMPRESA'];

	//CONSULTAR LOS DATOS DE LA CABECERA
	$sql   = "SELECT sucursal,tipo_factura,id_cuenta_pago,cuenta_pago,cuenta_colgaap,cuenta_niif,estado,id_sucursal,fecha_factura
				FROM facturas_saldos_iniciales
				WHERE activo=1
					AND id_empresa=$id_empresa
					AND id=$id_saldo_inicial";
	$query = mysql_query($sql,$link);

	$sucursal       = mysql_result($query,0,'sucursal');
	$tipo_factura   = mysql_result($query,0,'tipo_factura');
	$id_cuenta_pago = mysql_result($query,0,'id_cuenta_pago');
	$cuenta_pago    = mysql_result($query,0,'cuenta_pago');
	$cuenta_colgaap = mysql_result($query,0,'cuenta_colgaap');
	$cuenta_niif    = mysql_result($query,0,'cuenta_niif');
	$estado         = mysql_result($query,0,'estado');
	$id_sucursal    = mysql_result($query,0,'id_sucursal');
	$fecha_factura  = mysql_result($query,0,'fecha_factura');

	$tipoFacturaLabel = ($tipo_factura=='FV')? 'Factura de Venta' : 'Factura de Compra';
	$tipoFacturaLabel = ($tipo_factura=='FV')? 'Factura de Venta' : 'Factura de Compra';
	$tablaBd          = ($tipo_factura=='FV')? 'ventas_facturas' : 'compras_facturas';

	//=====================// BOQUEA BOTON SI HAY FACTURAS CRUZADAS //=====================//
	//*************************************************************************************//
	if ($estado > 0) {
		$sqlContFactura = "SELECT COUNT(F.id) AS contFactura
							FROM $tablaBd AS F INNER JOIN asientos_colgaap AS A ON (
									A.tipo_documento_cruce = '$tipo_factura'
									AND A.id_documento_cruce = F.id
									AND A.id_documento_cruce <> A.id_documento
									AND A.tipo_documento_cruce <> A.tipo_documento
								)
							WHERE F.activo=1
								AND F.id_sucursal=$id_sucursal
								AND F.id_empresa=$id_empresa
								AND F.id_saldo_inicial=$id_saldo_inicial
								AND F.estado=1";
		$queryContFactura = mysql_query($sqlContFactura,$link);
		if(mysql_result($queryContFactura, 0, 'contFactura') > 0){ echo'<script>Ext.getCmp("btn_editar_saldo_inicial").disable();</script>'; }
		else{ echo'<script>Ext.getCmp("btn_editar_saldo_inicial").enable();</script>'; }
	}

	if ($tipo_factura=='FC') {											//FACTURA DE COMPRA
		$sqlFactura = "SELECT id,prefijo_factura,numero_factura,observacion,fecha_final AS fecha_vencimiento,total_factura,nit,proveedor AS tercero,id_proveedor AS id_tercero
						FROM compras_facturas
						WHERE activo=1 AND id_sucursal=$id_sucursal AND id_empresa=$id_empresa AND id_saldo_inicial=$id_saldo_inicial";
	}
	else{																//FACTURA DE VENTA
		$sqlFactura = "SELECT id,prefijo AS prefijo_factura,numero_factura,observacion,fecha_vencimiento,total_factura,nit,cliente AS tercero,id_cliente AS id_tercero
						FROM ventas_facturas
						WHERE activo=1 AND id_sucursal=$id_sucursal AND id_empresa=$id_empresa AND id_saldo_inicial=$id_saldo_inicial";
	}

	//CONSULTAR LOS DOCUMENTOS
	$queryFactura = mysql_query($sqlFactura,$link);
	$cont         = 1;

	while ($row = mysql_fetch_assoc($queryFactura)) {
		$divBotones  = '';
		$eventoInput = '';
		$eventSave   = '';
		$scripAcum   = '';
		$btnBuscar   = '';
		$padingInput = 'padding-right: 5px;';

		$row['total_factura'] = $row['total_factura']*1;

		if ($estado == 0) {

			$divBotones = '<div style="float:left; min-width:80px;padding-left:5px;">
								<div onclick="guardarNewFactura'.$opcGrillaContable.'('.$cont.')" id="divImageSave'.$opcGrillaContable.'_'.$cont.'" title="Guardar Documento" style="width:20px; float:left; margin-top:3px;cursor:pointer;display:none;"><img src="img/reload.png" id="imgSaveArticulo'.$opcGrillaContable.'_'.$cont.'"/></div>
								<div onclick="retrocederDocumento'.$opcGrillaContable.'('.$cont.')" id="divImageDeshacer'.$opcGrillaContable.'_'.$cont.'" title="Deshacer Cambios" style="width:20px; float:left; margin-top:3px;cursor:pointer;display:none"><img src="img/deshacer.png" id="imgDeshacerArticulo'.$opcGrillaContable.'_'.$cont.'"></div>
								<div onclick="deleteFactura'.$opcGrillaContable.'('.$cont.')" id="deleteFactura'.$opcGrillaContable.'_'.$cont.'" title="Eliminar Documento" style="width:20px; float:left; margin-top:3px;cursor:pointer;"><img src="img/delete.png" /></div>
							</div>';

			$eventoInput = 'onKeyup="validarNumberDocumento'.$opcGrillaContable.'(event,this,'.$cont.')"';
			$eventSave   = 'onKeyup="guardarAuto'.$opcGrillaContable.'(event,this,'.$cont.');"';
			$scripAcum   = 'new Ext.form.DateField({
							    format     : "Y-m-d",
							    width      : 90,
							    allowBlank : false,
							    showToday  : false,
							    applyTo    : "fechaFactura'.$opcGrillaContable.'_'.$cont.'",
							    editable   : false,
							    listeners  : { select: function() { validarNumberDocumentoFecha'.$opcGrillaContable.'(document.getElementById("fechaFactura'.$opcGrillaContable.'_'.$cont.'"),'.$cont.');  } }
							});';

			$btnBuscar   = '<div class="iconBuscarProveedor" onclick="ventanaBusquedaTercero('.$cont.')" id="imgBuscarProveedor" title="Buscar Proveedor"><img src="img/buscar20.png"/></div>';
			$padingInput = 'padding-right: 25px;';
		}

		$bodyArticle .= '<div class="bodyDivArticulosNotaGeneral" id="bodyDivArticulos'.$opcGrillaContable.'_'.$cont.'">
							<div class="campo" style="width:40px !important; overflow:hidden;">
								<div style="float:left; margin:3px 0 0 2px;">'.$cont.'</div>
								<div style="float:left; width:18px; overflow:hidden;" id="renderArticulo'.$opcGrillaContable.'_'.$cont.'"></div>
							</div>

							<div class="campoNotaGeneral" style="width:95px;">
								<input type="text" id="nit_tercero_'.$opcGrillaContable.'_'.$cont.'" value="'.$row['nit'].'" readonly />
							</div>

							<div class="campoNotaGeneral" style="width:150px;">
								<input type="text" style="text-align:left; '.$padingInput.'" id="tercero_'.$opcGrillaContable.'_'.$cont.'" value="'.$row['tercero'].'" readonly />
							</div>
							'.$btnBuscar.'
							<div class="campoNotaGeneral" style="width:95px;">
								<input type="text" style="text-align:left;" id="prefijoFactura'.$opcGrillaContable.'_'.$cont.'" value="'.$row['prefijo_factura'].'" '.$eventoInput.' />
							</div>

							<div class="campoNotaGeneral" style="width:95px;">
								<input type="text" id="numeroFactura'.$opcGrillaContable.'_'.$cont.'" value="'.$row['numero_factura'].'" '.$eventoInput.' />
							</div>

							<div class="campoNotaGeneral" style="width:95px;">
								<input type="text" style="text-align:left;" id="detalle'.$opcGrillaContable.'_'.$cont.'" value="'.$row['observacion'].'" '.$eventoInput.' />
							</div>

							<div class="campoNotaGeneral" style="width:90px;">
								<input type="text" id="fechaFactura'.$opcGrillaContable.'_'.$cont.'" value="'.$row['fecha_vencimiento'].'" />
							</div>

							<div class="campoNotaGeneral" style="width:85px;">
								<input type="text" id="valorFactura'.$opcGrillaContable.'_'.$cont.'" value="'.$row['total_factura'].'" '.$eventSave.'  />
							</div>

							'.$divBotones.'

							<input type="hidden" id="idTecero'.$opcGrillaContable.'_'.$cont.'" value="'.$row['id_tercero'].'" />
							<input type="hidden" id="idInsertDoc'.$opcGrillaContable.'_'.$cont.'" value="'.$row['id'].'" />
						</div>
						<script>
							'.$scripAcum.'
						</script>';
		$cont++;
	}

	if ($estado == 0) {
		$bodyArticle .= '<div class="bodyDivArticulosNotaGeneral" id="bodyDivArticulos'.$opcGrillaContable.'_'.$cont.'">
							<div class="campo" style="width:40px !important; overflow:hidden;">
								<div style="float:left; margin:3px 0 0 2px;">'.$cont.'</div>
								<div style="float:left; width:18px; overflow:hidden;" id="renderArticulo'.$opcGrillaContable.'_'.$cont.'"></div>
							</div>

							<div class="campoNotaGeneral" style="width:95px;">
								<input type="text" id="nit_tercero_'.$opcGrillaContable.'_'.$cont.'" readonly />
							</div>

							<div class="campoNotaGeneral" style="width:150px;">
								<input type="text" style="padding-right: 25px;" id="tercero_'.$opcGrillaContable.'_'.$cont.'" readonly />
							</div>
							<div class="iconBuscarProveedor" onclick="ventanaBusquedaTercero('.$cont.')" id="imgBuscarProveedor" title="Buscar Proveedor">
		                       <img src="img/buscar20.png"/>
		                    </div>
							<div class="campoNotaGeneral" style="width:95px;">
								<input type="text" style="text-align:left;" id="prefijoFactura'.$opcGrillaContable.'_'.$cont.'" onKeyup="validarNumberDocumento'.$opcGrillaContable.'(event,this,'.$cont.')" />
							</div>

							<div class="campoNotaGeneral" style="width:95px;">
								<input type="text" id="numeroFactura'.$opcGrillaContable.'_'.$cont.'"  onKeyup="validarNumberDocumento'.$opcGrillaContable.'(event,this,'.$cont.')" />
							</div>

							<div class="campoNotaGeneral" style="width:95px;">
								<input type="text" style="text-align:left;" id="detalle'.$opcGrillaContable.'_'.$cont.'" onKeyup="validarNumberDocumento'.$opcGrillaContable.'(event,this,'.$cont.')" />
							</div>

							<div class="campoNotaGeneral" style="width:90px;">
								<input type="text" id="fechaFactura'.$opcGrillaContable.'_'.$cont.'"  />
							</div>

							<div class="campoNotaGeneral" style="width:85px;">
								<input type="text" id="valorFactura'.$opcGrillaContable.'_'.$cont.'" onKeyup="guardarAuto'.$opcGrillaContable.'(event,this,'.$cont.');"  />
							</div>

							<div style="float:left; min-width:80px;padding-left:5px;">
								<div onclick="guardarNewFactura'.$opcGrillaContable.'('.$cont.')" id="divImageSave'.$opcGrillaContable.'_'.$cont.'" title="Guardar Documento" style="width:20px; float:left; margin-top:3px;cursor:pointer;"><img src="img/save_true.png" id="imgSaveArticulo'.$opcGrillaContable.'_'.$cont.'"/></div>
								<div onclick="retrocederDocumento'.$opcGrillaContable.'('.$cont.')" id="divImageDeshacer'.$opcGrillaContable.'_'.$cont.'" title="Deshacer Cambios" style="width:20px; float:left; margin-top:3px;cursor:pointer;display:none"><img src="img/deshacer.png" id="imgDeshacerArticulo'.$opcGrillaContable.'_'.$cont.'"></div>
								<div onclick="deleteFactura'.$opcGrillaContable.'('.$cont.')" id="deleteFactura'.$opcGrillaContable.'_'.$cont.'" title="Eliminar Documento" style="width:20px; float:left; margin-top:3px; display:none;cursor:pointer;"><img src="img/delete.png" /></div>
							</div>

							<input type="hidden" id="idTecero'.$opcGrillaContable.'_'.$cont.'" " />
							<input type="hidden" id="idInsertDoc'.$opcGrillaContable.'_'.$cont.'" value="0" />
						</div>
						<script>
							new Ext.form.DateField({
							    format     : "Y-m-d",
							    width      : 90,
							    allowBlank : false,
							    showToday  : false,
							    applyTo    : "fechaFactura'.$opcGrillaContable.'_'.$cont.'",
							    editable   : false,
							    listeners  : { select: function() {  validarNumberDocumentoFecha'.$opcGrillaContable.'(document.getElementById("fechaFactura'.$opcGrillaContable.'_'.$cont.'"),'.$cont.'); } }
							});
							contArticulos'.$opcGrillaContable.' = '.$cont.';
						</script>';
		}

		//-------------------------//
		// VALIDAR EL CIERRE ANUAL //
		//-------------------------//

		//FECHA A BUSCAR LAS NOTAS GENERADAS PARA VALIDAR
		$fecha_inicio_buscar = date("Y", strtotime($fecha_factura)).'-01-01';
		$fecha_fin_buscar    = date("Y", strtotime($fecha_factura)).'-12-31';

		// VALIDAR QUE NO EXISTAN MAS NOTAS DE CIERRE CREADAS PARA ESE PERIODO
		$sql="SELECT COUNT(id) AS cont FROM nota_cierre WHERE activo=1 AND id_empresa=$id_empresa AND estado=1 AND fecha_nota>='$fecha_inicio_buscar' AND fecha_nota<='$fecha_fin_buscar' ";
		$query=mysql_query($sql,$link);
		$cont = mysql_result($query,0,'cont');

		// SI EXISTEN NOTAS DE CIERRE DE ESE PERIODO
		if ($cont>0) {
			echo '<script>Ext.getCmp("btn_generar_saldo").disable();Ext.getCmp("btn_upload_excel_saldo").disable();</script>';
		}
?>

<style>
	#contenedorSaldosFacturas{
		overflow   : hidden;
		height     : 100%;
		margin-top : 20px;
	}

	#contenedorSaldosFacturas .campoHead {
		float      : left;
		width      : 208px;
		overflow   : hidden;
		margin-top : 3px;
		height     : 20px;
		border     : 1px solid #B3B3B3;
	}

	#contenedorSaldosFacturas .campoHead > div:nth-child(1) {
		text-align       : left;
		float            : left;
		width            : 56px;
		background-color : #d4d4d4;
		padding          : 2px;
		height           : 100%;
		text-indent      : 5px;
		font-weight      : bold;
	}

	#contenedorSaldosFacturas .campoHead > div:nth-child(2) {
		float            : left;
		width            : 144px;
		background-color : #FFF;
		padding          : 2px;
		height           : 100%;
		text-indent      : 5px;
	}

	.campoContenido{
		height      : 15px;
		float       : left;
		width       : 100%;
		text-indent : 10px;
	}

	.fondo_modal_saldos{
		z-index  : 99999;
		top      : 0px;
		width    : 100%;
		height   : 100%;
		display  : table;
		left     : 0px;
		position : absolute !important;
	}

	.contenedor_ventana_modal{
		background-color      : #DFE8F6;
		width                 : 600px;
		height                : 257px;
		margin                : 0px auto;
		-webkit-border-radius : 10px;
		-moz-border-radius    : 10px;
		border-radius         : 10px;
		background-image      : -moz-linear-gradient(top, #ffffff, #dedcdb);
		background-image      : -ms-linear-gradient(top, #ffffff, #dedcdb);
		background-image      : -o-linear-gradient(top, #ffffff, #dedcdb);
		background-image      : -webkit-gradient(linear, center top, center bottom, from(#ffffff), to(#dedcdb));
		background-image      : -webkit-linear-gradient(top, #ffffff, #dedcdb);
		background-image      : linear-gradient(top, #ffffff, #dedcdb);
	}

	#modal{
		display        : table-cell;
		vertical-align : middle;
	}

</style>
<div id="contenedorSaldosFacturas">

    <div class="bodyTop" style="background-color:rgba(255,255,255,0) !important;">
        <div class="contInfoFact" style="background-color:rgba(255,255,255,0) !important;">
            <div id="terminar<?php echo $opcGrillaContable; ?>"></div>
            <div class="contTopFila">
            	<div style="overflow:hidden;">
            		<div class="campoHead" style="width:308px; height:22px; min-height:0px;">
	                    <div style="width:60px;">TIPO</div>
	                    <div style="width:240px;"><?php echo $tipoFacturaLabel; ?></div>
	                </div>
            	</div>

            	<div style="overflow:hidden;">
	                <div class="campoHead" style="width:308px; height:22px; min-height:0px;">
	                    <div style="width:80px;">SUCURSAL</div>
	                    <div style="width:220px;"><?php echo $sucursal; ?></div>
	                </div>
            	</div>

            	<div style="overflow:hidden;">
	                <div class="campoHead" style="width:308px; height:22px; min-height:0px;">
	                    <div style="width:100px;">CUENTA PAGO</div>
	                    <div style="width:200px;"><?php echo $cuenta_pago; ?></div>
	                </div>
            	</div>
            </div>
        </div>
    </div>

    <div class="bodyArticulos" id="bodyArticulos<?php echo $opcGrillaContable; ?>" style="background-color:rgba(255,255,255,0) !important;">
    	<div class="contenedorGrilla" style="height:315px;">
    		<div class="contenedorHeadArticulos" style="min-width:0 !important;">
				<div class="headArticulos">
					<div class="labelNotaGeneral" style="width:40px !important;"></div>
					<div class="labelNotaGeneral" style="width:95px;">Nit</div>
					<div class="labelNotaGeneral" style="width:150px;">Tercero</div>
					<div class="labelNotaGeneral" style="width:95px;">Prefijo</div>
					<div class="labelNotaGeneral" style="width:95px;">Numero Factura</div>
					<div class="labelNotaGeneral" style="width:95px;">Observaciones</div>
					<div class="labelNotaGeneral" style="width:90px;">Vencimiento</div>
					<div class="labelNotaGeneral" style="width:85px;">Valor</div>
				</div>
			</div>
			<div class="DivArticulos" id="DivArticulos<?php echo $opcGrillaContable; ?>" style="min-width:0 !important;">
    			<?php echo $bodyArticle; ?>
			</div>
		</div>
	</div>

</div>

<script>

  	//========================// FILTRO TECLA GUARDAR CUENTA //========================//
    function guardarAuto<?php echo $opcGrillaContable; ?>(event,input,cont){

        var idInsertDoc  = document.getElementById('idInsertDoc<?php echo $opcGrillaContable; ?>_'+cont).value
        ,   tecla = input? event.keyCode : event.which
        ,   value = input.value;

        if(tecla == 13){
            input.blur();
            guardarNewFactura<?php echo $opcGrillaContable; ?>(cont);
        }

        else if(tecla == 9 || tecla == 37 || tecla == 38 || tecla == 39 || tecla == 40 || tecla == 18 || tecla == 17 || tecla == 16){ return true; }
        else if (idInsertDoc>0) {
            document.getElementById('divImageSave<?php echo $opcGrillaContable; ?>_'+cont).style.display     = 'inline';
            document.getElementById('divImageDeshacer<?php echo $opcGrillaContable; ?>_'+cont).style.display = 'inline';
        }

        patron = /[^\d.]/g;
        if(patron.test(value)){ input.value = input.value.replace(patron,''); }
        return true;
    }

	function guardarNewFactura<?php echo $opcGrillaContable; ?>(cont) {
		var idInsertDoc    = document.getElementById('idInsertDoc<?php echo $opcGrillaContable; ?>_'+cont).value
		,	prefijoFactura = document.getElementById('prefijoFactura<?php echo $opcGrillaContable; ?>_'+cont).value
		,	id_tercero     = document.getElementById('idTecero<?php echo $opcGrillaContable; ?>_'+cont).value
		,	numeroFactura  = document.getElementById('numeroFactura<?php echo $opcGrillaContable; ?>_'+cont).value
		,	detalle        = document.getElementById('detalle<?php echo $opcGrillaContable; ?>_'+cont).value
		,	fechaFactura   = document.getElementById('fechaFactura<?php echo $opcGrillaContable; ?>_'+cont).value
		,	valorFactura   = document.getElementById('valorFactura<?php echo $opcGrillaContable; ?>_'+cont).value
		,	opc            = 'guardarDocumento';

        //VALIDAR QUE LA FILA TENGA UNA CUENTA
        if (id_tercero == 0){ alert('El campo tercero es Obligatorio'); setTimeout(function(){ document.getElementById('idTecero<?php echo $opcGrillaContable; ?>_'+cont).focus(); },100); return; }
        if (numeroFactura == 0){ alert('El campo numero factura es Obligatorio'); setTimeout(function(){ document.getElementById('numeroFactura<?php echo $opcGrillaContable; ?>_'+cont).focus(); },100); return; }
        // if (detalle ==  0){alert('El campo Orbservaciones factura es Obligatorio'); setTimeout(function(){ document.getElementById('detalle<?php echo $opcGrillaContable; ?>_'+cont).focus(); },100); return; }
        if (fechaFactura ==  0){alert('El campo fecha factura es Obligatorio'); setTimeout(function(){ document.getElementById('fechaFactura<?php echo $opcGrillaContable; ?>_'+cont).focus(); },100); return; }
    	if (valorFactura ==  0){alert('El campo valor factura es Obligatorio'); setTimeout(function(){ document.getElementById('valorFactura<?php echo $opcGrillaContable; ?>_'+cont).focus(); },100); return; }

        //VALIDACION SI ES UPDATE O INSERT
        if(idInsertDoc > 0){
            opc       = 'actualizaDocumento';
            divRender = 'renderArticulo<?php echo $opcGrillaContable; ?>_'+cont;
        }
        else{
            //VALIDAMOS PARA NO REPETIR FILAS DE LAN GRILLA
            contArticulos<?php echo $opcGrillaContable; ?>++;
            divRender = 'bodyDivArticulos<?php echo $opcGrillaContable; ?>_'+contArticulos<?php echo $opcGrillaContable; ?>;
            var div   = document.createElement('div');
            div.setAttribute('id','bodyDivArticulos<?php echo $opcGrillaContable; ?>_'+contArticulos<?php echo $opcGrillaContable; ?>);
            div.setAttribute('class','bodyDivArticulosNotaGeneral');
            document.getElementById('DivArticulos<?php echo $opcGrillaContable; ?>').appendChild(div);
        }

        // console.log("tipo_factura <?php echo $tipo_factura; ?>");
        // return;
        Ext.get(divRender).load({
            url     : 'facturas_saldos_iniciales/bd/bd.php',
            scripts : true,
            nocache : true,
            params  :
            {
				opc               : opc,
				opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
				consecutivo       : contArticulos<?php echo $opcGrillaContable; ?>,
				cont              : cont,
				idInsertDoc       : idInsertDoc,
				prefijoFactura    : prefijoFactura,
				numeroFactura     : numeroFactura,
				detalle           : detalle,
				fechaFactura      : fechaFactura,
				valorFactura      : valorFactura,
				id_saldo_inicial  : '<?php echo $id_saldo_inicial; ?>',
				tipo_factura      : '<?php echo $tipo_factura; ?>',
				idCuentaPago      : '<?php echo $id_cuenta_pago; ?>',
				filtro_sucursal   : '<?php echo $id_sucursal; ?>',
				id_tercero        : id_tercero,
            }
        });
	}


	//======================= BORRAR UN DOCUMENTO =============================================================//
    function deleteFactura<?php echo $opcGrillaContable; ?>(cont){
        var idDocumento = document.getElementById('idInsertDoc<?php echo $opcGrillaContable; ?>_'+cont).value;

        if(confirm('Esta Seguro de eliminar este documento?')){
            Ext.get('renderArticulo<?php echo $opcGrillaContable; ?>_'+cont).load({
                url     : 'facturas_saldos_iniciales/bd/bd.php',
                scripts : true,
                nocache : true,
                params  :
                {
					opc               : 'eliminaDocumento',
					opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
					idDocumento       : idDocumento,
					cont              : cont,
					id_saldo_inicial  : '<?php echo $id_saldo_inicial; ?>',
					tipo_factura      : '<?php echo $tipo_factura; ?>',
                }
            });
        }
    }

    //===================== CANCELAR LOS CAMBIOS DE UN DOCUMENTO ===============================================//
    function retrocederDocumento<?php echo $opcGrillaContable; ?>(cont){
        var idDocumento = document.getElementById("idInsertDoc<?php echo $opcGrillaContable; ?>_"+cont).value;

        Ext.get('renderArticulo<?php echo $opcGrillaContable; ?>_'+cont).load({
            url     : 'facturas_saldos_iniciales/bd/bd.php',
            scripts : true,
            nocache : true,
            params  :
            {
				opc               : 'retrocederDocumento',
				cont              : cont,
				opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
				idDocumento       : idDocumento,
				id_saldo_inicial  : '<?php echo $id_saldo_inicial; ?>',
				tipo_factura      : '<?php echo $tipo_factura; ?>',
            }
        });
    }

    //================================== VALIDACION NUMERICA  ===================================//
    function validarNumberDocumento<?php echo $opcGrillaContable; ?>(event,input,cont){
        // console.log(input);return;
        var contIdInput = (input.id).split('_')[1];
        var nombreInput = (input.id).split('_')[0];
        var idDocumento = document.getElementById("idInsertDoc<?php echo $opcGrillaContable; ?>_"+cont).value;

        numero = input.value;
        tecla  = (input) ? event.keyCode : event.which;

        if (nombreInput=='prefijoFactura<?php echo $opcGrillaContable; ?>') {
            if(tecla == 13){
            	document.getElementById('numeroFactura<?php echo $opcGrillaContable; ?>_'+cont).focus();
        	}
        }
        if (nombreInput=='numeroFactura<?php echo $opcGrillaContable; ?>') {
            if(tecla == 13){
            	document.getElementById('detalle<?php echo $opcGrillaContable; ?>_'+cont).focus();
        	}
        }
        if (nombreInput=='detalle<?php echo $opcGrillaContable; ?>') {
            if(tecla == 13){
            	document.getElementById('fechaFactura<?php echo $opcGrillaContable; ?>_'+cont).focus();
        	}
        }

        if (nombreInput=='numeroFactura<?php echo $opcGrillaContable; ?>') {
        	patron = /[^\d]/g;
        	if(patron.test(numero)){
        	    numero      = numero.replace(patron,'');
        	    input.value = numero;
        	}
        else if(isNaN(numero)){ input.value = numero.substring(0, numero.length-1); }
    	}

        if(idDocumento>0){
            document.getElementById('divImageSave<?php echo $opcGrillaContable; ?>_'+contIdInput).style.display    = 'inline';
            document.getElementById('divImageDeshacer<?php echo $opcGrillaContable; ?>_'+contIdInput).style.display = 'inline';
        }
    }

    function validarNumberDocumentoFecha<?php echo $opcGrillaContable; ?>(input,cont){
    	var contIdInput = (input.id).split('_')[1];
        var nombreInput = (input.id).split('_')[0];
        var idDocumento = document.getElementById("idInsertDoc<?php echo $opcGrillaContable; ?>_"+cont).value;

        if(idDocumento>0){
            document.getElementById('divImageSave<?php echo $opcGrillaContable; ?>_'+contIdInput).style.display    = 'inline';

            // if(document.getElementById('idInsertCuenta<?php echo $opcGrillaContable; ?>_'+contIdInput).value > 0){
                document.getElementById('divImageDeshacer<?php echo $opcGrillaContable; ?>_'+contIdInput).style.display = 'inline';
            // }
        }

    }

    //====================================// UPLOAD FILE NOTA CONTABLE //====================================//
	//*******************************************************************************************************//
	function createUploader(){

        var uploader = new qq.FileUploader({
            element : document.getElementById('div_upload_file2'),
            action  : 'upload_file_saldos/upload_file.php',
            debug   : false,
            params  : { opcion: 'loadExcel', idSaldoInicial: <?php echo $id_saldo_inicial; ?> },
            button            : null,
            multiple          : false,
            maxConnections    : 3,
            allowedExtensions : ['xls', 'ods','xlsx'],
            sizeLimit         : 10*1024*1024,
            minSizeLimit      : 0,
            onSubmit          : function(id, fileName){},
            onProgress        : function(id, fileName, loaded, total){},
            onComplete        : function(id, fileName, responseJSON){

                                    var JsonText = JSON.stringify(responseJSON);
                                    console.log(JsonText);
                                    if(JsonText == '{}'){
                                        alert("Aviso\nLo sentimos ha ocurrido un problema con la carga del archivo, por favor verifique si se logro subir el excel en caso contrario intentelo nuevamente!");
                                        return;
                                    }
                                    else if (responseJSON.success == true) {
                                        document.getElementById('divPadreModalUploadFile2').setAttribute('style','');
                                        // MyBusquedaitemsGeneral();
                                        console.log(responseJSON.debug);
                                        Ext.get("DivArticulos<?php echo $opcGrillaContable; ?>").load({
										    url     : 'facturas_saldos_iniciales/bd/bd.php',
										    scripts : true,
										    nocache : true,
										    params  :
										    {
										        opc               : 'reloadBody',
												opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
												id_saldo_inicial  : '<?php echo $id_saldo_inicial; ?>',
												tipo_factura      : '<?php echo $tipo_factura; ?>',
										    }
										});
                                    }
                                    else{
                                        document.getElementById('divPadreModalUploadFile2').setAttribute('style','');
                                        if (responseJSON.debug=='documentos') {
                                            var errorsDetail = '';
                                            for (var i in responseJSON.detalle){
                                                errorsDetail += `<div class='row'>
                                                                    <div class='cell' data-col='1'></div>
                                                                    <div class='cell' data-col='2' style='width:515px;font-weight: bold;'>${i}</div>
                                                                </div>`+responseJSON.detalle[i]
                                            }

                                            var contentHtml = `<style>
                                                                    .sub-content[data-position="right"]{width: 100%; height: 386px; }
                                                                    .content-grilla-filtro .cell[data-col="1"]{width: 2px;}
                                                                    .content-grilla-filtro .cell[data-col="2"]{width: 85px;}
                                                                    .content-grilla-filtro .cell[data-col="3"]{width: 419px;}
                                                                    .content-grilla-filtro .cell[data-col="4"]{width: 211px;}
                                                                    .sub-content [data-width="input"]{width: 120px;}
                                                                </style>

                                                                <div class="main-content" style="height: 409px;overflow-y: auto;overflow-x: hidden;">
                                                                    <div class="sub-content" data-position="right">
                                                                        <div class="title">DETALLE DE ERRORES POR FACTURA DEL EXCEL</div>
                                                                        <div class="content-grilla-filtro">
                                                                            <div class="head">
                                                                                <div class="cell" data-col="1"></div>
                                                                                <div class="cell" data-col="2">Factura</div>
                                                                                <div class="cell" data-col="3">Detalle del error</div>
                                                                            </div>
                                                                            <div class="body" id="body_grilla_filtro">
                                                                                ${errorsDetail}
                                                                            </div>
                                                                        </div>

                                                                    </div>
                                                                </div>`;
                                        }
                                        else{
                                            var errorsDetail = '';
                                            for (var i in responseJSON.detalle){
                                                errorsDetail += responseJSON.detalle[i]
                                            }
                                            var contentHtml = `<style>
                                                                    .sub-content[data-position="right"]{width: 100%; height: 386px; }
                                                                    .content-grilla-filtro .cell[data-col="1"]{width: 2px;}
                                                                    .content-grilla-filtro .cell[data-col="2"]{width: 220px;}
                                                                    .content-grilla-filtro .cell[data-col="3"]{width: 268px;}
                                                                    .content-grilla-filtro .cell[data-col="4"]{width: 211px;}
                                                                    .sub-content [data-width="input"]{width: 120px;}
                                                                </style>

                                                                <div class="main-content" style="height: 409px;overflow-y: auto;overflow-x: hidden;">
                                                                    <div class="sub-content" data-position="right">
                                                                        <div class="title">DETALLE DE ERRORES</div>
                                                                        <div class="content-grilla-filtro">
                                                                            <div class="head">
                                                                                <div class="cell" data-col="1"></div>
                                                                                <div class="cell" data-col="2">Error generado</div>
                                                                                <div class="cell" data-col="3">Detalle del error</div>
                                                                            </div>
                                                                            <div class="body" id="body_grilla_filtro">
                                                                                ${errorsDetail}
                                                                            </div>
                                                                        </div>

                                                                    </div>
                                                                </div>`;
                                        }

                                        Win_Ventana_errors = new Ext.Window({
                                            width       : 600,
                                            height      : 400,
                                            id          : 'Win_Ventana_errors',
                                            title       : 'Detalle de errores',
                                            modal       : true,
                                            autoScroll  : false,
                                            closable    : true,
                                            autoDestroy : true,
                                            html        : contentHtml
                                        }).show();

                                    }
                                },
            onCancel : function(fileName){},
            messages :
            {
                typeError    : "{file}\nArchivo no permitido.\n\n Solo se permiten los siguientes archivo:\n\n'xls', 'xlsx', 'ods'",
                sizeError    : "\"{file}\"  Archivo muy grande, Tamano Maximo Permitido ( {sizeLimit} ).",
                minSizeError : "{file} is too small, minimum file size is {minSizeLimit}.",
                emptyError   : "{file} is empty, please select files again without it.",
                onLeave      : "Cargando Archivo."
            }
        });
    }
    createUploader();

    //VENTANA MODAL CON LA IMAGEN DE AYUDA PARA CARGAR EL EXCEL
    function imagenAyudaModal() {

       	var contenido = '<div style="margin: 0px auto;width:778px;" >'+
	       					'<img src="img/saldos_facturas.png"><br>'+
	       					'<spam style="color:#FFF;font-weight:bold;font-size:9px;">HAGA CLICK PARA CERRAR</spam>'+
       					'</div>';

      	parentModal = document.createElement("div");
        parentModal.innerHTML = '<div id="modal">'+contenido+'</div>';
        parentModal.setAttribute("id", "divPadreModal");
        parentModal.setAttribute("onclick", "cerrarVentanaModal()");
        document.body.appendChild(parentModal);
        document.getElementById("divPadreModal").className = "fondo_modal_saldos";

    }

    function cerrarVentanaModal(){
    	document.getElementById('divPadreModal').parentNode.removeChild(document.getElementById('divPadreModal'));
    }

    function generar_saldo_inicial(){
    	if (contArticulos<?php echo $opcGrillaContable; ?>==1) {alert("Aviso!\nNo hay documentos agregados"); return;}
    	Ext.get('terminar<?php echo $opcGrillaContable; ?>').load({
    		url     : 'facturas_saldos_iniciales/bd/bd.php',
    		scripts : true,
    		nocache : true,
    		params  :
    		{
    			opc : 'generar_saldo_inicial',
				idSaldoInicial    : '<?php echo $id_saldo_inicial; ?>',
				opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
    		}
    	});
    }

    function editar_saldo_inicial(){
    	Ext.get('terminar<?php echo $opcGrillaContable; ?>').load({
    		url     : 'facturas_saldos_iniciales/bd/bd.php',
    		scripts : true,
    		nocache : true,
    		params  :
    		{
    			opc : 'editar_saldo_inicial',
				idSaldoInicial    : '<?php echo $id_saldo_inicial; ?>',
				opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
    		}
    	});
    }

    //VENTANA PARA BUSCAR EL TERCERO
	function ventanaBusquedaTercero(cont) {
		var myalto  = Ext.getBody().getHeight();
		var myancho = Ext.getBody().getWidth();

		Win_Ventana_buscar_tercero = new Ext.Window({
		    width       : myancho-100,
		    height      : myalto-50,
		    id          : 'Win_Ventana_buscar_tercero',
		    title       : 'Buscar tercero',
		    modal       : true,
		    autoScroll  : false,
		    closable    : false,
		    autoDestroy : true,
		    autoLoad    :
		    {
		        url     : '../funciones_globales/grillas/BusquedaTerceros.php',
		        scripts : true,
		        nocache : true,
		        params  :
		        {
		            nombre_grilla : 'buscar_tercero',
		            cargaFuncion : 'responseVentanaBuscarTercero(id,'+cont+');',
		        }
		    },
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
		                    text        : 'Regresar',
		                    scale       : 'large',
		                    iconCls     : 'regresar',
		                    iconAlign   : 'left',
		                    handler     : function(){ Win_Ventana_buscar_tercero.close(id) }
		                }
		            ]
		        }
		    ]
		}).show();
	}

	//RENDERIZA LA VENTA QUE BUSCA EL TERCERO
	function responseVentanaBuscarTercero(id,cont) {
		var tercero  = document.getElementById('div_buscar_tercero_nombre_'+id).innerHTML
		, 	documento = document.getElementById('div_buscar_tercero_numero_identificacion_'+id).innerHTML;

		document.getElementById('idTecero<?php echo $opcGrillaContable; ?>_'+cont).value = id;
		document.getElementById('tercero_<?php echo $opcGrillaContable; ?>_'+cont).value = tercero;
		document.getElementById('nit_tercero_<?php echo $opcGrillaContable; ?>_'+cont).value     = documento;

		var idDocumento = document.getElementById("idInsertDoc<?php echo $opcGrillaContable; ?>_"+cont).value;
		if(idDocumento>0){
            document.getElementById('divImageSave<?php echo $opcGrillaContable; ?>_'+cont).style.display    = 'inline';
            document.getElementById('divImageDeshacer<?php echo $opcGrillaContable; ?>_'+cont).style.display = 'inline';
        }

		Win_Ventana_buscar_tercero.close(id)
	}

</script>