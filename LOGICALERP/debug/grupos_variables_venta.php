
<?php 
	$id_empresa = 1;

	for($id=41; $id<=68; $id++){
		echo "INSERT INTO variables (nombre,detalle,id_grupo,grupo,campo,tabla,funcion,automatica,id_empresa)
				VALUES ('RAZON_SOCIAL', 'Razon social', '$id', '', 'razon_social', 'empresas', NULL, '1', '2'),
				('ACTIVIDAD_ECONOMICA', 'Actividad economica', '$id', '', 'actividad_economica', 'empresas', NULL, '1', '2'),
				('DIRECCION', 'Direccion', '$id', '', 'direccion', 'empresas', NULL, '1', '2'),
				('TIPO_REGIMEN', 'Tipo regimen', '$id', '', 'tipo_regimen', 'empresas', NULL, '1', '2'),
				('TIPO_IDENTIFICACION', 'Tipo identificacion', '$id', '', 'tipo_documento_nombre', 'empresas', NULL, '1', '2'),
				('NUMERO_IDENTIFICACION', 'Numero de identificacion', '$id', '', 'nit_completo', 'empresas', NULL, '1', '2'),
				('TELEFONO_EMPRESA', 'Telefono Empresa', '$id', '', 'telefono', 'empresas', NULL, '1', '2'),
				('CELULAR_EMPRESA', 'Celular Empresa', '$id', '', 'celular', 'empresas', NULL, '1', '2'),
				('CV_CONSECUTIVO', 'Consecutivo documento', '$id', '', 'consecutivo', 'ventas_cotizaciones', NULL, '1', '2'),
				('CV_SUCURSAL', 'Sucursal documento', '$id', '', 'sucursal', 'ventas_cotizaciones', NULL, '1', '2'),
				('CV_RECIBE_ALMACEN', 'Persona que recibe en almacen', '$id', '', 'usuario_recibe_en_almacen', 'ventas_cotizaciones', NULL, '1', '2'),
				('CV_FECHA_INICIAL', 'Fecha inicial del documento', '$id', '', 'fecha_inicio', 'ventas_cotizaciones', NULL, '1', '2'),
				('CV_ELABORO', 'Persona que elabora el documento', '$id', '', 'usuario', 'ventas_cotizaciones', NULL, '1', '2'),
				('CV_BODEGA', 'Bodega documento', '$id', '', 'bodega', 'ventas_cotizaciones', '', '1', '2'),
				('CV_USUARIO', 'Usuario Elaboracion', '$id', '', 'usuario', 'ventas_cotizaciones', NULL, '1', '2'),
				('CV_CC_USUARIO', 'Identificacion Usuario', '$id', '', 'documento_usuario', 'ventas_cotizaciones', NULL, '1', '2'),";

		$id++;
		echo "('RAZON_SOCIAL', 'Razon social', '$id', '', 'razon_social', 'empresas', NULL, '1', '2'),
				('ACTIVIDAD_ECONOMICA', 'Actividad economica', '$id', '', 'actividad_economica', 'empresas', NULL, '1', '2'),
				('DIRECCION', 'Direccion', '$id', '', 'direccion', 'empresas', NULL, '1', '2'),
				('TIPO_REGIMEN', 'Tipo regimen', '$id', '', 'tipo_regimen', 'empresas', NULL, '1', '2'),
				('TIPO_IDENTIFICACION', 'Tipo identificacion', '$id', '', 'tipo_documento_nombre', 'empresas', NULL, '1', '2'),
				('NUMERO_IDENTIFICACION', 'Numero de identificacion', '$id', '', 'nit_completo', 'empresas', NULL, '1', '2'),
				('TELEFONO_EMPRESA', 'Telefono Empresa', '$id', '', 'telefono', 'empresas', NULL, '1', '2'),
				('CELULAR_EMPRESA', 'Celular Empresa', '$id', '', 'celular', 'empresas', NULL, '1', '2'),
				('PV_CONSECUTIVO', 'Consecutivo documento', '$id', '', 'consecutivo', 'ventas_cotizaciones', NULL, '1', '2'),
				('PV_SUCURSAL', 'Sucursal documento', '$id', '', 'sucursal', 'ventas_cotizaciones', NULL, '1', '2'),
				('PV_RECIBE_ALMACEN', 'Persona que recibe en almacen', '$id', '', 'usuario_recibe_en_almacen', 'ventas_cotizaciones', NULL, '1', '2'),
				('PV_FECHA_INICIAL', 'Fecha inicial del documento', '$id', '', 'fecha_inicio', 'ventas_cotizaciones', NULL, '1', '2'),
				('PV_ELABORO', 'Persona que elabora el documento', '$id', '', 'usuario', 'ventas_cotizaciones', NULL, '1', '2'),
				('PV_BODEGA', 'Bodega documento', '$id', '', 'bodega', 'ventas_cotizaciones', '', '1', '2'),
				('PV_USUARIO', 'Usuario Elaboracion', '$id', '', 'usuario', 'ventas_cotizaciones', NULL, '1', '2'),
				('PV_CC_USUARIO', 'Identificacion Usuario', '$id', '', 'documento_usuario', 'ventas_cotizaciones', NULL, '1', '2'),";

		$id++;
		echo "('RAZON_SOCIAL', 'Razon social', '$id', '', 'razon_social', 'empresas', NULL, '1', '2'),
				('ACTIVIDAD_ECONOMICA', 'Actividad economica', '$id', '', 'actividad_economica', 'empresas', NULL, '1', '2'),
				('DIRECCION', 'Direccion', '$id', '', 'direccion', 'empresas', NULL, '1', '2'),
				('TIPO_REGIMEN', 'Tipo regimen', '$id', '', 'tipo_regimen', 'empresas', NULL, '1', '2'),
				('TIPO_IDENTIFICACION', 'Tipo identificacion', '$id', '', 'tipo_documento_nombre', 'empresas', NULL, '1', '2'),
				('NUMERO_IDENTIFICACION', 'Numero de identificacion', '$id', '', 'nit_completo', 'empresas', NULL, '1', '2'),
				('TELEFONO_EMPRESA', 'Telefono Empresa', '$id', '', 'telefono', 'empresas', NULL, '1', '2'),
				('CELULAR_EMPRESA', 'Celular Empresa', '$id', '', 'celular', 'empresas', NULL, '1', '2'),
				('RV_CONSECUTIVO', 'Consecutivo documento', '$id', '', 'consecutivo', 'ventas_cotizaciones', NULL, '1', '2'),
				('RV_SUCURSAL', 'Sucursal documento', '$id', '', 'sucursal', 'ventas_cotizaciones', NULL, '1', '2'),
				('RV_RECIBE_ALMACEN', 'Persona que recibe en almacen', '$id', '', 'usuario_recibe_en_almacen', 'ventas_cotizaciones', NULL, '1', '2'),
				('RV_FECHA_INICIAL', 'Fecha inicial del documento', '$id', '', 'fecha_inicio', 'ventas_cotizaciones', NULL, '1', '2'),
				('RV_ELABORO', 'Persona que elabora el documento', '$id', '', 'usuario', 'ventas_cotizaciones', NULL, '1', '2'),
				('RV_BODEGA', 'Bodega documento', '$id', '', 'bodega', 'ventas_cotizaciones', '', '1', '2'),
				('RV_USUARIO', 'Usuario Elaboracion', '$id', '', 'usuario', 'ventas_cotizaciones', NULL, '1', '2'),
				('RV_CC_USUARIO', 'Identificacion Usuario', '$id', '', 'documento_usuario', 'ventas_cotizaciones', NULL, '1', '2');";

		if($id_empresa == 1){ $id_empresa=47; }
		else{ $id_empresa++; }
	}

?>
