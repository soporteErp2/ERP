<?php
  include_once('../../../../configuracion/conectar.php');
  include_once('../../../../configuracion/define_variables.php');
	ob_start();

  if($IMPRIME_XLS=='true'){
    header('Content-type: application/vnd.ms-excel');
    header("Content-Disposition: attachment; filename=balance_general_".date("Y_m_d").".xls");
    header("Pragma: no-cache");
    header("Expires: 0");
  }

  $id_empresa          = $_SESSION['EMPRESA'];
  $desde               = $MyInformeFiltroFechaInicio;
  $hasta               = (isset($MyInformeFiltroFechaFinal))? $MyInformeFiltroFechaFinal : date("Y-m-d") ;
  $divTitleSucursal    = '';
  $whereSucursal       = '';
  $separador_decimales = ($separador_decimales=='')? "." : $separador_decimales ;
  $separador_miles     = ($separador_miles=='')? "," : $separador_miles ;

    if (isset($MyInformeFiltroFechaFinal) && isset($generar)) {
        $MyInformeFiltroFechaFinal=$MyInformeFiltroFechaFinal;
    }
    else{
        $MyInformeFiltroFechaFinal=date("Y-m-d");
        $generar = 'Grupos';
        $script  = 'localStorage.MyInformeFiltroFechaInicio="";
                    localStorage.MyInformeFiltroFechaFinal="";
                    localStorage.generar="";
                    localStorage.tipo_balance="";';
    }

    $tipo_balance=(isset($tipo_balance))? $tipo_balance: 'clasificado';

    //=========== NIVEL CUENTA //===========//
    $varCortar = $generar;

    //===============================// BALANCE CLASIFICADO //===============================//
    //***************************************************************************************//
    if ($tipo_balance=='clasificado') {
        //SALDOS
        $acumuladoDebe          = 0;
        $acumuladoHaber         = 0;
        $acumuladoSaldoAnterior = 0;
        $acumuladoSaldoActual   = 0;

        $acumActivos    = 0;
        $acumPasivos    = 0;
        $acumPatrimonio = 0;

        $newBalance = new balanceGeneral($MyInformeFiltroFechaFinal,$id_empresa,$varCortar,$mysql);

        $title = '<tr><td style="font-size:14px;">Balance General</td></tr>
                    <tr><td style="font-size:13px;"> A '.fecha_larga($newBalance->getFecha()).'</td></tr>';


        //=========== ACTIVO //===========>
        foreach ($newBalance->getActivo() as $cuenta => $rowActivos) {

            $cuerpoActivosT .= '<tr>
                                    <td width="100" class="defaultFont" style="padding-right:10px; text-align:right;">'.$cuenta.'</td>
                                    <td class="defaultFont">'.$rowActivos['descripcion'].'</td>
                                    <td class="defaultFont" style="text-align:right;">'.number_format($rowActivos['saldo'], $_SESSION['DECIMALESMONEDA'],$separador_decimales,$separador_miles).'</td>
                                </tr>';
            $acumActivos += $rowActivos['saldo'];
        }

        //=========== PASIVO //===========>
        foreach ($newBalance->getPasivo() as $cuenta => $rowPasivos) {

            $cuerpoPasivosT .= '<tr class="defaultFont">
                                    <td width="100" class="defaultFont" style="padding-right:10px; text-align:right;">'.$cuenta.'</td>
                                    <td class="defaultFont">'.$rowPasivos['descripcion'].'</td>
                                    <td class="defaultFont" style="text-align:right;">'.number_format($rowPasivos['saldo'], $_SESSION['DECIMALESMONEDA'],$separador_decimales,$separador_miles).'</td>
                                </tr>';

            $acumPasivos += $rowPasivos['saldo'];
        }

        //=========== PATRIMONIO //===========>
        foreach ($newBalance->getPatrimonio() as $cuenta => $rowPatrimonio) {

            $cuerpoPatrimonioT .= '<tr class="defaultFont">
                                        <td width="100" class="defaultFont" style="padding-right:10px; text-align:right;">'.$cuenta.'</td>
                                        <td class="defaultFont">'.$rowPatrimonio['descripcion'].'</td>
                                        <td class="defaultFont" style="text-align:right;">'.number_format($rowPatrimonio['saldo'], $_SESSION['DECIMALESMONEDA'],$separador_decimales,$separador_miles).'</td>
                                    </tr>';

            $acumPatrimonio += $rowPatrimonio['saldo'];
        }

        //=========== BODY //===========>
        $cuerpoInforme= '<table style="width:95%" cellspacing="10">
                            <tr><td colspan="3" class="labelResult">ACTIVO</td></tr>
                            '.$cuerpoActivosT.'
                            <tr><td>&nbsp;</td><td class="labelResult2">TOTAL ACTIVO</td><td class="labelResult3">'.number_format($acumActivos, $_SESSION['DECIMALESMONEDA'],$separador_decimales,$separador_miles).'</td></tr>

                            <tr><td colspan="3" class="labelResult"> PASIVO</td></tr>
                            '.$cuerpoPasivosT.'
                            <tr><td>&nbsp;</td><td class="labelResult2">TOTAL PASIVO</td><td class="labelResult3">'.number_format($acumPasivos, $_SESSION['DECIMALESMONEDA'],$separador_decimales,$separador_miles).'</td></tr>

                            <tr><td colspan="3" class="labelResult"> PATRIMONIO</td></tr>
                            '.$cuerpoPatrimonioT.'
                            <tr><td>&nbsp;</td><td class="labelResult2">TOTAL PATRIMONIO</td><td class="labelResult3">'.number_format($acumPatrimonio, $_SESSION['DECIMALESMONEDA'],$separador_decimales,$separador_miles).'</td></tr>

                            <tr><td>&nbsp;</td><td class="labelResult2" style="width:40%;" >SUMA DEL PASIVO Y EL PATRIMONIO</td><td class="labelResult3">'.number_format(($acumPatrimonio+$acumPasivos), $_SESSION['DECIMALESMONEDA'],$separador_decimales,$separador_miles).'</td></tr>
                        </table>

                        <table align="center" style="text-align:center; width:70%;">
                            <tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>
                            <tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>
                            <tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>
                            <tr>
                                <td class="defaultFont" style="border-top:1px solid #000; font-weight:bold;">APROBADO</td>
                                <td>&nbsp;</td>
                                <td class="defaultFont" style="border-top:1px solid #000; font-weight:bold;">REVISADO</td>
                            </tr>
                        </table><br>';

    }

    //===============================// BALANCE COMPARATIVO //===============================//
    //***************************************************************************************//
    else if ($tipo_balance=='comparativo') {

        $title='<tr><td style="font-size:14px;" >Balance General Comparativo</td></tr>';

        $acumSaldoInicialActivo  = 0;
        $acumSaldoActualActivo   = 0;
        $acumSaldoInicialPasivos = 0;
        $acumSaldoActualPasivos  = 0;
        $balanceInicial = new balanceGeneral($MyInformeFiltroFechaInicio,$id_empresa,$varCortar,$mysql);
        $balanceFinal   = new balanceGeneral($MyInformeFiltroFechaFinal,$id_empresa,$varCortar,$mysql);


        //============================// ACTIVO //============================//
        //********************************************************************//
        $arrayActivoInicial = $balanceInicial->getActivo();
        $arrayActivoFinal   = $balanceFinal->getActivo();

        $arrayCuentasInicial = $balanceInicial->getCuentasActivo();
        $arrayCuentasFinal   = $balanceFinal->getCuentasActivo();
        $arrayCuentas        = array_unique(array_merge($arrayCuentasInicial,$arrayCuentasFinal));

        asort($arrayCuentas,SORT_STRING);

        //=========== CONSOLIDADO //===========>
        foreach ($arrayCuentas as $cuenta) {
            $nombre = (gettype($arrayActivoInicial[$cuenta]) != 'NULL')? $arrayActivoInicial[$cuenta]['descripcion']: $arrayActivoFinal[$cuenta]['descripcion'];

            $saldoInicial = $arrayActivoInicial[$cuenta]['saldo'] * 1;
            $saldoFinal   = $arrayActivoFinal[$cuenta]['saldo'] * 1;

            $acumSaldoInicialActivo += $saldoInicial;             //PERIODO ANTERIOR
            $acumSaldoActualActivo  += $saldoFinal;               //NUEVO PERIODO

            $diferenciaActivos      = ($saldoFinal-$saldoInicial);            //DIFERENCIA
            $acumDiferenciaActivos += $diferenciaActivos;

            $porcentaje = ($diferenciaActivos*100)/$saldoInicial;            //PORCENTAJE

            $cuerpoActivosT .= '<tr class="defaultFont">
                                    <td class="defaultFont" style="width:60px; padding-right:10px; text-align:right;">'.$cuenta.'</td>
                                    <td class="defaultFont" style="width:300px;">'.$nombre.'</td>
                                    <td class="defaultFont" style="text-align:right;width:100px;">'.number_format($saldoInicial, $_SESSION['DECIMALESMONEDA'],$separador_decimales,$separador_miles).'</td>
                                    <td class="defaultFont" style="text-align:right;width:100px;">'.number_format($saldoFinal, $_SESSION['DECIMALESMONEDA'],$separador_decimales,$separador_miles).'</td>
                                    <td class="defaultFont" style="text-align:right;width:100px;">'.number_format($diferenciaActivos, $_SESSION['DECIMALESMONEDA'],$separador_decimales,$separador_miles).'</td>
                                    <td class="defaultFont" style="text-align:right;width:100px;">'.number_format($porcentaje, $_SESSION['DECIMALESMONEDA'],$separador_decimales,$separador_miles).'</td>
                                </tr>';
        }

        //============================// PASIVO //============================//
        //********************************************************************//
        $arrayPasivoInicial = $balanceInicial->getPasivo();
        $arrayPasivoFinal   = $balanceFinal->getPasivo();

        $arrayCuentasInicial = $balanceInicial->getCuentasPasivo();
        $arrayCuentasFinal   = $balanceFinal->getCuentasPasivo();
        $arrayCuentas        = array_unique(array_merge($arrayCuentasInicial,$arrayCuentasFinal));

        asort($arrayCuentas,SORT_STRING);

        //=========== CONSOLIDADO //===========>
        foreach ($arrayCuentas as $cuenta) {
            $nombre = (gettype($arrayPasivoInicial[$cuenta]) != 'NULL')? $arrayPasivoInicial[$cuenta]['descripcion']: $arrayPasivoFinal[$cuenta]['descripcion'];

            $saldoInicial = $arrayPasivoInicial[$cuenta]['saldo'] * 1;
            $saldoFinal   = $arrayPasivoFinal[$cuenta]['saldo'] * 1;

            $acumSaldoInicialPasivos += $saldoInicial;          //PERIODO ANTERIOR
            $acumSaldoActualPasivos  += $saldoFinal;            //NUEVO PERIODO

            $diferenciaPasivos      = $saldoFinal-$saldoInicial;            //DIFERENCIA
            $acumDiferenciaPasivos += $diferenciaPasivos;

            $porcentajePasivos = ($diferenciaPasivos*100)/$saldoInicial;            //PORCENTAJE

            $cuerpoPasivosT .= '<tr class="defaultFont">
                                    <td class="defaultFont" style="width:60px; padding-right:10px; text-align:right;">'.$cuenta.'</td>
                                    <td class="defaultFont" style="width:300px;">'.$nombre.'</td>
                                    <td class="defaultFont" style="text-align:right;width:100px;">'.number_format($saldoInicial, $_SESSION['DECIMALESMONEDA'],$separador_decimales,$separador_miles).'</td>
                                    <td class="defaultFont" style="text-align:right;width:100px;">'.number_format($saldoFinal, $_SESSION['DECIMALESMONEDA'],$separador_decimales,$separador_miles).'</td>
                                    <td class="defaultFont" style="text-align:right;width:100px;">'.number_format($diferenciaPasivos, $_SESSION['DECIMALESMONEDA'],$separador_decimales,$separador_miles).'</td>
                                    <td class="defaultFont" style="text-align:right;width:100px;">'.number_format($porcentajePasivos, $_SESSION['DECIMALESMONEDA'],$separador_decimales,$separador_miles).'</td>
                                </tr>';
        }

        //============================== PATRIMONIO ==============================//
        //************************************************************************//

        $arrayPatrimonioInicial = $balanceInicial->getPatrimonio();
        $arrayPatrimonioFinal   = $balanceFinal->getPatrimonio();

        $arrayCuentasInicial = $balanceInicial->getCuentasPatrimonio();
        $arrayCuentasFinal   = $balanceFinal->getCuentasPatrimonio();
        $arrayCuentas        = array_unique(array_merge($arrayCuentasInicial,$arrayCuentasFinal));

        asort($arrayCuentas,SORT_STRING);

        //=========== CONSOLIDADO //===========>
        foreach ($arrayCuentas as $cuenta) {

            $nombre = (gettype($arrayPatrimonioInicial[$cuenta]) != 'NULL')? $arrayPatrimonioInicial[$cuenta]['descripcion']: $arrayPatrimonioFinal[$cuenta]['descripcion'];

            $saldoInicial = $arrayPatrimonioInicial[$cuenta]['saldo'] * 1;
            $saldoFinal   = $arrayPatrimonioFinal[$cuenta]['saldo'] * 1;

            $acumSaldoInicialPatrimonio += $saldoInicial;            //PERIODO ANTERIOR
            $acumSaldoActualPatrimonio  += $saldoFinal;            //NUEVO PERIODO

            $diferenciaPatrimonio      = $saldoFinal-$saldoInicial;            //DIFERENCIA
            $acumDiferenciaPatrimonio += $diferenciaPatrimonio;

            $porcentajePatrimonio = ($diferenciaPatrimonio*100)/$saldoInicial;            //PORCENTAJE

            $cuerpoPatrimonioT .= '<tr class="defaultFont">
                                        <td class="defaultFont" style="width:60px; padding-right:10px; text-align:right;">'.$cuenta.'</td>
                                        <td class="defaultFont" style="width:300px;">'.$nombre.'</td>
                                        <td class="defaultFont" style="text-align:right;width:100px;">'.number_format($saldoInicial, $_SESSION['DECIMALESMONEDA'],$separador_decimales,$separador_miles).'</td>
                                        <td class="defaultFont" style="text-align:right;width:100px;">'.number_format($saldoFinal, $_SESSION['DECIMALESMONEDA'],$separador_decimales,$separador_miles).'</td>
                                        <td class="defaultFont" style="text-align:right;width:100px;">'.number_format($diferenciaPatrimonio, $_SESSION['DECIMALESMONEDA'],$separador_decimales,$separador_miles).'</td>
                                        <td class="defaultFont" style="text-align:right;width:100px;">'.number_format($porcentajePatrimonio, $_SESSION['DECIMALESMONEDA'],$separador_decimales,$separador_miles).'</td>
                                    </tr>';
        }

        $cuerpoInforme  = '<table style="width:98%" cellspacing="10">
                                <thead>
                                    <tr>
                                        <td class="labelResult" colspan="2" style="vertical-align:initial;">Reporte a Nivel de '.$generar.' </td>
                                        <td class="defaultFont" style="text-align:center; vertical-align:initial;" width="100"><b>Periodo Anterior</b><br>'.$balanceInicial->getFecha().'</td>
                                        <td class="defaultFont" style="text-align:center; vertical-align:initial;" width="100"><b>Hasta</b><br>'.$balanceFinal->getFecha().'</td>
                                        <td class="defaultFont" style="text-align:center; vertical-align:initial; font-weight:bold;" width="100">Diferencia</td>
                                        <td class="defaultFont" style="text-align:center; vertical-align:initial; font-weight:bold;" width="100">Porcentaje</td>
                                    </tr>
                                </thead>

                                <tr><td colspan="6" class="labelResult">ACTIVO</td></tr>

                                '.$cuerpoActivosT.'

                                 <tr>
                                    <td>&nbsp;</td>
                                    <td class="labelResult2" style="width:100px;">TOTAL ACTIVO</td>
                                    <td class="defaultFont" style="text-align:right;width:100px;font-weight:bold;">'.number_format($acumSaldoInicialActivo, $_SESSION['DECIMALESMONEDA'],$separador_decimales,$separador_miles).'</td>
                                    <td class="defaultFont" style="text-align:right;width:100px;font-weight:bold;">'.number_format($acumSaldoActualActivo, $_SESSION['DECIMALESMONEDA'],$separador_decimales,$separador_miles).'</td>
                                    <td class="defaultFont" style="text-align:right;width:100px;font-weight:bold;">'.number_format($acumDiferenciaActivos, $_SESSION['DECIMALESMONEDA'],$separador_decimales,$separador_miles).'</td>
                                    <td class="defaultFont" style="text-align:right;width:100px;">&nbsp;</td>

                                </tr>
                                <tr>
                                    <td>&nbsp;</td>
                                </tr>
                                <tr><td colspan="6" class="labelResult">PASIVO</td></tr>
                                '.$cuerpoPasivosT.'

                                 <tr>
                                    <td>&nbsp;</td>
                                    <td class="labelResult2" style="width:100px;">TOTAL PASIVO</td>
                                    <td class="defaultFont" style="text-align:right;width:100px;font-weight:bold;">'.number_format($acumSaldoInicialPasivos, $_SESSION['DECIMALESMONEDA'],$separador_decimales,$separador_miles).'</td>
                                    <td class="defaultFont" style="text-align:right;width:100px;font-weight:bold;">'.number_format($acumSaldoActualPasivos, $_SESSION['DECIMALESMONEDA'],$separador_decimales,$separador_miles).'</td>
                                    <td class="defaultFont" style="text-align:right;width:100px;font-weight:bold;">'.number_format($acumDiferenciaPasivos, $_SESSION['DECIMALESMONEDA'],$separador_decimales,$separador_miles).'</td>
                                    <td class="defaultFont" style="text-align:right;width:100px;">&nbsp;</td>

                                </tr>
                                <tr>
                                    <td>&nbsp;</td>
                                </tr>
                                <tr><td colspan="6" class="labelResult">PATRIMONIO</td></tr>
                                '.$cuerpoPatrimonioT.'

                                 <tr>
                                    <td>&nbsp;</td>
                                    <td class="labelResult2" style="width:100px;">TOTAL PATRIMONIO</td>
                                    <td class="defaultFont" style="text-align:right;width:100px;font-weight:bold;">'.number_format($acumSaldoInicialPatrimonio, $_SESSION['DECIMALESMONEDA'],$separador_decimales,$separador_miles).'</td>
                                    <td class="defaultFont" style="text-align:right;width:100px;font-weight:bold;">'.number_format($acumSaldoActualPatrimonio, $_SESSION['DECIMALESMONEDA'],$separador_decimales,$separador_miles).'</td>
                                    <td class="defaultFont" style="text-align:right;width:100px;font-weight:bold;">'.number_format($acumDiferenciaPatrimonio, $_SESSION['DECIMALESMONEDA'],$separador_decimales,$separador_miles).'</td>
                                    <td class="defaultFont" style="text-align:right;width:100px;">&nbsp;</td>
                                </tr>

                                <tr>
                                    <td>&nbsp;</td>
                                </tr>

                                <tr>
                                    <td class="labelResult2" style="border:1px; solid;width:100px;" colspan="2">SUMA DEL PASIVO Y EL PATRIMONIO</td>

                                    <td class="defaultFont" style="text-align:right;width:100px;font-weight:bold;">'.number_format(($acumSaldoInicialPasivos+$acumSaldoInicialPatrimonio), $_SESSION['DECIMALESMONEDA'],$separador_decimales,$separador_miles).'</td>
                                    <td class="defaultFont" style="text-align:right;width:100px;font-weight:bold;">'.number_format(($acumSaldoActualPasivos+$acumSaldoActualPatrimonio), $_SESSION['DECIMALESMONEDA'],$separador_decimales,$separador_miles).'</td>
                                    <td class="defaultFont" style="text-align:right;width:100px;font-weight:bold;">'.number_format(($acumSaldoActualPasivos+$acumSaldoActualPatrimonio)-($acumSaldoInicialPasivos+$acumSaldoInicialPatrimonio), $_SESSION['DECIMALESMONEDA'],$separador_decimales,$separador_miles).'</td>
                                </tr>

                            </table>
                            <br>
                            <table align="center" style="text-align:center; width:70%;">
                                <tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>
                                <tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>
                                <tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>
                                <tr>
                                    <td class="defaultFont" style="border-top:1px solid; font-weight:bold;">APROBADO</td>
                                    <td>&nbsp;</td><td class="defaultFont" style="border-top:1px solid; font-weight:bold;">REVISADO</td>
                                </tr>
                            </table><br>';
    }
    else{ exit; }
?>

<style>
	.my_informe_Contenedor_Titulo_informe{
        float         :	left;
        width         :	100%;
        border-bottom :	1px solid #CCC;
        margin        :	0 0 10px 0;
        font-size     :	11px;
        font-family   :	Verdana, Geneva, sans-serif;
	}

	.my_informe_Contenedor_Titulo_informe_label{
        float       : left;
        width       : 130px;
        font-weight : bold;
	}

	.my_informe_Contenedor_Titulo_informe_detalle{
        float         :	left;
        width         :	210px;
        padding       :	0 0 0 5px;
        white-space   : nowrap;
        overflow      : hidden;
        text-overflow : ellipsis;
	}

	.my_informe_Contenedor_Titulo_informe_Empresa{
        float       : left;
        width       : 100%;
        font-size   : 16px;
        font-weight : bold;
	}

    .defaultFont{ font-size:11px; }
    .labelResult{ font-weight:bold; font-size: 14px; }
    .labelResult2{ font-weight:bold; font-size: 12px; width: 20%; }
    .labelResult3{ font-weight:bold; font-size: 12px; text-align: right; }
</style>


<!--------------------------------   DESARROLLO DEL INFORME  ------------------------------------- -->
<!--***********************************************************************************************-->

<body>
    <div class="my_informe_Contenedor_Titulo_informe" style="float:left; width:100%">
        <div style="float:left; width:100%">
            <div style="float:left;width:100%; text-align:center">
                <table align="center" style="text-align:center;" >
                    <tr><td class="my_informe_Contenedor_Titulo_informe_Empresa" style="text-align:center;"><?php echo $_SESSION['NOMBREEMPRESA']?></td></tr>
                    <tr><td style="font-size:13px;"><b>NIT </b><?php echo $_SESSION['NITEMPRESA']; ?></td></tr>
                    <?php echo $title; ?>
                </table>
            </div>
        </div>
    </div>

 <br>
    <?php echo $cuerpoInforme; ?>
</body>

<script><?php echo $script; ?></script>

<?php

    $texto = ob_get_contents(); ob_end_clean();

	if(isset($TAM)){ $HOJA = $TAM; }
    else{ $HOJA = 'LETTER'; }

	if(!isset($ORIENTACION)){ $ORIENTACION = 'P'; }
	if(!isset($PDF_GUARDA)){ $PDF_GUARDA = 'false'; }
	if(!isset($IMPRIME_PDF)){ $IMPRIME_PDF = 'false'; }

	if(isset($MARGENES)){list($MS, $MD, $MI, $ML) = split( ',', $MARGENES ); }
    else{ $MS=10; $MD=10; $MI=10; $ML=10; }

	if(!isset($TAMANO_ENCA)){ $TAMANO_ENCA = 12 ; }
	if($IMPRIME_PDF == 'true'){
		include("../../../../misc/MPDF54/mpdf.php");
		$mpdf = new mPDF(
					'utf-8',  		// mode - default ''
					$HOJA,			// format - A4, for example, default ''
					12,				// font size - default 0
					'',				// default font family
					$MI,			// margin_left
					$MD,			// margin right
					$MS,			// margin top
					$ML,			// margin bottom
					10,				// margin header
					10,				// margin footer
					$ORIENTACION	// L - landscape, P - portrait
				);
        // $mpdf-> debug = true;
        $mpdf->SetProtection(array('print'));
        $mpdf->useSubstitutions = true;
        $mpdf->simpleTables     = true;
        $mpdf->packTableData    = true;
        $mpdf->useSubstitutions = true;
		$mpdf->SetAutoPageBreak(TRUE, 15);

		$mpdf->SetAuthor ( $_SESSION['NOMBREFUNCIONARIO']." // ".$_SESSION['NOMBREEMPRESA'] );
		$mpdf->SetDisplayMode ( 'fullpage' );
		$mpdf->SetHeader("");
        $mpdf->SetHTMLFooter('<div style="text-align:right; font-size:12px; font-weight:bold;">Pagina {PAGENO}/{nb}</div>');
		$mpdf->WriteHTML(utf8_encode($texto));

		if($PDF_GUARDA=='true'){ $mpdf->Output("balance_general.pdf",'D'); }
        else{ $mpdf->Output($documento.".pdf",'I'); }
		exit;
	}
    else{ echo $texto; }


    class balanceGeneral{

        private $fecha        = "";
        private $ArrayQuery   = array();
        private $ArrayCuentas = array();
        private $whereBalance = "";
        private $wherePyG     = "";

        /**
        * @param str fecha
        * @param int id empresa
        * @param int digitos por cuenta
        * @param obj api mysql
        */

        function __construct($fecha,$id_empresa,$varCortar,$mysql){

            list($year,$moth,$day) = explode("-", $fecha);

            $this->fecha = $fecha;
            $this->configWhereFecha();

            $this->ArrayQuery = $this->cuentasBalance($id_empresa,$varCortar,$mysql);       //ARRAY CUENTAS 1,2,3
            $arraySaldoPyg    = $this->saldoPyg($id_empresa,$varCortar,$mysql);                        //SALDO PYG
            $arrayUtilidad    = $this->cuentaUtilidad($arraySaldoPyg['cuenta'],$id_empresa,$varCortar,$mysql);  //CUENTA PATRIMONIO UTILIDAD
            $cuentaPyg        = $arrayUtilidad['cuenta'];

            if(gettype($this->ArrayQuery["campo_3"]["$cuentaPyg"]['saldo']) != 'NULL'){
                $this->ArrayQuery["campo_3"]["$cuentaPyg"]['saldo'] += $arraySaldoPyg['saldo'];
            }
            else{
                $this->ArrayCuentas["campo_3"][] = $cuentaPyg;
                $this->ArrayQuery["campo_3"]["$cuentaPyg"]['saldo']       = $arraySaldoPyg['saldo'];
                $this->ArrayQuery["campo_3"]["$cuentaPyg"]['descripcion'] = $arrayUtilidad['descripcion'];

                ksort($this->ArrayQuery["campo_3"],SORT_STRING);
            }
        }

        private function configWhereFecha(){
            $this->whereBalance = "AC.fecha <= '$this->fecha'";
            $this->wherePyG     = "AC.fecha <= '$this->fecha'";
        }

        private function cuentaUtilidad($pygCuenta,$id_empresa,$varCortar,$mysql){
            $sql = "SELECT digitos FROM puc_configuracion WHERE activo = 1 AND id_empresa = $_SESSION[EMPRESA] AND nombre = '$varCortar'";
            $query = $mysql->query($sql,$mysql->link);

            while($row = $mysql->fetch_array($query)){
              $arrayCuentas[] = $row['digitos'];
            }

            foreach($arrayCuentas as $rowx){
              $varCortar = $rowx;
              $where = ($varCortar <= 4)? "cuenta = LEFT ($pygCuenta, $varCortar)": "cuenta LIKE '$pygCuenta%' AND LENGTH(cuenta)=$varCortar";
              $sql   =  "SELECT
                              cuenta,
                              descripcion,
                              LEFT (cuenta, $varCortar) AS corte
                          FROM
                              puc
                          WHERE
                              activo = 1
                          AND $where
                          AND id_empresa = $id_empresa
                          GROUP BY corte LIMIT 0,1";

              $query = $mysql->query($sql, $mysql->link);
              $arrayPrincipal .= $mysql->fetch_assoc($query);
            }
            return $arrayPrincipal;
        }

        /**
        * @return array resultado pyg
        */
        private function saldoPyg($id_empresa,$varCortar,$mysql){

            $sql = "SELECT SUM(AC.debe - AC.haber) AS saldo
                    FROM asientos_colgaap AS AC,puc
                    WHERE
                        AC.activo=1
                        /*AND puc.activo=1*/
                        AND AC.id_empresa=$id_empresa
                        AND puc.id_empresa=$id_empresa
                        AND ( AC.codigo_cuenta LIKE '4%'
                            OR AC.codigo_cuenta LIKE '5%'
                            OR AC.codigo_cuenta LIKE '6%'
                            OR AC.codigo_cuenta LIKE '7%')
                        AND $this->wherePyG
                        AND LEFT (AC.codigo_cuenta, 1) = puc.cuenta
                        ";

            $query    = $mysql->query($sql,$mysql->link);
            $saldoPyg = $mysql->result($query, 0, "saldo");

            return array("saldo" => $saldoPyg, "cuenta" => ($saldoPyg > 0? 3610: 3605));        //3605 GANANCIA (SALDO < 0), 3610 PERDIDA (SALDO > 0)
        }

        /**
        * @return array capa1 Campo_+1,2,3 (activo,pasivo,patrimonio)
        *               capa2 Cuenta
        *               capa3 Campo descripcion, Campo saldo
        */
        private function cuentasBalance($id_empresa,$varCortar,$mysql){
            $sql = "SELECT digitos FROM puc_configuracion WHERE activo = 1 AND id_empresa = $_SESSION[EMPRESA] AND nombre = '$varCortar'";
            $query = $mysql->query($sql,$mysql->link);

            while($row = $mysql->fetch_array($query)){
              $arrayCuentas[] = $row['digitos'];
            }

            $whereNiveles = "";
            foreach($arrayCuentas as $rowx){
              $varCortar = $rowx;

              if($whereNiveles == ""){
                $whereNiveles .= "LEFT(AC.codigo_cuenta,$varCortar) = puc.cuenta";
              }
              else{
                $whereNiveles .= " OR LEFT(AC.codigo_cuenta,$varCortar) = puc.cuenta";
              }
            }

            $sql =  "SELECT puc.cuenta AS cuenta,
                            puc.descripcion AS descripcion,
                            SUM(AC.debe-AC.haber) AS saldo,
                            LEFT(puc.cuenta,1) AS campo
                        FROM puc,
                            asientos_colgaap AS AC
                        WHERE AC.activo=1
                            AND puc.activo=1
                            AND (
                                    AC.codigo_cuenta LIKE '1%'
                                    OR AC.codigo_cuenta LIKE '2%'
                                    OR AC.codigo_cuenta LIKE '3%'
                                )
                            AND ($whereNiveles)
                            AND $this->whereBalance
                            AND AC.id_empresa=$id_empresa
                            AND puc.id_empresa=$id_empresa
                        GROUP BY puc.cuenta
                        HAVING saldo <> 0
                        ORDER BY CAST(puc.cuenta AS CHAR) ASC";

            $query = $mysql->query($sql,$mysql->link);
            while ($row = $mysql->fetch_assoc($query)) {
                $ArrayBalance["campo_".$row["campo"]]["$row[cuenta]"] = array("descripcion"=>$row["descripcion"], "saldo"=>$row["saldo"]);
                $this->ArrayCuentas["campo_".$row["campo"]][] = $row["cuenta"];
            }

            return $ArrayBalance;
        }

        public function getActivo(){ return $this->ArrayQuery["campo_1"]; }
        public function getPasivo(){ return $this->ArrayQuery["campo_2"]; }
        public function getPatrimonio(){ return $this->ArrayQuery["campo_3"]; }

        public function getCuentasActivo(){ return $this->ArrayCuentas["campo_1"]; }
        public function getCuentasPasivo(){ return $this->ArrayCuentas["campo_2"]; }
        public function getCuentasPatrimonio(){ return $this->ArrayCuentas["campo_3"]; }

        public function getFecha(){ return $this->fecha; }
    }
?>
