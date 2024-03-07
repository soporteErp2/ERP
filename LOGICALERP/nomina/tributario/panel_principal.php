<?php

 ?>
 <style>
 	.divMensajeInicial{
		width       : 400px;
		margin      : 10% auto;
		font-weight : bold;
		font-size   : 13px;
		color       : #727272;
		font-family: Oswald, sans-serif;
 	}
 </style>
 <div style="width:calc(100% - 5px - 10px);height:calc(100% - 5px);" id="divContenedorPadreTributario">
 	<div style="width:300px;height:100%;float:left;" id="divContenedorIzquierdo">
 	</div>
 	<div style="width: calc(100% - 301px);height: 90%;float: left;margin-top: 3%;" id="divContenedorDerecho">
 		<div class="divMensajeInicial">
 			HAGA CLICK SOBRE ALGUN CARGO PARA CONFIGURARLO
 		</div>

 	</div>
 </div>
 <script>

	Ext.get('divContenedorIzquierdo').load({
		url     : 'tributario/menu_grilla_cargos.php',
		scripts : true,
		nocache : true,
		params  :
		{
			opcGrillaContable : 'nomina_tributario',
			var2 : 'var2',
		}
	});

 </script>