<?php
    include("../../../configuracion/conectar.php");
    include("../../../configuracion/define_variables.php");

    $id_empresa = $_SESSION['EMPRESA'];

    $sqlTerceros   = "SELECT * FROM terceros_upload WHERE id_empresa='$id_empresa' AND activo = 1 AND estado=0";
    $queryTerceros = mysql_query($sqlTerceros,$link);

    $body = '<script>contUploadTercero = 0;</script>
            <div class="contenedorUploadTercero">
                <div class="titleGrilla"><b>UPLOAD TERCEROS</b></div>
                <div class="contenedorHeadArticulos">
                    <div class="headArticulos" id="headUploadTercero">
                        <div class="label" style="width:40px !important; border-left:none; padding-left:2px;"></div>
                        <div class="label" title="Codigo/EAN">Codigo/EAN</div>
                        <div class="label">Articulo</div>
                        <div class="label" title="Unidad">Unidad</div>
                        <div class="label" title="Cantidad">Cantidad</div>
                        <div class="label" title="Descuento">Descuento</div>
                        <div class="label" title="Precio Unitario">Precio Unitario</div>
                        <div class="label" title="Precio Total">Precio Total</div>
                        <div class="labelCheck" title="Activo Fijo">A.F.</div>
                        <div class="labelCheck" title="Costo">C.</div>
                        <div class="labelCheck" title="Gasto de Venta" style="border-right: 1px solid #d4d4d4">G.V.</div>
                        <div style="float:right; min-width:80px;"></div>
                    </div>
                </div>
                <div class="bodyUploadTercero" id="bodyUploadTercero" onscroll="resizeHeadMyGrilla(this,\'headUploadTercero\')">';

    $cont = 0;
    while($rowTerceros = mysql_fetch_array($queryTerceros)){
        $cont++;
        $body .=    '<div class="filaUploadTercero" id="filaUploadTercero_'.$cont.'">
                        '.cargaDivsUnidadesBloqueadas($cont, $rowTerceros['id'], $rowTerceros['id_inventario'], $rowTerceros['codigo'], $rowTerceros['nombre'], $rowTerceros['cantidad'], $rowTerceros['costo_unitario'], $rowTerceros['tipo_descuento'], $rowTerceros['descuento'],$rowTerceros['id_impuesto'],$rowTerceros['impuesto'], $rowTerceros['valor_impuesto'],$estado,$rowTerceros['nombre_unidad_medida'],$rowTerceros['cantidad_unidad_medida'],$rowTerceros['check_opcion_contable']).'
                    </div>';
    }

    $body .=    '</div>
            </div>
            <script>
                // resizeHeadMyGrilla(document.getElementById("DivArticulosFactura"),\'headUploadTercero\');

                contUploadTercero='.$cont.';
            </script>';

    echo $body;

    //==================================== CARGAR ARTICULOS EN FACTURA ABIERTA ==================================================================//
    function cargaDivsUnidadesBloqueadas($cont,$id = 0,$id_inventario = '',$codigo = '',$nombre = '',$cantidad = 0,$costo_unitario = 0,$tipoDescuento='',$descuento = 0,$id_impuesto=0,$impuesto='', $valor_impuesto=0,$estado='',$nombre_unidad='',$cantidad_unidad='',$check_opcion_contable=''){

        $body ='<div class="label" style="width:40px !important; border-left:none; padding-left:2px;">
                    <div style="float:left; margin-top:3px;">'.$cont.'</div>
                    <div style="float:left; width:18px" id="renderUploadTercero_'.$cont.'"></div>
                </div>

                <div class="campo">
                    <input type="text" id="eanUploadTercero_'.$cont.'" readonly  style="float:left;" value="'.$codigo.'" />
                </div>

                <div class="campo"><input type="text" id="nombreUploadTercero_'.$cont.'" readonly style="text-align:left;" readonly value="'.$nombre.'"/></div>

                <div class="campo"><input type="text" id="unidadesFactura_'.$cont.'" style="text-align:left" value="'.$nombre_unidad.' x '.$cantidad_unidad.'" readonly/></div>
                <div class="campo"><input type="text" id="cantUploadTercero_'.$cont.'" value="'.$cantidad.'" readonly  /></div>

                <div class="campo campoDescuento">
                    <div id="tipoDescuentoArticulo_'.$cont.'" title="En '.$tipoDescuento.'">
                        <img src="img/'.$tipoDescuento.'.png" id="imgDescuentoArticulo_'.$cont.'"/>
                    </div>
                    <input type="text" id="descuentoUploadTercero_'.$cont.'" value="'.$descuento.'" readonly/>
                </div>

                <div class="campo" ><input type="text" id="costoUploadTercero_'.$cont.'" readonly  value="'.$costo_unitario.'"/></div>
                <div class="campo"><input type="text" id="costoTotalUploadTercero_'.$cont.'" readonly/></div>

                <div class="campoOptionCheck" id="div_check_factura_activo_fijo_'.$cont.'">'.$check_contable_activo.'</div>
                <div class="campoOptionCheck" id="div_check_factura_costo_'.$cont.'">'.$check_contable_costo.'</div>
                <div class="campoOptionCheck" id="div_check_factura_gasto_'.$cont.'" style="border-right: 1px solid #d4d4d4;">'.$check_contable_gasto.'</div>';

        return $body;
    }
?>

<div id="divPadreModalUploadFile" class="fondo_modal_upload_file">
    <div>
        <div>
            <div>
                <div id="div_upload_file">
                    <div></div>
                </div>
                <div class="btn_div_upload_file2" onclick="close_ventana_upload_file()">X</div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">

	function close_ventana_upload_file(){ document.getElementById('divPadreModalUploadFile').setAttribute('style',''); }
	function createUploader(){

        var uploader = new qq.FileUploader({
            element : document.getElementById('div_upload_file'),
            action  : '../terceros/terceros/upload_file/upload_file.php',
            debug   : false,
            params  : { },
            button            : null,
            multiple          : false,
            maxConnections    : 3,
            allowedExtensions : ['xls', 'xlsx', 'csv','doc', 'docx', 'bmp', 'jpeg', 'jpg', 'png', 'pdf', 'txt'],
            sizeLimit         : 10*1024*1024,
            minSizeLimit      : 0,
            onSubmit          : function(id, fileName){},
            onProgress        : function(id, fileName, loaded, total){},
            onComplete        : function(id, fileName, responseJSON){
                                    document.getElementById('div_upload_file').querySelector('.qq-upload-list').innerHTML='';
                                    document.getElementById('divPadreModalUploadFile').setAttribute('style','');

                                    if(responseJSON.idInsert > 0){ Inserta_Div_ordenesCompraDocumentos(responseJSON.idInsert); }
                                },
            onCancel : function(fileName){},
            messages :
            {
                typeError    : "{file}\nArchivo no permitido.\n\n Solo se permiten los siguientes archivo:\n\n'jpg', 'bmp', 'pdf','xls','doc'",
                sizeError    : "\"{file}\"  Archivo muy grande, Tamano Maximo Permitido ( {sizeLimit} ).",
                minSizeError : "{file} is too small, minimum file size is {minSizeLimit}.",
                emptyError   : "{file} is empty, please select files again without it.",
                onLeave      : "Cargando Archivo."
            }
        });
    }
    createUploader();

</script>