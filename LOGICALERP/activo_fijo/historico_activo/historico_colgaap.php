<?php

include("../../../configuracion/conectar.php");
include("../../../configuracion/define_variables.php");

$id_empresa=$_SESSION['EMPRESA'];
// CONSULTAMOS LA INFORMACION DEL ACTIVO
$sql="SELECT nombre_equipo,costo,depreciacion_acumulada FROM activos_fijos WHERE activo=1 AND id_empresa=$id_empresa AND id=$id_activo";
$query=mysql_query($sql,$link);
$nombre_equipo          = mysql_result($query,0,'nombre_equipo');
$costo                  = mysql_result($query,0,'costo');
$depreciacion_acumulada = mysql_result($query,0,'depreciacion_acumulada');
$valor_real             = $costo-$depreciacion_acumulada;

// CONSULTAMOS LOS DOCUMENTOS DEL ACTIVO
$sql="SELECT id_depreciacion,valor,depreciacion_acumulada FROM activos_fijos_depreciaciones_inventario WHERE activo=1 AND id_empresa=$id_empresa AND id_activo_fijo=$id_activo";
$query=mysql_query($sql,$link);
while ($row=mysql_fetch_array($query)) {
	$whereId.=($whereId=='')? 'id='.$row['id_depreciacion'] : ' OR id='.$row['id_depreciacion'] ;
	$arrayValor[$row['id_depreciacion']]=array('valor'=>$row['valor'],'depreciacion_acumulada'=>$row['depreciacion_acumulada']);
}

$sql="SELECT id,fecha_inicio,consecutivo FROM activos_fijos_depreciaciones WHERE activo=1 AND id_empresa=$id_empresa AND ($whereId) AND estado=1 AND sinc_nota='colgaap' ";
$query=mysql_query($sql,$link);
$cont=1;
while ($row=mysql_fetch_array($query)) {
	$bodyFilas.='<div class="filaTabla">
 					<div class="campo0">'.$cont.'</div>
 					<div class="campo0 campo1">'.$row['fecha_inicio'].'</div>
 					<div class="campo0 campo1">'.$row['consecutivo'].'</div>
 					<div class="campo0 campo1">'.$arrayValor[$row['id']]['depreciacion_acumulada'].'</div>
 					<div class="campo0 campo1">'.$arrayValor[$row['id']]['valor'].'</div>
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
		height            : calc(100% - 2px);
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
 			<div class="labelInfo">Nombre del Activo</div>
 			<div class="campoInfo"><?php echo $nombre_equipo; ?></div>
 		</div>
 		<div class="fila">
 			<div class="labelInfo">Costo del Activo</div>
 			<div class="campoInfo"><?php echo $costo; ?></div>
 		</div>
 		<div class="fila">
 			<div class="labelInfo">Depreciacion Acumulada</div>
 			<div class="campoInfo"><?php echo $depreciacion_acumulada; ?></div>
 		</div>
 		<div class="fila">
 			<div class="labelInfo">Valor Actual</div>
 			<div class="campoInfo"><?php echo $valor_real; ?></div>
 		</div>
 	</div>
 	<div class="grillaDocumentos">
 		<div class="contenedor_tabla">
 			<div class="headTabla">
 				<div class="campo0">&nbsp;</div>
 				<div class="campo0 campo1">Fecha Nota</div>
 				<div class="campo0 campo1" title="Consecutivo de la nota">Cons. Nota</div>
 				<div class="campo0 campo1" title="Depreciacion Acumulada">Deprec. Acum.</div>
 				<div class="campo0 campo1" title="Valor Depreciado">Depreciacion</div>
 			</div>
 			<div class="bodyTabla">
 				<?php echo $bodyFilas; ?>
 			</div>
 		</div>
 	</div>
 </div>