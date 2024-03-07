<?php
	header("Content-type: text/xml; charset=utf-8");
	echo'<definitions xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:SOAP-ENC="http://schemas.xmlsoap.org/soap/encoding/" xmlns:tns="urn:insertDocumentos" xmlns:soap="http://schemas.xmlsoap.org/wsdl/soap/" xmlns:wsdl="http://schemas.xmlsoap.org/wsdl/" xmlns="http://schemas.xmlsoap.org/wsdl/" targetNamespace="urn:insertDocumentos">
			<types>
				<xsd:schema targetNamespace="urn:insertDocumentos">
					<xsd:import namespace="http://schemas.xmlsoap.org/soap/encoding/"/>
					<xsd:import namespace="http://schemas.xmlsoap.org/wsdl/"/>
				</xsd:schema>
			</types>
			<message name="insertDocumentosRequest"/>
			<message name="insertDocumentosResponse"/>

			<portType name="insertDocumentosPortType">
				<operation name="insertDocumentos">
					<input message="tns:insertDocumentosRequest">
						<apiVersion>1</apiVersion>
						<nit_empresa>nit</nit_empresa>
						<sucursal>CALI (Principal)</sucursal>
						<username>username</username>
						<password>xxxxxxxx</password>
						<tipo_documento>factura_venta</tipo_documento>
						<fecha_documento>2014-12-11</fecha_documento>
						<fecha_vencimiento>2014-12-11</fecha_vencimiento>
						<nit_tercero>1112103967</nit_tercero>
						<cuenta_pago_colgaap>130505</cuenta_pago_colgaap>
						<prefijo_documento>FV</prefijo_documento>
						<numero_documento>201</numero_documento>
						<cuentas type="tns:array">
							<cuenta type="tns:array(1)">
								<cuenta_colgaap>130505</cuenta_colgaap>
								<cuenta_niif></cuenta_niif>
								<naturaleza>debito</naturaleza>
								<codigo_centro_costos></codigo_centro_costos>
								<saldo>200</saldo>
							</cuenta>
							<cuenta type="tns:array(2)">
								<cuenta_colgaap>135515</cuenta_colgaap>
								<cuenta_niif></cuenta_niif>
								<naturaleza>debito</naturaleza>
								<codigo_centro_costos></codigo_centro_costos>
								<saldo>200</saldo>
							</cuenta>
							<cuenta type="tns:array(3)">
								<cuenta_colgaap>143501</cuenta_colgaap>
								<cuenta_niif></cuenta_niif>
								<naturaleza>credito</naturaleza>
								<codigo_centro_costos></codigo_centro_costos>
								<saldo>200</saldo>
							</cuenta>
							<cuenta type="tns:array(4)">
								<cuenta_colgaap>240801</cuenta_colgaap>
								<cuenta_niif></cuenta_niif>
								<naturaleza>credito</naturaleza>
								<codigo_centro_costos></codigo_centro_costos>
								<saldo>200</saldo>
							</cuenta>
							<cuenta type="tns:array(5)">
								<cuenta_colgaap>413520</cuenta_colgaap>
								<cuenta_niif></cuenta_niif>
								<naturaleza>credito</naturaleza>
								<codigo_centro_costos></codigo_centro_costos>
								<saldo>200</saldo>
							</cuenta>
						</cuentas>
					</input>
					<output message="tns:insertDocumentosResponse">
						<estado>true</estado>
						<mensaje></mensaje>
					</output>
				</operation>
			</portType>
			<binding name="insertDocumentosBinding" type="tns:insertDocumentosPortType">
				<soap:binding style="rpc" transport="http://schemas.xmlsoap.org/soap/http"/>
				<operation name="insertDocumentos">
					<soap:operation soapAction="http://logicalerp.localhost/LOGICALERP/web_service/register.php/insertDocumentos" style="rpc"/>
					<input>
						<soap:body use="encoded" namespace="" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/"/>
					</input>
					<output>
						<soap:body use="encoded" namespace="" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/"/>
					</output>
				</operation>
			</binding>
			<service name="insertDocumentos">
			<port name="insertDocumentosPort" binding="tns:insertDocumentosBinding">
			<soap:address location="http://logicalerp.localhost/LOGICALERP/web_service/register.php"/>
			</port>
			</service>
			</definitions>';
?>
