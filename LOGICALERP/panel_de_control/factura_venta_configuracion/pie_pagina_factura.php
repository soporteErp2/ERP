<?php
	include("../../../configuracion/conectar.php");
	include("../../../configuracion/define_variables.php");

	$sql ="SELECT cabecera_pie_pagina,contenido_pie_pagina FROM ventas_facturas_configuracion_impresion WHERE activo=1 AND id_empresa=".$_SESSION['EMPRESA']." LIMIT 0,1";

	$query         = mysql_query($sql,$link);

	$cabecera_pie_pagina  = mysql_result($query,0,'cabecera_pie_pagina');
	$contenido_pie_pagina = mysql_result($query,0,'contenido_pie_pagina');

?>
<div id="barraBotones"></div>
<div style="margin:10px;">
	<p>Acontinuacion aparece como se mostrara el pie de pagina en la factura de venta, configurelo como lo necesite <br><br></p>
	<div style="float:left;font-size:12px; font-weight:normal;width:100%; height: 60%; margin-bottom:20px; margin-left:2px;border:1px solid;background-color: #FFF;">
		<!-- margin: arriba,  -->
			<div style="width:94%;height:13%;margin: 5px 0px 0px 15px; border:1px solid;" >
				<textarea id="cabeceraPiePagina" style="width:100%;height:100%;background-color: #CDCDCD;border:none;" placeholder="Escriba aqui la cabecera del pie de pagina..."><?php echo $cabecera_pie_pagina; ?></textarea>
			</div>
			<div style="width:98%;height:77%;margin: 5px 0px 0px 3px; border:1px solid;">
				<textarea id="contenidoPiePagina" style="width:100%;height:100%;background-color: #FFF;" placeholder="Escriba aqui el contenido del pie de pagina..."><?php echo $contenido_pie_pagina; ?></textarea>
			</div>

		<div style="float:left;width: 17px;margin-top: 50px;" id="cargarPiePagina"></div>
	</div>
</div>

<script>

	//barra de botones de la ventana
	var tb = new Ext.Toolbar();
	tb.render('barraBotones');
	tb.add(
		{
			xtype: 'buttongroup',
			columns: 2,
			items: [
				{
					text		: 'Guardar',
					scale		: 'large',
					width       : 80,
					height 		: 60,
					iconCls		: 'guardar',
					iconAlign	: 'top',
					handler		: function(){guardarPiePagina();}
				},
				{
					text		: 'Regresar',
					scale		: 'large',
					width       : 80,
					height 		: 60,
					iconCls		: 'regresar',
					iconAlign	: 'top',
					handler		: function(){Win_Panel_Global.close();}
				}
			]
		}

	);
	tb.doLayout();

	function guardarPiePagina(){
		cabecera_pie_pagina=document.getElementById('cabeceraPiePagina');
		contenido_pie_pagina=document.getElementById('contenidoPiePagina');

		if (cabecera_pie_pagina.value=='') { 	alert("Digite la cabecera del pie de pagina!"); cabecera_pie_pagina.focus(); return;}
		else if (contenido_pie_pagina.value=='') { alert("Digite el contenido del pie de pagina!"); contenido_pie_pagina.focus(); return;}


		Ext.get('cargarPiePagina').load({
			url     : 'factura_venta_configuracion/bd/bd.php',
			timeout : 180000,
			scripts : true,
			nocache : true,
			params  :
			{
				op                   : 'guardarPiePagina',
				cabecera_pie_pagina  :cabecera_pie_pagina.value,
				contenido_pie_pagina :contenido_pie_pagina.value,
			}

		});
	}


</script>