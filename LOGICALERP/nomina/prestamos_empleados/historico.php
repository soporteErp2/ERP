<?php

include("../../../configuracion/conectar.php");
include("../../../configuracion/define_variables.php");

$id_empresa=$_SESSION['EMPRESA'];
// CONSULTAMOS LA INFORMACION DEL PRESTAMO
$sql="SELECT valor_prestamo,cuotas,valor_prestamo_restante,cuotas_restantes,documento_tercero,tercero FROM nomina_prestamos_empleados WHERE activo=1 AND id_empresa=$id_empresa AND id=$id";
$query=mysql_query($sql,$link);

$valor_prestamo          = mysql_result($query,0,'valor_prestamo');
$cuotas                  = mysql_result($query,0,'cuotas');
$valor_prestamo_restante = mysql_result($query,0,'valor_prestamo_restante');
$cuotas_restantes        = mysql_result($query,0,'cuotas_restantes');
$documento_tercero       = mysql_result($query,0,'documento_tercero');
$tercero                 = mysql_result($query,0,'tercero');

// CONSULTAR LOS PRESTAMOS DE LA NOMINA
$sql="SELECT
            NPEC.id_planilla,
            NPEC.valor_concepto,
            NP.fecha_final,
            NP.consecutivo
      FROM
            nomina_planillas_empleados_conceptos AS NPEC
      INNER JOIN nomina_planillas AS NP ON NPEC.id_planilla=NP.id
      WHERE
            NP.activo = 1
      AND NP.id_empresa = $id_empresa
      AND NP.estado=1
      AND NPEC.id_prestamo=$id
      GROUP BY
            NPEC.id_planilla";
$query=mysql_query($sql,$link);
$cont=1;
while ($row=mysql_fetch_array($query)) {
	$bodyFilas.='<div class="filaTabla">
 					<div class="campo0">'.$cont.'</div>
 					<div class="campo0 campo1">'.$row['fecha_final'].'</div>
 					<div class="campo0 campo1">LN</div>
 					<div class="campo0 campo1">'.$row['consecutivo'].'</div>
 					<div class="campo0 campo1">'.number_format($row['valor_concepto'],$_SESSION['DECIMALESMONEDA']).'</div>
 					<!--<div class="campo0 campo1">'.$arrayValor[$row['id']]['valor'].'</div>-->
 				</div>';
	$cont++;
}

// CONSULTAR PRESTAMO LIQUIDACION
$sql="SELECT
            NPEC.id_planilla,
            NPEC.valor_concepto,
            NP.fecha_final,
            NP.consecutivo
      FROM
            nomina_planillas_liquidacion_empleados_conceptos AS NPEC
      INNER JOIN nomina_planillas_liquidacion AS NP ON NPEC.id_planilla=NP.id
      WHERE
            NP.activo = 1
      AND NP.id_empresa = $id_empresa
      AND NP.estado=1
      AND NPEC.id_prestamo=$id
      GROUP BY
            NPEC.id_planilla";
$query=mysql_query($sql,$link);

while ($row=mysql_fetch_array($query)) {
	$bodyFilas.='<div class="filaTabla">
 					<div class="campo0">'.$cont.'</div>
 					<div class="campo0 campo1">'.$row['fecha_final'].'</div>
 					<div class="campo0 campo1">LE</div>
 					<div class="campo0 campo1">'.$row['consecutivo'].'</div>
 					<div class="campo0 campo1">'.number_format($row['valor_concepto'],$_SESSION['DECIMALESMONEDA']).'</div>
 					<!--<div class="campo0 campo1">'.$arrayValor[$row['id']]['valor'].'</div>-->
 				</div>';
	$cont++;
}

// CONSULTAR LOS PAGO POR NOTAS O RECIBOS DE CAJA
$sql="SELECT * FROM nomina_prestamos_empleados_pagos WHERE activo=1 AND id_empresa=$id_empresa AND id_prestamo=$id";
$query=mysql_query($sql,$link);
while ($row=mysql_fetch_array($query)) {
	$bodyFilas.='<div class="filaTabla">
 					<div class="campo0">'.$cont.'</div>
 					<div class="campo0 campo1">'.$row['fecha'].'</div>
 					<div class="campo0 campo1">'.$row['tipo_documento'].'</div>
 					<div class="campo0 campo1">'.$row['consecutivo_documento'].'</div>
 					<div class="campo0 campo1">'.number_format($row['valor'],$_SESSION['DECIMALESMONEDA']).'</div>
 				</div>';
	$cont++;
}

 ?>
 <style>
 	.divContenedor{
		width      : 100%;
		height     : 100%;
		background : #FFF;
 	}
 	.infoActivo{
		float      : left;
		margin     : 20px 15px;
		width      : 351px;
		padding    : 10px;
		background : #EEE;
		border     : 1px solid #B3B3B3;
 	}

	.grillaDocumentos{
		width      : 95%;
		height     : 50%;
		height     : 250px;
		margin-top : 10px;
		margin     : auto;
	}

	.fila{
		float    : left;
		height   : 20px;
		overflow : hidden;
	}
	.labelInfo{
		width       : 150px;
		height      : 25px;
		font-weight : bold;
		float       : left;
		color       : #757575;
		line-height : 2;
		text-indent : 5px;
	}
	.campoInfo{
		width            : 200px;
		height           : 25px;
		float            : left;
		line-height      : 2;
		background-color : #FFFFFF;
		text-indent      : 5px;
		overflow         : hidden;
	}

	.contenedor_tabla{
		overflow          : hidden;
		width             : calc(100% - 2px);
		height            : calc(100% - 20px);
		border            : 1px solid #B3B3B3;
		border-radius     : 4px;
		webkit-box-shadow : 2px 2px 4px #666;
		-moz-box-shadow   : 2px 2px 2px #666;
		box-shadow        : 2px 2px 2px #666;
		background-color  : #F3F3F3;
	}
	.headTabla{
		overflow      : hidden;
		font-weight   : bold;
		width         : 100%;
		border-bottom : 1px solid #d4d4d4;
		height        : 22px;
	}

	.headTabla div{
		background-color :#F3F3F3;
		height           : 22px;
		padding-top      : 3;
	}
	.campo0{
		float            : left;
		width            : 28px;
		text-indent      : 5px;
		border-right     : 1px solid #d4d4d4;
		background-color : #F3F3F3;
	}

	.campo1{
		width: 100px;
		background-color: rgba(0,0,0,0);
        line-height: 1;
	}

	.bodyTabla{
		overflow-x       : hidden;
		overflow-y       : auto;
		width            : 100%;
		height           : 100%;
		background-color : #FFF;
		border-bottom    : 1px solid #d4d4d4;
	}

	.bodyTabla > div{
		overflow      : hidden;
		height        : 22px;
		border-bottom : 1px solid #d4d4d4;
	}

	.bodyTabla > div > div { height: 18px; /*background-color : #FFF;*/ padding-top: 4px; }
	.bodyTabla >  div:hover {background-color: #E3EBFC;}

	.filaTabla{ /*background-color:#F3F3F3;*/ cursor: hand; }

	.filaTabla input[type=text]{
		border:0px;
		width: 90%;
		height: 100%;
	}

	.filaTabla input[type=text]:focus { background: #FFF; }

 </style>

 <div class="divContenedor">
 	<div class="infoActivo">
 		<div class="fila">
 			<div class="labelInfo">Tercero Prestador</div>
 			<div class="campoInfo"><?php echo $documento_tercero.' - '.$tercero; ?></div>
 		</div>
 		<div class="fila">
 			<div class="labelInfo">Valor Prestamo</div>
 			<div class="campoInfo"><?php echo $valor_prestamo; ?></div>
 		</div>
 		<div class="fila">
 			<div class="labelInfo">Cuotas</div>
 			<div class="campoInfo"><?php echo $cuotas; ?></div>
 		</div>
 		<div class="fila">
 			<div class="labelInfo">Valor Restante</div>
 			<div class="campoInfo"><?php echo $valor_prestamo_restante; ?></div>
 		</div>
 		<div class="fila">
 			<div class="labelInfo">Cuotas Restantes</div>
 			<div class="campoInfo"><?php echo $cuotas_restantes; ?></div>
 		</div>
 	</div>
 	<div class="grillaDocumentos">
 		<div class="contenedor_tabla">
 			<div class="headTabla">
 				<div class="campo0">&nbsp;</div>
 				<div class="campo0 campo1">Fecha</div>
 				<div class="campo0 campo1" title="Documento">Documento</div>
 				<div class="campo0 campo1" title="Consecutivo del documento">Cons.</div>
                <div class="campo0 campo1" title="Abono">Abono</div>
 				<!--<div class="campo0 campo1" title="Valor Depreciado">Depreciacion</div>-->
 			</div>
 			<div class="bodyTabla">
 				<?php echo $bodyFilas; ?>
 			</div>
 		</div>
 	</div>
 </div>