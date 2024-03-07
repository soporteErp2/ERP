<?php

	include_once("../../../../../configuracion/conectar.php");
	include_once("../../../../../configuracion/define_variables.php");

	include_once("../../../../compras/facturacion/bd/ClassSupportDocument.php");


    class ClassSupportDocumentReturn extends ClassSupportDocument{

        // public $documentId;
		// public $mysql;

		public function __construct($documentId,$mysql){
			//INICIALIZAMOS EL CONSTRUCTOR PADRE PARA EL ID DEL DOCUMENTO Y LA CONEXION MYSQL
			parent::__construct($documentId,$mysql);
		}

		public function setStructureHeaderReturn($array){
			$this->fecha_vencimiento = $array['fecha_inicio'];
			$this->nombre_sucursal = $array['sucursal'];

			if(strlen($this->branchOfficeId) == 1){
				$this->idES = "0" . $this->branchOfficeId;
			}
			else{
				$this->idES = $this->branchOfficeId;
			}

			$this->jsonStructure["Comprobante"]=[
				"OrigenDocumento" => "",
				"TipoComprobante" => "95",
				"Fecha"           => "$array[fecha_finalizacion] $array[hora_finalizacion]",
				"Prefijo"         => "DC$this->idES",
				"Numero"          => "$array[consecutivo]",
				"Moneda"          => "COP",
				"Referencia"      => str_replace(" ", "", $array["numero_documento_compra"]),
				"ConceptoRef"     => "$array[id_motivo_dian]",
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

		public function getDocumentReturnInfo(){
			$sql = "SELECT
						DC.id,
						DC.fecha_finalizacion,
						DC.hora_finalizacion,
						DC.consecutivo,
						DC.nit,
						DC.id_proveedor,
						DC.observacion,
						DC.id_metodo_pago,
						DC.metodo_pago,
						DC.id_sucursal,
						DC.sucursal,
						DC.numero_documento_compra,
						DC.id_motivo_dian,
						DC.descripcion_motivo_dian,
						DC.id_documento_compra,
						DC.numero_documento_compra,
						CMP.codigo_metodo_pago_dian,
						CMP.nombre AS nombre_metodo_pago_dian,
						CCP.estado
					FROM
						devoluciones_compra AS DC
						LEFT JOIN configuracion_metodos_pago AS CMP ON DC.id_metodo_pago = CMP.id
						LEFT JOIN compras_facturas AS CF ON DC.id_documento_compra = CF.id
						LEFT JOIN configuracion_cuentas_pago AS CCP ON CF.id_configuracion_cuenta_pago = CCP.id 
					WHERE
						DC.activo = 1 
						AND DC.estado = 1 
						AND DC.id_empresa = $this->companyId
						AND DC.id = $this->documentId";
			
			$query = $this->mysql->query($sql,$this->mysql->link);
			return mysql_fetch_assoc($query);
		}

		public function getDocumentReturnDetails(){
			$sql = "SELECT
						DCI.codigo,
						SUM(DCI.cantidad) AS cantidad,
						DCI.nombre,
						DCI.costo_unitario,
						DCI.observaciones,
						DCI.tipo_descuento,
						DCI.descuento,
						DCI.impuesto,
						DCI.valor_impuesto,
						I.codigo_impuesto_dian,
						IU.codigo_dian AS codigo_unidad_medida
					FROM
						devoluciones_compra_inventario AS DCI
					LEFT JOIN
						devoluciones_compra AS DC
					ON
						DCI.id_devolucion_compra = DC.id
					LEFT JOIN
						impuestos AS I
					ON
						I.id = DCI.id_impuesto
					LEFT JOIN
						inventario_unidades AS IU
					ON
						DCI.id_unidad_medida = IU.id
					WHERE
						DCI.activo = 1
					AND
						DCI.id_devolucion_compra = $this->documentId
					AND
						DC.id_empresa = $this->companyId
					GROUP BY
						DCI.codigo,DCI.costo_unitario,DCI.tipo_descuento,DCI.descuento,DCI.observaciones";
			
			$query = $this->mysql->query($sql,$this->mysql->link);
			
			while($row = mysql_fetch_assoc($query)){
				$response[] = $row;
			}

			return $response;
		}

		public function sendDocumentReturn($debug = false){

			//OBTAIN INFO ABOUT THE RETURN DOCUMENT
			$headerDocumentReturn   = $this->getDocumentReturnInfo();
			$this->supplierId       = $headerDocumentReturn["id_proveedor"];
			$transmitter            = $this->getTransmitter();
			$supplier               = $this->getSupplier();
			$supplierAdditionalInfo = $this->getSupplierAdditionalInfo();
			$detailsDocument        = $this->getDocumentReturnDetails();
			$retentions             = $this->getInvoiceRetentions();

			//BUILD MAIN ARRAY WITH THE INFO
			$this->setStructureHeaderReturn($headerDocumentReturn);
			$this->setTransmitter($transmitter);
			$this->setSupplier($supplier);
			$this->setSupplierAdditionalInfo($supplierAdditionalInfo);
			$this->setInvoiceDetails($detailsDocument);
			$this->setTotals();
			$this->setTotalTaxes();
			$this->setInvoiceRetentions($retentions);
			$this->setFinalData($transmitter);
			// if($debug){
				return $this->printJson($debug);
			// }
			// else{
				// //send invoice here
				// $response['json'] = $this->printJson();
				// $response['result'] = str_replace("'","",$this->sendJson());
				// return $response;
			// }
		}

    }

	// $SupportDocumentReturn = new ClassSupportDocumentReturn(22,$mysql);
	// echo $testSql = $SupportDocumentReturn->sendDocumentReturn(true);
	

?>