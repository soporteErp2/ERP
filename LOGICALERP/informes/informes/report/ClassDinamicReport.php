<?php
    // error_reporting(E_ALL);
    // include_once('../../../../configuracion/conectar.php');
    // include_once('../../../../configuracion/define_variables.php');
    include '../../../funciones_globales/Clases/ClassRecursive/ClassRecursive.php';
    // header('Content-Type: text/html; charset=UTF-8');

    if (!isset($arrayCentroCostosJSON) && $arrayCentroCostosJSON=='[]') { $arrayCentroCostosJSON=''; }

    /**
    * @class ClassDinamicReport
    *
    */
    class ClassDinamicReport extends ClassRecursive
    {
        private $mysql                        = '';
        private $id_formato                   = '';
        private $fechaInicio                  = '';
        private $fechaFinal                   = '';
        private $separador_miles              = '';
        private $separador_decimales          = '';
        private $id_empresa                   = '';
        private $ccosFiltro                   = '';
        private $arrayGrupo                   = '';
        private $xlsPrint                     = '';
        private $arrayfilasFormato            = '';
        private $arraySeccionesCuentasFormato = '';
        private $whereAsientos                = '';
        private $whereIdTerceros              = '';
        private $arrayColumnasFormato         = '';
        private $arrayTerceros                = '';
        private $arrayJoined                  = '';
        private $arrayMeses                   = '';
        private $arrayFilasCcosFormato        = '';
        private $arrayFilasDocumentosFormato  = '';
        private $arrayFilasTercerosFormato    = '';


        /**
        * @method construct
        * @param int id del formato
        * @param srt fecha respectiva del periodo
        * @param obj objeto de conexion mysql
        */
        function __construct(
                                $id_formato,
                                $MyInformeFiltroFechaInicio,
                                $MyInformeFiltroFechaFinal,
                                $separador_miles,
                                $separador_decimales,
                                $arrayCentroCostosJSON,
                                $arrayGrupoJSON,
                                $id_empresa,
                                $id_sucursal,
                                $empresaGrupo=false,
                                $nit='',
                                $IMPRIME_XLS,
                                $mysql
                            )
        {
            parent::__construct('expand',NULL);
            $arrayNit = explode("-", $_SESSION['NITEMPRESA']);
            $this->id_formato          = $id_formato;
            $this->fechaInicio         = $MyInformeFiltroFechaInicio;
            $this->fechaFinal          = $MyInformeFiltroFechaFinal;
            $this->separador_miles     = $separador_miles;
            $this->separador_decimales = $separador_decimales;
            $this->id_empresa          = $id_empresa;
            $this->id_sucursal         = $id_sucursal;
            $this->ccosFiltro          = json_decode($arrayCentroCostosJSON,true);
            $this->arrayGrupo          = json_decode($arrayGrupoJSON,true);
            $this->empresaGrupo        = $empresaGrupo;
            $this->nit                 = ($nit<>'')? $nit :  $arrayNit[0] ;
            $this->xlsPrint            = $IMPRIME_XLS;
            $this->mysql               = $mysql;

            $this->getReportInfo();
        }

        /**
         * curl Funcion para consumo de api con curl
         * @param  Array $params Array con los parametros necesarios para el consumo del api
         * @param  String       $params.Authorization Si la peticion lleva un header de autorizacion entonces se envia la cabcera completa
         * @param  String       $params.request_url Url del api a consumir
         * @param  String       $params.request_method Metodo a usar en el consumo del API (GET,POST,PUT,DELETE)
         * @param  String       $params.data Datos a enviar al Api
         * @return Array        Lista con la respuesta del consumo del api
         */
        public function curlApi($params){
            $client = curl_init();
            $options = array(
                                CURLOPT_HTTPHEADER     => array(
                                                            'Content-Type: application/json',
                                                            "$params[Authorization]"),
                                CURLOPT_URL            => "$params[request_url]",
                                CURLOPT_CUSTOMREQUEST  => "$params[request_method]",
                                CURLOPT_RETURNTRANSFER => true,
                                CURLOPT_POSTFIELDS     => $params['data'],
                                CURLOPT_SSL_VERIFYPEER => false,
                            );
            curl_setopt_array($client, $options);
            $response = curl_exec($client);
            $curl_errors=curl_error($client);
            if(!empty($curl_errors)){
                $response['status']               = 'failed';
                $response['errors'][0]['titulo']  = curl_getinfo($client) ;
                $response['errors'][0]['detalle'] = curl_error($client);
                // return;
            }
            $httpCode = curl_getinfo($client, CURLINFO_HTTP_CODE);
            curl_close($client);
            return $response;
        }

        /**
         * getReportInfo Consultar la informacion del informe, filtros, nombre, etc.
         */
        public function getReportInfo(){
            $sql="SELECT
                        codigo,
                        nombre,
                        filtro_terceros,
                        filtro_ccos,
                        filtro_corte_anual,
                        filtro_corte_mensual,
                        filtro_rango_fechas,
                        filtro_cuentas,
                        comparativo_misma_fecha,
                        dinamico,
                        asientos
                    FROM informes_formatos
                    WHERE activo=1
                    AND id_empresa=$this->id_empresa
                    AND id=$this->id_formato
                    ";
            $query=$this->mysql->query($sql);
            $this->arrayReportInfo = array(
                                            'codigo'                  => $this->mysql->result($query,0,'codigo'),
                                            'nombre'                  => $this->mysql->result($query,0,'nombre'),
                                            'filtro_terceros'         => $this->mysql->result($query,0,'filtro_terceros'),
                                            'filtro_ccos'             => $this->mysql->result($query,0,'filtro_ccos'),
                                            'filtro_corte_anual'      => $this->mysql->result($query,0,'filtro_corte_anual'),
                                            'filtro_corte_mensual'    => $this->mysql->result($query,0,'filtro_corte_mensual'),
                                            'filtro_rango_fechas'     => $this->mysql->result($query,0,'filtro_rango_fechas'),
                                            'filtro_cuentas'          => $this->mysql->result($query,0,'filtro_cuentas'),
                                            'comparativo_misma_fecha' => $this->mysql->result($query,0,'comparativo_misma_fecha'),
                                            'dinamico'                => $this->mysql->result($query,0,'dinamico'),
                                            'asientos'                => $this->mysql->result($query,0,'asientos'),
                                        );
            $this->tabla_asientos = ($this->mysql->result($query,0,'asientos')=='Local')? "asientos_colgaap" : "asientos_niif" ;
        }

        /**
        * @method setSeccionesFormato secciones del formato
        */
        private function setSeccionesFormato()
        {
            $sql   = "SELECT
                            id,
                            nombre,
                            titulo,
                            codigo_seccion_padre,
                            totalizado
                        FROM informes_formatos_secciones
                        WHERE activo=1
                        AND id_empresa=$this->id_empresa
                        AND id_formato=$this->id_formato
                        ORDER BY CAST(codigo_seccion_padre AS int) DESC";
            $query = $this->mysql->query($sql,$this->mysql->link);
            while ($row=$this->mysql->fetch_array($query)) {
                $this->maxSeccion = ($this->maxSeccion<$row['id'])? $row['id'] : $this->maxSeccion ;
                $arrayTemp['items'][$row['id']]  = array(
                                                    'id'                   => $row['id'],
                                                    'codigo_seccion_padre' => $row['codigo_seccion_padre'],
                                                    'nombre'               => $row['nombre'],
                                                    'titulo'               => $row['titulo'],
                                                    'totalizado'           => $row['totalizado'],
                                                );
                $arrayTemp['parents'][$row['codigo_seccion_padre']][] = $row['id'];
            }

            $this->arraySeccionesFormato = $arrayTemp;
        }

        /**
        * @method setFilasCuentasFormato cuentas de los conceptos del formato
        */
        private function setFilasCuentasFormato()
        {
            $sql   = "SELECT
                            id,
                            id_formato,
                            codigo_formato,
                            nombre_formato,
                            id_seccion,
                            seccion,
                            id_cuenta_inicial,
                            cuenta_inicial,
                            descripcion_cuenta_inicial,
                            id_cuenta_final,
                            cuenta_final,
                            descripcion_cuenta_final,
                            forma_calculo
                        FROM
                            informes_formatos_secciones_cuentas
                        WHERE
                            activo=1
                            AND id_empresa=$this->id_empresa
                            AND id_formato=$this->id_formato";
            $query = $this->mysql->query($sql,$this->mysql->link);
            while ($row=$this->mysql->fetch_array($query)) {
                $id_seccion = $row['id_seccion'];
                $id_columna = $row['id_columna'];

                // $menus['items'][$row['id']] = $row;

                // $menus['items'][$row['id']]['html'] = "<i onclick='agregarModificarSeccion(".$row['id'].")' class='material-icons' title='Editar' style='font-size:17px;' >edit</i>";


                $arrayTemp[$id_seccion][$row['id']] = array(
                                                            'id_formato'                 => $row['id_formato'],
                                                            'codigo_formato'             => $row['codigo_formato'],
                                                            'nombre_formato'             => $row['nombre_formato'],
                                                            'id_seccion'                 => $row['id_seccion'],
                                                            'seccion'                    => $row['seccion'],
                                                            'id_columna'                 => $row['id_columna'],
                                                            'columna'                    => $row['columna'],
                                                            'id_cuenta_inicial'          => $row['id_cuenta_inicial'],
                                                            'cuenta_inicial'             => $row['cuenta_inicial'],
                                                            'descripcion_cuenta_inicial' => $row['descripcion_cuenta_inicial'],
                                                            'id_cuenta_final'            => $row['id_cuenta_final'],
                                                            'cuenta_final'               => $row['cuenta_final'],
                                                            'descripcion_cuenta_final'   => $row['descripcion_cuenta_final'],
                                                            'forma_calculo'              => $row['forma_calculo'],
                                                            );

                $whereTemp.=($whereTemp=='')? "CAST(codigo_cuenta AS CHAR) >='$row[cuenta_inicial]' AND CAST(codigo_cuenta AS CHAR) <= '$row[cuenta_final]' " : " OR CAST(codigo_cuenta AS CHAR) >='$row[cuenta_inicial]' AND CAST(codigo_cuenta AS CHAR) <= '$row[cuenta_final]'" ;

            }

            $this->arraySeccionesCuentasFormato = $arrayTemp;
            $this->whereAsientos = " AND ($whereTemp)";
        }

        /**
        * @method setAsientos consultar los asientos contables
        */
        private function setAsientos()
        {

                $whereFechaCol1    = " AND fecha BETWEEN '$this->fechaInicio' AND '$this->fechaFinal' ";
                $whereFechaAntCol1 = " AND fecha<'$this->fechaInicio' ";

                $this->title ="<tr><td style='font-size:11px;text-align:center;'>$_SESSION[NOMBRESUCURSAL]<br>$this->fechaInicio a $this->fechaFinal </td></tr>";

                $fechaIni = date( 'Y-m-d' ,strtotime ( '-1 year' , strtotime ( $this->fechaInicio ) ) );
                $fechaFin  = date( 'Y-m-d' ,strtotime ( '-1 year' , strtotime ( $this->fechaFinal  ) ) );
                $whereFechaCol2    = " AND fecha BETWEEN '$fechaIni' AND '$fechaFin' ";
                $whereFechaAntCol2 = " AND fecha<'$fechaIni' ";

                $arrayDateIni = explode("-", $this->fechaInicio);
                $arrayDateEnd = explode("-", $fechaIni);
                $this->anioInicio = $arrayDateIni[0];
                $this->anioFin    = $arrayDateEnd[0];

            foreach ($this->ccosFiltro as $indice => $id_centro_costos) {
                $whereCcos .= ($whereCcos=='')? ' id_centro_costos='.$id_centro_costos : ' OR id_centro_costos='.$id_centro_costos;
            }
            $whereCcos = ($whereCcos<>'')? " AND ( $whereCcos )" : "" ;

            // $arraySeccionesCuentasFormato[$id_seccion][$id_columna][$id_fila]

            // COLUMNA 1
            $sql="SELECT
                        codigo_cuenta,
                        cuenta,
                        id_tercero,
                        nit_tercero,
                        tercero,
                        SUM(debe) AS debito,
                        SUM(haber) AS credito,
                        codigo_centro_costos,
                        tipo_documento
                    FROM
                        $this->tabla_asientos
                    WHERE
                        activo=1
                        AND id_empresa=$this->id_empresa
                        AND tipo_documento<>'NCC'
                        $whereFechaCol1
                        $this->whereAsientos
                        $whereCcos
                        GROUP BY id_tercero,codigo_cuenta,codigo_centro_costos,tipo_documento;";
            $query=$this->mysql->query($sql,$this->mysql->link);
            while ($row=$this->mysql->fetch_array($query)) {

                $whereTemp .=($whereTemp=='')? 'id='.$row['id_tercero'] : ' OR id='.$row['id_tercero'] ;
                // echo "$row[cuenta] - $row[debito] | $row[credito]<br>";
                // RECORRER LA CONFIGURACION DE CUENTAS FILAS PARA ASIGNARLO AL ARRAY
                foreach ($this->arraySeccionesCuentasFormato as $id_seccion => $seccionResul){
                    foreach ($seccionResul as $default_id => $arrayResul) {
                        $search_ini = strpos($row['codigo_cuenta'],$arrayResul['cuenta_inicial']);
                        $search_end = strpos($row['codigo_cuenta'],$arrayResul['cuenta_final']);

                        if ($row['codigo_cuenta']>=$arrayResul['cuenta_inicial'] && $row['codigo_cuenta']<=$arrayResul['cuenta_final']) {
                            $this->arrayAsientos['1'][$row['codigo_cuenta']]['debito'] += $row['debito'];
                            $this->arrayAsientos['1'][$row['codigo_cuenta']]['credito'] += $row['credito'];

                            $this->arraySeccionesCuentasFormato[$id_seccion][$default_id]['1']['debito'] += $row['debito'];
                            $this->arraySeccionesCuentasFormato[$id_seccion][$default_id]['1']['credito'] += $row['credito'];

                            // CUENTAS POR FILA
                            // $whereTemp.=($whereTemp=='')?  ;
                            $this->arraySeccionesCuentasFormato[$id_seccion]['whereAsientos'][$arrayResul['cuenta_inicial']][$arrayResul['cuenta_final']] = $arrayResul['cuenta_inicial'];
                            // $this->arraySeccionesCuentasFormato[$id_seccion]['whereAsientos']['cuenta_final']     = $arrayResul['cuenta_final'];

                        }

                    }
                }

            }

            if ($this->arrayReportInfo['comparativo_misma_fecha']<>'Si'){ return; }
            // print_r($this->arrayAsientos);
            // COLUMNA 2
            $sql="SELECT
                        codigo_cuenta,
                        cuenta,
                        id_tercero,
                        nit_tercero,
                        tercero,
                        SUM(debe) AS debito,
                        SUM(haber) AS credito,
                        codigo_centro_costos,
                        tipo_documento
                    FROM
                        $this->tabla_asientos
                    WHERE
                        activo=1
                        AND id_empresa=$this->id_empresa
                        AND tipo_documento<>'NCC'
                        $whereFechaCol2
                        $this->whereAsientos
                        $whereCcos
                        GROUP BY id_tercero,codigo_cuenta,codigo_centro_costos,tipo_documento;";
            $query=$this->mysql->query($sql,$this->mysql->link);
            while ($row=$this->mysql->fetch_array($query)) {

                $whereTemp .=($whereTemp=='')? 'id='.$row['id_tercero'] : ' OR id='.$row['id_tercero'] ;

                // RECORRER LA CONFIGURACION DE CUENTAS FILAS PARA ASIGNARLO AL ARRAY

                foreach ($this->arraySeccionesCuentasFormato as $id_seccion => $seccionResul){
                    foreach ($seccionResul as $default_id => $arrayResul) {
                        $search_ini = strpos($row['codigo_cuenta'],$arrayResul['cuenta_inicial']);
                        $search_end = strpos($row['codigo_cuenta'],$arrayResul['cuenta_final']);

                        if ($row['codigo_cuenta']>=$arrayResul['cuenta_inicial'] && $row['codigo_cuenta']<=$arrayResul['cuenta_final']) {
                            $this->arrayAsientos['2'][$row['codigo_cuenta']]['debito'] += $row['debito'];
                            $this->arrayAsientos['2'][$row['codigo_cuenta']]['credito'] += $row['credito'];

                            $this->arraySeccionesCuentasFormato[$id_seccion][$default_id]['2']['debito'] += $row['debito'];
                            $this->arraySeccionesCuentasFormato[$id_seccion][$default_id]['2']['credito'] += $row['credito'];

                        }

                    }
                }
            }

     }

        /**
         * @method getAsientos consultar las cuentas del grupo empresarial
         */
        public function getAsientos(){
            $whereFechaCol1    = " AND fecha BETWEEN '$this->fechaInicio' AND '$this->fechaFinal' ";
            $whereFechaAntCol1 = " AND fecha<'$this->fechaInicio' ";

            $this->title ="<tr><td style='font-size:11px;text-align:center;'>$_SESSION[NOMBRESUCURSAL]<br>$this->fechaInicio a $this->fechaFinal </td></tr>";

            $fechaIni = date( 'Y-m-d' ,strtotime ( '-1 year' , strtotime ( $this->fechaInicio ) ) );
            $fechaFin  = date( 'Y-m-d' ,strtotime ( '-1 year' , strtotime ( $this->fechaFinal  ) ) );
            $whereFechaCol2    = " AND fecha BETWEEN '$fechaIni' AND '$fechaFin' ";
            $whereFechaAntCol2 = " AND fecha<'$fechaIni' ";

            $arrayDateIni = explode("-", $this->fechaInicio);
            $arrayDateEnd = explode("-", $fechaIni);
            $this->anioInicio = $arrayDateIni[0];
            $this->anioFin    = $arrayDateEnd[0];

            $this->whereAsientos = base64_encode($this->whereAsientos);
            // CONSULTAR API PARA LA PRIMERA COLUMNA
            $params = '';
            $params['request_url']    = "http://logicalerp.localhost/api/v1/contabilidad/?fecha_inicio=$this->fechaInicio&fecha_final=$this->fechaFinal&custonWhere=$this->whereAsientos&asientos=".$this->arrayReportInfo['asientos'];
            $params['request_method'] = "GET";
            $params['Authorization']  = "Authorization: Basic ".base64_encode('usuario.informes:$2y$10$Kye1ukwGdbtume0/QiIIB.igXWGn1flxaiHvzPaavJwwQWFCmo9Gi:'.$this->nit);
            $response = $this->curlApi($params);

            $arrayResponse = (is_array($response))? $response : json_decode($response,true) ;
            if ($arrayResponse['status']=='failed'){
                echo "<b>Error</b><br>".$arrayResponse['detalle'];
                exit;
            }
            // print_r($response);
            foreach ($arrayResponse["data"] as $key => $row) {
                $whereTemp .=($whereTemp=='')? 'id='.$row['id_tercero'] : ' OR id='.$row['id_tercero'] ;
                // echo "$row[cuenta] - $row[debito] | $row[credito]<br>";
                // RECORRER LA CONFIGURACION DE CUENTAS FILAS PARA ASIGNARLO AL ARRAY
                foreach ($this->arraySeccionesCuentasFormato as $id_seccion => $seccionResul){
                    foreach ($seccionResul as $default_id => $arrayResul) {
                        $search_ini = strpos($row['codigo_cuenta'],$arrayResul['cuenta_inicial']);
                        $search_end = strpos($row['codigo_cuenta'],$arrayResul['cuenta_final']);

                        if ($row['codigo_cuenta']>=$arrayResul['cuenta_inicial'] && $row['codigo_cuenta']<=$arrayResul['cuenta_final']) {
                            $this->arrayAsientos['1'][$row['codigo_cuenta']]['debito'] += $row['debito'];
                            $this->arrayAsientos['1'][$row['codigo_cuenta']]['credito'] += $row['credito'];

                            $this->arraySeccionesCuentasFormato[$id_seccion][$default_id]['1']['debito'] += $row['debito'];
                            $this->arraySeccionesCuentasFormato[$id_seccion][$default_id]['1']['credito'] += $row['credito'];

                        }

                    }
                }

            }

            // CONSULTAR API PARA LA SEGUNDA COLUMNA
            $params = '';
            $params['request_url']    = "http://logicalerp.localhost/api/v1/contabilidad/?fecha_inicio=$this->fechaIni&fecha_final=$this->fechaFin&custonWhere=$this->whereAsientos&asientos=".$this->arrayReportInfo['asientos'];
            $params['request_method'] = "GET";
            $params['Authorization']  = "Authorization: Basic ".base64_encode('usuario.informes:$2y$10$Kye1ukwGdbtume0/QiIIB.igXWGn1flxaiHvzPaavJwwQWFCmo9Gi:'.$this->nit);
            $response = $this->curlApi($params);

            $arrayResponse = (is_array($response))? $response : json_decode($response,true) ;
            if ($arrayResponse['status']=='failed'){
                echo "<b>Error</b><br>".$arrayResponse['detalle'];
                exit;
            }

            foreach ($arrayResponse["data"] as $key => $row) {
                $whereTemp .=($whereTemp=='')? 'id='.$row['id_tercero'] : ' OR id='.$row['id_tercero'] ;
                // echo "$row[cuenta] - $row[debito] | $row[credito]<br>";
                // RECORRER LA CONFIGURACION DE CUENTAS FILAS PARA ASIGNARLO AL ARRAY
                foreach ($this->arraySeccionesCuentasFormato as $id_seccion => $seccionResul){
                    foreach ($seccionResul as $default_id => $arrayResul) {
                        $search_ini = strpos($row['codigo_cuenta'],$arrayResul['cuenta_inicial']);
                        $search_end = strpos($row['codigo_cuenta'],$arrayResul['cuenta_final']);

                        if ($row['codigo_cuenta']>=$arrayResul['cuenta_inicial'] && $row['codigo_cuenta']<=$arrayResul['cuenta_final']) {
                            $this->arrayAsientos['2'][$row['codigo_cuenta']]['debito'] += $row['debito'];
                            $this->arrayAsientos['2'][$row['codigo_cuenta']]['credito'] += $row['credito'];

                            $this->arraySeccionesCuentasFormato[$id_seccion][$default_id]['2']['debito'] += $row['debito'];
                            $this->arraySeccionesCuentasFormato[$id_seccion][$default_id]['2']['credito'] += $row['credito'];

                        }

                    }
                }

            }
        }

        /**
         * mergeData asignar los valores a cada fila
         */
        public function mergeData(){
            // print_r($this->arraySeccionesCuentasFormato);
            foreach ($this->arraySeccionesCuentasFormato as $id_seccion => $seccionResul){
                foreach ($seccionResul as $default_id => $arrayResul) {
                    if ($default_id=='whereAsientos') { continue; }
                    // echo $default_id;
                    // print_r($arrayResul);
                    // echo "<br>";
                    switch ($arrayResul['forma_calculo']) {
                        case 'suma_debitos':
                            $valor1 = $arrayResul[1]['debito'];
                            $valor2 = $arrayResul[2]['debito'];
                            break;
                        case 'suma_creditos':
                            $valor1 = $arrayResul[1]['credito'];
                            $valor2 = $arrayResul[2]['credito'];
                            break;
                        case 'debito_menos_credito':
                            $valor1 = $arrayResul[1]['debito']-$arrayResul[1]['credito'];
                            $valor2 = $arrayResul[2]['debito']-$arrayResul[2]['credito'];
                            break;
                        case 'credito_menos_debito':
                            $valor1 = $arrayResul[1]['credito']-$arrayResul[1]['debito'];
                            $valor2 = $arrayResul[2]['credito']-$arrayResul[2]['debito'];
                            break;
                        case 'saldo_actual':
                            $valor1 += abs( (($arrayResul[1]['saldo_anterior']+$arrayResul[1]['debito'])-$arrayResul[1]['credito']) );
                            $valor2 += abs( (($arrayResul[2]['saldo_anterior']+$arrayResul[2]['debito'])-$arrayResul[2]['credito']) );
                            break;
                        case 'saldo_inicial':
                            $valor1 += $arrayResul[1]['saldo_anterior'];
                            $valor2 += $arrayResul[2]['saldo_anterior'];
                            break;
                    }
                    // $percent_1  = ;
                    // $percent_2  = ;
                    $diferencia = $valor1-$valor2;
                    $percent_3  = $diferencia/$valor2;

                    // print_r($arrayResul);
                    // echo "ini ".$this->arraySeccionesFormato['items'][$id_seccion]['valor1']."<br>";
                    $this->arraySeccionesFormato['items'][$id_seccion]['diferencia'] += $diferencia;
                    $this->arraySeccionesFormato['items'][$id_seccion]['percent_3']  += $percent_3;
                    $this->arraySeccionesFormato['items'][$id_seccion]['valor1']     += $valor1;
                    $this->arraySeccionesFormato['items'][$id_seccion]['valor2']     += $valor2;
                    // echo "end ".$this->arraySeccionesFormato['items'][$id_seccion]['valor1']."<br>";

                    $this->diferencia += $diferencia;
                    $this->percent_3  += $percent_3;
                    $this->valor1     += $valor1;
                    $this->valor2     += $valor2;

                }
            }
            // print_r($this->arraySeccionesFormato['items'][$id_seccion]);
            // $this->arraySeccionesFormato['parents'][0][] = $this->maxSeccion+1;
            // $this->arraySeccionesFormato['items'][$this->maxSeccion+1]['nombre'] = "<b>Totales";
            // $this->arraySeccionesFormato['items'][$this->maxSeccion+1]['css'] = " class='totales' ";
            // $this->arraySeccionesFormato['items'][$this->maxSeccion+1]['html'] = "
            //                                                                     <div>".number_format($this->percent_3,0,$this->separador_miles,$this->separador_decimales)."</div>
            //                                                                     <div>".number_format($this->diferencia,0,$this->separador_miles,$this->separador_decimales)."</div>
            //                                                                     <div>".number_format($this->percent_2,0,$this->separador_miles,$this->separador_decimales)."</div>
            //                                                                     <div>".number_format($this->valor2,0,$this->separador_miles,$this->separador_decimales)."</div>
            //                                                                     <div>".number_format($this->percent_1,0,$this->separador_miles,$this->separador_decimales)."</div>
            //                                                                     <div>".number_format($this->valor1,0,$this->separador_miles,$this->separador_decimales)."</div>
            //                                                                     ";

            // ASIGNAR VALORES A LAS SECCIONES PADRE
            foreach ($this->arraySeccionesFormato['items'] as $id_seccion => $arrayResul){
                $this->arraySeccionesFormato['items'][$arrayResul['codigo_seccion_padre']]['diferencia'] += $this->arraySeccionesFormato['items'][$id_seccion]['diferencia'];
                $this->arraySeccionesFormato['items'][$arrayResul['codigo_seccion_padre']]['percent_3']  += $this->arraySeccionesFormato['items'][$id_seccion]['percent_3'];
                $this->arraySeccionesFormato['items'][$arrayResul['codigo_seccion_padre']]['valor1']     += $this->arraySeccionesFormato['items'][$id_seccion]['valor1'];
                $this->arraySeccionesFormato['items'][$arrayResul['codigo_seccion_padre']]['valor2']     += $this->arraySeccionesFormato['items'][$id_seccion]['valor2'];
            }

            foreach ($this->arraySeccionesFormato['items'] as $id_seccion => $arrayResul){
                $this->arraySeccionesFormato['items'][$id_seccion]['percent_1']  = $arrayResul['valor1']/$this->arraySeccionesFormato['items'][$arrayResul['codigo_seccion_padre']]['valor1']*100;
                $this->arraySeccionesFormato['items'][$id_seccion]['percent_2']  = $arrayResul['valor2']/$this->arraySeccionesFormato['items'][$arrayResul['codigo_seccion_padre']]['valor2']*100;
                $this->arraySeccionesFormato['items'][$id_seccion]['percent_3']  = $arrayResul['valor2']/$arrayResul['diferencia'];
            }


            //ASIGNAR LOS VALORES PORCENTUALES DE LA COLUMNA

            foreach ($this->arraySeccionesFormato as $tipo => $arrayTipo) {
                foreach ($arrayTipo as $id_seccion => $arrayResul) {
                    $diferencia = number_format($arrayResul['diferencia'],0,$this->separador_miles,$this->separador_decimales);
                    // $percent_3  = number_format($arrayResul['percent_3'],0,$this->separador_miles,$this->separador_decimales);
                    $percent_3  = number_format($arrayResul['percent_3'],2,$this->separador_miles,$this->separador_decimales);
                    $valor1     = number_format($arrayResul['valor1'],0,$this->separador_miles,$this->separador_decimales);
                    $percent_1  = number_format($arrayResul['percent_1'],0,$this->separador_miles,$this->separador_decimales);
                    $valor2     = number_format($arrayResul['valor2'],0,$this->separador_miles,$this->separador_decimales);
                    $percent_2  = number_format($arrayResul['percent_2'],0,$this->separador_miles,$this->separador_decimales);

                    if ($arrayResul['totalizado']=='true') {
                        if ($this->arrayReportInfo['comparativo_misma_fecha']=='Si'){
                            $this->arraySeccionesFormato[$tipo][$id_seccion]['html'] .= "
                                                                                        <div>$percent_3</div>
                                                                                        <div>$diferencia</div>
                                                                                        <div>$percent_2</div>
                                                                                        <div>$valor2</div>
                                                                                        ";
                        }
                        $this->arraySeccionesFormato[$tipo][$id_seccion]['html'] .= "
                                                                                    <div>$percent_1</div>
                                                                                    <div>$valor1</div>
                                                                                    ";

                    }

                    // SI TIENE CUENTAS MOSTRAR ICONO PARA VER LAS CUENTAS
                    if (is_array($this->arraySeccionesCuentasFormato[$id_seccion])) {
                        // RECORRER LAS CUENTAS INICIALES Y FINALES PARA EL WHERE DEL DETALLE
                        $whereTemp = '';
                        foreach ($this->arraySeccionesCuentasFormato[$id_seccion]['whereAsientos'] as $cuenta_inicial => $arrayFinalCount) {
                            foreach ($arrayFinalCount as $cuenta_final => $value) {
                                $whereTemp.=($whereTemp=='')? "CAST(codigo_cuenta AS CHAR) >=$cuenta_inicial AND CAST(codigo_cuenta AS CHAR) <= $cuenta_final " : " OR CAST(codigo_cuenta AS CHAR) >=$cuenta_inicial AND CAST(codigo_cuenta AS CHAR) <= $cuenta_final" ;
                            }
                        }
                         $whereTemp = " AND ($whereTemp)";
                        // [$arrayResul['cuenta_inicial']][$arrayResul['cuenta_final']] = $arrayResul['cuenta_inicial'];

                        // $whereAsientos =  " AND ( ".str_replace("'", "\'", $this->arraySeccionesCuentasFormato[$id_seccion]['whereAsientos']).") ";
                        // $whereAsientos = json_encode($whereAsientos);
                        $params = "{nit:'$this->nit',fecha_inicio:'$this->fechaInicio',fecha_final:'$this->fechaFinal',custonWhere:'$whereTemp',asientos:'".$this->arrayReportInfo['asientos']."'}";
                        // $this->arraySeccionesFormato[$tipo][$id_seccion]['nombre']=" <img src='img/book.png'  style='width: 20px;height: 20px;cursor:pointer;' > &nbsp;&nbsp;".$this->arraySeccionesFormato[$tipo][$id_seccion]['nombre'];
                        $this->arraySeccionesFormato[$tipo][$id_seccion]['nombre']=" <i onclick=\"ventanaCuentasSeccion($params)\" class='material-icons' style='font-size: 20px;color:#7b7a7a;' title ='ver cuentas'>menu_book</i> &nbsp;&nbsp;".$this->arraySeccionesFormato[$tipo][$id_seccion]['nombre'];
                    }
                    // <i class="material-icons">menu_book</i>
                }
            }

        }

        public function getStyles(){
            ?>
                <style>
                    .tree li div, .table-form div{
                        float      : right;
                        width      : 100px;
                        text-align : right;
                        padding    : 1px;
                        cursor     : default;
                    }


                    .tree-view ul:hover{
                        background-color: #CCC;
                    }

                    .tree-view .totales{
                        background-color : #2A80B9;
                        color            : #fff;
                    }

                </style>
            <?php
        }

        public function getTitle(){
            ?>
                <div style="width:100%; text-align:center">
                    <table align="center" style="text-align:center;">
                        <tbody><tr><td class="titulo_informe_empresa" style="text-align:center;"><b><?= $_SESSION['NOMBREEMPRESA']; ?></b></td></tr>
                        <tr><td style="font-size:13px;text-align:center;"><b>NIT</b> <?= $_SESSION['NITEMPRESA'] ?></td></tr>
                        <tr><td style="width:100%; font-weight:bold; font-size:14px; text-align:center;text-transform: uppercase;"><?= $this->arrayReportInfo['nombre']; ?></td></tr>
                        <tr><td style="font-size:11px;text-align:center;">Periodo del <?= $this->fechaInicio ?> al <?= $this->fechaFinal ?> </td></tr>
                        <!--<tr><td style="font-size:11px; text-align:center;" >Impreso: Sabado 08 Febrero de 2020 11:52 am</td></tr>-->
                    </tbody></table>
                </div>
            <?php
        }

        /**
        * @method createFormat Crear el formato solicitado por el usuario
        */
        public function createFormat()
        {
            $this->setSeccionesFormato();
            $this->setFilasCuentasFormato();
            if ($this->empresaGrupo==true) {
                $this->getAsientos();
            }
            else{
                $this->setAsientos();
            }
            $this->mergeData();
            $this->getStyles();
            // print_r($this->arraySeccionesFormato);
            if ($this->empresaGrupo<>true) $this->getTitle();

            ?>

                <table class="table-form" style="width:calc(100% - 10px);margin-bottom: 20px;">
                    <tbody>
                        <tr class="thead">
                            <td colspan="2">
                                <b>Etiquetas de las filas</b>
                                <?php
                                    if ($this->arrayReportInfo['comparativo_misma_fecha']=='Si'){
                                        ?>
                                            <div>%</div>
                                            <div>Diferencia</div>
                                            <div>%</div>
                                            <div><?= $this->anioFin ?></div>
                                        <?php
                                    }
                                ?>
                                <div>%</div>
                                <div><?= $this->anioInicio ?></div>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <?= $this->createTreeView(0,$this->arraySeccionesFormato); ?>
                <table class="table-form" style="width:calc(100% - 10px);margin-bottom: 20px;">
                    <tbody>
                        <tr class="thead">
                            <td colspan="2">
                                <b>Totales</b>
                                <?php
                                    if ($this->arrayReportInfo['comparativo_misma_fecha']=='Si'){
                                        ?>
                                        <div><?= number_format($this->percent_3,0,$this->separador_miles,$this->separador_decimales) ?></div>
                                        <div><?= number_format($this->diferencia,0,$this->separador_miles,$this->separador_decimales) ?></div>
                                        <div><?= number_format($this->percent_2,0,$this->separador_miles,$this->separador_decimales) ?></div>
                                        <div><?= number_format($this->valor2,0,$this->separador_miles,$this->separador_decimales) ?></div>
                                    <?php
                                    }
                                ?>
                                <div><?= number_format($this->percent_1,0,$this->separador_miles,$this->separador_decimales) ?></div>
                                <div><?= number_format($this->valor1,0,$this->separador_miles,$this->separador_decimales) ?></div>
                            </td>
                        </tr>
                    </tbody>
                </table>

            <?php

            // LISTAR LAS EMPRESAS DEL GRUPO EMPRESARIAL A LAS CUALES SE LE CONSULTARA EL INFORME
            foreach ($this->arrayGrupo as $key => $arrayResul) {
                ?>
                    <div style="width:100%; text-align:center">
                        <table align="center" style="text-align:center;">
                            <tbody><tr><td class="titulo_informe_empresa" style="text-align:center;"><b><?= $arrayResul['nombre']; ?></b></td></tr>
                            <tr><td style="font-size:13px;text-align:center;"><b>NIT</b> <?= $arrayResul['nit'] ?></td></tr>
                            <tr><td style="width:100%; font-weight:bold; font-size:14px; text-align:center;text-transform: uppercase;"><?= $this->arrayReportInfo['nombre']; ?></td></tr>
                            <tr><td style="font-size:11px;text-align:center;">Periodo del <?= $this->fechaInicio ?> al <?= $this->fechaFinal ?> </td></tr>
                            <!--<tr><td style="font-size:11px; text-align:center;" >Impreso: Sabado 08 Febrero de 2020 11:52 am</td></tr>-->
                        </tbody></table>
                    </div>
                    <div id="content-<?= $arrayResul['nit'] ?>" style="margin-bottom: 30px;">
                        <center title="Cargar informacion de esta empresa" style="padding: 5px;cursor: pointer;" onclick="cargarInformeEmpresaGrupo({nit:'<?= $arrayResul['nit'] ?>',id_formato:'<?= $this->id_formato?>',fechaInicio:'<?= $this->fechaInicio?>',fechaFinal:'<?= $this->fechaFinal?>',separador_miles:'<?= $this->separador_miles?>',separador_decimales:'<?= $this->separador_decimales?>',sucursal:'<?= $this->id_sucursal?>',arrayCentroCostosJSON:''})">
                            <b>Cargar informacion</b>
                            <img src="img/deshacer.png">
                        </center>
                    </div>
                <?php
            }

        }
    }

    $id_empresa = $_SESSION['EMPRESA'];
    $object = new ClassDinamicReport(
                                        $id_formato,
                                        $MyInformeFiltroFechaInicio,
                                        $MyInformeFiltroFechaFinal,
                                        $separador_miles,
                                        $separador_decimales,
                                        $arrayCentroCostosJSON,
                                        $arrayGrupoJSON,
                                        $id_empresa,
                                        $id_sucursal,
                                        $empresaGrupo,
                                        $nit,
                                        $IMPRIME_XLS,
                                        $mysql
                                    );
    $object->createFormat();

?>