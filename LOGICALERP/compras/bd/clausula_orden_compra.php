<?php
// error_reporting(0);
// $nombre_empresa  = $_SESSION['NOMBREEMPRESA'];
// $nombreSucursal = $_SESSION['NOMBRESUCURSAL'];

$nombreSucursal = $sucursal;
$nombre_corto = "<b>PLATAFORMA</b>";

if($id_empresa == 1 ||  $id_empresa == 47){
	$nombreSucursal = 'Bogotá, D.C.';
}


	$textoClausula = "<div style='font-family:Arial;font-size:11px;'>
						<div style='text-align:center;font-size:12px;font-weight:bold;'>
							CONDICIONES GENERALES PARA LAS ÓRDENES DE COMPRA EXPEDIDAS POR $nombre_empresa.
						</div>
						<br><br>
							Las Condiciones Generales para las órdenes de compra expedidas por $nombre_empresa en adelante $nombre_corto, consignadas en el presente documento, regirán y serán de obligatorio cumplimiento para todos los proveedores de bienes y servicios, destinatarios de la orden de compra de $nombre_corto.
							Las cláusulas de estas condiciones generales para las órdenes de compra se entenderán incorporadas a las órdenes de compra respectivas expedidas por $nombre_corto; por lo cual, el proveedor con la ﬁrma de aceptación de la orden de compra o con el simple hecho de suministrar cualquier clase de bien o servicio, acepta simultáneamente las disposiciones contenidas en este documento.
								<br><br>
							<b>PRIMERA.- OBJETO:</b> EL PROVEEDOR se compromete a entregar los elementos señalados en la orden de compra, de acuerdo con las referencias, descripciones, cantidades y valores indicados en la misma.
								<br><br>
							<b>PARÁGRAFO:</b> El objeto de la orden de compra, será ejecutado de conformidad con la cotización presentada por EL PROVEEDOR.
								<br><br>
							<b>SEGUNDA.- VALOR:</b> El valor unitario y total, será el señalado en la orden de compra respectiva.
								<br><br>
							<b>PARÁGRAFO:</b> Del valor resultante a pagar a EL PROVEEDOR, se descontarán las sumas pertinentes por concepto de retenciones e impuestos a cargo del mismo.
								<br><br>
							<b>TERCERA.- FORMA DE PAGO:</b> El valor de la orden de compra será pagado de acuerdo con lo señalado en la misma.
								<br><br>
							<b>CUARTA.- PLAZO DE EJECUCION:</b> El plazo que tendrá EL PROVEEDOR para entregar el objeto de la orden de compra será el comprendido entre la fecha de expedición y la fecha de entrega señalada en la misma.
								<br><br>
							<b>QUINTA.- OBLIGACIONES ESPECIALES DEL PROVEEDOR:</b> Para el cumplimiento del objeto de la orden de compra, y sin perjuicio de las demás obligaciones inherentes a la ejecución de la misma, EL PROVEEDOR se obliga en forma especial a:
								<br><br>
								<div style='margin-left:15px;'>
									1.&nbsp;&nbsp;Cancelar oportunamente, y de acuerdo con la ley, sus obligaciones tanto con  sus  proveedores como con los empleados bajo su orden;<br>
									2.&nbsp;&nbsp;Mantener aﬁliados a sus empleados al sistema de seguridad social, es decir, salud, pensión y riesgos laborales. Igualmente, se obliga a pagar a sus trabajadores todos los salarios, vacaciones, primas y demás prestaciones sociales, estipuladas en la ley;<br>
									3.&nbsp;&nbsp;Entregar toda la documentación necesaria solicitada por $nombre_corto, para habilitarlo como proveedor en su registro de proponentes;<br>
									4.&nbsp;&nbsp;Garantizar que sus empleados guardarán un excelente comportamiento en las instalaciones de $nombre_corto, o con sus clientes, presentando buena conducta, respetando las normas y reglamento de la contratante, además de tener una excelente presentación personal.<br>
									5.&nbsp;&nbsp;Cumplir con las normas de seguridad necesarias para el efectivo cumplimiento de la labor contratada.<br>
									6.&nbsp;&nbsp;El personal de EL PROVEEDOR solo podrá permanecer en los horarios autorizados y áreas asignadas por $nombre_corto;<br>
									7.&nbsp;&nbsp;Emplear materiales e insumos de primera calidad que se encuentren en excelente presentación; que el personal sea idóneo para que permitan dar un adecuado cumplimiento al objeto de la orden de compra;<br>
									8.&nbsp;&nbsp;Mantener en perfecto estado de aseo y orden el lugar asignado para el cumplimiento del objeto de la orden de compra;<br>
									9.&nbsp;&nbsp;En general, todas aquellas de la naturaleza del objeto de la orden de compra.<br>
								</div>
								<br><br>
							<b>SEXTA.- OBLIGACIONES ESPECIALES DE $nombre_empresa: $nombre_corto</b>, se compromete a lo siguiente
								<br><br>
									<div style='margin-left:15px;'>
										1.&nbsp;&nbsp;Pagar a EL PROVEEDOR el valor de la orden de compra, en  los términos, forma y oportunidad establecidos en este documento.<br>
										2.&nbsp;&nbsp;En general, brindar la colaboración a EL PROVEEDOR para el cumplimiento satisfactorio del objeto de la orden de compra.<br>
									</div>
								<br><br>
							</div>";

	$texto2    = 	"<div style='font-family:Arial;font-size:11px;'>
							<b>SEPTIMA.- GARANTIA:</b> Con la ﬁrma de aceptación del PROVEEDOR de la orden de compra o el suministro de cualquier clase de bien o servicio, se compromete a otorgar una garantía mínima de un (1) año, sobre el objeto de la orden de compra o el término señalado en su oferta, en caso que este fuere superior. En caso que la garantía del producto fuera inferior al término exigido en esta cláusula, EL PROVEEDOR deberá informarlo por escrito, y solo operará esta garantía inferior, cuando $nombre_empresa DE COMUNICACIONES S.A.S., lo acepte expresamente.
								<br><br>
							<b>OCTAVA.- PROVEEDOR INDEPENDIENTE:</b> EL PROVEEDOR y sus subalternos o dependientes, no estarán laboralmente subordinados a $nombre_corto, ni serán intermediarios suyos y tendrán plena autonomía técnica administrativa y directiva. De acuerdo con lo estipulado en este documento, EL PROVEEDOR no será agente, ni representante o mandatario $nombre_corto., ni la obligará ante terceros, ni existirá solidaridad alguna por ningún motivo entre EL PROVEEDOR y $nombre_corto, ya que la primera desplegará la totalidad de la actividad que demande el debido cumplimiento del objeto de esta orden de compra, en forma autónoma e independiente y para ello utilizará el personal y medios que requiera, asumiendo todos los riesgos que impliquen las actividades necesarias para el cumplimiento de este contrato. $nombre_corto. en ningún momento será responsable por las obligaciones laborales de EL PROVEEDOR con su personal, y en el evento que $nombre_corto haya de responder por este motivo, EL PROVEEDOR lo indemnizará plenamente, dado el carácter totalmente autónomo con que EL PROVEEDOR asume las obligaciones en razón de esta orden de compra. EL PROVEEDOR asumirá igualmente cualquier tipo de riesgo que la actividad requerida para el cumplimiento de este documento le genere a este como persona jurídica o a cualquiera de sus empleados, socios, asesores etc.
								<br><br>
							<b>NOVENA.- RESPONSABILIDAD POR DAÑOS:</b> EL PROVEEDOR será responsable por los danos y perjuicios que se generen por actos u omisiones suyas o del personal a su cargo, durante el desarrollo de las labores necesarias para el cabal cumplimiento del objeto de la orden de compra. Por lo tanto, indemnizará plenamente a $nombre_corto. por cualquier daño o perjuicio ocasionado a esta o a sus clientes.
								<br><br>
							<b>DECIMA.- CLAUSULA PENAL:</b> En caso de incumplimiento de EL PROVEEDOR, de cualquiera de las obligaciones a su cargo, pagará a $nombre_corto, a título de cláusula penal el equivalente a veinte (20%) del valor de la orden de compra, sin perjuicio que pueda exigir el cumplimiento de la obligación principal y el pago de la indemnización a que haya lugar e iniciar las acciones judiciales o civiles correspondientes.
								<br><br>
							<b>DECIMA PRIMERA.- MULTAS:</b> Sin perjuicio de la cláusula anterior, $nombre_corto podrá imponer a EL PROVEEDOR multas, por el retardo en la entrega del objeto de la orden de compra. EL PROVEEDOR pagará a $nombre_corto a título de multa, una suma equivalente al uno por ciento (1%) del valor del valor de la orden de compra, por cada quince minutos de mora en la entrega, sin exceder el veinte por ciento (20%) del valor de la misma. Queda entendido que el pago de las multas no extinguirá la obligación principal, ni el pago de los demás perjuicios que se puedan ocasionar. Para efectos de lo aquí dispuesto se autoriza a $nombre_corto para descontar el  valor de estas multas de los saldos pendientes a favor de EL PROVEEDOR, sin perjuicio de la facultad de $nombre_corto de dar por terminada la relación contractual que surge a través del presente documento, cuando la mora sobrepase el diez por ciento (10%) del valor de la orden de compra quedando a salvo el  derecho a iniciar las demás acciones para reclamar la indemnización de todos los perjuicios que dicha mora pueda haberle causado.
								<br><br>
							<b>DECIMA SEGUNDA.- AUTORIZACION DE DESCUENTOS:</b> EL PROVEEDOR autoriza a $nombre_corto a retener el valor de la cláusula penal, de las penalizaciones y/o multas e impuestos a cargo de EL PROVEEDOR, que se causen durante la ejecución del presente contrato.
								<br><br>
							<b>DECIMA TERCERA.- TERMINACION:</b> La relación contractual que surge con ocasión a la orden de compra terminará por las causales dispuestas por la ley y por las siguientes: a) Si alguna de las partes es admitida en algún procedimiento concursal; b) Incumplimiento de alguna de las obligaciones a cargo del PROVEEDOR; c) Incumplimiento de la Cláusula denominada ''Cumplimiento de las normas'', según se señala en el presente documento. Estas causales operarán sin perjuicio de la facultad de $nombre_corto para iniciar las acciones judiciales o civiles pertinentes a ﬁn de hacerse indemnizar por los perjuicios que le ocasione EL PROVEEDOR.
								<br><br>
							<b>DECIMA CUARTA.- DOMICILIO CONTRACTUAL:</b> Para todos los efectos legales se entiende como domicilio contractual la ciudad de Bogotá, D.C.
								<br><br>
							<b>DECIMA QUINTA.- TITULO EJECUTIVO:</b> En caso de aceptarse la orden de compra, las partes reconocen que este documento presta mérito ejecutivo, renunciando desde ya al requerimiento por mora.
								<br><br>
							Con la aceptación de esta orden de compra, se aceptan los términos y condiciones incluidos en ella.
					</div>";

?>
