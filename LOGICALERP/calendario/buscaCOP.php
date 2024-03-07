<?php
    include("../../configuracion/conectar.php");
    include("../../configuracion/define_variables.php");

    $id_empresa     = $_SESSION['EMPRESA'];
?>
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
	if(!isset($opcion)){
?>

	<div id="ToolbarTareas">

    	<form action="javascript:BusquedaClientes();" >
            <div class="ic_find_in_page_white_24dp" style="float:left; width:36px; height:36px; margin: 15px 3px 0 6px;"></div>

            <div style="float:left; width:200px; font-size:20px; margin:25px 0 0 0;">
                <input id="FieldBusqueda" type="text" class="MyFieldNew" style="width:310px;" value="" placeholder="Busqueda de Clientes...">
            </div>

            <div style="width:48px; height:48px; float:right; cursor:pointer;" onclick="Win_Busca_COP.close();">
                <div class="ic_highlight_remove_white_24dp" style="float:left; width:36px; height:36px; margin: 0 0 0 6px;"></div>
                <div style="text-align:center">Cerrar</div>
            </div>
        </form>

    </div>

    <div id="ContenidoBusquedas">

    </div>

    <script>
		function BusquedaClientes(){
			busqueda = document.getElementById('FieldBusqueda').value;
			Ext.get('ContenidoBusquedas').load({
			url		: 'buscaCOP.php',
			timeout : 180000,
			scripts	: true,
			nocache	: true,
			params	:
					{
						opcion   : 'consulta',
						busqueda : busqueda
					}
			});
		}

		function BuscaObjetivo(id,cliente){
			Ext.get('ContenedorEmpresa_'+id).load({
			url		: 'buscaCOP.php',
			timeout : 180000,
			scripts	: true,
			nocache	: true,
			params	:
					{
						opcion   : 'consultaObjetivos',
						id 		 : id,
						cliente	 : cliente
					}
			});
		}

		function SelectCliente(id,cliente){
		var confir1 = Ext.MessageBox.confirm('Agregar Seguimiento a '+cliente, 'Esta seguro que quiere agregar este seguimiento a el <b>CLIENTE</b>?<br />si es asi  por favor de click en la tecla <b>\'SI\'</b><br /><br />Si lo que quiere es agregar el seguimiento a un <b>OBJETIVO</b> &oacute; <b>PROYECTO</b> de este cliente<br />por favor de click en la tecla <b>\'NO\'</b>', terminaCliente);
			function terminaCliente(btn){
				if(btn == 'yes'){SeleccionaCliente('cliente',id,'',cliente,'')}
				if(btn == 'no'){BuscaObjetivo(id,cliente);}
			}
		}

		function LimpiarContenedor(id){
			document.getElementById("ContenedorEmpresa_"+id).innerHTML = "";
		}

		function SeleccionaCliente(tipo,id_cliente,id_objetivo,cliente,objetivo){
			document.getElementById('tipo_crm').value = tipo;
			document.getElementById('id_cliente').value = id_cliente;
			document.getElementById('id_objetivo').value = id_objetivo;
			if(tipo == 'cliente'){
				document.getElementById('cliente').value = cliente;
			}
			if(tipo == 'objetivo'){
				document.getElementById('cliente').value = cliente+' / '+objetivo;
			}
			Win_Busca_COP.close();
		}

		document.getElementById('FieldBusqueda').focus();

	</script>

<?php }

	if($opcion == 'consulta'){

		$consul = mysql_query("SELECT * FROM terceros WHERE activo= 1 AND id_empresa='$id_empresa' AND (nombre LIKE '%$busqueda%' OR nombre_comercial LIKE '%$busqueda%' OR  numero_identificacion LIKE '%$busqueda%')", $link);
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

        <div class="ContenedorEmpresa">
        	<div style="width:100%; float:left;">
                <div class="IconoEmpresas"><img src="../../../temas/clasico/images/BotonesTabs/<?php echo $row['tipo']?>.png" width="16" height="16"></div>
                <div class="DatosEmpresas">
                    <span style="font-size:16px"><?php echo $row['nombre_comercial']?></span><br />
                    <?php echo $row['nombre']?><br />
                    <?php echo $row['tipo_identificacion']?> <?php echo $row['numero_identificacion']?>
                </div>
                <div class="OpcionesEmpresas">
                    <div style="margin:0 25px 0 0;" class="IconoEmpresas ic_check_circle_grey600_24dp" onClick="SelectCliente('<?php echo $row['id']?>','<?php echo $row['nombre']?>')"></div>
                    <div class="IconoEmpresas ic_list_grey600_24dp" onClick="BuscaObjetivo('<?php echo $row['id']?>','<?php echo $row['nombre']?>')"></div>
                </div>
            </div>
            <div id="ContenedorEmpresa_<?php echo $row['id']?>">

            </div>
        </div>

<?php 	}
	}

	if($opcion == 'consultaObjetivos'){

		$consul = mysql_query("SELECT * FROM crm_objetivos WHERE id_cliente = $id", $link);
		if(mysql_num_rows($consul)){
?>
            <div class="ContenedorObjetivo" style="width:470px; font-size:16px; padding:2px; margin:3px 0 0 30px; border-top:1px solid #CCC" onClick="LimpiarContenedor('<?php echo $id ?>')">
                <div style="float:left">Objetivos</div>
                <div class="ic_launch_white_16px" style="width:16px; height:16px; float:right; margin:2px 10px 0 0"></div>
            </div>
<?php
		}
		while($row = mysql_fetch_array($consul)){
?>

            <div class="ContenedorObjetivo" onClick="SeleccionaCliente('objetivo','<?php echo $id ?>','<?php echo $row['id'] ?>','<?php echo $cliente?>','<?php echo $row['objetivo']?>')">
                <div style="float:left"><?php echo $row['objetivo']?></div>
                <div class="ic_check_circle_white_16px" style="width:16px; height:16px; float:right" ></div>
            </div>

<?php 	}
	}
?>
