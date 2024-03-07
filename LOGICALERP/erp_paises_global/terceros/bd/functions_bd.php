<?php

	/* ******************************************* carga el id del formato del documento **************************************** */
	function cargaIdFormatoDocumento($id_intercambio,$tipo){
		global $id_formato_documento,$estado,$cliente,$id_cliente,$id_cotizacion,$id_ejecituvo,$pedido,$revision,$checklist;

		if($tipo=='P'){ $query = "SELECT id FROM configuracion_documentos WHERE tipo='pedido' AND id_empresa='".$_SESSION['EMPRESA']."' AND id_sucursal='".$_SESSION['SUCURSAL']."' LIMIT 1"; }
		elseif($tipo=='C') { $query = "SELECT id_formato_cotizacion FROM pedido WHERE id='$id_intercambio'" ; }
		elseif($tipo=='D'){ $query = "SELECT id FROM configuracion_documentos WHERE tipo='Cotizacion' AND id_empresa='".$_SESSION['EMPRESA']."' AND id_sucursal='".$_SESSION['SUCURSAL']."' LIMIT 1"; }
		else{ $query = "SELECT id FROM configuracion_documentos WHERE tipo='Cotizacion' AND id_empresa='".$_SESSION['EMPRESA']."' AND id_sucursal='".$_SESSION['SUCURSAL']."' LIMIT 1"; }

		$result	= mysql_query($query);
		$id_formato_documento = mysql_result($result,0);

		$query 	= "SELECT * FROM pedido WHERE id='".$id_intercambio."'";
		$result	= mysql_query($query);

		$estado = mysql_result($result,$i,"estado");
		$cliente = mysql_result($result,$i,"nombre_cliente");
		$id_cliente = mysql_result($result,$i,"id_cliente");
		$id_cotizacion = mysql_result($result,$i,"id_cotizacion");
		$id_ejecutivo = mysql_result($result,$i,"consecutivo_cotizacion");
		$pedido = mysql_result($result,$i,"codigo_documento");
		$revision = mysql_result($result,$i,"revision");
	}

	/* ************************************* Carga campo que contiene el formato del documento ****************************** */
	function cargaTextoDocumento($id){

		if($id!=""){
			$query = "SELECT texto FROM configuracion_documentos WHERE id=".$id;
			$result=mysql_query($query);

			global $texto;
			$texto= mysql_result($result,$i,"texto");
		}
	}

	/* *************************************** reemplaza variables tag en el documento ************************************** */
	function reemplazarVariables($id_busqueda,$texto,$tipo_documento){

		$totalCaracteres = strlen($texto);
		$posIni=$comienza="0";
		$cont=0;

		do{	///// RECOJE LAS VARIABLES USADAS EN EL DOCUMENTO EN UN ARRAY
			$posIni=strpos($texto,'<span style="background-color:#ff0000;">',$comienza);
			$comienza=$posIni;
			$posFin=strpos($texto,']</span>',$comienza);
			$comienza=$posFin;
			if($posIni!=""){
				$variable = substr($texto, $posIni, ($posFin - $posIni +8));
				$variable = str_replace('<span style="background-color:#ff0000;">[', "", $variable );
				$variable = str_replace(']</span>', "", $variable );

				$variables[$cont]=$variable; /// LAS VARIABLES SON APILADAS EN UN ARRAY PARA LUEGO HACER LA BUSQUEDA UNA A UNA

				$cont++;
				//echo $variables[$cont].".$cont<br>";
			}
		}while($posIni!="");

		/////// 	QUITA LOS SPAN DE LAS VARIABLES
		$texto = str_replace('<span style="background-color:#ff0000;">[', "", $texto );
		$texto = str_replace(']</span>', "", $texto );

		$count = count($variables);
		if($count>0){
			$variables=array_unique($variables); ///	 QUITA LAS VARIABLES REPETIDAS ASI NO EXISTEN BUSQUEDAS REPETIDAS DE VARIABLES

			for ($i = 0; $i < $count; $i++) {
				//echo $variables[$i]."<br>";
				if($variables[$i]!=""){ /////	 REEMPLAZA LAS VARIABLES USADAS EN EL DOCUMENTO HACIENDO LA BUSQUEDA UNA POR UNA
					$reemplazo = buscaVariable($variables[$i],"pedido.id='".$id_busqueda."'",$tipo_documento,$id_busqueda);
					$texto = str_replace($variables[$i],$reemplazo , $texto );
				}
			}
		}
		return $texto;
	}


	///////////////////////////// FUNCION QUE DESOCUPA DOCUMENTOS VACIOS CON ESTADO '0' EN RENTAL PEDIDO/ ///////////////////////////////////////////////////////////////////////////////

	function vaciar(){
		$query = "DELETE FROM pedido WHERE estado='0' AND vencimiento_cotizacion <= date(now())";
		//echo $query;
		mysql_query($query);
	}

	///////////////////////////// FUNCION QUE CARGA CONFIGURACION DE CORREO DE LA EMPRESA / ///////////////////////////////////////////////////////////////////////////////

	function cargaConfigCorreo($filtro_empresa){
		$sql="SELECT * FROM empresas_config_correo WHERE id_empresa='".$filtro_empresa."'";
		global $SERVIDOR,$CORREO,$PASSWORD,$PUERTO,$SEGURIDAD,$AUTENTICACION;

		$SERVIDOR		= "";
		$CORREO 		= "";
		$PASSWORD		= "";
		$PUERTO			= "";
		$SEGURIDAD		= "";
		$AUTENTICACION	= "";

		$result = mysql_query($sql);
		if(mysql_num_rows($result)){

			$SERVIDOR		= mysql_result($result,0,"servidor");
			$CORREO 		= mysql_result($result,0,"correo");
			$PASSWORD		= mysql_result($result,0,"password");
			$PUERTO			= mysql_result($result,0,"puerto");
			$SEGURIDAD		= mysql_result($result,0,"seguridad_smtp");
			$AUTENTICACION	= mysql_result($result,0,"autenticacion");


		}
		echo "SERVIDOR:$SERVIDOR</br>CORREO:$CORREO</br>PASSWORD:$PASSWORD</br>PUERTO:$PUERTO</br>SEGURIDAD:$SEGURIDAD</br>AUTENTICACION:$AUTENTICACION</br>";

	}
	function styleDoc(){
		$cuerpo='<style>
					.Fuente1						{font-size:11px; font-family:"Trebuchet MS", Verdana, Arial, sans-serif, "Lucida Grande"; 					}
					.Fuente2						{font-size:20px; font-family:"Trebuchet MS", Verdana, Arial, sans-serif, "Lucida Grande"; font:bold;		}
					.Fuente3						{font-size:15px; font-family:"Trebuchet MS", Verdana, Arial, sans-serif, "Lucida Grande"; text-align:right; }
					.titulos						{font-size:12px; font-family:"Trebuchet MS", Verdana, Arial, sans-serif, "Lucida Grande"; font:bold; 		}
					.blanco							{height:5px; width:100%; float:left;}
					.inverso						{min-height:20px; width:100%; float:left; background: #555; color:#fff}
					.table 							{margin: 0 0 10 0; width:740px;		}
					.sombra1						{background: #ddd;	}
					.sombra2 						{background: #aaa;	}
					.grupo							{width:740px;	}
					.table .grupo .la .cell2		{min-height:20px; width:183px;	 float:left;}
					.cell							{min-height:20px; width:368px;	 float:left;}
					.cell1							{min-height:20px; width:141px;	 float:left;}
					.cell2							{min-height:20px; width:222px; 	 float:left;}
					.cell3							{min-height:20px; width:72px; 	 float:left;}
					.cell4							{min-height:20px; width:141px; 	 float:left;}
					.cell5							{min-height:20px; width:589px;	 float:left;}
					.Codigo							{min-height:20px; width:55px; 	 float:left;}
					.Cantidad						{min-height:20px; width:60px; 	 float:left;}
					.Nombre							{min-height:20px; width:204px;	 float:left;}
					.Valor_U						{min-height:20px; width:100px; 	 float:left;}
					.Subtotal						{min-height:20px; width:100px; 	 float:left;}
					.Descuentos						{min-height:20px; width:100px; 	 float:left;}
					.Total							{min-height:20px; width:100px; 	 float:left;}
					.title_cotizacion{ float:left; width:100%; font-size:14px; font-family: Arial; font-weight:bold;}
				</style>';
		return $cuerpo;
	}

	///////////////////////////// FUNCION QUE CARGA EL TEXTO DEL TERCERO / ///////////////////////////////////////////////////////////////////////////////

	function cargarFormatoTercero($id,$num){

		if($id!=""){


			$query = "	SELECT 	estado,
								hora_final,
								fecha_final,
								hora_inicio,
								fecha_inicio,
								nombre_cliente,
								codigo_documento,
								fecha_entrega_instalacion,
								hora_entrega_instalacion,

								terceros.telefono1 AS telefono1,
								terceros.direccion AS direccion,
								terceros.numero_identificacion AS nit,
								terceros.tipo_identificacion AS tipo_id_empresa,

								terceros_contactos.cargo,
								terceros_contactos.nombre AS contactoNombre,
								terceros_contactos.telefono1 AS tel_contacto,
								terceros_contactos.identificacion AS id_tercero,
								terceros_contactos.tipo_identificacion AS tipo_id_tercero
						FROM 	pedido
						INNER JOIN terceros_contactos
							ON 	terceros_contactos.id_tercero = pedido.id_cliente
						INNER JOIN terceros
							ON 	terceros.id = pedido.id_cliente
						WHERE 	pedido.id='".$id."' LIMIT 1";

			$result = mysql_query($query);
			$tercero = "";
			$today = date("Y-m-d H:i:s");
			$fechaINI = explode(" ",$today);
			$fecha = fecha_larga($fechaINI[0]);
			$url = '../../../../ARCHIVOS_PROPIOS/formatos/encabezado_documento'.$num.'.php';
			if(file_exists($url)){
				include($url);
			}else{
				while ($row = mysql_fetch_array($result)) {
					switch ($row['estado']) {

						case "1":
							$estado="Documento Maestro";
						break;

						case "2":
							$estado="Cotizacion";
						break;

						case "3":
							$estado="Contrato";
						break;
					}

					$tercero .=	styleDoc().'
								<div class="grupo">
									 <span class="Fuente2 cell" >'.$estado.' No.'.$row['codigo_documento'].'</span>
								</div>

								<table cellspacing="3" style="width:740px; ">
									<tbody>
										<tr>
											<td class="title_cotizacion" colspan="3" style="margin:10px 0 10px 0;">DATOS DEL CLIENTE</td>
										</tr>
										<tr>
											<td class="titulos cell1">NOMBRE</td>
											<td class="Fuente1 cell2">'.$row['nombre_cliente'].'</td>
											<td class="titulos cell1">'.$row['tipo_id_empresa'].'</td>
											<td class="Fuente1 cell2">'.$row['nit'].'</td>
										</tr>
										<tr>
											<td class="titulos cell1">DIRECCION</td>
											<td class="Fuente1 cell2">'.$row['direccion'].'</td>
											<td class="titulos cell1">TELEFONO</td>
											<td class="Fuente1 cell2">'.$row['telefono1'].'</td>
										</tr>
										<tr>
											<td class="titulos cell1">CONTACTO</td>
											<td class="Fuente1 cell2">'.$row['contactoNombre'].'</td>
											<td class="titulos cell1">Telefono</td>
											<td	class="Fuente1 cell2">'.$row['tel_contacto'].'</td>
										</tr>
										<tr>
											<td class="titulos cell1">Email</td>
											<td class="Fuente1 cell2">'.$row['terceros_contactos_email'].'</td>
											<td class="titulos cell1">Cargo</td>
											<td class="Fuente1 cell2">'.$row['cargo'].'</td>
										</tr>

									</tbody>
								</table>
								<table cellspacing="3" style="width:740px; ">
									<tbody>
										<tr>
											<td class="title_cotizacion" colspan="3" style="margin:10px 0 10px 0;">DATOS DEL EVENTO</td>
										</tr>
										<tr>
											<td class="titulos cell1">FECHA INSTALACION</td>
											<td class="Fuente1 cell2">'.$row['fecha_entrega_instalacion'].'</td>
											<td class="titulos cell1">'.$row['hora_entrega_instalacion'].'</td>
											<td class="Fuente1 cell2"></td>
										</tr>
										<tr>
											<td class="titulos cell1">FECHA INICIO</td>
											<td class="Fuente1 cell2">'.$row['fecha_inicio'].'</td>
											<td class="titulos cell1">'.$row['hora_inicio'].'</td>
											<td class="Fuente1 cell2"></td>
										</tr>
										<tr>
											<td class="titulos cell1">FECHA FINALIZACION</td>
											<td class="Fuente1 cell2">'.$row['fecha_final'].'</td>
											<td class="titulos cell1">'.$row['hora_final'].'</td>
											<td	class="Fuente1 cell2"></td>
										</tr>
									</tbody>
								</table>';
				}
			}
			return $tercero;
		}
	}

	///////////////////////////// FUNCION QUE CARGA EL TEXTO DE LOS REQUERIMIENTOS / ///////////////////////////////////////////////////////////////////////////////

	function cargarFormatoRequerimientos($id_intercambio_cotizacion,$num){

		if($id_intercambio_cotizacion!=""){

			// $url = '../../../ARCHIVOS_PROPIOS/formatos/contenido_documento'.$num.'.php';
			// if(file_exists($url)){
			// 	include($url);
			// }else{
				$query = "	SELECT 	codigo_items,
									cantidad,
									nombre_items,
									id_dia,
									valor_unitario,
									descuento,
									tipo_descuento,
									total
							FROM pedido_requerimientos
							WHERE id_intercambio='$id_intercambio_cotizacion'
							ORDER BY id_dia ASC";

				$result=mysql_query($query);
				$requerimientos = "<br>";
				$requerimientos .= styleDoc().'
										<table class="table" cellspacing="3" style="width:740px;">
											<tbody>
												<tr>
													<td colspan="6" class="sombra2">REQUERIMIENTOS DEL SERVICIO</td>
												</tr>
												<tr>
													<td class="titulos sombra2">FECHA</td>
													<td class="titulos sombra2">CODIGO</td>
													<td class="titulos sombra2">CANTIDAD</td>
													<td class="titulos sombra2">SERVICIO</td>
													<td class="titulos sombra2">DESCUENTO</td>
													<td class="titulos sombra2">TOTAL</td>
												</tr>';
				while ($row = mysql_fetch_array($result)) {
					$requerimientos .= '
												<tr>
													<td class="Fuente1">'.$row['id_dia'].'</td>
													<td class="Fuente1">'.$row['codigo_items'].'</td>
													<td class="Fuente1">'.$row['cantidad'].'</td>
													<td class="Fuente1">'.$row['nombre_items'].'</td>
													<td class="Fuente1"><div style="float:right; width:15px; text-align:right">'.$row['tipo_descuento'].'</div><div style="float:right;">'.$row['descuento'].'</div></td>
													<td class="Fuente1" style="text-align:right">'.$row['total'].'</td>
												</tr>';
				}
				$requerimientos .= '		</tbody>
										</table>
										</ br>';
			//}
			return $requerimientos;
		}
	}



	///////////////////////////// FUNCION QUE BUSCA UNA VARIABLE EN LA BD, SE USA CON LA TABLA DE VARIABLES / ///////////////////////////////////////////////////////////////////////////////

	function buscaVariable($variable,$where,$tipo_documento,$id_busqueda){ /// FUNCION QUE BUSCA LA VARIABLE EN LA TABLA VARIABLES Y CON EL WHERE HACE LA CONSULTA PARA SABER EL VALOR

		if(strpos($variable,'CONTENIDO_CABECERA')===0 || strpos($variable,'CONTENIDO_DOCUMENTO')===0){
			if(strpos($variable,'CONTENIDO_DOCUMENTO')===0){
				$numero = ereg_replace("[^0-9]", "", $variable);
				return cargarFormatoRequerimientos($id_busqueda,$numero);
			}else
			if(strpos($variable,'CONTENIDO_CABECERA')===0){
				$numero = ereg_replace("[^0-9]", "", $variable);
				return cargarFormatoTercero($id_busqueda,$numero);
			}
		}else{

			$query = "SELECT campo,tabla FROM variables where nombre='".$variable."'";
			///echo $query."<br />";
			$result=mysql_query($query);

			$num	= mysql_numrows($result);

			if ($num!="0"){
				$campo= mysql_result($result,$i,"campo");
				$tabla= mysql_result($result,$i,"tabla");

				$query = "SELECT tabla_principal FROM configuracion_documentos_tipo WHERE id='".$tipo_documento."'";
				//echo $query;
				$result=mysql_query($query);

				$tabla_principal= mysql_result($result,$i,"tabla_principal");

				if($tabla_principal==$tabla){
					$query =" SELECT ".$campo.
					" FROM ".$tabla.
					" WHERE ".$where;
					//echo $query;

					$result=mysql_query($query);
					$campo= mysql_result($result,0);
				}else{
					$query =
					" SELECT ".$campo.
					" FROM ".$tabla.",".$tabla_principal.
					" WHERE ".$where.
					" AND ".$tabla.".id=id_".$tabla;
					//echo $query;
					$result=mysql_query($query);
					$campo= mysql_result($result,0);
				}
					return "<b>".$campo."</b>";
					//return $campo;
			}else{
				return '<b><span style="background-color:#ff0000;">!!CAMPO NO CREADO EN EL SISTEMA!!</span></b>';
			}
		}
	}


	function optionCuerpoMail($tipo,$id_contrato,$empresa,$sucursal){
		$query 	= "SELECT id,nombre FROM configuracion_documentos WHERE id_empresa=".$empresa." AND id_sucursal=".$sucursal." AND tipo='Cuerpo e-mail Cotizacion'";
		$result	= mysql_query($query);
		$num	= mysql_numrows($result);
		$i		= 0;
		//echo "<input type='text' value='".$query."'/>";

		while ($i < $num) {
			 	$id = mysql_result($result,$i,"id");
			 	$nombre = mysql_result($result,$i,"nombre");
			 	//if($id_contrato==$id){
				 	//echo "<option value='".$id."' selected>".$nombre."</option>";
				//}else{
					echo "<option value='".$id."'>".$nombre."</option>";
				//}
			 $i++;
		}
		echo "<option value=''>Vacio</option>";
	}

?>