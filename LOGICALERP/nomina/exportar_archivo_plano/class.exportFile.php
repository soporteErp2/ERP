<?php
	include("../../../configuracion/conectar.php");
    include("../../../configuracion/define_variables.php");
    require("class.functionsExport.php");
	require("arraysResolucion.php");

    class exportFile extends functionsExport
    {

        private $nit;
        private $mysql;
        private $id_empresa;
        private $id_sucursal;
        private $periodo_pago_dif;
        private $periodo_pago;
        private $arrayInfoEncabezado;
        private $arrayEncabezado;
        private $arrayRegistroTipo2;
        private $arrayRegistroTipo4;
        private $arrayEmpleados;
        private $arrayEPS;
        private $arrayEmpleadosCamposXArticulo10;

        /**
        * @method construct
        * @param arr array con la estructura del encabezado de la resolucion
        * @param arr array con la estructura del Registro Tipo 2 de la resolucion
        * @param int nit de la empresa
        * @param dat periodo que corresponde a la pension
        * @param dat periodo que corresponde a la salud
        * @param int id de la empresa
        * @param int id de la sucursal
        * @param obj objeto de conexion mysql
        */
        function __construct($arrayEncabezado,$arrayRegistroTipo2,$arrayRegistroTipo4,$nit,$periodo_pago_dif,$periodo_pago,$id_empresa,$id_sucursal,$mysql)
        {
            $this->arrayEncabezado    = $arrayEncabezado;
            $this->arrayRegistroTipo2 = $arrayRegistroTipo2;
            $this->arrayRegistroTipo4 = $arrayRegistroTipo4;
            $this->nit                = $nit;
            $this->periodo_pago_dif   = $periodo_pago_dif;
            $this->periodo_pago       = $periodo_pago;
            $this->id_sucursal        = $id_sucursal;
            $this->id_empresa         = $id_empresa;
            $this->mysql              = $mysql;

            $this->getInfoEncabezado();
            $this->getCamposXArticulo10();
            $this->getInfoEmpleados();
        }

        /**
        * @method getInfoEncabezado obtener la informacion del encabezado y asignarlo en el Array
        */
        public function getInfoEncabezado()
        {
            $sql="SELECT nombre,tipo_identificacion,numero_identificacion,dv FROM terceros WHERE activo=1 AND numero_identificacion=$this->nit AND id_empresa=$this->id_empresa ";
            $query=$this->mysql->query($sql,$this->mysql->link);
            while ($row=$this->mysql->fetch_array($query)) {
                foreach ($this->arrayEncabezado as $campo => $arrayResul) {
                    $datos_bd = explode('.', $arrayResul['campo_bd'] );
                    $tabla    = $datos_bd[0];
                    $campo_bd = $datos_bd[1];
                    if ($tabla=='terceros') {
                        // echo 'tabla: '.$tabla.' - campo: '.$campo_bd.' - resul query: '.$row[$campo_bd].' <br>';
                        $this->arrayEncabezado[$campo]['value'] =($campo_bd=='tipo_identificacion')? $this->get_tipo_documento($row[$campo_bd]) :  $row[$campo_bd];
                    }
                }
            }

            $sql="SELECT codigo_arl FROM configuracion_arl WHERE activo=1 AND id_empresa=$this->id_empresa";
            $query=$this->mysql->query($sql,$this->mysql->link);
            $codigo_arl = $this->mysql->result($query,0,'codigo_arl');
            $arrayEncabezado['13']['value'] = $codigo_arl;
        }

        public function getCamposXArticulo10()
        {

            $sql="SELECT id
                    FROM nomina_planillas
                    WHERE
                    activo=1
                    AND id_empresa=$this->id_empresa
                    AND (
                            (fecha_inicio >= '$this->periodo_pago_dif-01' AND fecha_final  <= '$this->periodo_pago_dif-31') OR
                            (fecha_inicio >= '$this->periodo_pago-01' AND fecha_final  <= '$this->periodo_pago-31')
                        ) ";
            $query=$this->mysql->query($sql,$this->mysql->link);
            while ($row=$this->mysql->fetch_array($query)) {
                $whereIdNomina .= ($whereIdNomina=='')? "id_planilla=".$row['id'] : " OR id_planilla=".$row['id'] ;
            }

            $sql="SELECT id
                    FROM nomina_planillas_liquidacion
                    WHERE
                    activo=1
                    AND id_empresa=$this->id_empresa
                    AND (
                            (fecha_documento >= '$this->periodo_pago_dif-01' AND fecha_documento  <= '$this->periodo_pago_dif-31') OR
                            (fecha_documento >= '$this->periodo_pago-01' AND fecha_documento  <= '$this->periodo_pago-31')
                        ) ";
            $query=$this->mysql->query($sql,$this->mysql->link);
            while ($row=$this->mysql->fetch_array($query)) {
                $whereIdLiquidacion .= ($whereIdLiquidacion=='')? "id_planilla=".$row['id'] : " OR id_planilla=".$row['id'] ;
            }

            // CAMPO 15 - ING: INGRESO

            // CAMPO 16 - RET: RETIRO
            if ($whereIdLiquidacion<>''){
                $sql="SELECT id_empleado,terminar_contrato FROM nomina_planillas_liquidacion_empleados WHERE activo=1 AND id_empresa=$this->id_empresa AND ($whereIdLiquidacion)";
                $query=$this->mysql->query($sql,$this->mysql->link);
                while ($row=$this->mysql->fetch_array($query)) {
                    $id_empleado = $row['id_empleado'];
                    if ($row['terminar_contrato']=='Si') {$this->arrayEmpleadosCamposXArticulo10[16][$id_empleado]='X';}
                }
            }

            // CAMPO 17 - TDE: TRASLADO DESDE OTRA EPS O EOC
            // CAMPO 18 - TAE: TRASLADO A OTRA EPS O EOC
            // CAMPO 19 - TDP: TRASLADO DESDE OTRA ADMINISTRADORA DE PENSIONES
            // CAMPO 20 - TAP: TRASLADO A OTRA ADMINISTRADORA DE PENSIONES

            // CAMPO 21 - VSP: VARIACION PERMANENTE DE SALARIO
            $sql="SELECT id_empleado FROM empleados_contratos_modificacion_salarios
                    WHERE activo=1 AND id_empresa=$this->id_empresa
                        AND
                        (
                            (fecha_modificacion >= '$this->periodo_pago_dif-01' AND fecha_modificacion  <= '$this->periodo_pago_dif-31') OR
                            (fecha_modificacion >= '$this->periodo_pago-01' AND fecha_modificacion  <= '$this->periodo_pago-31')
                        )
                    GROUP BY id_empleado";
            $query=$this->mysql->query($sql,$this->mysql->link);
            while($row=$this->mysql->fetch_array($query)){
                $id_empleado = $row['id_empleado'];
                $this->arrayEmpleadosCamposXArticulo10[21][$id_empleado]='X';
            }

            // CAMPO 22 - Correcciones
            // CAMPO 23 - VST: VARIACION TRANSITORIA DE SALARIO

            // CAMPO 24 - SLN: SUSPENSION TEMPORAL DEL CONTRATO DE TRABAJO O LICENCIA NO REMUNERADA O COMISION DE SERVICIOS
            // CAMPO 25 - IGE: INCAPACIDAD TEMPORAL POR ENFERMEDAD GENERAL
            // CAMPO 26 - LMA: LICENCIA DE MATERNIDAD O DE PATERNIDAD
            // CAMPO 30 - IRP: INCAPACIDAD POR ACCIDENTE DE TRABAJO O ENFERMEDAD PROFESIONAL
            if ($whereIdNomina<>'') {
                $sql="SELECT id_empleado,codigo_concepto FROM nomina_planillas_empleados_conceptos WHERE activo=1 AND id_empresa=$this->id_empresa AND ($whereIdNomina)";
                $query=$this->mysql->query($sql,$this->mysql->link);
                while ($row=$this->mysql->fetch_array($query)) {
                    $id_empleado = $row['id_empleado'];

                    if ($row['codigo_concepto']=='DS' || $row['codigo_concepto']=='PNR')
                        {$this->arrayEmpleadosCamposXArticulo10[24][$id_empleado]='X';}
                    if ($row['codigo_concepto']=='IM' || $row['codigo_concepto']=='IMEPS90' || $row['codigo_concepto']=='IMEPS180')
                        {$this->arrayEmpleadosCamposXArticulo10[25][$id_empleado]='X';}
                    if ($row['codigo_concepto']=='LM')
                        {$this->arrayEmpleadosCamposXArticulo10[26][$id_empleado]='X';}
                    if ($row['codigo_concepto']=='IMARL')
                        {$this->arrayEmpleadosCamposXArticulo10[30][$id_empleado]='X';}
                }
            }

            // CAMPO 27 - VAC: VACACIONES
            if ($whereIdLiquidacion<>''){
                $sql="SELECT id_empleado,codigo_concepto FROM nomina_planillas_liquidacion_empleados_conceptos WHERE activo=1 AND id_empresa=$this->id_empresa AND ($whereIdLiquidacion)";
                $query=$this->mysql->query($sql,$this->mysql->link);
                while ($row=$this->mysql->fetch_array($query)) {
                    $id_empleado = $row['id_empleado'];
                    if ($row['codigo_concepto']=='VC') {$this->arrayEmpleadosCamposXArticulo10[27][$id_empleado]='X';}
                }
            }


            // CAMPO 28 - AVP: APORTE VOLUNTARIO
            // CAMPO 29 - VCT: VARIACION CENTROS DE TRABAJO

            // print_r($this->arrayEmpleadosCamposXArticulo10);
        }

        /**
        * @method getInfoEmpleados obtener la informacion del encabezado y asignarlo en el Array
        */
        public function getInfoEmpleados()
        {
            $sql="SELECT id
                    FROM nomina_planillas
                    WHERE
                        activo=1
                        AND consecutivo>0
                        AND estado<>3
                        AND id_empresa=$this->id_empresa
                        AND (
                                (fecha_inicio>='$this->periodo_pago-01' AND fecha_inicio<='$this->periodo_pago-31')
                                OR (fecha_inicio>='$this->periodo_pago_dif-01' AND fecha_inicio<='$this->periodo_pago_dif-31')
                            )";
            $query=$this->mysql->query($sql,$this->mysql->link);
            while ($row=$this->mysql->fetch_array($query)) {
                $whereIdPlanillas .= ($whereIdPlanillas=='')? "NPEC.id_planilla=".$row['id'] : " OR NPEC.id_planilla=".$row['id'] ;
            }

            $cont_empleados = 0;
            $cont           = 1;

            $sql="SELECT
                        E.id,
                        E.tipo_documento_nombre,
                        E.documento,
                        E.codigo_departamento_laboral,
                        E.codigo_municipio_laboral,
                        E.apellido1,
                        E.apellido2,
                        E.nombre1,
                        E.nombre2,
                        E.tipo_cotizante,
                        E.subtipo_cotizante,
                        IF(E.extranjero_obligado_cotizar='Si','X',' ') AS extranjero_obligado_cotizar,
                        IF(E.residente_en_exterior='Si','X',' ') AS residente_en_exterior,
                        E.codigo_departamento_laboral,
                        E.codigo_municipio_laboral,
                        E.codigo_administradora_pensiones,
                        CONCAT('CCF',E.codigo_CCF) AS codigo_CCF,
                        CONCAT(E.tipo_entidad_salud,E.codigo_EPS_EOC) AS campo_EPS_EOC
                    FROM
                        nomina_planillas_empleados_conceptos AS NPEC
                    INNER JOIN empleados AS E ON E.id = NPEC.id_empleado
                    WHERE NPEC.activo=1
                    AND NPEC.id_empresa=$this->id_empresa
                    AND ($whereIdPlanillas)
                    GROUP BY NPEC.id_empleado";
            $query=$this->mysql->query($sql,$this->mysql->link);
            while ($row=$this->mysql->fetch_array($query)) {
                $cont_empleados ++;
                $id_empleado                                      = $row['id'];
                $this->arrayEmpleados[$id_empleado]               = $this->arrayRegistroTipo2;
                $this->arrayEPS[$id_empleado]                     = $this->arrayRegistroTipo4;
                $this->arrayEmpleados[$id_empleado]['2']['value'] = $cont;
                $this->arrayEPS[$id_empleado]['2']['value']       = $cont;

                foreach ($this->arrayEmpleados[$id_empleado] as $campo => $arrayResul) {
                    $datos_bd = explode('.', $arrayResul['campo_bd'] );
                    $tabla    = $datos_bd[0];
                    $campo_bd = $datos_bd[1];
                    if ($tabla=='empleados') {
                        $this->arrayEmpleados[$id_empleado][$campo]['value'] = ($campo_bd=='tipo_documento_nombre')? $this->get_tipo_documento($row[$campo_bd]) : $row[$campo_bd];
                    }

                    $this->arrayEmpleados[$id_empleado][15]['value'] = $this->arrayEmpleadosCamposXArticulo10[15][$id_empleado];
                    $this->arrayEmpleados[$id_empleado][16]['value'] = $this->arrayEmpleadosCamposXArticulo10[16][$id_empleado];
                    $this->arrayEmpleados[$id_empleado][17]['value'] = $this->arrayEmpleadosCamposXArticulo10[17][$id_empleado];
                    $this->arrayEmpleados[$id_empleado][18]['value'] = $this->arrayEmpleadosCamposXArticulo10[18][$id_empleado];
                    $this->arrayEmpleados[$id_empleado][19]['value'] = $this->arrayEmpleadosCamposXArticulo10[19][$id_empleado];
                    $this->arrayEmpleados[$id_empleado][20]['value'] = $this->arrayEmpleadosCamposXArticulo10[20][$id_empleado];
                    $this->arrayEmpleados[$id_empleado][21]['value'] = $this->arrayEmpleadosCamposXArticulo10[21][$id_empleado];
                    $this->arrayEmpleados[$id_empleado][22]['value'] = $this->arrayEmpleadosCamposXArticulo10[22][$id_empleado];
                    $this->arrayEmpleados[$id_empleado][23]['value'] = $this->arrayEmpleadosCamposXArticulo10[23][$id_empleado];
                    $this->arrayEmpleados[$id_empleado][24]['value'] = $this->arrayEmpleadosCamposXArticulo10[24][$id_empleado];
                    $this->arrayEmpleados[$id_empleado][25]['value'] = $this->arrayEmpleadosCamposXArticulo10[25][$id_empleado];
                    $this->arrayEmpleados[$id_empleado][26]['value'] = $this->arrayEmpleadosCamposXArticulo10[26][$id_empleado];
                    $this->arrayEmpleados[$id_empleado][27]['value'] = $this->arrayEmpleadosCamposXArticulo10[27][$id_empleado];
                    $this->arrayEmpleados[$id_empleado][28]['value'] = $this->arrayEmpleadosCamposXArticulo10[28][$id_empleado];
                    $this->arrayEmpleados[$id_empleado][29]['value'] = $this->arrayEmpleadosCamposXArticulo10[29][$id_empleado];

                    // ARRAY REGISTRO TIPO 4
                    $this->arrayEPS[$id_empleado][3]['value'] =  $row['campo_EPS_EOC'];


                }
                $cont++;

                $whereIdEmpleados .= ($whereIdEmpleados=='')? "id_empleado=$id_empleado" : " OR id_empleado=$id_empleado" ;
            }
            $arrayEncabezado['18']['value'] = $cont_empleados;

            // ENTIDADES DE LOS EMPLEADOS, Registro Tipo 4
            $sql="SELECT id_entidad,id_empleado,concepto
                    FROM empleados_contratos_entidades
                    WHERE activo=1 AND id_empresa=$this->id_empresa AND (concepto='EPS EMPLEADO' OR concepto='ARL') AND ($whereIdEmpleados)";
            $query=$mysql->query($sql,$mysql->link);
            while ($row=$mysql->fetch_array($query)) {
                $id_entidad  = $row['id_entidad'];
                $id_empleado = $row['id_empleado'];
                $concepto    = $row['concepto'];

                $arrayEntidades[$id_empleado][$concepto]=$id_entidad;
                $whereIdTerceros .= ($whereIdTerceros=='')? "id_empleado=$id_empleado" : " OR id_empleado=$id_empleado" ;

                // if ($concepto=='EPS EMPLEADO') {
                //     # code...
                // }
                // if ($concepto=='ARL') {
                //     # code...
                // }
                    // $this->arrayEPS[$id_empleado][3]['value'] =  $row['campo_EPS_EOC'];
            }

            // CONSULTAR LAS ENTIDADES
            $sql="SELECT numero_documento,dv FROM terceros WHERE activo=1 AND id_empresa=$this->id_empresa";
            $query=$mysql->query($sql,$mysql->link);
            while ($row=$mysql->fetch_array($query)) {
                # code...
            }

        }

        /**
        * @method getInfoNominaEncabezado obtener la informacion del encabezado y asignarlo en el Array
        */
        public function getInfoNominaEncabezado()
        {
            $sql="SELECT id FROM nomina_planillas WHERE activo=1 AND id_empresa=$this->id_empresa AND id_sucursal=$this->id_sucursal";
            $query=$this->mysql->query($sql,$this->mysql->link);
        }

        /**
        * @method buildFile armar el archivo plano
        */
        public function buildFile()
        {
            foreach ($this->arrayEncabezado as $campo => $arrayResul) {
                $filecontent.=str_pad($arrayResul['value'],$arrayResul['long'], $arrayResul['rellena_espacios'] ,$arrayResul['aling']);
            }
            $filecontent.=PHP_EOL;
            foreach ($this->arrayEmpleados as $id_empleado => $arrayResolucion) {
                foreach ($arrayResolucion as $campo => $arrayResul) {
                    $filecontent.=str_pad($arrayResul['value'],$arrayResul['long'], $arrayResul['rellena_espacios'] ,$arrayResul['aling']);
                }
                $filecontent.=PHP_EOL;
                // $filecontent.='<br>';
            }

            foreach ($this->arrayEPS as $id_empleado => $arrayResolucion) {
                foreach ($arrayResolucion as $campo => $arrayResul) {
                    $filecontent.=str_pad($arrayResul['value'],$arrayResul['long'], $arrayResul['rellena_espacios'] ,$arrayResul['aling']);
                }
                $filecontent.=PHP_EOL;
                // $filecontent.='<br>';
            }

            // print_r($this->arrayEmpleados);
            $fileName="archivo_plano_pila.txt";
            header("Content-disposition: attachment; filename=$fileName");
            header("Content-Type: application/force-download");
            header("Content-Transfer-Encoding: binary");
            header("Content-Length: ".strlen($filecontent));
            header("Pragma: no-cache");
            header("Expires: 0");
            /*echo $filecontent.PHP_EOL.'-proof';*/
            echo $filecontent;
        }

    }

    // $plainFile = new exportFile($arrayEncabezado,$arrayRegistroTipo2,explode('-', $_SESSION['NITEMPRESA'])[0],$periodo_pago_dif,$periodo_pago,$_SESSION['EMPRESA'],$_SESSION['SUCURSAL'],$mysql);
    $plainFile = new exportFile($arrayEncabezado,$arrayRegistroTipo2,$arrayRegistroTipo4,'900467785',$periodo_pago_dif,$periodo_pago,$_SESSION['EMPRESA'],$_SESSION['SUCURSAL'],$mysql);
    $plainFile->buildFile();

?>