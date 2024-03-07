	<style>
		#ToolbarTareas{
			font-family		:   RobotoDraft, 'Helvetica Neue', Helvetica, Arial;
			width			:	calc(100% - 40px);
			height			:	48px;
			background-color:	rgba(<?php echo $_SESSION["COLOR_MD_CALENDARIO"] ?>,1);
			margin			:	0 0 0 0;
			padding			:	20px;
			color			:	#FFF;

		}
		.TituloGrupo{
			font-size		: 	18px;
			font-weight		:	normal;
			font-family		:	RobotoDraft, 'Helvetica Neue', Helvetica, Arial;
			color 			: 	#333;
			padding			:	10px 0 5px 0 ;
			margin-top		:	10px;
		}
		.MyFieldNew{
			border			: 	0px;
			border-bottom	:	1px solid #FFF;
			background-color:	rgba(<?php echo $_SESSION["COLOR_MD_CALENDARIO"] ?>,1);
			color			:	#FFF;
			font-family		:   RobotoDraft, 'Helvetica Neue', Helvetica, Arial;
			font-size		:	18px;

		}
		#ContenidoBusquedas{
			width			:	calc(100% - 20px);
			height			:	calc(100% - 110px);
			padding			:	10px;
			overflow		:	hidden;
			overflow-y		: 	auto;


		}
		.ContenedorEmpresa{
			/*font-family		:   RobotoDraft, 'Helvetica Neue', Helvetica, Arial;*/
			width			:	calc(100% - 20px);
			padding			:	5px 10px 10px 10px;
			border-bottom	:	1px solid #CCC;
			float			:	left;
			cursor 			: 	pointer;
		}
		.IconoEmpresas{
			width			:	25px;
			float			:	left;
			height			:	40px;
		}
		.DatosEmpresas{
			width			:	400px;
			float			:	left;
		}
		.OpcionesEmpresas{
			width			:	100px;
			float			:	left;
		}
		.IconoEmpresas{
			width			:	36px;
			height			:	36px;
			loat			:	left;
			cursor			:	pointer;
		}
		.ContenedorObjetivo{
			/*font-family		:   RobotoDraft, 'Helvetica Neue', Helvetica, Arial;*/
			width			:	calc(100% - 150px);
			padding			:	5px 10px 5px 10px;
			margin			: 	0 70px 0 60px;
			/*border-bottom	:	1px solid #CCC;*/
			float			:	left;
		}

		.ContenedorObjetivo:hover {
			background-color 	:	#DDD;
			cursor				:	pointer;
		}
	</style>

<?php
	if(!isset($_POST['opcion2'])){
?>

	<div id="ToolbarTareas">

    	<form action="javascript:BusquedaClientes();" >
            <div class="ic_find_in_page_white_24dp" style="float:left; width:36px; height:36px; margin: 15px 3px 0 6px;"></div>

            <div style="float:left; width:200px; font-size:20px; margin:25px 0 0 0;">
                <input id="FieldBusqueda" type="text" class="MyFieldNew" style="width:310px;" value="" placeholder="Busqueda de Clientes...">
            </div>

            <div style="width:48px; height:48px; float:right; cursor:pointer;" onclick="CierraVentanBusqueda()">
                <div class="ic_highlight_remove_white_24dp" style="float:left; width:36px; height:36px; margin: 0 0 0 6px;"></div>
                <div style="text-align:center">Cerrar</div>
            </div>
        </form>

    </div>

    <div id="ContenidoBusquedas">

    </div>

    <script>
		function CierraVentanBusqueda(){
			Win_Busca_COP.close();
			Ext.getCmp('MyInformeFiltro_Clientes_<?php echo $this->InformeName ?>').setValue("");
		}

		function BusquedaClientes(){
			busqueda = document.getElementById('FieldBusqueda').value;
			Ext.get('ContenidoBusquedas').load({
			url		: '<?php echo $_SERVER['SCRIPT_NAME'] ?>',
			timeout : 180000,
			scripts	: true,
			nocache	: true,
			params	:
					{
						opcion2   	: 'consulta',
						MyFiltro	: 'true',
						opcion		: 'BusquedaClientes',
						busqueda 	: busqueda,
						prospectos  : '<?php echo $_POST["prospectos"]; ?>'
					}
			});
		}

		function SelectCliente(id,cliente){
			Ext.getCmp('MyInformeFiltro_Clientes_<?php echo $this->InformeName ?>').setValue('('+id+') '+cliente);
			Win_Busca_COP.close();
		}

		document.getElementById('FieldBusqueda').focus();

	</script>

<?php }

	if($_POST['opcion2'] == 'consulta'){

		$busqueda = $_POST['busqueda'];

		$filtro = 'AND tercero = 1';
		if($_POST['prospectos'] == 'true'){//PARA LOS INFORMES DEL CRM INCLUYE PROSPECTOS
			$filtro = '';
		}

		$consul = mysql_query("SELECT * FROM terceros WHERE id_empresa='$_SESSION[EMPRESA]' AND activo= 1 $filtro AND (nombre LIKE '%$busqueda%' OR nombre_comercial LIKE '%$busqueda%' OR  numero_identificacion LIKE '%$busqueda%')");
		while($row = mysql_fetch_array($consul)){

			if($row['nombre'] == ''){
				$row['nombre'] = $row['nombre_comercial'];//EN CASO DE SER PROSPECTO
			}

			if($row['tipo'] == ''){
				$row['tipo'] = 'Prospecto.png?v1';
			}

			if($row['tipo_identificacion'] == ''){
				$row['tipo_identificacion'] = 'Prospecto';
			}
?>
        	<div class="ContenedorEmpresa" onClick="SelectCliente('<?php echo $row['id'] ?>','<?php echo $row['nombre'] ?>')">
        		<div style="width:100%; float:left;">
        	        <div class="IconoEmpresas"><img src="../../../temas/clasico/images/BotonesTabs/<?php echo $row['tipo']?>.png" width="16" height="16"></div>
        	        <div class="DatosEmpresas">
        	            <span style="font-size:16px"><?php echo $row['nombre_comercial']?></span><br />
        	            <?php echo $row['nombre']?><br />
        	            <?php echo $row['tipo_identificacion']?> <?php echo $row['numero_identificacion']?>
        	        </div>
        	    </div>
        	</div>

<?php 	}
	}

?>

