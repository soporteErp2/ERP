<?php
	include("../../configuracion/conectar.php");
	include("../../configuracion/define_variables.php");

	//$consulE = mysql_query("SELECT id,nombre FROM empresas $filtroE",$link);
?>

<div id="panel_directorio" style="float:left; width:100%">
</div>

<div style="float:left; width:100%">
    <div id="recibidor_directorio" style="float:left; width:100%; height:100%; overflow:auto; background-color:#FFFFFF"></div>
</div>

<script>

	var myalto2  = Ext.getBody().getHeight();
	var myancho2  = Ext.getBody().getWidth();
	document.getElementById('recibidor_directorio').style.height = myalto2 - 155;


	new Ext.Panel
		(
			{
				renderTo	:'panel_directorio',
				frame		:false,
				border		:false,
				tbar		:
				[
					{
						xtype		: 'buttongroup',
						title		: 'Filtros',
						items		:
						[
							{
								xtype		: 'panel',
								border		: false,
								width		: 300,
								height		: 75,
								bodyStyle	: 'background-color:<?php echo $_SESSION['COLOR_FONDO'] ?>;',
								autoLoad	: {
									url		:	'filtro.php',
									scripts	:	true,
									nocache	:	true
								}
							}

						]

					},
					{
						xtype		: 'buttongroup',
						title		: 'Opciones',
						columns		: 3,
						items		:
						[
							{
								xtype		: 'button',
								height		: 75,
								text		: 'Generar Listado<br />de Funcionarios',
								scale		: 'large',
								iconCls		: 'genera_informe',
								iconAlign	: 'top',
								handler 	: function(){BloqBtn(this); generaDirectorio();}
							}/*,
							{
								xtype		: 'button',
								text		: 'Exportar a PDF',
								scale		: 'large',
								iconCls		: 'genera_pdf',
								iconAlign	: 'top',
								handler 	: function(){BloqBtn(this); generaDirectorioPDF();}
							}*/

						]
					}
				]
			}
		);

	function generaDirectorioPDF(){
		var empresa = document.getElementById('filtro_empresa_dir').value;
		var sucursal = document.getElementById('filtro_sucursal_dir').value;
		var nombre_empresa = document.getElementById('filtro_empresa_dir').options[document.getElementById('filtro_empresa_dir').selectedIndex].text;
		var nombre_sucursal = document.getElementById('filtro_sucursal_dir').options[document.getElementById('filtro_sucursal_dir').selectedIndex].text;
		var busca_funcionario = document.getElementById('busca_funcionario').value;

		window.open ("genera_directorio.php?sucursal="+sucursal+"&empresa="+empresa+"&busca_funcionario="+busca_funcionario+"&nombre_sucursal="+nombre_sucursal+"&nombre_empresa="+nombre_empresa+"&pdf=true","PDF");
	}

	function generaDirectorio(){
		var empresa = document.getElementById('filtro_empresa_dir').value;
		var sucursal = document.getElementById('filtro_sucursal_dir').value;
		var nombre_empresa = document.getElementById('filtro_empresa_dir').options[document.getElementById('filtro_empresa_dir').selectedIndex].text;
		var nombre_sucursal = document.getElementById('filtro_sucursal_dir').options[document.getElementById('filtro_sucursal_dir').selectedIndex].text;
		var busca_funcionario = document.getElementById('busca_funcionario').value;

		Ext.get('recibidor_directorio').load(
			{
				url		:	'genera_directorio.php',
				scripts	:	true,
				nocache	:	true,
				params	:
					{
						nombre_empresa		:	nombre_empresa,
						nombre_sucursal		:	nombre_sucursal,
						busca_funcionario	:	busca_funcionario,
						sucursal			:	sucursal
					}
			}
		);
	}

	function CambiaEmpresaDir(){
		var empresa = document.getElementById('filtro_empresa_dir').value;
		Ext.get('recibidor_filtro_empresa_dir').load(
			{
				url		:	'filtros_sucursal.php',
				scripts	:	true,
				nocache	:	true,
				params	:
					{
						empresa	:	empresa
					}
			}
		);
	}
	//creamos una funcion para que cuando se presione enter inicie la busqueda
	function buscaDirectorio(e,input){//VALIDAR NUMEROS
			tecla = (document.all) ? e.keyCode : e.which;
			if (tecla==13){ generaDirectorio();  return; }
		}
</script>