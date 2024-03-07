<?php
	include("../../configuracion/conectar.php");
	include(".../../configuracion/define_variables.php");
	include("bd/functions_bd.php");
?>
    <div id="DivBtnDocumentsSigui" style="float:left; width:320px; height:auto; margin:0 0 0 0">
    
    </div>

    <div style="float:left; width:287px; height:60px; border:1px #000">
        <div class="EmpConte" style="margin:15px 0 0 10px; width:300px">
            <div class="EmpLabel" style="width:100px;">
                Tipo de Documento
            </div>
            <div class="EmpField">
                <select class="myfield" name="tipo_documentoEmpleado" id="tipo_documentoEmpleado" style="width:170px">
                  <option value="0" selected>Seleccione...</option>
                    <?php
                        cargaOption("empleados_tipo_documento","id","nombre",false);
                    ?>
                </select>
            </div>
        </div>
    </div>

<script>
new Ext.Panel
(
	{ 
		border		: false,
		renderTo	: 'DivBtnDocumentsSigui',
		bodyStyle 	: 'background-color:<?php echo $_SESSION['COLOR_FONDO'] ?>;',
		tbar		:
		[
			{
				xtype		: 'button',
				text		: 'Siguiente',
				scale		: 'large',
				iconCls		: 'siguiente',
				iconAlign	: 'left',
				handler 	: function(){
									BloqBtn(this); 
									var tipo_documento = document.getElementById("tipo_documentoEmpleado").value;
									if(tipo_documento == 0){
										alert('Primero debe seleccionar el tipo de Documento!');return false;
									}
									SiguienteDocumento0(opcion_guardar,tipo_documento);
							  }					
			}
		]
	}
);


</script>