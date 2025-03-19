<?php
class AutorizaOrdenesCorreo{
    private $idEmpresa;     // id de la empesa
    private $idDocumento;   // id de la OC
    private $documentData;  // Datos del documento
    private $dataAutorizador;  // data del autorizador
    private $usuarioAutorizador;  // username del autorizador
    private $contrasenaAutorizador;  // password del autorizador
    private $tipoAutorizacion;  // tipo de autorizacion
    private $orm;  // clase ORM
    private $idSiguienteAutorizador;  // id del siguiente autorizador
    private $empresaData;  // id del siguiente autorizador
    private static $mensajesErrorEstadoDocumento = array(  //Mensajes de error para cada estado del documento
        0 => "El documento no está generado",
        2 => "El documento está bloqueado",
        3 => "El documento se encuentra cancelado"
    );
    private  $response;  // respuesta
    private  $mailConnection;
    private  $mail;

    /**
     * Constructor de la clase que inicializa las credenciales de la base de datos.
     * @param string $servidor - Dirección del servidor MySQL.
     * @param string $usuario - Nombre de usuario de la base de datos.
     * @param string $clave - Contraseña de acceso.
     * @param string $baseDatos - Nombre de la base de datos predeterminada.
     */
    public function __construct(Orm_Controller $orm, $idEmpresa, $idDocumento, $usuario, $contrasena,$tipoAutorizacion) {
        $this->orm                      = $orm;
        $this->idEmpresa                = $idEmpresa;
        $this->idDocumento              = $idDocumento;
        $this->usuarioAutorizador       = $usuario;
        $this->tipoAutorizacion         = $tipoAutorizacion;
        $this->contrasenaAutorizador    = md5($contrasena);
        $this->setDocumentData();
        $this->setEmpresaData();

        $this->response = ["success"=>true];
    }

    /**
     *  valida el usuario puede o no autorizar el documento
     * @return void 
     */
    private function validarPermisoAutorizacionUsuario() {
        //primero debe autentiarse
        if(!isset($this->dataAutorizador)){ 
            $this->response = ["success"=>false,"message"=>"No se ha autenticado el usuario"]; 
            return; 
        }

        //Valida si el usuario pertenece a los autorizadores
        if(!array_key_exists($this->dataAutorizador['id'],$this->documentData['autorizadores'])){
            $this->response = ["success"=>false,"message"=>"No tiene permisos para autorizar este documento"]; 
            return; 
        }

		//obtener el tipo de autorizacion anterior
		$tipoAutorizacionUsuarioAnterior = '';
        $ordenAutorizacionUsuario = $this->documentData['autorizadores'][$this->dataAutorizador['id']]['orden'];

		foreach ($this->documentData['autorizaciones'] as $autorizacion) {
			if ($autorizacion['orden'] == ($ordenAutorizacionUsuario - 1)) { //Si es la autorizacion anterior al usuario
				$tipoAutorizacionUsuarioAnterior = $autorizacion['tipo_autorizacion'];
				break;
			}
		}

        //Si el autorizador anterior no ha generado una autorizacion 
        //y el usuario no es el primer autorizador entonces no puede autorizar
        if($tipoAutorizacionUsuarioAnterior <> "Autorizada" && $ordenAutorizacionUsuario <> 1){ 
            $this->response = ["success"=>false,"message"=>"El autorizador anterior no ha autorizado el documento"]; 
            return; 
        }

        $this->response = ["success"=>true]; 
    }

    /**
     * Seter del id del usuario. Se validan las credenciales y se setea el id en caso de que sean correctas
     * @return void
     */
    private function autenticaUsuario() {
        //Validar credenciales
        $sqlAutenticaUsuario = "SELECT id,documento,nombre,id_cargo,cargo,email_empresa
                            FROM empleados 
                            WHERE 
                                username='$this->usuarioAutorizador' 
                                AND password='$this->contrasenaAutorizador' 
                                AND id_empresa = $this->idEmpresa 
                            LIMIT 0,1";
        $idUsuarioResult = $this->orm->fetchOne($sqlAutenticaUsuario);

        if(!$idUsuarioResult['success']){ $this->response = ["success"=>false,"message"=>"Ha ocurrido un error al autenticar el usuario"]; return; }
        
        if (count($idUsuarioResult['data']) === 0) { 
            $this->response = ["success" => false, "message" => "Las credenciales que ha ingresado son incorrectas"];
            return;
        }
        
        $this->response = ["success" => true];
        $this->dataAutorizador = $idUsuarioResult['data'];
    }

     /**
     * Seter de los datos del documento: area, estado, autorizaciones y autorizadores
     * @return void
     */
    private function setDocumentData() {
        //Consultar area del documento
        $sqlDocumentData   =   "SELECT id_area_solicitante as idArea,
                                estado,
                                autorizado,
					            sucursal,
					            bodega,
					            consecutivo,
					            nit,
					            proveedor,
					            documento_usuario,
					            usuario,
					            id_usuario as idUsuario
                                FROM compras_ordenes 
                                WHERE 
                                    activo=1 
                                    ANd id_empresa=$this->idEmpresa 
                                    AND id=$this->idDocumento
                                LIMIT 1";
        $docDataResult = $this->orm->fetchOne($sqlDocumentData);
        if(!$docDataResult['success']){ $this->response = ["success"=>false,"message"=>"Error al consultar los datos del documento"]; return; }
        $docDataArray = $docDataResult['data'];

        //Consultar las autorizaciones del documento
        $sqlAutorizacionesDocumento =   "SELECT id_empleado,tipo_autorizacion, orden
                                        FROM autorizacion_ordenes_compra_area 
                                        WHERE 
                                            activo=1 
                                            AND id_empresa=$this->idEmpresa 
                                            AND id_orden_compra=$this->idDocumento 
                                            AND id_area=".$docDataArray['idArea'];
        $docDataResultAutorizaciones = $this->orm->fetchIndexed($sqlAutorizacionesDocumento,"id_empleado");
        if(!$docDataResultAutorizaciones['success']){ $this->response = ["success"=>false,"message"=>"Error al consultar las autorizaciones del documento"]; return; }
        $docDataArray['autorizaciones'] = $docDataResultAutorizaciones['data'];
        
        
        //Consultar los autorizadores del area
        $sqlAutorizadoresDocumento =   "SELECT id_empleado, orden 
                                        FROM costo_autorizadores_ordenes_compra_area 
                                        WHERE 
                                            activo=1 
                                            AND id_empresa=$this->idEmpresa 
                                            AND id_area=" . $docDataArray['idArea'] . "
                                        ORDER BY orden ASC";
        
        $docDataResultAutorizadores = $this->orm->fetchIndexed($sqlAutorizadoresDocumento,"id_empleado");
        if(!$docDataResultAutorizadores['success']){ $this->response = ["success"=>false,"message"=>"Error al consultar los autorizadores del documento"]; return; }
        $docDataArray['autorizadores'] = $docDataResultAutorizadores['data'];

        $this->documentData = $docDataArray;
    }

    public function setEmpresaData(){
        $sqlEmpresa   = "SELECT nombre,
                                tipo_documento_nombre,
                                documento,
                                nit_completo,
                                actividad_economica,
                                pais,
                                ciudad,
                                direccion,
                                razon_social,
                                tipo_regimen,
                                telefono || '-' || celular AS telefonos
				        FROM empresas
				        WHERE id='$this->idEmpresa'
				        LIMIT 0,1";
		$queryEmpresaResult = $this->orm->fetchOne($sqlEmpresa);
        if(!$queryEmpresaResult['success']){ $this->response = ['success'=>false,"message"=>"Error al consultar los datos de la empresa"]; return;}

		$this->empresaData = $queryEmpresaResult['data'];
    }

    /**
     * Valida que el documento se pueda autorizar
     * @return void
     */
    private function validaEstadoDocumento() {
        // Obtener el estado del documento (verificamos que la clave exista)
        $estado = isset($this->documentData['estado']) ? $this->documentData['estado'] : null;
        
        // Verificar si el estado tiene un mensaje de error
        if (isset(self::$mensajesErrorEstadoDocumento[$estado])) {
            $this->response = ["success" => false, "message" => self::$mensajesErrorEstadoDocumento[$estado]];
            return;
        }

        // Obtener el estado de autorizacion documento (verificamos que la clave exista)
        $autorizado = isset($this->documentData['autorizado']) ? $this->documentData['autorizado'] : null;
        if ($autorizado == 'true') {
            $this->response = ["success" => false, "message" => "El documento ya se econtraba autorizado"];
            return;
        }
    }

    /**
     * Autoriza la orden de compra.
     * 
     * Se validan permisos, estado del documento y se inicia una transacción.
     * Luego, se guarda la autorización y se procesa el estado de la orden.
     * @return void
     */
    public function autorizarOrdenCompraArea() { 
        try {
            $this->autenticaUsuario();
            if (!$this->response['success']) throw new Exception($this->response['message']);
    
            $this->validarPermisoAutorizacionUsuario();
            if (!$this->response['success']) throw new Exception($this->response['message']);
    
            $this->validaEstadoDocumento();
            if (!$this->response['success']) throw new Exception($this->response['message']);
    
            $this->orm->query("BEGIN");
    
            $this->guardarAutorizacion();
            if (!$this->response['success']) throw new Exception($this->response['message']);
    
            $this->procesarEstadoOrden();
            if (!$this->response['success']) throw new Exception($this->response['message']);
    
            $this->orm->query("COMMIT");
    
        } catch (Exception $e) {
            $this->orm->query("ROLLBACK");
            $this->response = ["success" => false, "message" => $e->getMessage()];
        }
    }
    


    /**
     * Guarda la autorización de la orden de compra en la base de datos.
     * 
     * @return bool Retorna true si la operación fue exitosa, false en caso contrario.
     */
    private function guardarAutorizacion() {
        $idAutorizador = $this->dataAutorizador['id'];
        $ordenAutorizador = $this->documentData['autorizadores'][$idAutorizador]['orden'];

        if (array_key_exists($idAutorizador, $this->documentData['autorizaciones'])) {
            // Actualizar autorización existente
            $sql = "UPDATE autorizacion_ordenes_compra_area 
                    SET tipo_autorizacion='$this->tipoAutorizacion'
                    WHERE activo=1 
                    AND id_empresa = $this->idEmpresa 
                    AND id_empleado = $idAutorizador
                    AND id_orden_compra = $this->idDocumento
                    AND orden = $ordenAutorizador
                    AND id_area = ".$this->documentData['idArea'];
        } else {
            // Insertar nueva autorización
            $sql = "INSERT INTO autorizacion_ordenes_compra_area 
                    (orden, id_empleado, documento_empleado, nombre_empleado, id_cargo, cargo, email, 
                    tipo_autorizacion, id_orden_compra, id_area, fecha, hora, id_empresa) 
                    VALUES 
                    ($ordenAutorizador, 
                    " . $idAutorizador . ", 
                    '" . $this->dataAutorizador['documento'] . "', 
                    '" . $this->dataAutorizador['nombre'] . "', 
                    " . $this->dataAutorizador['id_cargo'] . ", 
                    '" . $this->dataAutorizador['cargo'] . "', 
                    '" . $this->dataAutorizador['email_empresa'] . "', 
                    '$this->tipoAutorizacion', 
                    $this->idDocumento,
                    '".$this->documentData['idArea']."', NOW(), NOW(), $this->idEmpresa)";
        }

        if(!$this->orm->query($sql)['success']) { $this->response = ["success"=>false,"message"=>"Error al guardar la autorizacion"]; return;}

        $this->response = ["success"=>true];
    }

    /**
     * Procesa el estado de la orden según el tipo de autorización.
     * 
     * @return bool Retorna true si la operación fue exitosa, false en caso contrario.
     */
    private function procesarEstadoOrden() {
        switch ($this->tipoAutorizacion) {
            case 'Autorizada':
                $this->manejarAutorizacion();
                break;

            case 'Rechazada':
                $this->actualizarEstadoOrden('false');
                break;

            case 'Aplazada':
                $this->actualizarEstadoOrden('false');
                break;

            default:
                $this->response = ['success'=>false,"message"=>"Error inesperado: Por favor contacte con soporte"];
                break;
        }
    }

    /**
     * Maneja la autorización de la orden,  confirmando la autorización si no hay mas autorizadores
     * 
     * @return bool Retorna true si la operación fue exitosa, false en caso contrario.
     */
    private function manejarAutorizacion() {
        $ordenAutorizador = $this->documentData['autorizadores'][$this->dataAutorizador['id']]['orden'];
        
        $this->idSiguienteAutorizador = null;
        foreach ($this->documentData['autorizadores'] as $autorizador) {
            if ($autorizador['orden'] == ($ordenAutorizador + 1)) { 
                $this->idSiguienteAutorizador = $autorizador['id_empleado'];
                break;
            }
        }

        if ($this->idSiguienteAutorizador) {
            return;
        }

        $this->actualizarEstadoOrden('true');
    }

    /**
     * Actualiza el estado de la orden de compra en la base de datos.
     * 
     * @param string $estado  Nuevo estado ('true' para autorizada, 'false' para rechazada o aplazada)
     * @param string $asunto  Asunto del correo de notificación
     * @param string $mensaje Contenido del correo de notificación
     * 
     * @return bool Retorna true si la actualización fue exitosa, false en caso contrario.
     */
    private function actualizarEstadoOrden($estado) {
        $sql = "UPDATE compras_ordenes SET autorizado='$estado' WHERE id='$this->idDocumento'";
        if (!$this->orm->query($sql)['success']) {
            $this->response = ['success'=>false,"message"=>"Error al actualizar el estado de la OC"];
            return;
        }
        $this->response = ["success"=>true,"message"=>"La OC fue $this->tipoAutorizacion con exito"];
    }

    /**
     * Envía una notificación por correo electrónico.
     * 
     * @param string $destinatario ID del destinatario (puede ser un usuario o el solicitante)
     * @param string $asunto       Asunto del correo
     * @param string $mensaje      Contenido del correo
     * 
     * @return bool Retorna true si el correo se envió correctamente, false en caso contrario.
     */
    public function enviarNotificacion(PHPMailer $mail) {
        $this->mail = $mail;
        try {
            $this->conexionCorreo();
            if (!$this->response['success']) throw new Exception($this->response['message']);

            $this->configuracionSMTP();

            $this->setEmailBody();

            $this->addAddresses();
            if (!$this->response['success']) throw new Exception($this->response['message']);

            $this->sendEmail();
            if (!$this->response['success']) throw new Exception($this->response['message']);

        } catch (Exception $e) {
            $this->response = ["success" => false, "message" => $e->getMessage()];
        }
    }

    private function conexionCorreo(){
        //Consultar la configuracion de correo
        $sqlConexion    = "SELECT * FROM empresas_config_correo WHERE id_empresa=$this->idEmpresa LIMIT 0,1";
		$conexionResult = $this->orm->fetchOne($sqlConexion);
        if(!$conexionResult['success']){ $this->response= ["success"=>false,"message"=>"Error al consultar los datos de conexion"]; return;}

		$this->mailConnection =  $conexionResult['data'];

        if ($this->mailConnection['correo']=='') { $this->response= ["success"=>false,"message"=>"No exite ninguna configuracion de correo SMTP!"]; return;}

        $this->response = ["success"=>true];
    }
    
    private function configuracionSMTP(){
        $this->mail->IsSMTP();
		$this->mail->SMTPAuth   = true;                  				// enable SMTP authentication
		$this->mail->SMTPSecure = $this->mailConnection['seguridad_smtp'];   // sets the prefix to the servier
		$this->mail->Host       = $this->mailConnection['servidor'];    // sets GMAIL as the SMTP server
		$this->mail->Port       = $this->mailConnection['puerto'];      // set the SMTP port
		$this->mail->Username   = $this->mailConnection['correo'];        // GMAIL username
		$this->mail->Password   = $this->mailConnection['password'];        // GMAIL password
		$this->mail->From       = $this->mailConnection['correo'];
		$this->mail->FromName   = "Sistema de Notificaciones Automaticas LOGICALSOFT ERP";
		$this->mail->Subject    = "Orden de Compra $this->tipoAutorizacion";
		$this->mail->AltBody    = "This is the body when user views in plain text format"; //Text Body
		$this->mail->WordWrap   = 50; // set word wrap
    }

    private function setEmailBody(){
        $datos = base64_encode($this->idDocumento   .'|'. 
					   $this->documentData['consecutivo'] .'|'. 
					   $this->documentData['sucursal'] 	.'|'. 
					   $this->empresaData['documento']    .'|'. 
					   $this->idEmpresa             .'|'.
					   $this->orm->getNameDb()
					);
        
        $tableAutorizarOC = '';
        $mensaje = "La orden de compra que solicito ha sido $this->tipoAutorizacion";
	    $serverRoot = ($_SERVER['SERVER_NAME'] == 'localhost')? "http://localhost/ERP/":$_SERVER['DOCUMENT_ROOT'];
        
        if($this->idSiguienteAutorizador){
           $tableAutorizarOC = '<table>
                                    <tr>
                                        <td  font-family:tahoma,arial,verdana,sans-serif; font-size:32px; font-weight:bold; ">
                                              <a href="'.$serverRoot.'/LOGICALERP/compras/ordenes_compra/bd/autorizacion_ordenes_correo/autorizar_ordenes_correo.php?data='.$datos.'"
                                               target="_blank" 
                                               style="font-family:tahoma,arial,verdana,sans-serif; 
                                               font-size:32px; font-weight:bold; ">Click aqui para autorizar</a>
                                        </td>
                                    </tr>
                                </table>';

            $mensaje = "Orden de compra pendiente por su autorizacion";
        }
		

		$body  = '<font color="black">
				<br>
				<b>'.$this->empresaData['razon_social'].'</b><br>
				<b>'.$this->empresaData['tipo_documento_nombre'].': </b>'.$this->empresaData['documento'].'<br>
				<b>Direccion: </b>'.$this->empresaData['direccion'].' - <b>'.$this->empresaData['ciudad'].' </b><br>
				<b>Telefono: </b>'.$this->empresaData['telefonos'].'<br>

				<br>

				<table>
					<tr>
						<td>Asunto: </td>
						<td>'.$mensaje.'</td>
					</tr>
					<tr>
						<td>Consecutivo</td>
						<td style="font-size:24px;font-weight:bold;">'.$this->documentData['consecutivo'].'</td>
					</tr>
					<tr>
						<td>Bodega: </td>
						<td> '.$this->documentData['bodega'].'</td>
					</tr>
					<tr>
						<td>Sucursal: </td>
						<td>'.$this->documentData['sucursal'].'</td>
					</tr>
					<tr>
						<td>Proveedor: </td>
						<td>'.$this->documentData['nit'].' - '.$this->documentData['proveedor'].' </td>
					</tr>
					<tr>
						<td>Usuario Creador</td>
						<td>'.$this->documentData['documento_usuario'].' - '.$this->documentData['usuario'].' </td>
					</tr>
				</table>
				'.$tableAutorizarOC.'
				<br>
				<br>
				Esta es una notificacion automatica generada por el software LogicalSoft ERP, por favor no responda este email.
			</font><br>';

		$this->mail->Body = $body;
		$this->mail->MsgHTML($body);
    }

    private function addAddresses(){
        $sqlCorreo = ($this->idSiguienteAutorizador)? 
                        "SELECT email_empresa FROM empleados WHERE activo=1 AND id_empresa=$this->idEmpresa AND id = $this->idSiguienteAutorizador" :
                        "SELECT email_empresa FROM empleados WHERE activo=1 AND id_empresa=$this->idEmpresa AND id = ".$this->documentData['idUsuario'];

        $sqlCorreoResult = $this->orm->fetchValue($sqlCorreo);

        if(!$sqlCorreoResult['success']){        
            $this->response = [ "success"=>false,
                                "message"=>"Se autorizo la OC pero ocurrió un error al consultar el correo de envio"];
            return;
        }

        if(count($sqlCorreoResult['data']) === 0){        
            $this->response = [ "success"=>false,
                                "message"=>"Se autorizo la OC pero no hay un email configurado para informar la actualizacion"];
            return;
        }

        $this->mail->AddAddress($sqlCorreoResult['data']);
        $this->response = ["success"=>true];
        return;
    }

    private function sendEmail(){
        if(!$this->mail->Send()){
            $this->response = [ "success"=>false,
                                "message"=>"Se autorizo la OC pero ocurrió un error inesperado al enviar el correo electronico"];
            return;
        }
        $this->mail->ClearAddresses();
        $this->response = [ "success"=>true,
                            "message"=>"La OC fue autorizada y se envio un correo electronico informando la actualizacion"];
    }

    public function getResponse(){
        return $this->response;
    }
}
?>
