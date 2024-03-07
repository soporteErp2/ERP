<?php
	header("Content-type: text/xml; charset=utf-8");
	echo'<definitions xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:SOAP-ENC="http://schemas.xmlsoap.org/soap/encoding/" xmlns:tns="urn:insertUpdateTercero" xmlns:soap="http://schemas.xmlsoap.org/wsdl/soap/" xmlns:wsdl="http://schemas.xmlsoap.org/wsdl/" xmlns="http://schemas.xmlsoap.org/wsdl/" targetNamespace="urn:insertUpdateTercero">
			<types>
				<xsd:schema targetNamespace="urn:insertUpdateTercero">
					<xsd:import namespace="http://schemas.xmlsoap.org/soap/encoding/"/>
					<xsd:import namespace="http://schemas.xmlsoap.org/wsdl/"/>
				</xsd:schema>
			</types>
			<message name="insertUpdateTerceroRequest"/>
			<message name="insertUpdateTerceroResponse"/>

			<portType name="insertUpdateTercero">
				<operation name="insertUpdateTercero">
					<input message="tns:insertUpdateTerceroRequest">
						<apiVersion>1</apiVersion>
						<nit_empresa>nit</nit_empresa>
						<username>username</username>
						<password>xxxxxxxxx</password>
						<tercero>
							<tipo_identificacion>C.C.</tipo_identificacion>
							<ciudad_identificacion>cali</ciudad_identificacion>
							<numero_identificacion>7000000</numero_identificacion>
							<dv>6</dv>
							<nombre>JHON ERICK SAS</nombre>
							<nombre_comercial>JHON ERICK SAS</nombre_comercial>
							<direccion>calle 81</direccion>
							<telefono1>telefono 1</telefono1>
							<telefono2>telefono 2</telefono2>
							<celular1>celular 1</celular1>
							<celular2>celular 2</celular2>
							<representante_legal>representante jhon erick sas</representante_legal>
							<tipo_identificacion_representante>C.C.</tipo_identificacion_representante>
							<identificacion_representante>cedula representante</identificacion_representante>
							<ciudad_id_representante>cuidad representante</ciudad_id_representante>
							<ciudad_representante>domicilio representante</ciudad_representante>
							<pagina_web>pagina web jhon.com</pagina_web>
							<id_tercero_tributario>6</id_tercero_tributario>
							<cliente>si</cliente>
							<proveedor>si</proveedor>
							<sector_empresarial>sector comercial</sector_empresarial>
							<exento_iva>no</exento_iva>
							<nombre1>JHON</nombre1>
							<nombre2></nombre2>
							<apellido1>MARROQUIN</apellido1>
							<apellido2></apellido2>
							<nombre_regimen>Regimen Comun</nombre_regimen>
						</tercero>
						<arrayContactos>
							<contacto type="tns:array(1)">
								<tipo_identificacion>C.C.</tipo_identificacion>
								<numero_identificacion>14469090</numero_identificacion>
								<tratamiento>Sr.</tratamiento>
								<nombre>JHON ERICK SAS</nombre>
								<cargo></cargo>
								<direccion></direccion>
								<telefono1></telefono1>
								<telefono2></telefono2>
								<celular1></celular1>
								<celular2></celular2>
								<nacimiento></nacimiento>
								<observaciones></observaciones>
								<sexo>Masculino</sexo>
								<emails>
									<email type="tns:array(1)">email@gmail.com</email>
									<email type="tns:array(2)">email@gmail.com</email>
									<email type="tns:array(3)">email@gmail.com</email>
								</emails>
							</contacto>
							<contacto type="tns:array(2)">
								<tipo_identificacion>C.C.</tipo_identificacion>
								<numero_identificacion>14469098</numero_identificacion>
								<tratamiento>Sr.</tratamiento>
								<nombre>JHON CONTACTO</nombre>
								<cargo>cargo contacto</cargo>
								<direccion>calle contyacto</direccion>
								<telefono1>telefono 1 contacto</telefono1>
								<telefono2>telefono2 cto</telefono2>
								<celular1>celular 1 cto</celular1>
								<celular2>celular2 cto</celular2>
								<nacimiento>1985-12-22</nacimiento>
								<observaciones>obs</observaciones>
								<sexo>Masculino</sexo>
								<emails>
									<email type="tns:array(1)">email@gmail.com</email>
									<email type="tns:array(2)">email@gmail.com</email>
									<email type="tns:array(3)">email@gmail.com</email>
								</emails>
							</contacto>
						</arrayContactos>
						<arraySucursales>
							<sucursal type="tns:array(1)">
								<nombre>Sucursal principal2</nombre>
								<direccion>calle 81</direccion>
								<telefono1>telefono 1</telefono1>
								<telefono2></telefono2>
								<celular1></celular1>
								<celular2></celular2>
								<pais>Colombia</pais>
								<departamento>Valle</departamento>
								<ciudad>Cali</ciudad>
							</sucursal>
							<sucursal type="tns:array(1)">
								<nombre>calle sucursal</nombre>
								<direccion>direccion sucursal</direccion>
								<telefono1>telefono 1 sucursal</telefono1>
								<telefono2>telefono 2 sucursal</telefono2>
								<celular1>celular 1 sucursal</celular1>
								<celular2>celular 2 sucursal</celular2>
							</sucursal>
						</arraySucursales>
					</input>
					<output message="tns:insertUpdateTerceroResponse">
						<estado>true</estado>
						<mensaje></mensaje>
					</output>
				</operation>
			</portType>

			<binding name="insertUpdateTerceroBinding" type="tns:insertUpdateTerceroPortType">
				<soap:binding style="rpc" transport="http://schemas.xmlsoap.org/soap/http"/>
				<operation name="insertUpdateTercero">
					<soap:operation soapAction="http://logicalerp.localhost/LOGICALERP/web_service/register.php/insertUpdateTercero" style="rpc"/>
					<input>
						<soap:body use="encoded" namespace="" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/"/>
					</input>
					<output>
						<soap:body use="encoded" namespace="" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/"/>
					</output>
				</operation>
			</binding>
			<service name="insertUpdateTercero">
				<port name="insertUpdateTerceroPort" binding="tns:insertUpdateTerceroBinding">
					<soap:address location="http://logicalerp.localhost/LOGICALERP/web_service/register.php"/>
				</port>
			</service>
		</definitions>';
?>
