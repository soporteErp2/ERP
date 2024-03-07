<?php
	include('../../../../configuracion/conectar.php');
	include("../../../../configuracion/define_variables.php");
	$id_empresa        = $_SESSION['EMPRESA'];
	$arrayMeses['01']  = 'Enero';
	$arrayMeses['02']  = 'Febrero';
	$arrayMeses['03']  = 'Marzo';
	$arrayMeses['04']  = 'Abril';
	$arrayMeses['05']  = 'Mayo';
	$arrayMeses['06']  = 'Junio';
	$arrayMeses['07']  = 'Julio';
	$arrayMeses['08']  = 'Agosto';
	$arrayMeses['09']  = 'Septiembre';
	$arrayMeses['10']  = 'Octubre';
	$arrayMeses['11']  = 'Noviembre';
	$arrayMeses['12']  = 'Diciembre';
	$arraytercerosJSON = json_decode($arraytercerosJSON);

	if (!empty($arraytercerosJSON)) {
		foreach ($arraytercerosJSON as $key => $id_cliente) {
			$whereIdTercero .= ($whereIdTercero=='')? "id_cliente=$id_cliente" : " OR id_cliente=$id_cliente" ;
		}
	}

	$whereIdTercero=($whereIdTercero<>'')? "AND ($whereIdTercero)" : "" ;
	if ($filtroSucursal<>'global') {
		$sql="SELECT nombre FROM empresas_sucursales WHERE activo=1 AND id_empresa=$id_empresa ANd id=$filtroSucursal";
		$query=$mysql->query($sql,$mysql->link);
		$nombre_sucursal = $mysql->result($query,0,'nombre');
		$whereIdSucursal = " AND id_sucursal=$filtroSucursal";
		$title_sucursal  = 'Sucursal: '.$nombre_sucursal;
	}
	else{
		$title_sucursal  = 'Todas las sucursales';
	}

	// CONSULTAR LAS FACTURAS EN ESE PERIODO DE TIEMPO
	$sql="SELECT id,numero_factura_completo,fecha_inicio,estado,exento_iva
			FROM ventas_facturas
			WhERE activo=1 AND id_empresa=$id_empresa AND fecha_inicio BETWEEN '$fecha_inicial' AND '$fecha_final' $whereIdTercero $whereIdSucursal";
	$query=$mysql->query($sql,$mysql->link);
	while ($row=$mysql->fetch_array($query)) {
		$id_factura_venta = $row['id'];
		$whereIdFacturas .= ($whereIdFacturas=='')? 'id_factura_venta='.$id_factura_venta : ' OR id_factura_venta='.$id_factura_venta ;
		$arrayFacturas[$id_factura_venta] = array('fecha_inicio'=>$row['fecha_inicio'],'estado'=>$row['estado'],'exento_iva'=>$row['exento_iva']);
	}

	// RECORREC EL INVENTARIO PARA OBTENER EL VALOR DEL SUBTOTAL
	$sql="SELECT id,
                id_factura_venta,
                codigo,
                nombre_unidad_medida,
                cantidad_unidad_medida,
                nombre,
                cantidad,
                saldo_cantidad,
                costo_unitario,
                costo_inventario,
                tipo_descuento,
                descuento,
                valor_impuesto
            FROM ventas_facturas_inventario
            WHERE activo=1 AND ($whereIdFacturas)";
	$query=$mysql->query($sql,$mysql->link);
	while ($row=$mysql->fetch_array($query)) {
		// SI ESTADO ES 3 DEJAR VACIOS LOS CAMPOS ACUMULADOS
        if ($arrayFacturas[$row['id_factura_venta']]['estado']==3) {
            $row['saldo_cantidad']   = 0;
            $row['descuento']        = 0;
            $row['costo_unitario']   = 0;
            $row['costo_inventario'] = 0;
            $row['valor_impuesto']   = 0;
        }

        $total_articulo = 0;
        $iva            = 0;
        $subtotal       = 0;
        $descuento      = 0;

        //SI EL DESCUENTO DEL ARTICULO FUE POR PORCENTAJE
        if ($row['descuento']>0) {
            $descuento =($row['tipo_descuento']=='porcentaje')?
                                                                ((($row['cantidad']*$row['costo_unitario'])*$row['descuento'])/100)
                                                                :
                                                                $row['descuento']
                                                                ;
        }

        $subtotal =($row['cantidad']*$row['costo_unitario']);

        //CALCULAR LOS VALORES ACUMULADOS
        if ($arrayFacturas[$row['id_factura_venta']]['exento_iva']<>'Si') {
            $iva=(($subtotal-$descuento)*$row['valor_impuesto'])/100;
        }

        // $acumuladoSubtotal  += $subtotal;
        // $acumuladoDescuento += $descuento;
        // $acumuladoIva       += $iva;


        // $arrayFacturas[$row['id_factura_venta']]['costo']     += ($row['cantidad']*$row['costo_inventario']);
        // $arrayFacturas[$row['id_factura_venta']]['subtotal']  += $subtotal;
        // $arrayFacturas[$row['id_factura_venta']]['descuento'] += $descuento;
        // $arrayFacturas[$row['id_factura_venta']]['iva']       += $iva;

        $fecha = $arrayFacturas[$row['id_factura_venta']]['fecha_inicio'];
		$fecha_explode = explode('-', $fecha);

        // $meses = explode('-', $fecha);
        // $anios = explode('-', $fecha);
        if ($tipo_informe=='dias') {
        	$dataArray[$fecha]+= ($valores_informe=='sin_iva')? $subtotal-$descuento : $subtotal-$descuento+$iva ;

        }
		if ($tipo_informe=='meses'){
			$dataArray[ $fecha_explode[0].'-'.$fecha_explode[1] ]+= ($valores_informe=='sin_iva')? $subtotal-$descuento : $subtotal-$descuento+$iva ;
		}
		if ($tipo_informe=='anios'){
			$dataArray[$fecha_explode[0]]+= ($valores_informe=='sin_iva')? $subtotal-$descuento : $subtotal-$descuento+$iva ;
		}

	}

	foreach ($dataArray as $fecha => $valor) {
		$fecha_explode = explode('-', $fecha);
        if ($tipo_informe=='dias') {
        	$arrayJs .='{
							"category"          :"'.$fecha.'",
							"values"            :"'.$valor.'",
							"customDescription" :  "'.number_format($valor).'"
						},';
        }
		if ($tipo_informe=='meses'){
			$arrayJs .='{
							"category"          :"'.$arrayMeses[$fecha_explode[1]].'",
							"values"            :"'.$valor.'",
							"customDescription" :  "'.number_format($valor).'"
						},';
		}
		if ($tipo_informe=='anios'){
			$arrayJs .='{
							"category"          :"'.$fecha_explode[0].'",
							"values"            :"'.$valor.'",
							"customDescription" :  "'.number_format($valor).'"
						},';
		}

	}

    if ($tipo_informe=='dias') {
		$title_tipo    = 'Grafico por Dias';
	}
	if ($tipo_informe=='meses'){
		$title_tipo    = 'Grafico por Meses';
	}
	if ($tipo_informe=='anios'){
		$title_tipo    = 'Grafico por Años';
	}

	$title_valores = ($valores_informe=='sin_iva')? 'Valores Sin IVA (Subtotal)' : 'Valores con IVA (Total)' ;
		// print_r($dataArray);
	// print_r(json_decode($arraytercerosJSON));

	// fecha_inicial
	// fecha_final

?>

<style>
	.content-head-report{
		width          : 95%;
		margin         : 25px 0px 10px 30px;
		border-bottom  : 1px solid #d4d4d4;
		padding-bottom : 10px;
	}
	.content-head-report .title{
		font-size: 15px;
		color: #1e82c4;
		font-weight: bold;
	}
	.content-head-report .sub-title{
		font-size: 12px;
		padding-left: 10px;
		/*font-weight: bold;*/
		/*color: #1e82c4;*/
	}

</style>

<body>
	<div class="content-head-report">
		<div class="title">FACTURAS DE VENTA</div>
		<div class="sub-title"><?php echo $title_sucursal ?></div>
		<div class="sub-title"><?php echo $title_tipo ?></div>
		<div class="sub-title"><?php echo $title_valores ?></div>
		<div class="sub-title">Fecha Inicial: <?php echo $fecha_inicial ?>   Fecha Final: <?php echo $fecha_final ?></div>
	</div>
	<div id="chartdiv" style="width: 100%; height: 400px; background-color: #FFFFFF;" ></div>
</body>

<!-- amCharts javascript code-->
<script>


AmCharts.makeChart("chartdiv",{
	"type"          : "serial",
	"categoryField" : "category",
	"startDuration" : 1,
	"theme": "light",
	"categoryAxis"  : {
		"gridPosition": "start",
		"labelRotation": 45
	},
	"trendLines" : [],
	"graphs"     : [
		{
			// "balloonText"    : "[[category]]: $[[value]]",
			"bullet"            : "round",
			"customBulletField" : "customBullet",
			"id"                : "AmGraph-1",
			"title"             : "graph 1",
			"valueField"        : "values",
			"visibleInLegend"   : false,
			"balloonText"       : "[[customDescription]]",
			"labelText"         : "[[customDescription]]",
			"labelRotation": -45
			// "valueField"     : "value"
		},
	],
	"guides"    : [],
	"valueAxes" : [
		{
			"id"               : "ValueAxis-1",
			// "logarithmic"   : true,
			// "autoGridCount" : false,
			"title"            : "Valor en Ventas"
		}
	],
	"allLabels" : [],
	"balloon"   : {},
	"legend"    : {
		"enabled"          : true,
		"useGraphSettings" : true
	},
	"titles": [],
	"dataProvider": [
		<?php echo $arrayJs ?>

		/*
		{
			"category": "category 1",
			"values": 800
		},
		{
			"category": "category 2",
			"values": 600
		},
		{
			"category": "category 3",
			"values": 200
		},
		{
			"category": "category 4",
			"values": 1
		},
		{
			"category": "category 5",
			"values": 200
		},
		{
			"category": "category 6",
			"values": 30
		},
		{
			"category": "category 7",
			"values": 60
		}*/
	],
  	"export": {
  	    	"enabled": true
  		}
	}
);
</script>
