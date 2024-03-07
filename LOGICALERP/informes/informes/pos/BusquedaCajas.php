<?php
	include("../../../../configuracion/conectar.php");
	include("../../../../configuracion/define_variables.php");

	$contFilaCuenta = 0;
	$id_empresa     = $_SESSION['EMPRESA'];

 	$sqlCuentas   = "SELECT id,consecutivo_caja
					FROM ventas_pos_consecutivos_caja
					WHERE activo=1 AND id_empresa=$id_empresa
					GROUP BY consecutivo_caja ASC";

	$queryCuentas = mysql_query($sqlCuentas,$link);

	while ($rowCuentas = mysql_fetch_array($queryCuentas)) {
		$contFilaCuenta++;

		$divSaldoPendiente=($tabla!='terceros')? '<div class="campo3" id="saldo_'.$contFilaCuenta.'">'.$rowCuentas['saldo'].'</div>' : '' ;

		$filaInsertBoleta .= '<div class="filaBoleta" id="fila_boleta_'.$opcGrillaContable.'_'.$contFilaCuenta.'">

								<div class="campo0">'.$contFilaCuenta.'</div>
								<div class="campo1" id="consecutivo_caja_'.$contFilaCuenta.'">'.$rowCuentas['consecutivo_caja'].'</div>
								<div class="campo4" >
									<input type="checkbox" id="checkbox_'.$contFilaCuenta.'" onchange="checkGrilla(this,\''.$contFilaCuenta.'\')" value="'.$rowCuentas['id'].'" >
								</div>

							</div>';
	}
?>

<style>
	#contenedor_formulario{
		overflow   : hidden;
		width      : calc(100% - 30px);
		margin     : 15px;
		margin-top : 0px;
	}
</style>

<div id="contenedor_formulario">
	<div class="loadSaveFormulario" id="loadSaveFormulario_<?php echo $opcGrillaContable; ?>"></div>
	<div id="contenedor_tabla_boletas" style="height:300px;">
		<div class="headTablaBoletas">
			<div class="campo0"></div>
			<div class="campo1">Caja #</div>
			<div class="campo4" >Seleccione</div>
		</div>
		<div id="bodyTablaBoletas" style="height:260px;"><?php echo $filaInsertBoleta; ?></div>
	</div>
</div>

<script>

	//RECORRER EL ARRAY DE LOS CLIENTES, PARA HACER CHECK A LOS QUE YA ESTAN EN LA GRILLA PRINCIPAL DE CONFIGURACION
	for ( i =1; i < arrayConsecutivos.length ; i ++) {
		if (arrayConsecutivos[i]!="" && typeof(arrayConsecutivos[i])!="undefined") {
			document.getElementById('checkbox_'+i).checked=true;
		}
	};

	function checkGrilla(checkbox,cont){

		if (checkbox.checked ==true) {

			//CREAMOS EL DIV EN LA TABLA DE CONFIGURAR
            var div   = document.createElement('div');
            div.setAttribute('id','fila_consecutivo_caja_'+cont);
            div.setAttribute('class','filaBoleta');
            document.getElementById('bodyTablaConfiguracion').appendChild(div);

            //CAPTURAR LOS VALORES DE LA FILA PARA LUEGO MOSTRARLOS
			consecutivo_caja     = document.getElementById('consecutivo_caja_'+cont).innerHTML;

            //LLENAMOS EL ARRAY CON ELCLIENTE CREADO
            consecutivosConfigurados[cont]='<div class="campo0">'+contConsecutivos+'</div><div class="campo1" id="consecutivo_caja_'+cont+'">'+consecutivo_caja+'</div><div class="campo4" style="width:25px;" ><img src="img/eliminar.png" style="margin-right:6px;margin-top:-1px;" onclick="eliminaCaja('+cont+')" title="Eliminar Caja"></div>';
            //CREAMOS LOS ELEMENTOS DEL ELEMENTO CREADO
            document.getElementById('fila_consecutivo_caja_'+cont).innerHTML=consecutivosConfigurados[cont];
            contConsecutivos++;

            //LENAMOS UN ARRAY CON INDICE EL CONT Y COMO VALOR EL ID TERCERO
            arrayConsecutivos[cont]=document.getElementById('consecutivo_caja_'+cont).innerHTML;

		}
		else if (checkbox.checked ==false) {
			delete arrayConsecutivos[cont];
			delete consecutivosConfigurados[cont];
			(document.getElementById("fila_consecutivo_caja_"+cont)).parentNode.removeChild(document.getElementById("fila_consecutivo_caja_"+cont));
		}

	}


</script>