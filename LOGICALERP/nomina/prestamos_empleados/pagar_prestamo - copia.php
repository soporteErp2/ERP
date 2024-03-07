<?php

include("../../../configuracion/conectar.php");
include("../../../configuracion/define_variables.php");

$id_empresa=$_SESSION['EMPRESA'];


 ?>
 <style>
 	.divContenedor{
		width      : 100%;
		height     : 100%;
		/*background : #FFF;*/
 	}

 	.fila{
 		width: 90%;
 		height: 25px;
 		/*border: 1px solid;*/
 		margin-top: 8px;
 		float: left;
 	}
 	.fila>div{
 		width: 49%;
 		height: 100%;
 		float: left;

 	}
 	.fila+div{
 		text-indent:15px;
 	}

 </style>

 <div class="divContenedor">
 	<div id="toolbar_correo" style="height:85px;"></div>
 	<div id="divLoad"></div>
	<div class="fila">
		<div style="text-indent: 15px;">Tipo de Documento</div>
		<div style="text-indent: 15px;">
			<select id="tipo_documento" class="myfield">
				<option value="RC">Recibo de Caja</option>
				<option value="NC">Nota Contable</option>
			</select>
		</div>
	</div>

	<div class="fila">
		<div>Consecutivo Documento</div>
		<div>
			<input type="text" id="consecutivo_documento" class="myfield" style="width:110px;float:left;margin-left:15px;" readonly>
			<div onclick="ventanaBuscardocumentoCruce()" style="width:20px;height:20px;float:left;cursor:pointer;background-image:url(img/buscar20.png);background-color:#F3F3F3;border:1px solid #D4D4D4;" title="Buscar Documento Cruce"></div>
			<input type="hidden" id="id_documento">
		</div>

	</div>

	<div class="fila">
		<div>Valor a Pagar</div>
		<div> <input type="text" id="abono" class="myfield"></div>
	</div>
	<div class="fila">
		<div>Observacion</div>
		<div><textarea id='observacion' class="myfield" cols="4" rows="10"></textarea></div>
	</div>

 </div>
 <script>

 	new Ext.Panel
	(
		{
			renderTo	:'toolbar_correo',
			frame		:false,
			border		:false,
			tbar		:
			[
				{
					xtype	: 'buttongroup',
					columns	: 3,
					title	: 'Opciones',
					items	:
					[
						{
							xtype		: 'button',
							text		: 'Abonar-Pagar',
							scale		: 'large',
							iconCls		: 'guardar',
							iconAlign	: 'top',
							handler 	: function(){BloqBtn(this); abonarPagar();}
						},
						{
							xtype		: 'button',
							text		: 'Regresar',
							scale		: 'large',
							iconCls		: 'regresar',
							iconAlign	: 'top',
							handler 	: function(){BloqBtn(this); Win_Ventana_ventana_abono.close(id);}
						}
					]
				}
			]
		}
	);

	function abonarPagar(){
		var tipo_documento        = document.getElementById('tipo_documento').value
		,	id_documento          = document.getElementById('id_documento').value
		,	consecutivo_documento = document.getElementById('consecutivo_documento').value
		,	abono                 = document.getElementById('abono').value
		,	observacion           = document.getElementById('observacion').value;

		Ext.get('divLoad').load({
			url     : 'prestamos_empleados/bd/bd.php',
			scripts : true,
			nocache : true,
			params  :
			{
				opc                   : 'abonarPagar',
				id_prestamo           : '<?php echo $id_prestamo; ?>',
				tipo_documento        : tipo_documento,
				id_documento          : id_documento,
				consecutivo_documento : consecutivo_documento,
				abono                 : abono,
				observacion           : observacion,
			}
		});
	}

	function ventanaBuscardocumentoCruce() {
		var tipo_documento        = document.getElementById('tipo_documento').value
		,	title = (tipo_documento=='RC')? 'Recibo de Caja' : 'Nota Contable' ;


		Win_Ventana_buscar_documento_cruce = new Ext.Window({
		    width       : 490,
		    height      : 450,
		    id          : 'Win_Ventana_buscar_documento_cruce',
		    title       : title,
		    modal       : true,
		    autoScroll  : false,
		    closable    : false,
		    autoDestroy : true,
		    autoLoad    :
		    {
		        url     : 'prestamos_empleados/bd/grillaBuscarDocumentoCruce.php',
		        scripts : true,
		        nocache : true,
		        params  :
		        {
		            tipo_documento : tipo_documento
		        }
		    }
		}).show();
	}

 </script>