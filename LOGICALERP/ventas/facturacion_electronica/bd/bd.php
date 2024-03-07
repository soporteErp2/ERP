<?php
  include("../../../../configuracion/conectar.php");
  include("../../../../configuracion/define_variables.php");
  include("../../config_var_global.php");

  $id_host     = $_SESSION['ID_HOST'];
  $id_empresa  = $_SESSION['EMPRESA'];
  $id_sucursal = $_SESSION['SUCURSAL'];
  $server_name = $_SERVER['SERVER_NAME'];

  //VERIFICAMOS SI VAMOS A USAR UN DOMINIO DE PRUEBAS O PRODUCCION
  if($server_name == "logicalerp.localhost"){
    $dominio = "http://fst.facse.net/";
  }
  else{
    $dominio = "https://web.facse.net:444/";
  }

  switch($opc){
    case 'buscarFacturaElectronica':
      buscarFacturaElectronica($fecha_inicio,$fecha_fin,$estado,$notificaciones,$dominio,$id_empresa,$mysql);
      break;

    case 'reenviarCorreoFacturaElectronica':
      reenviarCorreoFacturaElectronica($numeroDocumento,$correo,$archivos,$dominio,$id_empresa,$id_sucursal,$mysql);
      break;

    case 'reenviarDianFacturaElectronica':
      reenviarDianFacturaElectronica($documentos,$dominio,$id_empresa,$id_sucursal,$mysql);
      break;

    case 'reenviarClienteFacturaElectronica':
      reenviarClienteFacturaElectronica($numeroDocumentos,$dominio,$id_empresa,$id_sucursal,$mysql);
      break;
  }

  function reenviarClienteFacturaElectronica($numeroDocumentos,$dominio,$id_empresa,$id_sucursal,$mysql){
    //SEPARAMOS LOS DOCUMENTOS PARA LA CONSULTA
    $documentos = substr($numeroDocumentos, 0, -1);
    $documentos = explode(",",$documentos);

    $whereDocumentos = "";
    foreach($documentos as $key){
    	//VERIFICAMOS SI EL DOCUMENTO ES UNA DEVOLUCION
	    $texto = strpos($key,"DV");

	    if($texto !== false){
	    	$texto = substr($key,5);

	    	if($whereDocumentosDV == ""){
	        $whereDocumentosDV = "DV.consecutivo = $texto";
	      }
	      else{
	        $whereDocumentosDV .= " OR DV.consecutivo = $texto";
	      }
	    }
	    else{
	      if($whereDocumentosFV == ""){
	        $whereDocumentosFV = "VF.numero_factura_completo = '$key'";
	      }
	      else{
	        $whereDocumentosFV .= " OR VF.numero_factura_completo = '$key'";
	      }
	    }
    }

    if($whereDocumentosFV != ""){
	    // BUSCAMOS LA FACTURA A ENVIAR Y LAS DIRECCIONES DE CORREO
	    $sql = "SELECT VF.numero_factura_completo,VF.UUID,VF.email_fe,TDE.email
	            FROM ventas_facturas AS VF
	            LEFT JOIN terceros_direcciones_email AS TDE ON (TDE.id_direccion = VF.id_sucursal_cliente AND TDE.activo = 1)
	            WHERE VF.activo = 1
	            AND VF.estado = 1
	            AND VF.id_empresa = $id_empresa
	            AND ($whereDocumentosFV)";
	    $query = $mysql->query($sql,$mysql->link);

	    if($mysql->num_rows($query) == 0){
	      echo "<script type='text/javascript'>
	              window.close();
	              alert('No es posible enviar los documentos.');
	            </script>";
	      exit;
	    }

	    while($row = $mysql->fetch_array($query)){
	      if($arrayDocumentos[$row['UUID']]['email'] == ""){
	        $arrayDocumentos[$row['UUID']]['email'] = ($row['email'] != "")? $row['email'] : $row['email_fe'];
	        $arrayDocumentos[$row['UUID']]['numero'] = $row['numero_factura_completo'];
	      }
	      else{
	      	$arrayDocumentos[$row['UUID']]['email'] .= ",";
	        $arrayDocumentos[$row['UUID']]['email'] .= ($row['email'] != "")? $row['email'] : $row['email_fe'];
	        $arrayDocumentos[$row['UUID']]['numero'] = $row['numero_factura_completo'];
	      }
	    }
	  }
	  
	  
	  if($whereDocumentosDV != ""){
	  	// BUSCAMOS LA FACTURA A ENVIAR Y LAS DIRECCIONES DE CORREO
	    $sql = "SELECT DV.id_sucursal,DV.consecutivo,DV.UUID,VF.email_fe,TDE.email
	            FROM devoluciones_venta AS DV
	            LEFT JOIN ventas_facturas AS VF ON DV.id_documento_venta = VF.id
	            LEFT JOIN terceros_direcciones_email AS TDE ON (TDE.id_direccion = VF.id_sucursal_cliente AND TDE.activo = 1)
	            WHERE DV.activo = 1
	            AND DV.estado = 1
	            AND DV.id_empresa = $id_empresa
	            AND ($whereDocumentosDV)";
	    $query = $mysql->query($sql,$mysql->link);

	    if($mysql->num_rows($query) == 0){
	      echo "<script type='text/javascript'>
	              window.close();
	              alert('No es posible enviar los documentos.');
	            </script>";
	      exit;
	    }

	    while($row = $mysql->fetch_array($query)){
	    	if(strlen($row['id_sucursal']) == 1){
	        $codigoSucursal = "0" . $row['id_sucursal'];
	      }
	      else{
	        $codigoSucursal = $row['id_sucursal'];
	      }

	      if($arrayDocumentos[$row['UUID']]['email'] == ""){
	        $arrayDocumentos[$row['UUID']]['email'] = ($row['email'] != "")? $row['email'] : $row['email_fe'];
	        $arrayDocumentos[$row['UUID']]['numero'] = "DV" . $codigoSucursal . " " . $row['consecutivo'];
	      }
	      else{
	      	$arrayDocumentos[$row['UUID']]['email'] .= ",";
	        $arrayDocumentos[$row['UUID']]['email'] .= ($row['email'] != "")? $row['email'] : $row['email_fe'];
	        $arrayDocumentos[$row['UUID']]['numero'] = "DV" . $codigoSucursal . " " . $row['consecutivo'];
	      }
	    }
	  }
	  
    // BUSCAMOS CODIGO DE LA SUCURSAL
    $sql = "SELECT UUID
            FROM empresas_sucursales
            WHERE activo = 1
            AND id_empresa = $id_empresa
            AND id = $id_sucursal";
    $query = $mysql->query($sql,$mysql->link);
    $UUIDSC = $mysql->result($query,0,'UUID');

    if($UUIDSC == "" || $UUIDSC == null){
      echo "<script type='text/javascript'>
              alert('No es posible encontrar la sucursal.');
            </script>";
      exit;
    }

    //============================ ENVIAR CORREOS ============================//
    //CONSTRUIMOS EL ARRAY PARA CONSULTAR EL API
    foreach($arrayDocumentos as $uuid => $contenidos){
      $json = array(
                      "IdDocumento" => "$uuid",
                      "IdEmisorSucursal" => "$UUIDSC",
                      "Correo" => "$contenidos[email]",
                      "ListadoAdjunto" => ""
                    );

      //CONVERTIMOS EL ARRAY EN JSON
      $datos = json_encode($json,JSON_PRETTY_PRINT);

      //API A CONSUMIR
      $dominio = "https://web.facse.net:444/";
      $url_api = $dominio."api/Facse/EnvioCorreo";

      //CONSUMIMOS EL API
      $respuesta = consumirApi($url_api,"POST",$datos);

      // VALIDAMOS LA RESPUESTA DEL API
      if($respuesta["Respuesta"] == true){
        $mensajeAlert .= "$respuesta[Mensaje] -- $contenidos[numero]\n";
      }
      else if($respuesta["Respuesta"] == false){
        if($respuesta["Mensaje"] == "Ha ocurrido un error al enviar el correo"){
          $mensajeAlert .= "Ha ocurrido un error al enviar el correo -- $contenidos[numero]\n";
        }
        else if($respuesta["Mensaje"] == "Por favor valide la información e intente nuevamente"){
          $mensajeAlert .= "Por favor valide la información e intente nuevamente -- $contenidos[numero]\n";
        }
        else if($respuesta["Mensaje"] == "No se encontró el documento informado, si el error persiste contactar con el administrador"){
          $mensajeAlert .= "No se encontró el documento informado, si el error persiste contactar con el administrador -- $contenidos[numero]\n";
        }
        else{
          $mensajeAlert .= "$respuesta[Mensaje]\n";
        }
      }
    }

    echo $mensajeAlert;
  }

  function reenviarDianFacturaElectronica($documentos,$dominio,$id_empresa,$id_sucursal,$mysql){
    //SEPARAMOS LOS DOCUMENTOS PARA LA CONSULTA
    $documentos = substr($documentos, 0, -1);
    $documentos = explode(",",$documentos);

    $whereDocumentos = "";
    foreach($documentos as $key){
    	//VERIFICAMOS SI EL DOCUMENTO ES UNA DEVOLUCION
	    $texto = strpos($key,"DV");

	    if($texto !== false){
	    	$texto = substr($key,5);

	    	if($whereDocumentosDV == ""){
	        $whereDocumentosDV = "DV.consecutivo = $texto";
	      }
	      else{
	        $whereDocumentosDV .= " OR DV.consecutivo = $texto";
	      }
	    }
	    else{
	      if($whereDocumentosFV == ""){
	        $whereDocumentosFV = "VF.numero_factura_completo = '$key%";
	      }
	      else{
	        $whereDocumentosFV .= " OR VF.numero_factura_completo = '$key%";
	      }
	    }
    }

    if($whereDocumentosFV != ""){
	    // BUSCAMOS LA FACTURA A ENVIAR Y LAS DIRECCIONES DE CORREO
	    $sql = "SELECT VF.UUID
	            FROM ventas_facturas AS VF
	            WHERE VF.activo = 1
	            AND VF.estado = 1
	            AND VF.id_empresa = $id_empresa
	            AND ($whereDocumentosFV)";
	    $query = $mysql->query($sql,$mysql->link);

	    if($mysql->num_rows($query) == 0){
	      echo "<script type='text/javascript'>
	              window.close();
	              alert('No es posible enviar los documentos.');
	            </script>";
	      exit;
	    }

	    //CONSTRUIMOS EL ARRAY PARA CONSULTAR EL API
	    $cont = 0;
	    while($row = $mysql->fetch_array($query)){
	      if($row['UUID'] != null || $row['UUID'] != ""){
	        $json['ListadoDocumentos'][] .= $row['UUID'];
	        $cont++;
	      }
	    }
	  }
	  
	  
	  if($whereDocumentosDV != ""){
	  	// BUSCAMOS LA FACTURA A ENVIAR Y LAS DIRECCIONES DE CORREO
	    $sql = "SELECT DV.UUID
	            FROM devoluciones_venta AS DV
	            WHERE DV.activo = 1
	            AND DV.estado = 1
	            AND DV.id_empresa = $id_empresa
	            AND ($whereDocumentosDV)";
	    $query = $mysql->query($sql,$mysql->link);

	    if($mysql->num_rows($query) == 0){
	      echo "<script type='text/javascript'>
	              window.close();
	              alert('No es posible enviar los documentos.');
	            </script>";
	      exit;
	    }

	    //CONSTRUIMOS EL ARRAY PARA CONSULTAR EL API
	    $cont = 0;
	    while($row = $mysql->fetch_array($query)){
	      if($row['UUID'] != null || $row['UUID'] != ""){
	        $json['ListadoDocumentos'][] .= $row['UUID'];
	        $cont++;
	      }
	    }
	  }

    //CONVERTIMOS EL ARRAY EN JSON
    $datos = json_encode($json,JSON_PRETTY_PRINT);

    //API A CONSUMIR
    $dominio = "https://web.facse.net:444/";
    $url_api = $dominio."api/Comunicacion/ReenviarDocumentos";

    //CONSUMIMOS EL API
    $respuesta = consumirApi($url_api,"POST",$datos);

    for($i = 0; $i < $cont; $i++){
      if($respuesta['Respuesta'] == true){
        $numeroDocumento = $respuesta['Contenido']['ListadoRespuesta'][$i]['Documento'];
        $textoDocumento  = $respuesta['Contenido']['ListadoRespuesta'][$i]['Respuesta'];

        if(strpos($textoDocumento,"Documento enviado") != false){
          $mensajeAlert .= "El documento $numeroDocumento se ha enviado.\n";
        }
        else if(strpos($textoDocumento,"Documento no enviado, Ya cuenta con env") != false){
          $mensajeAlert .= "El documento $numeroDocumento no se ha enviado. Ya cuenta con envio.\n";
        }
        else if(strpos($textoDocumento,"se encuentra Aceptado") != false){
          $mensajeAlert .= "El set de pruebas se encuentra aceptado. -- $numeroDocumento.\n";
        }
        else if(strpos($textoDocumento,"se encuentra Rechazado") != false){
          $mensajeAlert .= "El set de pruebas se encuentra rechazado. -- $numeroDocumento.\n";
        }
      }
      else if($respuesta['Respuesta'] == false){
        $mensajeAlert .= "$respuesta[Mensaje]";
      }
    }

    echo $mensajeAlert;
  }

  function reenviarCorreoFacturaElectronica($numeroDocumento,$correo,$archivos,$dominio,$id_empresa,$id_sucursal,$mysql){
    //VERIFICAMOS SI EL DOCUMENTO ES UNA DEVOLUCION
    $texto = strpos($numeroDocumento,"DV");

    if($texto !== false){
      $texto = substr($numeroDocumento,5);
      // BUSCAMOS LA DEVOLUCION A IMPRIMIR
      $sql = "SELECT UUID
              FROM devoluciones_venta
              WHERE activo = 1
              AND estado = 1
              AND id_empresa = $id_empresa
              AND consecutivo = $texto";
      $query = $mysql->query($sql,$mysql->link);
      $UUID = $mysql->result($query,0,'UUID');
    }
    else{
      // BUSCAMOS LA FACTURA A IMPRIMIR
      $sql = "SELECT UUID
              FROM ventas_facturas
              WHERE activo = 1
              AND estado = 1
              AND id_empresa = $id_empresa
              AND numero_factura_completo LIKE '%$numeroDocumento%'";
      $query = $mysql->query($sql,$mysql->link);
      $UUID = $mysql->result($query,0,'UUID');
    }

    // BUSCAMOS CODIGO DE LA SUCURSAL
    $sql = "SELECT UUID
            FROM empresas_sucursales
            WHERE activo = 1
            AND id_empresa = $id_empresa
            AND id = $id_sucursal";
    $query = $mysql->query($sql,$mysql->link);
    $UUIDSC = $mysql->result($query,0,'UUID');

    if($UUIDSC == "" || $UUIDSC == null){
      echo "<script type='text/javascript'>
              alert('No es posible encontrar la sucursal.');
            </script>";
      exit;
    }

    //CONVERTIMOS EL ARRAY CON LOS ARCHIVOS EN BASE 64
    $archivos = json_decode($archivos,TRUE);

    //============================ ENVIAR CORREOS ============================//
    //CONSTRUIMOS EL ARRAY PARA CONSULTAR EL API
    $json = array(
                    "IdDocumento" => "$UUID",
                    "IdEmisorSucursal" => "$UUIDSC",
                    "Correo" => "$correo",
                    "ListadoAdjunto" => $archivos
                  );

    //CONVERTIMOS EL ARRAY EN JSON
    $datos = json_encode($json,JSON_PRETTY_PRINT);

    //API A CONSUMIR
    $dominio = "https://web.facse.net:444/";
    $url_api = $dominio."api/Facse/EnvioCorreo";

    //CONSUMIMOS EL API
    $respuesta = consumirApi($url_api,"POST",$datos);

    // VALIDAMOS LA RESPUESTA DEL API
    if($respuesta["Respuesta"] == true){
      $mensajeAlert .= "$respuesta[Mensaje] -- $numeroDocumento\n";
    }
    else if($respuesta["Respuesta"] == false){
      if($respuesta["Mensaje"] == "Ha ocurrido un error al enviar el correo"){
        $mensajeAlert .= "Ha ocurrido un error al enviar el correo -- $numeroDocumento\n";
      }
      else if($respuesta["Mensaje"] == "Por favor valide la información e intente nuevamente"){
        $mensajeAlert .= "Por favor valide la información e intente nuevamente -- $numeroDocumento\n";
      }
      else if($respuesta["Mensaje"] == "No se encontró el documento informado, si el error persiste contactar con el administrador"){
        $mensajeAlert .= "No se encontró el documento informado, si el error persiste contactar con el administrador -- $numeroDocumento\n";
      }
      else{
        $mensajeAlert .= "$respuesta[Mensaje]\n";
      }
    }

    echo $mensajeAlert;
  }

  function imprimirFacturaElectronica($numeroDocumento,$dominio,$id_empresa,$mysql){
    //VERIFICAMOS SI EL DOCUMENTO ES UNA DEVOLUCION
    $texto = strpos($numeroDocumento,"DV");

    if($texto !== false){
      $consecutivo = substr($numeroDocumento,5);
      $id_sucursal = substr($numeroDocumento,2,3);

      $texto_sucursal = strpos($id_sucursal,"0");

      if($texto_sucursal !== false){
      	$id_sucursal = substr($id_sucursal,1);
      }
      else{
      	$id_sucursal = $id_sucursal;
      }

      // BUSCAMOS LA DEVOLUCION A IMPRIMIR
      $sql = "SELECT UUID
              FROM devoluciones_venta
              WHERE activo = 1
              AND estado = 1
              AND id_empresa = $id_empresa
              AND id_sucursal = $id_sucursal
              AND consecutivo = $consecutivo";
      $query = $mysql->query($sql,$mysql->link);
      $UUID = $mysql->result($query,0,'UUID');
    }
    else{
      // BUSCAMOS LA FACTURA A IMPRIMIR
      $sql = "SELECT UUID
              FROM ventas_facturas
              WHERE activo = 1
              AND estado = 1
              AND id_empresa = $id_empresa
              AND numero_factura_completo LIKE '%$numeroDocumento%'";
      $query = $mysql->query($sql,$mysql->link);
      $UUID = $mysql->result($query,0,'UUID');
    }

    if($UUID == "" || $UUID == null){
      echo  "<script type='text/javascript'>
              window.close();
              alert('No es posible generar el documento pdf.');
            </script>";
      exit;
    }

    //======================== CONSULTAR PDF DOCUMENTO =======================//
    //API A CONSUMIR
    $dominio = "https://web.facse.net:444/";
    $url_api = $dominio."api/Pdf/GenerarPdf/$UUID";

    //CONSUMIMOS EL API
    $respuesta = consumirApi($url_api,"GET","");
    $respuesta['endpoint_facse'] = $url_api;
    return $respuesta;
  }

  function buscarFacturaElectronica($fecha_inicio,$fecha_fin,$estado,$notificaciones,$dominio,$id_empresa,$mysql){
    //CONSULTAR TOKENS DE LA EMPRESA
    $sqlEmpresa  = "SELECT documento,client_token,access_token
                    FROM empresas
                    WHERE id = $id_empresa
                    AND activo = 1";
    $queryEmpresa  = $mysql->query($sqlEmpresa,$mysql->link);
    $documentoE    = $mysql->result($queryEmpresa,0,'documento');
    $client_tokenE = $mysql->result($queryEmpresa,0,'client_token');
    $access_tokenE = $mysql->result($queryEmpresa,0,'access_token');

    if($id_empresa == "1" || $id_empresa == "47"){
    	$documentoD    = "900013664";
	    $client_tokenD = "479c2f39dbc894c8f0dbf5d277f3fff98603fa3fb0e63c6b05746ad7a38d074e";
	    $access_tokenD = "a32a4a336351155927116acae1b74750d8cacd9654a7ce7af7d6b609f39428c7";	
    }
    else{
    	$documentoD    = "900467785";
	    $client_tokenD = "ec51e51436a3ab27cc8b7e1c2ddbb00bd8a442302b8e90a0b2476e629da960dd";
	    $access_tokenD = "74d0c995224dbcbec27458d4d0eb7cff0a4c2856e42ca16e88ba9320f2335230";	
    }

    //===================== CONSULTAR DOCUMENTOS ENVIADOS ====================//
    //CONSTRUIMOS EL ARRAY PARA CONSULTAR EL API
    $json = array(
                    "IdentificacionDistribuidor" => "$documentoD",
                    "AccessToken" => "$access_tokenD",
                    "ClientToken" => "$client_tokenD",
                    "FechaInicio" => "$fecha_inicio"."T00:00:00.3304746-05:00",
                    "FechaFin" => "$fecha_fin"."T00:00:00.3304746-05:00",
                    "IdentificacionEmisor" => "$documentoE"
                  );

    //VERIFICAMOS SI EL FILTRO DE ESTADO BUSCARA TODOS LOS DOCUMENTOS
    if($estado != "todo"){
    	$json['EstadoDocumento'] = "$estado";
    }

    //CONVERTIMOS EL ARRAY EN JSON
    $datos = json_encode($json,JSON_PRETTY_PRINT);

    //API A CONSUMIR
    $url_api = $dominio."api/Comunicacion/ListadoDocumentos";

    //CONSUMIMOS EL API
    $respuesta = consumirApi($url_api,"POST",$datos);

    //RECORREMOS LA REPSUESTA DEL API
    foreach($respuesta as $key => $value){
      if($key == "Contenido"){
        $rowColor = "#ffffff66";

        foreach($value as $result){
          $contenidoDocumentos .= "<tr style='background-color:$rowColor;'>
                                    <td style='text-align:center;'><input class='documentosElectronicos' type='checkbox' value='$result[Prefijo] $result[Numero]'></td>
                                    <td style='text-align:center;'>$result[Prefijo]$result[Numero]</td>
                                    <td style='text-align:center;'>$result[IdentificacionReceptor]</td>
                                    <td style='text-align:center;'>$result[NombreReceptor]</td>
                                    <td style='text-align:right;'>$".number_format($result['Total'],$_SESSION['DECIMALESMONEDA'],",",".")."</td>
                                    <td style='text-align:center;'>
                                      <table width='100%'>
                                        <tr>
                                          <td width='50%'>
                                            <div align='center' title='Imprimir Factura' onclick='generarPDF(\"$result[Prefijo] $result[Numero]\")'><img src='../../../../temas/clasico/images/BotonesTabs/pdf16.png'></div>
                                          </td>
                                          <td width='50%'>
                                            <div align='center' title='Enviar Factura' onclick='reenviarCorreo(\"$result[Prefijo] $result[Numero]\")'><img src='../../../../temas/clasico/images/BotonesTabs/enviaremail16.png'></div>
                                          </td>
                                        <tr>
                                      </table>
                                    </td>
                                  </tr>";

          if($rowColor == "#ffffff66"){
            $rowColor = "#dedede";
          }
          else{
            $rowColor = "#ffffff66";
          }
        }
      }
    }

    if($notificaciones == "si"){
      //======================= CONSULTAR NOTIFICACIONES =======================//
      //CONSTRUIMOS EL ARRAY PARA CONSULTAR EL API
      $json = array(
                      "IdentificacionEmisor" => "$documentoE",
                      "ClientToken" => "$client_tokenE",
                      "AccessToken" => "$access_tokenE",
                      "FechaInicio" => "$fecha_inicio"."T00:00:00.3304746-05:00",
                      "FechaFin" => "$fecha_fin"."T00:00:00.3304746-05:00"
                    );

      //CONVERTIMOS EL ARRAY EN JSON
      $datos = json_encode($json,JSON_PRETTY_PRINT);

      //API A CONSUMIR
      $url_api = $dominio."api/Comunicacion/ListadoNovedadesNotificaciones";

      //CONSUMIMOS EL API
      $respuesta = consumirApi($url_api,"POST",$datos);
      
      //RECORREMOS LA REPSUESTA DEL API
      foreach($respuesta as $key => $value){
        if($key == "Contenido"){
          $rowColor = "#ffffff66";

          foreach($value as $listado => $result){
            if($listado == "ListadoNovedades"){
              foreach($result as $notificacion){
                $contenidoNotificaciones .= "<tr style='background-color:$rowColor;'>
                                              <td style='text-align:center;'>Aviso</td>
                                              <td style='text-align:center;'>" . utf8_decode($notificacion['DescripcionFacse']) . " -- $notificacion[Documento]</td>
                                              <td style='text-align:center;'>$notificacion[Fecha]</td>
                                            </tr>";

                if($rowColor == "#ffffff66"){
                  $rowColor = "#dedede";
                }
                else{
                  $rowColor = "#ffffff66";
                }
              }
            }
            else{
              foreach($result as $notificacion){
                $contenidoNotificaciones .= "<tr style='background-color:$rowColor;'>
                                              <td style='text-align:center;'>" . utf8_decode($notificacion['TipoNotificacion']) . "</td>
                                              <td style='text-align:center;'>" . utf8_decode($notificacion['Descripcion']) . "</td>
                                              <td style='text-align:center;'>$notificacion[Fecha]</td>
                                            </tr>";
                if($rowColor == "#ffffff66"){
                  $rowColor = "#dedede";
                }
                else{
                  $rowColor = "#ffffff66";
                }
              }
            }
          }
        }
      }

      $tablaNotificaciones = "<td width='2%'>&nbsp;</td>
                              <td valign='top' width='49%'>
                                <table class='tableInforme' border='0'>
                                  <thead class='thead'>
                                    <tr class='total' style='text-align:center;'>
                                      <td><b>TIPO</b></td>
                                      <td><b>MENSAJE</b></td>
                                      <td><b>FECHA</b></td>
                                    </tr>
                                  </thead>
                                  <tbody>
                                    $contenidoNotificaciones
                                  </tbody>
                                </table>
                              </td>";
      $widthTablaPrincipal = "";
    }
    else{
      $tablaNotificaciones = "";
      $widthTablaPrincipal = "style='width:100%;'";
    }

    //GENERAMOS LA TABLA CORRESPONDIENTE
    echo "<style>
            .tableInforme{
              font-size       : 13px;
              width           : 100%;
              border-collapse : collapse;
            }
            .tableInforme .thead td{
              color : #FFF;
            }
            .tableInforme .thead{
              height      : 25px;
              background  : #999;
              height      : 25px;
              font-size   : 12px;
              color       : #FFF;
              font-weight : bold;
            }
            .tableInforme .total{
              height        : 25px;
              font-weight   : bold;
              color         : #8E8E8E;
            }
            .table thead{
              background : #999;
            }
            .table thead td{
              height       : 30px;
              background   : #999;
              color        : #FFF;
            }
          </style>
          <div style='margin:10px;padding:5px;'>
            <table $widthTablaPrincipal>
              <tr>
                <td valign='top' width='49%'>
                  <table class='tableInforme' border='0'>
                    <thead class='thead'>
                      <tr class='total' style='text-align:center;'>
                        <td>&nbsp;<b><input type='checkbox' id='checkbox_all' onchange='seleccionarTodo(this)'></b>&nbsp;</td>
                        <td><b>NUMERO</b></td>
                        <td><b>NIT</b></td>
                        <td><b>NOMBRE</b></td>
                        <td><b>TOTAL</b></td>
                        <td><b>OPCIONES</b></td>
                      </tr>
                    </thead>
                    <tbody>
                      $contenidoDocumentos
                    </tbody>
                  </table>
                </td>
                $tablaNotificaciones
              </tr>
            </table>
          </div>
          <script>
            function generarPDF(documento){
              ventanaPdf = window.open('facturacion_electronica/imprimir_factura_electronica.php?numeroDocumento='+documento);
            }

            function reenviarCorreo(documento){
              // correo = prompt('Por favor escriba el correo electronico al que desea enviar el documento. En caso de que sean varias direcciones de correo, separa cada una con comas.');

              // if(correo == undefined){
              //   return;
              // }
              // else if(correo == ''){
              //   alert('Por favor digite un correo electronico.');
              //   reenviarCorreo(documento);
              //   return;
              // }

              // cargando_documentos('Enviando Documento...','');

              // Ext.Ajax.request({
              //   url    : 'facturacion_electronica/bd/bd.php',
              //   params :  {
              //               opc             : 'reenviarCorreoFacturaElectronica',
              //               numeroDocumento : documento,
              //               correo          : correo
              //             },
              //   success : function(result,request){
              //               alert(result.responseText);
              //               document.getElementById('modal').parentNode.parentNode.removeChild(document.getElementById('modal').parentNode);
              //             },
              //   failure : function(){
              //               alert('Problema de conexion con el servidor.');
              //               document.getElementById('modal').parentNode.parentNode.removeChild(document.getElementById('modal').parentNode);
              //             }
              // });

              Win_Ventana_Documentos_FE = new Ext.Window({
						    width       	: 546,
						    height      	: 500,
						    id          	: 'Win_Ventana_Documentos_FE',
						    title       	: 'Formulario subir documentos',
						    modal       	: true,
						    autoScroll  	: false,
						    closable    	: false,
						    autoDestroy 	: true,
						    autoLoad    	: {
													        url  		   	: 'facturacion_electronica/ventana_documentos_factura_electronica.php',
													        scripts 		: true,
													        nocache 		: true,
													        params  		: { documento : documento }
														    },
						    tbar       		:	[
													        {
												            xtype   : 'buttongroup',
												            columns : 3,
												            title   : 'Opciones',
												            style   : 'border-right:none;',
												            items   : [
																                {
																                  xtype       : 'button',
																                  width       : 60,
																                  height      : 56,
																                  text        : 'Regresar',
																                  scale       : 'large',
																                  iconCls     : 'regresar',
																                  iconAlign   : 'top',
																                  hidden      : false,
																                  handler     : function(){ BloqBtn(this); Win_Ventana_Documentos_FE.close(id) }
																                },
                                                {
                                                  xtype       : 'button',
                                                  width       : 60,
                                                  height      : 56,
                                                  text        : 'Enviar',
                                                  scale       : 'large',
                                                  iconCls     : 'upload_file32',
                                                  iconAlign   : 'top',
                                                  hidden      : false,
                                                  handler     : function(){ BloqBtn(this); reenviarCorreoFacturaElectronica(); }
                                                }
																	            ]
													        }
													    	]
							}).show();
            }

            function windows_upload_file(){
							document.getElementById('divPadreModalUploadFile').setAttribute('style','display:block;');
						}

						function close_ventana_upload_file(){
							document.getElementById('divPadreModalUploadFile').setAttribute('style','');
						}

            function seleccionarTodo(elemento){
              checkboxes = document.getElementsByClassName('documentosElectronicos');

              for(i = 0;i < checkboxes.length; i++){
            		if(checkboxes[i].type == 'checkbox'){
            			checkboxes[i].checked = elemento.checked;
            		}
            	}
            }
          </script>";
  }

  function consumirApi($url_api,$metodo,$datos){
    // Creamos los parametros para consumir la API
    $params                   = [];
    $params['request_url']    = $url_api;
    $params['request_method'] = $metodo;
    $params['Authorization']  = "";
    $params['data']           = $datos;

    // Consumimos el API y obtenemos sus resultados
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
                      CURLOPT_SSL_VERIFYPEER => false,
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
