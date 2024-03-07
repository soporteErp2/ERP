<?php

	/* ******************************************* carga el id del formato del documento **************************************** */
	function cargaFormatoDocumento($id_empresa){
		$query  = "SELECT id,texto FROM configuracion_documentos_erp WHERE tipo='FV' AND id_empresa='$id_empresa' LIMIT 0,1"; 
		$result = mysql_query($query);
		$texto  = mysql_result($result,0,'texto');

		return $texto;
	}

		///////////////////////////// FUNCION QUE REEMPLAZA LAS VARIABLES TAG EN EL DOCUMENTO / ///////////////////////////////////////////////////////////////////////////////
	function reemplazarVariables($texto,$body,$id_empresa,$id_documento){
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


			$sql   = "SELECT campo,tabla,nombre FROM variables WHERE $whileVariables";
			$query = mysql_query($sql);
			while ($rowVar = mysql_fetch_array($query)) {
				$tabla    = $rowVar['tabla'];
				$campo    = $rowVar['campo'];
				$variable = $rowVar['nombre'];

				$arrayCampos[$tabla] = ($arrayCampos[$tabla] != '')? $arrayCampos[$tabla].",$campo": "$campo";
				$arrayVariables[$tabla][$campo] = $variable; 
			}

			foreach ($arrayCampos as $tabla => $campos) {
				switch ($tabla) {
					case 'ventas_facturas':
						$whereTabla = "id = $id_documento";
						break;

					case 'ventas_facturas_configuracion':
						$whereTabla = "activo=1 AND id_empresa=$id_empresa ORDER BY id DESC";
						break;

					case 'empresas':
						$whereTabla = "activo=1 AND id=$id_empresa";
						break;
					
					default:
						echo 'Error al reemplazar las variables';
						break;
				}
				$sql   = "SELECT $campos FROM $tabla WHERE $whereTabla LIMIT 0,1";
				$query = mysql_query($sql);

				$arrayCamposSelect = explode(',', $campos);

				foreach ($arrayCamposSelect as $campoSelect) {
					$varReplace = mysql_result($query,0,$campoSelect);
					$variable   = $arrayVariables[$tabla][$campoSelect];

					$texto = str_replace('['.$variable.']' , $varReplace , $texto);
				}
			}

			if(strpos($texto,'[CONTENIDO_DOCUMENTO]')>=0){
				$texto = str_replace('[CONTENIDO_DOCUMENTO]' , '{[mitad]}'.$body , $texto);
				$arrayDocumento = explode('{[mitad]}', $texto);
			}
			return $arrayDocumento;
		}
	}

?>