<?php
  $sql = "SELECT
            N.consecutivo,
            N.sucursal,
            NE.documento_empleado,
            NE.nombre_empleado,
            NC.codigo_concepto,
            NC.concepto,
            NC.naturaleza,
            NC.valor_concepto,
            NC.valor_concepto_ajustado,
            NC.dias_laborados,
            NC.dias_adicionales,
            NC.base,
            N.fecha_documento,
            N.fecha_inicio,
            N.fecha_final
          FROM
            nomina_planillas_liquidacion AS N,
            nomina_planillas_liquidacion_empleados AS NE,
            nomina_planillas_liquidacion_empleados_conceptos AS NC
          WHERE N.fecha_inicio >= '2020-01-01' AND N.fecha_final<='2020-06-30'
          AND N.estado = 1
          AND N.id_empresa = 47
          AND NE.id_planilla = N.id
          AND NC.id_planilla = N.id
          AND NC.id_empleado = NE.id_empleado
          AND NC.codigo_concepto = 'PS'";
?>

SELECT

IF (
  N.id_empresa = 1,
  "COLOMBIA",
  "COMUNICACIONES"
) AS empresa N.consecutivo,
 N.sucursal,
 NE.documento_empleado,
 NE.nombre_empleado,
 NC.codigo_concepto,
 NC.concepto,
 NC.naturaleza,
 NC.valor_concepto,
 NC.valor_concepto_ajustado,
 NC.dias_laborados,
 NC.dias_adicionales,
 NC.base,
 N.fecha_documento,
 N.fecha_inicio,
 N.fecha_final
FROM
  nomina_planillas_liquidacion AS N,
  nomina_planillas_liquidacion_empleados AS NE,
  nomina_planillas_liquidacion_empleados_conceptos AS NC
WHERE
  N.fecha_inicio >= '2021-01-01'
AND N.fecha_final <= '2021-06-30'
AND N.estado = 1
AND (
  N.id_empresa = 47
  OR N.id_empresa = 1
)
AND NE.id_planilla = N.id
AND NC.id_planilla = N.id
AND NC.id_empleado = NE.id_empleado
AND NC.codigo_concepto = 'PS'