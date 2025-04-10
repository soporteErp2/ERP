<?php
include_once '../../../../configuracion/conexion.php';

class Api_Controller
{
    private $mysqli = null;
    
    public function __construct() 
    {
        $this->connect();
    }

    public function connect()
    {
        global $server;
        $this->mysqli = new mysqli($server->server_name, $server->user, $server->password, $server->database);
        if ($this->mysqli->connect_error) {
            return ["error conectando al servidor: " . $this->mysqli->connect_error];
        }

        $this->mysqli->set_charset("utf8mb4");
        return $this->mysqli;
    }

    public function change_connection($database)
    {
        $this->mysqli->close();

        global $server;
        $this->mysqli = new mysqli($server->server_name, $server->user, $server->password, $database);
        if ($this->mysqli->connect_error) {
            return ["error conectando al servidor: " . $this->mysqli->connect_error];
        }

        $this->mysqli->set_charset("utf8mb4");
        return $this->mysqli;
    }

    public function get_company_id($licence_id,$company_doc){
        $this->change_connection("erp_".$licence_id);
        $sql = "SELECT id FROM empresas WHERE documento='$company_doc' ";
        $query = $this->mysqli->query($sql);
        $id = ($fila = $query->fetch_row()) ? $fila[0] : null;
        return $id;
    }

    public function get_branch($id_empresa,$licence_id){
        $this->change_connection("erp_".$licence_id);
        $sql = "SELECT id FROM empresas_sucursales WHERE id_empresa='$id_empresa' ";
        $query = $this->mysqli->query($sql);
        $id = ($fila = $query->fetch_row()) ? $fila[0] : null;
        return $id;
    }

    public function verify_db($data)
    {
        $sql = "SELECT id FROM host WHERE nit = '$data[company]'";
        $result = $this->mysqli->query($sql);
        // $rows = $result->fetch_all(MYSQLI_ASSOC);
        if ($result->num_rows>0) 
        {
            $this->structure_db();
        }
        else
        {
            $this->create_db($data);
        }

        // echo $result->num_rows;
    }

    public function create_db($data)
    {

        $dbName = "erp_$data[licence]"; // Nombre de la base de datos
        $sql = "CREATE DATABASE  $dbName DEFAULT CHARACTER SET utf8mb4 DEFAULT COLLATE utf8mb4_general_ci";
        
        if ($this->mysqli->query($sql)) {
            $this->mysqli->select_db($dbName);

            // Ruta del archivo SQL (tres directorios atrás)
            $sqlFilePath = realpath('../../../../configuracion/structure.sql');

            if ($sqlFilePath && file_exists($sqlFilePath)) {
                $sqlContent = file_get_contents($sqlFilePath);
                
                // Ejecutar las consultas del archivo
                if ($this->mysqli->multi_query($sqlContent)) {
                    do {
                        // Vaciar el buffer de resultados
                        if ($result = $this->mysqli->store_result()) {
                            $result->free();
                        }
                    } while ($this->mysqli->next_result());

                    echo json_encode(["message" => "Base de datos creada y estructura ejecutada correctamente"]);
                } else {
                    echo json_encode(["error" => "Error ejecutando el archivo SQL: " . $this->mysqli->error]);
                }

                $installation_path = realpath('../../../../configuracion/installation.sql');
                if ($installation_path && file_exists($installation_path)) {
                    $installation_sql = file_get_contents($installation_path);
                    
                    
                    // Ejecutar las consultas del archivo
                    if ($this->mysqli->multi_query($installation_sql)) {
                        do {
                            // Vaciar el buffer de resultados
                            if ($result = $this->mysqli->store_result()) {
                                $result->free();
                            }
                        } while ($this->mysqli->next_result());
    
                        // echo json_encode(["message" => "Base de datos creada y estructura ejecutada correctamente"]);
                    } else {
                        echo json_encode(["error" => "Error ejecutando el archivo SQL: " . $this->mysqli->error]);
                    }
                }


            } else {
                echo json_encode(["error" => "Archivo SQL no encontrado en: " . $sqlFilePath]);
            }
        } else {
            echo json_encode(["error" => "Error creando la base de datos o la base de datos ya existe: " . $this->mysqli->error]);
        }
    }

    function structure_db(){
        echo json_encode("db exist, structure");
    }

    public function create_company($data)
    {
        $this->change_connection("erp_".$data["licence"]);

        $sql = "INSERT INTO empresas (nombre,tipo_documento_nombre,id_pais,pais,id_departamento,id_ciudad, razon_social,tipo_regimen,actividad_economica,direccion,documento,digito_verificacion,telefono,celular,zona_horaria,id_moneda,formato_hora,interface,grupo_empresarial)
			 	VALUES ('$data[company_name]','','','','','','$data[company_rs]','','','','$data[company]',$data[company_dv],'','','','','','','')";
        $query = $this->mysqli->query($sql);

        if (!$query) {
            echo json_encode("error al insertar la empresa");
            return;
        }

        $id_empresa = $this->get_company_id($data['licence'],$data['company']);
        $sql = "INSERT INTO `empresas_sucursales` ( `id_empresa`, `nombre` ) VALUES ('$id_empresa', 'SUCURSAL PRINCIPAL')";
        $query = $this->mysqli->query($sql);

        $id_sucursal = $this->get_branch($id_empresa,$data['licence']);

    	$sql   = "INSERT INTO empresas_sucursales_bodegas (id_empresa,id_sucursal,nombre) VALUES ($id_empresa,$id_sucursal,'Bodega Principal')";
        $query = $this->mysqli->query($sql);

        
        $idGrupoEmpresarial = 0;
        // insertar el puff
        include "../../configuraciones/configuracion_col/puc_colgaap.php";
        $query = $this->mysqli->query($sqlPucColgaap);
        include "../../configuraciones/configuracion_col/puc_niif.php";
        $query = $this->mysqli->query($sqlPucNiif);

        //nomina
        include "Nomina_Controller.php";        
        Nomina_Controller::insert_concepts($this->mysqli,$id_empresa);

        // cuentas por defecto
        $this->purchase_sale_accounts($id_empresa);
        // tipos de nota
        $this->note_type($id_empresa);
        //impuestos
        $this->taxes($id_empresa);
        //retenciones
        $this->retentions($id_empresa);
        //cargos/ roles empleados
        $this->employee_authentication($id_empresa);
        //tipos de documentos
        $this->document_types($id_empresa);
        //metodos de pago
        $this->pay_methods($id_empresa);
        // tipos de documentos (archivos) para empleados
        $this->employee_docs_types($id_empresa);
        // forma de pago por defecto
        $this->default_pay_method($id_empresa);
        // cuentas de pago por defecto (cabecera de facturas)
        $this->default_payment_accounts($id_empresa);
        // configuracion de puc
        $this->puc_config($id_empresa);
        // tipos de unidades de items
        $this->types_units_items($id_empresa);
        // configuraciones generales terceros
        $this->third_party_configuration($id_empresa);
        //tipos de ordenes de compra (valores por defecto)
        $this->purchase_order_type($id_empresa);
        


        $this->change_connection("erp_acceso");
        $sql = "INSERT INTO host (id,`nit`, `nombre`, `servidor`, `bd`, `id_plan`, `fecha_creacion`, `hora_creacion`, `fecha_vencimiento_plan`, `timezone`, `almacenamiento`, `activo`, `usuario_nombre1`, `usuario_nombre2`, `usuario_apellido1`, `usuario_apellido2`) 
                VALUES 
                ($data[licence],'$data[company]', '$data[company_name]', 'localhost', 'erp_$data[licence]', '1', '".date("Y-m-d")."', '".date("H:i:s")."', '2100-04-02', 'America/Bogota', '50', '1', NULL, NULL, NULL, NULL);";
        $query = $this->mysqli->query($sql);

        if (!$query) {
            echo json_encode("error al insertar la empresa en la tabla host");
            return;
        }

        

        echo json_encode("compañia creada!");
    }

    public function purchase_sale_accounts($id_empresa){
        $arrayCuentasDefault = array('compra' => array(143501 => array('niif' => true, 'estado' => 'debito', 'detalle' => 'items_compra_precio'),
                                                        240802 => array('niif' => true, 'estado' => 'debito', 'detalle' => 'items_compra_impuesto'),
                                                        220501 => array('niif' => true, 'estado' => 'credito', 'detalle' => 'items_compra_contraPartida_precio'),
                                                        519530 => array('niif' => true, 'estado' => 'debito', 'detalle' => 'items_compra_gasto'),
                                                        151610 => array('niif' => true, 'estado' => 'debito', 'detalle' => 'items_compra_activo_fijo'),
                                                        613520 => array('niif' => true, 'estado' => 'debito', 'detalle' => 'items_compra_costo')
                                                    ),
									'venta' => array(143501 => array('niif' => true, 'estado' => 'credito', 'detalle' => 'items_venta_costo'),
													613516 => array('niif' => true, 'estado' => 'debito', 'detalle' => 'items_venta_contraPartida_costo'),
													413520 => array('niif' => true, 'estado' => 'credito', 'detalle' => 'items_venta_precio'),
													130505 => array('niif' => true, 'estado' => 'debito', 'detalle' => 'items_venta_contraPartida_precio'),
													240801 => array('niif' => true, 'estado' => 'credito', 'detalle' => 'items_venta_impuesto'),
													417501 => array('niif' => false, 'estado' => 'debito', 'detalle' => 'items_venta_devprecio')
												)
									);

        $whereCuentas = "";
        $valueAsientosDefault = "";
        foreach ($arrayCuentasDefault as $tipoCuenta => $arrayCuentaDefault) {

            //FOREACH CONFIGURACION CUENTAS POR DEFAULT
            foreach ($arrayCuentaDefault as $cuentaColgaap => $arrayCuenta){
                $whereCuentas         .= ' OR cuenta = '.$cuentaColgaap;
                $valueAsientosDefault .= "('".$arrayCuenta['detalle']."','".$arrayCuenta['estado']."','".$cuentaColgaap."',$id_empresa),";
            }
        }

        $whereCuentas = substr($whereCuentas, 4);
        $whereCuentas = 'AND ('.$whereCuentas.')';

        //==================== ASIENTOS COLGAAP DEFAULT ======================//
        $valueAsientosDefault = substr($valueAsientosDefault, 0, -1);
        $sqlAsientosDefault   = "INSERT INTO asientos_colgaap_default (descripcion,estado,cuenta,id_empresa) VALUES $valueAsientosDefault";
        $query = $this->mysqli->query($sqlAsientosDefault);

        $valueInsert = '';
        $sqlCuentasDefaultNiif   = "SELECT cuenta_niif, cuenta AS cuenta_colgaap
                                    FROM puc
                                    WHERE activo=1 AND id_empresa='$id_empresa' $whereCuentas";
        $query = $this->mysqli->query($sqlCuentasDefaultNiif);

		while ($rowCuentasDefault= $query->fetch_array()) {
            $cuentaColgaap = $rowCuentasDefault['cuenta_colgaap'];
            $arrayCuentaNiif[$cuentaColgaap] = $rowCuentasDefault['cuenta_niif'];
        }

        //FOREACH OPCION COMPRA-VENTA
        foreach ($arrayCuentasDefault as $tipoCuenta => $arrayCuentaDefault) {

            //FOREACH CONFIGURACION CUENTAS POR DEFAULT
            foreach ($arrayCuentaDefault as $cuentaColgaap => $arrayCuenta) {
                if($arrayCuenta['niif'] != true) continue;
                $valueInsert .= "('".$arrayCuenta['detalle']."', '".$arrayCuenta['estado']."', '".$arrayCuentaNiif[$cuentaColgaap]."', '$id_empresa'),";
            }
        }

        $valueInsert = substr($valueInsert, 0, -1);
        $sqlInsertDefaulNiif   = "INSERT INTO asientos_niif_default(descripcion,estado,cuenta,id_empresa) VALUES $valueInsert";
        $query = $this->mysqli->query($sqlInsertDefaulNiif);
                        
    }

    function note_type($id_empresa) {
        $sql="INSERT INTO tipo_nota_contable (descripcion,consecutivo,consecutivo_niif,documento_cruce,id_empresa)
							VALUES
							('NOTA GENERAL',1,1,'Si',$id_empresa),
							('NOTA BANCARIA',1,1,'No',$id_empresa),
							('SALDO INICIAL CONTABLE',1,1,'No',$id_empresa),
							('DEPRECIACION',1,1,'No',$id_empresa),
							('AMORTIZACION',1,1,'No',$id_empresa)";
        $query = $this->mysqli->query($sql);
    }

    function taxes($id_empresa){
        $sql = "INSERT INTO impuestos (impuesto, valor, compra, cuenta_compra, cuenta_compra_niif, cuenta_compra_devolucion, cuenta_compra_devolucion_niif, venta, cuenta_venta, cuenta_venta_niif, cuenta_venta_devolucion, cuenta_venta_devolucion_niif, id_empresa)
							VALUES
								('IVA SERVICIOS 19%', '19.00', 'No', '', '', '', '', 'Si', '24080107', '24080107', '24080201', '24080201', '$id_empresa'),
								('IVA SERVICIOS 5%', '5.00', 'No', '', '', '', '', 'Si', '24080107', '24080107', '24080201', '24080201', '$id_empresa'),
								('IVA COMPRAS 5%', '5.00', 'Si', '24080220', '24080220', '24080220', '24080220', 'No', '', '', '', '', '$id_empresa'),
								('IVA COMPRAS 19%', '19.00', 'Si', '24080219', '24080219', '24080219', '24080219', 'No', '', '', '', '', '$id_empresa')";
        $query = $this->mysqli->query($sql);

    }

    function retentions($id_empresa){
        $sql="INSERT INTO retenciones (retencion,tipo_retencion,valor,base,cuenta,cuenta_niif,cuenta_autoretencion,cuenta_autoretencion_niif,modulo,id_empresa,id_departamento,departamento,id_ciudad,ciudad)
							VALUES
							('RETEFUENTE SERVICIOS 1%', 'ReteFuente', 1.00, 110000.00, 23652501, 23652501, 0, 0, 'Compra', $id_empresa, 1277, 'Valle', 2259, 'Cali' ),
							('RETEFUENTE SERVICIOS 2%', 'ReteFuente', 2.00, 110000.00, 23652502, 23652502, 0, 0, 'Compra', $id_empresa, 1277, 'Valle', 2259, 'Cali' ),
							('RETEFUENTE SERVICIOS 3%', 'ReteFuente', 3.00, 110000.00, 23652503, 23652503, 0, 0, 'Compra', $id_empresa, 1277, 'Valle', 2259, 'Cali' ),
							('RETEFUENTE POR COMPRAS 3.5% (NO DECLARANTES)', 'ReteFuente', 3.50, 742000.00, 23654001, 23654001, 0, 0, 'Compra', $id_empresa, 1277, 'Valle', 2259, 'Cali' ),
							('RETEFUENTE POR COMPRAS 1%', 'ReteFuente', 1.00, 742000.00, 23654002, 23654002, 0, 0, 'Compra', $id_empresa, 1277, 'Valle', 2259, 'Cali' ),
							('RETEFUENTE POR COMPRAS 2.5% (DECLARANTES)', 'ReteFuente', 2.50, 742000.00, 23654005, 23654005, 0, 0, 'Compra', $id_empresa, 1277, 'Valle', 2259, 'Cali' ),
							('CALI - RETEICA SERVICIOS 10%', 'ReteIca', 1.00, 82000.00, 23680110, 23680110, 0, 0, 'Compra', $id_empresa, 1277, 'Valle', 2259, 'Cali' ),
							('CALI - RETEICA SERVICIOS 2.2%', 'ReteIca', 0.22, 82000.00, 23680122, 23680122, 0, 0, 'Compra', $id_empresa, 1277, 'Valle', 2259, 'Cali' ),
							('CALI - RETEICA SERVICIOS 3.3%', 'ReteIca', 0.33, 82000.00, 23680133, 23680133, 0, 0, 'Compra', $id_empresa, 1277, 'Valle', 2259, 'Cali' ),
							('CALI - RETEICA INDUSTRIAL 6.6%', 'ReteIca', 0.66, 412000.00, 23680166, 23680166, 0, 0, 'Compra', $id_empresa, 1277, 'Valle', 2259, 'Cali' ),
							('CALI - RETEICA COMERCIAL 7.7%', 'ReteIca', 0.77, 412000.00, 23680177, 23680177, 0, 0, 'Compra', $id_empresa, 1277, 'Valle', 2259, 'Cali' ),
							('CALI - RETEICA SERVICIOS 8.8%', 'ReteIca', 0.88, 82000.00, 23680188, 23680188, 0, 0, 'Compra', $id_empresa, 1277, 'Valle', 2259, 'Cali' ),
							('RETEF. R.S 15% PARA IVA 5% SERVICIOS', 'ReteFuente', 15.00, 5500.00, 23670103, 23670103, 24080237, 24080237, 'Compra', $id_empresa, 1277, 'Valle', 2259, 'Cali' ),
							('RETEF. R.S 15% PARA IVA 5% COMPRAS', 'ReteFuente', 15.00, 37100.00, 23670104, 23670104, 24080238, 24080238, 'Compra', $id_empresa, 1277, 'Valle', 2259, 'Cali' ),
							('RETEF. R.S 15% PARA IVA 19% SERVICIOS', 'ReteFuente', 15.00, 17600.00, 23670105, 23670105, 24080240, 24080240, 'Compra', $id_empresa, 1277, 'Valle', 2259, 'Cali' ),
							('RETEF. R.S 15% PARA IVA 19% COMPRAS', 'ReteFuente', 15.00, 118720.00, 23670106, 23670106, 24080241, 24080241, 'Compra', $id_empresa, 1277, 'Valle', 2259, 'Cali' ),
							('AUTORRETENCION DE CREE 0.80%', 'AutoRetencion', 0.80, 0.00, 13551920, 13551920, 23692015, 23692015, 'Venta', $id_empresa, 1277, 'Valle', 2259, 'Cali' ),
							('RETEFUENTE A FAVOR ALQUILER', 'ReteFuente', 4.00, 0.00, 13551501, 13551501, 0, 0, 'Venta', $id_empresa, 1277, 'Valle', 2259, 'Cali' ),
							('RETEFUENTE A FAVOR SERVICIOS', 'ReteFuente', 4.00, 110000.00, 13551502, 13551502, 0, 0, 'Venta', $id_empresa, 1277, 'Valle', 2259, 'Cali' ),
							('RETEFUENTE A FAVOR INTERESES', 'ReteFuente', 7.00, 0.00, 13551503, 13551503, 0, 0, 'Venta', $id_empresa, 1277, 'Valle', 2259, 'Cali' ),
							('RETEFUENTE A FAVOR HONORARIOS', 'ReteFuente', 11.00, 0.00, 13551504, 13551504, 0, 0, 'Venta', $id_empresa, 1277, 'Valle', 2259, 'Cali' ),
							('RETEFUENTE A FAVOR COMPRAS', 'ReteFuente', 2.50, 742000.00, 13551506, 13551506, 0, 0, 'Venta', $id_empresa, 1277, 'Valle', 2259, 'Cali' ),
							('IMPOVENTAS RETENIDO A FAVOR (ALQ-HON-FIN)', 'ReteIva', 15.00, 0.00, 13551701, 13551701, 0, 0, 'Venta', $id_empresa, 1277, 'Valle', 2259, 'Cali' ),
							('IMPOVENTAS RETENIDO A FAVOR (SERVICIOS)', 'ReteIva', 15.00, 110000.00, 13551701, 13551701, 0, 0, 'Venta', $id_empresa, 1277, 'Valle', 2259, 'Cali' ),
							('IMPOVENTAS RETENIDO A FAVOR (COMPRAS)', 'ReteIva', 15.00, 742000.00, 13551701, 13551701, 0, 0, 'Venta', $id_empresa, 1277, 'Valle', 2259, 'Cali' ),
							('RETENCION ICA - CALI', 'ReteIca', 1.00, 82000.00, 13551801, 13551801, 0, 0, 'Venta', $id_empresa, 1277, 'Valle', 2259, 'Cali' ),
							('RETENCION ICA - PEREIRA', 'ReteIca', 1.00, 110000.00, 13551804, 13551804, 0, 0, 'Venta', $id_empresa, 1027, 'Risaralda', 2266, 'Pereira' ),
							('RETENCION ICA - CARTAGENA', 'ReteIca', 0.80, 154000.00, 13551805, 13551805, 0, 0, 'Venta', $id_empresa, 188, 'Bolívar', 2262, 'Cartagena' ),
							('RETENCION ICA - ARMENIA', 'ReteIca', 1.00, 1232000.00, 13551806, 13551806, 0, 0, 'Venta', $id_empresa, 1006, 'Quindío', 2273, 'Armenia' )";
			
        $query = $this->mysqli->query($sql);
        
    }

    function employee_authentication($id_empresa){
        $sql="INSERT INTO empleados_roles (nombre,valor,id_empresa)
						VALUES
						('Administrador',1,$id_empresa),
						('Director de Zona',2,$id_empresa),
						('Auditoria Interna',2,$id_empresa),
						('Direccion de Calidad',2,$id_empresa),
						('Auxiliar de Recursos Humanos',2,$id_empresa),
						('Direccion General',2,$id_empresa),
						('Subdireccion General',2,$id_empresa),
						('Direccion Financiera',2,$id_empresa),
						('Direccion de Compras y Contrataciones',2,$id_empresa),
						('Direccion Comercial',2,$id_empresa),
						('Direccion Juridica',2,$id_empresa),
						('Direccion de Proyectos',2,$id_empresa),
						('Direccion de Tecnologia e Informatica',2,$id_empresa),
						('Auxiliar de Sistemas',2,$id_empresa)";
        $query = $this->mysqli->query($sql);

        $sql = "INSERT INTO empleados_cargos (nombre,id_empresa)
                        VALUES
                        ('Administrador',$id_empresa),
                        ('Director General',$id_empresa),
                        ('Subdirectora General',$id_empresa),
                        ('Director Juridico',$id_empresa),
                        ('Director Financiero',$id_empresa),
                        ('Director de Tecnologia e Informática',$id_empresa),
                        ('Directora de Calidad',$id_empresa),
                        ('Directora Comercial',$id_empresa),
                        ('Director de Proyectos y Mantenimiento',$id_empresa),
                        ('Director de Video y Comunicaciones Dig',$id_empresa),
                        ('Director de Zona',$id_empresa),
                        ('Asistente Administrativa',$id_empresa),
                        ('Asistente Administrativo',$id_empresa),
                        ('Asistente Financiera',$id_empresa),
                        ('Directora de Compras y Contrataciones',$id_empresa),
                        ('Directora de Recursos Humanos',$id_empresa),
                        ('Tesorero',$id_empresa),
                        ('Mensajero',$id_empresa),
                        ('Asistente de Mantenimiento',$id_empresa),
                        ('Coordinador de Eventos',$id_empresa),
                        ('Director Operativo',$id_empresa),
                        ('Asistente de Video y Comunicaciones Di',$id_empresa),
                        ('Asistente Comercial',$id_empresa),
                        ('Asistente de Sistemas',$id_empresa),
                        ('Auditora Interna',$id_empresa),
                        ('Servicios Generales',$id_empresa),
                        ('Estudiante SENA',$id_empresa),
                        ('Ejecutivo Comercial',$id_empresa),
                        ('Director Unidad Independiente',$id_empresa),
                        ('Gerente General',$id_empresa),
                        ('Desarrollador y Soportista',$id_empresa)";
        $query = $this->mysqli->query($sql);
    }

    function document_types($id_empresa){
        $sql = "INSERT INTO tipo_documento (codigo, codigo_tipo_documento_dian, nombre, detalle, tipo, id_empresa)
								VALUES
									('1', '11', 'R.C', 'Registro civil', 'Persona', '$id_empresa'),
									('2', '12', 'T.I', 'Tarjeta de identidad', 'Persona', '$id_empresa'),
									('3', '13', 'C.C', 'Cedula de Ciudadania', 'Persona', '$id_empresa'),
									('4', '21', 'T.E', 'Tarjeta de extranjeria', 'Persona', '$id_empresa'),
									('5', '22', 'C.E', 'Cedula de extranjeria', 'Persona', '$id_empresa'),
									('6', '31', 'NIT', 'Numero de Identificacion', 'Empresa', '$id_empresa'),
									('7', '41', 'Pasaporte', 'Pasaporte', 'Persona', '$id_empresa'),
									('8', '42', 'DIE', '	Documento de identificacion extranjero', 'Persona', '$id_empresa'),
									('9', '91', 'NUIP', 'NUIP *', 'Persona', '$id_empresa')";
        $query = $this->mysqli->query($sql);
    }

    function pay_methods($id_empresa){
        $sql = "INSERT INTO configuracion_metodos_pago (id, nombre, activo, codigo_metodo_pago_dian, id_empresa)
					VALUES
						('1', 'Efectivo', '1', '25', '$id_empresa'),
						('2', 'Cheque', '1', '26', '$id_empresa'),
						('3', 'Transferencia Bancaria', '1', '27', '$id_empresa'),
						('4', 'Consigancion Bancaria', '1', '28', '$id_empresa');";
        $query = $this->mysqli->query($sql);
    }

    function employee_docs_types($id_empresa){
        $sql = "INSERT INTO empleados_tipo_documento (nombre,id_empresa)
		 								VALUES ('Documento de Identidad',$id_empresa),
		 										('Libreta Militar',$id_empresa),
		 										('Certificado Judicial',$id_empresa),
		 										('Contrato',$id_empresa),
		 										('Hoja de Vida',$id_empresa),
		 										('Certificado de Estudios',$id_empresa),
		 										('Afiliaciones',$id_empresa),
		 										('Llamados de Atencion',$id_empresa),
		 										('Felicitaciones',$id_empresa),
		 										('Evaluaciones de Desempeño',$id_empresa),
		 										('Perfil de Cargos y Funciones',$id_empresa)";
        $query = $this->mysqli->query($sql);
    }

    function default_pay_method($id_empresa){
        $sql="INSERT INTO configuracion_formas_pago (nombre,plazo,id_empresa)
                VALUES ('Contado','1',$id_empresa),
                        ('Semanal','7',$id_empresa),
                        ('Quincena','15',$id_empresa),
                        ('Mes','30',$id_empresa)";
        $query = $this->mysqli->query($sql);
    }

    function default_payment_accounts($id_empresa){
        $arrayCuentaPagoDefault = array(22050101 => array('type' => 'Compra', 'detalle' => 'PROVEEDORES', 'estado' => 'Credito'),
									13050501 => array('type' => 'Venta', 'detalle' => 'CLIENTES', 'estado' => 'Credito'),
									11050501 => array('type' => 'Venta', 'detalle' => 'VENTA CAJA', 'estado' => 'Contado'),
									11100501 => array('type' => 'Venta', 'detalle' => 'VENTA BANCOS', 'estado' => 'Contado'));
        $whereCuentas = "";
        foreach ($arrayCuentaPagoDefault as $cuentaColgaap => $arrayCuenta) { $whereCuentas .= ' OR cuenta = '.$cuentaColgaap; }
        $whereCuentas = substr($whereCuentas, 4);
        $whereCuentas = 'AND ('.$whereCuentas.')';

        $sqlCuentasDefaultNiif   = "SELECT cuenta_niif, cuenta AS cuenta_colgaap
                                    FROM puc
                                    WHERE activo=1
                                        AND id_empresa='$id_empresa' $whereCuentas";
        $queryCuentasDefaultNiif = $this->mysqli->query($sqlCuentasDefaultNiif);
		while ($rowCuentasDefault= $queryCuentasDefaultNiif->fetch_array()) {
            $cuentaColgaap = $rowCuentasDefault['cuenta_colgaap'];
            $arrayCuentaPagoDefault[$cuentaColgaap]['cuenta_niif'] = $rowCuentasDefault['cuenta_niif'];
        }

        $valueInsert = '';
        foreach ($arrayCuentaPagoDefault as $cuentaColgaap => $arrayCuenta) {
            $valueInsert .= "('".$arrayCuenta['detalle']."', '".$arrayCuenta['type']."', '".$cuentaColgaap."','".$arrayCuenta['cuenta_niif']."', '$id_empresa', '".$arrayCuenta['estado']."'),";
        }
        $valueInsert     = substr($valueInsert, 0, -1);
        $sqlCuentaPago   = "INSERT INTO configuracion_cuentas_pago (nombre,tipo,cuenta,cuenta_niif,id_empresa,estado) VALUES $valueInsert";
        $query = $this->mysqli->query($sqlCuentaPago);
    }

    function puc_config($id_empresa) {
        $sql = "INSERT INTO puc_configuracion (nombre,digitos,id_empresa)
							VALUES ('CLASE',1,$id_empresa),
									('GRUPO',2,$id_empresa),
									('CUENTA',4,$id_empresa),
									('SUBCUENTA',6,$id_empresa),
									('AUXILIARES',8,$id_empresa)";
        $query = $this->mysqli->query($sql);
    }

    function types_units_items($id_empresa){
        $sql="INSERT INTO inventario_unidades (nombre, unidades,id_empresa)
                VALUES ('Unidad','1',$id_empresa),
                        ('Docena','12',$id_empresa),
                        ('Servicio','1',$id_empresa)";
        $query = $this->mysqli->query($sql);
        
    }

    function third_party_configuration($id_empresa){
        $sql = "INSERT INTO configuracion_sector_empresarial (nombre,id_empresa)
                VALUES('Educativo', $id_empresa),
                        ('Hotelero', $id_empresa),
                        ('Comercial', $id_empresa),
                        ('Industrial', $id_empresa),
                        ('Financiero', $id_empresa),
                        ('Salud', $id_empresa),
                        ('Produccion de Eventos', $id_empresa),
                        ('Centros Comerciales', $id_empresa),
                        ('Clubes', $id_empresa),
                        ('Asociaciones', $id_empresa),
                        ('Servicios', $id_empresa),
                        ('Persona Natural', $id_empresa),
                        ('Iglesias', $id_empresa),
                        ('Software y Tecnologia', $id_empresa)";
        $query = $this->mysqli->query($sql);

		$sql="INSERT INTO terceros_tratamiento (nombre,id_empresa)
										VALUES ('Sr.',$id_empresa),
												('Sra.',$id_empresa),
												('Srta.',$id_empresa),
												('Dr.',$id_empresa),
												('Dra.',$id_empresa),
												('Lic.',$id_empresa),
												('Ing.',$id_empresa)";
        $query = $this->mysqli->query($sql);

        $sql = "INSERT INTO terceros_tipo_documento (nombre,id_empresa)
										VALUES ('Foto',$id_empresa),
												('Cedula',$id_empresa),
												('Tarjeta',$id_empresa),
												('Certificado',$id_empresa),
												('Contrato',$id_empresa),
												('Cedula de Extranjeria',$id_empresa),
												('RUT',$id_empresa)";
        $query = $this->mysqli->query($sql);
    }

    function purchase_order_type($id_empresa){
        $sql = "INSERT INTO compras_ordenes_tipos (nombre, id_empresa)
                    VALUES ('NUEVO PROYECTO', '$id_empresa'),
                        ('REPOSICION DE EQUIPOS', '$id_empresa'),
                        ('REFUERZO DE OPERACION', '$id_empresa'),
                        ('EQUIPOS PARA LA VENTA', '$id_empresa'),
                        ('SUMINISTROS', '$id_empresa')";
        $query = $this->mysqli->query($sql);
    }

    public function create_user($data){
        $id_empresa = $this->get_company_id($data['licence'],$data['company_doc']);
        $id_sucursal = $this->get_branch($id_empresa,$data['licence']);
        $password=md5("12345678");
        $sql ="INSERT INTO `empleados` 
                ( `tipo_documento`, `tipo_documento_nombre`, `documento`, `nombre1`, `nombre2`, `apellido1`, `apellido2`, `nombre`, `id_empresa`, `empresa`, `id_sucursal`, `sucursal`, `id_unidad_negocio`, `unidad_negocio`, `id_pais`, `pais`, `id_departamento`, `departamento`, `id_ciudad`, `ciudad`, `id_rol`, `rol`, `id_cargo`, `cargo`, `username`, `password`, `email_empresa`, `nacimiento`, `direccion`, `email_personal`, `telefono1`, `telefono2`, `celular1`, `id_celular_empresa`, `celular_empresa`, `foto`, `id_contrato`, `contrato`, `salario_base`, `salario`, `ad_contrato`, `ad_certificado_judicial`, `ad_cedula`, `ad_certificado_estudios`, `ad_hoja_vida`, `ad_afiliaciones`, `alerta_actualizacion`, `activo`, `ciudad_cedula`, `eps`, `arp`, `tecnico_operativo`, `conductor`, `vendedor`, `qrcode`, `color_menu`, `color_fondo`, `change_update`, `sinc_tercero`, `id_tercero`)
                VALUES ('1', 'C.C', '$data[user_doc]', '$data[user_firstname]', '$data[user_secondname]', '$data[user_firstlastname]',' $data[user_secondlastename]', NULL, '$id_empresa', '', '$id_sucursal', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', 'Administrador', '0', NULL, '$data[user_email]', '$password', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '00', '0.00', NULL, NULL, NULL, NULL, NULL, NULL, 'false', '1', NULL, NULL, NULL, 'false', 'false', 'false', NULL, '0,0,0', '32,124,229', NULL, 'false', NULL);";
        $query = $this->mysqli->query($sql);
        if (!$query) {
            echo json_encode("error al insertar el usuario a la empresa");
            return;
        }
        echo json_encode("usuario insertado");
    }

}
