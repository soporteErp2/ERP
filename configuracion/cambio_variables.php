<?php
include('define_variables.php');
error_reporting(0);

function CAMBIO_VARIABLES($id_intercambio, $cadena){

	if(strrpos($cadena, "ESTE_DOCUMENTO_ES_UN_PEDIDO") != false){
		$cadena = str_replace('ESTE_DOCUMENTO_ES_UN_PEDIDO', '', $cadena );
		$es_pedido = 'true';
	}else{
		$es_pedido = 'false';
	}

	$consul_conse = mysql_query("SELECT consecutivo_pedido from configuracion_global WHERE id = $_SESSION[ID_PROPIEDAD]");
	$consecutivo_pedido = mysql_result($consul_conse,0,'consecutivo_pedido');
	//CONSULTA LOS DATOS DEL PEDIDO
	$SQL1 = "SELECT 
				pedido.pedido,
				pedido.npedido,
				pedido.evento,
				pedido.id_cliente,
				pedido.contacto,
				pedido.id_ejecutivo,
				pedido.ejecutivo,
				pedido.id_usuario,
				pedido.usuario,
				forma_de_pago.nombre as Fpago
			 FROM 
			 	pedido 
			Inner Join forma_de_pago ON pedido.forma_de_pago = forma_de_pago.id 
			WHERE 
				pedido.id = '$id_intercambio'";
	
	$consul = mysql_query($SQL1);
	
	if(mysql_num_rows($consul)){
		
		$row = mysql_fetch_array($consul); //ARRAY DE PEDIDO
		if($row['npedido'] != '' &&  $es_pedido == 'true'){ // SI EL DOCUMENTO ES PEDIDO Y TIENE CONSECUTIVO PROPIO
			$NUMERO_DOCUMENTO = $row['npedido'];
		}else{
			$NUMERO_DOCUMENTO = $row['pedido'];
		}
		$NOMBRE_EVENTO = $row['evento'];
		$FORMA_DE_PAGO = $row['Fpago'];
		$ID_USUARIO = $row['id_usuario'];
		$USUARIO = $row['usuario'];
		$ID_EJECUTIVO = $row['id_ejecutivo'];
		$EJECUTIVO = $row['ejecutivo'];
	}	
	
	//CONSULTA LOS DATOS DE LA CONFIGURACION GLOBAL
	$CONSULVAR2 = mysql_query("SELECT * FROM configuracion_global ");
	if(mysql_num_rows($CONSULVAR2)){
		$EMPRESA = utf8_encode(mysql_result($CONSULVAR2,$_SESSION['ID_PROPIEDAD'],"nombre_empresa"));
		$SUCURSAL = utf8_encode(mysql_result($CONSULVAR2,$_SESSION['ID_PROPIEDAD'],"nombre_sucursal"));
		$EMPRESA_NIT = utf8_encode(mysql_result($CONSULVAR2,$_SESSION['ID_PROPIEDAD'],"nit"));
		$EMPRESA_REPRESENTANTE_LEGAL = utf8_encode(mysql_result($CONSULVAR2,$_SESSION['ID_PROPIEDAD'],"representante"));
		$EMPRESA_CEDULA_REPRESENTANTE_LEGAL = utf8_encode(mysql_result($CONSULVAR2,$_SESSION['ID_PROPIEDAD'],"cedula"));
		$EMPRESA_CIUDAD_CEDULA_REPRESENTANTE_LEGAL = utf8_encode(mysql_result($CONSULVAR2,$_SESSION['ID_PROPIEDAD'],"origen_cedula"));
		$EMPRESA_DIRECCION = utf8_encode(mysql_result($CONSULVAR2,$_SESSION['ID_PROPIEDAD'],"direccion"));
		$EMPRESA_TELEFONO = utf8_encode(mysql_result($CONSULVAR2,$_SESSION['ID_PROPIEDAD'],"Telefonos"));
		$EMPRESA_EJECUTIVO = utf8_encode(mysql_result($CONSULVAR2,$_SESSION['ID_PROPIEDAD'],"ejecutivo"));
	}
	
	//CONSULTA FIRMA DEL USUARIO
	//echo $ID_USUARIO.'<br />';
	$con_firma = mysql_query("SELECT firma FROM usuarios WHERE id = '$ID_USUARIO'");

		$row_firma = mysql_result($con_firma,0,'firma');
		if($row_firma != ''){
			//echo $row_firma[firma];
			$FIRMA_ELABORACION_DOCUMENTO = str_replace('<BR>', '<br />', $row_firma);
			$FIRMA_ELABORACION_DOCUMENTO = utf8_encode($FIRMA_ELABORACION_DOCUMENTO);
		}else{
			//echo $row[usuario].'<BR />Ejecutivo Comercial';
			$FIRMA_ELABORACION_DOCUMENTO = utf8_encode($USUARIO);
		}

	//CONSULTA FIRMA DEL EJECUTIVO
	if($EMPRESA_EJECUTIVO == 'true'){
		$con_firma2 = mysql_query("SELECT firma FROM usuarios WHERE id = $ID_EJECUTIVO");
		$row_firma2 = mysql_result($con_firma2,0,'firma');
		if($row_firma2['firma'] != ''){
			//echo $row_firma[firma]; 
			$FIRMA_EJECUTIVO_DOCUMENTO = str_replace('<BR>', '<br />', $row_firma2);
		}else{
			//echo $row[ejecutivo].'<BR />Ejecutivo Comercial'; 
			$FIRMA_EJECUTIVO_DOCUMENTO =  $EJECUTIVO;
		}	
	}else{
		$FIRMA_EJECUTIVO_DOCUMENTO = $FIRMA_ELABORACION_DOCUMENTO;
	}
	
	
	//CONSULTA LOS DATOS DEL TERCERO
	$CONSULVAR = mysql_query("SELECT * FROM terceros WHERE id = $row[id_cliente] ");
	if(mysql_num_rows($CONSULVAR)){
		$TERCERO = utf8_encode(mysql_result($CONSULVAR,0,"empresa"));
		$TERCERO_NIT = utf8_encode(mysql_result($CONSULVAR,0,"nit").'-'.mysql_result($CONSULVAR,0,"dv"));
		$TERCERO_REPRESENTANTE_LEGAL = utf8_encode(mysql_result($CONSULVAR,0,"representante"));
		$TERCERO_CEDULA_REPRESENTANTE_LEGAL = utf8_encode(mysql_result($CONSULVAR,0,"c_representante"));
		$TERCERO_CIUDAD_CEDULA_REPRESENTANTE_LEGAL = utf8_encode(mysql_result($CONSULVAR,0,"de_representante"));
		$TERCERO_DIRECCION = utf8_encode(mysql_result($CONSULVAR,0,"direccion"));
		$TERCERO_TELEFONO = utf8_encode(mysql_result($CONSULVAR,0,"telefono"));
	}
	
	//CONSULTA LOS DATOS DEL CONTACTO
	$SQL4a = "SELECT * FROM terceros_contactos WHERE id_tercero =  '$row[id_cliente]'";
	$SQL4b = "SELECT * FROM terceros_contactos_email WHERE id_tercero =  '$row[id_cliente]' AND id_contacto = $row[contacto] AND activo = 1";
	
	$cual_tercero = $row['contacto'];
	
	$CONSULVAR4a = mysql_query($SQL4a);
	$CONSULVAR4b = mysql_query($SQL4b);
	if(mysql_num_rows($CONSULVAR4a)){
		$TERCERO_CONTACTO = utf8_encode(mysql_result($CONSULVAR4a,$cual_tercero,"nombre"));
		$TERCERO_CONTACTO_CARGO = utf8_encode(mysql_result($CONSULVAR4a,$cual_tercero,"cargo"));
	}
	if(mysql_num_rows($CONSULVAR4b)){
		$TERCERO_CONTACTO_EMAIL = utf8_encode(mysql_result($CONSULVAR4b,$cual_tercero,"email"));
	}else{
		$TERCERO_CONTACTO_EMAIL = '';
	}
	
	//CONSULTA LOS DATOS DEL CONTRATO
	$CONSULVAR3 = mysql_query("SELECT * FROM cpedido WHERE id = '$id_intercambio'");
	if(mysql_num_rows($CONSULVAR3)){
		$FIRMA_TERCERO = utf8_encode(mysql_result($CONSULVAR3,0,"firma_tercero"));
		$FIRMA_TERCERO_CEDULA = utf8_encode(mysql_result($CONSULVAR3,0,"firma_cedula"));
		$FECHA_CONTRATO = utf8_encode(mysql_result($CONSULVAR3,0,"revision_fecha"));
	}
	
	$cadena = str_replace('style="background-color: rgb(255, 204, 51);"', '',$cadena );
	$cadena = str_replace('/ASISTE/ARCHIVOS_PROPIOS/mylogoempresa.png', '../../ARCHIVOS_PROPIOS/logo_'.$_SESSION['ID_PROPIEDAD'].'.png',$cadena );
	$cadena = str_replace('[NUMERO_DOCUMENTO]', $NUMERO_DOCUMENTO, $cadena );
	//$cadena = str_replace('[NUMERO_REVISION]', $NUMERO_REVISION, $cadena ); ==> ESTE REPLACE SE HACE EN LA RAIZ DEL DOCUMENTO, YA QUE AQUI NO SE PUEDE CALCULAR LAREVISION
	$cadena = str_replace('[NOMBRE_EVENTO]', $NOMBRE_EVENTO, $cadena );
	$cadena = str_replace('[FORMA_DE_PAGO]', $FORMA_DE_PAGO, $cadena );
	$cadena = str_replace('[FIRMA_ELABORACION_DOCUMENTO]', $FIRMA_ELABORACION_DOCUMENTO, $cadena );
	$cadena = str_replace('[FIRMA_EJECUTIVO_DOCUMENTO]', $FIRMA_EJECUTIVO_DOCUMENTO, $cadena );
	
	$cadena = str_replace('[EMPRESA]', $EMPRESA, $cadena );
	$cadena = str_replace('[SUCURSAL]', $SUCURSAL, $cadena );
	$cadena = str_replace('[EMPRESA_NIT]', $EMPRESA_NIT, $cadena );
	$cadena = str_replace('[EMPRESA_REPRESENTANTE_LEGAL]', $EMPRESA_REPRESENTANTE_LEGAL, $cadena );
	$cadena = str_replace('[EMPRESA_CEDULA_REPRESENTANTE_LEGAL]', $EMPRESA_CEDULA_REPRESENTANTE_LEGAL, $cadena );
	$cadena = str_replace('[EMPRESA_CIUDAD_CEDULA_REPRESENTANTE_LEGAL]', $EMPRESA_CIUDAD_CEDULA_REPRESENTANTE_LEGAL, $cadena );
	$cadena = str_replace('[EMPRESA_DIRECCION]', $EMPRESA_DIRECCION, $cadena );
	$cadena = str_replace('[EMPRESA_TELEFONO]', $EMPRESA_TELEFONO, $cadena );
	
	$cadena = str_replace('[TERCERO]', $TERCERO, $cadena );
	$cadena = str_replace('[TERCERO_NIT]', $TERCERO_NIT, $cadena );
	$cadena = str_replace('[TERCERO_REPRESENTANTE_LEGAL]', $TERCERO_REPRESENTANTE_LEGAL, $cadena );
	$cadena = str_replace('[TERCERO_CEDULA_REPRESENTANTE_LEGAL]', $TERCERO_CEDULA_REPRESENTANTE_LEGAL, $cadena );
	$cadena = str_replace('[TERCERO_CIUDAD_CEDULA_REPRESENTANTE_LEGAL]', $TERCERO_CIUDAD_CEDULA_REPRESENTANTE_LEGAL, $cadena );
	$cadena = str_replace('[FIRMA_TERCERO]', $FIRMA_TERCERO, $cadena );
	$cadena = str_replace('[FIRMA_TERCERO_CEDULA]', $FIRMA_TERCERO_CEDULA, $cadena );
	$cadena = str_replace('[TERCERO_DIRECCION]', $TERCERO_DIRECCION, $cadena );
	$cadena = str_replace('[TERCERO_TELEFONO]', $TERCERO_TELEFONO, $cadena );
	$cadena = str_replace('[TERCERO_CONTACTO]', $TERCERO_CONTACTO, $cadena );
	$cadena = str_replace('[TERCERO_CONTACTO_CARGO]', $TERCERO_CONTACTO_CARGO, $cadena );
	$cadena = str_replace('[TERCERO_CONTACTO_EMAIL]', $TERCERO_CONTACTO_EMAIL, $cadena );
	
	$cadena = str_replace('[FECHA_CONTRATO]', fecha_larga_hora($FECHA_CONTRATO), $cadena );
	
	return $cadena;
	//return mysql_errno().": ".mysql_error();
}	
?>