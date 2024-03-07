<?php
    include("../../configuracion/conectar.php");
    include("../../configuracion/define_variables.php");
?>

<div id='toolbarBuscaFuncionarios'></div>
<div id='panelBuscaFuncionarios'></div>

<script>
	var ToolbarTareas = new Ext.Toolbar(
		{
			renderTo	: 'toolbarBuscaFuncionarios',
			items: [
				{
					xtype	: 'buttongroup',
					columns	: 3,
					title	: 'Busqueda',
					items	:
					[
						{
							xtype     : 'panel',
							border    : false,
							width     : 240,
							height    : 56,
							bodyStyle : 'background-color:<?php echo $_SESSION['COLOR_FONDO'] ?>;',
							html      : '<div style="margin:18 0 0 15"><input class="myfieldBusqueda" name="busqueda_empleado" type="text" id="busqueda_empleado" style="width:210px" onKeyUp="ValEnterBusqEmpleados(event)" onFocus="this.value=\'\'" /></div>',
						}
					]
				},
				{
					xtype   : 'buttongroup',
					columns	: 3,
					title	: 'Filtros',
					items	:
					[
						{
							xtype		: 'panel',
							border		: false,
							width		: 260,
							height		: 56,
							bodyStyle 	: 'background-color:<?php echo $_SESSION['COLOR_FONDO'] ?>;',
							autoLoad    :
							{
								url     : '../funcionarios/filtros_sucursal.php',
								scripts : true,
								nocache : true
							}
						}
					]
				}
			]
		}
	);

	function BusquedaEmpleados(){//FUNCION DE BUSQUEDA DE EMPLEADOS
		var filtro          = document.getElementById('busqueda_empleado').value;
		var filtro_sucursal = document.getElementById('filtro_sucursal').value;

		Ext.get('panelBuscaFuncionarios').load(
			{
				url		: '../funcionarios/empleados.php',
				scripts	: true,
				nocache	: true,
				params	:
				{
					filtro          : filtro,
					filtro_sucursal : filtro_sucursal
				}
			}
		);
	}

	function ValEnterBusqEmpleados(e) {//FUNCIONES QUE VALIDAN EL ENTER E INVOCA LA BUSQUEDA DE EMPLEADOS
		tecla = (document.all)?e.keyCode:e.which;
		if (tecla==13){BusquedaEmpleados();} ;
		return true;
	}

	function Editar_Empleados(id){
		Ext.Ajax.request(
			{
				url		: '../funcionarios/obtiene_funcionario.php',
				params	: {	id 	: 	id},
				success	: function (result, request){
					if('<?php echo $filtro_informe ?>' == 'true'){
						//ENTRA AQUI SOLO SI VIENE DE LA CLASE MYINFORME CUANDO SE HABILITA EL FILTRO DE FUNCIONARIOS
						document.getElementById('<?php echo $id_filtro ?>').value = '('+id+')'+' '+result.responseText;
					}
					else{
						document.getElementById('<?php echo $id_field ?>').value = id;
						document.getElementById('<?php echo $field ?>').focus();
						document.getElementById('<?php echo $field ?>').value = result.responseText;
						document.getElementById('<?php echo $field ?>').blur();
					}
				}
			}
		);
		Win_BuscarFuncionario.close();
	}

</script>