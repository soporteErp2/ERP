<?php
/**
*@class ClassFacturaVenta
*/
class ClassFacturaVenta{
	public $mysql;

	function __construct($mysql){
		$this->mysql = $mysql;
	}

	public function createXML($codigoFactura){

		//DATOS DE LA CABECERA DE LA FACTURA
		$sqlVentasFacturas = "SELECT
														ventas_facturas.id,
														ventas_facturas.prefijo,
														ventas_facturas.numero_factura,
														ventas_facturas.numero_factura_completo,
														ventas_facturas.nit,
														ventas_facturas.sucursal_cliente,
														ventas_facturas.fecha_inicio,
														ventas_facturas.hora_inicio,
														ventas_facturas.total_factura,
														ventas_facturas_configuracion.prefijo,
														ventas_facturas_configuracion.consecutivo_resolucion,
														ventas_facturas_configuracion.fecha_resolucion AS fecha_resolucion_inicio,
														DATE_ADD(fecha_resolucion,INTERVAL 2 YEAR) AS fecha_resolucion_fin,
														ventas_facturas_configuracion.numero_inicial_resolucion,
														ventas_facturas_configuracion.numero_final_resolucion
													FROM
														ventas_facturas
													INNER JOIN
														ventas_facturas_configuracion
													ON
														ventas_facturas.id_configuracion_resolucion = ventas_facturas_configuracion.id
													WHERE
														ventas_facturas.activo = 1
													AND
														ventas_facturas.estado = 1
													AND
														ventas_facturas.id = $codigoFactura";

		$queryVentasFacturas 					= $this->mysql->query($sqlVentasFacturas,$this->mysql->link);
		$idVF   											= $this->mysql->result($queryVentasFacturas,0,'id');
		$prefijoVF                   	= $this->mysql->result($queryVentasFacturas,0,'prefijo');
		$numero_facturaVF 	 					= $this->mysql->result($queryVentasFacturas,0,'numero_factura');
		$numero_factura_completoVF		= $this->mysql->result($queryVentasFacturas,0,'numero_factura_completo');
		$nitVF 												= $this->mysql->result($queryVentasFacturas,0,'nit');
		$sucursal_clienteVF 					= $this->mysql->result($queryVentasFacturas,0,'sucursal_cliente');
		$fecha_inicioVF      					= $this->mysql->result($queryVentasFacturas,0,'fecha_inicio');
		$hora_inicioVF       					= $this->mysql->result($queryVentasFacturas,0,'hora_inicio');
		$total_facturaVF							= $this->mysql->result($queryVentasFacturas,0,'total_factura');
		$prefijoVF1										= $this->mysql->result($queryVentasFacturas,0,'prefijo1');
		$consecutivo_resolucionVF    	= $this->mysql->result($queryVentasFacturas,0,'consecutivo_resolucion');
		$fecha_resolucion_inicioVF   	= $this->mysql->result($queryVentasFacturas,0,'fecha_resolucion_inicio');
		$fecha_resolucion_finVF      	= $this->mysql->result($queryVentasFacturas,0,'fecha_resolucion_fin');
		$numero_inicial_resolucionVF 	= $this->mysql->result($queryVentasFacturas,0,'numero_inicial_resolucion');
		$numero_final_resolucionVF   	= $this->mysql->result($queryVentasFacturas,0,'numero_final_resolucion');

		//DATOS DEL TERCERO O CLIENTE
		$sqlTerceros = "SELECT
											terceros.id_tipo_persona_dian,
											terceros.numero_identificacion,
											terceros.nombre,
											terceros.departamento,
											terceros.ciudad,
											terceros.direccion,
											terceros.iso2 AS pais,
											terceros_tributario.codigo_regimen_dian,
											tipo_documento.codigo_tipo_documento_dian
										FROM
											terceros
										INNER JOIN
											terceros_tributario
										ON
											terceros.id_tercero_tributario = terceros_tributario.id
										LEFT JOIN
											tipo_documento
										ON
											terceros.id_tipo_identificacion = tipo_documento.id
										WHERE
											terceros.activo = 1
										AND
											terceros.numero_identificacion = $nitVF
										AND
											terceros.id_empresa = 48";

		$queryTerceros           			= $this->mysql->query($sqlTerceros,$this->mysql->link);
		$id_tipo_persona_dianT   			= $this->mysql->result($queryTerceros,0,'id_tipo_persona_dian');
		$numero_identificacionT  			= $this->mysql->result($queryTerceros,0,'numero_identificacion');
		$nombreT											= $this->mysql->result($queryTerceros,0,'nombre');
		$departamentoT           			= $this->mysql->result($queryTerceros,0,'departamento');
		$ciudadT                 			= $this->mysql->result($queryTerceros,0,'ciudad');
		$direccionT              			= $this->mysql->result($queryTerceros,0,'direccion');
		$paisT                   			= $this->mysql->result($queryTerceros,0,'pais');
		$codigo_regimen_dianT    			= $this->mysql->result($queryTerceros,0,'codigo_regimen_dian');
		$codigo_tipo_documento_dianT 	= $this->mysql->result($queryTerceros,0,'codigo_tipo_documento_dian');

		//DATOS DE LAS RETENCIONES
		$sqlVentasFacturasRetenciones =  "SELECT
																				ventas_facturas_retenciones.valor,
																				ventas_facturas_retenciones.base,
																				ventas_facturas_retenciones.tipo_retencion
																			FROM
																				ventas_facturas_retenciones
																			INNER JOIN
																				ventas_facturas
																			ON
																				ventas_facturas_retenciones.id_factura_venta=ventas_facturas.id
																			WHERE
																				ventas_facturas_retenciones.activo = 1
																			AND
																				ventas_facturas_retenciones.id_factura_venta = $idVF";

    $queryVentasFacturasRetenciones = $this->mysql->query($sqlVentasFacturasRetenciones,$this->mysql->link);
		$contRetenciones = $this->mysql->num_rows($queryVentasFacturasRetenciones);
		for($i = 0; $i < $contRetenciones; $i++){
			$valorVFR[$i] 					= $this->mysql->result($queryVentasFacturasRetenciones,$i,'valor');
			$baseVFR[$i]  					= $this->mysql->result($queryVentasFacturasRetenciones,$i,'base');
			$tipo_retencionVFR[$i]  = $this->mysql->result($queryVentasFacturasRetenciones,$i,'tipo_retencion');
		}

		//DATOS DE lOS ARTICULOS
		$sqlVentasFacturasInventario = "SELECT
																			ventas_facturas_inventario.cantidad,
																			ventas_facturas_inventario.nombre,
																			ventas_facturas_inventario.costo_unitario,
																			ventas_facturas_inventario.tipo_descuento,
																			ventas_facturas_inventario.descuento,
																			ventas_facturas_inventario.valor_impuesto,
																			impuestos.codigo_impuesto_dian
																		FROM
																			ventas_facturas_inventario
																		INNER JOIN
																			ventas_facturas
																		ON
																			ventas_facturas_inventario.id_factura_venta = ventas_facturas.id
																		LEFT JOIN
																			impuestos
																		ON
																			impuestos.id = ventas_facturas_inventario.id_impuesto
																		WHERE
																			ventas_facturas_inventario.activo = 1
																		AND
																			ventas_facturas_inventario.id_factura_venta = $idVF";

		$queryVentasFacturasInventario = $this->mysql->query($sqlVentasFacturasInventario,$this->mysql->link);
		//Contamos el numero de articulos que posee la factura
		$contArticulos = $this->mysql->num_rows($queryVentasFacturasInventario);
		for($i = 0; $i < $contArticulos; $i++){
			$cantidadVFI[$i] 					= $this->mysql->result($queryVentasFacturasInventario,$i,'cantidad');
			$nombreVFI[$i] 						= $this->mysql->result($queryVentasFacturasInventario,$i,'nombre');
			$costo_unitarioVFI[$i] 		= $this->mysql->result($queryVentasFacturasInventario,$i,'costo_unitario');
			$tipo_descuentoVFI[$i] 		= $this->mysql->result($queryVentasFacturasInventario,$i,'tipo_descuento');
			$descuentoVFI[$i] 		 		= $this->mysql->result($queryVentasFacturasInventario,$i,'descuento');
			$valor_impuestoVFI[$i] 		= $this->mysql->result($queryVentasFacturasInventario,$i,'valor_impuesto');
			$codigo_impuesto_dian[$i] = $this->mysql->result($queryVentasFacturasInventario,$i,'codigo_impuesto_dian');
		}
		//Buscamos primero si el articulo tiene o no descuento
		for($i = 0; $i < $contArticulos; $i++){
			if($descuentoVFI[$i] != 0){
				if($tipo_descuentoVFI[$i] == "porcentaje"){
					$costo_finalVFI[$i] = ($cantidadVFI[$i] * $costo_unitarioVFI[$i]) - ($cantidadVFI[$i] * $costo_unitarioVFI[$i] * $descuentoVFI[$i] / 100);
				}else if($tipo_descuentoVFI[$i] == "pesos"){
					$costo_finalVFI[$i] = ($cantidadVFI[$i] * $costo_unitarioVFI[$i]) - $descuentoVFI[$i];
				}
			}else{
				$costo_finalVFI[$i] = $cantidadVFI[$i] * $costo_unitarioVFI[$i];
			}
			//Calculamos el valor total de los impuestos
			$costo_impuestoVFI[$i] = $costo_finalVFI[$i] * $valor_impuestoVFI[$i] / 100;
		}

		//COMIENZO DE CONSTRUCCION DEL XML
		$xml = new DomDocument('1.0', 'UTF-8');
		$xml->xmlStandalone = 'no';

		$feInvoice = $xml->createElement('fe:Invoice');
		$feInvoice = $xml->appendChild($feInvoice);
		$feInvoice->setAttribute('xmlns:fe','http://www.dian.gov.co/contratos/facturaelectronica/v1');
		$feInvoice->setAttribute('xmlns:cac','urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2');
		$feInvoice->setAttribute('xmlns:cbc','urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2');
		$feInvoice->setAttribute('xmlns:clm54217','urn:un:unece:uncefact:codelist:specification:54217:2001');
		$feInvoice->setAttribute('xmlns:clm66411','urn:un:unece:uncefact:codelist:specification:66411:2001');
		$feInvoice->setAttribute('xmlns:clmIANAMIMEMediaType','urn:un:unece:uncefact:codelist:specification:IANAMIMEMediaType:2003');
		$feInvoice->setAttribute('xmlns:ext','urn:oasis:names:specification:ubl:schema:xsd:CommonExtensionComponents-2');
		$feInvoice->setAttribute('xmlns:qdt','urn:oasis:names:specification:ubl:schema:xsd:QualifiedDatatypes-2');
		$feInvoice->setAttribute('xmlns:sts','http://www.dian.gov.co/contratos/facturaelectronica/v1/Structures');
		$feInvoice->setAttribute('xmlns:udt','urn:un:unece:uncefact:data:specification:UnqualifiedDataTypesSchemaModule:2');
		$feInvoice->setAttribute('xmlns:xsi','http://www.w3.org/2001/XMLSchema-instance');
		$feInvoice->setAttribute('xsi:schemaLocation','http://www.dian.gov.co/contratos/facturaelectronica/v1 ../xsd/DIAN_UBL.xsd urn:un:unece:uncefact:data:specification:UnqualifiedDataTypesSchemaModule:2 ../../ubl2/common/UnqualifiedDataTypeSchemaModule-2.0.xsd urn:oasis:names:specification:ubl:schema:xsd:QualifiedDatatypes-2 ../../ubl2/common/UBL-QualifiedDatatypes2.0.xsd');

    $extUBLExtensions = $xml->createElement('ext:UBLExtensions');
		$extUBLExtensions = $feInvoice->appendChild($extUBLExtensions);

		$extUBLExtension = $xml->createElement('ext:UBLExtension');
		$extUBLExtension = $extUBLExtensions->appendChild($extUBLExtension);

		$extExtensionContent = $xml->createElement('ext:ExtensionContent');
		$extExtensionContent = $extUBLExtension->appendChild($extExtensionContent);

		$stsDianExtensions = $xml->createElement('sts:DianExtensions');
		$stsDianExtensions = $extExtensionContent->appendChild($stsDianExtensions);

		$stsInvoiceControl = $xml->createElement('sts:InvoiceControl');
		$stsInvoiceControl = $stsDianExtensions->appendChild($stsInvoiceControl);

		$stsInvoiceAuthorization = $xml->createElement('sts:InvoiceAuthorization',$consecutivo_resolucionVF);
		$stsInvoiceAuthorization = $stsInvoiceControl->appendChild($stsInvoiceAuthorization);

		$stsAuthorizationPeriod = $xml->createElement('sts:AuthorizationPeriod');
		$stsAuthorizationPeriod = $stsInvoiceControl->appendChild($stsAuthorizationPeriod);

		$cbcStartDate = $xml->createElement('cbc:StartDate',$fecha_resolucion_inicioVF);
		$cbcStartDate = $stsAuthorizationPeriod->appendChild($cbcStartDate);

		$cbcEndDate = $xml->createElement('cbc:EndDate',$fecha_resolucion_finVF);
		$cbcEndDate = $stsAuthorizationPeriod->appendChild($cbcEndDate);

		$stsAuthorizedInvoices = $xml->createElement('sts:AuthorizedInvoices');
		$stsAuthorizedInvoices = $stsInvoiceControl->appendChild($stsAuthorizedInvoices);

		$stsPrefix = $xml->createElement('sts:Prefix',$prefijoVF1);
		$stsPrefix = $stsAuthorizedInvoices->appendChild($stsPrefix);

		$stsFrom = $xml->createElement('stsFrom',$numero_inicial_resolucionVF);
		$stsFrom = $stsAuthorizedInvoices->appendChild($stsFrom);

		$stsTo = $xml->createElement('sts:To',$numero_final_resolucionVF);
		$stsTo = $stsAuthorizedInvoices->appendChild($stsTo);

		$stsInvoiceSource = $xml->createElement('sts:InvoiceSource');
		$stsInvoiceSource = $stsDianExtensions->appendChild($stsInvoiceSource);

		$cbcIdentificationCode = $xml->createElement('cbc:IdentificationCode','CO');
		$cbcIdentificationCode = $stsInvoiceSource->appendChild($cbcIdentificationCode);
		$cbcIdentificationCode->setAttribute('listAgencyID','6');
		$cbcIdentificationCode->setAttribute('listAgencyName','United Nations Economic Commission for Europe');
		$cbcIdentificationCode->setAttribute('listSchemeURI','urn:oasis:names:specification:ubl:codelist:gc:CountryIdentificationCode2.0');

		$stsSoftwareProvider = $xml->createElement('sts:SoftwareProvider');
		$stsSoftwareProvider = $stsDianExtensions->appendChild($stsSoftwareProvider);

		$stsProviderID = $xml->createElement('sts:ProviderID','900467785');
		$stsProviderID = $stsSoftwareProvider->appendChild($stsProviderID);
		$stsProviderID->setAttribute('schemeAgencyID','195');
		$stsProviderID->setAttribute('schemeAgencyName','CO, DIAN (Direccion de Impuestos y Aduanas Nacionales)');

		$stsSoftwareID = $xml->createElement('sts:SoftwareID','69d4f0b6-4924-4479-bdc6-26ef726eaee3');
		$stsSoftwareID = $stsSoftwareProvider->appendChild($stsSoftwareID);
		$stsSoftwareID->setAttribute('schemeAgencyID','195');
		$stsSoftwareID->setAttribute('schemeAgencyName','CO, DIAN (Direccion de Impuestos y Aduanas Nacionales)');

		$stsSoftwareSecurityCode = $xml->createElement('sts:SoftwareSecurityCode','db5af267-2d41-4ccc-9e6b-9ee251f26483');
		$stsSoftwareSecurityCode = $stsDianExtensions->appendChild($stsSoftwareSecurityCode);
		$stsSoftwareSecurityCode->setAttribute('schemeAgencyID','195');
		$stsSoftwareSecurityCode->setAttribute('schemeAgencyName','CO, DIAN (Direccion de Impuestos y Aduanas Nacionales)');

		$extUBLExtension1 = $xml->createElement('ext:UBLExtension');
		$extUBLExtension1 = $extUBLExtensions->appendChild($extUBLExtension1) ;

		$extUBLExtensionContent1 = $xml->createElement('ext:ExtensionContent');
		$extUBLExtensionContent1 = $extUBLExtension1->appendChild($extUBLExtensionContent1);

		//------------------------------------------//
		//-----------Comienzo De La Firma-----------//
		$dsSignature = $xml->createElement('ds:Signature');
		$dsSignature = $extUBLExtensionContent1->appendChild($dsSignature);
		$dsSignature->setAttribute('xmlns:ds','http://www.w3.org/2000/09/xmldsig#');

		$dsSignedInfo = $xml->createElement('ds:SignedInfo');
		$dsSignedInfo = $dsSignature->appendChild($dsSignedInfo);

		$dsCanonicalizationMethod = $xml->createElement('ds:CanonicalizationMethod');
		$dsCanonicalizationMethod = $dsSignedInfo->appendChild($dsCanonicalizationMethod);
		$dsCanonicalizationMethod->setAttribute('Algorithm','http://www.w3.org/TR/2001/REC-xml-c14n-20010315');

		$dsSignatureMethod = $xml->createElement('ds:SignatureMethod');
		$dsSignatureMethod = $dsSignedInfo->appendChild($dsSignatureMethod);
		$dsSignatureMethod->setAttribute('Algorithm','http://www.w3.org/2000/09/xmldsig#rsa-sha1');

		$dsReference = $xml->createElement('ds:Reference');
		$dsReference = $dsSignedInfo->appendChild($dsReference);
		$dsReference->setAttribute('Id','xmldsig-79c270e3-50bb-4fcf-b9bc-3a95bcf2466d-ref0');
		$dsReference->setAttribute('URI','');

		$dsTransforms = $xml->createElement('ds:Transforms');
		$dsTransforms = $dsReference->appendChild($dsTransforms);

		$dsTransform = $xml->createElement('ds:Transform');
		$dsTransform = $dsTransforms->appendChild($dsTransform);
		$dsTransform->setAttribute('Algorithm','http://www.w3.org/2000/09/xmldsig#enveloped-signature');

		$dsDigestMethod = $xml->createElement('ds:DigestMethod');
		$dsDigestMethod = $dsReference->appendChild($dsDigestMethod);
		$dsDigestMethod->setAttribute('Algorithm','http://www.w3.org/2000/09/xmldsig#sha1');

		//------------------------------------------//
		//---------------UBLVersionID---------------//
		$cbcUBLVersionID = $xml->createElement('cbc:UBLVersionID','UBL 2.0');
		$cbcUBLVersionID = $feInvoice->appendChild($cbcUBLVersionID);

		//------------------------------------------//
		//-----------------ProfileID----------------//
		$cbcProfileID = $xml->createElement('cbc:ProfileID','DIAN 1.0');
		$cbcProfileID = $feInvoice->appendChild($cbcProfileID);

		//------------------------------------------//
		//---------------------ID-------------------//
		$cbcID = $xml->createElement('cbc:ID',$numero_facturaVF);
		$cbcID = $feInvoice->appendChild($cbcID);

		//------------------------------------------//
		//----------------UUID o CUFE---------------//
		$fecFac = str_replace("-","",$fecha_inicioVF) . str_replace(":","",$hora_inicioVF);
		$valFac = array_sum($costo_finalVFI);
		for($i = 0; $i < $contArticulos; $i++){
			if($valor_impuestoVFI[$i] != NULL){
				$impuestos .= $codigo_impuesto_dian[$i] . $costo_impuestoVFI[$i];
			}
	  }
		for($i = 0; $i < $contRetenciones; $i++){
			if($tipo_retencionVFR[$i] != "AutoRetencion"){

				if(array_sum($costo_finalVFI) > $baseVFR[$i]){
					$costo_retencionVFR[$i] = array_sum($costo_finalVFI) * $valorVFR[$i] / 100;
				  if($tipo_retencionVFR[$i] == 'ReteIca'){
						$impuestos .= "03" . $costo_retencionVFR[$i];
					}else{
						$impuestos .= "" . $costo_retencionVFR[$i];
					}
				}
			}
	  }
		$vallmp = array_sum($costo_impuestoVFI);
		$cufe = sha1($numero_factura.$fecFac.$valFac.$impuestos.$vallmp."900467785".$codigo_tipo_documento_dianT.$id_tipo_persona_dianT."dd85db55545bd6566f36b0fd3be9fd8555c36e");
		$cbcUUID = $xml->createElement('cbc:UUID',$cufe);
		$cbcUUID = $feInvoice->appendChild($cbcUUID);
		$cbcUUID->setAttribute('schemeAgencyID','195');
		$cbcUUID->setAttribute('schemeAgencyName','CO, DIAN (Direccion de Impuestos y Aduanas Nacionales)');

		//------------------------------------------//
		//------------------IssueDate---------------//
		$cbcIssueDate = $xml->createElement('cbc:IssueDate',$fecha_inicioVF);
		$cbcIssueDate = $feInvoice->appendChild($cbcIssueDate);

		//------------------------------------------//
		//------------------IssueTime---------------//
		$cbcIssueTime = $xml->createElement('cbc:IssueTime',$hora_inicioVF);
		$cbcIssueTime = $feInvoice->appendChild($cbcIssueTime);

		//------------------------------------------//
		//--------------InvoiceTypeCode-------------//
		$cbcInvoiceTypeCode = $xml->createElement('cbc:InvoiceTypeCode','1');
		$cbcInvoiceTypeCode = $feInvoice->appendChild($cbcInvoiceTypeCode);
		$cbcInvoiceTypeCode->setAttribute('listAgencyID','195');
		$cbcInvoiceTypeCode->setAttribute('listAgencyName','CO, DIAN (Direccion de Impuestos y Aduanas Nacionales)');
		$cbcInvoiceTypeCode->setAttribute('listSchemeURI','http://www.dian.gov.co/contratos/facturaelectronica/v1/InvoiceType');

		//------------------------------------------//
		//-----------DocumentCurrencyCode-----------//
		$cbcDocumentCurrencyCode = $xml->createElement('cbc:DocumentCurrencyCode','COP');
		$cbcDocumentCurrencyCode = $feInvoice->appendChild($cbcDocumentCurrencyCode);

		//------------------------------------------//
		//-----Datos Del Facturador Electronico-----//
		$feAccountingSupplierParty = $xml->createElement('fe:AccountingSupplierParty');
		$feAccountingSupplierParty = $feInvoice->appendChild($feAccountingSupplierParty);

		$cbcAdditionalAccountID = $xml->createElement('cbc:AdditionalAccountID','1');
		$cbcAdditionalAccountID = $feAccountingSupplierParty->appendChild($cbcAdditionalAccountID);

		$feParty = $xml->createElement('fe:Party');
		$feParty = $feAccountingSupplierParty->appendChild($feParty);

		$cacPartyIdentification = $xml->createElement('cac:PartyIdentification');
		$cacPartyIdentification = $feParty->appendChild($cacPartyIdentification);

		$cbcID = $xml->createElement('cbc:ID','900467785');
		$cbcID = $cacPartyIdentification->appendChild($cbcID);
		$cbcID->setAttribute('schemeAgencyID','195');
		$cbcID->setAttribute('schemeAgencyName','CO, DIAN (Direccion de Impuestos y Aduanas Nacionales)');
		$cbcID->setAttribute('schemeID','31');

		$cacPartyName = $xml->createElement('cac:PartyName');
		$cacPartyName = $feParty->appendChild($cacPartyName);

		$cbcName = $xml->createElement('cbc:Name','LOGICALSOFT S.A.S');
		$cbcName = $cacPartyName->appendChild($cbcName);

		$fePhysicalLocation = $xml->createElement('fe:PhysicalLocation');
		$fePhysicalLocation = $feParty->appendChild($fePhysicalLocation);

		$feAddress = $xml->createElement('fe:Address');
		$feAddress = $fePhysicalLocation->appendChild($feAddress);

		$cbcDepartment = $xml->createElement('cbc:Department','Valle Del Cauca');
		$cbcDepartment = $feAddress->appendChild($cbcDepartment);

		$cbcCitySubdivisionName = $xml->createElement('cbc:CitySubdivisionName','Sur');
		$cbcCitySubdivisionName = $feAddress->appendChild($cbcCitySubdivisionName);

		$cbcCityName = $xml->createElement('cbc:CityName','Cali');
		$cbcCityName = $feAddress->appendChild($cbcCityName);

		$cacAddressLine = $xml->createElement('cac:AddressLine');
		$cacAddressLine = $feAddress->appendChild($cacAddressLine);

		$cbcLine = $xml->createElement('cbc:Line','Calle 3 #60-29');
		$cbcLine = $cacAddressLine->appendChild($cbcLine);

		$cacCountry = $xml->createElement('cac:Country');
		$cacCountry = $feAddress->appendChild($cacCountry);

		$cbcIdentificationCode1 = $xml->createElement('cbc:IdentificationCode','CO');
		$cbcIdentificationCode1 = $cacCountry->appendChild($cbcIdentificationCode1);

		$fePartyTaxScheme = $xml->createElement('fe:PartyTaxScheme');
		$fePartyTaxScheme = $feParty->appendChild($fePartyTaxScheme);

		$cbcTaxLevelCode = $xml->createElement('cbc:TaxLevelCode','2');
		$cbcTaxLevelCode = $fePartyTaxScheme->appendChild($cbcTaxLevelCode);

		$cacTaxScheme = $xml->createElement('cac:TaxScheme');
		$cacTaxScheme = $fePartyTaxScheme->appendChild($cacTaxScheme);

		$fePartyLegalEntity = $xml->createElement('fe:PartyLegalEntity');
		$fePartyLegalEntity = $feParty->appendChild($fePartyLegalEntity);

		$cbcRegistrationName = $xml->createElement('cbc:RegistrationName','LOGICALSOFT S.A.S');
		$cbcRegistrationName = $fePartyLegalEntity->appendChild($cbcRegistrationName);

		//------------------------------------------//
		//--------------Datos Del Cliente-----------//
		$feAccountingCustomerParty = $xml->createElement('fe:AccountingCustomerParty');
		$feAccountingCustomerParty = $feInvoice->appendChild($feAccountingCustomerParty);

		$cbcAdditionalAccountID1 = $xml->createElement('cbc:AdditionalAccountID',$id_tipo_persona_dianT);
		$cbcAdditionalAccountID1 = $feAccountingCustomerParty->appendChild($cbcAdditionalAccountID1);

		$feParty1 = $xml->createElement('fe:Party');
		$feParty1 = $feAccountingCustomerParty->appendChild($feParty1);

		$cacPartyIdentification1 = $xml->createElement('cac:PartyIdentification');
		$cacPartyIdentification1 = $feParty1->appendChild($cacPartyIdentification1);

		$cbcID1 = $xml->createElement('cbc:ID',$numero_identificacionT);
		$cbcID1 = $cacPartyIdentification1->appendChild($cbcID1);
		$cbcID1->setAttribute('schemeAgencyID','195');
		$cbcID1->setAttribute('schemeAgencyName','CO, DIAN (Direccion de Impuestos y Aduanas Nacionales)');
		$cbcID1->setAttribute('schemeID','22');

		$fePhysicalLocation1 = $xml->createElement('fe:PhysicalLocation');
		$fePhysicalLocation1 = $feParty1->appendChild($fePhysicalLocation1);

		$feAddress1 = $xml->createElement('fe:Address');
		$feAddress1 = $fePhysicalLocation1->appendChild($feAddress1);

		$cbcDepartment1 = $xml->createElement('cbc:Department',$departamentoT);
		$cbcDepartment1 = $feAddress1->appendChild($cbcDepartment1);

		$cbcCitySubdivisionName1 = $xml->createElement('cbc:CitySubdivisionName',$sucursal_clienteVF);
		$cbcCitySubdivisionName1 = $feAddress1->appendChild($cbcCitySubdivisionName1);

		$cbcCityName1 = $xml->createElement('cbc:CityName',$ciudadT);
		$cbcCityName1 = $feAddress1->appendChild($cbcCityName1);

		$cacAddressLine1 = $xml->createElement('cac:AddressLine');
		$cacAddressLine1 = $feAddress1->appendChild($cacAddressLine1);

		$cbcLine1 = $xml->createElement('cbc:Line',$direccionT);
		$cbcLine1 = $cacAddressLine1->appendChild($cbcLine1);

		$cacCountry1 = $xml->createElement('cac:Country');
		$cacCountry1 = $feAddress1->appendChild($cacCountry1);

		$cbcIdentificationCode2 = $xml->createElement('cbc:IdentificationCode',$paisT);
		$cbcIdentificationCode2 = $cacCountry1->appendChild($cbcIdentificationCode2);

		$fePartyTaxScheme1 = $xml->createElement('fe:PartyTaxScheme');
		$fePartyTaxScheme1 = $feParty1->appendChild($fePartyTaxScheme1);

		$cbcTaxLevelCode1 = $xml->createElement('cbc:TaxLevelCode',$codigo_regimen_dianT);
		$cbcTaxLevelCode1 = $fePartyTaxScheme1->appendChild($cbcTaxLevelCode1);

		$cacTaxScheme1 = $xml->createElement('cac:TaxScheme');
		$cacTaxScheme1 = $fePartyTaxScheme1->appendChild($cacTaxScheme1);

		if($id_tipo_persona_dianT == 1){
			$fePerson = $xml->createElement('fe:Person');
			$fePerson = $feParty1->appendChild($fePerson);

			$cbcFirstName = $xml->createElement('cbc:FirstName',$nombreT);
			$cbcFirstName = $fePerson->appendChild($cbcFirstName);

			$cbcFamilyName = $xml->createElement('cbc:FamilyName',$nombreT);
			$cbcFamilyName = $fePerson->appendChild($cbcFamilyName);

			$cbcMiddleName = $xml->createElement('cbc:MiddleName',$nombreT);
			$cbcMiddleName = $fePerson->appendChild($cbcMiddleName);
		}

		//------------------------------------------//
		//------------------Impuestos---------------//
		for($i = 0; $i < $contArticulos; $i++){
			if($valor_impuestoVFI[$i] != NULL){
				$feTaxTotal = $xml->createElement('fe:TaxTotal');
				$feTaxTotal = $feInvoice->appendChild($feTaxTotal);

				$cbcTaxAmount = $xml->createElement('cbc:TaxAmount',$costo_impuestoVFI[$i]);
				$cbcTaxAmount = $feTaxTotal->appendChild($cbcTaxAmount);
				$cbcTaxAmount->setAttribute('currencyID','COP');

				$cbcTaxEvidenceIndicator = $xml->createElement('cbc:TaxEvidenceIndicator','false');
				$cbcTaxEvidenceIndicator = $feTaxTotal->appendChild($cbcTaxEvidenceIndicator);

				$feTaxSubtotal = $xml->createElement('fe:TaxSubtotal');
				$feTaxSubtotal = $feTaxTotal->appendChild($feTaxSubtotal);

				$cbcTaxableAmount = $xml->createElement('cbc:TaxableAmount',$costo_finalVFI[$i]);
				$cbcTaxableAmount = $feTaxSubtotal->appendChild($cbcTaxableAmount);
				$cbcTaxableAmount->setAttribute('currencyID','COP');

				$cbcTaxAmount1 = $xml->createElement('cbc:TaxAmount',$costo_impuestoVFI[$i]);
				$cbcTaxAmount1 = $feTaxSubtotal->appendChild($cbcTaxAmount1);
				$cbcTaxAmount1->setAttribute('currencyID','COP');

				$cbcPercent = $xml->createElement('cbc:Percent',$valor_impuestoVFI[$i]);
				$cbcPercent = $feTaxSubtotal->appendChild($cbcPercent);

				$cacTaxCategory = $xml->createElement('cac:TaxCategory');
				$cacTaxCategory = $feTaxSubtotal->appendChild($cacTaxCategory);

				$cacTaxScheme = $xml->createElement('cac:TaxScheme');
				$cacTaxScheme = $cacTaxCategory->appendChild($cacTaxScheme);

			  $cbcID2 = $xml->createElement('cbc:ID',$codigo_impuesto_dian[$i]);
				$cbcID2 = $cacTaxScheme->appendChild($cbcID2);
			}
	  }

		//------------------------------------------//
		//----------------Retenciones---------------//

		for($i = 0; $i < $contRetenciones; $i++){
			if($tipo_retencionVFR[$i] != "AutoRetencion"){
				if(array_sum($costo_finalVFI) > $baseVFR[$i]){
					$costo_retencionVFR[$i] = array_sum($costo_finalVFI) * $valorVFR[$i] / 100;

					$feTaxTotal = $xml->createElement('fe:TaxTotal');
					$feTaxTotal = $feInvoice->appendChild($feTaxTotal);

					$cbcTaxAmount = $xml->createElement('cbc:TaxAmount',$costo_retencionVFR[$i]);
					$cbcTaxAmount = $feTaxTotal->appendChild($cbcTaxAmount);
					$cbcTaxAmount->setAttribute('currencyID','COP');

					$cbcTaxEvidenceIndicator = $xml->createElement('cbc:TaxEvidenceIndicator','true');
					$cbcTaxEvidenceIndicator = $feTaxTotal->appendChild($cbcTaxEvidenceIndicator);

					$feTaxSubtotal = $xml->createElement('fe:TaxSubtotal');
					$feTaxSubtotal = $feTaxTotal->appendChild($feTaxSubtotal);

					$cbcTaxableAmount = $xml->createElement('cbc:TaxableAmount',array_sum($costo_finalVFI));
					$cbcTaxableAmount = $feTaxSubtotal->appendChild($cbcTaxableAmount);
					$cbcTaxableAmount->setAttribute('currencyID','COP');

					$cbcTaxAmount1 = $xml->createElement('cbc:TaxAmount',$costo_retencionVFR[$i]);
					$cbcTaxAmount1 = $feTaxSubtotal->appendChild($cbcTaxAmount1);
					$cbcTaxAmount1->setAttribute('currencyID','COP');

					$cbcPercent = $xml->createElement('cbc:Percent',$valorVFR[$i] / 1);
					$cbcPercent = $feTaxSubtotal->appendChild($cbcPercent);

					$cacTaxCategory = $xml->createElement('cac:TaxCategory');
					$cacTaxCategory = $feTaxSubtotal->appendChild($cacTaxCategory);

					$cacTaxScheme = $xml->createElement('cac:TaxScheme');
					$cacTaxScheme = $cacTaxCategory->appendChild($cacTaxScheme);

				  if($tipo_retencionVFR[$i] == 'ReteIca'){
						$cbcID2 = $xml->createElement('cbc:ID','03');
					}else{
						$cbcID2 = $xml->createElement('cbc:ID','');
					}
					$cbcID2 = $cacTaxScheme->appendChild($cbcID2);
				}
			}
	  }

		//------------------------------------------//
		//-----------Totales De La Factura----------//
		$feLegalMonetaryTotal = $xml->createElement('fe:LegalMonetaryTotal');
		$feLegalMonetaryTotal = $feInvoice->appendChild($feLegalMonetaryTotal);

		$cbcLineExtensionAmount = $xml->createElement('cbc:LineExtensionAmount',array_sum($costo_finalVFI));
		$cbcLineExtensionAmount = $feLegalMonetaryTotal->appendChild($cbcLineExtensionAmount);
		$cbcLineExtensionAmount->setAttribute('currencyID','COP');

		$cbcTaxExclusiveAmount = $xml->createElement('cbc:TaxExclusiveAmount',array_sum($costo_impuestoVFI));
		$cbcTaxExclusiveAmount = $feLegalMonetaryTotal->appendChild($cbcTaxExclusiveAmount);
		$cbcTaxExclusiveAmount->setAttribute('currencyID','COP');

		$totalFactura = array_sum($costo_finalVFI) + array_sum($costo_impuestoVFI) - array_sum($costo_retencionVFR);
		$cbcPayableAmount = $xml->createElement('cbc:PayableAmount',$totalFactura);
		$cbcPayableAmount = $feLegalMonetaryTotal->appendChild($cbcPayableAmount);
		$cbcPayableAmount->setAttribute('currencyID','COP');

		//------------------------------------------//
		//-------Informacion De Los Articulos-------//
		for($i = 0; $i < $contArticulos; $i++){
			$feInvoiceLine = $xml->createElement('fe:InvoiceLine');
			$feInvoiceLine = $feInvoice->appendChild($feInvoiceLine);

			$cbcID3 = $xml->createElement('cbc:ID',$i+1);//codigo del articulo
			$cbcID3 = $feInvoiceLine->appendChild($cbcID3);

			$cbcInvoicedQuantity = $xml->createElement('cbc:InvoicedQuantity',$cantidadVFI[$i]);//cantidad del articulo
			$cbcInvoicedQuantity = $feInvoiceLine->appendChild($cbcInvoicedQuantity);

			$cbcLineExtensionAmount = $xml->createElement('cbc:LineExtensionAmount',$costo_finalVFI[$i]);//costo total del articulo
			$cbcLineExtensionAmount = $feInvoiceLine->appendChild($cbcLineExtensionAmount);
			$cbcLineExtensionAmount->setAttribute('currencyID','COP');

			$feItem = $xml->createElement('fe:Item');
			$feItem = $feInvoiceLine->appendChild($feItem);

			$cbcDescription = $xml->createElement('cbc:Description',$nombreVFI[$i]);//descripcion del articulo
			$cbcDescription = $feItem->appendChild($cbcDescription);

			$fePrice = $xml->createElement('fe:Price');
			$fePrice = $feInvoiceLine->appendChild($fePrice);

			$cbcPriceAmount = $xml->createElement('cbc:PriceAmount',$costo_unitarioVFI[$i]);//precio unitario sin impuestos
			$cbcPriceAmount = $fePrice->appendChild($cbcPriceAmount);
			$cbcPriceAmount->setAttribute('currencyID','COP');
		}

		//CONSTRUCCION DEL ARCHIVO XML
    $xml->formatOutput = true;
    $xml->save('face_f0900467785hhhhhhhhhh.xml');
		// $xmlString = $xml->saveXML();
		// $this->xmlString = $xmlString;
	}

	public function signXML(){
		//Configuracion de la firma
		$settingKeys = array(
		  'config'           => 'C:\xampp\php\extras\openssl\openssl.cnf',
		  'private_key_bits' => 2048,
		  'private_key_type' => OPENSSL_KEYTYPE_RSA,
		);

		//Generar el par de claves publica y privada
		$newKeys = openssl_pkey_new($settingKeys);

		//Construye el archivo que contendra la clave privada
		openssl_pkey_export_to_file($newKeys,'C:\PROYECTOS\LOGICALERP\LOGICALERP\funciones_globales\xml\privateKey.pem',NULL,$settingKeys);

		//Capturamos el contenido de la clave privada
		$pemPrivateKey = file_get_contents('privateKey.pem');

		//Obtengo la clave privada
		$private_key = openssl_pkey_get_private($pemPrivateKey);

		//Obtengo la clave publica
		$public_key = openssl_pkey_get_details($private_key)['key'];
		file_put_contents('publicKey.pem',$public_key);

		//Capturamos el contenido del XML
		$xml = file_get_contents('face_f0900467785hhhhhhhhhh.xml');

		//Creamos el tag DigestValue basado en todo el documento XML
		$digestAllDocument = openssl_digest($xml,"sha1",true);
		$digestAllDocument = base64_encode($digestAllDocument);
		echo "<br><br>Se creo la digestion de todo el documento:<br>" . $digestAllDocument;
		$this->digestAllDocument = $digestAllDocument;

		//Creamos el tag SignatureValue basado en todo el documento XML
		openssl_sign($xml,$sign,$private_key);
		$sign = base64_encode($sign);
		echo "<br><br>Se creo la firma de todo el documento:<br>" . $sign;
		$this->sign = $sign;

		//Pasamos los datos que contendra el certificado
		$settingInformation = array(
	    'countryName' 						=> 'CO',
	    'stateOrProvinceName' 		=> 'Valle Del Cauca',
	    'localityName' 						=> 'Cali',
	    'organizationName' 				=> 'LogicalSoft SAS',
	    'organizationalUnitName' 	=> 'Tecnologia',
	    'commonName' 							=> 'logicalsoft-erp.com',
	    'emailAddress' 						=> 'logical.erp@logicalsoft.co'
		);

		//Creamos el certificado CSR
		$newCSR = openssl_csr_new($settingInformation,$newKeys,$settingKeys);
		openssl_csr_export($newCSR,$certificate);

		//Pasamos parametros de configuracion al certificado
		$settingCRS = array(
		  'config' => 'C:\xampp\php\extras\openssl\openssl.cnf',
		);

		//Firmamos el certificado CRS
		$random = rand(1000000000000000000,9999999999999999999);
		$certificate = openssl_csr_sign($newCSR,NULL,$newKeys,365,$settingCRS,$random);

		//Exportamos el certificado CRS a un certificado x509
		openssl_x509_export($certificate,$certificateIn);
		$string = array ("-----BEGIN CERTIFICATE-----","-----END CERTIFICATE-----");
		$replace = array ("","");
		$certificateOut = str_replace($string,$replace,$certificateIn);
		echo "<br><br>Se creo el certificado X509:<br>" . $certificateOut;
		$this->certificateOut = $certificateOut;

		//Creamos el tag DigestValue basado en el nodo KeyInfo
		$digestKeyInfo = openssl_digest($certificateOut,"sha1",true);
		$digestKeyInfo = base64_encode($digestKeyInfo);
		echo "<br><br>Se creo la digestion del KeyInfo:<br>" . $digestKeyInfo;
		$this->digestKeyInfo = $digestKeyInfo;

		//Creamos el tag IssuerSerial basado en el certificado x509
		$issuerSerial = openssl_x509_parse($certificateIn, FALSE);
		echo "<br><br>Se creo la matriz del certificado x509:<br>";
		var_dump($issuerSerial);
		// Verificamos si el xml no ha sido alterado
		// if(openssl_verify($xml,$sign,$public_key) === 1){
		//   echo '<br>la firma es valida y los datos son confiables';
		// }else{
		//   echo '<br>la firma es invalida y/o los datos fueron alterados';
		// }
	}

	public function addSignXML(){
		//Creamos un objeto
		$xml = new DOMDocument();
		$dateTime = date('Y-m-d') . "T" . date('H:i:s');

		//Pasamos parametros de configuracion al objeto para dar formato al xml
		$xml->preserveWhiteSpace = false;
		$xml->formatOutput = true;

		//Cargamos el xml
		$xml->load('test.xml');

		//Añadimos el atributo xmlns:ds porque este bloquea cualquier modificacion
		$dsSignature = $xml->getElementsByTagName('ds:Signature')->item(0);
		$dsSignature->setAttribute('xmlns:ds','http://www.w3.org/2000/09/xmldsig#');

		$dsReference   = $xml->getElementsByTagName('ds:Reference')->item(0);
		$dsDigestValue = $xml->createDocumentFragment();
		$dsDigestValue->appendXML("<ds:DigestValue>$this->digestAllDocument</ds:DigestValue>");
		$dsReference->appendChild($dsDigestValue);

		$dsSignatureValue = $xml->createDocumentFragment();
		$dsSignatureValue->appendXML("<ds:SignatureValue>$this->sign</ds:SignatureValue>");
		$dsSignature->appendChild($dsSignatureValue);

		$dsKeyInfo = $xml->createDocumentFragment();
		$dsKeyInfo->appendXML("<ds:KeyInfo><ds:X509Data><ds:X509Certificate>$this->certificateOut</ds:X509Certificate></ds:X509Data></ds:KeyInfo>");
		$dsSignature->appendChild($dsKeyInfo);

		// $prueba = $xml->getElementsByTagName('sts:prueba')->item(0);
		// $fragment = $xml->createDocumentFragment();
		// $fragment->appendXML("<sts:hola>aqui</sts:hola>");
		// $prueba->appendChild($fragment);

		$xml->save('test.xml');
	}

}
?>
