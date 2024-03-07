SELECT
	VF.id,
	DV.fecha_registro,
	VF.fecha_inicio,
	VF.fecha_vencimiento,
	VF.numero_factura_completo,
	VF.nit,
	VF.cliente,
	VF.sucursal_cliente,
	VF.bodega,
	VF.sucursal,
	VF.codigo_centro_costo,
	VF.centro_costo,
	VFI.codigo,
	VFI.nombre,
	VFI.cantidad,
	VFI.costo_unitario,
	VFI.tipo_descuento,
	VFI.descuento,
	VFI.valor_impuesto,
	SUM(
		VFI.cantidad * VFI.costo_unitario
	) AS subtotal,

IF (
	VFI.tipo_descuento = 'porcentaje',
	(
		SUM(
			VFI.cantidad * VFI.costo_unitario
		) * VFI.descuento
	) / 100,
	VFI.descuento
) AS descuento_pesos,
 (
	(
		VFI.cantidad * VFI.costo_unitario - (

			IF (
				VFI.tipo_descuento = 'porcentaje',
				(
					SUM(
						VFI.cantidad * VFI.costo_unitario
					) * VFI.descuento
				) / 100,
				VFI.descuento
			)
		)
	) * VFI.valor_impuesto
) / 100 AS IVA,
 VFI.cantidad * VFI.costo_unitario - (

	IF (
		VFI.tipo_descuento = 'porcentaje',
		(
			SUM(
				VFI.cantidad * VFI.costo_unitario
			) * VFI.descuento
		) / 100,
		VFI.descuento
	)
) + (
	(
		VFI.cantidad * VFI.costo_unitario - (

			IF (
				VFI.tipo_descuento = 'porcentaje',
				(
					SUM(
						VFI.cantidad * VFI.costo_unitario
					) * VFI.descuento
				) / 100,
				VFI.descuento
			)
		)
	) * VFI.valor_impuesto
) / 100 AS total,

IF (
	VF.id_empresa = 1,
	'COLOMBIA',
	'COMUNICACIONES'
) AS empresa
FROM
	ventas_facturas AS VF
INNER JOIN devoluciones_venta AS DV ON VF.id = DV.id_documento_venta
INNER JOIN devoluciones_venta_inventario AS VFI ON VFI.id_devolucion_venta = DV.id
WHERE
	VF.activo = 1
AND (
	VF.id_empresa = 47
	OR VF.id_empresa = 1
)
AND VF.estado = 1 #AND VF.fecha_inicio BETWEEN '2016-01-01'AND '2016-01-31'
AND DV.fecha_registro BETWEEN '2016-07-01'
AND '2016-09-31'
AND VFI.id_devolucion_venta = DV.id
AND VF.id = DV.id_documento_venta
AND DV.estado = 1 -- AND VF.id
#AND VF.id_sucursal=45
GROUP BY
	VFI.id