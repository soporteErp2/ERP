<?php
	include("../../../../configuracion/conectar.php");
	include("../../../../configuracion/define_variables.php");

	$id_empresa  = $_SESSION['EMPRESA'];

	switch ($opc) {
		case 'sincronizaPucPagoNiif':
			sincronizaPucPagoNiif($cuenta,$id_empresa,$link);
			break;

		case 'validarCuenta':
			validarCuenta($nombreTabla,$cuenta,$id_empresa,$link);
			break;

		case 'ventanaCuentaPagoTercero':
			ventanaCuentaPagoTercero($idCuentaPago,$id_empresa,$link);
			break;

		case 'guardarTerceroCuentaPago':
			guardarTerceroCuentaPago($idCuentaPago,$idTercero,$id_empresa,$link);
			break;

		case 'eliminarTerceroCuentaPago':
			eliminarTerceroCuentaPago($idCuentaPago,$id_empresa,$link);
			break;
	}

	function sincronizaPucPagoNiif($cuenta,$id_empresa,$link){
		$sqlNiif   = "SELECT COUNT(PN.id) AS cont_niif, PN.descripcion, P.cuenta_niif
						FROM puc AS P, puc_niif AS PN
						WHERE P.activo=1
							AND P.cuenta='$cuenta'
							AND P.id_empresa='$id_empresa'
							AND PN.activo=1
							AND PN.id_empresa=P.id_empresa
							AND PN.cuenta=P.cuenta_niif
							LIMIT 0,1";
		$queryNiif = mysql_query($sqlNiif,$link);

		$contNiif        = mysql_result($queryNiif,0,'cont_niif');
		$cuentaNiif      = mysql_result($queryNiif,0,'cuenta_niif');
		$descripcionNiif = mysql_result($queryNiif,0,'descripcion');

		if($contNiif == 0){ echo'<script>alert("No existe una cuenta niif asociada a la cuenta colgaap No. '.$cuenta.'");</script>'; }
		else{ echo'<script>codigo_puc_niif.value = "'.$cuentaNiif.'";</script>'; }

		echo'<img src="img/refresh.png" onclick="sincronizaPucPagoNiif()"/>';
	}

	function validarCuenta($nombreTabla,$cuenta,$id_empresa,$link){
		$sqlCuenta   = "SELECT COUNT(id) AS contCuenta FROM $nombreTabla WHERE activo=1 AND id_empresa='$id_empresa' AND cuenta LIKE '$cuenta%'";
		$queryCuenta = mysql_query($sqlCuenta,$link);

		echo mysql_result($queryCuenta, 0, 'contCuenta');
	}

	function ventanaCuentaPagoTercero($idCuentaPago,$id_empresa,$link){
		$sqlTercero   = "SELECT id_tercero,nit_tercero,tercero FROM configuracion_cuentas_pago WHERE id='$idCuentaPago'";
		$queryTercero = mysql_query($sqlTercero,$link);

		$id_tercero  = mysql_result($queryTercero, 0, 'id_tercero');
		$nit_tercero = mysql_result($queryTercero, 0, 'nit_tercero');
		$tercero     = mysql_result($queryTercero, 0, 'tercero');

		if($id_tercero == 0 || $id_tercero==''){ echo'<script>Ext.getCmp("Btn_eliminar_tercero_cuenta_pago").hide();</script>'; }

		echo'<div style="overflow:hidden; position:fixed; float:left; width:18px; height:18px;" id="loadSaveTerceroCuentaPago"></div>
			<div style="margin:10px; overflow:hidden; width:100%;">
				<input type="hidden" class="myfield" id="inputIdTerceroCuentaPago" value="'.$id_tercero.'"/>
				<div style="float:left; width:32%; height:25px;">Nit Tercero</div>
				<div style="float:left; width:68%; height:25px;"><input type="text" id="inputNitTerceroCuentaPago" style="width:165px;" class="myfield" value="'.$nit_tercero.'" onclick="buscarTerceroCuentaPago();" readonly/></div>
				<div style="float:left; width:32%; height:25px;">Nombre Tercero</div>
				<div style="float:left; width:68%; height:25px;"><input type="text" id="inputTerceroCuentaPago" style="width:165px;" class="myfield" value="'.$tercero.'" readonly/></div>
			</div>';
	}

	function guardarTerceroCuentaPago($idCuentaPago,$idTercero,$id_empresa,$link){
		$sqlTercero   = "UPDATE configuracion_cuentas_pago SET id_tercero='$idTercero' WHERE id='$idCuentaPago'";
		$queryTercero = mysql_query($sqlTercero,$link);

		echo "<script>Win_Ventana_Tercero_cuenta_pago.close(id);</script>";
	}

	function eliminarTerceroCuentaPago($idCuentaPago,$id_empresa,$link){
		$sqlTercero   = "UPDATE configuracion_cuentas_pago SET id_tercero='' WHERE id='$idCuentaPago'";
		$queryTercero = mysql_query($sqlTercero,$link);

		echo "<script>Win_Ventana_Tercero_cuenta_pago.close(id);</script>";
	}

?>