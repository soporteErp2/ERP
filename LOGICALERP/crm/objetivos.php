<?php
    include("../../configuracion/conectar.php");
    include("../../configuracion/define_variables.php");

    $consul2 = mysql_query("SELECT clasificacion,tipo,nombre,nombre_comercial FROM terceros WHERE id = $id_cliente ",$link);

	$NomC = mysql_result($consul2,0,'nombre_comercial');
    $Non = mysql_result($consul2,0,'nombre');
    if($NomC == $Non){
		$LabelCli = '<b>'.$NomC.'</b>';
	}else{
		$LabelCli = '<b>'.$NomC.'</b><br /><span style=font-size:11px>'.$Non.'</span>';
	}
?>
<style>
	.InfoCliente{
		background: -moz-linear-gradient(top, <?php echo $_SESSION['COLOR_FONDO'] ?> 0%, <?php echo $_SESSION['COLOR_CONTRASTE'] ?> 100%);
		background: -webkit-gradient(left top, left bottom, color-stop(0%, <?php echo $_SESSION['COLOR_FONDO'] ?>), color-stop(100%, <?php echo $_SESSION['COLOR_CONTRASTE'] ?>));
		background: -webkit-linear-gradient(top, <?php echo $_SESSION['COLOR_FONDO'] ?> 0%, <?php echo $_SESSION['COLOR_CONTRASTE'] ?> 100%);
		background: -o-linear-gradient(top, <?php echo $_SESSION['COLOR_FONDO'] ?> 0%, <?php echo $_SESSION['COLOR_CONTRASTE'] ?> 100%);
		background: -ms-linear-gradient(top, <?php echo $_SESSION['COLOR_FONDO'] ?> 0%, <?php echo $_SESSION['COLOR_CONTRASTE'] ?> 100%);
	}

	/******  ESTILOS DE LOS FORMULARIOS  tareas_agregar.php, llamnadas_agregar.php, citas_agregar.php  *****/
	.ActividadesReglon{
		float: left;
		width: 420px;
		margin: 10px 0 0 10px;
	}
	.Actividadesfield{
		float: left;
		width: 100px;
	}
	.ActividadesControl{
		float: left;
		width: 300px;
	}
	/*******************************************************************************************************/
</style>

<div id="ToolbarTareas" style="width:100%; height:60px; padding: 10px 0 0 10px; overflow:hidden; ">

    <div style="width:48px; height:48px; float:right; margin:0 20px 0 0; cursor:pointer;" onclick="Win_Ventana_CRMObjetivos.close();">
        <div class="ic_highlight_remove_white_24dp" style="float:left; width:36px; height:36px; margin: 0 0 0 6px;"></div>
        <div style="text-align:center">Cerrar</div>
    </div>

    <div style="float:left; width:50px; margin:0 5px 0 0; border-right:1px solid <?php echo $_SESSION['COLOR_LINEA'] ?>">
		<img id="imgTipoTerceroX" src="../crm/images/<?php echo mysql_result($consul2,0,'tipo') ?>.png?v1">
	</div>

	<!--<div style="float:left; width:50px; margin:0 10px 0 0 ; border-right:1px solid <?php echo $_SESSION['COLOR_LINEA'] ?>">
		<img src="../crm/images/<?php echo mysql_result($consul2,0,'clasificacion') ?>_44.png">
	</div>	-->

	<div style="float:left; width:400px">
		<div style="float:left; width:100%; ">
			<div style="float:left; width:350px; font-size:14px;"><?php echo $LabelCli ?></div>
		</div>
	</div>


</div>

<div id="panelObjetivos"></div>

<script>

	var imgTipo = document.getElementById('imgTipoTerceroX');

	if(imgTipo.getAttribute('src') == "../crm/images/.png?v1"){
		imgTipo.setAttribute("src","../crm/images/Prospecto.png?v1");
	}

	//alert(imgTipo);

	var TabsObjetivos = new Ext.TabPanel(
		{

			renderTo	: 'panelObjetivos',
			//id          : 'DivDelTabMaestroAActividades',
			activeTab	: 0,
			height		: myalto - 130,
			bodyStyle 	: 'background-color:<?php echo $_SESSION['COLOR_FONDO'] ?>;',
			border		: false,
			items		:
				[
					//OBJETIVOS O PROYECTOS////////////////////////////////////////////////////////////////////////////////////////////////////
					{
						closable	: false,
						autoScroll	: false,
						border		: false,
						//height		: myalto - 300,
						title		: 'Proyectos',
						bodyStyle 	: 'background-color:<?php echo $_SESSION['COLOR_FONDO']?>;',
						iconCls 	: 'proyecto16',
						autoLoad	:
						{
							url		: '../crm/objetivos_grilla.php',
							scripts	: true,
							nocache	: true,
							params	:
								{
									id_cliente  : 	'<?php echo $id_cliente ?>'
								}
						}

					},

					//ACTIVIDADES ////////////////////////////////////////////////////////////////////////////////////////////////////
					{
						closable	: false,
						autoScroll	: false,
						border		: false,
						//height		: myalto - 185,
						title		: 'Actividades',
						bodyStyle 	: 'background-color:<?php echo $_SESSION['COLOR_FONDO'] ?>;',
						iconCls 	: 'actividad16',
						autoLoad	:
						{
							url		: '../crm/actividades_grilla.php',
							scripts	: true,
							nocache	: true,
							params	:
								{
									id_cliente  : 	'<?php echo $id_cliente ?>'
								}
						}
					}
				]
		}
	);



	//////////////////////////////////////////////////
	//												//
	//          FUNCIONES DE OBJETIVOS	        	//
	//												//
	//////////////////////////////////////////////////

	function FinalizaObjetivo(elid){

		 	Win_FinalizaObjetivo = new Ext.Window({
				id			: 'Win_FinalizaObjetivo',
				width		: 450,
				height		: 200,
				title		: 'Finalizar Objetivo' ,
				iconCls 	: '1',
				modal		: true,
				autoScroll	: false,
				closable	: true,
				autoDestroy : true,
				autoLoad	:
				{
					url		:'../crm/objetivos_finalizar.php',
					scripts	:true,
					nocache	:true,
					params	:
							{
								id  		: 	elid
							}
				}
			}).show();
	}




	//////////////////////////////////////////////////
	//												//
	//          FUNCIONES DE ACTIVIDADES        	//
	//												//
	//////////////////////////////////////////////////

	function FinalizaActividad(elid,NombreGrillaActiva){

		Win_FinalizaActividad = new Ext.Window({
			id			: 'Win_FinalizaActividad',
			width		: 450,
			height		: 250,
			//title		: 'FinalizarActividad' ,
			//iconCls 	: '1',
			modal		: true,
			border		: false,
			autoScroll	: false,
			closable	: false,
			autoDestroy : true,
			autoLoad	:
			{
				url		:'../crm/actividades/actividades_finalizar.php',
				scripts	:true,
				nocache	:true,
				params	:
						{
							id_actividad  		: 	elid,
							NombreGrillaActiva 	: 	NombreGrillaActiva
						}
			}
		}).show();
	}


	function Agregar_ActividadesTareas(objetivo){

		var myalto  = Ext.getBody().getHeight();
		var myancho  = Ext.getBody().getWidth();

		Win_Agrega_Registro = new Ext.Window({
			id			: 'Win_Agrega_Registro',
			width		: 625,//400,
			height		: myalto-20, //290,
			plain		: false,
			border		: false,
			//title		: 'Nuevo Registro',
			//iconCls 	: 'add16',
			modal		: true,
			autoScroll	: false,
			closable	: false,
			autoDestroy : true,
			bodyStyle	: "background-color:#FFF",

			autoLoad	:
			{
				url		:'../crm/actividades/nuevoRegistro.php',
				scripts	:true,
				nocache	:true,
				params	:
						{
							id_objetivo  : 	objetivo,
							id_cliente  : 	'<?php echo $id_cliente ?>'
						}
			}
		}).show();
		Ext.getCmp('Win_Agrega_Registro').center();
	}

	function Editar_ActividadesTareas(id){

		var myalto  = Ext.getBody().getHeight();
		var myancho  = Ext.getBody().getWidth();

		Win_Agrega_Registro = new Ext.Window({
			id			: 'Win_Agrega_Registro',
			width		: 625,//400,
			height		: myalto-20, //290,
			plain		: false,
			border		: false,
			//title		: 'Nuevo Registro',
			//iconCls 	: 'add16',
			modal		: true,
			autoScroll	: false,
			closable	: false,
			autoDestroy : true,
			bodyStyle	: "background-color:#FFF",

			autoLoad	:
			{
				url		:'../crm/actividades/editaRegistro.php',
				scripts	:true,
				nocache	:true,
				params	:
						{
							id	:	id
						}
			}
		}).show();
		Ext.getCmp('Win_Agrega_Registro').center();
	}


</script>