<?php
	include("../../../configuracion/conectar.php");
	include("../../../configuracion/define_variables.php");

	$id_empresa = $_SESSION['EMPRESA'];

	$sql="SELECT codigo FROM configuracion_arl WHERE activo=1 AND id_empresa=$id_empresa";
	$query=$mysql->query($sql,$mysql->link);

	$codigo = $mysql->result($query,0,'codigo');

 ?>
<style>
	#div-content {
		float      : left;
		width      : 100%;
		border-top :1px solid #99bbe8;
	}
	.table-form  input{
		line-height      : 1.42857143;
		color            : #555;
		background-color : #fff;
		border           : 1px solid #ccc;
		height           : 30px;
		width            : 130px;
		padding-left     : 5px;
	}

	.table-form {
		font-family : arial,sans-serif;
		margin-top  : 20px;
		font-size   : 12px;
		float       : left;
		margin-left : 10px;
    }

    .table-form td{
    	padding-left: 15px;
    }

</style>
<div id="div-content">
	<table class="table-form">
		<tr>
			<td>Codigo ARL</td>
			<td><input type="text" id="codigo" maxlength="6" value="<?php echo $codigo ?>"></td>
		</tr>
	</table>
</div>

<script>

	function guardarConfiguracion(){
		var codigo = document.getElementById('codigo').value;
		if (codigo=='') { alert("Aviso\nEl campo codigo no puede estar vacio"); return; }
		MyLoading2('on');
		Ext.Ajax.request({
		    url     : 'niveles_riesgos_laborales/bd/bd.php',
		    params  :
		    {
				op     : 'guardarConfiguracionARL',
				codigo : codigo,
		    },
		    success :function (result, request){
		                if(result.responseText == 'true'){ MyLoading2('off',{icono:'sucess',texto:'Se guardo el registro',duracion:2000}); Win_Ventana_confi_arl.close(); }
		                else{ MyLoading2('off',{icono:'fail',texto:'Error, no se actualizo el registro',duracion:2000}); }
		            },
		    failure : function(){ MyLoading2('off',{icono:'fail',texto:'Error de conexion',duracion:2000}); }
		});

	}

</script>