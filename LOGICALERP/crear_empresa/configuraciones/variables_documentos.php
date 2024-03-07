<?php

	$valuesGeneral = "('RAZON_SOCIAL', 'Razon social', '$idGrupoGeneral', '', 'razon_social', 'empresas', NULL, '1', ''),
					('ACTIVIDAD_ECONOMICA', 'Actividad economica', '$idGrupoGeneral', '', 'actividad_economica', 'empresas', NULL, '1', ''),
					('DIRECCION', 'Direccion', '$idGrupoGeneral', '', 'direccion', 'empresas', NULL, '1', ''),
					('TIPO_REGIMEN', 'Tipo regimen', '$idGrupoGeneral', '', 'tipo_regimen', 'empresas', NULL, '1', ''),
					('TIPO_IDENTIFICACION', 'Tipo identificacion', '$idGrupoGeneral', '', 'tipo_documento_nombre', 'empresas', NULL, '1', ''),
					('NUMERO_IDENTIFICACION', 'Numero de identificacion', '$idGrupoGeneral', '', 'nit_completo', 'empresas', NULL, '1', ''),
					('TELEFONO_EMPRESA', 'Telefono Empresa', '$idGrupoGeneral', '', 'telefono', 'empresas', NULL, '1', ''),
					('CELULAR_EMPRESA', 'Celular Empresa', '$idGrupoGeneral', '', 'celular', 'empresas', NULL, '1', ''),";

	$valuesCV = "('CV_CONSECUTIVO', 'Consecutivo documento', '$idGrupoCV', '', 'consecutivo', 'ventas_cotizaciones', NULL, '1', ''),
				('CV_SUCURSAL', 'Sucursal documento', '$idGrupoCV', '', 'sucursal', 'ventas_cotizaciones', NULL, '1', ''),
				('CV_RECIBE_ALMACEN', 'Persona que recibe en almacen', '$idGrupoCV', '', 'usuario_recibe_en_almacen', 'ventas_cotizaciones', NULL, '1', ''),
				('CV_FECHA_INICIAL', 'Fecha inicial del documento', '$idGrupoCV', '', 'fecha_inicio', 'ventas_cotizaciones', NULL, '1', ''),
				('CV_ELABORO', 'Persona que elabora el documento', '$idGrupoCV', '', 'usuario', 'ventas_cotizaciones', NULL, '1', ''),
				('CV_BODEGA', 'Bodega documento', '$idGrupoCV', '', 'bodega', 'ventas_cotizaciones', '', '1', ''),
				('CV_USUARIO', 'Usuario Elaboracion', '$idGrupoCV', '', 'usuario', 'ventas_cotizaciones', NULL, '1', ''),
				('CV_CC_USUARIO', 'Identificacion Usuario', '$idGrupoCV', '', 'documento_usuario', 'ventas_cotizaciones', NULL, '1', ''),";

	$valuesPV = "('PV_CONSECUTIVO', 'Consecutivo documento', '$idGrupoPV', '', 'consecutivo', 'ventas_pedidos', NULL, '1', ''),
				('PV_SUCURSAL', 'Sucursal documento', '$idGrupoPV', '', 'sucursal', 'ventas_pedidos', NULL, '1', ''),
				('PV_RECIBE_ALMACEN', 'Persona que recibe en almacen', '$idGrupoPV', '', 'usuario_recibe_en_almacen', 'ventas_pedidos', NULL, '1', ''),
				('PV_FECHA_INICIAL', 'Fecha inicial del documento', '$idGrupoPV', '', 'fecha_inicio', 'ventas_pedidos', NULL, '1', ''),
				('PV_ELABORO', 'Persona que elabora el documento', '$idGrupoPV', '', 'usuario', 'ventas_pedidos', NULL, '1', ''),
				('PV_BODEGA', 'Bodega documento', '$idGrupoPV', '', 'bodega', 'ventas_pedidos', '', '1', ''),
				('PV_USUARIO', 'Usuario Elaboracion', '$idGrupoPV', '', 'usuario', 'ventas_pedidos', NULL, '1', ''),
				('PV_CC_USUARIO', 'Identificacion Usuario', '$idGrupoPV', '', 'documento_usuario', 'ventas_pedidos', NULL, '1', ''),";

	$valuesRV = "('RV_CONSECUTIVO', 'Consecutivo documento', '$idGrupoRV', '', 'consecutivo', 'ventas_remisiones', NULL, '1', ''),
				('RV_SUCURSAL', 'Sucursal documento', '$idGrupoRV', '', 'sucursal', 'ventas_remisiones', NULL, '1', ''),
				('RV_RECIBE_ALMACEN', 'Persona que recibe en almacen', '$idGrupoRV', '', 'usuario_recibe_en_almacen', 'ventas_remisiones', NULL, '1', ''),
				('RV_FECHA_INICIAL', 'Fecha inicial del documento', '$idGrupoRV', '', 'fecha_inicio', 'ventas_remisiones', NULL, '1', ''),
				('RV_ELABORO', 'Persona que elabora el documento', '$idGrupoRV', '', 'usuario', 'ventas_remisiones', NULL, '1', ''),
				('RV_BODEGA', 'Bodega documento', '$idGrupoRV', '', 'bodega', 'ventas_remisiones', '', '1', ''),
				('RV_USUARIO', 'Usuario Elaboracion', '$idGrupoRV', '', 'usuario', 'ventas_remisiones', NULL, '1', ''),
				('RV_CC_USUARIO', 'Identificacion Usuario', '$idGrupoRV', '', 'documento_usuario', 'ventas_remisiones', NULL, '1', ''),";

	$valuesFV = "('FV_NUMERO_FACTURA', 'Numero ', '$idGrupoFV', '', 'numero_factura_completo', 'ventas_facturas', '', 1,''),
				('FV_FECHA_INICIO', 'Fecha de facturacion', '$idGrupoFV', '', 'fecha_inicio', 'ventas_facturas', '', 1,''),
				('FV_FECHA_VENCIMIENTO', 'Fecha de vencimiento de la factura', '$idGrupoFV', '', 'fecha_vencimiento', 'ventas_facturas', '', 1,''),
				('FV_PREFIJO_RESOLUCION_DIAN', 'Prefijo Resolucion Dian', '$idGrupoFV', '', 'prefijo', 'ventas_facturas_configuracion', '', 1,''),
				('FV_NUMERO_RESOLUCION_DIAN', 'Numero Resolucion Dian', '$idGrupoFV', '', 'consecutivo_resolucion', 'ventas_facturas_configuracion', '', 1,''),
				('FV_FECHA_RESOLUCION_DIAN', 'Fecha de la Resolucion Dian', '$idGrupoFV', '', 'fecha_resolucion', 'ventas_facturas_configuracion', '', 1,''),
				('FV_NUMERO_INICIAL_RESOLUCION', 'Numero inicial de la Resolucion Dian', '$idGrupoFV', '', 'numero_inicial_resolucion', 'ventas_facturas_configuracion', '', 1,''),
				('FV_NUMERO_FINAL_RESOLUCION', 'Numero final de la resolucion Dian', '$idGrupoFV', '', 'numero_final_resolucion', 'ventas_facturas_configuracion', '', 1,''),
				('FV_SUCURSAL','Sucursal ', '$idGrupoFV', '', 'sucursal', 'ventas_facturas', '', 1,''),
				('FV_VENDEDOR','Vendedor', '$idGrupoFV', '', 'nombre_vendedor', 'ventas_facturas', '', 1,''),";

	$valuesOC = "('OC_CONSECUTIVO', 'Consecutivo documento', '$idGrupoOC', '', 'consecutivo', 'compras_ordenes', NULL, '1', ''),
				('OC_SUCURSAL', 'Sucursal documento', '$idGrupoOC', '', 'sucursal', 'compras_ordenes', NULL, '1', ''),
				('OC_RECIBE_ALMACEN', 'Persona que recibe en almacen', '$idGrupoOC', '', 'usuario_recibe_en_almacen', 'compras_ordenes', NULL, '1', ''),
				('OC_FECHA_INICIAL', 'Fecha inicial del documento', '$idGrupoOC', '', 'fecha_inicio', 'compras_ordenes', NULL, '1', ''),
				('OC_ELABORO', 'Persona que elabora el documento', '$idGrupoOC', '', 'usuario', 'compras_ordenes', NULL, '1', ''),
				('OC_BODEGA', 'Bodega documento', '$idGrupoOC', '', 'bodega', 'compras_ordenes', '', '1', ''),
				('OC_USUARIO', 'Usuario Elaboracion', '$idGrupoOC', '', 'usuario', 'compras_ordenes', NULL, '1', ''),
				('OC_CC_USUARIO', 'Identificacion Usuario', '$idGrupoOC', '', 'documento_usuario', 'compras_ordenes', NULL, '1', ''),";

	$valuesFC = "('FC_CONSECUTIVO', 'Consecutivo documento', '$idGrupoFC', '', 'consecutivo', 'compras_facturas', NULL, '1', ''),
				('FC_SUCURSAL', 'Sucursal documento', '$idGrupoFC', '', 'sucursal', 'compras_facturas', NULL, '1', ''),
				('FC_RECIBE_ALMACEN', 'Persona que recibe en almacen', '$idGrupoFC', '', 'usuario_recibe_en_almacen', 'compras_facturas', NULL, '1', ''),
				('FC_FECHA_EMISION', 'Fecha de emision del documento', '$idGrupoFC', '', 'fecha_inicio', 'compras_facturas', NULL, '1', ''),
				('FC_FECHA_VENCIMIENTO', 'Fecha de vencimiento del documento', '$idGrupoFC', '', 'fecha_inicio', 'compras_facturas', NULL, '1', ''),
				('FC_ELABORO', 'Persona que elabora el documento', '$idGrupoFC', '', 'usuario', 'compras_facturas', NULL, '1', ''),
				('FC_BODEGA', 'Bodega documento', '$idGrupoFC', '', 'bodega', 'compras_facturas', '', '1', ''),
				('FC_USUARIO', 'Usuario Elaboracion', '$idGrupoFC', '', 'usuario', 'compras_facturas', NULL, '1', ''),
				('FC_CC_USUARIO', 'Identificacion Usuario', '$idGrupoFC', '', 'documento_usuario', 'compras_facturas', NULL, '1', ''),
				('FC_PREFIJO', 'Prefijo de Factura', '$idGrupoFC', '', 'prefijo_factura', 'compras_facturas',  NULL, '1', ''),
				('FC_NUMERO', 'Numero de Factura', '$idGrupoFC', '', 'numero_factura', 'compras_facturas',  NULL, '1', '')";

?>