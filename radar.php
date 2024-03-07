<?php
	include('configuracion/conectar.php');
	include('configuracion/define_variables.php');

	$HORA = date("Y-m-d H:i:s");

	if(!isset($_SESSION["IDUSUARIO"]))//VERIFICA SI EL USUARIO ESTÁ LOGUEADO, SINO ESTA DEVUELVE login
	{
		echo 'logged_out';
		/*DEVUELVE logged_out, LA FUNCION radar() EN escritorio.php, VARIFICA EL VALOR DEVUELTO Y LO REDIRECCION AL FORMULARIO DE LOGIN*/
	}else{

		//CONSULTA QUE VERIFICA SI HAY ALGUNA ALERTA ACTIVA///////////////////////////////////
		$SQL1 = "SELECT CN.id
				  FROM calendario_notificaciones AS CN
				  INNER JOIN calendario AS C ON (C.id = CN.id_calendario)
				  INNER JOIN crm_objetivos_actividades AS CRM ON (C.id_actividad_crm = CRM.id)
				  WHERE CN.id_empleado = '$_SESSION[IDUSUARIO]'
				  AND CN.descartar = 'false'
				  AND (
						  if(CN.pospuesto = 'false',
						  	 CN.fecha_hora <= '$HORA',
						  	 CN.fecha_pospuesto <= '$HORA'
						  )
					  )
				  AND CRM.estado = 0";

		//CONSULTA QUE MUESTRA TODAS LAS ALERTAS/////////////////////////////////////////////////
		$SQL2 = "SELECT
		                CN.id,
						CN.hora,
					    CN.fecha_hora,
					    CN.fecha_pospuesto,
					    CN.pospuesto,
					    CN.time,
					    CN.time_type,
					    C.icono,
					    C.fechai,
					    C.horai,
					    C.fechaf,
					    C.horaf,
					    C.tema,
					    C.tipo,
					    C.descripcion,
					    C.id AS id_calendario,
					    CRM.usuario
				  FROM calendario_notificaciones AS CN
				  INNER JOIN calendario AS C ON (C.id = CN.id_calendario)
				  INNER JOIN crm_objetivos_actividades AS CRM ON (C.id_actividad_crm = CRM.id)
				  WHERE CN.id_empleado = '$_SESSION[IDUSUARIO]'
				  AND CN.descartar = 'false'
				  AND CRM.estado = 0";

		//CONSULTA QUE VERIFICA SI HAY ALGUNA ALERTA ACTIVA///////////////////////////////////
		/*$SQL1 ="SELECT
					*
				FROM
					seguimientos
				WHERE
					seguimientos.id_asignado = $_SESSION[IDUSUARIO]
					AND seguimientos.tipo_seguimiento != 4
					AND seguimientos.descartar = 'false'
					AND estado = 0
					AND (
							if(seguimientos.pospuesto = 'false',
								seguimientos.fecha_recordatorio <= '$HORA',
								seguimientos.fecha_pospuesto <= '$HORA'
							)
						)
				";*/
		//echo $SQL1;
		/////////////////////////////////////////////////////////////////////////////////////////

		//CONSULTA QUE MUESTRA TODAS LAS ALERTAS/////////////////////////////////////////////////
		/*$SQL1 ="SELECT
					*,
					if(seguimientos.pospuesto = 'false', fecha_recordatorio, fecha_pospuesto ) as FeOrden
				FROM
					seguimientos
				WHERE
					seguimientos.id_asignado = $_SESSION[IDUSUARIO]
					AND seguimientos.tipo_seguimiento != 4
					AND seguimientos.descartar = 'false'
					AND estado = 0
				ORDER BY FeOrden

				";*/
		//echo $SQL1;
		/////////////////////////////////////////////////////////////////////////////////////////

		if($consulta == 'true'){//PARAMETRO QUE INDICA QUE ES LA CONSULTA PARA VERIFICAR SI HAY PENDIENTES
			$consul = mysql_query($SQL1,$link);
			if(mysql_num_rows($consul)){
				echo 'true';
			}else{
				echo 'false';
			}
		}

		if($consulta == 'manual'){//PARAMETRO QUE INDICA QUE ES LA CONSULTA PARA VERIFICAR SI HAY PENDIENTES
			$consul = mysql_query($SQL2,$link);
			if(mysql_num_rows($consul)){
				echo 'true';
			}else{
				echo 'false';
			}
		}

		if(!isset($consulta)){

			$imagen = array('','tareas','llamadas','citas','visita comercial de seguimiento','correo electronico','atencion PQR','visita PQR','gestion de cartera','visita gestion de cartera');

			echo'	<div style="width:100%; height:65px;">';
			echo'    	<div style="float:left; margin:10px">';
			echo'    		<img src="temas/clasico/images/iconos/acercade44.png">';
			echo'        </div>';
			echo'        <div style="float:left; margin:20px 0 0 10px; font-size:18px">';
			echo'        	Tiene las Siguientes Tareas Pendientes';
			echo'        </div>';
			echo'    </div>';


			echo '<div style="margin:5px; width:98%; height:240px; overflow:auto; overflow-x:hidden; border:1px solid #999999; background-color:#FFFFFF ">';

			echo'	<div title="sdhjshdjs" style="float:left; width:100%; height:25px; font-size:11px; font-weight:bold; color:#15428b; background-image:url(LOGICALERP/crm/images/alertas/fondo_cabecera.png);">';
			echo'		<div style="float:left; margin:6px 0 0 0; width:260px">&nbsp;&nbsp;Tema</div>';
			echo'		<div style="float:left; margin:6px 0 0 0; width:270px">Vencimiento / Recordatorio</div>';
			echo'		<div style="float:left; margin:6px 0 0 0; width:170px">Programado por. /Opciones</div>';
			echo' 	</div>';

			$consul = mysql_query($SQL2,$link);
			while($row = mysql_fetch_array($consul)){
				/*if($row['tipo_referencia']==1){
					$consulTR = mysql_query("SELECT pedido,npedido,cliente,evento,estado FROM pedido WHERE id = '$row[referencia]'",$link);
					//echo "SELECT pedido,npedido,cliente,evento,estado FROM pedido WHERE id = $row[referencia]";
					$pedido   = mysql_result($consulTR,0,'pedido');
					$npedido  = mysql_result($consulTR,0,'npedido');
					$estado   = mysql_result($consulTR,0,'estado');
					$cliente  = mysql_result($consulTR,0,'cliente');
					$evento   = mysql_result($consulTR,0,'evento');
				}*/

?>

			<div id="capa_segui_<?php echo $row['id']?>" onclick="ventana_actividad('<?php echo $row[id_calendario] ?>','<?php echo $row[fechai] ?>')" style="float:left; cursor:pointer; width:100%; margin:2px 0 2px 0; border-bottom: 1px solid #CCCCCC;  <?php if($row['FeOrden'] <= $HORA){echo 'background-image:url(LOGICALERP/crm/images/alertas/info.png?v3); background-color:#FFCCCC; background-repeat:no-repeat;';} ?> ">
				<div style="float:left; margin:2px 0 0 5px; width:22px"><img src="LOGICALERP/calendario/images/<?php echo 't'.$row['icono'].'B.png'; ?>" alt="<?php echo $imagen[$row['tipo']] ?>"/></div>
				<div style="float:left; margin:2px 0 0 0; width:233px">
					<span style="font-weight:bold">
						<?php echo $row['tema']; ?><br />
					</span>
					<span style="font-size:10px">
					<?php
						echo $row['descripcion'];
					?>
					</span>
				</div>
				<div style="float:left; margin:2px 0 0 0; width:270px">
					<div style="float:left; width:18px; height:16px">
						<img src="LOGICALERP/crm/images/alertas/calendario_16.png" />
					</div>
					<div style="float:left; width:250px; height:16px">
						<?php echo fecha_larga_hora_m($row['fechaf'].' '.$row['horaf']).'<br />';?>
					</div>
					<div style="float:left; width:18px; height:16px">
						<img src="LOGICALERP/crm/images/alertas/reloj_16.png" />
					</div>
					<div style="float:left; width:250px; height:16px">
						<?php
							if($row['pospuesto'] == 'false'){
								echo fecha_larga_hora_m($row['fecha_hora']);
							}else{
								echo fecha_larga_hora_m($row['fecha_pospuesto']);
							}
						?>
					</div>
				</div>
				<div style="float:left; margin:0 0 0 0; width:170px">
					<div style="float:left; margin:0 0 0 0; width:170px">
						<div style="float:left; width:18px; height:16px">
							<img src="LOGICALERP/crm/images/alertas/usuario.png" />
						</div>
						<div style="float:left; width:150px; height:16px">
							<?php echo $row['usuario']; ?></div>
						</div>
					<div style="float:left; margin:2px 0 0 0; width:170px">
						<div id="btn_posp_<?php echo $row['id']?>" style="float:left; width:70px; height:20px; margin:0 0 2px 0; background-image:url(temas/clasico/images/formularios/boton_posponer.png); cursor:pointer" onClick="posponer_alerta('<?php echo $row['id']?>')">
							<div style="margin:2px 0 0 20px;">Posponer</div>
						</div>
						<div id="btn_canc_<?php echo $row['id']?>" style="float:left; width:70px; height:20px; margin:0 0 2px 5px; background-image:url(temas/clasico/images/formularios/boton_cancelar.png); cursor:pointer" onClick="cancelar_alerta('<?php echo $row['id']?>')">
							<div style="margin:2px 0 0 20px">Cancelar</div>
						</div>
					</div>
				</div>
			</div>

<?php
			}
			echo '</div>';
			echo '<div style="float:right;  width:80px;  margin:5px 15px 5px 5px;"><input name="" type="button" onClick="cerrar_ventana_alertas()" value="      Cerrar      "></div>';
			echo '<div style="float:right;  width:330px;  margin:10px;">';
			if($check_no_mas != 'false'){
				echo '	  <div style="float:left"><input id="check_no_mas"name="check_no_mas" type="checkbox" value=""></div>';
				echo '	  <div style="float:left">&nbsp;&nbsp;No volver a abrir hasta la proxima vez que ingrese al sistema</div>';
			}else{
				echo '	  <div style="float:left; visibility:hidden"><input id="check_no_mas"name="check_no_mas" type="checkbox" value=""></div>';
			}
			echo '</div>';

?>
			<script>
				function posponer_alerta(id){
					win_posponer_fecha = new Ext.Window
					(
						{
							id			: 'win_posponer_fecha',
							width		: 200,
							height		: 150,
							title		: 'Posponer Alerta',
							modal		: true,
							autoDestroy : true,
							autoLoad	:
							{
								url		:'LOGICALERP/crm/acciones/actualizar_fecha_alerta.php?id_registro='+id,
								scripts	:true,
								nocache	:true
							}
						}
					).show();
					//document.getElementById('capa_segui_'+id).style.color='#999999';
	
				}
				function cancelar_alerta(id){
	
					function termina_cancelar_alerta(btn){
						if(btn == 'yes'){
							Ext.Ajax.request({
								url: 'LOGICALERP/crm/acciones/actualiza_desde_radar.php',
								success	: function (result, request)
										  {
											document.getElementById('capa_segui_'+id).style.color='#999999';
											document.getElementById('btn_posp_'+id).onclick = "";
											document.getElementById('btn_canc_'+id).onclick = "";
										  },
								failure	: function()
										  {
											alert('Error actualizando el estado de la alerta. por favor intente de nuevo!');
										  },
								params	: {
											opcion  : 'cancela_alerta',
											id		: id
										  }
							});
						}
					}
	
					Ext.MessageBox.buttonText.yes = "SI";
					Ext.MessageBox.buttonText.no  = "NO";
					Ext.Msg.show({   width : 350, title:'Cancelar Alerta',
									 msg: '<center><br />Esta seguro que desea cancelar esta alerta?<br/><br /></center>',
									 buttons: Ext.Msg.YESNO, fn: termina_cancelar_alerta, icon: Ext.MessageBox.WARNING
					});
	
				}
	
				function cerrar_ventana_alertas(){
					<?php if(isset($check_no_mas)){?>
							if(radar_habilitado == 'true'){
								setTimeout('radar()',30000);
							}
					<?php }else{ ?>
							if(document.getElementById('check_no_mas').checked == true){
								//alert('habilitado');
								radar_habilitado = 'false';
							}else{
								//alert('no habilitado');
								setTimeout('radar()',30000);
							}
					<?php } ?>
					win_pendientes.close();
				}
	
				function ventana_actividad(id,fecha){
					var myalto  = Ext.getBody().getHeight();
					var myancho  = Ext.getBody().getWidth();
			
					Win_Agrega_Registro = new Ext.Window({
						id			: 'Win_Agrega_Registro',
						width		: 625,//400,
						height		: myalto-100, //290,
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
							url		:'LOGICALERP/calendario/actividades/editaRegistro.php',
							scripts	:true,
							nocache	:true,
							params	:
									{
										id	  :	id,
										fecha : fecha
									}
						}
					}).show();
					Ext.getCmp('Win_Agrega_Registro').center();
				}

				function recarga(){

    				//...
    			}			

			</script>
        <?php
		}
	}
?>