<?php
	include("../../../../configuracion/conectar.php");
	include("../../../../configuracion/define_variables.php");

	$id_empresa = $_SESSION['EMPRESA'];
	$id_pais    = $_SESSION['PAIS'];

	switch($opc){
		case 'buscar_ciudad':
			buscar_ciudad($id_pais,$id_departamento,$id_ciudad,$id_empresa,$mysql);
			break;

		case 'actualiza_info_empresa':
			actualiza_info_empresa($nombre_empresa,$razon_social,$regimen,$actividad_economica,$id_departamento,$id_ciudad,$direccion,$telefono,$celular,$id_empresa,$mysql,$email,$tipo_persona_codigo,$tipo_persona_nombre,$software_facturacion,$tipo_responsabilidad,$id_software);
			break;

		case 'configurar_empresa':
			configurar_empresa($id_empresa,$mysql);
			break;
	}

	function buscar_ciudad($id_pais,$id_departamento,$id_ciudad,$id_empresa,$mysql){
		$sql = "SELECT id,ciudad FROM ubicacion_ciudad WHERE activo = 1 AND id_departamento = $id_departamento AND id_pais = $id_pais";
		$query = $mysql->query($sql,$mysql->link);

		while($row = $mysql->fetch_array($query)){
			$selected = ($id_ciudad == $row['id'])? 'selected' : '' ;
			$ciudades .= '<option value="'.$row['id'].'" '.$selected.' >'.$row['ciudad'].'</option>';
		}

		echo "<select style='width:190px;' data-requiere='true' id='id_ciudad' >$ciudades</select>";
	}

	function actualiza_info_empresa($nombre_empresa,$razon_social,$regimen,$actividad_economica,$id_departamento,$id_ciudad,$direccion,$telefono,$celular,$id_empresa,$mysql,$email,$tipo_persona_codigo,$tipo_persona_nombre,$software_facturacion,$tipo_responsabilidad,$id_software){
		$sql = "UPDATE
							empresas
						SET
							nombre      				 = '$nombre_empresa',
							razon_social         = '$razon_social',
							tipo_regimen         = '$regimen',
							actividad_economica  = '$actividad_economica',
							id_departamento      = '$id_departamento',
							id_ciudad            = '$id_ciudad',
							direccion            = '$direccion',
							telefono             = '$telefono',
							celular              = '$celular',
							email 							 = '$email',
							tipo_persona_codigo  = '$tipo_persona_codigo',
							tipo_persona_nombre  = '$tipo_persona_nombre',
							software_facturacion = '$software_facturacion',
							tipo_responsabilidad = '$tipo_responsabilidad',
							id_software          = '$id_software'
						WHERE
							activo = 1
						AND
							id = $id_empresa";

		$query = $mysql->query($sql,$mysql->link);

		if($query){
			echo "<script>
							MyLoading2('off');
							Win_Panel_Global.close();
						</script>";
		}
		else{
			echo "<script>
							MyLoading2('off',{icono:'fail',texto:'Error al actualizar la informacion'});
						</script>";
		}
	}

	function configurar_empresa($id_empresa,$mysql){
		$sql = "SELECT
							E.documento,
							E.digito_verificacion,
							TD.codigo,
							E.tipo_persona_codigo,
							TT.codigo_regimen_dian,
							E.tipo_responsabilidad,
							E.nombre,
							ES.numero_matricula_mercantil,
							M.id,
							E.direccion,
							E.telefono,
							E.email,
							E.id_software
						FROM
							empresas AS E
						LEFT JOIN empresas_sucursales AS ES ON E.id = ES.id_empresa
						LEFT JOIN tipo_documento AS TD ON E.tipo_documento = TD.id
						LEFT JOIN terceros_tributario AS TT ON E.tipo_regimen = TT.nombre
						LEFT JOIN ubicacion_departamento AS UD ON E.id_departamento = UD.id
						LEFT JOIN ubicacion_ciudad AS UC ON E.id_ciudad = UC.id
						LEFT JOIN municipalities AS M ON CONCAT(
							UD.codigo_departamento,
							UC.codigo_ciudad
						) = M.code
						WHERE
							E.id = 48
						AND E.activo = 1";
		$query = $mysql->query($sql,$mysql->link);
		$documento                  = $mysql->result($query,0,'documento');
		$digito_verificacion        = $mysql->result($query,0,'digito_verificacion');
		$codigo_tipo_documento      = $mysql->result($query,0,'codigo');
		$codigo_tipo_persona        = $mysql->result($query,0,'tipo_persona_codigo');
		$codigo_regimen_dian        = $mysql->result($query,0,'codigo_regimen_dian');
		$tipo_responsabilidad       = $mysql->result($query,0,'tipo_responsabilidad');
		$nombre                     = $mysql->result($query,0,'nombre');
		$numero_matricula_mercantil = $mysql->result($query,0,'numero_matricula_mercantil');
		$codigo_municipio           = $mysql->result($query,0,'id');
		$direccion                  = $mysql->result($query,0,'direccion');
		$telefono                   = $mysql->result($query,0,'telefono');
		$email                      = $mysql->result($query,0,'email');
		$id_software                = $mysql->result($query,0,'id_software');

		$data = array(
			"type_document_identification_id" => (int) $codigo_tipo_documento,
			"type_organization_id" => (int) $codigo_tipo_persona,
			"type_regime_id" => (int) $codigo_regimen_dian,
			"type_liability_id" => (int) $tipo_responsabilidad,
			"business_name" => $nombre,
			"merchant_registration" => $numero_matricula_mercantil,
			"municipality_id" => (int) $codigo_municipio,
			"address" => $direccion,
			"phone" => (int) $telefono,
			"email" => $email
		);

		$data = json_encode($data,JSON_PRETTY_PRINT);

		$params                   = [];
		$params['request_url']    = "http://192.168.8.2/apidian2020/public/api/ubl2.1/config/$documento/$digito_verificacion";
		$params['request_method'] = "POST";
		$params['Authorization']  = "";
		$params['data']           = $data;

		$respuesta = curlApi($params);
		$respuesta = json_decode($respuesta,true);

		if($respuesta['success'] == true){
			$sql = "UPDATE empresas SET token = '$respuesta[token]'
							WHERE activo = 1 AND id = $id_empresa";
			$query = $mysql->query($sql,$mysql->link);

			if(!$query){
				$script = "No se pudo actualizar la empresa.";
			}
			else{
				$script = "$respuesta[message] \\n";

				$respuestaCS = configurar_software($id_software,$respuesta["token"]);

				if($respuestaCS['success'] == true){
					$script .= "$respuestaCS[message] \\n";
				}

				$respuestaCC = configurar_certificado($respuesta["token"]);

				if($respuestaCC['success'] == true){
					$script .= "$respuestaCC[message]";
				}
			}
		}
		else{
			$script = "$respuesta[message]";
		}

		echo "<script>
						console.log('$script');
						MyLoading2('off');
					</script>";
	}

	function configurar_software($id_software,$token){
		$data = array(
			"id" => "$id_software",
			"pin" => (int) 20196
		);

		$data = json_encode($data,JSON_PRETTY_PRINT);

		$params                   = [];
		$params['request_url']    = "http://192.168.8.2/apidian2020/public/api/ubl2.1/config/software";
		$params['request_method'] = "PUT";
		$params['Authorization']  = "Authorization: Bearer $token";
		$params['data']           = $data;

		$respuesta = curlApi($params);
		$respuesta = json_decode($respuesta,true);

		return $respuesta;
	}

	function configurar_certificado($token){
		$data = array(
			"certificate" => "MIIcegIBAzCCHEAGCSqGSIb3DQEHAaCCHDEEghwtMIIcKTCCFp8GCSqGSIb3DQEHBqCCFpAwghaMAgEAMIIWhQYJKoZIhvcNAQcBMBwGCiqGSIb3DQEMAQYwDgQIpfCEVdOxlV8CAggAgIIWWOXPkh2LeGghZcpdXWOGSVBSOWYJ+hSZyZTAyfcMB4CT8b+gvcv1wFP84JUquviTybO6BYsrG97OA0D81dt+Hz8Ze9dovojcRpNiDmxK2m1BUzi6TDWfwJ8W+gGuMaAl9RsUqO9+lSHgZXYiGW35jfaD6wOtnMVV006yURhNXhwkdBk5MTQYUXfAZR0qz+Wa+SlHHhpHpq3jkfxFrL6zBRjhl0ob70ibcPOCgAhF5pp2Nneq3PdfhUanGqynJ1XqZgtFp+fadgOYmaatv2HSq/EmwJrSXXNMzV6sP8DUTgwlaTGoyYHO2kW7QRuVwhKIIGFBNIHLwRE3cl8AZQQVGhZfSlQa4i3PYVeLjyfQKFpS1eC4hPOk1jbTvkdT57IFb8pSJleJrIv+p9ppq+WZRiAWdg776EWUL1aksbNSBnDIljR9Ew7c7VTRIl1j29BJy53OPMJJrZ9qTNuyIiy1nZygZBqU/s9oI46pr0XPzG+9/ffgvpdMIOhLbP5zWcGCpzGMVNIYUI/3jpODnfXAVJCzn1nZk8ds9bpTf46uTWup//hiWUsoxRaicIoWLgLOoHtHTSeHPXxe5OkN7gya+JJf9L5ET0VS66KTerwpAO6/oTxuW3Hfi9jWdcfMlFt89y++bCkbyRC3FhZ7zwdazR19mJh16mIE5zShFTxXX4Vn0M1LcaLEnfV6JZY/xppVVe7ruXGiIhOun9/cstxiPE4QHfM2+7MZg2r4SAxSJO7QtJVk77RipXVMo5jF75SFEBlyL8Nn7SI4qLKDsJlgGZm+5oxjXQPQ5RkNV2hED6tx/LpCgWpbXSyr4wNHES2W1Z5bFHS3uMS23hREXcB+Y6d4ba/ZBbLeMLMJyetKYbTT7B/mdCzlwZ9LYYBpzKDxvIJIFQbSgCKK0JQsjQdZ/XUe0cvxycnJzBPD0OYiMckeY3j9tiBNDsnOI4+95UbA66ghkK9urNx1Pd46W5Cad25SFhBkbLVBV7HyUKVjnFHDqG2UIj5yH89Ig9EumvsHtSwn63jtis3u8rzBnxl8tHi8weCq9R9KyBA/0j9Ak1Vpad0rhggQuTf/PCtVv+cC1VNsWZStL4U/SJSILOA10eDoHIRx6H+gw/XIsizq75AkvbjOHr67Ypxeze96XSXBxDsDhUxqlI+sRlH7yqWtnNzVj2TIwChPnfc3KUNVXgzv2H3zlybjlihZci3NqrKFmonWmpHbMK0+CZ8KnS6aCH39bxqonszg8TCTLRbsaW7ICOHcqmJdLqp9Ec/b+FdrvaNnpLTLrU+FuLzQ7hhGvGS/iAmjR0Ogju0ceClrbJE3goBVhYDhIrbnApZpsVuYWwY2ZQvOYakVg1pg4ICX5t9CsAAe6Zc28jGuawPbnq6iaLAz22qgqZcUhQQoWKcpwDquZd0o2dh6nJXMcFyJ68vGlI5MYaPFWZTSspEZ6skNd/1WyagftxqjquvBhEl2THUh1PfoPQK7yA7Twa+TRbJNiBFDH9Z6gZ/9JlPATmdEjK9n6KDmxVy3YFfjtW4u+qM/GIn9qVdmAikCWvoqAHKNBGVhCU5YpuH1glqbpIy9MuQqFF1Qr5JdB9ZeBNJPXFlV5uwVRJbnrf/0RJ2oghHgjxL4/kt1U71sZUd/dFDp2MqOFBrizthLuR+UeiEkBp02ER1UtioueHUc1c3IlUngwsI5dQCSQj2HizoWV/LOL6R1TGGnKmAh5k75H2HEITlUWnszWg7D4RF5L7bQxi9dapNkmvR489mCyX5B+03pu0uu0nxj6xbYUMPH+ckSicoFVRAwGcOvGiS1lC2Rxds9gLIdFVZzraQu4jbF9TerEX5CGvhDANYmj4d2QuoItLaaIw8+TFIjWzWbjJS0uJHK1aEh3pTpY7t9blYtRr1+ICVnrwHyzeXtbZCge6X1ru6wSok2y3ArBHRi1CbwPU16/9S6b9ZuBSw4UBanwBpMPoBuiqQcgEsZ/I0eTNaR8PZq9rgDWFjdVG8KGyXOf6eT3XCZDsPTcPGcHHXRz0Znj2gOkVSc42O0tkql0KfV4br/sIGVTlikGRCG7bDpB4+3cSW6FHAmTarbOZOOIlblU2yH2htW/5Q0HeO7/7yNkAWj9aa6gPpXROSPua9i3Tpo4nO/NMebWcOiuDpr61FfowE2JB1fUUfhvSdnSN4p3C+SyOWNgc6EdrVyP+JWB9qv3qtpYwyi97v7is6E1xDdu7G+yPDT82438D+wzVnguL3hh5L1Is8G5ApLVcTsCD3+ahMUQbHBEasEjul5t1HdaR4mw2l0RG0l9U6Kdct/THwyF6Wl4zLudKayILpgv+J5mn3quIV0tM2SIxSifhIP7ldDDNuaXzDO1h3SxFH9txmJPiptIHDkpMSJxZhocND+wnAjTrZy0YX1oVC0vLBip+PwuJDmXnoRJF6lh8Kjc0TwXOdrnawMDef7Z1KbS5Y4YNw73muFYq7HPgghNbxnDg55Yk2MWs/lxzrswAcMW3Y18xpiIMynPpAzlbf2sGXEgqFkG42ZbdRUvlVUXxnQIKlgKN4wfPXzVb3O4GBmLeb2kjKmf5gxyJvBkT7FH9cC+InFgPVAR986jjXQh+xWw0qS3iuCdUxZgT/s5ZEWmryE/JE+Qj7+S4UA5/5S0uM435LLRqgN1RRqmnq/S87j0EL22iwbmBunMV8aduEVtqu57shYHkznW8JIz8mdrcaaWNaXNFTLyeHNxHkD2iPwEqbE1m5hscqCGn9pp5RvvxtdQWCD7RmgFOblPCw6xGPw1F41cjf6y/mNU33fvsBQ1J3xqvQbLBreiFMSBWUXAA/yeC+zScczGAAcbJvwXOSSPN7pC9D0QLPD4iSvK4CDBfYC3HkGeCtcuE0V8Cn2XptynGz+CkcpsCDGFeIYKHhhAQlamxD28EfSuRxY+zTQnMJiXmHEPp/uCIRPi6ICXH05hPbmhCjcEU6tkwb4zNlkIyhq/+lfjadV1Z0KUNS3cBHIYDQytrA8q0U82LxG1bOMhaJ1sODshTA3bz+EDBsvpWjhCMqtLm4NtjFwd2PqbNXQNxyumRoyIXLexqUyTGjeMUTa9lMy0gFbQ3Udzf2z0xYN1/9I0sw3uEAPcJxDYbpKwQghtZucnNdWXUHUxhmlK5FJCF8F167zWhp91FULYSVMQTvqGAD+As8Bsj2bcTLXlg8JdURA05tirc884JApH2ELGOLvfJq1RDonOMmWaV4V0DgirO6yGSGtsbekwot05ESkeL9WdjzwAgeCxaZSu+uU6qmeE9NFXZY0wo9/b+6MjdGq2vm04sKx+Qw03/C/mxq0AAEhYu0BqpS1toE3eudcXr5yGrO13xjYKgvySXTiKHhmFe5GCZ+nLfCQfrmwLiGXFNjnEDLFWCBfnQgcuP6ri2jFeIxBd0kVW6314J0Q4yz0//3joripSi4oQfZQSyO9BcMBiDl7j9KRPdRhdWCZVJsXM027uKG9wJZCFSAvV2Et1db2OinfcpcYoj8JXcYJIIz8m07tFh1kCpAPBRG1TuN7Q6MqUQ3cBCLpArpTgrG/JQqIctXD6kzp/uFmgGCZB0p3XdxSyc0Clt1J45B28tNyzULaafX61QPhDq3qr9Pw3aH02PFusGptG9n/1z/ckZiW0zDeADlfBgd+Y9vX5htgZYSp5CuROtKi/n/kwgVSbMCRzDXxyhLaMNJo8RDRjrCJoC9mwvWsm27Xw1MxN5i38YqsxjQQgELnoyR0pwqfFtNhxeC25Mqrs5LaTSSxnB9WbAXss2m1+A6oPIe1TwTK0rtojeYLOeksuVu/jgE6ku8SgJXKDN8EOwe+Fwh/+1k/3piga++dUq2hUGx4mJ9HgtlBtC5hPnJnoCmMo7mJh/WlqKRGCE1sPlqspiFzGAL09shtqhZQqQ8WPGa1X03pClF7nCGhB4PMNe4VFGoKjs3QvS9iTpx6X8gPTlSpILphn+lCTDYPIl1UP4BvU7G+OM67Ev5jbVEyxZmxoGezERTu5eyWEIhmd0VM+dWaiQTwi/kjUcI5hiqWq3YBwmgeJT7uOjOUNMwVxCOVYOC7gnDHLl8r3G/7DPbCXO/dZQvJSxkt7vWfocOTGr5VmKSF0e0DSoRfP1ugmueuA4cRQ6IrX5dxRUBu4dTZCzbZBSl9eXVDzEc85MAwdLAmFTv00EeIUodRIrt6sjyqIwhN/TqdMCP/I7E+LCyEss12m3tn8Ye9IGEMQvskMzp3weDP1SIgZmEVc8Im8vnLGuHpfutzmwYUnV+3q9hsGWZyxPYCAQn5IdYbNYKeu2nHj6gjunUIe080zaJD//K6GQ65Tx44osl0/iPXF2iLvdb2ICyS00qo/5HO/iKIzvQxIxAa1PTiqUix3aWlkSlkExZFMtfmBGKHkcPh652fyFyVD+HV+OqErQQrWDI7si6qH3rccD75chgXU2iU3F0maIlUnuwqzxC/bT9Dnzu2sTPI5vNkEYgkXI8IL+HBZejWY1kX8DFlOA/CWledT9nz9qFqXGHe4NT+qYMcnU7N1x6PuHMkGJYqA9iVNEMMCbC5saxaIiAKtzPl2S9Ngfmig51pnQVqTnmlWSYDuZhUQi0TfQpX5+TBP9Aff1+uQTSxhQBaGbKWglFE+y8jhfEpECfXZH3Gr6UgWG9R4p9rOCiz0EZtCGinRY9Wq4nUFHoHLfbsihbKYVx6CYdsE7SJYDpW1W+RZYkCahoIc4LpvLTbqgP+IJwb7zqSVWeg3NHaKjPqM0LgoxiujQGFHafZhCFHVDrLNm+v0MBbNahkwvIEfQe6ii0gJ6Q9XEvBeGXlU/RrqDh3QN/4yQnI4J1fLR/A/o+nCYZAb35GWTenXammFrOBhEmWMpd5TAjMP+alZWlXxQoGEGhFgAyruFrjHeaB4jc3ZBsyqde4XEd3ZLiQhmLp1CZ7DU9j4GVknW9LwWGS7Y5U7DUceJzHmAvbWAreDFZKLTKIWs2c2iFOhzkNxGDXQXGn9xfUSWe3NjADbDfd+NCzKpgqp6ashAl1ul+cfSHlonPijGyIrJ0qbFAiXRMckn/+9pSgA+S7QiAFYkib5u9/u5A7nrEYV2ZePilwRtsYyNY7I55Zgik1vVosGg2ITgCwE1zxLk6C/5xLAh8sYqP1zPGM58fRlC2iXnS9Ic+qoMKhYh26hV+GZsw92NUInf8d2FJCjNRJYaGD86hOEoKrq/rSINb8DEUX0T7D0wrjlCSHqF05abPwDjd9Pi/bFMlsTyvwBTcASf1ChgKfFY8mno9oPtH7/grUnT3U26x1wKRPUcDfYd2OpGa8fRb1mekBdxDZjNBegnJGLSAILmhKm8FKJrCrBmmr5heFOxt3Uj4Wi+co6KCqqGUBQZNpk52c0VHzRf8j0nhrqndC+jaYFvEwy7S5k2VOiCn/u8jBjcLFOub7R6EDYqlAXxT2a491eSd6gVE5r7aGHOQqyqA+VhTigfon9/N9BW7bEgPxo3LrYhhU06ZqLY6uVMEiwkvwE0eEKE5tmrdF1cORHZzgMxtij8SGyqj7TnhmbY2RQYwyis6Kn6Xu+HrNpoRwOdtMIipmQAKJUHvXVUVKRBOYn3ZWUk3h65A0d770TzD6TBAa9/UM9SytcWswyj9GZOEpXyhdUX2Wn/7JZ5VvYaWsi/rlWDr4//y6HBqZAieK7Tm2RZAODETN4nkaEaF3XdcdqJRm9SsIvZmqT3RcBbSErK07xF7LGdfq7nXliAaZkV7Bq0dYhex3R81bqjby/fQbDRz83S/JoV4mIUCC7VTksAHQiCiUhaD+41sQjNKs2XlgDbZwoquItYgFCuXVFyswVoFppDaSF3v3lPGTFXu3POaqrH0JXCKgRdqDyovpjk6Iu48/4UFnM8Go/kl99Kszo9SMb8ZwSmEZ7Q9dzcgSIomcrcwmt2ARVLiFeJ1sxHavdu7dTQQ20F1kD1gN93g1okckTM78+8ccbXUhOV0Wv2Z0hF/SWfVaEWrzwvo8flWoI6U+lSqtkbDAPsSsOJwKIn+Vxtu8O0YhfHSmGGP5GpCxfSYmOUt0PljYtk9Pac5oPgjvtTdRK77qWZXVFI73iHYC4ThHZFCrKjDai8WcOmUC9YyKA3z6Vjh5F/oWfaqWjrHA/QalJD6tiLLfPjKKchwJz9N1JjmL6BYI3Z3zO2c/Dh30SOiv35yV7zDEQQkmSEEBHsWM44ynNnZco5DGAaq3BT9/GELM3bOxZgt3DuXaqJEpmqDIc+KH4aXUrWgIZwIpH99HDyZcUpUdu0Hirj/D7hGc7edckC2VmNbSNGYtqtXjCEuZ83T4ejas1rTYgu27LP3k9IDMuzPm+YZbVnNxI7si0hHbdN/m7sVlH1Dw07HxyHB/M6u139A1EhiAs3n5f3GUomucWxLb0D0kz2Nbsfn3C5fB6NXyxTKqSAPfIlHF6FfLIqMCv7a8ID5WGDOuqD7FHjPRxfQQpmaVZF+jcpVq+8wdNJPBdlocEn07PQmboFF1S5a0jkRfkHW8UYZYcykwzicZ4rQK6kHtKib8joaa3QLK3jLW/rhifuUOXKUvBiOMgj39+xvEURaZh4qmFu8bzrivU/pnsagvrk5Je8+Srof3D0csF0vFZgcp9xZyBwMf4VOyoSGGoVXfFr2UXVCeoG/JvYZNIMJRfPfP06aUngtInfTfjGA/W3j6xjCuIbFaDFTYiTTzkR70orUQKMu2MV0z8R3k0dr01+O4WADwDtjrDAA826ijQ2u/teHgD9GIRLfSMspKESziOuTNCF/zXKJzidAN3Y4C0y0pCMmc1zdrTuOaS7R5XYSvMy/LWfXBbtqhwJ3wkr+hfp/sygc5OXLqpom29RyHUGaCIPxQ2cHMSJBLQwI3P2tt3JCaP3c0Sk9w+uLT8zN4Dnu+4IiJojg/jy+MvOBnGRgrMxLSsP0HCcRZuYoXVtutMci5uT4m8p9S7R6a4YdXXOCnjsIOtvA2tWxypIKut/osYQsAZ4DIGqgF6KFmu0GYKwF/kUF431khD7YT4m9fN7abXc97CxLlftib96DjDYfudu5178EayDguTmi694WcvYMfsGv/saaMXfXwr6Mx31myUGGxJv8nSQqV7Kb93inAYEIil3NlivfGnuNZnxOlz0B7ol8cEW34RiyjnBEdLulRQYyhloyr0JvwuAXPxpPVZeg8bwCx+dZjp74XVDMD6JWnoz1wn317pDMqJXLljYOB/T/s8lTNEtiGNDqH6LUiZcSfcICVL7S8R0apLeRIc63NjOVSW2YfAoD5GtghkCSAukiF2wR0VNS3WwcpO8UVef2MIUxAE6P/n6cK1f/wkcN2cDbN424ywQJPp3wQrUgVzA/rI5lbPPKsq1RWh28/V8Cxh4xolf67RQlywPzy1jYl0u9kuocuhliUX5lzSo3K76NvPPEqLig7+wa2iAFKt79juQFzgdGkx7MjuLSn80CUpCT/R/UppBeLPFOWhnghkhmiPUflp7dMDdg9D2vjGYqFfpTYHBZR4cY+682DlBxgmi1cg3z5yoP534NP/8ArpcRyklh5COLOdVWnh1CONH/lhWbqneTe7xswjRMQlZRdxfSKXevTSZIewxBc85UfCLusz9dePS/qo/JBnDRyS3aLrgvy0AhDMIIFggYJKoZIhvcNAQcBoIIFcwSCBW8wggVrMIIFZwYLKoZIhvcNAQwKAQKgggTuMIIE6jAcBgoqhkiG9w0BDAEDMA4ECBxFFNHPPCMnAgIIAASCBMiVCMZD66do3DOwgJ7uBU2fGDn8kceF/uINckmnwBDlEiHUQ+uQ3DMsIUIwysy/QoMr4NHfZbmcCwhST8rt3P6hTLICi/+EcFF/XjNR104PC7+vu0WbssQnMFqzXITC2PSaGQtIFJASGCONu5jUN3Mrp5aOk4Wk2KJtxaTr0DObVvmu8VtDGlk/g3fvcN5KSAbCVnJAXmZgEU88rEZZLO+to2uXvrCdjBHqFrNYtdu1L8tHwvExXpPzEYKV+3Jw/SoplwEvdB/pDIYE6t/p52mmaWSuxlBUnNUzhjCGv5+4qDVsMQHdgsqQ/HjGwSpFw0zMso+YQggIsKJpQxIHgv6fTKqZiZ2qcSqVLJ0prnd42GXa6TXh8wrqWhTYOSSVn1c8uqZSCamxhhGfwKCZYG/v1w25PaZ+8pnq7Qz6DV/gHCGfgKTpz454Zq+pYkttGZET3/a96vps2tK95RqPiywXzr1gGiy/1xBmammgCgmBBc8k36MWMLp8NaeqxDs4owVIgdfuBhYwxlINIkzTl5wLRMuXrQZxeKXiZyzoC8vF2bML7pMcT/ohvGFIYAULRE54Xz4CEhROVYDaBUVfqR6IG40rzSa6rKw8X6pjQ2rxB9Fi4bo1/m6efDHPgGRZ/RlRIx0aByxblnsNZckVZnR8ZrdRFt9EWseAM9H+LKZAPPuEo6GqWcLoPz5Y+63hu/r9Xz8/xRjdyuQBQe/r2p7LEC0wOzKXm5GWE1pQdylIjeCclo2b3gWiCw8cZ76G/zwhC9cPzUTW+7qmWXlzaVSuEu85W6iYK38PxgJPupWExQogDnkC3uo0Zr4wTRMP3NeIpPSDaO2gylq3jyHM1uxmCj/trwJscczdkUtlf+7Y4OgnOcLEJVXssP3HlUGcr4DvEg24xnAr0hC/gojUuI0OlzwijNWdOMewzn45rhXRplqqJ/A7u6qaw5APkKKBWHenFT7lwZ6baeSgro2WZhglxjC5JaqGRWvso04j2M6MIT524lUIJnLKSEXwabfjonBzcsWLmIBbcAYpqsCna1yCQhyMUICFztttwYLy3momP5MWGnp19yD1wm++fPYytgiEEaR/V+kIJmibHA42JDQzPpc1PrKqRBBPgm2E/ndyF3yvkqOQrV781Vv9mFy5nde0LBZmiqA+/uRWl9iMOCzJdtx8b+Z+y5/W9GGyKPTDbQeiQvwMXuwwUES70/KG4Qj1VHgoNkvlQ7m/I0eaYDZ6jvwh/enGj+Na0pVfSa2QZt38vazzHeq5VUZf4qyvMaIq9libdFObuMUgY69p3qPOtskC1SPjrdcXG/Bj9l4Q4K/uJVqCHcO3MwJsTQKzEoDNJNRO9G9POl6XS86khRNkSBnv8zQ5c4kDDuqZERN3bJJdxhTWgt1j2Jr6bhMH38Q8JMzcJu7i2wPVjgsxImKfAGkXNfSK5NymvJ6mHlN3Q8A2eZmzXd5ZsuPNjUaYsiwImTDM7s9uMIEDqL7xt9lNYRoIltGkThUoG0xeXzVXpA23zzh7ibfklLZ5lEj26w7gkaBFn0nUw/ED/oO/mKjyvjTLC/3zw0gb4n5yB44SapKXFKHI8IWnhz0cdHn20wxVqk/z7+/tAlGhh0BF1RDRvKo8p9il+T4xZjAjBgkqhkiG9w0BCRUxFgQURf9WMIPy1+XjxMffD3eG2eQrHFcwPwYJKoZIhvcNAQkUMTIeMAAgAFAATABBAFQAQQBGAE8AUgBNAEEAIABDAE8ATABPAE0AQgBJAEEAIABTAEEAUzAxMCEwCQYFKw4DAhoFAAQUHFLi7L4VwY2fDHCURPed3CscD4IECLpJ7jLHI3U2AgIIAA==",
			"password" => "900013664"
		);

		$data = json_encode($data,JSON_PRETTY_PRINT);

		$params                   = [];
		$params['request_url']    = "http://192.168.8.2/apidian2020/public/api/ubl2.1/config/certificate";
		$params['request_method'] = "PUT";
		$params['Authorization']  = "Authorization: Bearer $token";
		$params['data']           = $data;

		$respuesta = curlApi($params);
		$respuesta = json_decode($respuesta,true);

		return $respuesta;
	}

	function curlApi($params){
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
?>
