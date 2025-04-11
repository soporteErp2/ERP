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
        //variables para los documentos y documentos (para la impresion)
        $this->document_variables($id_empresa);
        // cuenta de pago por defecto del comprobante de egreso
        $this->default_ce_account($id_empresa);
        // configuracion empleados/nomina
        $this->payroll_config($id_empresa);


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

    public function note_type($id_empresa) {
        $sql="INSERT INTO tipo_nota_contable (descripcion,consecutivo,consecutivo_niif,documento_cruce,id_empresa)
							VALUES
							('NOTA GENERAL',1,1,'Si',$id_empresa),
							('NOTA BANCARIA',1,1,'No',$id_empresa),
							('SALDO INICIAL CONTABLE',1,1,'No',$id_empresa),
							('DEPRECIACION',1,1,'No',$id_empresa),
							('AMORTIZACION',1,1,'No',$id_empresa)";
        $query = $this->mysqli->query($sql);
    }

    public function taxes($id_empresa){
        $sql = "INSERT INTO impuestos (impuesto, valor, compra, cuenta_compra, cuenta_compra_niif, cuenta_compra_devolucion, cuenta_compra_devolucion_niif, venta, cuenta_venta, cuenta_venta_niif, cuenta_venta_devolucion, cuenta_venta_devolucion_niif, id_empresa)
							VALUES
								('IVA SERVICIOS 19%', '19.00', 'No', '', '', '', '', 'Si', '24080107', '24080107', '24080201', '24080201', '$id_empresa'),
								('IVA SERVICIOS 5%', '5.00', 'No', '', '', '', '', 'Si', '24080107', '24080107', '24080201', '24080201', '$id_empresa'),
								('IVA COMPRAS 5%', '5.00', 'Si', '24080220', '24080220', '24080220', '24080220', 'No', '', '', '', '', '$id_empresa'),
								('IVA COMPRAS 19%', '19.00', 'Si', '24080219', '24080219', '24080219', '24080219', 'No', '', '', '', '', '$id_empresa')";
        $query = $this->mysqli->query($sql);

    }

    public function retentions($id_empresa){
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

    public function employee_authentication($id_empresa){
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

    public function document_types($id_empresa){
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

    public function pay_methods($id_empresa){
        $sql = "INSERT INTO configuracion_metodos_pago (id, nombre, activo, codigo_metodo_pago_dian, id_empresa)
					VALUES
						('1', 'Efectivo', '1', '25', '$id_empresa'),
						('2', 'Cheque', '1', '26', '$id_empresa'),
						('3', 'Transferencia Bancaria', '1', '27', '$id_empresa'),
						('4', 'Consigancion Bancaria', '1', '28', '$id_empresa');";
        $query = $this->mysqli->query($sql);
    }

    public function employee_docs_types($id_empresa){
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

    public function default_pay_method($id_empresa){
        $sql="INSERT INTO configuracion_formas_pago (nombre,plazo,id_empresa)
                VALUES ('Contado','1',$id_empresa),
                        ('Semanal','7',$id_empresa),
                        ('Quincena','15',$id_empresa),
                        ('Mes','30',$id_empresa)";
        $query = $this->mysqli->query($sql);
    }

    public function default_payment_accounts($id_empresa){
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

    public function puc_config($id_empresa) {
        $sql = "INSERT INTO puc_configuracion (nombre,digitos,id_empresa)
							VALUES ('CLASE',1,$id_empresa),
									('GRUPO',2,$id_empresa),
									('CUENTA',4,$id_empresa),
									('SUBCUENTA',6,$id_empresa),
									('AUXILIARES',8,$id_empresa)";
        $query = $this->mysqli->query($sql);
    }

    public function types_units_items($id_empresa){
        $sql="INSERT INTO inventario_unidades (nombre, unidades,id_empresa)
                VALUES ('Unidad','1',$id_empresa),
                        ('Docena','12',$id_empresa),
                        ('Servicio','1',$id_empresa)";
        $query = $this->mysqli->query($sql);
        
    }

    public function third_party_configuration($id_empresa){
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

    public function purchase_order_type($id_empresa){
        $sql = "INSERT INTO compras_ordenes_tipos (nombre, id_empresa)
                    VALUES ('NUEVO PROYECTO', '$id_empresa'),
                        ('REPOSICION DE EQUIPOS', '$id_empresa'),
                        ('REFUERZO DE OPERACION', '$id_empresa'),
                        ('EQUIPOS PARA LA VENTA', '$id_empresa'),
                        ('SUMINISTROS', '$id_empresa')";
        $query = $this->mysqli->query($sql);
    }

    public function document_variables($id_empresa){
        $sql   = "INSERT INTO variables_grupos (nombre,id_empresa) VALUES
										('General','$id_empresa'),
										('Cotizacion de Venta','$id_empresa'),
										('Pedido de Venta','$id_empresa'),
										('Remision de Venta','$id_empresa'),
										('Factura de Venta','$id_empresa'),
										('Orden de Compra','$id_empresa'),
										('Factura de Compra','$id_empresa')";
        $query = $this->mysqli->query($sql);

        include_once ('../../configuraciones/variables_documentos.php');
        $sql   = "INSERT INTO variables (nombre,detalle,id_grupo,grupo,campo,tabla,funcion,automatica,id_empresa)
                            VALUES $valuesGeneral $valuesCV $valuesPV $valuesRV $valuesFV $valuesOC $valuesFC";
        $query = $this->mysqli->query($sql);

        //INSERTAR EL DOCUMENTO POR DEFECTO
        $sql = "INSERT INTO configuracion_documentos_erp (nombre,tipo,texto,id_empresa,empresa,id_sucursal) VALUES
                                    ('Cotizacion de Venta','CV','<style type=\"text/css\">\n.StyleTableHeader{\n	font-size		:10px;\n		font-family		:\"Segoe UI Light\",\"Helvetica Neue Light\",\"Segoe UI\",\"Helvetica Neue\",\"Trebuchet MS\",Helvetica,\"Droid Sans\",Tahoma,Geneva,sans-serif;\n		border			:1px solid #000;\nborder-collapse:collapse;\n	}\n	.StyleTableFooter{\n		font-size		:9px;\n		font-family		:\"Segoe UI Light\",\"Helvetica Neue Light\",\"Segoe UI\",\"Helvetica Neue\",\"Trebuchet MS\",Helvetica,\"Droid Sans\",Tahoma,Geneva,sans-serif;\n		border			:1px solid #000;\n	}\n.StyleTablaEncuesta td{ border-right:1px solid #000; border-bottom:1px solid #000;}\n.StyleTablaEncuesta{ border-collapse:collapse; border:none; }</style>\n<htmlpageheader class=\"SoloPDF\" name=\"MyHeader1\">\n<table border=\"1\" cellpadding=\"0\" cellspacing=\"0\" class=\"StyleTableHeader\" width=\"740\">\n	<tbody>\n		<tr>\n			<td align=\"center\">\n				<img alt=\"\" src=\"../../../../ARCHIVOS_PROPIOS/imagenes_empresas/empresa_1/formato_documentos/plataforma_LOGO.png\" style=\"width: 200px; height: 51px;\" /></td>\n			<td align=\"center\">\n				<span style=\"font-size:14px; font-weight:bold\"><span style=\"font-size:18px;\">COTIZACION DE VENTA</span></span></td>\n			<td>\n				<div style=\"text-align: left;\">\n					<strong><span style=\"font-size:12px;\">Codigo:&nbsp;<br />\n					Version: 1<br />\n					Vigencia:&nbsp;</span></strong></div>\n			</td>\n		</tr>\n		<tr>\n			<td align=\"center\" colspan=\"3\">\n				<span style=\"font-size:16px;\"><span style=\"font-weight: bold;\"><span style=\"background-color: rgb(255, 0, 0);\">[RAZON_SOCIAL]</span></span></span><br />\n				<span style=\"font-size:12px;\"><span style=\"font-family:tahoma,geneva,sans-serif;\"><span style=\"font-size:12px;\"><span style=\"background-color: rgb(255, 0, 0);\">[TIPO_REGIMEN]</span>&nbsp;<span style=\"background-color: rgb(255, 0, 0);\">[TIPO_IDENTIFICACION]</span>&nbsp;<span style=\"background-color: rgb(255, 0, 0);\">[NUMERO_IDENTIFICACION]</span></span></span><br />\n				<strong>SUCURSAL:</strong>&nbsp;<span style=\"background-color: rgb(255, 0, 0);\">[CV_SUCURSAL]</span>&nbsp;<br />\n				<strong>BODEGA:</strong>&nbsp;<span style=\"background-color: rgb(255, 0, 0);\">[CV_BODEGA]</span>&nbsp;</span><br />\n				<span style=\"font-size:12px;\">&nbsp;<span style=\"background-color: rgb(255, 0, 0);\">[DIRECCION]</span>&nbsp;</span>&nbsp;<span style=\"font-size:14px;\"><span style=\"font-size:12px;\">CALI-COLOMBIA</span></span><br />\n				<span style=\"font-size:12px;\"><strong>TELEFONO:</strong>&nbsp;<span style=\"background-color: rgb(255, 0, 0);\">[TELEFONO_EMPRESA]</span></span><br />\n				&nbsp;</td>\n		</tr>\n		<tr align=\"left\">\n			<td>\n				<span style=\"font-size:12px;\"><span style=\"font-weight: bold;\">FECHA SOLICITUD:<strong>&nbsp;</strong></span><strong><span style=\"background-color: rgb(255, 0, 0);\">[CV_FECHA_INICIAL]</span>&nbsp;</strong></span></td>\n			<td style=\"text-align: right;\">\n				<span style=\"font-size:12px;\"><span style=\"font-weight: bold;\">COTIZACION N.&nbsp;</span><strong><span style=\"background-color: rgb(255, 0, 0);\">[CV_CONSECUTIVO]</span>&nbsp;<span style=\"font-weight: bold;\">&nbsp;&nbsp;</span></strong></span></td>\n			<td style=\"text-align: right;\">\n				<span style=\"font-size:12px;\"><span style=\"font-weight: bold;\">Paginas({PAGENO} de {nb})</span></span></td>\n		</tr>\n	</tbody>\n</table>\n</htmlpageheader> <sethtmlpageheader name=\"MyHeader1\" show-this-page=\"1\" value=\"on\"></sethtmlpageheader> <span style=\"background-color: rgb(255, 0, 0);\">[CONTENIDO_DOCUMENTO]</span>\n<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"StyleTableCabecera\" style=\"font-size:10px\" width=\"740\">\n	<tbody>\n		<tr>\n			<td colspan=\"3\" height=\"60\">\n				&nbsp;</td>\n		</tr>\n		<tr>\n			<td align=\"center\" style=\"border-top:1px solid #000; font-size:14px\" width=\"350\">\n				Elaboro<br />\n				<span style=\"background-color: rgb(255, 0, 0);\">[CV_USUARIO]</span>&nbsp;<br />\n				<span style=\"background-color: rgb(255, 0, 0);\">[CV_CC_USUARIO]</span></td>\n			<td align=\"center\" width=\"40\">\n				&nbsp;</td>\n			<td align=\"center\" style=\"border-top:1px solid #000; font-size:14px\" width=\"350\">\n				Autorizacion Requisicion</td>\n		</tr>\n	</tbody>\n</table>\n<htmlpagefooter class=\"SoloPDF\" name=\"MyFooter1\">\n<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"StyleTableFooter\" style=\"font-size:10px\" width=\"740\">\n	<tbody>\n		<tr>\n			<td align=\"center\" width=\"400\">\n				<table cellpadding=\"1\" cellspacing=\"1\" class=\"StyleTablaEncuesta\" style=\"width: 400px;\">\n					<tbody>\n						<tr>\n							<td style=\"width: 250px;\">\n								<span style=\"font-size:12px;\"><strong>EVALUACION DE COMPRA</strong></span></td>\n							<td style=\"width: 50px;\">\n								<span style=\"font-size:12px;\"><strong>MARQUE (SI O NO)</strong></span></td>\n						</tr>\n						<tr>\n							<td>\n								<span style=\"font-size:12px;\">CUMPLE CON LAS ESPECIFICACIONES</span></td>\n							<td style=\"width: 100px;\">\n								&nbsp;</td>\n						</tr>\n						<tr>\n							<td>\n								<span style=\"font-size:12px;\">ESTADO FISICO DE LOS EQUIPOS</span></td>\n							<td>\n								&nbsp;</td>\n						</tr>\n						<tr>\n							<td>\n								<span style=\"font-size:12px;\">CALIDAD DEL EMPAQUE</span></td>\n							<td>\n								&nbsp;</td>\n						</tr>\n						<tr>\n							<td style=\"border-bottom: none;\">\n								<span style=\"font-size:12px;\">APROBADO PARA PAGO</span></td>\n							<td style=\"width: 100px; border-bottom: none;\">\n								&nbsp;</td>\n						</tr>\n					</tbody>\n				</table>\n			</td>\n			<td align=\"center\" width=\"250\">\n				<br />\n				<br />\n				<br />\n				<font size=\"3\"><b>_________________________________<br />\n				<span style=\"font-size:12px;\">Firma aceptacion</span></b></font></td>\n			<td style=\"border-left:1px solid;\" width=\"250\">\n				<span style=\"font-size:11px;\"><span style=\"font-weight: bold;\">&nbsp;Observacion Final</span></span><br />\n				<br />\n				<br />\n				<br />\n				&nbsp;</td>\n		</tr>\n	</tbody>\n</table>\n</htmlpagefooter> <sethtmlpagefooter name=\"MyFooter1\" value=\"on\"></sethtmlpagefooter> ',$id_empresa,'$razon_social','$id_sucursal'),
                                    ('Pedido de Venta','PV','<style type=\"text/css\">\n.StyleTableHeader{\n		font-size		:10px;\n		font-family		:\"Segoe UI Light\",\"Helvetica Neue Light\",\"Segoe UI\",\"Helvetica Neue\",\"Trebuchet MS\",Helvetica,\"Droid Sans\",Tahoma,Geneva,sans-serif;\n		border			:1px solid #000;\nborder-collapse:collapse;\n	}\n	.StyleTableFooter{\n		font-size		:9px;\n		font-family		:\"Segoe UI Light\",\"Helvetica Neue Light\",\"Segoe UI\",\"Helvetica Neue\",\"Trebuchet MS\",Helvetica,\"Droid Sans\",Tahoma,Geneva,sans-serif;\n		border			:1px solid #000;\n	}\n.StyleTablaEncuesta td{ border-right:1px solid #000; border-bottom:1px solid #000;}\n.StyleTablaEncuesta{ border-collapse:collapse; border:none; }</style>\n<htmlpageheader class=\"SoloPDF\" name=\"MyHeader1\">\n<table border=\"1\" cellpadding=\"0\" cellspacing=\"0\" class=\"StyleTableHeader\" width=\"740\">\n	<tbody>\n		<tr>\n			<td align=\"center\">\n				<img alt=\"\" src=\"../../../../ARCHIVOS_PROPIOS/imagenes_empresas/empresa_1/formato_documentos/plataforma_LOGO.png\" style=\"width: 200px; height: 51px;\" /></td>\n			<td align=\"center\">\n				<span style=\"font-size:14px; font-weight:bold\"><span style=\"font-size:18px;\">PEDIDO DE VENTA</span></span></td>\n			<td>\n				<div style=\"text-align: left;\">\n					<strong><span style=\"font-size:12px;\">Codigo: COM-PR-01-F03<br />\n					Version: 1<br />\n					Vigencia:&nbsp;<span style=\"color: rgb(38, 38, 38); font-family: arial, sans-serif; line-height: 16px;\">2015-03-16</span></span></strong></div>\n			</td>\n		</tr>\n		<tr>\n			<td align=\"center\" colspan=\"3\">\n				<span style=\"font-size:16px;\"><span style=\"font-weight: bold;\"><span style=\"background-color: rgb(255, 0, 0);\">[RAZON_SOCIAL]</span></span></span><br />\n				<span style=\"font-size:12px;\"><span style=\"font-family:tahoma,geneva,sans-serif;\"><span style=\"font-size:12px;\"><span style=\"background-color: rgb(255, 0, 0);\">[TIPO_REGIMEN]</span>&nbsp;<span style=\"background-color: rgb(255, 0, 0);\">[TIPO_IDENTIFICACION]</span>&nbsp;<span style=\"background-color: rgb(255, 0, 0);\">[NUMERO_IDENTIFICACION]</span></span></span><br />\n				<strong>SUCURSAL:</strong>&nbsp;<span style=\"background-color: rgb(255, 0, 0);\">[PV_SUCURSAL]</span>&nbsp;<br />\n				<strong>BODEGA:</strong>&nbsp;<span style=\"background-color: rgb(255, 0, 0);\">[PV_BODEGA]</span>&nbsp;</span><br />\n				<span style=\"font-size:12px;\">&nbsp;<span style=\"background-color: rgb(255, 0, 0);\">[DIRECCION]</span>&nbsp;</span>&nbsp;<span style=\"font-size:14px;\"><span style=\"font-size:12px;\">CALI-COLOMBIA</span></span><br />\n				<span style=\"font-size:12px;\"><strong>TELEFONO:</strong>&nbsp;<span style=\"background-color: rgb(255, 0, 0);\">[TELEFONO_EMPRESA]</span></span><br />\n				&nbsp;</td>\n		</tr>\n		<tr align=\"left\">\n			<td>\n				<span style=\"font-size:12px;\"><span style=\"font-weight: bold;\">FECHA SOLICITUD:<strong>&nbsp;</strong></span><strong><span style=\"background-color: rgb(255, 0, 0);\">[PV_FECHA_INICIAL]</span>&nbsp;</strong></span></td>\n			<td style=\"text-align: right;\">\n				<span style=\"font-size:12px;\"><span style=\"font-weight: bold;\">PEDIDO N.&nbsp;</span><strong><span style=\"background-color: rgb(255, 0, 0);\">[PV_CONSECUTIVO]</span>&nbsp;<span style=\"font-weight: bold;\">&nbsp;&nbsp;</span></strong></span></td>\n			<td style=\"text-align: right;\">\n				<span style=\"font-size:12px;\"><span style=\"font-weight: bold;\">Paginas({PAGENO} de {nb})</span></span></td>\n		</tr>\n	</tbody>\n</table>\n</htmlpageheader> <sethtmlpageheader name=\"MyHeader1\" show-this-page=\"1\" value=\"on\"></sethtmlpageheader> <span style=\"background-color: rgb(255, 0, 0);\">[CONTENIDO_DOCUMENTO]</span>\n<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"StyleTableCabecera\" style=\"font-size:10px\" width=\"740\">\n	<tbody>\n		<tr>\n			<td colspan=\"3\" height=\"60\">\n				&nbsp;</td>\n		</tr>\n		<tr>\n			<td align=\"center\" style=\"border-top:1px solid #000; font-size:14px\" width=\"350\">\n				Elaboro<br />\n				<span style=\"background-color: rgb(255, 0, 0);\">[PV_USUARIO]</span>&nbsp;<br />\n				<span style=\"background-color: rgb(255, 0, 0);\">[PV_CC_USUARIO]</span></td>\n			<td align=\"center\" width=\"40\">\n				&nbsp;</td>\n			<td align=\"center\" style=\"border-top:1px solid #000; font-size:14px\" width=\"350\">\n				Autorizacion Requisicion</td>\n		</tr>\n	</tbody>\n</table>\n<htmlpagefooter class=\"SoloPDF\" name=\"MyFooter1\">\n<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"StyleTableFooter\" style=\"font-size:10px\" width=\"740\">\n	<tbody>\n		<tr>\n			<td align=\"center\" width=\"400\">\n				<table cellpadding=\"1\" cellspacing=\"1\" class=\"StyleTablaEncuesta\" style=\"width: 400px;\">\n					<tbody>\n						<tr>\n							<td style=\"width: 250px;\">\n								<span style=\"font-size:12px;\"><strong>EVALUACION DE COMPRA</strong></span></td>\n							<td style=\"width: 50px;\">\n								<span style=\"font-size:12px;\"><strong>MARQUE (SI O NO)</strong></span></td>\n						</tr>\n						<tr>\n							<td>\n								<span style=\"font-size:12px;\">CUMPLE CON LAS ESPECIFICACIONES</span></td>\n							<td style=\"width: 100px;\">\n								&nbsp;</td>\n						</tr>\n						<tr>\n							<td>\n								<span style=\"font-size:12px;\">ESTADO FISICO DE LOS EQUIPOS</span></td>\n							<td>\n								&nbsp;</td>\n						</tr>\n						<tr>\n							<td>\n								<span style=\"font-size:12px;\">CALIDAD DEL EMPAQUE</span></td>\n							<td>\n								&nbsp;</td>\n						</tr>\n						<tr>\n							<td style=\"border-bottom: none;\">\n								<span style=\"font-size:12px;\">APROBADO PARA PAGO</span></td>\n							<td style=\"width: 100px; border-bottom: none;\">\n								&nbsp;</td>\n						</tr>\n					</tbody>\n				</table>\n			</td>\n			<td align=\"center\" width=\"250\">\n				<br />\n				<br />\n				<br />\n				<font size=\"3\"><b>_________________________________<br />\n				<span style=\"font-size:12px;\">Firma aceptacion</span></b></font></td>\n			<td style=\"border-left:1px solid;\" width=\"250\">\n				<span style=\"font-size:11px;\"><span style=\"font-weight: bold;\">&nbsp;Observacion Final</span></span><br />\n				<br />\n				<br />\n				<br />\n				&nbsp;</td>\n		</tr>\n	</tbody>\n</table>\n</htmlpagefooter> <sethtmlpagefooter name=\"MyFooter1\" value=\"on\"></sethtmlpagefooter> ',$id_empresa,'$razon_social','$id_sucursal'),
                                    ('Remision de Venta','RV','<style type=\"text/css\">\n.StyleTableHeader{\n		font-size		:10px;\n		font-family		:\"Segoe UI Light\",\"Helvetica Neue Light\",\"Segoe UI\",\"Helvetica Neue\",\"Trebuchet MS\",Helvetica,\"Droid Sans\",Tahoma,Geneva,sans-serif;\n		border			:1px solid #000;\nborder-collapse:collapse;\n	}\n	.StyleTableFooter{\n		font-size		:9px;\n		font-family		:\"Segoe UI Light\",\"Helvetica Neue Light\",\"Segoe UI\",\"Helvetica Neue\",\"Trebuchet MS\",Helvetica,\"Droid Sans\",Tahoma,Geneva,sans-serif;\n		border			:1px solid #000;\n	}\n.StyleTablaEncuesta td{ border-right:1px solid #000; border-bottom:1px solid #000;}\n.StyleTablaEncuesta{ border-collapse:collapse; border:none; }</style>\n<htmlpageheader class=\"SoloPDF\" name=\"MyHeader1\">\n<table border=\"1\" cellpadding=\"0\" cellspacing=\"0\" class=\"StyleTableHeader\" width=\"740\">\n	<tbody>\n		<tr>\n			<td align=\"center\">\n				<img alt=\"\" src=\"../../../../ARCHIVOS_PROPIOS/imagenes_empresas/empresa_1/formato_documentos/plataforma_LOGO.png\" style=\"width: 200px; height: 51px;\" /></td>\n			<td align=\"center\">\n				<span style=\"font-size:14px; font-weight:bold\"><span style=\"font-size:18px;\">REMISION DE VENTA</span></span></td>\n			<td>\n				<div style=\"text-align: left;\">\n					<strong><span style=\"font-size:12px;\">Codigo:<br />\n					Version:<br />\n					Vigencia:&nbsp;<span style=\"color: rgb(38, 38, 38); font-family: arial, sans-serif; line-height: 16px;\">2015-03-16</span></span></strong></div>\n			</td>\n		</tr>\n		<tr>\n			<td align=\"center\" colspan=\"3\">\n				<span style=\"font-size:16px;\"><span style=\"font-weight: bold;\"><span style=\"background-color: rgb(255, 0, 0);\">[RAZON_SOCIAL]</span></span></span><br />\n				<span style=\"font-size:12px;\"><span style=\"font-family:tahoma,geneva,sans-serif;\"><span style=\"font-size:12px;\"><span style=\"background-color: rgb(255, 0, 0);\">[TIPO_REGIMEN]</span>&nbsp;<span style=\"background-color: rgb(255, 0, 0);\">[TIPO_IDENTIFICACION]</span>&nbsp;<span style=\"background-color: rgb(255, 0, 0);\">[NUMERO_IDENTIFICACION]</span></span></span><br />\n				<strong>SUCURSAL:</strong>&nbsp;<span style=\"background-color: rgb(255, 0, 0);\">[RV_SUCURSAL]</span>&nbsp;<br />\n				<strong>BODEGA:</strong>&nbsp;<span style=\"background-color: rgb(255, 0, 0);\">[RV_BODEGA]</span>&nbsp;</span><br />\n				<span style=\"font-size:12px;\">&nbsp;<span style=\"background-color: rgb(255, 0, 0);\">[DIRECCION]</span>&nbsp;</span>&nbsp;<span style=\"font-size:14px;\"><span style=\"font-size:12px;\">CALI-COLOMBIA</span></span><br />\n				<span style=\"font-size:12px;\"><strong>TELEFONO:</strong>&nbsp;<span style=\"background-color: rgb(255, 0, 0);\">[TELEFONO_EMPRESA]</span></span><br />\n				&nbsp;</td>\n		</tr>\n		<tr align=\"left\">\n			<td>\n				<span style=\"font-size:12px;\"><span style=\"font-weight: bold;\">FECHA SOLICITUD:<strong>&nbsp;</strong></span><strong><span style=\"background-color: rgb(255, 0, 0);\">[RV_FECHA_INICIAL]</span>&nbsp;</strong></span></td>\n			<td style=\"text-align: right;\">\n				<span style=\"font-size:12px;\"><span style=\"font-weight: bold;\">REMISION N.&nbsp;</span><strong><span style=\"background-color: rgb(255, 0, 0);\">[RV_CONSECUTIVO]</span>&nbsp;<span style=\"font-weight: bold;\">&nbsp;&nbsp;</span></strong></span></td>\n			<td style=\"text-align: right;\">\n				<span style=\"font-size:12px;\"><span style=\"font-weight: bold;\">Paginas({PAGENO} de {nb})</span></span></td>\n		</tr>\n	</tbody>\n</table>\n</htmlpageheader> <sethtmlpageheader name=\"MyHeader1\" show-this-page=\"1\" value=\"on\"></sethtmlpageheader> <span style=\"background-color: rgb(255, 0, 0);\">[CONTENIDO_DOCUMENTO]</span>\n<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"StyleTableCabecera\" style=\"font-size:10px\" width=\"740\">\n	<tbody>\n		<tr>\n			<td colspan=\"3\" height=\"60\">\n				&nbsp;</td>\n		</tr>\n		<tr>\n			<td align=\"center\" style=\"border-top:1px solid #000; font-size:14px\" width=\"350\">\n				Elaboro<br />\n				<span style=\"background-color: rgb(255, 0, 0);\">[RV_USUARIO]</span>&nbsp;<br />\n				<span style=\"background-color: rgb(255, 0, 0);\">[RV_CC_USUARIO]</span></td>\n			<td align=\"center\" width=\"40\">\n				&nbsp;</td>\n			<td align=\"center\" style=\"border-top:1px solid #000; font-size:14px\" width=\"350\">\n				Autorizacion Requisicion</td>\n		</tr>\n	</tbody>\n</table>\n<htmlpagefooter class=\"SoloPDF\" name=\"MyFooter1\">\n<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"StyleTableFooter\" style=\"font-size:10px\" width=\"740\">\n	<tbody>\n		<tr>\n			<td align=\"center\" width=\"400\">\n				<table cellpadding=\"1\" cellspacing=\"1\" class=\"StyleTablaEncuesta\" style=\"width: 400px;\">\n					<tbody>\n						<tr>\n							<td style=\"width: 250px;\">\n								<span style=\"font-size:12px;\"><strong>EVALUACION DE COMPRA</strong></span></td>\n							<td style=\"width: 50px;\">\n								<span style=\"font-size:12px;\"><strong>MARQUE (SI O NO)</strong></span></td>\n						</tr>\n						<tr>\n							<td>\n								<span style=\"font-size:12px;\">CUMPLE CON LAS ESPECIFICACIONES</span></td>\n							<td style=\"width: 100px;\">\n								&nbsp;</td>\n						</tr>\n						<tr>\n							<td>\n								<span style=\"font-size:12px;\">ESTADO FISICO DE LOS EQUIPOS</span></td>\n							<td>\n								&nbsp;</td>\n						</tr>\n						<tr>\n							<td>\n								<span style=\"font-size:12px;\">CALIDAD DEL EMPAQUE</span></td>\n							<td>\n								&nbsp;</td>\n						</tr>\n						<tr>\n							<td style=\"border-bottom: none;\">\n								<span style=\"font-size:12px;\">APROBADO PARA PAGO</span></td>\n							<td style=\"width: 100px; border-bottom: none;\">\n								&nbsp;</td>\n						</tr>\n					</tbody>\n				</table>\n			</td>\n			<td align=\"center\" width=\"250\">\n				<br />\n				<br />\n				<br />\n				<font size=\"3\"><b>_________________________________<br />\n				<span style=\"font-size:12px;\">Firma aceptacion</span></b></font></td>\n			<td style=\"border-left:1px solid;\" width=\"250\">\n				<span style=\"font-size:11px;\"><span style=\"font-weight: bold;\">&nbsp;Observacion Final</span></span><br />\n				<br />\n				<br />\n				<br />\n				&nbsp;</td>\n		</tr>\n	</tbody>\n</table>\n</htmlpagefooter> <sethtmlpagefooter name=\"MyFooter1\" value=\"on\"></sethtmlpagefooter> ',$id_empresa,'$razon_social','$id_sucursal'),
                                    ('Factura de Venta','FV','<style type=\"text/css\">\n.StyleTableHeader{\n		font-size		:10px;\n		font-family		:\"Segoe UI Light\",\"Helvetica Neue Light\",\"Segoe UI\",\"Helvetica Neue\",\"Trebuchet MS\",Helvetica,\"Droid Sans\",Tahoma,Geneva,sans-serif;\n		border			:1px solid #000;\nborder-collapse:collapse;\n	}\n	.StyleTableFooter{\n		font-size		:9px;\n		font-family		:\"Segoe UI Light\",\"Helvetica Neue Light\",\"Segoe UI\",\"Helvetica Neue\",\"Trebuchet MS\",Helvetica,\"Droid Sans\",Tahoma,Geneva,sans-serif;\n		border			:1px solid #000;\n	}</style>\n<htmlpageheader class=\"SoloPDF\" name=\"MyHeader1\">\n<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"StyleTableHeader\" width=\"740\">\n	<tbody>\n		<tr>\n			<td align=\"center\">\n				<img alt=\"\" src=\"../../../../ARCHIVOS_PROPIOS/imagenes_empresas/empresa_1/formato_documentos/plataforma_LOGO.png\" style=\"width: 300px; height: 51px;\" /></td>\n			<td align=\"center\">\n				<span style=\"font-size:18px; font-weight:bold\">FACTURA DE VENTA</span><br />\n				<span style=\"font-size:22px; font-weight:bold\">No.<span style=\"background-color: rgb(255, 0, 0);\">[FV_NUMERO_FACTURA]</span></span></td>\n		</tr>\n		<tr>\n			<td align=\"center\">\n				<span style=\"font-size:18px; font-weight:bold\"><span style=\"background-color: rgb(255, 0, 0);\">[RAZON_SOCIAL]</span></span><br />\n				<span style=\"font-size:18px;\"><span style=\"background-color: rgb(255, 0, 0);\">[TIPO_REGIMEN]</span>&nbsp;<span style=\"background-color: rgb(255, 0, 0);\">[TIPO_IDENTIFICACION]</span>&nbsp;<span style=\"background-color: rgb(255, 0, 0);\">[NUMERO_IDENTIFICACION]</span></span><br />\n				<span style=\"font-size:14px; font-weight:bold\"><b>CALI OFICINA PRINCIPAL</b></span><br />\n				<span style=\"font-size:14px; font-weight:bold\">CALLE 3 No. 60-46 BARRIO PAMPALINDA</span><br />\n				<span style=\"font-size:14px; font-weight:bold\">CALI - VALLE</span></td>\n			<td align=\"center\">\n				<span style=\"font-size:14px;\">Paginas({PAGENO} de {nb})</span><br />\n				<br />\n				ACTIVIDAD ECONOMICA <span style=\"background-color: rgb(255, 0, 0);\">[ACTIVIDAD_ECONOMICA]</span><br />\n				RETENEDOR IVA A REGIMEN SIMPLIFICADO<br />\n				REGIMEN COMUN</td>\n		</tr>\n		<tr>\n			<td align=\"center\" colspan=\"2\">\n				AUTORIZACION DE FACTURACION No&nbsp;<span style=\"background-color: rgb(255, 0, 0);\">[FV_NUMERO_RESOLUCION_DIAN]</span>&nbsp;DE&nbsp;<span style=\"background-color: rgb(255, 0, 0);\">[FV_FECHA_RESOLUCION_DIAN]</span>&nbsp; &nbsp;DEL&nbsp;<span style=\"background-color: rgb(255, 0, 0);\">[FV_PREFIJO_RESOLUCION_DIAN]</span>&nbsp;<span style=\"background-color: rgb(255, 0, 0);\">[FV_NUMERO_INICIAL_RESOLUCION]</span>&nbsp;HASTA&nbsp;<span style=\"background-color: rgb(255, 0, 0);\">[FV_PREFIJO_RESOLUCION_DIAN]</span>&nbsp;<span style=\"background-color: rgb(255, 0, 0);\">[FV_NUMERO_FINAL_RESOLUCION]</span>&nbsp; - &nbsp;FACTURA IMPRESA POR COMPUTADOR</td>\n		</tr>\n	</tbody>\n</table>\n</htmlpageheader> <sethtmlpageheader name=\"MyHeader1\" show-this-page=\"1\" value=\"on\"></sethtmlpageheader> <span style=\"background-color: rgb(255, 0, 0);\">[CONTENIDO_DOCUMENTO]</span>\n<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"StyleTableCabecera\" style=\"font-size:10px\" width=\"740\">\n	<tbody>\n		<tr>\n			<td colspan=\"3\" height=\"60\">\n				&nbsp;</td>\n		</tr>\n		<tr>\n			<td align=\"center\" style=\"border-top:1px solid #000; font-size:14px\" width=\"350\">\n				Vendedor <span style=\"background-color: rgb(255, 0, 0);\">[FV_VENDEDOR]</span></td>\n			<td align=\"center\" width=\"40\">\n				&nbsp;</td>\n			<td align=\"center\" style=\"border-top:1px solid #000; font-size:14px\" width=\"350\">\n				Aceptacion de la factura<br />\n				Nombre legible de quien recibe y sello</td>\n		</tr>\n	</tbody>\n</table>\n<htmlpagefooter class=\"SoloPDF\" name=\"MyFooter1\">\n<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"StyleTableFooter\" style=\"font-size:10px\" width=\"740\">\n	<tbody>\n		<tr>\n			<td align=\"center\" width=\"488\">\n				ESTA FACTURA DE VENTA SE PAGARA A LA ORDEN DE&nbsp;<span style=\"background-color: rgb(255, 0, 0);\">[RAZON_SOCIAL]</span>&nbsp;<br />\n				A PARTIR DEL VENCIMIENTO SE CAUSARAN INTERESES DE MORA A LA TASA MAXIMA LEGAL VIGENTE<br />\n				FAVOR GIRAR CHEQUE CRUZADO Y ENVIAR EL COMPROBANTE DE PAGO AL DEPARTAMENTO DE CARTERA<br />\n				&nbsp;</td>\n			<td align=\"center\" width=\"250\">\n				<span style=\"font-size:16px; font-weight:bold\">PAGOS POR CONSIGNACION</span><br />\n				<span style=\"font-size:12px;\">BBVA CTA.CTE.397009739<br />\n				BANCOLOMBIA CTA.CTE.381-239127-37<br />\n				BANCOLOMBIA CTA.AH.3001-5259857<br />\n				BCO. BOGOTA CTA.CTE.249243387 </span></td>\n		</tr>\n		<tr>\n			<td colspan=\"2\">\n				&nbsp;</td>\n		</tr>\n	</tbody>\n</table>\n</htmlpagefooter> <sethtmlpagefooter name=\"MyFooter1\" value=\"on\"></sethtmlpagefooter> ',$id_empresa,'$razon_social','$id_sucursal'),
                                    ('Orden de Compra', 'OC', '<style type=\"text/css\">\n.StyleTableHeader{\n		font-size		:10px;\n		font-family		:\"Segoe UI Light\",\"Helvetica Neue Light\",\"Segoe UI\",\"Helvetica Neue\",\"Trebuchet MS\",Helvetica,\"Droid Sans\",Tahoma,Geneva,sans-serif;\n		border			:1px solid #000;\nborder-collapse:collapse;\n	}\n	.StyleTableFooter{\n		font-size		:9px;\n		font-family		:\"Segoe UI Light\",\"Helvetica Neue Light\",\"Segoe UI\",\"Helvetica Neue\",\"Trebuchet MS\",Helvetica,\"Droid Sans\",Tahoma,Geneva,sans-serif;\n		border			:1px solid #000;\n	}\n.StyleTablaEncuesta td{ border-right:1px solid #000; border-bottom:1px solid #000;}\n.StyleTablaEncuesta{ border-collapse:collapse; border:none; }</style>\n<htmlpageheader class=\"SoloPDF\" name=\"MyHeader1\">\n<table border=\"1\" cellpadding=\"0\" cellspacing=\"0\" class=\"StyleTableHeader\" width=\"740\">\n	<tbody>\n		<tr>\n			<td align=\"center\">\n				<img alt=\"\" src=\"../../../../ARCHIVOS_PROPIOS/imagenes_empresas/empresa_1/formato_documentos/plataforma_LOGO.png\" style=\"width: 200px; height: 51px;\" /></td>\n			<td align=\"center\">\n				<span style=\"font-size:14px; font-weight:bold\"><span style=\"font-size:18px;\">ORDEN DE COMPRA</span> </span></td>\n			<td>\n				<div style=\"text-align: left;\">\n					<strong><span style=\"font-size:12px;\">Codigo: COM-PR-01-F03<br />\n					Version: 1<br />\n					Vigencia:&nbsp;<span style=\"color: rgb(38, 38, 38); font-family: arial, sans-serif; line-height: 16px;\">2015-03-16</span></span></strong></div>\n			</td>\n		</tr>\n		<tr>\n			<td align=\"center\" colspan=\"3\">\n				<span style=\"font-size:16px;\"><span style=\"font-weight: bold;\"><span style=\"background-color: rgb(255, 0, 0);\">[RAZON_SOCIAL]</span></span></span><br />\n				<span style=\"font-size:12px;\"><span style=\"font-family:tahoma,geneva,sans-serif;\"><span style=\"font-size:12px;\"><span style=\"background-color: rgb(255, 0, 0);\">[TIPO_REGIMEN]</span>&nbsp;<span style=\"background-color: rgb(255, 0, 0);\">[TIPO_IDENTIFICACION]</span>&nbsp;<span style=\"background-color: rgb(255, 0, 0);\">[NUMERO_IDENTIFICACION]</span></span></span><br />\n				<strong>SUCURSAL:</strong>&nbsp;<span style=\"background-color: rgb(255, 0, 0);\">[OC_SUCURSAL]</span>&nbsp;<br />\n				<strong>BODEGA:</strong>&nbsp;<span style=\"background-color: rgb(255, 0, 0);\">[OC_BODEGA]</span>&nbsp;</span><br />\n				<span style=\"font-size:12px;\">&nbsp;<span style=\"background-color: rgb(255, 0, 0);\">[DIRECCION]</span>&nbsp;</span>&nbsp;<span style=\"font-size:14px;\"><span style=\"font-size:12px;\">CALI-COLOMBIA</span></span><br />\n				<span style=\"font-size:12px;\"><strong>TELEFONO:</strong>&nbsp;<span style=\"background-color: rgb(255, 0, 0);\">[TELEFONO_EMPRESA]</span></span><br />\n				&nbsp;</td>\n		</tr>\n		<tr align=\"left\">\n			<td>\n				<span style=\"font-size:12px;\"><span style=\"font-weight: bold;\">FECHA SOLICITUD:<strong>&nbsp;</strong></span><strong><span style=\"background-color: rgb(255, 0, 0);\">[OC_FECHA_INICIAL]</span>&nbsp;</strong></span></td>\n			<td style=\"text-align: right;\">\n				<span style=\"font-size:12px;\"><span style=\"font-weight: bold;\">ORDEN N.&nbsp;</span><strong><span style=\"background-color: rgb(255, 0, 0);\">[OC_CONSECUTIVO]</span>&nbsp;<span style=\"font-weight: bold;\">&nbsp;&nbsp;</span></strong></span></td>\n			<td style=\"text-align: right;\">\n				<span style=\"font-size:12px;\"><span style=\"font-weight: bold;\">Paginas({PAGENO} de {nb})</span></span></td>\n		</tr>\n	</tbody>\n</table>\n</htmlpageheader> <sethtmlpageheader name=\"MyHeader1\" show-this-page=\"1\" value=\"on\"></sethtmlpageheader> <span style=\"background-color: rgb(255, 0, 0);\">[CONTENIDO_DOCUMENTO]</span>\n<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"StyleTableCabecera\" style=\"font-size:10px\" width=\"740\">\n	<tbody>\n		<tr>\n			<td colspan=\"3\" height=\"60\">\n				&nbsp;</td>\n		</tr>\n		<tr>\n			<td align=\"center\" style=\"border-top:1px solid #000; font-size:14px\" width=\"350\">\n				Elaboro<br />\n				<span style=\"background-color: rgb(255, 0, 0);\">[OC_USUARIO]</span>&nbsp;<br />\n				<span style=\"background-color: rgb(255, 0, 0);\">[OC_CC_USUARIO]</span></td>\n			<td align=\"center\" width=\"40\">\n				&nbsp;</td>\n			<td align=\"center\" style=\"border-top:1px solid #000; font-size:14px\" width=\"350\">\n				Autorizacion Requisicion</td>\n		</tr>\n	</tbody>\n</table>\n<htmlpagefooter class=\"SoloPDF\" name=\"MyFooter1\">\n<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"StyleTableFooter\" style=\"font-size:10px\" width=\"740\">\n	<tbody>\n		<tr>\n			<td align=\"center\" width=\"400\">\n				<table cellpadding=\"1\" cellspacing=\"1\" class=\"StyleTablaEncuesta\" style=\"width: 400px;\">\n					<tbody>\n						<tr>\n							<td style=\"width: 250px;\">\n								<span style=\"font-size:12px;\"><strong>EVALUACION DE COMPRA</strong></span></td>\n							<td style=\"width: 50px;\">\n								<span style=\"font-size:12px;\"><strong>MARQUE (SI O NO)</strong></span></td>\n						</tr>\n						<tr>\n							<td>\n								<span style=\"font-size:12px;\">CUMPLE CON LAS ESPECIFICACIONES</span></td>\n							<td style=\"width: 100px;\">\n								&nbsp;</td>\n						</tr>\n						<tr>\n							<td>\n								<span style=\"font-size:12px;\">ESTADO FISICO DE LOS EQUIPOS</span></td>\n							<td>\n								&nbsp;</td>\n						</tr>\n						<tr>\n							<td>\n								<span style=\"font-size:12px;\">CALIDAD DEL EMPAQUE</span></td>\n							<td>\n								&nbsp;</td>\n						</tr>\n						<tr>\n							<td style=\"border-bottom: none;\">\n								<span style=\"font-size:12px;\">APROBADO PARA PAGO</span></td>\n							<td style=\"width: 100px; border-bottom: none;\">\n								&nbsp;</td>\n						</tr>\n					</tbody>\n				</table>\n			</td>\n			<td align=\"center\" width=\"250\">\n				<br />\n				<br />\n				<br />\n				<font size=\"3\"><b>_________________________________<br />\n				<span style=\"font-size:12px;\">Firma aceptacion</span></b></font></td>\n			<td style=\"border-left:1px solid;\" width=\"250\">\n				<span style=\"font-size:11px;\"><span style=\"font-weight: bold;\">&nbsp;Observacion Final</span></span><br />\n				<br />\n				<br />\n				<br />\n				&nbsp;</td>\n		</tr>\n	</tbody>\n</table>\n</htmlpagefooter> <sethtmlpagefooter name=\"MyFooter1\" value=\"on\"></sethtmlpagefooter> ', '$id_empresa', '$razon_social', '$id_sucursal'),
                                    ('Factura de Compra', 'FC', '<style type=\"text/css\">\n.StyleTableHeader{\n	font-size		:10px;\n		font-family		:\"Segoe UI Light\",\"Helvetica Neue Light\",\"Segoe UI\",\"Helvetica Neue\",\"Trebuchet MS\",Helvetica,\"Droid Sans\",Tahoma,Geneva,sans-serif;\n		border			:1px solid #000;\nborder-collapse:collapse;\n	}\n	.StyleTableFooter{\n		font-size		:9px;\n		font-family		:\"Segoe UI Light\",\"Helvetica Neue Light\",\"Segoe UI\",\"Helvetica Neue\",\"Trebuchet MS\",Helvetica,\"Droid Sans\",Tahoma,Geneva,sans-serif;\n		border			:1px solid #000;\n	}\n.StyleTableFooter td{ border-right:1px solid #000; border-bottom:1px solid #000; padding-left: 5px; }</style>\n<htmlpageheader class=\"SoloPDF\" name=\"MyHeader1\">\n<table border=\"1\" cellpadding=\"0\" cellspacing=\"0\" class=\"StyleTableHeader\" width=\"740\">\n	<tbody>\n		<tr>\n			<td align=\"center\">\n				<img alt=\"\" src=\"../../../../../ARCHIVOS_PROPIOS/imagenes_empresas/empresa_1/formato_documentos/plataforma_LOGO.png\" style=\"width: 200px; height: 51px;\" /></td>\n			<td align=\"center\">\n				<span style=\"font-size:14px; font-weight:bold\"><span style=\"font-size:18px;\">FACTURA DE COMPRA</span> </span></td>\n			<td>\n				<div style=\"text-align: left;\">\n					<strong><span style=\"font-size:12px;\">&nbsp;Codigo: COM-PR-01-F03<br />\n					&nbsp;Version: 1<br />\n					&nbsp;Vigencia:&nbsp;<span style=\"color: rgb(38, 38, 38); font-family: arial, sans-serif; line-height: 16px;\">2015-03-16</span></span></strong></div>\n			</td>\n		</tr>\n		<tr>\n			<td align=\"center\" colspan=\"3\">\n				<span style=\"font-size:16px;\"><span style=\"font-weight: bold;\"><span style=\"background-color: rgb(255, 0, 0);\">[RAZON_SOCIAL]</span></span></span><br />\n				<span style=\"font-size:12px;\"><span style=\"font-family:tahoma,geneva,sans-serif;\"><span style=\"font-size:12px;\"><span style=\"background-color: rgb(255, 0, 0);\">[TIPO_REGIMEN]</span>&nbsp;<span style=\"background-color: rgb(255, 0, 0);\">[TIPO_IDENTIFICACION]</span>&nbsp;<span style=\"background-color: rgb(255, 0, 0);\">[NUMERO_IDENTIFICACION]</span></span></span><br />\n				<strong>SUCURSAL:</strong>&nbsp;<span style=\"background-color: rgb(255, 0, 0);\">[FC_SUCURSAL]</span>&nbsp;<br />\n				<span style=\"font-size:12px;\">&nbsp;<span style=\"background-color: rgb(255, 0, 0);\">[DIRECCION]</span>&nbsp;</span>&nbsp;<span style=\"font-size:14px;\"><span style=\"font-size:12px;\">BOGOTA-COLOMBIA</span></span><br />\n				<span style=\"font-size:12px;\"><strong>TELEFONO:</strong>&nbsp;<span style=\"background-color: rgb(255, 0, 0);\">[TELEFONO_EMPRESA]</span></span><br />\n				&nbsp;</span></td>\n		</tr>\n		<tr align=\"left\">\n			<td>\n				<span style=\"font-size:11px;\"><strong><span style=\"font-weight: bold;\">&nbsp;FECHA SOLICITUD:&nbsp;</span></strong><span style=\"background-color: rgb(255, 0, 0);\">[FC_FECHA_EMISION]</span>&nbsp;<br />\n				<strong>&nbsp;FECHA DE VENCIMIENTO:&nbsp;</strong><span style=\"background-color: rgb(255, 0, 0);\">[FC_FECHA_VENCIMIENTO]</span>&nbsp;</span></td>\n			<td>\n				<span style=\"font-size:11px;\"><strong><span style=\"font-weight: bold;\">&nbsp;FC N.&nbsp;</span></strong><span style=\"background-color: rgb(255, 0, 0);\">[FC_PREFIJO]</span>&nbsp;<span style=\"background-color: rgb(255, 0, 0);\">[FC_NUMERO]</span>&nbsp;<strong style=\"font-size: 12px;\">&nbsp;</strong><br />\n				<strong><span style=\"font-weight: bold;\">&nbsp;CONSECUTIVO N.</span></strong>&nbsp;<span style=\"background-color: rgb(255, 0, 0);\">[FC_CONSECUTIVO]</span>&nbsp;&nbsp;&nbsp;</span></td>\n			<td style=\"text-align: right;\">\n				<span style=\"font-size:12px;\"><span style=\"font-weight: bold;\">Paginas({PAGENO} de {nb})</span></span></td>\n		</tr>\n	</tbody>\n</table>\n</htmlpageheader> <sethtmlpageheader name=\"MyHeader1\" show-this-page=\"1\" value=\"on\"></sethtmlpageheader> <span style=\"background-color: rgb(255, 0, 0);\">[CONTENIDO_DOCUMENTO]</span>\n<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"StyleTableCabecera\" style=\"font-size:10px\" width=\"740\">\n	<tbody>\n		<tr>\n			<td colspan=\"3\" height=\"60\">\n				&nbsp;</td>\n		</tr>\n		<tr>\n			<td align=\"center\" style=\"border-top:1px solid #000; font-size:14px\" width=\"350\">\n				Elaboro<br />\n				<span style=\"background-color: rgb(255, 0, 0);\">[OC_USUARIO]</span>&nbsp;<br />\n				<span style=\"background-color: rgb(255, 0, 0);\">[OC_CC_USUARIO]</span></td>\n			<td align=\"center\" width=\"40\">\n				&nbsp;</td>\n			<td align=\"center\" style=\"font-size:14px\" width=\"350\">\n				&nbsp;</td>\n		</tr>\n	</tbody>\n</table>\n<htmlpagefooter class=\"SoloPDF\" name=\"MyFooter1\"></htmlpagefooter><br />\n<sethtmlpagefooter name=\"MyFooter1\" value=\"on\"></sethtmlpagefooter>', '$id_empresa', 'PLATAFORMA COLOMBIA S.A.S.   ', '$id_sucursal');";
        $query = $this->mysqli->query($sql);
    }

    public function default_ce_account($id_empresa) {
        $sql   = "INSERT INTO configuracion_comprobante_egreso (cuenta,descripcion,id_empresa) VALUES ('2','PASIVOS',$id_empresa)";
        $query = $this->mysqli->query($sql);
        
    }

    public function payroll_config($id_empresa){
        $sql = "INSERT INTO nomina_tipos_liquidacion (nombre,dias,id_empresa) VALUES ('NOMINA QUINCENAL','15',$id_empresa) ";
        $query = $this->mysqli->query($sql);

        $sql = "INSERT INTO nomina_tipo_contrato (descripcion,dias,id_empresa)
									VALUES
									('TERMINO INDEFINIDO','0',$id_empresa),
								 	('TERMINO FIJO','365',$id_empresa),
								 	('OBRA O LABOR','365',$id_empresa),
								 	('TEMPORAL','365',$id_empresa),
								 	('APRENDIZ SENA ETAPA LECTIVA','365',$id_empresa),
								 	('APRENDIZ SENA ETAPA PRODUCTIVA','180',$id_empresa),
								 	('PRACTICANTE UNIVERSITARIO','180',$id_empresa)";
        $query = $this->mysqli->query($sql);

        // INSERTAR LOS GRUPOS DE TRABAJO DE LA NOMINA
        $sql = "INSERT INTO nomina_grupos_trabajo (nombre,id_empresa)
									VALUES
									('ADMINISTRACION',$id_empresa),
								 	('VENTAS',$id_empresa),
								 	('PRODUCCION',$id_empresa) ";
        $query = $this->mysqli->query($sql);

        // INSERTAR LOS NIVELES DE RIESGO LABORAL DE LA NOMINA
        $sql = "INSERT INTO nomina_niveles_riesgos_laborales (nombre,porcentaje,id_empresa)
									VALUES
									('RIESGO 1  (0.522%)',0.522,$id_empresa),
									('RIESGO 2 (1.044%)',1.044,$id_empresa),
									('RIESGO 3 (2.436%)',2.436,$id_empresa),
									('RIESGO 4 (4.35%)',4.350,$id_empresa),
									('RIESGO 5 (6.96%)',6.960,$id_empresa)";
        $query = $this->mysqli->query($sql);

		// CONFIGURACION DEL CERTIFICADO DE INGRESOS Y RETENCIONES DE LOS EMPLEADOS
		$sql   = "INSERT INTO certificado_ingreso_retenciones_empleados_secciones (id, nombre, nombre_total, codigo_total, id_empresa)
					VALUES
					(1,'Concepto de los Ingresos', 'Total de ingresos brutos (Sume casillas 37 a 41)', '42', $id_empresa),
					(2,'Concepto de los aportes', 'Valor de la retención en la fuente por salarios y demás pagos laborales', '46', $id_empresa)";
        $query = $this->mysqli->query($sql);

		$sql   = "INSERT INTO certificado_ingreso_retenciones_empleados_secciones_filas (id_seccion, nombre, codigo, id_empresa)
					VALUES
						('1', 'Pagos al empleado  (No incluya valores de las casillas 38 a 41)', '37', $id_empresa),
						('1', 'Cesantias e intereses de cesantias efectivamente pagadas en el periodo', '38', $id_empresa),
						('1', 'Gastos de representacion', '39', $id_empresa),
						('1', 'Pensiones de jubilacion, vejez o invalidez', '40', $id_empresa),
						('1', 'Otros ingresos como empleado', '41', $id_empresa),
						('2', 'Aportes obligatorios por salud', '43', $id_empresa),
						('2', 'Aportes obligatorios a fondos de pensiones y solidaridad pensional', '44', $id_empresa),
						('2', 'Aportes voluntarios, a fondos de pensiones y cuentas AFC', '45', $id_empresa)";
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
