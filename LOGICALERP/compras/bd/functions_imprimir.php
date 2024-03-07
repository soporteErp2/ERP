<?php

	/* ******************************************* carga el id del formato del documento **************************************** */

	function cargaFormatoDocumento($id_empresa, $id_sucursal, $tipoDocumento){
		$query  = "SELECT id,texto FROM configuracion_documentos_erp WHERE tipo='$tipoDocumento' AND id_empresa='$id_empresa' AND id_sucursal='$id_sucursal' LIMIT 0,1";
		$result = mysql_query($query);
		$texto  = mysql_result($result,0,'texto');

		return $texto;
	}

	///////////////////////////// FUNCION QUE REEMPLAZA LAS VARIABLES TAG EN EL DOCUMENTO / ///////////////////////////////////////////////////////////////////////////////
	function reemplazarVariables($texto,$body,$id_empresa,$id_sucursal,$id_documento){

		if(strpos($texto,'<span style="background-color: rgb(255, 0, 0);">[CONTENIDO_DOCUMENTO]</span>')>=0){
			$texto = str_replace('<span style="background-color: rgb(255, 0, 0);">[CONTENIDO_DOCUMENTO]</span>' , $body , $texto);
		}

		$totalCaracteres = strlen($texto);
		$comienza = $posIni = "0";
		$cont     = 0;

		while ( 1 == 1) {	///// RECOJE LAS VARIABLES USADAS EN EL DOCUMENTO EN UN ARRAY
			$posIni   = strpos($texto,'<span style="background-color: rgb(255, 0, 0);">',$comienza);
			$comienza = $posIni;
			$posFin   = strpos($texto,']</span>',$comienza);
			$comienza = $posFin;

			if($posIni!=""){
				$variable = substr($texto, $posIni, ($posFin - $posIni +8));
				$variable = str_replace('<span style="background-color: rgb(255, 0, 0);">[', "", $variable );
				$variable = str_replace(']</span>', "", $variable );

				if($variable == ''){ break; }
				$variables[$cont] = $variable; /// LAS VARIABLES SON APILADAS EN UN ARRAY PARA LUEGO HACER LA BUSQUEDA UNA A UNA
				$cont++;
			}
			else{ break; }
		}


		/////// 	QUITA LOS SPAN DE LAS VARIABLES
		$texto = str_replace('<span style="background-color: rgb(255, 0, 0);">[', "[", $texto );
		$texto = str_replace(']</span>', "]", $texto );

		if($cont>0){

			$whileVariables = '';
			$variables      = array_unique($variables); 	// QUITA LAS VARIABLES REPETIDAS ASI NO EXISTEN BUSQUEDAS REPETIDAS DE VARIABLES

			for ($i = 0; $i < $cont; $i++) {
				$whileVariables .= ($whileVariables == '')? "nombre='".$variables[$i]."'": " OR nombre='".$variables[$i]."'";
			}

			$sql   = "SELECT campo,tabla,nombre FROM variables WHERE id>0 AND ($whileVariables) GROUP BY nombre";
			$query = mysql_query($sql);
			while ($rowVar = mysql_fetch_array($query)) {
				$tabla    = $rowVar['tabla'];
				$campo    = $rowVar['campo'];
				$variable = $rowVar['nombre'];

				$arrayTable[$tabla][$campo] = $variable;
				// $arrayCampos[$tabla] = ($arrayCampos[$tabla] != '')? $arrayCampos[$tabla].",$campo": "$campo";
				$arrayVariables[$tabla][$variable] = $campo;
			}

			foreach ($arrayTable as $tabla => $arrayCampos) {
				switch ($tabla) {
					case 'compras_ordenes':
						$whereTabla = "id = $id_documento";
						break;

					case 'empresas':
						$whereTabla = "activo=1 AND id=$id_empresa";
						break;

					case 'compras_requisicion':
					    $whereTabla = "id = $id_documento";
					    break;

					case 'compras_facturas':
					    $whereTabla = "id = $id_documento";
					    break;

					case 'compras_entrada_almacen':
					    $whereTabla = "id = $id_documento";
					    break;

					default:
						echo 'Error al reemplazar las variables';
						break;
				}

				$camposSql = implode(",", array_keys($arrayCampos));
				$sql   = "SELECT $camposSql FROM $tabla WHERE $whereTabla LIMIT 0,1;";
				$query = mysql_query($sql);

				foreach ($arrayVariables[$tabla] as $variable => $campo) {
					$varReplace = mysql_result($query,0,$campo);
					if($arrayDigitos[$tabla][$campo] >= 4){ $varReplace = str_pad($varReplace, $arrayDigitos[$tabla][$campo], "0", STR_PAD_LEFT); }
					$texto = str_replace('['.$variable.']' , $varReplace , $texto);
					// echo $campo.' -> ['.$var.'] = '.$arrayVariables[$tabla][$variable].'<br>';
				}
			}

			return $texto;
		}
	}

	// FUNCIONES DE CONVERSION DE NUMEROS A LETRAS.

	/*!
	  @function num2letras ()
	  @abstract Dado un n?mero lo devuelve escrito.
	  @param $num number - N?mero a convertir.
	  @param $fem bool - Forma femenina (true) o no (false).
	  @param $dec bool - Con decimales (true) o no (false).
	  @result string - Devuelve el n?mero escrito en letra.
	*/
	function num2letras($num, $fem = false, $dec = true) {
	   $matuni[2]  = "dos";
	   $matuni[3]  = "tres";
	   $matuni[4]  = "cuatro";
	   $matuni[5]  = "cinco";
	   $matuni[6]  = "seis";
	   $matuni[7]  = "siete";
	   $matuni[8]  = "ocho";
	   $matuni[9]  = "nueve";
	   $matuni[10] = "diez";
	   $matuni[11] = "once";
	   $matuni[12] = "doce";
	   $matuni[13] = "trece";
	   $matuni[14] = "catorce";
	   $matuni[15] = "quince";
	   $matuni[16] = "dieciseis";
	   $matuni[17] = "diecisiete";
	   $matuni[18] = "dieciocho";
	   $matuni[19] = "diecinueve";
	   $matuni[20] = "veinte";
	   $matunisub[2] = "dos";
	   $matunisub[3] = "tres";
	   $matunisub[4] = "cuatro";
	   $matunisub[5] = "quin";
	   $matunisub[6] = "seis";
	   $matunisub[7] = "sete";
	   $matunisub[8] = "ocho";
	   $matunisub[9] = "nove";

	   $matdec[2] = "veint";
	   $matdec[3] = "treinta";
	   $matdec[4] = "cuarenta";
	   $matdec[5] = "cincuenta";
	   $matdec[6] = "sesenta";
	   $matdec[7] = "setenta";
	   $matdec[8] = "ochenta";
	   $matdec[9] = "noventa";
	   $matsub[3]  = 'mill';
	   $matsub[5]  = 'bill';
	   $matsub[7]  = 'mill';
	   $matsub[9]  = 'trill';
	   $matsub[11] = 'mill';
	   $matsub[13] = 'bill';
	   $matsub[15] = 'mill';
	   $matmil[4]  = 'millones';
	   $matmil[6]  = 'billones';
	   $matmil[7]  = 'de billones';
	   $matmil[8]  = 'millones de billones';
	   $matmil[10] = 'trillones';
	   $matmil[11] = 'de trillones';
	   $matmil[12] = 'millones de trillones';
	   $matmil[13] = 'de trillones';
	   $matmil[14] = 'billones de trillones';
	   $matmil[15] = 'de billones de trillones';
	   $matmil[16] = 'millones de billones de trillones';

	   //Zi hack
	   $float=explode('.',$num);
	   $num=$float[0];

	   $num = trim((string)@$num);
	   if ($num[0] == '-') {
	      $neg = 'menos ';
	      $num = substr($num, 1);
	   }else
	      $neg = '';
	   while ($num[0] == '0') $num = substr($num, 1);
	   if ($num[0] < '1' or $num[0] > 9) $num = '0' . $num;
	   $zeros = true;
	   $punt = false;
	   $ent = '';
	   $fra = '';
	   for ($c = 0; $c < strlen($num); $c++) {
	      $n = $num[$c];
	      if (! (strpos(".,'''", $n) === false)) {
	         if ($punt) break;
	         else{
	            $punt = true;
	            continue;
	         }

	      }elseif (! (strpos('0123456789', $n) === false)) {
	         if ($punt) {
	            if ($n != '0') $zeros = false;
	            $fra .= $n;
	         }else

	            $ent .= $n;
	      }else

	         break;

	   }
	   $ent = '     ' . $ent;
	   if ($dec and $fra and ! $zeros) {
	      $fin = ' coma';
	      for ($n = 0; $n < strlen($fra); $n++) {
	         if (($s = $fra[$n]) == '0')
	            $fin .= ' cero';
	         elseif ($s == '1')
	            $fin .= $fem ? ' una' : ' un';
	         else
	            $fin .= ' ' . $matuni[$s];
	      }
	   }else
	      $fin = '';
	   if ((int)$ent === 0) return 'Cero ' . $fin;
	   $tex = '';
	   $sub = 0;
	   $mils = 0;
	   $neutro = false;
	   while ( ($num = substr($ent, -3)) != '   ') {
	      $ent = substr($ent, 0, -3);
	      if (++$sub < 3 and $fem) {
	         $matuni[1] = 'una';
	         $subcent = 'as';
	      }else{
	         $matuni[1] = $neutro ? 'un' : 'uno';
	         $subcent = 'os';
	      }
	      $t = '';
	      $n2 = substr($num, 1);
	      if ($n2 == '00') {
	      }elseif ($n2 < 21)
	         $t = ' ' . $matuni[(int)$n2];
	      elseif ($n2 < 30) {
	         $n3 = $num[2];
	         if ($n3 != 0) $t = 'i' . $matuni[$n3];
	         $n2 = $num[1];
	         $t = ' ' . $matdec[$n2] . $t;
	      }else{
	         $n3 = $num[2];
	         if ($n3 != 0) $t = ' y ' . $matuni[$n3];
	         $n2 = $num[1];
	         $t = ' ' . $matdec[$n2] . $t;
	      }
	      $n = $num[0];
	      if ($n == 1) {
	         $t = ' ciento' . $t;
	      }elseif ($n == 5){
	         $t = ' ' . $matunisub[$n] . 'ient' . $subcent . $t;
	      }elseif ($n != 0){
	         $t = ' ' . $matunisub[$n] . 'cient' . $subcent . $t;
	      }
	      if ($sub == 1) {
	      }elseif (! isset($matsub[$sub])) {
	         if ($num == 1) {
	            $t = ' mil';
	         }elseif ($num > 1){
	            $t .= ' mil';
	         }
	      }elseif ($num == 1) {
	         $t .= ' ' . $matsub[$sub] . 'ón';
	      }elseif ($num > 1){
	         $t .= ' ' . $matsub[$sub] . 'ones';
	      }
	      if ($num == '000') $mils ++;
	      elseif ($mils != 0) {
	         if (isset($matmil[$sub])) $t .= ' ' . $matmil[$sub];
	         $mils = 0;
	      }
	      $neutro = true;
	      $tex = $t . $tex;
	   }
	   $tex = $neg . substr($tex, 1) . $fin;
	   //Zi hack --> return ucfirst($tex);
	   // $end_num=ucfirst($tex).' pesos '.$float[1].'/100 M.N.';
	   $end_num=ucfirst($tex).' '.$_SESSION['DESCRIMONEDA'];
	   return $end_num;
	}

?>