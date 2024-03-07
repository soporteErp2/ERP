<?php
  include_once('../../configuracion/conectar.php');
  include_once('../../configuracion/define_variables.php');

  /**
   * @class FuncionesInforme
   */
  class FuncionesInforme{
    //====================== CARGAR EL FORMATO EDITABLE ======================//
    public function cargaFormatoDocumento($tipo_documento,$id_empresa,$id_sucursal){
    	$sql = "SELECT id,texto
              FROM configuracion_documentos_erp
              WHERE tipo = '$tipo_documento'
              AND id_empresa = '$id_empresa'
              AND id_sucursal = '$id_sucursal'
              LIMIT 0,1";
    	$query = $this->mysql->query($sql,$this->mysql->link);
    	$formato = $this->mysql->result($query,0,'texto');

      return $formato;
    }

    //============ REEMPLAZAR LAS VARIABLES DEL DOCUMENTO EDITABLE ===========//
    public function reemplazarVariables($formato,$contenido,$id_empresa,$id_sucursal,$id_cliente,$cantidad_facturas,$fecha_inicio,$fecha_fin){

      //REEMPLAZAR CONTENIDO PRINCIPAL DEL FORMATO
    	if(strpos($formato,'<span style="background-color: rgb(255, 0, 0);">[CONTENIDO_DOCUMENTO]</span>') >= 0){
    		$formato = str_replace('<span style="background-color: rgb(255, 0, 0);">[CONTENIDO_DOCUMENTO]</span>',$contenido,$formato);
    	}

      //REEMPLAZAR FECHAS DEL FORMATO
      $meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");

      //REEMPLAZAR FECHA ACTUAL
      if(strpos($formato,'<span style="background-color: rgb(255, 0, 0);">[FECHA_ACTUAL]</span>') >= 0){
        $sql = "SELECT ciudad
                FROM empresas_sucursales
                WHERE id = $id_sucursal";
        $query = $this->mysql->query($sql,$this->mysql->link);
        $ciudad = $this->mysql->result($query,0,'ciudad');

        $fechaActual = $ciudad . ", " . $meses[date('n') - 1] . date(" j ") . "del" . date(" Y");
        $formato = str_replace('<span style="background-color: rgb(255, 0, 0);">[FECHA_ACTUAL]</span>',$fechaActual,$formato);
      }

      //REEMPLAZAR FECHA INICIO Y FECHA FIN
      if(strpos($formato,'<span style="background-color: rgb(255, 0, 0);">[FECHA_INICIO]</span>') >= 0){
        $fecha_inicio = $meses[date('n',strtotime($fecha_inicio)) - 1] . date(" j ",strtotime($fecha_inicio)) . "del" . date(" Y",strtotime($fecha_inicio));
        $fecha_fin = $meses[date('n',strtotime($fecha_fin)) - 1] . date(" j ",strtotime($fecha_fin)) . "del" . date(" Y",strtotime($fecha_fin));
        $formato = str_replace('<span style="background-color: rgb(255, 0, 0);">[FECHA_INICIO]</span>',$fecha_inicio,$formato);
    		$formato = str_replace('<span style="background-color: rgb(255, 0, 0);">[FECHA_FIN]</span>',$fecha_fin,$formato);
    	}

      //REEMPLAZAR TIPO DOCUMENTO TERCERO
      if(strpos($formato,'<span style="background-color: rgb(255, 0, 0);">[FV_TI_CLIENTE]</span>') >= 0){
        $sql = "SELECT tipo_identificacion
                FROM terceros
                WHERE id = $id_cliente";
        $query = $this->mysql->query($sql,$this->mysql->link);
        $tipo_identificacion = $this->mysql->result($query,0,'tipo_identificacion');
        $formato = str_replace('<span style="background-color: rgb(255, 0, 0);">[FV_TI_CLIENTE]</span>',$tipo_identificacion,$formato);
      }

      //REEMPLAZAR NUMERO DE FACTURAS
      if(strpos($formato,'<span style="background-color: rgb(255, 0, 0);">[CANTIDAD_FACTURAS]</span>') >= 0){
    		$formato = str_replace('<span style="background-color: rgb(255, 0, 0);">[CANTIDAD_FACTURAS]</span>',$cantidad_facturas,$formato);
    	}

    	$totalCaracteres = strlen($formato);
    	$comienza        = $posIni = "0";
    	$cont            = 0;

      //ACUMULAR LAS VARIABLES EN UN ARRAY
    	while(1 == 1){
    		$posIni   = strpos($formato,'<span style="background-color: rgb(255, 0, 0);">',$comienza);
    		$comienza = $posIni;
    		$posFin   = strpos($formato,']</span>',$comienza);
    		$comienza = $posFin;

    		if($posIni != ""){
    			$variable = substr($formato, $posIni, ($posFin - $posIni +8));
    			$variable = str_replace('<span style="background-color: rgb(255, 0, 0);">[', "", $variable );
    			$variable = str_replace(']</span>', "", $variable );

    			if($variable == ''){
            break;
          }

          //LAS VARIABLES SON APILADAS EN UN ARRAY PARA LUEGO HACER LA BUSQUEDA UNA A UNA
    			$arrayVariables[$cont] = $variable;
    			$cont++;
    		}
    		else{
          break;
        }
    	}

    	//QUITA LOS SPAN DE LAS VARIABLES
    	$formato = str_replace('<span style="background-color: rgb(255, 0, 0);">[', "[", $formato );
    	$formato = str_replace(']</span>',"]",$formato);

    	if($cont > 0){
        // QUITA LAS VARIABLES REPETIDAS ASI NO EXISTEN BUSQUEDAS REPETIDAS DE VARIABLES
    		$whileVariables = '';
        $arrayVariables = array_unique($arrayVariables);
    		$whileVariables = "nombre = '" . implode("' OR nombre = '", $arrayVariables) . "'";

    		$sql = "SELECT campo,tabla,nombre
                FROM variables
                WHERE ($whileVariables)
                GROUP BY nombre";
        $query = $this->mysql->query($sql,$this->mysql->link);

    		while($row = $this->mysql->fetch_assoc($query)){
    			$tabla    = $row['tabla'];
    			$campo    = $row['campo'];
    			$variable = $row['nombre'];

    			$arrayTable[$tabla][$campo] = $variable;
    		}

    		//CANTIDAD DE DIGITOS EN LOS CONSECUTIVOS
    		$sql = "SELECT documento,digitos
        				FROM configuracion_consecutivos_documentos
        				WHERE activo = 1
        				AND modulo = 'venta'
        				AND id_empresa = '$id_empresa'
        				AND id_sucursal = '$id_sucursal'";
    		$query = $this->mysql->query($sql,$this->mysql->link);

    		while($rowDigitos = mysql_fetch_assoc($query)){
    			switch($rowDigitos['documento']){
    				case 'cotizacion':
    					$arrayDigitos['ventas_cotizaciones']["consecutivo"] = $rowDigitos['digitos'];
    					break;

    				case 'pedido':
    					$arrayDigitos['ventas_pedidos']["consecutivo"] = $rowDigitos['digitos'];
    					break;

    				case 'remision':
    					$arrayDigitos['ventas_remisiones']["consecutivo"] = $rowDigitos['digitos'];
    					break;

    				case 'factura':
    					$arrayDigitos['ventas_facturas']["numero_factura"] = $rowDigitos['digitos'];
    					break;
    			}
    		}

    		$digitos = $this->mysql->result($query,0,'digitos_facturacion');

    		foreach($arrayTable as $tabla => $arrayCampos){
    			$digitos = 0;
    			switch($tabla){
    				case 'ventas_cotizaciones':
    				case 'ventas_pedidos':
    				case 'ventas_remisiones':
    				case 'ventas_facturas':
    					$whereTabla = "id_cliente = $id_cliente";
    					break;

    				case 'ventas_facturas_configuracion':
    					$whereTabla = "activo = 1 AND id_empresa = '$id_empresa' AND id_sucursal = '$id_sucursal' ORDER BY id DESC";
    					break;

    				case 'empresas':
    					$whereTabla = "activo = 1 AND id = $id_empresa";
    					break;

    				default:
    					echo 'Error al reemplazar las variables';
    					break;
    			}

    			$camposSql = implode(",", array_keys($arrayCampos));

    			$sql   = "SELECT $camposSql
                    FROM $tabla
                    WHERE $whereTabla
                    LIMIT 0,1";

    			$query = $this->mysql->query($sql,$this->mysql->link);

    			foreach ($arrayCampos as $campo=>$variable) {
    				$varReplace = $this->mysql->result($query,0,$campo);
    				if($arrayDigitos[$tabla][$campo] >= 4){
              $varReplace = str_pad($varReplace, $arrayDigitos[$tabla][$campo], "0", STR_PAD_LEFT);
            }
    				$formato = str_replace('['.$variable.']' , $varReplace , $formato);
    			}
    		}
    		return $formato;
    	}
    }
  }
?>
