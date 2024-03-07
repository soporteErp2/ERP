<?php
	include("../../../configuracion/conectar.php");
    include("../../../configuracion/define_variables.php");

    $texto1 = 'Asistente Generador Orden de Compra';
	$texto2 = 'Por favor seleccione la moneda que desdea utilizar en el Documento.';
	$texto3 = 'Por favor seleccione la tasa de cambio';

	$id_empresa  = $_SESSION['EMPRESA'];

	$SQL1     = "SELECT id,descripcion FROM configuracion_moneda WHERE activo=1 AND id <> 1";
	$consul1  = mysql_query($SQL1,$link);

	$SQL2     = "SELECT id,tasa_cambio,id_moneda FROM compras_ordenes WHERE activo=1 AND id_empresa = $id_empresa AND id = $consecutivo";
	$consul2  = mysql_query($SQL2,$link);
	$id_orden = mysql_result($consul2,0,'id');
	$tasa_cambio = mysql_result($consul2,0,'tasa_cambio');
	$id_moneda   = mysql_result($consul2,0,'id_moneda');

	/*if ($tasa_cambio != 0){

		 $tasa_cambio_ver = rtrim($tasa_cambio,'0');
		 echo $tasa_cambio_ver;
	}*/

   /* echo $id_orden;
    echo ' '.$id_empresa;
    echo ' '.$id_moneda;*/

?>
<div id="WizCapaPrincipal">
	<div id="WizCapaIzquierda"></div>
    <div id="WizCapaDerecha">
    	<div class="WizTitulo"><?php echo $texto1; ?></div>
		<div  class="WizContenido">
			<b><?php echo $texto2; ?></b>
			<br /><br />
		    <select class="myfield" id="id_moneda" style="width:170px">
			  <?php
		            //echo '<option value="0" selected>TODAS</option>';
					while($rowS=mysql_fetch_array($consul1)){
						$selected = ($rowS['id'] == $id_moneda)? 'selected': '';
					 	echo '<option value="'.$rowS['id'].'" '.$selected.'>'.$rowS['descripcion'].'</option>';
				    }
	          ?>
		    </select>
		</br>
		</br>
		  	<b><?php echo $texto3; ?></b>
		  	<br />
		  	<br />
		    <input type="text" class="myfield" id="tasa_cambio" value ="<?php echo $tasa_cambio;?>" style="width:170px;height:24px" onKeyup="validar_numero_moneda(event,this);"/>
		</br>
		</br>
		<input type="button" value="Generar >>" onClick="generaPDFMoneda();" style="width:100px; height:30px;"/>
		</div>
    </div>
</div>

<script>
	function generaPDFMoneda(){

			var tasa_cambio = document.getElementById('tasa_cambio').value,
			      id_moneda = document.getElementById('id_moneda').value;

		window.open("ordenes_compra/imprimir_orden_compra.php?id="+'<?php echo $id_orden; ?>'+'&id_moneda='+id_moneda+'&tasa_cambio='+tasa_cambio);
		windows_wizard_moneda.close(id);
	}

	function validar_numero_moneda(event,input,cont){
        var tecla = (input) ? event.keyCode : event.which
        ,   value = input.value;

        if(tecla == 13){
            input.blur();
            return true;
        }
        else if(tecla == 9 || tecla == 37 || tecla == 38 || tecla == 39 || tecla == 40 || tecla == 18 || tecla == 17 || tecla == 16){ return true; }

        patron = /[^\d.]/g;
        if(patron.test(value)){
            value       = value.replace(patron,'');
            input.value = value;
        }
        else if(isNaN(value)){ input.value = value.substring(0, value.length-1); }
        return true;
    }


</script>
