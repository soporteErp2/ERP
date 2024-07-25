<?php
	
	include_once("../../../../configuracion/conectar.php");
	include_once("../../../../configuracion/define_variables.php");
	
	/**
	 * class ClassSupportDocument
	 * @param private string 	documentId invoice unique id
	 * @param private Object 	mysql objecto connection
	 * @param private string 	companyId id for each company
	 * @param private string   	branchOfficeId id for place selected
	 * @param private string    supplierId id about supplier into invoice
	 * 
	 */
	class ClassSupportDocument{

		public $documentId;
		public $supplierId;
		public $mysql;
		public $companyId;
		public $branchOfficeId;
		public $currencyDecimals;
		public $jsonStructure;
		public $totalsDocument;
		public $totalTaxes;

		public function __construct($documentId,$mysql){
			$this->documentId       = $documentId;
			$this->mysql            = $mysql;
			$this->companyId        = $_SESSION["EMPRESA"];
			$this->branchOfficeId   = $_SESSION["SUCURSAL"];
			$this->currencyDecimals = $_SESSION["DECIMALESMONEDA"];
		}

		/**
		 * getDocumentInfo get header invoice information
		 * @return [array] associative array with invoice header information
		 */
		public function getDocumentInfo(){
			$sql = "SELECT
						CF.fecha_inicio,
						CF.hora_generacion,
						CF.prefijo_factura,
						CF.numero_factura,
						CF.consecutivo,
						CF.id_proveedor,
						CF.nit,
						CF.fecha_final,
						CF.observacion,
						CF.id_sucursal,
						CF.sucursal,
						CF.fecha_final,
						CMP.codigo_metodo_pago_dian	,
						CCP.estado			
					FROM
						compras_facturas AS CF
					LEFT JOIN
						configuracion_metodos_pago AS CMP
					ON
						CF.id_metodo_pago = CMP.id
					LEFT JOIN
						configuracion_cuentas_pago AS CCP
					ON
						CF.id_configuracion_cuenta_pago = CCP.id
					WHERE
						CF.id = $this->documentId";

			$query = $this->mysql->query($sql,$this->mysql->link);
			return mysql_fetch_assoc($query);
		}

		/**
		 * setStructureHeader create the json structure
		 */
		public function setStructureHeader($array){
			$this->fecha_vencimiento = $array['fecha_inicio'];
			$this->nombre_sucursal = $array['sucursal'];

			$this->jsonStructure["Comprobante"]=[
				"OrigenDocumento" => "",
				"TipoComprobante" => "05",
				"Fecha"           => "$array[fecha_inicio] $array[hora_generacion]",
				"Prefijo"         => "$array[prefijo_factura]",
				"Numero"          => "$array[consecutivo]",
				"Moneda"          => "COP",
				"Referencia"      => "",
				"ConceptoRef"     => 0,
				"Observaciones"   => $this->removeAccents($array["observacion"]),
				"Usuario"         => ($_SESSION['NOMBREFUNCIONARIO'] == "")? "Usuario Soporte" : $this->removeAccents($_SESSION['NOMBREFUNCIONARIO']),
				"Descripcion"     => [],
				"MetodoPago"      => [
					array(
						"FormaPago"         => "1",
						"MedioPago"         => $array['codigo_metodo_pago_dian'],
						"Fecha_Vencimiento" => $array['fecha_inicio']
					)
				],
				"Anexos"                        => null,
				"NumeroOrden"                   => "",
				"NumeroDespacho"                => "",
				"NumeroRecepcion"               => "",
				"DocumentoAdicionalNotaCredito" => "",
				"DocumentoReferenciaCodigo"     => ""
			];
		}

		/**
		 * getTransmitter obtain info about transmitter
		 */
		public function getTransmitter(){
			$sql = "SELECT
						E.documento,
						E.digito_verificacion,
						E.tipo_regimen,
						E.razon_social,
						E.nombre,
						E.email,
						E.client_token,
						E.access_token,
						E.tipo_persona_codigo,
						TD.codigo_tipo_documento_dian,
						UP.pais,
						UP.iso2,
						CM.moneda,
						ES.direccion,
						ES.telefono,
						ES.codigo_postal,
						ES.numero_matricula_mercantil,
						UD.departamento,
						UD.codigo_departamento,
						UC.ciudad,
						CONCAT(UD.codigo_departamento,UC.codigo_ciudad) AS codigo_ciudad
					FROM
						empresas AS E
					LEFT JOIN
						tipo_documento AS TD
					ON
						E.tipo_documento = TD.id
					LEFT JOIN
						ubicacion_pais AS UP
					ON
						E.id_pais = UP.id
					LEFT JOIN
						configuracion_moneda AS CM
					ON
						E.id_moneda = CM.id
					LEFT JOIN
						empresas_sucursales AS ES
					ON
						E.id = ES.id_empresa
					LEFT JOIN
						ubicacion_departamento AS UD
					ON
						ES.id_departamento = UD.id
					LEFT JOIN
						ubicacion_ciudad AS UC
					ON
						ES.id_ciudad = UC.id
					WHERE
						E.id = '$this->companyId'
					AND
						ES.id = '$this->branchOfficeId'
					GROUP BY
						E.id";
			
			$query = $this->mysql->query($sql,$this->mysql->link);
			return mysql_fetch_assoc($query);
		}

		/**
		 * setTransmitter transmit info about transmitter
		 */
		public function setTransmitter($array){
			$this->jsonStructure["Emisor"]=[
				"Identificacion"           => $array['documento'],
				"DigitoVerificador"        => $array['digito_verificacion'],
				"TipoPersona"              => $array['tipo_persona_codigo'],
				"TipoIdentificacion"       => $array['codigo_tipo_documento_dian'],
				"TipoEmisor"               => "R-99-PN",
				"RazonSocial"              => $this->removeAccents($array['razon_social']),
				"NombreComercial"          => $this->removeAccents($array['nombre']),
				"Sucursal"                 => $this->nombre_sucursal,
				"Direccion"                => $this->removeAccents($array['direccion']),
				"Telefono"                 => $array['telefono'],
				"email"                    => $this->removeAccents($array['email']),
				"Pais"                     => $array['pais'],
				"PaisCodigo"               => $array['iso2'],
				"Departamento"             => $this->removeAccents($array['departamento']),
				"DepartamentoCodigo"       => $array['codigo_departamento'],
				"Ciudad"                   => $this->removeAccents($array['ciudad']),
				"CiudadCodigo"             => $array['codigo_ciudad'],
				"CodigoPostal"             => $array['codigo_postal'],
				"Descripcion"              => null,
				"NumeroMatriculaMercantil" => $array['numero_matricula_mercantil'],
				"CorreoAutomatico"         => null,
      			"PrefijoCorreo"            => null
			];
		}

		/**
		 * getSupplier obtain info about supplier
		 */
		public function getSupplier(){
			$sql = "SELECT
                        T.id_tipo_persona_dian,
                        T.numero_identificacion,
                        T.nombre,
						T.nombre_comercial,
						T.email,
						T.telefono1,
						T.direccion,
                        T.iso2,
                        T.id_pais,
						T.pais,
						T.sector_empresarial,
						T.dv,
						T.id_tipo_persona_dian,
                        TT.codigo_regimen_dian,
                        UD.departamento,
                        UD.codigo_departamento,
                        UC.ciudad,
						CONCAT(UD.codigo_departamento,UC.codigo_ciudad) AS codigo_ciudad,
                        TD.codigo_tipo_documento_dian
					FROM
						terceros AS T
					LEFT JOIN
						terceros_tributario AS TT
					ON
						T.id_tercero_tributario = TT.id
					LEFT JOIN
						tipo_documento AS TD
					ON
						T.id_tipo_identificacion = TD.id
					LEFT JOIN
						ubicacion_departamento AS UD
					ON
						T.id_departamento = UD.id
					LEFT JOIN
						ubicacion_ciudad AS UC
					ON
						T.id_ciudad = UC.id
                    WHERE
                        T.activo = 1
                    AND
                        T.id = '$this->supplierId'
                    AND
                        T.id_empresa = '$this->companyId'";
			
			$query = $this->mysql->query($sql,$this->mysql->link);
			return mysql_fetch_assoc($query);
		}

		/**
		 * setSupplier transmit info about supplier
		 */
		public function setSupplier($array){
			$this->jsonStructure["Receptor"]=[
				"Residente"          => ($array['iso2'] == "CO")? true : false,
				"Identificacion"     => $array['numero_identificacion'],
				"DigitoVerificador"  => $array['dv'],
				"TipoPersona"        => ($array['codigo_tipo_documento_dian'] == '31')? '1' : '2',
				"TipoIdentificacion" => $array['codigo_tipo_documento_dian'],
				"TipoReceptor"       => "R-99-PN",
				"RazonSocial"        => $this->removeAccents($array['nombre']),
				"NombreComercial"    => $this->removeAccents($array['nombre_comercial']),
				"Direccion"          => $this->removeAccents($array['direccion']),
				"Telefono"           => $array['telefono1'],
				"email"              => $this->removeAccents($array['email']),
				"Pais"               => $array['pais'],
				"PaisCodigo"         => $array['iso2'],
				"Departamento"       => ($array['iso2'] == "CO")? $this->removeAccents($array['departamento']) : "No Aplica",
				"DepartamentoCodigo" => ($array['iso2'] == "CO")? $array['codigo_departamento'] : "99",
				"Ciudad"             => ($array['iso2'] == "CO")? $this->removeAccents($array['ciudad']) : "No Aplica",
				"CiudadCodigo"       => ($array['iso2'] == "CO")? $array['codigo_ciudad'] : "99",
			];
		}

		/**
		 * getSupplierAdditionalInfo obtain additional info about supplier
		 */
		public function getSupplierAdditionalInfo(){
			$sql = "SELECT
						TD.direccion,
						TD.ciudad,
						TD.departamento,
						TD.telefono1,
						TD.codigo_postal,
						UD.codigo_departamento,
						CONCAT(UD.codigo_departamento,UC.codigo_ciudad) AS codigo_ciudad,
						TD.numero_matricula_mercantil
					FROM
						terceros_direcciones AS TD
					LEFT JOIN
						ubicacion_departamento AS UD
					ON
						TD.id_departamento = UD.id
					LEFT JOIN
						ubicacion_ciudad AS UC
					ON
						TD.id_ciudad = UC.id
					WHERE
						TD.id_tercero = '$this->supplierId'
					AND
						TD.activo = 1
					LIMIT
						0,1";
			$query = $this->mysql->query($sql,$this->mysql->link);
			return mysql_fetch_assoc($query);
		}

		/**
		 * setSupplierAdditionalInfo transmit additional info about supplier
		 */
		public function setSupplierAdditionalInfo($array){
			$this->jsonStructure["Receptor"]+=[
				"CodigoPostal"             => ($array['codigo_departamento'] != '')? $array['codigo_postal'] : '11001',
				"Descripcion"              => null,
				"NumeroMatriculaMercantil" => $array['numero_matricula_mercantil'],
			];
		}

		/**
		 * getInvoiceDetails obtain invoice details
		 */
		public function getInvoiceDetails(){
			$sql = "SELECT
						CF.fecha_inicio,
						CFI.codigo,
						SUM(CFI.cantidad) AS cantidad,
						CFI.nombre,
						CFI.costo_unitario,
						CFI.observaciones,
						CFI.tipo_descuento,
						CFI.descuento,
						CFI.impuesto,
						CFI.valor_impuesto,
						I.codigo_impuesto_dian,
						IU.codigo_dian AS codigo_unidad_medida
					FROM
						compras_facturas_inventario AS CFI
					LEFT JOIN
						compras_facturas AS CF
					ON
						CFI.id_factura_compra = CF.id
					LEFT JOIN
						impuestos AS I
					ON
						I.id = CFI.id_impuesto
					LEFT JOIN
						inventario_unidades AS IU
					ON
						CFI.id_unidad_medida = IU.id
					WHERE
						CFI.activo = 1
					AND
						CFI.id_factura_compra = $this->documentId
					AND
						CFI.id_empresa = '$this->companyId'
					GROUP BY
						CFI.codigo,CFI.costo_unitario,CFI.tipo_descuento,CFI.descuento,CFI.observaciones";
			
			$query = $this->mysql->query($sql,$this->mysql->link);
			
			while($row = mysql_fetch_assoc($query)){
				$response[] = $row;
			}

			return $response;
		}

		/**
		 * setInvoiceDetails obtain invoice details
		 */
		public function setInvoiceDetails($array){
			foreach($array as $data){

				//SUBTOTAL WITHOUT DISCOUNT
				$subtotalItem = $data["cantidad"] * $data["costo_unitario"];

				//CALCULATE ONLY DISCOUNT
				if($data["descuento"] > 0){
					if($data["tipo_descuento"] == "porcentaje"){
						$totalDiscount = $subtotalItem * $data["descuento"] / 100;
					}
					else{
						$totalDiscount = $data["descuento"];
					}
				}
				else{
					$totalDiscount = 0;
				}

				//SUBTOTAL WITH DISCOUNT
				$subtotalItemMinusDiscount = $subtotalItem - $totalDiscount;

				if($data["valor_impuesto"] > 0){
					$totalTax = $this->roundValues($subtotalItemMinusDiscount) * $data["valor_impuesto"] / 100;
				}
				else{
					$totalTax = 0;
				}

				//TOTAL WITH TAX
				$totalItem = $subtotalItemMinusDiscount + $totalTax;
				
				//TAX BY EACH ITEM
				if($data["impuesto"] == null || $data["impuesto"] == ""){
					$codeTax = "ZY";
					$nameTax = "";
					$percentajeTax = 0;
					$valueTax = 0;
				}
				else{
					$codeTax = $data["codigo_impuesto_dian"];
					$nameTax = $data["impuesto"];
					$percentajeTax = $this->roundValues($data["valor_impuesto"]);
					$valueTax = $totalTax;
				}

				$productList[] = array(
					"idDetalle" => "",
					"Nombre" => $this->removeAccents(trim($data["nombre"])),
					"UnidadCodigo" => $data["codigo_unidad_medida"],
					"Cantidad" => (float) $data["cantidad"],
					"ValorUnitario" => (float) $data["costo_unitario"],
					"Descuento" => (int) $totalDiscount,
					"Cargos" => 0,
					"SubTotal" => $subtotalItemMinusDiscount,
					"Total" => $totalItem,
					"codigo" => $data["codigo"],
					"Impuestos" => [
						array(
							"Base"                => $subtotalItemMinusDiscount,
							"CodigoImpuesto"      => "01",
							"Nombre"              => "IVA",
							"Porcentaje"          => 0.00,
							"Impuesto"            => 0.00,
							"PorcentajeRetencion" => 0.0
						)
					],
					"Descripcion" => [
						[
							"Nombre" => "Observacion",
							"Valor" => $this->removeAccents($data["observaciones"])
						]
					],
					"AllowanceCharge" => null,
					"PricingReference" => null,
					"Fecha" => $data["fecha_inicio"],
					"FormaGeneracion" => 1,
					"ValorDebito" => 0,
					"ValorCredito" => 0,
					"Consecutivo" => null,
					"Base_Impuesto" => 0,
					// "Base_Impuesto" => $this->roundValues($subtotalItemMinusDiscount),
					//"Codigo_Impuesto" => $codeTax,
					"Codigo_Impuesto" => null,
					//"Nombre_Impuesto" => $nameTax,
					"Nombre_Impuesto" => null,
					//"Porcentaje_Impuesto" => $percentajeTax,
					"Porcentaje_Impuesto" => 0,
					//"Valor_Impuesto" => $this->roundValues($valueTax)
					"Valor_Impuesto" => 0
				);

				$subtotalDetailsFinal += $this->roundValues($subtotalItemMinusDiscount);
				$discountDetailsFinal += $totalDiscount;
				$taxDetailsFinal      += $this->roundValues($valueTax);
				$quantityDetailsFinal += $data["cantidad"];

				$this->totalTaxes[$codeTax][$percentajeTax]["base"] += $this->roundValues($subtotalItemMinusDiscount);
				$this->totalTaxes[$codeTax][$percentajeTax]["tax"] += $this->roundValues($valueTax);
			}
			
			$this->jsonStructure["Detalles"] = $productList;

			$this->totalsDocument["subtotal"] = $subtotalDetailsFinal;
			$this->totalsDocument["discount"] = $discountDetailsFinal;
			$this->totalsDocument["tax"]      = $taxDetailsFinal;
			$this->totalsDocument["quantity"] = $quantityDetailsFinal;

			return $totalTaxes;
		}

		/**
		 * getInvoiceRetentions obtain invoice retentions
		 */
		public function getInvoiceRetentions(){
			$sql = "SELECT
						CFR.valor,
						CFR.base,
						CFR.retencion,
						CFR.tipo_retencion
					FROM
						compras_facturas_retenciones AS CFR
					LEFT JOIN
						compras_facturas AS CF
					ON
						CFR.id_factura_compra = CF.id
					WHERE
						CFR.activo = 1
					AND
						CFR.id_factura_compra = $this->documentId";

			$query = $this->mysql->query($sql,$this->mysql->link);
			
			while($row = mysql_fetch_assoc($query)){
				$response[] = $row;
			}

			return $response;
		}

		/**
		 * setInvoiceRetentions transmit invoice retentions
		 */
		public function setInvoiceRetentions($array){
			//var_dump($array);
			if($array){
				foreach($array as $data){
					$dataBase = (float) $data["base"];
					$dataTax = (float) $data["tax"];

					if($data["tipo_retencion"] == "ReteIva"){
						if($this->totalsDocument["tax"] > $dataBase){
							$retentionBase = $this->totalsDocument["tax"];
							$retentionCode = "05";
						}
						else{
							continue;
						}
					}
					else{
						if($this->totalsDocument["subtotal"] > $dataBase){
							$retentionBase = $this->totalsDocument["subtotal"];
							$retentionCode = ($data["tipo_retencion"] == "ReteFuente")? "06" : "07";
						}
						else{
							continue;
						}
					}

					$retentionName = $data["retencion"];
					$retentionPercentage = ($retentionBase > 0) ? $data["valor"] : 0;
					$retentionValue = $retentionBase * $data["valor"] / 100;

					$this->jsonStructure["TotalImpuestos"][] = [
						"Base"                => $retentionBase,
						"CodigoImpuesto"      => $retentionCode,
						"Nombre"              => $retentionName,
						"Descripcion"         => null,
						"Porcentaje"          => $retentionPercentage,
						"Impuesto"            => $retentionValue,
						"PorcentajeRetencion" => 0
					];
					
					$this->totalsDocument["retention"] += $retentionValue;
				}
			}
		}

		/**
		 * setTotals obtain invoice's totals
		 */
		public function setTotals(){
			// $finalTotalDocument = $this->totalsDocument["subtotal"] - $this->totalsDocument["discount"] + $this->totalsDocument["tax"];
			$finalTotalDocument = $this->totalsDocument["subtotal"];

			$this->jsonStructure["Totales"]=[
				"Total"                       => $finalTotalDocument,
				"TotalEnLetras"               => $this->convertNumberToWords($finalTotalDocument),
				// "TotalEnLetrasSinRetencion"   => $this->convertNumberToWords($finalTotalDocument),
				"SubTotal"                    => $this->totalsDocument["subtotal"],
				"Cargos"                      => 0,
				// "Descuentos"                  => $this->totalsDocument["discount"],
				"Descuentos"                  => 0,
				"SubTotalSinCargosDescuentos" => $this->totalsDocument["subtotal"],
				"IVA"                         => 0,
				// "TotalCantidad"               => 0,
				// "TotalConRetencion"           => $this->totalsDocument["subtotal"],
			];
		}

		/**
		 * setTotalTaxes obtain invoice's total taxes
		 */
		public function setTotalTaxes(){
			// foreach($this->totalTaxes as $codeTax => $percentage){
				// foreach($percentage as $data => $value){
					$this->jsonStructure["TotalImpuestos"][]=[
						"Base"                => $this->totalsDocument["subtotal"],
						"CodigoImpuesto"      => "01",
						"Nombre"              => "IVA",
						"Porcentaje"          => 0,
						"Impuesto"            => 0,
						"PorcentajeRetencion" => 0
					];
				// }
			// }
		}

		/**
		 * setFinalData obtain the final elements for build the json
		 */
		public function setFinalData($array){
			$this->jsonStructure["DetallesComprobante"] = [
				array(
					"Nombre" => "Fecha Vencimiento",
					"Valor"  => $this->fecha_vencimiento
				)
			];
			$this->jsonStructure["cufe"] = null;
			$this->jsonStructure["FirmaDigital"] = null;
			$this->jsonStructure["Credenciales"] = [
				"ClientToken" => $array["client_token"],
				"AccessToken" => $array["access_token"]
			];
			$this->jsonStructure["AllowanceCharge"] =[];
			$this->jsonStructure["PaymentExchangeRate"] =null;
			$this->jsonStructure["TerminosPago"] =[
				"Codigo" => "2",
				"UnidadCodigo" => "DAY",
				"Duracion" => 1
			];
		}

		/**
		 * removeAccents take accents off from text
		 */
		public function removeAccents($string){
			$specialCharacters = array("\t","\r","\n",chr(160));
			$originals  = 'ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝÞßàáâãäåæçèéêëìíîïðñòóôõöøùúûýýþÿ°ª&º/';
			$modified = 'AAAAAAACEEEEIIIIDNOOOOOOUUUUYbsaaaaaaaceeeeiiiidnoooooouuuyybyoayo/';
			$string = strtr($string, utf8_decode($originals), $modified);
			$string = str_replace($specialCharacters,"",$string);
			
			return utf8_encode($string);
		}

		public function utf8_encode_recursive($mixed) {
			if (is_array($mixed)) {
				foreach ($mixed as &$valor) {
					$valor = $this->utf8_encode_recursive($valor);
				}
			} elseif (is_string($mixed)) {
				$mixed = utf8_encode($mixed);
			}
			return $mixed;
		}
		/**
		 * printJson show json in the screen
		 */
		public function printJson($debug = false){
			// echo "<pre>".json_encode($this->jsonStructure)."</pre>";
			$this->jsonStructure = $this->utf8_encode_recursive($this->jsonStructure);
			if($debug){

				$json_final =  json_encode($this->jsonStructure, JSON_PRETTY_PRINT);
				if(json_last_error_msg() == "No error"){
					return $json_final;
				}

				else{
					return print("<pre>".print_r($this->jsonStructure,true)."</pre>"."<pre>".json_last_error_msg()."</pre>");
					#return json_encode(array("Hola"=>"Mundo"), JSON_PRETTY_PRINT);
				}
								
			}

			else {
			return json_encode($this->jsonStructure, JSON_PRETTY_PRINT);}
		}

		/**
		 * roundValues To round a specific value from a variable global
		 */
		public function roundValues($value){
			return ROUND($value,$this->currencyDecimals);
		}

		/**
		 * convertNumberToWords receives the number that it will convert
		 */
		public function convertNumberToWords($num,$fem = false,$dec = false){
			$float = explode('.',$num);
			$num   = $float[0];
			$num2  = $float[1];

			$end_num  = $this->operateNumberToWords($num, $fem = false, $dec = false);
			$end_num2 = $this->operateNumberToWords($num2, $fem = false, $dec = false);
			if($end_num2 <> ''){
				return $end_num . ' ' . $_SESSION['DESCRIMONEDA'] . ' con ' . $end_num2 . ' centavos';
			}
			else{
				return $end_num . ' ' . $_SESSION['DESCRIMONEDA'];
			}
		}

		/**
		 * operateNumberToWords change numbers for text
		 */
		public function operateNumberToWords($num,$fem = false,$dec = false){
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

		  	$num = trim((string)@$num);

			if($num[0] == '-'){
				$neg = 'menos ';
				$num = substr($num, 1);
			}
			else{
				$neg = '';
				while($num[0] == '0') $num = substr($num, 1);
				if ($num[0] < '1' or $num[0] > 9) $num = '0' . $num;
				$zeros = true;
				$punt = false;
				$ent = '';
				$fra = '';

				for($c = 0; $c < strlen($num); $c++){
					$n = $num[$c];

					if(!(strpos(".,'''", $n) === false)){
						if($punt){
							break;
						}
						else{
							$punt = true;
							continue;
						}
					}
					elseif(!(strpos('0123456789', $n) === false)){
						if($punt){
							if($n != '0'){
								$zeros = false;
								$fra .= $n;
							}
						}
						else{
							$ent .= $n;
						}
					}
					else{
						break;
					}
				}

				$ent = '     ' . $ent;
				if($dec and $fra and ! $zeros){
					$fin = ' coma';
					for($n = 0; $n < strlen($fra); $n++){
						if(($s = $fra[$n]) == '0'){
							$fin .= ' cero';
						}
		        		elseif($s == '1'){
							$fin .= $fem ? ' una' : ' un';
						}
		        		else{
		          			$fin .= ' ' . $matuni[$s];
						}
		      		}
		    	}
				else{
					$fin = '';
				}

				if((int)$ent === 0){ return 'Cero' . $fin; }
				$tex = '';
				$sub = 0;
				$mils = 0;
				$neutro = false;

		    	while(($num = substr($ent, -3)) != '   '){
					$ent = substr($ent, 0, -3);

					if(++$sub < 3 and $fem){
						$matuni[1] = 'una';
						$subcent = 'as';
					}
					else{
						$matuni[1] = $neutro ? 'un' : 'uno';
						$subcent = 'os';
					}

					$t = '';
					$n2 = substr($num, 1);
					if($n2 == '00'){
					}
					elseif ($n2 < 21){
						$t = ' ' . $matuni[(int)$n2];
					}
					elseif($n2 < 30){
						$n3 = $num[2];
						if($n3 != 0){ $t = 'i' . $matuni[$n3]; }
						$n2 = $num[1];
						$t = ' ' . $matdec[$n2] . $t;
					}
					else{
						$n3 = $num[2];
						if($n3 != 0){ $t = ' y ' . $matuni[$n3]; }
						$n2 = $num[1];
						$t = ' ' . $matdec[$n2] . $t;
					}

					$n = $num[0];
					if($n == 1){
						$t = ' ciento' . $t;
					}
					elseif($n == 5){
						$t = ' ' . $matunisub[$n] . 'ient' . $subcent . $t;
					}
					elseif($n != 0){
						$t = ' ' . $matunisub[$n] . 'cient' . $subcent . $t;
					}

					if($sub == 1){
					}
					elseif(!isset($matsub[$sub])){
						if($num == 1){
							$t = ' mil';
						}
						elseif($num > 1){
							$t .= ' mil';
						}
					}
					elseif($num == 1){
						$t .= ' ' . $matsub[$sub] . 'on';
					}
					elseif($num > 1){
						$t .= ' ' . $matsub[$sub] . 'ones';
					}

					if($num == '000'){
						$mils ++;
					}
					elseif($mils != 0){
						if(isset($matmil[$sub])){ $t .= ' ' . $matmil[$sub]; }
						$mils = 0;
					}
					$neutro = true;
					$tex = $t . $tex;
		    	}

				$tex = $neg . substr($tex, 1) . $fin;

				$end_num = ucfirst($tex);
				return $end_num;
			}
	  	}

		/**
		 * sendInvoice	
		 */
	  	public function sendInvoice($debug = false){

	  		//OBTAIN INFO ABOUT THE INVOICE
			$headerInvoice          = $this->getDocumentInfo();
			$this->supplierId       = $headerInvoice["id_proveedor"];
			$transmitter            = $this->getTransmitter();
			$supplier               = $this->getSupplier();
			$supplierAdditionalInfo = $this->getSupplierAdditionalInfo();
			$detailsInvoice         = $this->getInvoiceDetails();
			$retentions             = $this->getInvoiceRetentions();

			//BUILD MAIN ARRAY WITH THE INFO
			$this->setStructureHeader($headerInvoice);
			$this->setTransmitter($transmitter);
			$this->setSupplier($supplier);
			$this->setSupplierAdditionalInfo($supplierAdditionalInfo);
			$this->setInvoiceDetails($detailsInvoice);
			$this->setTotals();
			$this->setTotalTaxes();
			// $this->setInvoiceRetentions($retentions);
			$this->setFinalData($transmitter);
			if($debug){
				return $this->printJson($debug);
			}
			else{
				//send invoice here
				$response['json'] = $this->printJson();
				$response['result'] = str_replace("'","",$this->sendJson());
				return $response;
			}
	  	}

		public function sendJson(){
			// API para enviar el JSON a la DIAN
			$url_api = "https://web.facse.net:444/api/soporte/documento";

			// Creamos los parametros para consumir la API
			$params                   = [];
			$params['request_url']    = $url_api;
			$params['request_method'] = "POST";
			$params['Authorization']  = "";
			$params['data']           = json_encode($this->jsonStructure);

			// Consumimos el API y obtenemos sus resultados
			$response = $this->curlApi($params);
			$response = json_decode($response,true);

			// Para mejorar la robustez, si hay un error, se intenta hacer un segundo envio
			if(strpos($data["result"],"Procesado Correctamente") == false 
			&& strpos($data["result"],"Documento no enviado, Ya cuenta con env") == false 
			&& strpos($data["result"],"procesado anteriormente") == false 
			&& strpos($data["result"],"ha sido autorizada") == false){
				$response = $this->curlApi($params);
				$response = json_decode($response,true);
				return $response["respuesta"];
			}
			
			return $response["respuesta"];
		}

		public function curlApi($params){
			$client = curl_init();
			$options = array(
				CURLOPT_HTTPHEADER     => array('Content-Type: application/json',"$params[Authorization]"),
				CURLOPT_URL            => "$params[request_url]",
				CURLOPT_CUSTOMREQUEST  => "$params[request_method]",
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_POSTFIELDS     => $params['data'],
				CURLOPT_SSL_VERIFYPEER => false
			);
			curl_setopt_array($client,$options);
			$response    = curl_exec($client);
			$curl_errors = curl_error($client);

			if(!empty($curl_errors)){
				$response['status']               = 'failed';
				$response['errors'][0]['titulo']  = curl_getinfo($client);
				$response['errors'][0]['detalle'] = curl_error($client);
			}

			$httpCode = curl_getinfo($client, CURLINFO_HTTP_CODE);
			curl_close($client);
			return $response;
		}
	}
?>