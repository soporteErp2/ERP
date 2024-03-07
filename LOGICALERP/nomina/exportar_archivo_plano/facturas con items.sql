SELECT
	VF.fecha_inicio,
	VF.fecha_vencimiento,
	VF.numero_factura_completo,
	VF.nit,
	VF.cliente,
	VF.sucursal_cliente,
	VF.bodega,
	VF.sucursal,VF.id_sucursal,
	VF.codigo_centro_costo,
	VF.centro_costo,
	VFI.codigo,
	VFI.nombre,
	SUM(VFI.cantidad) AS cantidad,
	VFI.costo_unitario,
	VFI.tipo_descuento,
	VFI.descuento,
	VFI.valor_impuesto,	
  IF (VFI.tipo_descuento = 'porcentaje',((	SUM(VFI.cantidad) * VFI.costo_unitario) * VFI.descuento) / 100,VFI.descuento) AS descuento_pesos,
  #SUM(VFI.cantidad * VFI.costo_unitario) AS subtotal,
SUM(VFI.cantidad) * VFI.costo_unitario-(IF(VFI.tipo_descuento = 'porcentaje',(SUM(VFI.cantidad * VFI.costo_unitario) * VFI.descuento) / 100,VFI.descuento)) AS subtotal,
((SUM(VFI.cantidad) * VFI.costo_unitario-(IF(VFI.tipo_descuento = 'porcentaje',(SUM(VFI.cantidad * VFI.costo_unitario) * VFI.descuento) / 100,VFI.descuento)))*VFI.valor_impuesto)/100 AS IVA,
SUM(VFI.cantidad) * VFI.costo_unitario-(IF(VFI.tipo_descuento = 'porcentaje',(SUM(VFI.cantidad * VFI.costo_unitario) * VFI.descuento) / 100,VFI.descuento))+((SUM(VFI.cantidad) * VFI.costo_unitario-(IF(VFI.tipo_descuento = 'porcentaje',(SUM(VFI.cantidad * VFI.costo_unitario) * VFI.descuento) / 100,VFI.descuento)))*VFI.valor_impuesto)/100 AS total,
#REPLACE((VFI.cantidad * VFI.costo_unitario-(IF(VFI.tipo_descuento = 'porcentaje',(SUM(VFI.cantidad * VFI.costo_unitario) * VFI.descuento) / 100,VFI.descuento))+((VFI.cantidad * VFI.costo_unitario-(IF(VFI.tipo_descuento = 'porcentaje',(SUM(VFI.cantidad * VFI.costo_unitario) * VFI.descuento) / 100,VFI.descuento)))*VFI.valor_impuesto)/100),'.',','),
IF(VF.id_empresa=1,'COLOMBIA','COMUNICACIONES') AS empresa
FROM
	ventas_facturas AS VF
INNER JOIN ventas_facturas_inventario AS VFI
WHERE
	VF.activo = 1
AND (
	VF.id_empresa = 47	OR VF.id_empresa = 1
)
AND (VF.estado = 1 OR VF.estado=2)
AND VF.fecha_inicio BETWEEN '2016-07-01' AND '2016-09-31'
AND VFI.id_factura_venta = VF.id
#AND VF.id_sucursal=40
#AND VF.id_sucursal=45
GROUP BY
	VF.id,VFI.id_inventario,VFI.costo_unitario