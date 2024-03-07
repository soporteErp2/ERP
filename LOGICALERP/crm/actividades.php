<?php
    include("../../configuracion/conectar.php");
    include("../../configuracion/define_variables.php");

    if(isset($opcion_objetivo) && $opcion_objetivo =='documento'){
   		 $consul1 = mysql_query("SELECT id FROM crm_objetivos WHERE tipo = 1 AND referencia = '$id_intercambio'",$link);
   		 if(mysql_num_rows($consul1)){
   		 	$id_objetivo = mysql_result($consul1,0,'id');
   		 }else{
   		 	mysql_query("INSERT INTO crm_objetivos (tipo,referencia,id_usuario) VALUES (1,'$id_intercambio',$_SESSION[IDUSUARIO])",$link);
   		 	$id_objetivo = mysql_insert_id();
   		 }
   		 $id_cliente = mysql_result(mysql_query("SELECT id_cliente FROM crm_objetivos WHERE id = $id_objetivo",$link),0,'id_cliente');
    }

    if(isset($opcion_objetivo) && $opcion_objetivo =='personalizado'){
 		$id_objetivo = $id;
 		$id_cliente = mysql_result(mysql_query("SELECT id_cliente FROM crm_objetivos WHERE id = $id_objetivo",$link),0,'id_cliente');

    }

    $consul2 = mysql_query("SELECT clasificacion,tipo FROM terceros WHERE id = $id_cliente ",$link);
    $consul3 = mysql_query("SELECT * FROM crm_objetivos WHERE id = $id_objetivo ",$link);
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



    <div style="float:left; width:50px; margin:0 5px 0 0; border-right:1px solid <?php echo $_SESSION['COLOR_LINEA'] ?>">
        <img id="imgTipoTerceroActX" src="../crm/images/<?php echo mysql_result($consul2,0,'tipo') ?>.png">
    </div>

    <!--<div style="float:left; width:50px; margin:0 10px 0 0 ; border-right:1px solid <?php echo $_SESSION['COLOR_LINEA'] ?>">
        <img src="../crm/images/<?php echo mysql_result($consul2,0,'clasificacion') ?>_44.png">
    </div>	-->

    <div style="float:left; width:350px">
        <div style="float:left; width:100%; ">
            <div style="float:left; width:350px; font-size:14px; font-weight:bold"><?php echo mysql_result($consul3,0,'cliente') ?></div>
        </div>
        <div style="float:left; width:100%; margin:2px 0 0 0">
            <!--<div style="float:left; width:150px"><b>Objetivo &oacute; Proyecto </b></div>-->
            <div style="float:left; width:350px; font-size:14px;">Proyecto: <?php echo mysql_result($consul3,0,'objetivo') ?></div>
        </div>
    </div>

    <div style="width:48px; height:48px; float:right; cursor:pointer; margin: 0 10px 0 0" onclick="Win_Ventana_CRM.close();">
        <div class="ic_highlight_remove_white_24dp" style="float:left; width:36px; height:36px; margin: 0 0 0 6px;"></div>
        <div style="text-align:center">Cerrar</div>
    </div>


</div>

<div id="panel_actividades"></div>

<script>

	var imgTipo = document.getElementById('imgTipoTerceroActX');

	if(imgTipo.getAttribute('src') == "../crm/images/.png"){
		imgTipo.setAttribute("src","../crm/images/Prospecto.png");
	}

	var TabsActividades = new Ext.Panel(
			{
				renderTo	: 'panel_actividades',
				id          : 'DivDelTabMaestroAActividades',
				closable	: false,
				autoScroll	: false,
				border		: false,
				//height		: myalto - 300,
				//title		: 'Tareas',
				bodyStyle 	: 'background-color:<?php echo $_SESSION['COLOR_FONDO']?>;',
				iconCls 	: 'tareas16',
				autoLoad	:
				{
					url		: '../crm/actividades_grilla2.php',
					scripts	: true,
					nocache	: true,
					params	:
					{
						id_objetivo	: 	'<?php echo $id_objetivo ?>',
						id_cliente	:	'<?php echo $id_cliente ?>'
					}
				}


			}
		);


	/*function FinalizaActividad(elid,NombreGrillaActiva){

		 	Win_FinalizaActividad = new Ext.Window({
				id			: 'Win_FinalizaActividad',
				width		: 450,
				height		: 200,
				title		: 'FinalizarActividad' ,
				iconCls 	: '1',
				modal		: true,
				autoScroll	: true,
				closable	: true,
				autoDestroy : true,
				autoLoad	:
				{
					url		:'../crm/actividades_finalizar.php',
					scripts	:true,
					nocache	:true,
					params	:
							{
								id_actividad  		: 	elid,
								NombreGrillaActiva 	: 	NombreGrillaActiva
							}
				}
			}).show();
	}*/

</script>