<?php

//========================//
// ARRAY CON LOS FORMATOS //
//========================//
$array_formatos['1001'] = array('nombre' => 'PAGOS Y ABONOS EN CUENTA Y RETENCIONES PRACTICADAS','insert'=>'true');
$array_formatos['1003'] = array('nombre' => 'RETENCIONES EN LA FUENTE QUE LE PRACTICARON','insert'=>'true');
$array_formatos['1007'] = array('nombre' => 'INGRESOS RECIBIDOS EN EL ANIO','insert'=>'true');
$array_formatos['1008'] = array('nombre' => 'DEUDORES DE CReDITOS ACTIVOS A 31 DE DICIEMBRE','insert'=>'true');
$array_formatos['1009'] = array('nombre' => 'SALDO DE LOS PASIVOS A 31 DE DICIEMBRE','insert'=>'true');

//========================================//
// ARRAY CON LAS COLUMNAS DE LOS FORMATOS //
//========================================//
$array_formatos_columnas['1001'][] = array('orden'=> '1', 'nombre'=>'Pago o Abono en cuenta Deducible','insert'=>'true');
$array_formatos_columnas['1001'][] = array('orden'=> '2', 'nombre'=>'Pago o Abono en Cuenta no Deducible','insert'=>'true');
$array_formatos_columnas['1001'][] = array('orden'=> '3', 'nombre'=>'Iva mayor valor del costo o gasto deducible','insert'=>'true');
$array_formatos_columnas['1001'][] = array('orden'=> '4', 'nombre'=>'Iva mayor valor del costo o gasto no deducible','insert'=>'true');
$array_formatos_columnas['1001'][] = array('orden'=> '5', 'nombre'=>'Retencion en la fuente practicada en renta','insert'=>'true');
$array_formatos_columnas['1001'][] = array('orden'=> '6', 'nombre'=>'Retencion en la fuente asumida en renta','insert'=>'true');
$array_formatos_columnas['1001'][] = array('orden'=> '7', 'nombre'=>'Retencion en la fuente practicada IVA regimen comun','insert'=>'true');
$array_formatos_columnas['1001'][] = array('orden'=> '8', 'nombre'=>'Retencion en la fuente asumida  IVA regimen simplificado','insert'=>'true');
$array_formatos_columnas['1001'][] = array('orden'=> '9', 'nombre'=>'Retencion en la fuente practicada IVA no domiciliados','insert'=>'true');
$array_formatos_columnas['1001'][] = array('orden'=> '10','nombre'=>'Retencion en la fuente practicadas CREE','insert'=>'true');
$array_formatos_columnas['1001'][] = array('orden'=> '11','nombre'=>'Retencion en la fuente asumidas CREE','insert'=>'true');

$array_formatos_columnas['1003'][] = array('orden'=> '1','nombre'=>'Valor acumulado del pago o abono sujeto a retencion en la fuente','insert'=>'true');
$array_formatos_columnas['1003'][] = array('orden'=> '2','nombre'=>'Retencion en la fuente que le practicaron','insert'=>'true');

$array_formatos_columnas['1007'][] = array('orden'=> '1','nombre'=>'Ingresos a traves de consorcios o uniones temporales','insert'=>'true');
$array_formatos_columnas['1007'][] = array('orden'=> '2','nombre'=>'Ingresos a traves de contratos de mandato o administracion delegada','insert'=>'true');
$array_formatos_columnas['1007'][] = array('orden'=> '3','nombre'=>'Ingresos a traves de exploracion y explotacion de minerales','insert'=>'true');
$array_formatos_columnas['1007'][] = array('orden'=> '4','nombre'=>'Ingresos a traves de fiducias','insert'=>'true');
$array_formatos_columnas['1007'][] = array('orden'=> '5','nombre'=>'Ingresos recibidos a traves de terceros','insert'=>'true');
$array_formatos_columnas['1007'][] = array('orden'=> '6','nombre'=>'Devoluciones, rebajas y descuentos','insert'=>'true');

$array_formatos_columnas['1008'][] = array('orden'=> '1','nombre'=>'Saldo cuentas por cobrar al 31-12','insert'=>'true');

$array_formatos_columnas['1009'][] = array('orden'=> '1','nombre'=>'Saldo cuentas por pagar al 31-12','insert'=>'true');

//=========================================//
// ARRAY CON LOS CONCEPTOS DE LOS FORMATOS //
//=========================================//
$array_formatos_conceptos['1001'][] = array('concepto'=> '5001','descripcion'=>'Salarios, prestaciones sociales y demas pagos laborales: El valor acumulado efectivamente pagado al trabajador','insert'=>'true');
$array_formatos_conceptos['1001'][] = array('concepto'=> '5055','descripcion'=>'Viaticos: El valor acumulado efectivamente pagado que no constituye ingreso para el trabajador','insert'=>'true');
$array_formatos_conceptos['1001'][] = array('concepto'=> '5056','descripcion'=>'Gastos de representacion: El valor acumulado efectivamente pagado que no constituye ingreso para el trabajador','insert'=>'true');
$array_formatos_conceptos['1001'][] = array('concepto'=> '5022','descripcion'=>'Pensiones: El valor acumulado efectivamente pagado','insert'=>'true');
$array_formatos_conceptos['1001'][] = array('concepto'=> '5002','descripcion'=>'Honorarios: El valor acumulado pagado o abonado en cuenta','insert'=>'true');
$array_formatos_conceptos['1001'][] = array('concepto'=> '5003','descripcion'=>'Comisiones: El valor acumulado pagado o abonado en cuenta','insert'=>'true');
$array_formatos_conceptos['1001'][] = array('concepto'=> '5004','descripcion'=>'Servicios: El valor acumulado pagado o abonado en cuenta','insert'=>'true');
$array_formatos_conceptos['1001'][] = array('concepto'=> '5005','descripcion'=>'Arrendamientos: El valor acumulado pagado o abonado en cuenta','insert'=>'true');
$array_formatos_conceptos['1001'][] = array('concepto'=> '5006','descripcion'=>'Intereses y rendimientos financieros: El valor acumulado pagado o abonado en cuenta','insert'=>'true');
$array_formatos_conceptos['1001'][] = array('concepto'=> '5007','descripcion'=>'Compra de activos movibles: El valor acumulado pagado o abonado en cuenta','insert'=>'true');
$array_formatos_conceptos['1001'][] = array('concepto'=> '5008','descripcion'=>'Compra de activos fijos: El valor acumulado pagado o abonado en cuenta','insert'=>'true');
$array_formatos_conceptos['1001'][] = array('concepto'=> '5010','descripcion'=>'Los pagos efectuados en el año gravable 2016 por concepto de aportes parafiscales al SENA, a las Cajas de Compensacion Familiar y al Instituto Colombiano de Bienestar Familiar','insert'=>'true');
$array_formatos_conceptos['1001'][] = array('concepto'=> '5011','descripcion'=>'Los pagos efectuados en el año gravable 2016 por concepto de aportes parafiscales a las empresas promotoras de salud EPS y los aportes al Sistema de Riesgos Laborales, incluidos los aportes del trabajador','insert'=>'true');

$array_formatos_conceptos['1001'][] = array('concepto'=> '5012','descripcion'=>'Los pagos efectuados en el año gravable 2016 por concepto de aportes obligatorios para pensiones efectuados al ISS y a los Fondos de Pensiones, incluidos los aportes del trabajador','insert'=>'true');
$array_formatos_conceptos['1001'][] = array('concepto'=> '5013','descripcion'=>'Las donaciones en dinero efectuadas , a las entidades señaladas en los articulos 125, 125- 4, 126-2 y 158-1 del Estatuto Tributario y la establecida en el articulo 16 de la Ley 814 de 2003, y demas que determine la ley','insert'=>'true');
$array_formatos_conceptos['1001'][] = array('concepto'=> '5014','descripcion'=>'Las donaciones en activos diferentes a dinero efectuadas a las entidades señaladas en los articulos 125, 125-4, 126-2 y 158-1 del Estatuto Tributario y la establecida en el articulo 16 de la Ley 814 de 2003, y demas que determine la ley','insert'=>'true');
$array_formatos_conceptos['1001'][] = array('concepto'=> '5015','descripcion'=>'El valor de los impuestos efectivamente pagados solicitados como deduccion','insert'=>'true');
$array_formatos_conceptos['1001'][] = array('concepto'=> '5058','descripcion'=>'El valor de los aportes, tasas y contribuciones efectivamente pagados , solicitados como deduccion','insert'=>'true');
$array_formatos_conceptos['1001'][] = array('concepto'=> '5060','descripcion'=>'Redencion de inversiones en lo que corresponde al rembolso del capital por titulos de capitalizacion','insert'=>'true');
$array_formatos_conceptos['1001'][] = array('concepto'=> '5016','descripcion'=>'Los demas costos y deducciones','insert'=>'true');
$array_formatos_conceptos['1001'][] = array('concepto'=> '5020','descripcion'=>'Compra de activos fijos sobre los cuales solicito deduccion segun el paragrafo, del articulo 158-3 del Estatuto Tributario: El valor acumulado pagado o abonado en cuenta, en el concepto 5020. Este valor no debe incluirse en el concepto 5008','insert'=>'true');
$array_formatos_conceptos['1001'][] = array('concepto'=> '5027','descripcion'=>'El valor acumulado de los pagos o abonos en cuenta al exterior por servicios técnicos, en el concepto 5027. Este valor no debe incluirse en el concepto 5004','insert'=>'true');
$array_formatos_conceptos['1001'][] = array('concepto'=> '5023','descripcion'=>'El valor acumulado de los pagos o abonos en cuenta al exterior por asistencia técnica','insert'=>'true');
$array_formatos_conceptos['1001'][] = array('concepto'=> '5024','descripcion'=>'El valor acumulado de los pagos o abonos en cuenta al exterior por marcas','insert'=>'true');
$array_formatos_conceptos['1001'][] = array('concepto'=> '5025','descripcion'=>'El valor acumulado de los pagos o abonos en cuenta al exterior por patentes','insert'=>'true');
$array_formatos_conceptos['1001'][] = array('concepto'=> '5026','descripcion'=>'El valor acumulado de los pagos o abonos en cuenta al exterior por regalias','insert'=>'true');
$array_formatos_conceptos['1001'][] = array('concepto'=> '5028','descripcion'=>'El valor acumulado de la devolucion de pagos o abonos en cuenta y retenciones correspondientes a operaciones de años anteriores debe reportarse','insert'=>'true');
$array_formatos_conceptos['1001'][] = array('concepto'=> '5029','descripcion'=>'Cargos diferidos y/o gastos pagados por anticipado por Compras: El valor acumulado pagado o abonado en cuenta se debe reportar','insert'=>'true');
$array_formatos_conceptos['1001'][] = array('concepto'=> '5030','descripcion'=>'Cargos diferidos y/o gastos pagados por anticipado por Honorarios: El valor acumulado pagado o abonado en cuenta se debe reportar','insert'=>'true');
$array_formatos_conceptos['1001'][] = array('concepto'=> '5031','descripcion'=>'Cargos diferidos y/o gastos pagados por anticipado por Comisiones: El valor acumulado pagado o abonado en cuenta se debe reportar','insert'=>'true');
$array_formatos_conceptos['1001'][] = array('concepto'=> '5032','descripcion'=>'Cargos diferidos y/o gastos pagados por anticipado por Servicios: El valor acumulado pagado o abonado en cuenta se debe reportar','insert'=>'true');


$array_formatos_conceptos['1001'][] = array('concepto'=> '5033','descripcion'=>'Cargos diferidos y/o gastos pagados por anticipado por arrendamientos: El valor acumulado pagado o abonado en cuenta se debe reportar','insert'=>'true');
$array_formatos_conceptos['1001'][] = array('concepto'=> '5034','descripcion'=>'Cargos diferidos y/o gastos pagados por anticipado por intereses y rendimientos financieros: El valor acumulado pagado o abonado en cuenta se debe reportar','insert'=>'true');
$array_formatos_conceptos['1001'][] = array('concepto'=> '5035','descripcion'=>'Cargos diferidos y/o gastos pagados por anticipado por otros conceptos: El valor acumulado pagado o abonado en cuenta se debe reportar','insert'=>'true');
$array_formatos_conceptos['1001'][] = array('concepto'=> '5019','descripcion'=>'El monto de las amortizaciones realizadas durante el año se debe reportar en el concepto 5019, excepto el valor del concepto 5057','insert'=>'true');
$array_formatos_conceptos['1001'][] = array('concepto'=> '5057','descripcion'=>'El monto de las amortizaciones realizadas durante el año relativo a los Cargos diferidos por el impuesto al patrimonio','insert'=>'true');
$array_formatos_conceptos['1001'][] = array('concepto'=> '5036','descripcion'=>'Inversiones en control y mejoramiento del medio ambiente por Compras pagadas o abonadas en cuenta','insert'=>'true');
$array_formatos_conceptos['1001'][] = array('concepto'=> '5037','descripcion'=>'Inversiones en control y mejoramiento del medio ambiente por Honorarios pagados o abonados en cuenta','insert'=>'true');
$array_formatos_conceptos['1001'][] = array('concepto'=> '5038','descripcion'=>'Inversiones en control y mejoramiento del medio ambiente por Comisiones pagadas o abonadas en cuenta','insert'=>'true');
$array_formatos_conceptos['1001'][] = array('concepto'=> '5039','descripcion'=>'Inversiones en control y mejoramiento del medio ambiente por Servicios pagados o abonados en cuenta','insert'=>'true');
$array_formatos_conceptos['1001'][] = array('concepto'=> '5040','descripcion'=>'Inversiones en control y mejoramiento del medio ambiente por Arrendamientos pagados o abonados en cuenta','insert'=>'true');
$array_formatos_conceptos['1001'][] = array('concepto'=> '5041','descripcion'=>'Inversiones en control y mejoramiento del medio ambiente por Intereses y Rendimientos financieros pagados o abonados en cuenta','insert'=>'true');
$array_formatos_conceptos['1001'][] = array('concepto'=> '5042','descripcion'=>'Inversiones en control y mejoramiento del medio ambiente por otros conceptos pagados o abonados en cuenta','insert'=>'true');
$array_formatos_conceptos['1001'][] = array('concepto'=> '5043','descripcion'=>'El valor de las participaciones o dividendos pagados o abonados en cuenta en calidad de exigibles durante el año 2016','insert'=>'true');
$array_formatos_conceptos['1001'][] = array('concepto'=> '5044','descripcion'=>'El pago por loterias, rifas, apuestas y similares','insert'=>'true');
$array_formatos_conceptos['1001'][] = array('concepto'=> '5045','descripcion'=>'Retencion sobre ingresos de tarjetas debito y credito','insert'=>'true');
$array_formatos_conceptos['1001'][] = array('concepto'=> '5046','descripcion'=>'Enajenacion de activos fijos de personas naturales ante oficinas de transito y otras entidades autorizadas','insert'=>'true');
$array_formatos_conceptos['1001'][] = array('concepto'=> '5059','descripcion'=>'El pago o abono en cuenta realizado a cada uno de los cooperados, del valor del Fondo para revalorizacion de aportes','insert'=>'true');
$array_formatos_conceptos['1001'][] = array('concepto'=> '5061','descripcion'=>'Las utilidades pagadas o abonadas en cuenta, cuando el beneficiario es diferente al fideicomitente, se informara en el FORMATO 1014 en el concepto 5061','insert'=>'true');
$array_formatos_conceptos['1001'][] = array('concepto'=> '5063','descripcion'=>'Intereses y rendimientos financieros pagados: El valor pagado','insert'=>'true');
$array_formatos_conceptos['1001'][] = array('concepto'=> '5049','descripcion'=>'Autorretenciones por ventas','insert'=>'true');
$array_formatos_conceptos['1001'][] = array('concepto'=> '5050','descripcion'=>'Autorretenciones por servicios','insert'=>'true');
$array_formatos_conceptos['1001'][] = array('concepto'=> '5051','descripcion'=>'Autorretenciones por rendimientos financieros','insert'=>'true');
$array_formatos_conceptos['1001'][] = array('concepto'=> '5052','descripcion'=>'Otras Autorretenciones','insert'=>'true');
$array_formatos_conceptos['1001'][] = array('concepto'=> '5062','descripcion'=>'Autorretenciones por CREE','insert'=>'true');
$array_formatos_conceptos['1001'][] = array('concepto'=> '5053','descripcion'=>'Retenciones practicadas a titulo de timbre','insert'=>'true');
$array_formatos_conceptos['1001'][] = array('concepto'=> '5054','descripcion'=>'La devolucion de retenciones a titulo de impuesto de timbre, correspondientes a operaciones de años anteriores','insert'=>'true');
$array_formatos_conceptos['1001'][] = array('concepto'=> '5018','descripcion'=>'El importe de las primas de reaseguros pagados o abonados en cuenta','insert'=>'true');
$array_formatos_conceptos['1001'][] = array('concepto'=> '5047','descripcion'=>'El importe de los siniestros por lucro cesante pagados o abonados en cuenta','insert'=>'true');
$array_formatos_conceptos['1001'][] = array('concepto'=> '5048','descripcion'=>'El importe de los siniestros por daño emergente pagados o abonados en cuenta','insert'=>'true');
$array_formatos_conceptos['1001'][] = array('concepto'=> '5064','descripcion'=>'Devoluciones de saldos de aportes pensionales pagados','insert'=>'true');
$array_formatos_conceptos['1001'][] = array('concepto'=> '5065','descripcion'=>'Excedentes pensionales de libre disponibilidad componente de capital pagados','insert'=>'true');


$array_formatos_conceptos['1003'][] = array('concepto'=> '1301','descripcion'=>'Retencion por salarios prestaciones y demas pagos laborales','insert'=>'true');
$array_formatos_conceptos['1003'][] = array('concepto'=> '1302','descripcion'=>'Retencion por ventas','insert'=>'true');
$array_formatos_conceptos['1003'][] = array('concepto'=> '1303','descripcion'=>'Retencion por servicios','insert'=>'true');
$array_formatos_conceptos['1003'][] = array('concepto'=> '1304','descripcion'=>'Retencion por honorarios','insert'=>'true');
$array_formatos_conceptos['1003'][] = array('concepto'=> '1305','descripcion'=>'Retencion por comisiones','insert'=>'true');
$array_formatos_conceptos['1003'][] = array('concepto'=> '1306','descripcion'=>'Retencion por intereses y rendimientos financieros','insert'=>'true');
$array_formatos_conceptos['1003'][] = array('concepto'=> '1307','descripcion'=>'Retencion por arrendamientos','insert'=>'true');
$array_formatos_conceptos['1003'][] = array('concepto'=> '1308','descripcion'=>'Retencion por otros conceptos','insert'=>'true');
$array_formatos_conceptos['1003'][] = array('concepto'=> '1309','descripcion'=>'Retencion en la fuente en el impuesto sobre las ventas','insert'=>'true');
$array_formatos_conceptos['1003'][] = array('concepto'=> '1310','descripcion'=>'Retencion por dividendos y participaciones','insert'=>'true');
$array_formatos_conceptos['1003'][] = array('concepto'=> '1311','descripcion'=>'Retencion por enajenacion de activos fijos de personas naturales ante oficinas de transito y otras entidades autorizadas','insert'=>'true');
$array_formatos_conceptos['1003'][] = array('concepto'=> '1312','descripcion'=>'Retencion por ingresos de tarjetas debito y credito','insert'=>'true');
$array_formatos_conceptos['1003'][] = array('concepto'=> '1313','descripcion'=>'Retencion por loterias, rifas, apuestas y similares','insert'=>'true');
$array_formatos_conceptos['1003'][] = array('concepto'=> '1314','descripcion'=>'Retencion por impuesto de timbre','insert'=>'true');

$array_formatos_conceptos['1007'][] = array('concepto'=> '4001','descripcion'=>'Ingresos brutos operacionales','insert'=>'true');
$array_formatos_conceptos['1007'][] = array('concepto'=> '4002','descripcion'=>'Ingresos no operacionales','insert'=>'true');
$array_formatos_conceptos['1007'][] = array('concepto'=> '4003','descripcion'=>'Ingresos por intereses y rendimientos financieros','insert'=>'true');
$array_formatos_conceptos['1007'][] = array('concepto'=> '4004','descripcion'=>'Ingresos por intereses correspondientes a creditos hipotecarios','insert'=>'true');

$array_formatos_conceptos['1008'][] = array('concepto'=> '1315','descripcion'=>'El valor total del saldo de las cuentas por cobrar a clientes','insert'=>'true');
$array_formatos_conceptos['1008'][] = array('concepto'=> '1316','descripcion'=>'El valor total del saldo de las cuentas por cobrar a accionistas, socios, comuneros, cooperados y compañias vinculadas','insert'=>'true');
$array_formatos_conceptos['1008'][] = array('concepto'=> '1317','descripcion'=>'El valor total de otras cuentas por cobrar','insert'=>'true');
$array_formatos_conceptos['1008'][] = array('concepto'=> '1318','descripcion'=>'El valor total del saldo fiscal de la provision de cartera, en el concepto 1318, identificandolo con el NIT del deudor','insert'=>'true');

$array_formatos_conceptos['1009'][] = array('concepto'=> '2201','descripcion'=>'El valor del saldo de los pasivos con proveedores','insert'=>'true');
$array_formatos_conceptos['1009'][] = array('concepto'=> '2202','descripcion'=>'El valor del saldo de los pasivos con compañias vinculadas accionistas y socios','insert'=>'true');
$array_formatos_conceptos['1009'][] = array('concepto'=> '2203','descripcion'=>'El valor del saldo de las obligaciones financieras','insert'=>'true');
$array_formatos_conceptos['1009'][] = array('concepto'=> '2204','descripcion'=>'El valor del saldo de los pasivos por impuestos, gravamenes y tasas','insert'=>'true');
$array_formatos_conceptos['1009'][] = array('concepto'=> '2205','descripcion'=>'El valor del saldo de los pasivos laborales','insert'=>'true');
$array_formatos_conceptos['1009'][] = array('concepto'=> '2207','descripcion'=>'El valor del saldo del pasivo determinado por el calculo actuarial, en el concepto 2207, con el NIT del informante','insert'=>'true');
$array_formatos_conceptos['1009'][] = array('concepto'=> '2209','descripcion'=>'El valor de los pasivos exclusivos de las compañias de seguros','insert'=>'true');
$array_formatos_conceptos['1009'][] = array('concepto'=> '2208','descripcion'=>'El valor de los pasivos respaldados en documento de fecha cierta','insert'=>'true');
$array_formatos_conceptos['1009'][] = array('concepto'=> '2206','descripcion'=>'El valor del saldo de los demas pasivos','insert'=>'true');

?>

