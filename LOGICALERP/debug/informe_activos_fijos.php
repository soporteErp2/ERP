<?php

  include_once("../../configuracion/conectar.php");
  //INFORME DISEÑADO PARA PLATAFORMA Y SU PRESENTACION DE IMPUESTOS ANUALES
  $sql = "SELECT
  FC.sucursal,
  FC.fecha_inicio,
  FC.fecha_final,
  FC.consecutivo,
  FC.numero_factura,
  FC.nit,
  FC.proveedor,
  FC.sucursal,
  FC.bodega,
  FCI.codigo,
  FCI.nombre,
  FCI.cantidad,
  FCI.costo_unitario,
  FCI.tipo_descuento,
  FCI.descuento,
  FCI.impuesto,
  FCI.valor_impuesto AS porcentaje_impuesto,

IF (
  FCI.descuento > 0,

IF (
  FCI.tipo_descuento = 'porcentaje',
  (
    FCI.cantidad * FCI.costo_unitario
  ) - (
    (
      FCI.cantidad * FCI.costo_unitario
    ) * FCI.descuento / 100
  ),
  (
    FCI.cantidad * FCI.costo_unitario
  ) - FCI.descuento
),
 FCI.cantidad * FCI.costo_unitario
) AS subtotal,

IF (
  FCI.descuento > 0,

IF (
  FCI.tipo_descuento = 'porcentaje',
  (
    FCI.cantidad * FCI.costo_unitario
  ) - (
    (
      FCI.cantidad * FCI.costo_unitario
    ) * FCI.descuento / 100
  ),
  (
    FCI.cantidad * FCI.costo_unitario
  ) - FCI.descuento
),
 FCI.cantidad * FCI.costo_unitario
) * FCI.valor_impuesto / 100 AS valor_impuesto
FROM
  compras_facturas AS FC
LEFT JOIN compras_facturas_inventario AS FCI ON FC.id = FCI.id_factura_compra
WHERE
  FC.activo = 1
AND FC.id_empresa = 47
AND FC.fecha_inicio >= '2019-01-01'
AND FC.fecha_inicio <= '2019-12-31'
AND (
  FC.bodega != ""
  OR FC.bodega != NULL
)
AND FCI.opcion_activo_fijo = 'true'
ORDER BY
  FC.fecha_inicio ASC";



?>
