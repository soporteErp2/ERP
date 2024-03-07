<?php
    // header('Access-Control-Allow-Origin: *');
    // set_time_limit(0);

    $array = array(
                'apiVersion'  => '1',
                'nit_empresa' => '900467785',
                'username'    => 'jhon.marroquin',
                'password'    => '123456789',
                'tercero'     => array
                (
                    'tipo_identificacion' => 'C.C.',
                    'ciudad_identificacion' => 'cali',
                    'numero_identificacion' => '7000000',
                    'dv' => '6',
                    'nombre' => 'JHON ERICK SAS',
                    'nombre_comercial' => 'JHON ERICK SAS',
                    'direccion' => 'calle 81',
                    'telefono1' => 'telefono 1',
                    'telefono2' => 'telefono 2',
                    'celular1' => 'celular 1',
                    'celular2' => 'celular 2',
                    // 'pais' => 'Colombia',
                    // 'departamento' => 'Valle',
                    // 'ciudad' => 'Cali',
                    'representante_legal' => 'representante jhon erick sas',
                    'tipo_identificacion_representante' => 'C.C.',
                    'identificacion_representante' => 'cedula representante',
                    'ciudad_id_representante' => 'cuidad representante',
                    'ciudad_representante' => 'domicilio representante',
                    'pagina_web' => 'pagina web jhon.com',
                    'id_tercero_tributario' => '6',
                    'cliente' => 'si',
                    'proveedor' => 'si',
                    'sector_empresarial' => 'sector comercial',
                    'exento_iva' => 'no',
                    'nombre1' => 'JHON',
                    'nombre2' => '',
                    'apellido1' => 'MARROQUIN',
                    'apellido2' => '',
                    'nombre_regimen' => 'Regimen Comun',
                ),
                'arrayContactos' => array
                (
                    '1' => array
                    (
                        'tipo_identificacion' => 'C.C.',
                        'numero_identificacion' => '14469090',
                        'tratamiento' => 'Sr.',
                        'nombre' => 'JHON ERICK SAS',
                        'cargo' => '',
                        'direccion' => '',
                        'telefono1' => '',
                        'telefono2' => '',
                        'celular1' => '',
                        'celular2' => '',
                        'nacimiento' => '',
                        'observaciones' => '',
                        'sexo' => 'Masculino',
                        'emails'=> array('jhon3rick@gmail.com','jhon3rick1@gmail.com','jhon3rick2@gmail.com')
                    ),
                    '2' => array
                    (
                        'tipo_identificacion' => 'C.C.',
                        'numero_identificacion' => '14469098',
                        'tratamiento' => 'Sr.',
                        'nombre' => 'JHON CONTACTO',
                        'cargo' => 'cargo contacto',
                        'direccion' => 'calle contyacto',
                        'telefono1' => 'telefono 1 contacto',
                        'telefono2' => 'telefono2 cto',
                        'celular1' => 'celular 1 cto',
                        'celular2' => 'celular2 cto',
                        'nacimiento' => '1985-12-22',
                        'observaciones' => 'obs',
                        'sexo' => 'Masculino',
                        'emails'=> array('jhon3rick@gmail.com','jhon3rick1@gmail.com','jhon3rick2@gmail.com')
                    )
                ),
                'arraySucursales' => array
                (
                    '1' => array
                    (
                        'nombre' => 'Sucursal principal2',
                        'direccion' => 'calle 81',
                        'telefono1' => 'telefono 1',
                        'telefono2' => '',
                        'celular1' => '',
                        'celular2' => '',
                        'pais' => 'Colombia',
                        'departamento' => 'Valle',
                        'ciudad' => 'Cali',
                    ),
                    '2' => array
                    (
                        'nombre' => 'calle sucursal',
                        'direccion' => 'direccion sucursal',
                        'telefono1' => 'telefono 1 sucursal',
                        'telefono2' => 'telefono 2 sucursal',
                        'celular1' => 'celular 1 sucursal',
                        'celular2' => 'celular 2 sucursal',
                        // 'pais' => 'Colombia',
                        // 'departamento' => 'Valle',
                        // 'ciudad' => 'Cali'
                    )
                ),
            );


    $bodyxml = '<?xml version="1.0" encoding="utf-8"?>
                    <soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:wsa="http://schemas.xmlsoap.org/ws/2004/08/addressing" xmlns:wsse="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd" xmlns:wsu="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd">
                      <soap:Header>
                        <wsa:Action>http://htng.org/PWSWG/2007/01/DigitalSignage#MeetingSpaceRequest</wsa:Action>
                        <wsa:MessageID>urn:uuid:074d71ba-bdfc-4805-bc70-d6a19794cb0a</wsa:MessageID>
                        <wsa:ReplyTo>
                          <wsa:Address>http://schemas.xmlsoap.org/ws/2004/08/addressing/role/anonymous</wsa:Address>
                        </wsa:ReplyTo>
                        <wsa:To>http://vqas079/NIIS/MeetingSpaceServices/Request.asmx</wsa:To>
                        <wsse:Security soap:mustUnderstand="1">
                          <wsu:Timestamp wsu:Id="Timestamp-6debed6f-b2ea-4e91-a56b-c447a6372edb">
                            <wsu:Created>2014-07-21T11:12:08Z</wsu:Created>
                            <wsu:Expires>2014-08-21T11:17:08Z</wsu:Expires>
                          </wsu:Timestamp>
                          <wsse:UsernameToken xmlns:wsu="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd" wsu:Id="SecurityToken-f3394922-7f07-4c3f-8ebb-c0f183d3d6e9">
                            <wsse:Username>LogicalSignage</wsse:Username>
                            <wsse:LicenseKey Type="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-username-token-profile-1.0#PasswordText">+Olrk4NXVYIp4nPGX0m8CSOHDJ1AEMg3iQcRjN/nLCzvUpTgk0ztow==</wsse:LicenseKey>
                            <wsse:Password Type="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-username-token-profile-1.0#PasswordText">+Olrk4NXVYIp4nPGX0m8CSOHDJ1AEMg3iQcRjN/nLCzvUpTgk0ztow==</wsse:Password>
                            <wsse:Nonce>vZbj+AaJkKu3mbz9l4fHeQ==</wsse:Nonce>
                            <wsu:Created>2011-04-21T11:12:08Z</wsu:Created>
                          </wsse:UsernameToken>
                        </wsse:Security>
                      </soap:Header>
                      <soap:Body>
                        <MeetingSpaceRequest propertyKey="10196" isExhibit="1" xmlns="http://htng.org/PWSWG/2007/01/DigitalSignage/MeetingSpaceRequest/Types">
                          <DateRange startDateTime="2014-01-25T01:00:00" endDateTime="2014-04-30T23:00:00"/>
                        </MeetingSpaceRequest>

                      </soap:Body>
                    </soap:Envelope>';


    if(!isset($_GET['debug'])){
        require_once ("nuSoap/nusoap.php");

        // $cliente = new nusoap_client("http://logicalerp.localhost/logicalerp/web_service/register.php",true);
        $cliente = new nusoap_client("http://www.logicalsoft-erp.com/LOGICALERP/web_service/register.php",true);
        $error   = $cliente->getError();
        $fecha   = date("Y-m-d");

        if ($error) { echo "<h2>Constructor error</h2><pre>".$error."</pre>"; }

        $result = $cliente->call("insertUpdateTercero", array('arrayWs' => $array));

        $client->soap_defencoding = 'utf-8';
        $client->useHTTPPersistentConnection();
        $client->setUseCurl($useCURL);
        $bsoapaction = "http://htng.org/PWSWG/2007/01/DigitalSignage#MeetingSpaceRequest";

        $result = $client->send($bodyxml, $bsoapaction);

        if ($cliente->fault) {
            echo '<h2>Fault</h2><pre>';
                print_r($result);
            echo '</pre>';
        }
        else {
            $error = $cliente->getError();
            if ($error) {
                echo '<h2>Error</h2><pre>'.$error.'</pre>';
            }
            else {
                echo'<h2 style="background-color:silver;">Web service</h2>
                    <pre>';
                    print_r($result);
                echo'</pre>';
            }
        }
    }
    else{
        $debug = true;
        include('register.php');
        print_r(insertUpdateTercero($array));
    }


?>