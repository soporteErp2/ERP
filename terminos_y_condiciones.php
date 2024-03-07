<?php
	header('Content-Type: text/html; charset=utf-8');

	switch ($_POST['opc']) {
		case 'actualizaTerminosCondiciones':
			$key = base64_decode($_POST['key']);
			$arrayKey = explode(";", $key);
			actualizaTerminosCondiciones($_POST['aceptacion'],$arrayKey[0],$arrayKey[1],$arrayKey[2],$arrayKey[3],$arrayKey[4]);
			break;

		default:
			muestraTerminosCondiciones($mysql);
			break;
	}

	function muestraTerminosCondiciones($mysql){
		// CONSULTAR SI EL USUARIO ACEPTO LOS TERMINOS Y CONDICIONES
		$sql="SELECT
	 			acepta_terminos_condiciones,
				fecha_aceptacion_terminos_condiciones,
				hora_aceptacion_terminos_condiciones,
				ip_aceptacion_terminos_condiciones
			FROM empleados WHERE activo=1 AND id=$_SESSION[IDUSUARIO] ";
		$query=$mysql->query($sql,$mysql->link);

		$acepta_terminos_condiciones           = $mysql->result($query,0,'acepta_terminos_condiciones');
		$fecha_aceptacion_terminos_condiciones = $mysql->result($query,0,'fecha_aceptacion_terminos_condiciones');
		$hora_aceptacion_terminos_condiciones  = $mysql->result($query,0,'hora_aceptacion_terminos_condiciones');
		$ip_aceptacion_terminos_condiciones    = $mysql->result($query,0,'ip_aceptacion_terminos_condiciones');

		if ($acepta_terminos_condiciones=='Si') { return; }

		?>
		<head>
			<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
		</head>
			<style>
				.modalDialog {
					position           : absolute;
					width              :100%;
					height             :100%;
					font-family        : 'Raleway', sans-serif;
					top                : 0;
					right              : 0;
					bottom             : 0;
					left               : 0;
					background         : rgba(0,0,0,0.8);
					z-index            : 1000000;
					-webkit-transition : opacity 400ms ease-in;
					-moz-transition    : opacity 400ms ease-in;
					transition         : opacity 400ms ease-in;
				}

				.modalDialog > div {
					width                : 400px;
					position             : relative;
					margin               : 10% auto;
					text-align           : center;
					background           : #fff;
					-webkit-transition   : opacity 400ms ease-in;
					-moz-transition      : opacity 400ms ease-in;
					transition           : opacity 400ms ease-in;
				}

				.modalDialog > div > h2 {
					background : #0091cd;
					color      : #FFF;
					text-align : center;
					padding    : 10px;
					font-size  : 18px;
				}

				.modalDialog > div > h2 >span {
					font-size  : 12px;
				}

				.modalDialog > div > div {
					width      : 100%;
					height     : calc(100% - 76px);
					overflow-y : auto;
				}

				.modalDialog > div > p , .modalDialog > div > div > p {
					padding   : 5px 20px 13px 20px;
					font-size : 12px;
					text-align: justify;
				}

				.modalDialog > div > div > p > table{
					width           : 100%;
					font-size       : 12px;
					border-collapse : collapse;
					border          : 1px solid #000;
				}

				.modalDialog > div > div > p > table tr{
					border-bottom: 1px solid #000;
				}

				.modalDialog > div > div > p > table > thead {
					/*background : #0091cd;*/
					/*color      : #FFF;*/
					text-align : center;
					font-size: 14px;
				}

				.modalDialog > div > button {
					margin-bottom      : 10px;
					color              : #fff;
					outline            : none;
					padding            : 10px 10px;
					cursor             : pointer;
					font-size          : 12px;
					font-weight        : bold;
					border             : none;
					text-transform     : uppercase;
					transition         : 0.9s all;
					-webkit-transition : 0.9s all;
					-moz-transition    : 0.9s all;
					-o-transition      : 0.9s all;
					-ms-transition     : 0.9s all;
				}

				.modalDialog > div > button[data-value="aceptar"]{
					background-color   : #5dc799;
				}

				.modalDialog > div > button[data-value="cancelar"]{
					background-color   : #d16463;
				}

				.modalText{
					opacity    : 0;
					z-index    : 100000;
					background : none;
				}

				.modalText > div{
					width: 600px;
					height: 500px;
				}

				.modalDialog:target {
					opacity        : 1;
					pointer-events : auto;
					z-index        : 1000000;
				}

				.close {
					color       : #FFFFFF !important;
					line-height : 43px;
					position    : absolute;
					right       : 0px;
					text-align  : right;
					width       : 42px;
					text-align  : center;
					font-size   : 14px;
				}

				.close:hover { background: #68c7ee; }

			</style>

			<div  class="modalDialog" id="openModal2">
				<div>
					<h2>Terminos y Condiciones</h2>
					<p>Se han realizado cambios en las politicas de prestacion de servicios, por eso es necesario que lea atentamente los terminos y condiciones de uso
						de las aplicaciones y que acepte estos nuevos terminos </p>
					<p><a href="https://cloud.logicalsoft.co/terminos" target="_blank" style="color: #006699;">Ver terminos y Condiciones</a></p>

					<button data-value="aceptar" onclick="aceptacionTerminos('Si')">Aceptar</button>
					<button data-value="cancelar" onclick="aceptacionTerminos('No')">Rechazar</button>

				</div>
			</div>

			<div class="modalDialog modalText" id="openModal">
				<div>
					<a href="#close" title="Cerrar" class="close">X</a>
					<h2>TERMINOS Y CONDICIONES DE USO <br> LOGICALSOFT SAS <br> <span>Ver 1.0 - Aplica a partir de: 01/Agosto/2018</span> </h2>
					<div>
						<p>
							Para acceder a nuestros servicios es indispensable que usted conozca el siguiente documento donde se describe los términos de uso y los alcances,
							condiciones y limitaciones de los servicios de software, prestados bajo la modalidad "software como servicio" (SaaS).
					 	</p>
					 	<p>
					 		<b>1. SERVICIO</b> : Descripción del Servicio: <b>LOGICALSOFT SAS</b> provee una serie de servicios de software prestados en la modalidad de "SaaS"
					 		software como servicio. La utilización de los servicios de <b>LOGICALSOFT Cloud </b> Implica que EL USUARIO debe poseer los dispositivos de cómputo
					 		requeridos y su respectiva conexión a Internet (denominados dispositivos clientes), es decir, computadores que tienen acceso a Internet mediante los
					 		navegadores o browsers. bajo la modalidad de contratación (SaaS) EL USUARIO no tendrá instalado el software en sus máquinas, sino que tendrá acceso al
					 		servicio bajo el hosting suministrado por <b>LOGICALSOFT SAS</b>
					 	</p>
						<p>
							<b>2. MODULO DE SOPORTE: LOGICALSOFT SAS</b> Cuenta con un modulo de soporte propio lo que nos permite garantizar el buen funcionamiento y eficacia del mismo.
							Por este modulo que estará habilitado en todos  los servicios de <b>LOGICALSOFT Cloud </b> el usuario podrá reportar todos los casos que requieran soporte,
							programar las capacitaciones y tener contacto con nuestro equipo de soporte. El soportes de  los servicios de <b>LOGICALSOFT Cloud</b> se brinda  de
							manera remota por medio de la atención de tickets los cuales se priorizan y se atienden segun las políticas de Soporte que se describen mas adelante.
							Asi mismo el usuario podrá tener con claridad la trazabilidad de su caso ya que toda acción realizada se comunica via e-mail con mensaje automatico del
							modulo de soporte <b>LOGICALSOFT SUPPORT.</b> El soporte remoto  incluye: Chat, videoconferencia y asistencia telefónica de Lunes a Viernes de 08:00 a  18:00 y
							Sabados de 08:00 a 12:00 (Hora Colombia GTM-5), en caso de requerir alguna capacitación adicional o soporte presencial a cualquier  ciudad o país
							diferentes a Cali - Colombia el cliente deberá asumir los costos de los desplazamientos y viáticos  del personal de soporte.<br>
							<b>2.1.</b> Políticas de Soporte: <b>LOGICALSOFT SAS</b> se compromete en priorizar los tickets en las siguientes 3 horas hábiles después de recibidos,
							de acuerdo a las siguientes criterios de solución de tickets:<br>
							<table>
								<thead>
									<tr>
										<td><b>Prioridad</b></td>
										<td width="200"><b>Criterio</b></td>
										<td><b>Tiempo de Solucion</b></td>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td>BAJA &nbsp; </td>
										<td>
											<ul>
												<li type= disc >No se afecta el desempeño del software</li>
												<li type= disc >Impide de forma parcial al usuario realizar algunas funciones que no afectan la funcionalidad del Software.</li>
											</ul>
										</td>
										<td>Hasta 32 horas habiles</td>
									</tr>
									<tr>
										<td>MEDIA &nbsp;</td>
										<td>
											<ul>
												<li type= disc >Se identifica pérdida parcial de alguna funcionalidad del módulo ó Herramienta.</li>
												<li type= disc >Impide de forma parcial al usuario realizar algunas funciones que afectan la funcionalidad del Software</li>
											</ul>
										</td>
										<td>Hasta 16 horas habiles</td>
									</tr>
									<tr>
										<td>ALTA &nbsp;</td>
										<td>
											<ul>
												<li type= disc >Se identifica perdida total de la funcionalidad del software</li>
												<li type= disc >Impide de forma total al usuario ejecutar las funciones del software</li>
											</ul>
										</td>
										<td>Hasta 6 horas habiles</td>
									</tr>
								</tbody>
							</table>
							En algunos casos los tiempos de solución pueden variar dependiendo Factores ajenos al control de LOGICALSOFT SAS o sus asociados, Fallas en su hardware,
							software, servicio de internet, equipos, canales de comunicación, acciones u omisiones de los usuarios.
						</p>
						<p>
							<b>3. ACCESO Y USO DEL SERVICIO:</b>  El usuario debe utilizar de forma adecuada y lícita los servicios de <b>LOGICALSOFT Cloud</b> de conformidad con la legislación
							Colombiana aplicable; y los términos y condiciones de uso.  <b>3.1.</b> El usuario titular en los formatos de registro será el responsable del manejo de la
							cuenta adquirida, y del manejo adecuado, a quien se le entregará un usuario y clave. Recomendamos proteger la confidencialidad de su contraseña, ya que
							al compartir información con terceros, usted asumirá la responsabilidad de las acciones derivadas de este hecho, convirtiéndose así usted en el único
							responsable del manejo de su cuenta. Si conoce o sospecha del uso inadecuado de su cuenta por personas ajenas a usted, notifíquelo de inmediato a nuestra
							dirección <a href="mailto:soporte@logicalsoft.co" target="_top">soporte@logicalsoft.co</a>  Se recomienda en equipos de uso compartido, cerrar la sesión al concluir cada visita. <b>3.2.</b> Este contrato por ningún
							motivo es transferible por cesión o venta. <b> 3.3.</b> Después de activado el servicio, el usuario es libre de utilizarlo o no, sin embargo no se hará
							devolución de dinero por el no uso del mismo. El no cumplimiento con los pagos llevará a la suspensión del servicio, dicha suspensión no detiene la
							obligación contraída, y el servicio se reactivará en el momento que el usuario se encuentre al día con los pagos pactados. <b>3.4.</b> Los datos con que se
							alimenta el software estarán a cargo del usuario y será de su entera responsabilidad. <b>LOGICALSOFT SAS</b>, no es responsable del ingreso de datos y tampoco
							de la calidad de contenido ingresado por el usuario. <b>3.5.</b> De esta manera <b>LOGICALSOFT SAS</b>, NO se hace responsable de la información consignada en el
							software que ofrecemos en calidad de arrendamiento, sino por el contrario hacemos uso del principio de buena fe que se le dará uso legal y adecuado a las
							herramientas suministradas.<b> 3.6.</b> No está permitido al usuario copiar nuestros modelos de formatos o imágenes, con fines de plagio, pues se encuentran
							amparados por la Constitución y las diferentes Leyes que protegen la propiedad intelectual y sancionan a quienes atentan contra ella. <b>3.7.</b> Si el usuario
							decide no utilizar más el software adquirido, tendrá 30 días hábiles a partir del día de notificación  por escrito enviada al correo <a href="mailto:Comercial@logicalsoft.co" target="_top">Comercial@logicalsoft.co</a>
							par exportar la información a formatos excel o pdf, si se trata de alguna herramienta contable a un balance contable de la información procesada.<b> 3.8.</b>
							Los datos solicitados al usuario serán usados  únicamente con el fin de contactarlo y mantener una comunicación eficaz que permita notificarlo continuamente
							de novedades, informes, reportes y cualquier otra información de interés común, está información no será usada por terceros ni será comercializada como base
							de datos, es totalmente privada. <b>3.9.</b>  El usuario debe abstenerse de: a) dar un uso no autorizado o fraudulento a los los servicios de <b>LOGICALSOFT Cloud</b> b)
							acceder o intentar acceder a recursos o áreas restringidas de los servicios de <b>LOGICALSOFT Cloud</b>  sin cumplir las condiciones exigidas para dicho acceso; c)
							utilizar las plataformas de los servicios de <b>LOGICALSOFT Cloud</b> con fines ilícitos, ilegales, contrarios a lo establecido en las presentes condiciones generales,
							a las condiciones particulares, a la buena fe y al orden público, lesivos de los derechos e intereses de terceros, o que de cualquier forma puedan dañar,
							inutilizar, sobrecargar o impedir la normal utilización de los servicios de <b>LOGICALSOFT Cloud</b>  d) introducir o difundir en la red virus informáticos o realizar
							actuaciones susceptibles de alterar, interrumpir o generar errores o daños en los documentos electrónicos, datos o sistemas informáticos (hardware y software) de
							<b>LOGICALSOFT SAS</b>; e) intentar acceder, utilizar y/o manipular las bases de datos de los servicios de <b>LOGICALSOFT Cloud</b>  que no sean de su propiedad; f) suplantar
							la identidad de otro usuario, al momento de utilizar la plataforma de los servicios de <b>LOGICALSOFT Cloud</b> ; g) realizar cualquier conducta que genere algún tipo
							de daño a la plataforma de los servicios de <b>LOGICALSOFT Cloud</b>  o a terceros. El usuario responderá por los daños y perjuicios de toda naturaleza que <b>LOGICALSOFT SAS</b>
							pueda sufrir, como consecuencia de la indebida utilización de la plataforma de los servicios de <b>LOGICALSOFT Cloud</b>, evento en el cual <b>LOGICALSOFT SAS</b> ejercerá las
							acciones legales pertinentes.
						</p>
						<p>
							<b>4. GARANTIA DE DESEMPEÑO : LOGICALSOFT SAS</b>  Garantiza la prestación del servicio las 24 horas del día, desde cualquier equipo con acceso a Internet,
							se comunicará a los clientes con antelación sobre cualquier proceso de mantenimiento que afecte la entrada al servidor. El datacenter donde está alojada
							la aplicación cuenta con los más altos estándares de seguridad que garantizan que no hay pérdida de datos permanentes ni temporales, además de un 98% del
							tiempo de presencia garantizada Entendiéndose esta disponibilidad como el tiempo que el servicio estará habilitado para el uso general por parte de los
							usuarios. Son excluidos del cálculo de disponibilidad los tiempos en que el sistema este por fuera de línea debido a: (I) Suspensiones por fuerza mayor o
							que hayan sido planeadas e informadas con antelación; (II) Factores ajenos al control de LOGICALSOFT SAS o sus asociados; (III) Fallas en su hardware,
							software, servicio de internet, equipos o canal de comunicaciones; (IV) Acciones de sus empleados o terceros que con su cuenta de acceso y clave, generen
							alguna indisponibilidad del servicio. (V) Acciones de hackers, malware, virus, cookies, virus defectuosos, etc.; (VI) Una mala configuración por su parte
							del servicio al no seguir las instrucciones provistas por <b>LOGICALSOFT SAS.</b>; (VII) Interrupciones planificadas (que notificaremos en lo posible con por lo
							menos 8 horas de anticipación a través de los servicios adquiridos); (VIII) Los Servicios pueden estar sujetos a otras limitaciones como, por ejemplo,
							límites en el espacio de almacenamiento adquirido, caídas del sistema por parte de nuestros proveedores de infraestructura o hosting y otros casos no
							expresamente señalados ahora. <b>LOGICALSOFT SAS</b> No tiene ningún tipo de relación comercial con las empresas proveedoras del servicio de internet y no se
							hace responsable de los daños y perjuicios derivados del servicio de internet contratado por el usuario para acceder a la plataforma, además no estará
							en facultad de recomendar la compra del servicio, favoreciendo a alguna empresa proveedora del mismo. <b>LOGICALSOFT SAS</b>  realiza BackUp de manera diaria
							con el fin de asegurar y proteger la información y de esta manera ofrecer tranquilidad a nuestros usuarios, sin embargo no responde por daños originados
							por hechos vandálicos y/o acciones erróneas ejecutadas intencionalmente por terceros, ni tampoco por acciones ajenas a nuestro alcance y responsabilidad
							directa.
						</p>
						<p>
							<b>5. MODOS Y CONDICIONES DE PAGO: LOGICALSOFT SAS</b> emitirá una factura mensual la cual deberá pagarse durante los 10 días siguientes a su emisión.
							Esta  puede ser cancelada por consignación realizada a la cuenta corriente # 38174788138 de Bancolombia a nombre de <b>LOGICALSOFT SAS</b> o a la Cuenta
							Corriente del Banco de Bogotá  # 869013961 a Nombre de <b>LOGICALSOFT SAS</b>, En cuanto sea registrado el pago, el servicio será habilitado dentro de las
							24 horas siguientes al reporte de pago de la cuota pactada.  Las tarifas pactadas en cada plan tendrán un incremento anual de acuerdo al IPC del
							momento estos valores serán actualizados en nuestra página <a href="www.logicalsoft.co">www.logicalsoft.co</a>  igualmente serán dados a conocer a nuestros usuarios vía email.
						</p>
						<p>
							<b>6. SUSPENSION DEL SERVICIO : LOGICALSOFT SAS</b> hará uso de su derecho de suspender el servicio si pasados 10 días hábiles después de la fecha convenida
							para el pago no se ha recibido reporte del mismo. El servicio será restablecido durante las siguientes 24 horas de verificación del pago.
							Si el Cliente decide no continuar con nuestros servicios la cancelación de este deberá realizarse de manera formal por escrito, con una antelación
							de diez días y así no generar el proceso de facturación del mes siguiente.
						</p>
						<p>
							<b>7. PROPIEDAD PRIVADA: LOGICALSOFT SAS</b> es quien tiene los derechos reservados sobre el software, la plataforma, los modelos de formatos, e imágenes
							aquí utilizados, por lo cual este contrato se ciñe estrictamente al arrendamiento de la plataforma, la cual será utilizada por el usuario solamente
							para procesar su información .La información registrada por el usuario es propiedad exclusiva del mismo, así que <b>LOGICALSOFT SAS</b>, no le dará un uso
							diferente al de permitir al usuario ingresarla y procesarla en nuestra plataforma, con la libertad de bajarlo a un archivo plano en el momento que
							así lo desee, pues la información ingresada es de estricto uso del usuario.
						</p>
						<p>
							<b>8. NOTIFICACIONES, RECLAMACIONES Y DESACUERDOS</b>: Para cualquier inquietud el usuario podrá dirigirse a <a href="mailto:Comercial@logicalsoft.co" target="_top">Comercial@logicalsoft.co</a>, En caso de
							presentarse alguna inconformidad, las partes deberán esforzarse por resolver cordialmente el desacuerdo haciendo uso del conducto regular
							establecido para la solución de esta, de la siguiente forma: <b>8.1.</b> El usuario debe comunicarse con su asesor directamente y plantear de manera
							respetuosa su inquietud. <b>8.2.</b>  De no obtener respuesta satisfactoria, el usuario se dirigirá a la empresa <b>LOGICALSOFT SAS</b>, en donde se agotarán
							todos los recursos necesarios y se aplicaran las medidas correctivas requeridas con el fin de dar solución adecuada y oportuna a nuestro usuario.
							<b>8.3.</b> Si persistiera el inconveniente y no se encuentra solución satisfactoria al respecto, las partes seguirán el proceso de acuerdo a las leyes
							de arbitrariamente que rigen la República de Colombia, en cuyo caso, los costos que pudieran generarse por el proceso de arbitramento serán asumidos
							por quien lo determine el ente arbitral. <b>LOGICALSOFT SAS</b> puede modificar las condiciones de uso en el momento que sea necesario. Si se requiere un
							cambio sustancial, se notificará a nuestro usuario solicitando que confirme la aceptación de los términos lo que dará validez, y obligará a las partes
							según a la última actualización del mismo.
						</p>
						<p>
							<b>9. PRIVACIDAD O PROTECCIÓN DE DATOS:</b> dando cumplimiento a la Ley 1581 de 2012, expedida el 17 de octubre del 2012  por la cual se dictan disposiciones
							generales para la protección de datos personales, la cual fue reglamentada el pasado 27 de Junio a través del Decreto 1377 de 2013, expedido por
							el Ministerio de Comercio Industria y Turismo. Con base en lo establecido en la mencionada Ley y su Decreto Reglamentario, se busca regularizar
							el manejo de datos personales en Colombia, con el fin de proteger el derecho fundamental al habeas data o a la identidad informática. Teniendo en
							cuenta que <b>LOGICALSOFT SAS.</b> cuenta actualmente con bases de datos en las que reposan datos personales que han sido suministrados por usted , y en
							cumplimiento de lo establecido en el artículo 10 del Decreto 1377 de 2013, la aceptación de estos Términos y Condiciones nos autoriza para continuar
							con el tratamiento de los datos en los términos indicados en este artículo. Todo lo anterior, sin perjuicio de la facultad que usted tiene de ejercer
							en cualquier momento, cualquiera de los derechos consagrados en la Ley 1581 de 2012 y en el Decreto Reglamentario 1377 de 2013 a favor de los titulares
							de datos personales. Si usted desea mayor información sobre el manejo de sus datos personales puede consultar nuestra política de protección de datos en
							<a href="www.logicalsoft.co">www.logicalsoft.co</a>
						</p>
						<p>
							<b>10. LEGISLACIÓN APLICABLE Y DOMICILIO:</b>  Las condiciones generales de uso de los productos  <b>LOGICALSOFT</b> y las particulares que se llegaren a establecer,
							se rigen y deben ser interpretadas de conformidad con las leyes de la República de Colombia, y para todos los efectos legales a que haya lugar en el
							desarrollo y cumplimiento de las obligaciones derivadas del presente contrato las mismas se señala como domicilio contractual la ciudad de Cali, Valle
							del Cauca - Colombia.
						</p>
						<p>
							<b>11. EXCLUSION DE GARANTIAS Y DE RESPONSABILIDAD POR EL FUNCIONAMIENTO DE LOS SERVICOS DE LOGICALSOFT Cloud . LOGICALSOFT SAS</b> no será responsable por los
							daños y perjuicios que puedan derivarse de: a) fallas en el funcionamiento de la plataforma por causas ajenas a la voluntad y diligencia de <b>LOGICALSOFT SAS</b>;
							b) la interrupción en el funcionamiento del la plataforma o fallas informáticas, desconexiones, retrasos o bloqueos causados por deficiencias o sobrecargas
							en las redes, en el sistema de Internet o en otros sistemas electrónicos utilizados en el curso de su funcionamiento; c) la falta de idoneidad de la
							plataforma  para las necesidades específicas de los usuarios; y d) daños que puedan ser causados por terceras personas mediante intromisiones no autorizadas
							ajenas al control de <b>LOGICALSOFT SAS.  LOGICALSOFT SAS</b> no garantiza la ausencia de virus ni de otros elementos en la plataforma LOGICALSOFTERP, que sean
							introducidos por terceros ajenos a <b>LOGICALSOFT SAS</b> y que puedan producir alteraciones en los sistemas informáticos (software y hardware) de los usuarios.
							En consecuencia, <b>LOGICALSOFT SAS</b> no será en ningún caso responsable de los daños y perjuicios que pudieran derivarse de la presencia de virus u otros
							elementos que puedan producir alteraciones en los sistemas físicos o lógicos, documentos electrónicos o archivos de los usuarios.
							Cualquier duda o comentario respecto a los términos y condiciones de uso generales y particulares  de los servicios de <b>LOGICALSOFT Cloud.</b> podrá
							manifestarlo al correo electrónico mailto
							<a href="mailto:soporte@logicalsoft.co" target="_top">soporte@logicalsoft.co</a>

						</p>

					</div>

				</div>
			</div>

			<script>
				function aceptacionTerminos(aceptacion) {
					Ext.Ajax.request({
					    url     : 'terminos_y_condiciones.php',
					    params  :
					    {
							opc        : 'actualizaTerminosCondiciones',
							aceptacion : aceptacion,
							key        : '<?php echo base64_encode($_SESSION['IDUSUARIO'].";$mysql->ServidorDb;$mysql->UsuarioDb;$mysql->PasswordDb;$mysql->NameDb;".date("Y-m-d")); ?>',
					    },
					    success :function (result, request){
					    			console.log(result.responseText);
					    			if (result.responseText=='error_key') { alert('Key de validacion no valida!'); }
					    			if (result.responseText=='error_sql') {alert('Error al guardar la opcion seleccionada!');}
					    			if (result.responseText=='true') {
					    				if (aceptacion=='Si') {
					    					document.getElementById('openModal').parentNode.removeChild(document.getElementById('openModal'));
					    					document.getElementById('openModal2').parentNode.removeChild(document.getElementById('openModal2'));
					    				}
					    				else{
					    					window.location.href = "logout.php";
					    				}
					    			}
					            },
					    failure : function(){ alert("Error de conexion! \nIntentelo de nuevo"); }
					});
				}
			</script>
		<?php

	}

	// function (){
	// 	# code...
	// }

	function actualizaTerminosCondiciones($aceptacion,$id_usuario,$ServidorDb,$UsuarioDb,$PasswordDb,$NameDb){
		include_once('misc/ConnectDb/class.ConnectDb.php');
		$objConectDB = new ConnectDb(
						"MySql",		// API SQL A UTILIZAR  MySql, MySqli
						"$ServidorDb",	// SERVIDOR
						"$UsuarioDb",	// USUARIO DATA BASE
						"$PasswordDb",	// PASSWORD DATA BASE
						"$NameDb"		// NOMBRE DATA BASE
					);

		$mysql = $objConectDB->getApi();
		$link  = $mysql->conectar();

		$sql="UPDATE empleados
				SET
					acepta_terminos_condiciones           = '$aceptacion',
					fecha_aceptacion_terminos_condiciones = '".date('Y-m-d')."',
					hora_aceptacion_terminos_condiciones  = '".date('H:i:s')."',
					ip_aceptacion_terminos_condiciones    = '".$_SERVER['REMOTE_ADDR']."'
				WHERE activo=1 AND id=$id_usuario ";
		$query=$mysql->query($sql,$mysql->link);

		if (!$query) { echo "error_sql"; return; }
		else{ echo 'true'; return; }
	}



?>
