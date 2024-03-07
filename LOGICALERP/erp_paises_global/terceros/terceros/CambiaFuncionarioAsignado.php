<?php
    include("../../../../configuracion/conectar.php");
    include("../../../../configuracion/define_variables.php");

	if(!isset($option)){
		$consul = mysql_query("SELECT id_asignado,asignado FROM terceros_asignados WHERE id_tercero = $id AND id_empresa = $_SESSION[EMPRESA]",$link);
		$elid = mysql_result($consul,0,"id_asignado");
		$nombre = mysql_result($consul,0,"asignado");
?>

    <div id="ToolbarTareas">
    	<div style="float:left; width:270px; font-size:20px; margin:0 0 0 0;">Cambiar al Funcionario Asignado<br /><span style="font-size:12px"><?php echo '' ?></span></div>

        <div style="width:48px; height:48px; float:right; cursor:pointer;" onclick="Win_Cambia_Funcionario.close();">
    		<div class="ic_highlight_remove_white_24dp" style="float:left; width:36px; height:36px; margin: 0 0 0 6px;"></div>
        	<div style="text-align:center">Cerrar</div>
        </div>

		<div style="width:48px; height:48px; float:right; cursor:pointer;" onclick="GuardaFuncionarioAsignado();">
       		<div class="ic_check_circle_white_24dp" style="float:left; width:36px; height:36px; margin: 0 0 0 6px;"></div>
        	<div style="text-align:center">Guardar</div>
        </div>
    </div>

    <div class='ActividadesReglon' style="width:400px; margin:25px 0 0 10px;;">
        <div class='Actividadesfield' style="width:100px; float:left">Asignado a</div>
        <div id="ContenedorPersonas" class='ActividadesControl' style="width:400px;">
            <div >
                <div style="width:300px; float:left;">
                    <input id="ActividadId_asignado" type="hidden" value="<?php echo $elid?>" >
                    <input id="ActividadAsignado" onChange="alert('si');" type="text" class="MyField" style="width:310px; font-weight:bold" value="<?php echo $nombre ?>" onclick="BuscarFuncionario('ActividadId_asignado','ActividadAsignado')" onBlur="ValidarFieldVacio(this)" readonly="readonly" placeholder=" Funcionario al cual se le asigna el Prospecto...">
                </div>
            </div>

        </div>
    </div>


<script>
	function GuardaFuncionarioAsignado(){
		var id_asignado = document.getElementById('ActividadId_asignado').value;
		var nombre = document.getElementById('ActividadAsignado').value;
		if(id_asignado==""){alert("Por favor seleccione un funcionario"); return false;}
		Ext.Ajax.request(
			{
				url		: '../terceros/terceros/CambiaFuncionarioAsignado.php',
				params	: {
					option			:	'guarda',
					id_asignado		:	id_asignado,
					id				:	<?php echo $id ?>
				},
				success	: function (result, request){
					Win_Cambia_Funcionario.close();
					Actualiza_Div_<?php echo $cual ?>(<?php echo $id ?>);
				},
				failure : function(){alert('Error guardando el cambio de funcionario : '+result);}
			}
		);

	}

</script>


<?php
	}else{
		$consul = mysql_query("SELECT id_asignado FROM terceros_asignados WHERE id_tercero = $id AND id_empresa = $_SESSION[EMPRESA]",$link);
		if(mysql_num_rows($consul)>0){
			mysql_query("UPDATE terceros_asignados SET id_asignado = $id_asignado WHERE id_tercero = $id AND id_empresa = $_SESSION[EMPRESA]",$link);
		}else{
			mysql_query("INSERT INTO terceros_asignados (id_empresa,id_tercero,id_asignado) VALUES ($_SESSION[EMPRESA],$id,$id_asignado)",$link);
		}
		//echo "UPDATE terceros SET crm_asignado = $id_asignado WHERE id=$id";
	}
?>