<?php
	include("../../../configuracion/conectar.php");
	include("../bd/functions_bd.php");
	include("../../../configuracion/define_variables.php");
	include("../../../misc/MyGrilla/class.MyGrilla.php");
	
	if($id!=''){
		cargaDatosVariable($id);
		echo'<script>
				var op  = "actualizaVariable"
				,	 id = "'.$id.'";
			</script>';
	}
	else{ 
		echo'<script>
				var op = "guardaVariable"
				,	id = "";
			</script>'; 
	}		
?>
<script src="../../misc/MyFunctions.js"> </script>

<style>
	.EmpConte{float :left; margin:0 0 0 0; width:340px; height:25px}
	.EmpLabel{float :left; width:150px;}
	.EmpField{float :left; width:170px;}
</style>

<form name="FormularioVariables" id="FormularioVariables">   
    <div style="float:left; margin:10px; width:350px">

                <div class="EmpConte">
                    <div class="EmpLabel">
                        Nombre Variable
                    </div>
                    <div class="EmpField">
                        <input class="myfieldObligatorio" name="nombre_variable"  type="text" id="nombre_variable" value="<?php echo $nombre_variable; ?>" style="width:200px;" onkeypress="return ValidarNL(event)" onKeyUp="upperCase(this.id)" onBlur="ValidarFieldVacio(this)" <?php if($id) echo "disabled"; ?> /> 
						<input class="myfield" name="id_variable"  type="hidden" id="id_variable" value="<?php echo $id; ?>"/>
                    
                    </div>
                </div> 
				
				 <div class="EmpConte">
                    <div class="EmpLabel">
                        Detalle de la Variable
                    </div>
                    <div class="EmpField">
                        <input class="myfieldObligatorio" name="detalle_variable"  type="text" id="detalle_variable" value="<?php echo $detalle_variable; ?>" style="width:200px;" onBlur="ValidarFieldVacio(this)"/>
                    </div>
                </div> 
                            		
                <div class="EmpConte">
                    <div class="EmpLabel">
                        Tabla
                    </div>
                    <div class="EmpField">
                        <select class="myfieldObligatorio" name="tabla" id="tabla" onChange="buscarCampo();" style="width:200px" onBlur="ValidarFieldVacio(this)">
                            <option value="" selected>Seleccione...</option>
							<?php
                                cargaOptionsTablas($tabla);
                            ?>
                        </select>
                    </div>
                </div> 
				
				 <div class="EmpConte">
                    <div class="EmpLabel">
                        Campo
                    </div>
                    <div class="EmpField" id="campo_div">
                        <select class="myfieldObligatorio" name="campo" id="campo" style="width:200px" <?php if(!$campo) echo "disabled" ?> onChange="existeVariable()" onBlur="ValidarFieldVacio(this)">
							<option value="" selected>Seleccione...</option>
							<?php
								if($campo){
									cargaOptionsCampo($tabla,$campo);
								}						
							?>
                        </select>
                    </div>
                </div> 
				
				 <div class="EmpConte">
                    <div class="EmpLabel">
                        Funcion
                    </div>
                    <div class="EmpField">
                        <select class="myfield" name="funcion" id="funcion" style="width:200px" >
                        	<option value="">Seleccione...</option>
                            <?php
                                // cargaOption("tipo_documento","id","nombre",false,$tipo_id1);
                            ?>
                        </select>
                    </div>
                </div>                                  
    </div>
</form>  
<script type="text/javascript">

	ValidaFormularioEnCarga('FormularioVariables');

	function buscarCampo() {
		var selIndex = document.getElementById("tabla").selectedIndex;
		var tabla    = document.getElementById("tabla").options[selIndex].value;

		Ext.get('campo_div').load({
			url		:	'bd/bd.php',
			scripts	:	true,
			nocache	:	true,
			params	:
			{
				op    :	'optionCampo',
				tabla :	tabla
			}
		});
	}

	function existeVariable(){
		var op    = "existeVariable";
		var tabla = document.getElementById("tabla").value;
		var campo = document.getElementById("campo").value;

		Ext.Ajax.request({
			url		: 'bd/bd.php',
			method	: 'post',
			params	:
			{
				op    :	op,
				campo :	campo,
				tabla :	tabla,
				id    :	id
			},
			success : function (result, request)
			{
				var resultado  =  result.responseText.split("{.}");
				var respuesta = resultado[0];
				if(respuesta == 'true'){
					alert("Esta variable ya fue definida anteriormente, no se guardara.");
					return respuesta;				
				}
				else{ return respuesta;	}
			}
		});
	}

	function eliminaVariable(){

		var op = "eliminaVariable";
		Ext.Ajax.request
		(
			{
			url		: 'bd/bd.php',
			method	: 'post',
			timeout : 180000,
			params	:
			{
				op : op,
				id : id
			},
			success : function (result, request)
				{
					var resultado  =  result.responseText.split("{.}");
					var respuesta = resultado[0];
					var res = resultado[1];
					if(respuesta == 'false'){
						alert(res);	
					}
					if(respuesta == 'true'){
						Win_Editar_Variable.close();
						Elimina_Div_Variables(id);
					}
				}
			}
		);
	}

	function guardaVariable(){
		
		var tabla 				= document.getElementById("tabla").value;
		var nombre_variable 	= document.getElementById("nombre_variable").value;
		var detalle_variable 	= document.getElementById("detalle_variable").value;
		var campo 				= document.getElementById("campo").value;
		var funcion				= document.getElementById("funcion").value;
		
		if(tabla==""||nombre_variable==""||detalle_variable==""||campo==""){ //||funcion==""
			alert("Faltan campos por diligenciar.");
		}
		else{
			Ext.Ajax.request
			(
				{
				url		: 'bd/bd.php',
				method	: 'post',
				params	:
					{
						op				: op,
						id				: id,
						tabla			: tabla,
						grupo			: '<?php echo $id_grupo ?>',
						nombre_variable	: nombre_variable,
						detalle_variable: detalle_variable,
						campo			: campo,
						funcion			: funcion
					},
				success : function (result, request)
					{
						var resultado  =  result.responseText.split("{.}");
						var respuesta = resultado[0];
						var resp = resultado[1];
						if(respuesta == 'false'){
							alert(resp);
						}else{
							Win_Editar_Variable.close();
							if(op=="guardaVariable"){
								Inserta_Div_Variables(resp);							
							}else{
								Actualiza_Div_Variables(id);
							}							
						}
					}
				}
			);
		}
	}
</script>