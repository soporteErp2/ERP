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
		text-align: center;
 	}
 </style>
 <div style="width:calc(100% - 5px - 10px);height:calc(100% - 5px);" id="divContenedorPadreConceptosEmpleados">
 	<div style="width:300px;height:100%;float:left;" id="divContenedorIzquierdoConceptosEmpleados">
 	</div>
 	<div style="width: calc(100% - 301px);height: 90%;float: left;margin-top: 3%;" id="divContenedorDerechoConceptosEmpleados">
 		<div class="divMensajeInicial">
 			HAGA CLICK SOBRE ALGUN EMPLEADO PARA CONFIGURAR LOS CONCEPTOS PERSONALES
 		</div>
 	</div>
 </div>
 <script>

	Ext.get('divContenedorIzquierdoConceptosEmpleados').load({
		url     : 'conceptos_empleados/menu_grilla_cargos.php',
		scripts : true,
		nocache : true,
		params  :
		{
			opcGrillaContable : 'nomina_conceptos_empleados',
		}
	});

 </script>