<?php

//========================//
// ARRAY CON LOS FORMATOS //
//========================//
$array_formatos['210000'] = array('nombre' => 'Estado de situación financiera, corriente/no corriente','filtro_corte_anual' => 'Si','filtro_rango_fechas'=>'No','filtro_cuentas'=>'No','insert'=>'true');
$array_formatos['310000'] = array('nombre' => 'Estado de resultado integral, resultado del periodo, por función de gasto','filtro_corte_anual' => 'Si','filtro_rango_fechas'=>'No','filtro_cuentas'=>'No','insert'=>'true');
$array_formatos['410000'] = array('nombre' => 'Estado del resultado integral, componentes ORI presentados netos de impuestos','filtro_corte_anual' => 'Si','filtro_rango_fechas'=>'No','filtro_cuentas'=>'No','insert'=>'true');
$array_formatos['520000'] = array('nombre' => 'Estado de flujos de efectivo, método indirecto','filtro_corte_anual' => 'Si','filtro_rango_fechas'=>'No','filtro_cuentas'=>'No','insert'=>'true');
//$array_formatos['800100'] = array('nombre' => 'Notas - Subclasificaciones de activos, pasivos y patrimonios','filtro_corte_anual' => 'Si','filtro_rango_fechas'=>'No','filtro_cuentas'=>'No','insert'=>'true');
//$array_formatos['800200'] = array('nombre' => 'Notas - Análisis de ingresos y gastos','filtro_corte_anual' => 'Si','filtro_rango_fechas'=>'No','filtro_cuentas'=>'No','insert'=>'true');
//$array_formatos['818000'] = array('nombre' => 'Notas - Partes relacionadas','filtro_corte_anual' => 'Si','filtro_rango_fechas'=>'No','filtro_cuentas'=>'No','insert'=>'true');
//$array_formatos['825700'] = array('nombre' => 'Notas - Participaciones en otras entidades','filtro_corte_anual' => 'Si','filtro_rango_fechas'=>'No','filtro_cuentas'=>'No','insert'=>'true');
//$array_formatos['832410'] = array('nombre' => 'Notas - Deterioro del valor de activos','filtro_corte_anual' => 'Si','filtro_rango_fechas'=>'No','filtro_cuentas'=>'No','insert'=>'true');
//$array_formatos['832600'] = array('nombre' => 'Notas – Arrendamientos','filtro_corte_anual' => 'Si','filtro_rango_fechas'=>'No','filtro_cuentas'=>'No','insert'=>'true');
//$array_formatos['880000'] = array('nombre' => 'Notas - Información adiciona','filtro_corte_anual' => 'Si','filtro_rango_fechas'=>'No','filtro_cuentas'=>'No','insert'=>'true');




//=========================================================================================//
// ARRAY CON LAS ESTRUCTURAS DE CADA INFORME //
//
// POSICIONES DEL ARRAY
// array_formatos['CODIGO DEL FORMATO']
// array_formatos_secciones['CODIGO DEL FORMATO']['CODIGO DE LA SECCION']
// array_formatos_secciones_filas['CODIGO DEL FORMATO']['CODIGO DE LA SECCION A LA QUE PERTENECE']
//
//==========================================================================================//

// FORMATO 210000 ESTADO DE SITUACIÓN FINANCIERA, CORRIENTE/NO CORRIENTE
$array_formatos_secciones['210000'][1] = array('orden'=>1,'nombre'=>'Estado de situación financiera','tipo'=>'sinopsis','descripcion_tipo'=>'sinopsis','totalizado'=>'false','formula_totalizado'=>'','codigo_seccion_padre'=>0);
// INFORMACION LLENADA POR EL USUARIO
// $array_formatos_secciones['210000'][2] = array('orden'=>1,'nombre'=>'Clase de Separación de la Información Financiera ','tipo'=>'tabla','descripcion_tipo'=>'tabla','totalizado'=>'false','formula_totalizado'=>'','codigo_seccion_padre'=>1);
// $array_formatos_secciones['210000'][3] = array('orden'=>1,'nombre'=>'Clase de Separación','tipo'=>'eje','descripcion_tipo'=>'eje de la tabla','totalizado'=>'false','formula_totalizado'=>'','codigo_seccion_padre'=>2);
// $array_formatos_secciones['210000'][4] = array('orden'=>1,'nombre'=>'Total por Clase de Separación','tipo'=>'miembro','descripcion_tipo'=>'columna de la tabla','totalizado'=>'false','formula_totalizado'=>'','codigo_seccion_padre'=>3);
// $array_formatos_secciones['210000'][5] = array('orden'=>1,'nombre'=>'Público','tipo'=>'miembro','descripcion_tipo'=>'columna de la tabla','totalizado'=>'false','formula_totalizado'=>'','codigo_seccion_padre'=>4);
// $array_formatos_secciones['210000'][6] = array('orden'=>2,'nombre'=>'Privado','tipo'=>'miembro','descripcion_tipo'=>'columna de la tabla','totalizado'=>'false','formula_totalizado'=>'','codigo_seccion_padre'=>4);

// SECCION CON SECCIONES //
$array_formatos_secciones['210000'][2] = array('orden'=>2,'nombre'=>'Activos','tipo'=>'sinopsis','descripcion_tipo'=>'sinopsis','totalizado'=>'true','formula_totalizado'=>'','codigo_seccion_padre'=>1);
// SECCION CON FILAS
$array_formatos_secciones['210000'][3] = array('orden'=>1,'nombre'=>'Activos Corrientes','tipo'=>'sinopsis','descripcion_tipo'=>'sinopsis','totalizado'=>'true','formula_totalizado'=>'','codigo_seccion_padre'=>2);
// FILAS DE LA SECCION ACTIVOS CORRIENTES
$array_formatos_secciones_filas['210000'][3][] = array('codigo'=> 1,'orden' => 1, 'nombre'=>'Inventarios Corrientes','naturaleza'=>'debito' );
$array_formatos_secciones_filas['210000'][3][] = array('codigo'=> 2,'orden' => 2, 'nombre'=>'Cuentas comerciales por cobrar y otras cuentas por cobrar corrientes ','naturaleza'=>'debito' );
$array_formatos_secciones_filas['210000'][3][] = array('codigo'=> 3,'orden' => 3, 'nombre'=>'Otros activos financieros corrientes','naturaleza'=>'debito' );
$array_formatos_secciones_filas['210000'][3][] = array('codigo'=> 4,'orden' => 4, 'nombre'=>'Otros activos no financieros corrientes','naturaleza'=>'debito' );
$array_formatos_secciones_filas['210000'][3][] = array('codigo'=> 5,'orden' => 5, 'nombre'=>'Efectivo y equivalentes al efectivo','naturaleza'=>'debito' );
$array_formatos_secciones_filas['210000'][3][] = array('codigo'=> 6,'orden' => 6, 'nombre'=>'Activos corrientes distintos de los activos no corrientes o grupo de activos para su disposición clasificados como mantenidos para la venta o como mantenidos para distribuir a los propietarios','naturaleza'=>'debito' );
$array_formatos_secciones_filas['210000'][3][] = array('codigo'=> 7,'orden' => 7, 'nombre'=>'Activos no corrientes o grupos de activos para su disposición clasificados como mantenidos para la venta o como mantenidos para distribuir a los propietarios','naturaleza'=>'debito' );
// SECCION CON FILAS
$array_formatos_secciones['210000'][4] = array('orden'=>2,'nombre'=>'Activos no Corrientes','tipo'=>'sinopsis','descripcion_tipo'=>'sinopsis','totalizado'=>'true','formula_totalizado'=>'','codigo_seccion_padre'=>2);
// FILAS DE LA SECCION ACTIVOS NO CORRIENTES
$array_formatos_secciones_filas['210000'][4][] = array('codigo'=> 8,'orden' => 1, 'nombre'=>'Propiedades, planta y equipo','naturaleza'=>'debito' );
$array_formatos_secciones_filas['210000'][4][] = array('codigo'=> 9,'orden' => 2, 'nombre'=>'Propiedad de inversión','naturaleza'=>'debito' );
$array_formatos_secciones_filas['210000'][4][] = array('codigo'=> 10,'orden' => 3, 'nombre'=>'Activos intangibles distintos de la plusvalía','naturaleza'=>'debito' );
$array_formatos_secciones_filas['210000'][4][] = array('codigo'=> 11,'orden' => 4, 'nombre'=>'Inversiones contabilizadas utilizando el método de la participación','naturaleza'=>'debito' );
$array_formatos_secciones_filas['210000'][4][] = array('codigo'=> 12,'orden' => 5, 'nombre'=>'Inversiones en subsidiarias, negocios conjuntos y asociadas','naturaleza'=>'debito' );
$array_formatos_secciones_filas['210000'][4][] = array('codigo'=> 13,'orden' => 6, 'nombre'=>'Cuentas comerciales por cobrar y otras cuentas por cobrar no corrientes','naturaleza'=>'debito' );
$array_formatos_secciones_filas['210000'][4][] = array('codigo'=> 14,'orden' => 7, 'nombre'=>'Inventarios no corrientes','naturaleza'=>'debito' );
$array_formatos_secciones_filas['210000'][4][] = array('codigo'=> 15,'orden' => 8, 'nombre'=>'Otros activos financieros no corrientes ','naturaleza'=>'debito' );
$array_formatos_secciones_filas['210000'][4][] = array('codigo'=> 16,'orden' => 9, 'nombre'=>'Otros activos no financieros no corrientes','naturaleza'=>'debito' );
// SECCION CON SECCIONES
$array_formatos_secciones['210000'][5] = array('orden'=>3,'nombre'=>'Patrimonio y Pasivos','tipo'=>'sinopsis','descripcion_tipo'=>'sinopsis','totalizado'=>'true','formula_totalizado'=>'','codigo_seccion_padre'=>1);
// SECCION CON SECCIONES
$array_formatos_secciones['210000'][6] = array('orden'=>1,'nombre'=>'Pasivos','tipo'=>'sinopsis','descripcion_tipo'=>'sinopsis','totalizado'=>'true','formula_totalizado'=>'','codigo_seccion_padre'=>5);
// SECCION CON SECCIONES
$array_formatos_secciones['210000'][7] = array('orden'=>1,'nombre'=>'Pasivos Corrientes','tipo'=>'sinopsis','descripcion_tipo'=>'sinopsis','totalizado'=>'true','formula_totalizado'=>'','codigo_seccion_padre'=>6);
// SECCION
$array_formatos_secciones['210000'][8] = array('orden'=>1,'nombre'=>'Disposiciones Actuales','tipo'=>'sinopsis','descripcion_tipo'=>'sinopsis','totalizado'=>'true','formula_totalizado'=>'','label_totalizado'=>'Total de provisiones corrientes','codigo_seccion_padre'=>7);
// FILAS DE LA SECCION DISPOSICIONES ACTUALES
$array_formatos_secciones_filas['210000'][8][] = array('codigo'=> 17,'orden' => 1, 'nombre'=>'Provisiones corrientes por beneficios a los empleados','naturaleza'=>'credito' );
$array_formatos_secciones_filas['210000'][8][] = array('codigo'=> 18,'orden' => 2, 'nombre'=>'Otras provisiones corrientes','naturaleza'=>'credito' );
// FILAS DE LA SECCION PASIVOS CORRIENTES
$array_formatos_secciones_filas['210000'][7][] = array('codigo'=> 19,'orden' => 1, 'nombre'=>'Cuentas por pagar comerciales y otras cuentas por pagar','naturaleza'=>'credito' );
$array_formatos_secciones_filas['210000'][7][] = array('codigo'=> 20,'orden' => 2, 'nombre'=>'Otros pasivos financieros corrientes','naturaleza'=>'credito' );
$array_formatos_secciones_filas['210000'][7][] = array('codigo'=> 21,'orden' => 3, 'nombre'=>'Otros pasivos no financieros corrientes','naturaleza'=>'credito' );
$array_formatos_secciones_filas['210000'][7][] = array('codigo'=> 22,'orden' => 4, 'nombre'=>'Pasivos corrientes distintos de los pasivos incluidos en grupos de activos para su disposición clasificados como mantenidos para la venta','naturaleza'=>'credito' );
$array_formatos_secciones_filas['210000'][7][] = array('codigo'=> 23,'orden' => 5, 'nombre'=>'Pasivos incluidos en grupos de activos para su disposición clasificados como mantenidos para la venta','naturaleza'=>'credito' );
// SECCION CON SECCIONES
$array_formatos_secciones['210000'][8] = array('orden'=>1,'nombre'=>'Pasivos no Corrientes','tipo'=>'sinopsis','descripcion_tipo'=>'sinopsis','totalizado'=>'true','formula_totalizado'=>'','codigo_seccion_padre'=>6);
// SECCION CON SECCIONES
$array_formatos_secciones['210000'][9] = array('orden'=>1,'nombre'=>'Provisiones no Corrientes','tipo'=>'sinopsis','descripcion_tipo'=>'sinopsis','totalizado'=>'true','formula_totalizado'=>'','codigo_seccion_padre'=>8);
// FILAS DE LA SECCION PROVISIONES NO CORRIENTES
$array_formatos_secciones_filas['210000'][9][] = array('codigo'=> 24,'orden' => 1, 'nombre'=>'Provisiones no corrientes por beneficios a los empleados','naturaleza'=>'credito' );
$array_formatos_secciones_filas['210000'][9][] = array('codigo'=> 25,'orden' => 2, 'nombre'=>'Otras provisiones no corrientes','naturaleza'=>'credito' );
// FILAS DE LA SECCION PASIVOS NO CORRIENTES
$array_formatos_secciones_filas['210000'][8][] = array('codigo'=> 26,'orden' => 1, 'nombre'=>'Cuentas comerciales por pagar y otras cuentas por pagar no corrientes','naturaleza'=>'credito' );
$array_formatos_secciones_filas['210000'][8][] = array('codigo'=> 27,'orden' => 2, 'nombre'=>'Otros pasivos financieros no corrientes ','naturaleza'=>'credito' );
$array_formatos_secciones_filas['210000'][8][] = array('codigo'=> 28,'orden' => 3, 'nombre'=>'Otros pasivos no financieros no corrientes','naturaleza'=>'credito' );

// SECCION
$array_formatos_secciones['210000'][9] = array('orden'=>2,'nombre'=>'Patrimonio','tipo'=>'sinopsis','descripcion_tipo'=>'sinopsis','totalizado'=>'true','label_totalizado'=>'Patrimonio','formula_totalizado'=>'','codigo_seccion_padre'=>10);
// FILAS DE LA SECCION PATRIMONIO
$array_formatos_secciones_filas['210000'][9][] = array('codigo'=> 29,'orden' => 1, 'nombre'=>'Fondo Social','naturaleza'=>'credito' );
$array_formatos_secciones_filas['210000'][9][] = array('codigo'=> 30,'orden' => 2, 'nombre'=>'Ganancias Acumuladas','naturaleza'=>'credito' );
$array_formatos_secciones_filas['210000'][9][] = array('codigo'=> 31,'orden' => 3, 'nombre'=>'Otras Reservas','naturaleza'=>'credito' );

//=========================================================================================//
// ARRAY CON LAS ESTRUCTURAS DE CADA INFORME //
//
// POSICIONES DEL ARRAY
// array_formatos['CODIGO DEL FORMATO']
// array_formatos_secciones['CODIGO DEL FORMATO']['CODIGO DE LA SECCION']
// array_formatos_secciones_filas['CODIGO DEL FORMATO']['CODIGO DE LA SECCION A LA QUE PERTENECE']
//
//==========================================================================================//

// FORMATO 310000  ESTADO DE RESULTADO INTEGRAL, RESULTADO DEL PERIODO, POR FUNCIÓN DE GASTO
$array_formatos_secciones['310000'][1] = array('orden'=>1,'nombre'=>'Estado del resultado integral, componentes ORI presentados netos de impuestos','tipo'=>'sinopsis','descripcion_tipo'=>'sinopsis','totalizado'=>'false','formula_totalizado'=>'','codigo_seccion_padre'=>0);
// INFORMACION LLENADA POR EL USUARIO
// $array_formatos_secciones['310000'][2] = array('orden'=>1,'nombre'=>'Resultado de periodo','tipo'=>'resumen','descripcion_tipo'=>'resumen','totalizado'=>'false','formula_totalizado'=>'','codigo_seccion_padre'=>1);
// $array_formatos_secciones['310000'][3] = array('orden'=>2,'nombre'=>'Clase de Separación de la Información Financiera ','tipo'=>'tabla','descripcion_tipo'=>'tabla','totalizado'=>'false','formula_totalizado'=>'','codigo_seccion_padre'=>1);
// $array_formatos_secciones['310000'][4] = array('orden'=>1,'nombre'=>'Clase de Separación','tipo'=>'eje','descripcion_tipo'=>'eje de la tabla','totalizado'=>'false','formula_totalizado'=>'','codigo_seccion_padre'=>2);
// $array_formatos_secciones['310000'][5] = array('orden'=>1,'nombre'=>'Total por Clase de Separación','tipo'=>'miembro','descripcion_tipo'=>'columna de la tabla','totalizado'=>'false','formula_totalizado'=>'','codigo_seccion_padre'=>3);
// $array_formatos_secciones['310000'][6] = array('orden'=>1,'nombre'=>'Público','tipo'=>'miembro','descripcion_tipo'=>'columna de la tabla','totalizado'=>'false','formula_totalizado'=>'','codigo_seccion_padre'=>4);
// $array_formatos_secciones['310000'][7] = array('orden'=>2,'nombre'=>'Privado','tipo'=>'miembro','descripcion_tipo'=>'columna de la tabla','totalizado'=>'false','formula_totalizado'=>'','codigo_seccion_padre'=>4);

$array_formatos_secciones['310000'][2] = array('orden'=>2,'nombre'=>'Ganancia (pérdida)','tipo'=>'sinopsis','descripcion_tipo'=>'','totalizado'=>'false','formula_totalizado'=>'','codigo_seccion_padre'=>1);
// FILAS DE LA SECCION GANANCIA (PERDIDA)
$array_formatos_secciones_filas['310000'][2][] = array('codigo'=> 1, 'orden' => 1, 'nombre'=>'Ingresos de actividades ordinarias','naturaleza'=>'credito' );
$array_formatos_secciones_filas['310000'][2][] = array('codigo'=> 2, 'orden' => 2, 'nombre'=>'Costo de ventas','naturaleza'=>'deudor' );
$array_formatos_secciones_filas['310000'][2][] = array('codigo'=> 3, 'orden' => 3, 'nombre'=>'Ganancia bruta','naturaleza'=>'credito' );
$array_formatos_secciones_filas['310000'][2][] = array('codigo'=> 4, 'orden' => 4, 'nombre'=>'Otros ingresos','naturaleza'=>'credito' );
$array_formatos_secciones_filas['310000'][2][] = array('codigo'=> 5, 'orden' => 5, 'nombre'=>'Costos de distribución','naturaleza'=>'deudor' );
$array_formatos_secciones_filas['310000'][2][] = array('codigo'=> 6, 'orden' => 6, 'nombre'=>'Gastos de administración','naturaleza'=>'deudor' );
$array_formatos_secciones_filas['310000'][2][] = array('codigo'=> 7, 'orden' => 7, 'nombre'=>'Otros gastos, por función','naturaleza'=>'deudor' );
$array_formatos_secciones_filas['310000'][2][] = array('codigo'=> 8, 'orden' => 8, 'nombre'=>'Otras ganancias (pérdidas) ','naturaleza'=>'credito' );
$array_formatos_secciones_filas['310000'][2][] = array('codigo'=> 9, 'orden' => 9, 'nombre'=>'Ganancia (pérdida) por actividades de operación','naturaleza'=>'credito' );
$array_formatos_secciones_filas['310000'][2][] = array('codigo'=> 10, 'orden' => 10, 'nombre'=>'Ganancias (pérdidas) que surgen de la baja en cuentas de activos financieros medidos al costo amortizado','naturaleza'=>'credito' );
$array_formatos_secciones_filas['310000'][2][] = array('codigo'=> 11, 'orden' => 11, 'nombre'=>'Ingresos financieros ','naturaleza'=>'credito' );
$array_formatos_secciones_filas['310000'][2][] = array('codigo'=> 12, 'orden' => 12, 'nombre'=>'Costos financieros','naturaleza'=>'deudor' );
$array_formatos_secciones_filas['310000'][2][] = array('codigo'=> 13, 'orden' => 13, 'nombre'=>'Pérdidas por deterioro de valor (ganancias por deterioro de valor y reversión de pérdidas por deterioro de valor) determinadas de acuerdo con la NIIF 9','naturaleza'=>'deudor' );
$array_formatos_secciones_filas['310000'][2][] = array('codigo'=> 14, 'orden' => 14, 'nombre'=>'Participación en las ganancias (pérdidas) de asociadas y negocios conjuntos que se contabilicen utilizando el método de la participación','naturaleza'=>'credito' );
$array_formatos_secciones_filas['310000'][2][] = array('codigo'=> 15, 'orden' => 15, 'nombre'=>'Otros ingresos (gastos) procedentes de subsidiarias, entidades controladas de forma conjunta y asociadas','naturaleza'=>'credito' );
$array_formatos_secciones_filas['310000'][2][] = array('codigo'=> 16, 'orden' => 16, 'nombre'=>'Ganancias (pérdidas) que surgen de diferencias entre el costo amortizado anterior y el valor razonable de activos financieros reclasificados de la categoría de medición costo amortizado a la categoría de medición de valor razonable con cambios en resultados ','naturaleza'=>'credito' );
$array_formatos_secciones_filas['310000'][2][] = array('codigo'=> 17, 'orden' => 17, 'nombre'=>'Ganancia (pérdida) acumulada anteriormente reconocida en otro resultado integral que surge de la reclasificación de activos financieros de la categoría de medición de valor razonable con cambios en otro resultado integral a la de valor razonable con cambios en resultados','naturaleza'=>'credito' );
$array_formatos_secciones_filas['310000'][2][] = array('codigo'=> 18, 'orden' => 18, 'nombre'=>'Ganancia (pérdida), antes de impuestos','naturaleza'=>'credito' );
$array_formatos_secciones_filas['310000'][2][] = array('codigo'=> 19, 'orden' => 19, 'nombre'=>'Ganancia (pérdida) procedente de operaciones continuadas','naturaleza'=>'credito' );
$array_formatos_secciones_filas['310000'][2][] = array('codigo'=> 20, 'orden' => 20, 'nombre'=>'Ganancia (pérdida) procedente de operaciones discontinuadas','naturaleza'=>'credito' );
$array_formatos_secciones_filas['310000'][2][] = array('codigo'=> 21, 'orden' => 21, 'nombre'=>'Ganancia (pérdida)','naturaleza'=>'credito' );

//=========================================================================================//
// ARRAY CON LAS ESTRUCTURAS DE CADA INFORME //
//
// POSICIONES DEL ARRAY
// array_formatos['CODIGO DEL FORMATO']
// array_formatos_secciones['CODIGO DEL FORMATO']['CODIGO DE LA SECCION']
// array_formatos_secciones_filas['CODIGO DEL FORMATO']['CODIGO DE LA SECCION A LA QUE PERTENECE']
//
//==========================================================================================//

// FORMATO 410000  ESTADO DE RESULTADO INTEGRAL, RESULTADO DEL PERIODO, POR FUNCIÓN DE GASTO
$array_formatos_secciones['410000'][1] = array('orden'=>1,'nombre'=>'Estado del resultado integral','tipo'=>'sinopsis','descripcion_tipo'=>'sinopsis','totalizado'=>'false','formula_totalizado'=>'','codigo_seccion_padre'=>0);
// INFORMACION LLENADA POR EL USUARIO
// $array_formatos_secciones['410000'][2] = array('orden'=>1,'nombre'=>'Resultado de periodo','tipo'=>'resumen','descripcion_tipo'=>'resumen','totalizado'=>'false','formula_totalizado'=>'','codigo_seccion_padre'=>1);
// $array_formatos_secciones['410000'][3] = array('orden'=>2,'nombre'=>'Clase de Separación de la Información Financiera ','tipo'=>'tabla','descripcion_tipo'=>'tabla','totalizado'=>'false','formula_totalizado'=>'','codigo_seccion_padre'=>1);
// $array_formatos_secciones['410000'][4] = array('orden'=>1,'nombre'=>'Clase de Separación','tipo'=>'eje','descripcion_tipo'=>'eje de la tabla','totalizado'=>'false','formula_totalizado'=>'','codigo_seccion_padre'=>3);
// $array_formatos_secciones['410000'][5] = array('orden'=>1,'nombre'=>'Total por Clase de Separación','tipo'=>'miembro','descripcion_tipo'=>'columna de la tabla','totalizado'=>'false','formula_totalizado'=>'','codigo_seccion_padre'=>4);
// $array_formatos_secciones['410000'][6] = array('orden'=>1,'nombre'=>'Público','tipo'=>'miembro','descripcion_tipo'=>'columna de la tabla','totalizado'=>'false','formula_totalizado'=>'','codigo_seccion_padre'=>5);
// $array_formatos_secciones['410000'][7] = array('orden'=>2,'nombre'=>'Privado','tipo'=>'miembro','descripcion_tipo'=>'columna de la tabla','totalizado'=>'false','formula_totalizado'=>'','codigo_seccion_padre'=>5);
$array_formatos_secciones['410000'][2] = array('orden'=>3,'nombre'=>'Ganancia (pérdida)','tipo'=>'','descripcion_tipo'=>'','totalizado'=>'false','formula_totalizado'=>'','codigo_seccion_padre'=>1);
$array_formatos_secciones['410000'][3] = array('orden'=>4,'nombre'=>'Otro Resultado Integral ','tipo'=>'sinopsis','descripcion_tipo'=>'sinopsis','totalizado'=>'true','formula_totalizado'=>'','label_totalizado'=>'Resultado integral total','codigo_seccion_padre'=>1);
$array_formatos_secciones['410000'][4]= array('orden'=>1,'nombre'=>'Componentes de otro resultado integral que no se reclasificarán al resultado del periodo, neto de impuestos ','tipo'=>'','descripcion_tipo'=>'','totalizado'=>'true','label_totalizado'=>'Total otro resultado integral que no se reclasificará al resultado del periodo, neto de impuestos','codigo_seccion_padre'=>3);

// FILAS DE LA SECCION OTRO RESULTADO INTEGRAL
$array_formatos_secciones_filas['410000'][4][] = array('codigo'=> 1, 'orden'=>1, 'nombre'=>'Otro resultado integral, neto de impuestos, ganancias (pérdidas) de inversiones en instrumentos de patrimonio','naturaleza'=>'credito' );
$array_formatos_secciones_filas['410000'][4][] = array('codigo'=> 2, 'orden'=>2, 'nombre'=>'Otro resultado integral, neto de impuestos, ganancias (pérdidas) por revaluación','naturaleza' =>'credito' );
$array_formatos_secciones_filas['410000'][4][] = array('codigo'=> 3, 'orden'=>3, 'nombre'=>'Otro resultado integral, neto de impuestos, ganancias (pérdidas) por nuevas mediciones de planes de beneficios definidos','naturaleza'=>'credito' );
$array_formatos_secciones_filas['410000'][4][] = array('codigo'=> 4, 'orden'=>4, 'nombre'=>'Participación de otro resultado integral de asociadas y negocios conjuntos contabilizados utilizando el método de la participación que no se reclasificará al resultado del periodo, neto de impuestos ','naturaleza'=>'credito' );

$array_formatos_secciones['410000'][5]= array('orden'=>1,'nombre'=>'Componentes de otro resultado integral que se reclasificarán al resultado del periodo, neto de impuestos ','tipo'=>'resumen','descripcion_tipo'=>'resumen','totalizado'=>'true','label_totalizado'=>'Otro resultado integral que se reclasificará al resultado del periodo, neto de impuestos','codigo_seccion_padre'=>3);
$array_formatos_secciones['410000'][6]= array('orden'=>1,'nombre'=>'Coberturas del flujo de efectivo','tipo'=>'sinopsis','descripcion_tipo'=>'sinopsis','totalizado'=>'true','label_totalizado'=>'Otro resultado integral, neto de impuestos, coberturas del flujo de efectivo','codigo_seccion_padre'=>5);

// FILAS DE LA SECCION COBERTURAS DEL FLUJO DE EFECTIVO
$array_formatos_secciones_filas['410000'][6][] = array('codigo'=> 5, 'orden'=>1, 'nombre'=>'Ganancias (pérdidas) por coberturas de flujos de efectivo, neto de impuestos','naturaleza'=>'credito' );
$array_formatos_secciones_filas['410000'][6][] = array('codigo'=> 6, 'orden'=>2, 'nombre'=>'Ajustes de reclasificación en coberturas de flujos de efectivo, neto de impuestos','naturaleza'=>'deudor' );
$array_formatos_secciones_filas['410000'][6][] = array('codigo'=> 7, 'orden'=>3, 'nombre'=>'Importes eliminados del patrimonio e incluidos en el importe en libros de activos (pasivos) no financieros que se hayan adquirido o incurrido mediante una transacción prevista altamente probable cubierta, neto de impuestos','naturaleza'=>'deudor' );

$array_formatos_secciones['410000'][7]= array('orden'=>2,'nombre'=>'Activos financieros medidos al valor razonable con cambios en otro resultado integral ','tipo'=>'resumen','descripcion_tipo'=>'resumen','totalizado'=>'true','label_totalizado'=>'Otro resultado integral, neto de Impuestos, activos financieros medidos al valor razonable con cambios en otro resultado integral','codigo_seccion_padre'=>5);
// FILAS DE LA SECCION ACTIVOS FINANCIEROS MEDIDOS AL VALOR RAZONABLE CON CAMBIOS EN OTRO RESULTADO INTEGRAL
$array_formatos_secciones_filas['410000'][7][] = array('codigo'=> 8, 'orden'=>1, 'nombre'=>'Ganancias (pérdidas) por activos financieros medidos al valor razonable con cambios en otro resultado integral, neto de impuestos','naturaleza'=>'credito' );
$array_formatos_secciones_filas['410000'][7][] = array('codigo'=> 9, 'orden'=>2, 'nombre'=>'Ajustes de reclasificación sobre activos financieros medidos al valor razonable con cambios en otro resultado integral, netos de impuestos','naturaleza'=>'deudor' );
$array_formatos_secciones_filas['410000'][7][] = array('codigo'=> 10,'orden'=>3, 'nombre'=>'Importes eliminados del patrimonio y ajustados contra el valor razonable de activos financieros en el momento de la reclasificación fuera de la categoría de medición de valor razonable con cambios en otro resultado integral, neto de impuestos','naturaleza'=>'deudor' );

$array_formatos_secciones['410000'][8]= array('orden'=>4,'nombre'=>'Participación de otro resultado integral de asociadas y negocios conjuntos contabilizados utilizando el método de la participación que se reclasificará al resultado del periodo, neto de impuestos','tipo'=>'resumen','descripcion_tipo'=>'resumen','totalizado'=>'','label_totalizado'=>'','codigo_seccion_padre'=>7);

//=========================================================================================//
// ARRAY CON LAS ESTRUCTURAS DE CADA INFORME //
//
// POSICIONES DEL ARRAY
// array_formatos['CODIGO DEL FORMATO']
// array_formatos_secciones['CODIGO DEL FORMATO']['CODIGO DE LA SECCION']
// array_formatos_secciones_filas['CODIGO DEL FORMATO']['CODIGO DE LA SECCION A LA QUE PERTENECE']
//
//==========================================================================================//

$array_formatos_secciones['520000'][1] = array('orden'=>1,'nombre'=>'Estado de flujos de efectivo','tipo'=>'sinopsis','descripcion_tipo'=>'sinopsis','totalizado'=>'false','formula_totalizado'=>'','codigo_seccion_padre'=>0);
// INFORMACION LLENADA POR EL USUARIO
// $array_formatos_secciones['520000'][2] = array('orden'=>1,'nombre'=>'Resultado de periodo','tipo'=>'resumen','descripcion_tipo'=>'resumen','totalizado'=>'false','formula_totalizado'=>'','codigo_seccion_padre'=>1);
// $array_formatos_secciones['520000'][3] = array('orden'=>2,'nombre'=>'Clase de Separación de la Información Financiera ','tipo'=>'tabla','descripcion_tipo'=>'tabla','totalizado'=>'false','formula_totalizado'=>'','codigo_seccion_padre'=>1);
// $array_formatos_secciones['520000'][4] = array('orden'=>1,'nombre'=>'Clase de Separación','tipo'=>'eje','descripcion_tipo'=>'eje de la tabla','totalizado'=>'false','formula_totalizado'=>'','codigo_seccion_padre'=>3);
// $array_formatos_secciones['520000'][5] = array('orden'=>1,'nombre'=>'Total por Clase de Separación','tipo'=>'miembro','descripcion_tipo'=>'columna de la tabla','totalizado'=>'false','formula_totalizado'=>'','codigo_seccion_padre'=>4);
// $array_formatos_secciones['520000'][6] = array('orden'=>1,'nombre'=>'Público','tipo'=>'miembro','descripcion_tipo'=>'columna de la tabla','totalizado'=>'false','formula_totalizado'=>'','codigo_seccion_padre'=>5);
// $array_formatos_secciones['520000'][7] = array('orden'=>2,'nombre'=>'Privado','tipo'=>'miembro','descripcion_tipo'=>'columna de la tabla','totalizado'=>'false','formula_totalizado'=>'','codigo_seccion_padre'=>5);

$array_formatos_secciones['520000'][2] = array('orden'=>3,'nombre'=>'Flujos de efectivo procedentes de (utilizados en) actividades de operación','tipo'=>'sinopsis','descripcion_tipo'=>'sinopsis','totalizado'=>'true','label_totalizado'=>'Flujos de efectivo netos procedentes de (utilizados en) actividades de operación ', 'formula_totalizado'=>'','codigo_seccion_padre'=>1);
$array_formatos_secciones['520000'][3] = array('orden'=>1,'nombre'=>'Ganancia (pérdida)','tipo'=>'','descripcion_tipo'=>'','totalizado'=>'false','formula_totalizado'=>'','codigo_seccion_padre'=>2);
$array_formatos_secciones['520000'][4] = array('orden'=>2,'nombre'=>'Ajustes para conciliar la ganancia (pérdida)','tipo'=>'sinopsis','descripcion_tipo'=>'sinopsis','totalizado'=>'false','formula_totalizado'=>'','codigo_seccion_padre'=>2);

// FILAS DE LA SECCION AJUSTE PARA CONCILIAR LA GANACIA (PÉRDIDA)
$array_formatos_secciones_filas['520000'][4][] = array('codigo'=> 1, 'orden'=>1, 'nombre'=>'Ajustes por gasto por impuestos a las ganancias ','naturaleza'=>'debito' );
$array_formatos_secciones_filas['520000'][4][] = array('codigo'=> 2, 'orden'=>2, 'nombre'=>'Ajustes por costos financieros','naturaleza'=>'debito' );
$array_formatos_secciones_filas['520000'][4][] = array('codigo'=> 3, 'orden'=>3, 'nombre'=>'Ajustes por disminuciones (incrementos) en los inventarios','naturaleza'=>'debito' );
$array_formatos_secciones_filas['520000'][4][] = array('codigo'=> 4, 'orden'=>4, 'nombre'=>'Ajustes por la disminución (incremento) de cuentas por cobrar de origen comercial','naturaleza'=>'debito' );
$array_formatos_secciones_filas['520000'][4][] = array('codigo'=> 5, 'orden'=>5, 'nombre'=>'Ajustes por disminuciones (incrementos) en otras cuentas por cobrar derivadas de las actividades de operación','naturaleza'=>'debito' );
$array_formatos_secciones_filas['520000'][4][] = array('codigo'=> 6, 'orden'=>6, 'nombre'=>'Ajustes por el incremento (disminución) de cuentas por pagar de origen comercial','naturaleza'=>'debito' );
$array_formatos_secciones_filas['520000'][4][] = array('codigo'=> 7, 'orden'=>7, 'nombre'=>'Ajustes por incrementos (disminuciones) en otras cuentas por pagar derivadas de las actividades de operación','naturaleza'=>'debito' );
$array_formatos_secciones_filas['520000'][4][] = array('codigo'=> 8, 'orden'=>8, 'nombre'=>'Ajustes por gastos de depreciación y amortización ','naturaleza'=>'debito' );
$array_formatos_secciones_filas['520000'][4][] = array('codigo'=> 9, 'orden'=>9, 'nombre'=>'Ajustes por deterioro de valor (reversiones de pérdidas por deterioro de valor) reconocidas en el resultado del periodo','naturaleza'=>'debito' );
$array_formatos_secciones_filas['520000'][4][] = array('codigo'=> 10,'orden'=>10,'nombre'=>'Ajustes por provisiones ','naturaleza'=>'debito' );
$array_formatos_secciones_filas['520000'][4][] = array('codigo'=> 11,'orden'=>11,'nombre'=>'Ajustes por pérdidas (ganancias) de moneda extranjera no realizadas','naturaleza'=>'debito' );
$array_formatos_secciones_filas['520000'][4][] = array('codigo'=> 12,'orden'=>12,'nombre'=>'Ajustes por pagos basados en acciones','naturaleza'=>'debito' );
$array_formatos_secciones_filas['520000'][4][] = array('codigo'=> 13,'orden'=>13,'nombre'=>'Ajustes por pérdidas (ganancias) del valor razonable','naturaleza'=>'debito' );
$array_formatos_secciones_filas['520000'][4][] = array('codigo'=> 14,'orden'=>14,'nombre'=>'Ajustes por ganancias no distribuidas de asociadas','naturaleza'=>'credito' );
$array_formatos_secciones_filas['520000'][4][] = array('codigo'=> 15,'orden'=>15,'nombre'=>'Otros ajustes por partidas distintas al efectivo','naturaleza'=>'debito' );
$array_formatos_secciones_filas['520000'][4][] = array('codigo'=> 16,'orden'=>16,'nombre'=>'Ajustes por pérdidas (ganancias) por la disposición de activos no corrientes','naturaleza'=>'debito' );
$array_formatos_secciones_filas['520000'][4][] = array('codigo'=> 17,'orden'=>17,'nombre'=>'Otros ajustes para los que los efectos sobre el efectivo son flujos de efectivo de inversión o financiación ','naturaleza'=>'debito' );
$array_formatos_secciones_filas['520000'][4][] = array('codigo'=> 18,'orden'=>18,'nombre'=>'Otros ajustes para conciliar la ganancia (pérdida) ','naturaleza'=>'debito' );

// FILAS DE LA SECCION FLUJOS DE EFECTIVO PROCEDENTES DE (UTILIZADOS EN) ACTIVIDADES DE OPERACIÓNA)
$array_formatos_secciones_filas['520000'][4][] = array('codigo'=> 20, 'orden'=>3, 'nombre'=>'Flujos de efectivo procedentes (utilizados en) operaciones','naturaleza'=>'debito' );
$array_formatos_secciones_filas['520000'][4][] = array('codigo'=> 21, 'orden'=>4, 'nombre'=>'Dividendos pagados, clasificados como actividades de operación','naturaleza'=>'debito' );
$array_formatos_secciones_filas['520000'][4][] = array('codigo'=> 22, 'orden'=>5, 'nombre'=>'Dividendos recibidos, clasificados como actividades de operación','naturaleza'=>'debito' );
$array_formatos_secciones_filas['520000'][4][] = array('codigo'=> 23, 'orden'=>6, 'nombre'=>'Intereses pagados, clasificados como actividades de operación','naturaleza'=>'debito' );
$array_formatos_secciones_filas['520000'][4][] = array('codigo'=> 24, 'orden'=>7, 'nombre'=>'Intereses recibidos, clasificados como actividades de operación','naturaleza'=>'debito' );
$array_formatos_secciones_filas['520000'][4][] = array('codigo'=> 25, 'orden'=>8, 'nombre'=>'Impuestos a las ganancias pagados (reembolsados), clasificados como actividades de operación','naturaleza'=>'debito' );
$array_formatos_secciones_filas['520000'][4][] = array('codigo'=> 26, 'orden'=>9, 'nombre'=>'Otras entradas (salidas) de efectivo, clasificados como actividades de operación','naturaleza'=>'debito' );
$array_formatos_secciones_filas['520000'][4][] = array('codigo'=> 27, 'orden'=>10,'nombre'=>'Flujos de efectivo netos procedentes de (utilizados en) actividades de operación','naturaleza'=>'debito' );

$array_formatos_secciones['520000'][5] = array('orden'=>3,'nombre'=>'Flujos de efectivo procedentes de (utilizados en) actividades de inversión','tipo'=>'sinopsis','descripcion_tipo'=>'sinopsis','totalizado'=>'true','formula_totalizado'=>'','label_totalizado'=>'Flujos de efectivo netos procedentes de (utilizados en) actividades de inversión','codigo_seccion_padre'=>2);
// FILAS DE LA SECCION FLUJOS DE EFECTIVO PROCEDENTES DE (UTILIZADOS EN) ACTIVIDADES DE INVERSIÓN
$array_formatos_secciones_filas['520000'][5][] = array('codigo'=> 28, 'orden'=>1, 'nombre'=>'Flujos de efectivo procedentes de la pérdida de control de subsidiarias u otros negocios, clasificados como actividades de inversión','naturaleza'=>'credito' );
$array_formatos_secciones_filas['520000'][5][] = array('codigo'=> 29, 'orden'=>2, 'nombre'=>'Flujos de efectivo utilizados para obtener el control de subsidiarias u otros negocios, clasificados como actividades de inversión','naturaleza'=>'debito' );
$array_formatos_secciones_filas['520000'][5][] = array('codigo'=> 30, 'orden'=>3, 'nombre'=>'Otros cobros por la venta de patrimonio o instrumentos de deuda de otras entidades, clasificados como actividades de inversión','naturaleza'=>'debito' );
$array_formatos_secciones_filas['520000'][5][] = array('codigo'=> 31, 'orden'=>4, 'nombre'=>'Otros pagos para adquirir patrimonio o instrumentos de deuda de otras entidades, clasificados como actividades de inversión','naturaleza'=>'credito' );
$array_formatos_secciones_filas['520000'][5][] = array('codigo'=> 32, 'orden'=>5, 'nombre'=>'Otros cobros por la venta de participaciones en negocios conjuntos, clasificados como actividades de inversión','naturaleza'=>'debito' );
$array_formatos_secciones_filas['520000'][5][] = array('codigo'=> 33, 'orden'=>6, 'nombre'=>'Otros pagos para adquirir participaciones en negocios conjuntos, clasificados como actividades de inversión','naturaleza'=>'credito' );
$array_formatos_secciones_filas['520000'][5][] = array('codigo'=> 34, 'orden'=>7, 'nombre'=>'Importes procedentes de ventas de propiedades, planta y equipo, clasificados como actividades de inversión','naturaleza'=>'debito' );
$array_formatos_secciones_filas['520000'][5][] = array('codigo'=> 35, 'orden'=>8, 'nombre'=>'Compras de propiedades, planta y equipo, clasificados como actividades de inversión ','naturaleza'=>'credito' );
$array_formatos_secciones_filas['520000'][5][] = array('codigo'=> 36, 'orden'=>9, 'nombre'=>'Importes procedentes de ventas de activos intangibles, clasificados como actividades de inversión','naturaleza'=>'debito' );
$array_formatos_secciones_filas['520000'][5][] = array('codigo'=> 37, 'orden'=>10,'nombre'=>'Compras de activos intangibles, clasificados como actividades de inversión','naturaleza'=>'debito' );
$array_formatos_secciones_filas['520000'][5][] = array('codigo'=> 38, 'orden'=>11,'nombre'=>'Recursos por ventas de otros activos a largo plazo, clasificados como actividades de inversión','naturaleza'=>'debito' );
$array_formatos_secciones_filas['520000'][5][] = array('codigo'=> 39, 'orden'=>12,'nombre'=>'Compras de otros activos a largo plazo, clasificados como actividades de inversión','naturaleza'=>'credito' );
$array_formatos_secciones_filas['520000'][5][] = array('codigo'=> 40, 'orden'=>13,'nombre'=>'Importes procedentes de subvenciones del gobierno, clasificados como actividades de inversión','naturaleza'=>'debito' );
$array_formatos_secciones_filas['520000'][5][] = array('codigo'=> 41, 'orden'=>14,'nombre'=>'Anticipos de efectivo y préstamos concedidos a terceros, clasificados como actividades de inversión','naturaleza'=>'credito' );
$array_formatos_secciones_filas['520000'][5][] = array('codigo'=> 42, 'orden'=>15,'nombre'=>'Cobros procedentes del reembolso de anticipos y préstamos concedidos a terceros, clasificados como actividades de inversión','naturaleza'=>'debito' );
$array_formatos_secciones_filas['520000'][5][] = array('codigo'=> 43, 'orden'=>16,'nombre'=>'Pagos en efectivo por contratos de futuros, contratos a término, contratos de opciones y contratos de permuta financiera, clasificados como actividades de inversión','naturaleza'=>'credito' );
$array_formatos_secciones_filas['520000'][5][] = array('codigo'=> 44, 'orden'=>17,'nombre'=>'Cobros en efectivo por contratos de futuros, contratos a término, contratos de opciones y contratos de permuta financiera, clasificados como actividades de inversión','naturaleza'=>'debito' );
$array_formatos_secciones_filas['520000'][5][] = array('codigo'=> 45, 'orden'=>18,'nombre'=>'Dividendos recibidos, clasificados como actividades de inversión ','naturaleza'=>'debito' );
$array_formatos_secciones_filas['520000'][5][] = array('codigo'=> 46, 'orden'=>19,'nombre'=>'Intereses pagados, clasificados como actividades de inversión','naturaleza'=>'credito' );
$array_formatos_secciones_filas['520000'][5][] = array('codigo'=> 47, 'orden'=>20,'nombre'=>'Intereses recibidos, clasificados como actividades de inversión','naturaleza'=>'debito' );
$array_formatos_secciones_filas['520000'][5][] = array('codigo'=> 48, 'orden'=>21,'nombre'=>'Impuestos a las ganancias pagados (reembolsados), clasificados como actividades de inversión','naturaleza'=>'credito' );
$array_formatos_secciones_filas['520000'][5][] = array('codigo'=> 49, 'orden'=>22,'nombre'=>'Otras entradas (salidas) de efectivo, clasificados como actividades de inversión','naturaleza'=>'debito' );

$array_formatos_secciones['520000'][6] = array('orden'=>4,'nombre'=>'Flujos de efectivo procedentes de (utilizados en) actividades de financiación','tipo'=>'sinopsis','descripcion_tipo'=>'sinopsis','totalizado'=>'true','formula_totalizado'=>'','label_totalizado'=>'','codigo_seccion_padre'=>2);
// FILAS DE LA SECCION FLUJOS DE EFECTIVO PROCEDENTES DE (UTILIZADOS EN) ACTIVIDADES DE INVERSIÓN
$array_formatos_secciones_filas['520000'][6][] = array('codigo'=> 50, 'orden'=>1, 'nombre'=>'Recursos por cambios en las participaciones en la propiedad en subsidiarias que no dan lugar a la pérdida de control ','naturaleza'=>'debito' );
$array_formatos_secciones_filas['520000'][6][] = array('codigo'=> 51, 'orden'=>2, 'nombre'=>'Pagos por cambios en las participaciones en la propiedad en subsidiarias que no dan lugar a la pérdida de control ','naturaleza'=>'credito' );
$array_formatos_secciones_filas['520000'][6][] = array('codigo'=> 52, 'orden'=>3, 'nombre'=>'Importes procedentes de la emisión de acciones','naturaleza'=>'debito' );
$array_formatos_secciones_filas['520000'][6][] = array('codigo'=> 53, 'orden'=>4, 'nombre'=>'Importes procedentes de la emisión de otros instrumentos de patrimonio','naturaleza'=>'debito' );
$array_formatos_secciones_filas['520000'][6][] = array('codigo'=> 54, 'orden'=>5, 'nombre'=>'Pagos por adquirir o rescatar las acciones de la entidad','naturaleza'=>'credito' );
$array_formatos_secciones_filas['520000'][6][] = array('codigo'=> 55, 'orden'=>6, 'nombre'=>'Pagos por otras participaciones en el patrimonio','naturaleza'=>'credito' );
$array_formatos_secciones_filas['520000'][6][] = array('codigo'=> 56, 'orden'=>7, 'nombre'=>'Importes procedentes de préstamos, clasificados como actividades de financiación','naturaleza'=>'debito' );
$array_formatos_secciones_filas['520000'][6][] = array('codigo'=> 57, 'orden'=>8, 'nombre'=>'Reembolsos de préstamos, clasificados como actividades de financiación','naturaleza'=>'credito' );
$array_formatos_secciones_filas['520000'][6][] = array('codigo'=> 58, 'orden'=>9, 'nombre'=>'Pagos de pasivos por arrendamiento financiero, clasificados como actividades de financiación','naturaleza'=>'debito' );
$array_formatos_secciones_filas['520000'][6][] = array('codigo'=> 59, 'orden'=>10,'nombre'=>'Pagos de pasivos por arrendamiento, clasificados como actividades de financiación ','naturaleza'=>'credito' );
$array_formatos_secciones_filas['520000'][6][] = array('codigo'=> 60, 'orden'=>11,'nombre'=>'Importes procedentes de subvenciones del gobierno, clasificados como actividades de financiación','naturaleza'=>'credito' );
$array_formatos_secciones_filas['520000'][6][] = array('codigo'=> 61, 'orden'=>12,'nombre'=>'Dividendos pagados, clasificados como actividades de financiación','naturaleza'=>'credito' );
$array_formatos_secciones_filas['520000'][6][] = array('codigo'=> 62, 'orden'=>13,'nombre'=>'Intereses pagados, clasificados como actividades de financiación','naturaleza'=>'credito' );
$array_formatos_secciones_filas['520000'][6][] = array('codigo'=> 63, 'orden'=>14,'nombre'=>'Impuestos a las ganancias pagados (reembolsados), clasificados como actividades de financiación','naturaleza'=>'credito' );
$array_formatos_secciones_filas['520000'][6][] = array('codigo'=> 64, 'orden'=>15,'nombre'=>'Otras entradas (salidas) de efectivo, clasificados como actividades de financiación','naturaleza'=>'debito' );
$array_formatos_secciones_filas['520000'][6][] = array('codigo'=> 65, 'orden'=>16,'nombre'=>'Flujos de efectivo netos procedentes de (utilizados en) actividades de financiación','naturaleza'=>'debito' );

$array_formatos_secciones['520000'][7] = array('orden'=>5,'nombre'=>'Incremento (disminución) en el efectivo y equivalentes al efectivo, antes del efecto de los cambios en la tasa de cambio','tipo'=>'','descripcion_tipo'=>'','totalizado'=>'false','formula_totalizado'=>'','label_totalizado'=>'','codigo_seccion_padre'=>1 );

$array_formatos_secciones['520000'][8] = array('orden'=>6,'nombre'=>'Efectos de la variación en la tasa de cambio sobre el efectivo y equivalentes al efectivo','tipo'=>'sinopsis','descripcion_tipo'=>'sinopsis','totalizado'=>'false','formula_totalizado'=>'','label_totalizado'=>'','codigo_seccion_padre'=>2);
$array_formatos_secciones_filas['520000'][8][] = array('codigo'=> 67, 'orden'=>1, 'nombre'=>'Efectos de la variación en la tasa de cambio sobre el efectivo y equivalentes al efectivo','naturaleza'=>'debito' );

$array_formatos_secciones['520000'][9] = array('orden'=> 7, 'nombre'=>'Incremento (disminución) de efectivo y equivalentes al efectivo ', 'tipo'=>'','descripcion_tipo'=>'','totalizado'=>'false','formula_totalizado'=>'','label_totalizado'=>'','codigo_seccion_padre'=>1);
$array_formatos_secciones['520000'][10] = array('orden'=> 8, 'nombre'=>'Efectivo y equivalentes al efectivo al principio del periodo', 'tipo'=>'','descripcion_tipo'=>'','totalizado'=>'false','formula_totalizado'=>'','label_totalizado'=>'','codigo_seccion_padre'=>1);
$array_formatos_secciones['520000'][11] = array('orden'=> 9, 'nombre'=>'Efectivo y equivalentes al efectivo al final del periodo', 'tipo'=>'','descripcion_tipo'=>'','totalizado'=>'false','formula_totalizado'=>'','label_totalizado'=>'','codigo_seccion_padre'=>1);

?>
