<?php
	header("Content-type: text/xml; charset=utf-8");
	echo'<definitions xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:SOAP-ENC="http://schemas.xmlsoap.org/soap/encoding/" xmlns:tns="urn:insertCuentas" xmlns:soap="http://schemas.xmlsoap.org/wsdl/soap/" xmlns:wsdl="http://schemas.xmlsoap.org/wsdl/" xmlns="http://schemas.xmlsoap.org/wsdl/" targetNamespace="urn:insertCuentas">
			<types>
				<xsd:schema targetNamespace="urn:insertCuentas">
					<xsd:import namespace="http://schemas.xmlsoap.org/soap/encoding/"/>
					<xsd:import namespace="http://schemas.xmlsoap.org/wsdl/"/>
				</xsd:schema>
			</types>
			<message name="insertCuentasRequest"/>
			<message name="insertCuentasResponse"/>

			<portType name="insertCuentas">
				<operation name="insertCuentas">
					<input message="tns:insertCuentasRequest">
						<apiVersion>1</apiVersion>
						<nit_empresa>nit</nit_empresa>
						<sucursal>CALI (Principal)</sucursal>
						<username>username</username>
						<password>xxxxxxxx</password>
						<tipo_documento>nota_general</tipo_documento>
						<tipo_nota>NOTA GENERAL</tipo_nota>
						<fecha_documento>2014-12-11</fecha_documento>
						<nit_tercero>1112103967</nit_tercero>
						<cuentas type="tns:array">
							<cuenta type="tns:array(1)">
								<cuenta_colgaap>110505</cuenta_colgaap>
								<cuenta_niif>11100511</cuenta_niif>
								<naturaleza>debito</naturaleza>
								<nit_tercero_cuenta>nit</nit_tercero_cuenta>
								<saldo>200</saldo>
							</cuenta>
							<cuenta type="tns:array(2)">
								<cuenta_colgaap>110505</cuenta_colgaap>
								<cuenta_niif></cuenta_niif>
								<naturaleza>credito</naturaleza>
								<nit_tercero_cuenta>1112103967</nit_tercero_cuenta>
								<saldo>200</saldo>
							</cuenta>
						</cuentas>
					</input>
					<output message="tns:insertCuentasResponse">
						<estado>true</estado>
						<mensaje></mensaje>
					</output>
				</operation>
			</portType>
			<binding name="insertCuentasBinding" type="tns:insertCuentasPortType">
				<soap:binding style="rpc" transport="http://schemas.xmlsoap.org/soap/http"/>
				<operation name="insertCuentas">
					<soap:operation soapAction="http://logicalerp.localhost/LOGICALERP/web_service/register.php/insertCuentas" style="rpc"/>
					<input>
						<soap:body use="encoded" namespace="" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/"/>
					</input>
					<output>
						<soap:body use="encoded" namespace="" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/"/>
					</output>
				</operation>
			</binding>
			<service name="insertCuentas">
				<port name="insertCuentasPort" binding="tns:insertCuentasBinding">
					<soap:address location="http://logicalerp.localhost/LOGICALERP/web_service/register.php"/>
				</port>
			</service>
		</definitions>';
?>
