<?php
	include("../../../../configuracion/conectar.php");
	include("../../../../configuracion/define_variables.php");

	$id_usuario      =	$_SESSION['IDUSUARIO'];
	$nombre_usuario  =	$_SESSION['NOMBREFUNCIONARIO'];
	$filtro_sucursal = $_SESSION['SUCURSAL'];

	switch ($op) {
		case "filtro_inventario_parcial":
			filtro_inventario_parcial();
			break;

		case 'guardarValoresCantidadStock':
			guardarValoresCantidadStock($cantidad_minima,$cantidad_maxima,$id,$filtro_sucursal,$idBodega,$link);
			break;

		case 'filtro_fecha_kardex':
			filtro_fecha_kardex($id,$id_item,$filtro_bodega);
			break;
}


	function filtro_inventario_parcial(){
		echo '	<div style="width:100%; margin:10px; overflow:hidden;">
					<div style="float:left; width:30%;">Fecha Inicial</div>
					<div style="float:left; width:65%;"><input type="text" id="fecha_ini"/></div>
				</div>
				<div style="width:100%; margin:10px; overflow:hidden;">
					<div style="float:left; width:30%;">Fecha Final</div>
					<div style="float:left; width:65%;"><input type="text" id="fecha_fin"/></div>
				</div>
				<script>
					new Ext.form.DateField(
						{
							format 		:	"Y-m-d",
							width		:	150,
							allowBlank	:	false,
							showToday	:	false,
							applyTo		:	"fecha_ini",
							editable	:	false
						}
					);

					new Ext.form.DateField(
						{
							format 		:	"Y-m-d",
							width		:	150,
							allowBlank	:	false,
							showToday	:	false,
							applyTo		:	"fecha_fin",
							editable	:	false
						}
					);

				</script>';
	}


	function guardarValoresCantidadStock($cantidad_minima,$cantidad_maxima,$id,$filtro_sucursal,$idBodega,$link){
		//ACTUALIZAMOS LOS VALORES PARA EL STOCK DE ESE ARTICULO
		$sql="UPDATE inventario_totales SET cantidad_minima_stock=$cantidad_minima, cantidad_maxima_stock=$cantidad_maxima
				WHERE id='$id' AND id_sucursal='$filtro_sucursal' AND id_ubicacion='$idBodega' AND activo=1 AND id_empresa=".$_SESSION['EMPRESA'];
		$query=mysql_query($sql,$link);

		if ($query) {
			echo '<img src="images/saved.png" title="Guardado" />
					<script>
						Actualiza_Div_InventarioTotales("'.$id.'");
						Win_inventario_configuracion.close();
					</script>
					';
		}else{
			echo '
				<script>alert("Error!\nSe produjo un error y no se guardaron los datos\nSi el problema persiste comuniquese con el administrador de sistema");</script>
				<img src="images/alert.png" title="Error! no se guardaron" />
			';
		}
	}

	function filtro_fecha_kardex($id,$id_item,$filtro_bodega){
		$dateIni = date("Y-m-01");
		$dateFin = date("Y-m-d");

		echo'<div style="height:100%; border-right:1px solid #5179B3; width:145px;">
				<div style="color:#5179B3; text-align:center; margin:5px;">Filtro Fecha</div>
				<div style="margin:5px;"><input type="text" id="filtro_fecha_kardex" style="width:125px;"></div>
			</div>
			<script type="text/javascript">
				new Ext.form.DateField({
				    format     : "Y-m-d",               //FORMATO
				    width      : 130,                   //ANCHO
				    allowBlank : false,
				    showToday  : false,
				    applyTo    : "filtro_fecha_kardex",
				    editable   : false,                 //EDITABLE
				    maxValue   : "'.$dateFin.'",          //MAXIMO
				    value      : "'.$dateIni.'",             //VALOR POR DEFECTO
				    listeners  : { select: function() { reloadKardex(this.value); } }
				});

				function reloadKardex(fecha_consulta){

					Ext.get("contenedor_kardex").load({
						url     : "inventario_unidades/kardex/kardex.php",
						scripts : true,
						nocache : true,
						params  :
						{
							fecha_consulta : fecha_consulta,
							id             : "'.$id.'",
							id_item        : "'.$id_item.'",
							filtro_bodega  : "'.$filtro_bodega.'",
						}
					});
				}

				reloadKardex("'.$dateIni.'");
			</script>';


	}

?>