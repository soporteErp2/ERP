<?php
  include_once('../../../../configuracion/conectar.php');
  include_once('../../../../configuracion/define_variables.php');
  include_once('../../ClassFuncionesInforme.php');

  /**
   * @class InformeFacturaRadicada
   */
  class InformeFacturaRadicada extends FuncionesInforme{
    public $IMPRIME_HTML                = '';
    public $IMPRIME_PDF                 = '';
    public $MyInformeFiltroFechaInicio  = '';
    public $MyInformeFiltroFechaFinal   = '';
    public $sucursal                    = '';
    public $cliente                     = '';
    public $arrayccosJSON               = '';
    public $GUARDAR_PDF                 = '';
    public $mysql                       = '';
    public $id_empresa                  = '';
    public $customWhere                 = '';
    public $arrayDoc                    = array();

    /**
     * [__construct]
     * @param str $IMPRIME_HTML                 Generar en HTML
     * @param str $IMPRIME_PDF                  Generar en PDF
     * @param dat $MyInformeFiltroFechaInicio   Fecha inicial del informe
     * @param dat $MyInformeFiltroFechaFinal    Fecha final del informe
     * @param int $sucursal                     Filtro por sucursal
     * @param int $cliente                      Filtro por cliente
     * @param obj $mysql                        Objeto de conexion a la base de datos
     */
    function __construct($IMPRIME_HTML,$IMPRIME_PDF,$MyInformeFiltroFechaInicio,$MyInformeFiltroFechaFinal,$sucursal,$cliente,$arrayccosJSON,$MyInformeIncluirAnuladasNC,$GUARDAR_PDF,$mysql){
      $this->IMPRIME_HTML                 = $IMPRIME_HTML;
      $this->IMPRIME_PDF                  = $IMPRIME_PDF;
      $this->MyInformeFiltroFechaInicio   = $MyInformeFiltroFechaInicio;
      $this->MyInformeFiltroFechaFinal    = $MyInformeFiltroFechaFinal;
      $this->MyInformeIncluirAnuladasNC   = $MyInformeIncluirAnuladasNC;
      $this->sucursal                     = $sucursal;
      $this->cliente                      = $cliente;
      $this->arrayccosJSON                = json_decode($arrayccosJSON);
      $this->mysql                        = $mysql;
      $this->GUARDAR_PDF                  = $GUARDAR_PDF;
      $this->id_empresa                   = $_SESSION['EMPRESA'];
      $this->id_sucursal                  = $_SESSION['SUCURSAL'];
    }

    /**
     * @method showError Mostrar alerta si se presenta un error
     * @param  str $mensaje Mensaje de error a mostrar
     */
    public function showError($mensaje){
      echo "<script>alert('Error\n $mensaje');</script>" . $mensaje;
      exit;
    }

    /**
     * @method getCustomFiltres armar los filtros a aplicar al informe
     */
    public function getCustomFiltres(){
      if($this->MyInformeFiltroFechaFinal == "" || $this->MyInformeFiltroFechaInicio == ""){
        $this->showError("Debe Seleccionar las fechas del informe");
      } else{
        $whereFechas = " AND VF.fecha_inicio BETWEEN '$this->MyInformeFiltroFechaInicio' AND '$this->MyInformeFiltroFechaFinal'";
      }

      if(!empty($this->cliente)){
        $whereTercero = " AND VF.id_cliente = '$this->cliente'";
      }

      if($this->sucursal != "" && $this->sucursal != "global"){
        $whereSucursal = " AND VF.id_sucursal = '$this->sucursal'";
      }

      if(!empty($this->arrayccosJSON)){
        foreach($this->arrayccosJSON as $indice => $codigo_centro_costo){
          $ccos .= ($ccos == "")? "VF.id_centro_costo = '$codigo_centro_costo'" : " OR VF.id_centro_costo = '$codigo_centro_costo'";
        }
        $this->whereCcos .= " AND ($ccos)";
      }

      $this->customWhere = $whereFechas.$whereTercero.$whereSucursal.$this->whereCcos;
    }

    /**
     * @method getDocumentoInfo consultar la informacion de las facturas de compra
     */
    public function getDocumentoInfo(){

      //--------------------- DATOS CABECERA DE LA FACTURA -------------------//
      $sql_facturas_radicadas = "SELECT
                VF.id,
                VF.nit,
                VF.cliente,
                VF.numero_factura_completo,
                VF.fecha_creacion,
                VF.total_factura,
                VF.estado,
                VF.id_centro_costo,
                VF.codigo_centro_costo
              FROM
                ventas_facturas AS VF
              WHERE
                VF.activo = 1
              AND
                (VF.estado = 1 OR VF.estado = 2 OR VF.estado = 3)
              AND
                VF.id_empresa = $this->id_empresa
                $this->customWhere
              GROUP BY
                VF.id
              ORDER BY
                VF.centro_costo ASC";

      $this->query_facturas_radicadas = $this->mysql->query($sql_facturas_radicadas,$this->mysql->link);
      while($row = $this->mysql->fetch_array($this->query_facturas_radicadas)){
        $id_doc   = $row['id'];
        $id_ccos  = $row['id_centro_costo'];

        $this->arrayDoc[$id_ccos][$id_doc] = array(
                                                    'numero_factura_completo'  => $row['numero_factura_completo'],
                                                    'nit'                      => $row['nit'],
                                                    'cliente'                  => $row['cliente'],
                                                    'fecha_creacion'           => $row['fecha_creacion'],
                                                    'total_factura'            => $row['total_factura'],
                                                    'estado'                   => $row['estado'],
                                                    'codigo_centro_costo'      => $row['codigo_centro_costo']
                                                  );

        $this->totalFacturas[$id_ccos] += $row['total_factura'];
      }

      $this->cantidad_facturas = $this->mysql->num_rows($this->query_facturas_radicadas);
    }

    /**
     * getHtmlPdf armar el informe para la vista en la app y pdf
     * @return str body informe generado
     */
    public function getHtmlPdf(){
      if($this->cantidad_facturas > 0){
        foreach($this->arrayDoc as $id_ccos => $result1){
          //CABECERA DEL INFORME
          $table .=  '<tr class="thead" style="border: 1px solid #999; color: #f7f7f7;">
                        <td style="text-align:center;"><b>NUMERO FACTURA</b></td>
                        <td style="text-align:center;"><b>COD. CCOS</b></td>
                        <td style="text-align:center;"><b>NIT</b></td>
                        <td style="text-align:center;"><b>RAZON SOCIAL</b></td>
                        <td style="text-align:center;"><b>FECHA ELABORACION</b></td>
                        <td style="text-align:center;"><b>TOTAL</b></td>
                      </tr>';

          foreach($result1 as $id_fc => $result2){
            $styleCancel = ($result2['estado'] == 3)? "color:red;" : "";

            //CUERPO DEL INFORME
            $table .=  '<tr class="detail" style="color: #0a0318">
                          <td style="border-left:1px solid #999;text-align:center; ' . $styleCancel . '">' . $result2['numero_factura_completo'] . '</td>
                          <td style="text-align:center; ' . $styleCancel . '">' . $result2['codigo_centro_costo'] . '</td>
                          <td style="text-align:center; ' . $styleCancel . '">' . $result2['nit'] . '</td>
                          <td style="text-align:center; ' . $styleCancel . '">' . $result2['cliente'] . '</td>
                          <td style="text-align:center; ' . $styleCancel . '">' . $result2['fecha_creacion'] . '</td>
                          <td style="border-right:1px solid #999;text-align:right; ' . $styleCancel . '">' . number_format($result2['total_factura'], 0, "", ",") . '</td>
                        </tr>';
          }

          //PIE DE PAGINA DEL INFORME
          $table .=  '<tr class="total" style="border:1px solid #999;">
                        <td style="text-align:left;" colspan="5">TOTAL FACTURAS RADICADAS</td>
                        <td style="text-align:right;">' . number_format($this->totalFacturas[$id_ccos], 0, "", ",") . '</td>
                      </tr>';

          if($result1 != end($this->arrayDoc)){
            $table .=  '<tr>
                          <td>&nbsp;</td>
                        <tr>';
          }
        }

        $texto = '<style>
                    @page {
                      margin-top    : 1cm;
                      margin-bottom : 2cm;
                      margin-left   : 0.5cm;
                      margin-right  : 0.5cm;
                    }
                    .tableInforme{
                      font-size       : 12px;
                      width           : 100%;
                      margin-top      : 20px;
                      border-collapse : collapse;
                    }
                    .tableInforme .thead{
                      height        : 25px;
                      background    : #EEE;
                      color         : #8E8E8E;
                      border-top    : 1px solid #999;
                      border-bottom : 1px solid #999;
                      font-weight   : bold;
                      font-family   : arial,helvetica;
                    }
                    .tableInforme .total{
                      height        : 25px;
                      background    : #EEE;
                      color         : #8E8E8E;
                      border-top    : 1px solid #999;
                      border-bottom : 1px solid #999;
                      font-weight   : bold;
                      font-family   : arial,helvetica;
                    }
                    .my_informe_Contenedor_Titulo_informe{
                      float         :	left;
                      width         :	100%;
                      margin        :	0 0 10px 0;
                      font-size     :	11px;
                    }
                    .table{
                      font-size       : 12px;
                      width           : 100%;
                      border-collapse : collapse;
                      color           : #FFF;
                    }
                    .thead{
                      background  : #999;
                      font-weight : bold;
                      font-family : arial,helvetica;
                    }
                    .thead td {
                      height        : 30px;
                      background    : #999;
                      height        : 25px;
                      font-weight   : bold;
                      font-family   :arial,helvetica;
                      color         : #FFF;
                    }
                    .detail{
                      background  : #FFF;
                      font-family : arial,helvetica;
                    }
                    .detail td{
                      background    : #FFF;
                      height        : 25px;
                      font-family   :arial,helvetica;
                      color         : #000000;
                    }
                    .total{
                      background  : #EEE;
                      font-weight : bold;
                      font-family : arial,helvetica;
                    }
                    .total td{
                      border-top    : 1px solid #999;
                      border-bottom : 1px solid #999;
                      background    : #EEE;
                      height        : 25px;
                      font-weight   : bold;
                      font-family   :arial,helvetica;
                      color         : #8E8E8E;
                    }
                  </style>
                  <div class="my_informe_Contenedor_Titulo_informe" style="float:left;">
                    <table class="tableInforme" style="width:100%; border-collapse:collapse;">
                      ' . $table . '
                    </table>
                  </div>';

        $formato = $this->cargaFormatoDocumento('FR',$this->id_empresa,$this->id_sucursal);
        $textoFinal = $this->reemplazarVariables($formato,$texto,$this->id_empresa,$this->id_sucursal,$this->cliente,$this->cantidad_facturas,$this->MyInformeFiltroFechaInicio,$this->MyInformeFiltroFechaFinal);
        $documento = "Facturas Radicadas";

        if(isset($TAM)){$HOJA = $TAM;}else{$HOJA = 'LETTER';}
        if(!isset($ORIENTACION)){$ORIENTACION = 'P';}
        if(!isset($PDF_GUARDA)){$PDF_GUARDA = false;}
        if(isset($MARGENES)){
          list($MS, $MD, $MI, $ML) = split( ',', $MARGENES );
        } else{
          $MS = 30;
          $MD = 30;
          $MI = 30;
          $ML = 30;
        }
        if(!isset($TAMANO_ENCA)){$TAMANO_ENCA = 12 ;}

        if($this->IMPRIME_PDF == 'true'){
          include_once("../../../../misc/MPDF54/mpdf.php");
          $mpdf = new mPDF(
                            'utf-8',  		// mode - default ''
                            $HOJA,			  // format - A4, for example, default ''
                            12,				    // font size - default 0
                            '',				    // default font family
                            $MI,			    // margin left
                            $MD,			    // margin right
                            $MS,			    // margin top
                            $ML,			    // margin bottom
                            10,				    // margin header
                            10,				    // margin footer
                            $ORIENTACION	// L - landscape, P - portrait
                          );
          $mpdf->useSubstitutions = false;
          $mpdf->packTableData = false;
          $mpdf->SetAutoPageBreak(true);
          $mpdf->SetTitle($documento);
          $mpdf->SetAuthor($_SESSION['NOMBREFUNCIONARIO']." // ".$_SESSION['NOMBREEMPRESA']);
          $mpdf->SetDisplayMode( 'fullpage' );
          $mpdf->SetHeader("");
          $mpdf->WriteHTML(utf8_encode($textoFinal));

          //OUTPUT A ARCHIVO
          if($this->GUARDAR_PDF == 'true'){
      			$serv = $_SERVER['DOCUMENT_ROOT'] . "/";
      			$url  = $serv . 'ARCHIVOS_PROPIOS/empresa_' . $_SESSION['ID_HOST'] . '/archivos_temporales/';
      			if(!file_exists($url)){
              mkdir($url);
            }

      			$mpdf->Output($url . "Facturas_Radicadas.pdf",'F');
      		}
          //OUTPUT A VISTA
      		else{
            $mpdf->Output($documento.".pdf",'I');
          }
        }
        if($this->IMPRIME_HTML == 'true'){
          echo $textoFinal;
        }
      } else{
        echo "No hay resultados para este informe.";
      }
    }

    /**
     * @method generate Generar el informe
     */
    public function generate(){
      $this->getCustomFiltres();
      $this->getDocumentoInfo();
      $this->getHtmlPdf();
    }
  }

  $objectInform = new InformeFacturaRadicada($IMPRIME_HTML,$IMPRIME_PDF,$MyInformeFiltroFechaInicio,$MyInformeFiltroFechaFinal,$sucursal,$cliente,$arrayccosJSON,$GUARDAR_PDF,$mysql);
  $objectInform->generate();
?>
