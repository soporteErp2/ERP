<?php

    class dbFunctions
    {

        public  $link;
        function __construct($link)
        {
            $this->link=$link;
        }

        public function query($sql){
            return mysql_query($sql,$this->link);
        }

        public function fetch_array($query){
            return mysql_fetch_array($query);
        }
    }

    // FUNCION PARA CONTABILIZAR LAS CUENTAS CONFIGURADAS DE UNA CUENTA CONTABILIZADA
    function contabilizacionSimultanea($id_documento,$tipo_documento,$id_sucursal,$id_empresa,$mysql){
        // VERIFICAR LA VARIABLE DE CONEXION A LA BASE DE DATOS ES UN OBJETO
        if (!is_object($mysql)){
            $link = $mysql;
            $mysql = new dbFunctions($link);
        }

        // CONTABILIDAD LOCAL
        $sql="SELECT * FROM asientos_colgaap WHERE activo=1 AND id_documento=$id_documento AND tipo_documento='$tipo_documento' AND id_sucursal=$id_sucursal AND id_empresa=$id_empresa";
        $query=$mysql->query($sql,$mysql->link);
        while ($row=$mysql->fetch_array($query)) {
            $id_cuenta = $row['id_cuenta'];
            $arrayPuc[$id_cuenta] = array('cuenta' => $row['codigo_cuenta'],'descripcion' => $row['cuenta'] );
            $arraycuentas[$id_cuenta][] = array(
                                                'id_documento'             => $row['id_documento'],
                                                'consecutivo_documento'    => $row['consecutivo_documento'],
                                                'tipo_documento'           => $row['tipo_documento'],
                                                'tipo_documento_extendido' => $row['tipo_documento_extendido'],
                                                'id_documento_cruce'       => $row['id_documento_cruce'],
                                                'tipo_documento_cruce'     => $row['tipo_documento_cruce'],
                                                'numero_documento_cruce'   => $row['numero_documento_cruce'],
                                                'fecha'                    => $row['fecha'],
                                                'debe'                     => $row['debe'],
                                                'haber'                    => $row['haber'],
                                                'id_cuenta'                => $row['id_cuenta'],
                                                'codigo_cuenta'            => $row['codigo_cuenta'],
                                                'cuenta'                   => $row['cuenta'],
                                                'id_tercero'               => $row['id_tercero'],
                                                'nit_tercero'              => $row['nit_tercero'],
                                                'tercero'                  => $row['tercero'],
                                                'id_sucursal'              => $row['id_sucursal'],
                                                'sucursal'                 => $row['sucursal'],
                                                'permiso_sucursal'         => $row['permiso_sucursal'],
                                                'id_empresa'               => $row['id_empresa'],
                                                'id_centro_costos'         => $row['id_centro_costos'],
                                                'codigo_centro_costos'     => $row['codigo_centro_costos'],
                                                'centro_costos'            => $row['centro_costos'],
                                                'id_flujo_efectivo'        => $row['id_flujo_efectivo'],
                                                'flujo_efectivo'           => $row['flujo_efectivo'],
                                                'id_sucursal_cruce'        => $row['id_sucursal_cruce'],
                                                'sucursal_cruce'           => $row['sucursal_cruce'],
                                                'observacion'              => $row['observacion'],
                                            );
            $whereCuentas .= ($whereCuentas=='')? " id_cuenta_principal='$id_cuenta' " : " OR id_cuenta_principal='$id_cuenta' " ;
        }

        // CONSULTAR LAS CUENTAS CONFIGURADAS A CAUSAR
        $sql="SELECT
                    id_cuenta_principal,
                    id_cuenta,
                    cuenta,
                    descripcion,
                    naturaleza
                FROM puc_cuentas_simultaneas WHERE activo=1 AND id_empresa=$id_empresa AND ($whereCuentas) ";
        $query=$mysql->query($sql,$mysql->link);
        while ($row=$mysql->fetch_array($query)) {
            $id_cuenta = $row['id_cuenta_principal'];
            $arrayCtasSimultaneas[$id_cuenta][] = array(
                                                        'id_cuenta'   => $row['id_cuenta'],
                                                        'cuenta'      => $row['cuenta'],
                                                        'descripcion' => $row['descripcion'],
                                                        'naturaleza'  => $row['naturaleza'],
                                                    );
        }

        // RECORRER LAS CUENTAS DEL DOCUMENTO
        foreach ($arraycuentas as $id_cuenta_principal => $arraycuentasResult) {
            foreach ($arraycuentasResult as $key => $arrayResult) {
                // RECORRER LAS CUENTAS SIMULTANEAS DE CADA CUENTA CAUSADA
                foreach ($arrayCtasSimultaneas[$id_cuenta_principal] as $key2 => $arrayResultCtas) {

                        switch ($arrayResultCtas['naturaleza']) {
                            case 'debito':
                                $debe  = ($arrayResult['debe']>0)? $arrayResult['debe'] : $arrayResult['haber'] ;
                                $haber = 0;
                                break;
                            case 'credito':
                                $debe  = 0;
                                $haber = ($arrayResult['debe']>0)? $arrayResult['debe'] : $arrayResult['haber'] ;
                                break;
                        }

                        $valueInsert .= "(
                                        '$arrayResult[id_documento]',
                                        '$arrayResult[consecutivo_documento]',
                                        '$arrayResult[tipo_documento]',
                                        '$arrayResult[tipo_documento_extendido]',
                                        '$arrayResult[id_documento_cruce]',
                                        '$arrayResult[tipo_documento_cruce]',
                                        '$arrayResult[numero_documento_cruce]',
                                        '$arrayResult[fecha]',
                                        '$debe',
                                        '$haber',
                                        '$arrayResultCtas[id_cuenta]',
                                        '$arrayResultCtas[cuenta]',
                                        '$arrayResultCtas[descripcion]',
                                        '$arrayResult[id_tercero]',
                                        '$arrayResult[nit_tercero]',
                                        '$arrayResult[tercero]',
                                        '$arrayResult[id_sucursal]',
                                        '$arrayResult[sucursal]',
                                        '$arrayResult[permiso_sucursal]',
                                        '$arrayResult[id_empresa]',
                                        '$arrayResult[id_centro_costos]',
                                        '$arrayResult[codigo_centro_costos]',
                                        '$arrayResult[centro_costos]',
                                        '$arrayResult[id_flujo_efectivo]',
                                        '$arrayResult[flujo_efectivo]',
                                        '$arrayResult[id_sucursal_cruce]',
                                        '$arrayResult[sucursal_cruce]',
                                        'Cuenta insertada automaticamente, configurada en la cuenta: ".$arrayPuc[$id_cuenta_principal]['cuenta'].' - '.$arrayPuc[$id_cuenta_principal]['descripcion']." '
                                    ),";

                }


            }
        }

        $valueInsert = substr($valueInsert, 0, -1);
        $sql="INSERT INTO asientos_colgaap
                (
                    id_documento,
                    consecutivo_documento,
                    tipo_documento,
                    tipo_documento_extendido,
                    id_documento_cruce,
                    tipo_documento_cruce,
                    numero_documento_cruce,
                    fecha,
                    debe,
                    haber,
                    id_cuenta,
                    codigo_cuenta,
                    cuenta,
                    id_tercero,
                    nit_tercero,
                    tercero,
                    id_sucursal,
                    sucursal,
                    permiso_sucursal,
                    id_empresa,
                    id_centro_costos,
                    codigo_centro_costos,
                    centro_costos,
                    id_flujo_efectivo,
                    flujo_efectivo,
                    id_sucursal_cruce,
                    sucursal_cruce,
                    observacion
                )
            VALUES $valueInsert ";
        $query=$mysql->query($sql,$mysql->link);



    }

?>