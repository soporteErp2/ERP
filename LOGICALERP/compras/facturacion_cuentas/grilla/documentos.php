<?php
	include("../../../../configuracion/conectar.php");
    include("../../../../configuracion/define_variables.php");

    $id_empresa = $_SESSION['EMPRESA'];

    // CONSULTAR EL TERCERO DE LA FACTURA DE COMPRA
    $sql="SELECT id_proveedor,proveedor FROM compras_facturas WHERE activo=1 AND id_empresa=$id_empresa AND id=$id_factura_compra";
    $query=$mysql->query($sql,$mysql->link);
	$id_proveedor = $mysql->result($query,0,'id_proveedor');
	$proveedor    = $mysql->result($query,0,'proveedor');

    // CONSULTAR LOS DOCUMENTOS CRUCE DE LA FACTURA
    $sql="SELECT id_tercero,tercero,tipo_documento_cruce,prefijo_documento_cruce,numero_documento_cruce
    		FROM compras_facturas_cuentas
    		WHERE activo=1 AND id_empresa=$id_empresa AND id_factura_compra=$id_factura_compra AND tipo_documento_cruce<>''
    		GROUP BY id_tercero,tipo_documento_cruce,prefijo_documento_cruce,numero_documento_cruce ";
    $query=$mysql->query($sql,$mysql->link);
    while ($row=$mysql->fetch_array($query)) {

        $id_tercero = ($row['id_tercero']=='' || $row['id_tercero']==0)? $id_proveedor  : $row['id_tercero'];
        $tercero    = ($row['tercero']=='')? $proveedor  : $row['tercero'];

        $arrayTabla[$id_tercero][$row['tipo_documento_cruce']][$row['prefijo_documento_cruce']][$row['numero_documento_cruce']] = $tercero;

    }

    // CONSULTAR LOS DOCUMENTOS CRUCE DE LA FACTURA
    $sql="SELECT id_tercero,nombre_tercero,tipo_documento,prefijo_documento,numero_documento
            FROM compras_facturas_archivos_adjuntos
            WHERE activo=1 AND id_empresa=$id_empresa AND id_factura_compra=$id_factura_compra AND tipo_documento<>''
            GROUP BY id_tercero,tipo_documento,prefijo_documento,numero_documento ";
    $query=$mysql->query($sql,$mysql->link);
    while ($row=$mysql->fetch_array($query)) {
        $id_tercero = ($row['id_tercero']=='' || $row['id_tercero']==0)? $id_proveedor  : $row['id_tercero'];
        $tercero    = ($row['tercero']=='')? $proveedor  : $row['tercero'];

        $arrayTabla[$id_tercero][$row['tipo_documento']][$row['prefijo_documento']][$row['numero_documento']] = $tercero;

    }

    foreach ($arrayTabla as $id_tercero => $arrayTabla1) {
        foreach ($arrayTabla1 as $tipo_documento_cruce => $arrayTabla2) {
            foreach ($arrayTabla2 as $prefijo_documento_cruce => $arrayTabla3) {
                foreach ($arrayTabla3 as $numero_documento_cruce => $tercero) {
                  $bodyTable .= '<tr>
                                    <td title="'.$tercero.'">'.$tercero.' </td>
                                    <td>'.$tipo_documento_cruce.'</td>
                                    <td>'.$prefijo_documento_cruce.'</td>
                                    <td>'.$numero_documento_cruce.'</td>
                                    <td><img src="img/adjunto.png" title="Archivos Adjuntos" onclick="ventanaArchivosAdjuntos('.$id_tercero.',\''.$tipo_documento_cruce.'\',\''.$prefijo_documento_cruce.'\',\''.$numero_documento_cruce.'\')"></td>
                                </tr>';
                }
            }
        }
    }


    // compras_facturas_archivos_adjuntos
 ?>
<link rel="stylesheet" type="text/css" href="facturacion_cuentas/grilla/style.css">
<div class="content">
	<div class="separator">DOCUMENTOS<div class="close" onclick="Win_Ventana_documentos_cruce.close();"></div></div>
    <div class="content-table">
    	<table class="table-grilla">
    		<tr class="thead">
    			<td>TERCERO</td>
    			<td>DOCUMENTO</td>
    			<td>PREFIJO</td>
    			<td>NUMERO</td>
    			<td></td>
    		</tr>

    		<tbody class="tbody" id="">
    			<?php echo $bodyTable; ?>
    		</tbody>

    	</table>
    </div>

</div>

<script>

	function ventanaArchivosAdjuntos(id_tercero,tipo_documento_cruce,prefijo_documento_cruce,numero_documento_cruce) {
		if (id_tercero=='') {
			alert('Aviso!\nLas cuentas o el documento debe tener un tercero!');
			return;
		}

        Win_Ventana_adjuntos_documentos = new Ext.Window({
            width       : 550,
            height      : 500,
            id          : 'Win_Ventana_adjuntos_documentos',
            title       : '',
            modal       : true,
            autoScroll  : false,
            closable    : false,
            autoDestroy : true,
            autoLoad    :
            {
                url     : 'facturacion_cuentas/grilla/documentos_adjuntos.php',
                scripts : true,
                nocache : true,
                params  :
                {
					id_factura_compra       : '<?php echo $id_factura_compra; ?>',
					id_tercero              : id_tercero,
					tipo_documento_cruce    : tipo_documento_cruce,
					prefijo_documento_cruce : prefijo_documento_cruce,
					numero_documento_cruce  : numero_documento_cruce,
                }
            },
        }).show();
    }

</script>