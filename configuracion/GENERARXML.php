<?php

	header ("Content-Type:text/xml");

	
	set_time_limit(0);
		
	
	function array_to_xml(array $arr, SimpleXMLElement $xml)
	{
		foreach ($arr as $k => $v) {
			is_array($v)
				? array_to_xml($v, $xml->addChild($k))
				: $xml->addChild($k, $v);
		}
		return $xml;
	}


    $arrayNotas = array(
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
	
	
	/*$xml = new SimpleXMLElement('<root/>');
	array_walk_recursive($arrayNotas, array ($xml, 'addChild'));
	print $xml->asXML();*/
	
	echo array_to_xml($arrayNotas, new SimpleXMLElement('<root/>'))->asXML();
?>
