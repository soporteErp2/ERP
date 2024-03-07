<?php
    header('Access-Control-Allow-Origin: *');
    set_time_limit(0);

    //=========================// ARRAY TERCERO //=========================//
    $arrayTercero = array(
                'apiVersion'  => '2',
                'nit_empresa' => '123456',
                'username'    => 'usuario.administracion',
                'password'    => '123456789',
                'tercero'     => array
                (
                    'codigo_tipo_identificacion' => '1',
                    'codigo_ciudad_identificacion' => '7',
                    'numero_identificacion' => '7500000',
                    'dv' => '6',
                    'nombre' => 'JHON ERICK SAS',
                    'nombre_comercial' => 'JHON ERICK SAS',
                    'direccion' => 'calle 81',
                    'telefono1' => 'telefono 1',
                    'telefono2' => 'telefono 2',
                    'celular1' => 'celular 1',
                    'celular2' => 'celular 2',
                    'codigo_ciudad' => '6',
                    'codigo_regimen' => '3',
                    'cliente' => 'si',
                    'proveedor' => 'si',
                    'exento_iva' => 'no',
                    'nombre1' => 'JHON',
                    'nombre2' => '',
                    'apellido1' => 'MARROQUIN',
                    'apellido2' => '',
                ),
                'arrayContactos' => array
                (
                    '1' => array
                    (
                        'codigo_tipo_identificacion' => '1',
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
                        'codigo_tipo_identificacion' => '1',
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
                        'codigo_ubicacion' => '20',
                    ),
                    '2' => array
                    (
                        'nombre' => 'calle sucursal',
                        'direccion' => 'direccion sucursal',
                        'telefono1' => 'telefono 1 sucursal',
                        'telefono2' => 'telefono 2 sucursal',
                        'celular1' => 'celular 1 sucursal',
                        'celular2' => 'celular 2 sucursal',
                        'codigo_ubicacion' => '30'
                    )
                ),
            );

    if($_SERVER['SERVER_NAME'] == 'logicalerp.localhost'){
        $arrayTercero['apiVersion']  = '2';
        $arrayTercero['nit_empresa'] = '900467785';
        $arrayTercero['username']    = 'jhon.marroquin';
        $arrayTercero['password']    = 'jhon3rick';
    }

    // $_POST['dataJsonTerceros'] = json_encode($arrayTercero);
    // include('register.php'); exit;

    // echo '<div style="color:red;">'.json_encode($arrayTercero).'</div>';
    //=========================// ARRAY NOTAS //=========================//
    $arrayNotas = array(
                        'apiVersion'      => '1',
                        'nit_empresa'     => '123456',
                        'codigo_sucursal' => '2', // NOMBRE DE LA SUCURSAL DONDE SE REALIZA LA NOTA
                        'username'        => 'usuario.administracion',
                        'password'        => '123456789',
                        'documento'       => array(
                            'consecutivo'      => '1', // CONSECUTIVO CRUCE
                            'codigo_tipo_nota' => '1', // TIPO DE LA NOTA CONTABLE, DEBE ESTAR CREADA EN EL ERP
                            'fecha_documento'  => date("Y-m-d"),
                            'nit_tercero'      => '1112103967', // TERCERO GENERAL DE LA NOTA
                            'cuentas'          => array(
                                '1'=>array(
                                    'cuenta_colgaap'     => '110505', // CUENTA COLGAAP
                                    'naturaleza'         => 'debito', // NATURALEZA 'DEBITO' - 'CREDITO'
                                    'nit_tercero_cuenta' => '7500000', // TERCERO DE LA CUENTA (OBLIGATORIO)
                                    'saldo'              => '200', // SALDO QUE MOVERA LA CUENTA, LA SUMA DE LAS CUENTAS DEBEN SER SALDOS IGUALES
                                ),
                                '2'=>array(
                                    'cuenta_colgaap'     => '110505',
                                    'naturaleza'         => 'credito',
                                    'nit_tercero_cuenta' => '1112103967',
                                    'saldo'              => '200',
                                )
                            )
                        )
                    );

    if($_SERVER['SERVER_NAME'] == 'logicalerp.localhost'){
        $arrayNotas['apiVersion']      = '2';
        $arrayNotas['nit_empresa']     = '900467785';
        $arrayNotas['username']        = 'jhon.marroquin';
        $arrayNotas['password']        = 'jhon3rick';
        $arrayNotas['codigo_sucursal'] = '1';
    }

    $_POST['dataJsonNota'] = json_encode($arrayNotas);
    // echo $_POST['dataJsonNota']; exit;
    include('register.php'); exit;


    //=========================// ARRAY FACTURAS DE VENTA //=========================//
    $arrayFacturaVenta = array(
                            'apiVersion'           => '1',
                            'nit_empresa'          => '900467785',
                            'sucursal'             => 'CALI (Principal)', // NOMBRE DE LA SUCURSAL DONDE SE REALIZA LA FACTURA DE VENTA
                            'username'             => 'jhon.marroquin',
                            'password'             => '123456789',
                            'id_pais'              => '49',
                            'tipo_documento'       => 'factura_venta',
                            'fecha_documento'      => date("Y-m-d"),
                            'fecha_vencimiento'    => date("Y-m-d"),
                            'nit_tercero'          => '1112103967', // TERCERO A QUIEN SE LE REALIZA LA FACTURA
                            'cuenta_pago_colgaap'  => '130505',// CUENTA DE COBRO, DEBE ESTAR CREADA Y CONFIGURADA EN EL ERP
                            'prefijo_documento'    => 'FV',
                            'numero_documento'     => '201',
                            'id_empleado'          => '',
                            'cuentas'              => array(
                                '1'=>array(
                                            'cuenta_colgaap'       => '130505',
                                            'cuenta_niif'          => '',     //OPCIONAL SI NO TIENE EL TOMA POR DEFECTO LA CUENTA NIIF CONFIGURADA
                                            'naturaleza'           => 'debito',
                                            'codigo_centro_costos' => '', //OPCIONAL, APLICABLE SOLO A LAS CUENTAS 4 - 5 - 6
                                            'saldo'                => '200',
                                ),
                                '2'=>array(
                                            'cuenta_colgaap'     => '135515',
                                            'cuenta_niif'        => '',
                                            'naturaleza'         => 'debito',
                                            'codigo_centro_costos' => '',
                                            'saldo'              => '200',
                                ),
                                '3'=>array(
                                            'cuenta_colgaap'     => '143501',
                                            'cuenta_niif'        => '',
                                            'naturaleza'         => 'credito',
                                            'codigo_centro_costos' => '',
                                            'saldo'              => '200',
                                ),
                                '4'=>array(
                                            'cuenta_colgaap'     => '240801',
                                            'cuenta_niif'        => '',
                                            'naturaleza'         => 'credito',
                                            'codigo_centro_costos' => '',
                                            'saldo'              => '200',
                                ),
                                '5'=>array(
                                            'cuenta_colgaap'     => '413520',
                                            'cuenta_niif'        => '',
                                            'naturaleza'         => 'credito',
                                            'codigo_centro_costos' => '',
                                            'saldo'              => '200',
                                ),
                                '6'=>array(
                                            'cuenta_colgaap'     => '613516',
                                            'cuenta_niif'        => '',
                                            'naturaleza'         => 'debito',
                                            'codigo_centro_costos' => '',
                                            'saldo'              => '200',
                                )
                            )
                        );

    //=========================// ARRAY FACTURAS DE COMPRA //=========================//
    $arrayFacturaCompra = array(
                            'apiVersion'          => '1',
                            'nit_empresa'         => '900467785',
                            'sucursal'            => 'CALI (Principal)', // NOMBRE DE LA SUCURSAL DONDE SE REALIZA LA FACTURA
                            'username'            => 'jhon.marroquin',
                            'password'            => '123456789',
                            'id_pais'             => '49',
                            'tipo_documento'      => 'factura_compra',
                            'fecha_documento'     => date("Y-m-d"),
                            'fecha_vencimiento'   => date("Y-m-d"),
                            'nit_tercero'         => '1112103967', // TERCERO DE QUIEN ES LA FACTURA
                            'cuenta_pago_colgaap' => '22050101', // CUENTA DE COBRO, DEBE ESTAR CREADA Y CONFIGURADA EN EL ERP
                            'prefijo_documento'   => 'FC',  // PREFIJO DEL NUMERO DE LA FACTURA DE COMPRA
                            'numero_documento'    => '201', // NUMERO DE LA FACTURA DE COMPRA
                            'cuentas'             => array(
                                '1'=>array(
                                            'cuenta_colgaap'       => '412099',
                                            'cuenta_niif'          => '',     //OPCIONAL SI NO TIENE EL TOMA POR DEFECTO LA CUENTA NIIF CONFIGURADA
                                            'naturaleza'           => 'credito',
                                            'codigo_centro_costos' => '1010',
                                            'saldo'                => '200',
                                ),
                                '2'=>array(
                                            'cuenta_colgaap'     => '143501',
                                            'cuenta_niif'        => '',
                                            'naturaleza'         => 'debito',
                                            'codigo_centro_costos' => '1010',
                                            'saldo'              => '150',
                                ),
                                '3'=>array(
                                            'cuenta_colgaap'     => '240802',
                                            'cuenta_niif'        => '',
                                            'naturaleza'         => 'debito',
                                            'codigo_centro_costos' => '1010',
                                            'saldo'              => '50',
                                )
                            )
                        );

    if(!isset($_GET['debug'])){
        require_once "nuSoap/nusoap.php";
        // $cliente = new nusoap_client("http://logicalerp.localhost/logicalerp/web_service/register.php");
        $cliente = new nusoap_client("http://www.logicalsoft-erp.com/LOGICALERP/web_service/register.php");

        $error = $cliente->getError();
        if ($error) { echo "<h2>Constructor error</h2><pre>".$error."</pre>"; }
        $fecha = date("Y-m-d");

        // $result = $cliente->call("insertCuentasContables", array('arrayWs' => $arrayNotas));
        $result = $cliente->call("insertUpdateTercero", array('arrayWs' => $arrayTercero));

        // print_r($cliente); exit;

        if ($cliente->fault) {
            echo "<h2>Fault</h2><pre>";
            print_r($result);
            echo "</pre>";
        }
        else {
            $error = $cliente->getError();
            if ($error) {
                echo "<h2>Error</h2><pre>" . $error . "</pre>";
            }
            else {
                echo "<h2 style='background-color:silver;'>Web service</h2><pre>";
                print_r($result);
                echo "</pre>";
            }
        }
    }
    else{
        $debug = true;
        include('register.php');
        print_r(insertUpdateTercero($arrayTercero));
    }
?>