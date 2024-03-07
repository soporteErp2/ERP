<?php

	include("../../../configuracion/conectar.php");
    include("../../../configuracion/define_variables.php");

	$id_sucursal = $_SESSION['SUCURSAL'];
	$id_empresa  = $_SESSION['EMPRESA'];

	$PAC = (user_permisos(199,'false') == 'false')? 'false' : 'true';
    $PEC = (user_permisos(200,'false') == 'false')? 'false' : 'true';
    $PCC = (user_permisos(201,'false') == 'false')? 'false' : 'true';
    $PRC = (user_permisos(202,'false') == 'false')? 'false' : 'true';

    switch ($opc) {
    	case 'ventanaAgregarEditar':
    		ventanaAgregarEditar($id,$id_sucursal,$id_empresa,$mysql);
    		break;

    	case 'generarCierre':
    		generarCierre($fecha_inicio,$fecha_final,$observacion,$id,$id_sucursal,$id_empresa,$mysql);
    		break;
    	case 'editarCierre':
    		editarCierre($id,$id_sucursal,$id_empresa,$mysql);
    		break;

    	case 'eliminaCierre':
    		eliminaCierre($id,$id_sucursal,$id_empresa,$mysql);
    		break;

    	case 'restauraCierre':
    		restauraCierre($id,$id_sucursal,$id_empresa,$mysql);
    		break;
    }

    function ventanaAgregarEditar($id,$id_sucursal,$id_empresa,$mysql){
    	global $PAC,$PEC,$PCC,$PRC;
    	if ($id>0) {
			$sql   = "SELECT fecha_inicio,fecha_final,observacion,estado,consecutivo,usuario FROM cierre_por_periodo WHERE activo=1 AND id=$id AND id_empresa=$id_empresa AND id_sucursal=$id_sucursal";
			$query = $mysql->query($sql,$mysql->link);
			$consecutivo  = $mysql->result($query,0,'consecutivo');
			$fecha_inicio = $mysql->result($query,0,'fecha_inicio');
			$fecha_final  = $mysql->result($query,0,'fecha_final');
			$observacion  = $mysql->result($query,0,'observacion');
			$usuario      = $mysql->result($query,0,'usuario');
			$estado       = $mysql->result($query,0,'estado');

			$tilte_info = 'No. '.$consecutivo;

			// SI ESTA CANCELADO
			if ($estado==3) {
				$acumScript = "	Ext.getCmp('content_btn').hide();
								Ext.getCmp('btn_elimina_cierre').hide();
								Ext.getCmp('btn_restaura_cierre').".( ($PRC=='true')? 'show' : 'hide' )."();
								document.getElementById('observacion').readOnly = true;
								";
				$tilte_info .= '<br><i>(Cancelado)</i>';
			}
			// SI ESTA GENERADO
			else if ($estado==1) {
				$acumScript = "	Ext.getCmp('btn_genera_cierre').hide();
								Ext.getCmp('btn_restaura_cierre').hide();
								Ext.getCmp('btn_edita_cierre').".( ($PEC=='true')? 'show' : 'hide' )."();
								Ext.getCmp('btn_elimina_cierre').".( ($PCC=='true')? 'show' : 'hide' )."();
								document.getElementById('observacion').readOnly = true;";
			}
			else{
				$acumScript = "	Ext.getCmp('btn_genera_cierre').".( ($PAC=='true')? 'show' : 'hide' )."();
								Ext.getCmp('btn_edita_cierre').hide();
								Ext.getCmp('btn_elimina_cierre').hide();
								Ext.getCmp('btn_restaura_cierre').hide();
								";
			}
    	}
    	else{
    		$sql="SELECT nombre1,nombre2,apellido1,apellido2 FROM empleados WHERE activo=1 AND id=".$_SESSION['IDUSUARIO'];
    		$query=$mysql->query($sql,$mysql->link);
    		$usuario = $mysql->result($query,0,'nombre1').' '.$mysql->result($query,0,'nombre2').' '.$mysql->result($query,0,'apellido1').' '.$mysql->result($query,0,'apellido2');
    	}

    	?>
    		<style>

    			.content{
					width       : 100%;
					height      : 100%;
					font-family : "Trebuchet MS", Verdana, Arial, sans-serif, "Lucida Grande";
    			}

    			.title{
					width          : 100%;
					font-size      : 14px;
					text-align     : center;
					padding-top    : 10px;
					padding-bottom : 10px;
					border-top     : 1px solid #99BBE8;
    			}

    			.body{
					width     : 100%;
					height    : calc(100% - 27px);
    			}

    			.table{
    				font-size : 12px;
    			}

    			.table td{
    				padding: 3px 5px 3px 0px;
    			}

    		</style>
    		<div class="content">
				<div class="title">
					<b>CIERRE POR PERIODO <?php echo $tilte_info; ?></b><br>
					<span style="font-size:11px;">Elaborado Por</span>
					<br><?php echo $usuario; ?><br><br>
					<span style="font-size:11px;">Genere un cierre que bloquea los documentos del sistema en un determinado periodo de tiempo, recuerde que puede quitar ese bloqueo editando el cierre.</span>
				</div>
				<div class="body">
					<table class="table" align="center">
						<tr>
							<td>Fecha Inicial</td><td id="td_fecha_inicio"><input type="text" readonly class="myField" id="fecha_inicio" value="<?php echo $fecha_inicio; ?>"></td>
						</tr>
						<tr>
							<td>Fecha Final</td><td id="td_fecha_final"><input type="text" readonly class="myField" id="fecha_final" value="<?php echo $fecha_final; ?>"></td>
						</tr>
						<tr>
							<td>Observaciones</td><td> <textarea class="myField" id="observacion"><?php echo $observacion; ?></textarea> </td>
						</tr>
					</table>
				</div>
				<div id="loadCierre" style="display:none;"></div>
    		</div>

    		<script>
    			<?php echo $acumScript; ?>
    			if ('<?php echo $estado;?>'!='1' && '<?php echo $estado;?>'  !='3') {
	    			new Ext.form.DateField({
	    			    format     : 'Y-m-d',               //FORMATO
	    			    width      : 130,                   //ANCHO
	    			    applyTo    : 'fecha_inicio',
	    			    editable   : false,                 //EDITABLE
	    			});

	    			new Ext.form.DateField({
	    			    format     : 'Y-m-d',               //FORMATO
	    			    width      : 130,                   //ANCHO
	    			    applyTo    : 'fecha_final',
	    			    editable   : false,                 //EDITABLE
	    			});
    			}

    			function generarCierre() {
    				var fecha_inicio = document.getElementById('fecha_inicio').value
    				, 	fecha_final = document.getElementById('fecha_final').value
    				, 	observacion = document.getElementById('observacion').value;

    				observacion = observacion.replace(/[\#\<\>\'\"]/g, '');

    				if (fecha_inicio == '' || fecha_inicio == 0 ||
						fecha_final == ''  || fecha_final == 0 ||
						observacion == ''  || observacion == 0) {
    					alert("Aviso\nTodos los campos son obligatorios!");
    					return;
    				}

    				MyLoading2('on');
    				Ext.get('loadCierre').load({
    					url     : 'cierre_por_periodo/bd.php',
    					scripts : true,
    					nocache : true,
    					params  :
    					{
							opc          : 'generarCierre',
							fecha_inicio : fecha_inicio,
							fecha_final  : fecha_final,
							observacion  : observacion,
							id           : '<?php echo $id; ?>',
    					}
    				});
    			}

    			function editarCierre() {
    				if ( confirm('Aviso!\nSi continua se podran modificar documentos de ese periodo\nRealmente desea levantar el periodo de cierre?') ){
    					MyLoading2('on');
	    				Ext.get('loadCierre').load({
	    					url     : 'cierre_por_periodo/bd.php',
	    					scripts : true,
	    					nocache : true,
	    					params  :
	    					{
								opc : 'editarCierre',
								id  : '<?php echo $id; ?>',
	    					}
	    				});
    				}
    			}

    			function eliminaCierre() {
    				if ( confirm('Aviso!\nSi continua se podran modificar documentos de ese periodo\nRealmente desea levantar el periodo de cierre?') ){

    					MyLoading2('on');
	    				Ext.get('loadCierre').load({
	    					url     : 'cierre_por_periodo/bd.php',
	    					scripts : true,
	    					nocache : true,
	    					params  :
	    					{
								opc : 'eliminaCierre',
								id  : '<?php echo $id; ?>',
	    					}
	    				});

    				}
    			}

    			function restauraCierre() {

					MyLoading2('on');
    				Ext.get('loadCierre').load({
    					url     : 'cierre_por_periodo/bd.php',
    					scripts : true,
    					nocache : true,
    					params  :
    					{
							opc : 'restauraCierre',
							id  : '<?php echo $id; ?>',
    					}
    				});

    			}

    		</script>
    	<?php
    }


    function generarCierre($fecha_inicio,$fecha_final,$observacion,$id,$id_sucursal,$id_empresa,$mysql){
    	global $PEC;
    	if ($id>0){
			$sql   = "UPDATE cierre_por_periodo SET fecha_inicio='$fecha_inicio',fecha_final='$fecha_final',observacion='$observacion',estado=1
							WHERE activo=1 AND id=$id AND id_empresa=$id_empresa AND id_sucursal=$id_sucursal";
			$query = $mysql->query($sql,$mysql->link);
			if ($query) {
				echo '<script>
						Actualiza_Div_cierre_por_periodo('.$id.');
						MyLoading2("off");
					</script>';
			}
			else{
				echo '<script>
						MyLoading2("off",{icono:"fail",texto:"Error no se inserto el cierre",duracion:3000});
					</script>';
			}
		}
		else{
			// VALIDAR OTROS CIERRES EN ESE PERIODO
			$sql="SELECT COUNT(id) AS cont FROM cierre_por_periodo WHERE activo=1 AND id_empresa=$id_empresa AND fecha_inicio>='$fecha_inicio' AND fecha_final<='$fecha_final' ";
			$query=$mysql->query($sql,$mysql->link);
			$cierres = mysql_result($query,0,'cont');

			if ($cierres>=1) {
				echo '<script>
						alert("Aviso\nExisten cierres creados en este perido de tiempo, solo es valido un cierre por rango de tiempo");
						MyLoading2("off",{icono:"fail",texto:"Existe otro cierre en ese perido",duracion:3000});
					</script>';
				exit;
			}

			// CONSULTAR LA INFORMACION DEL USUARIO
			$sql="SELECT nombre1,nombre2,apellido1,apellido2,documento FROM empleados WHERE activo=1 AND id=".$_SESSION['IDUSUARIO'];
    		$query=$mysql->query($sql,$mysql->link);
    		$documento = $mysql->result($query,0,'documento');
    		$usuario = $mysql->result($query,0,'nombre1').' '.$mysql->result($query,0,'nombre2').' '.$mysql->result($query,0,'apellido1').' '.$mysql->result($query,0,'apellido2');

			// INSERTAR EL CIERRE
			$sql="INSERT INTO cierre_por_periodo (fecha_inicio,fecha_final,observacion,estado,id_sucursal,id_empresa,id_usuario,documento_usuario,usuario)
					VALUES ('$fecha_inicio','$fecha_final','$observacion',1,'$id_sucursal','$id_empresa',$_SESSION[IDUSUARIO],'$documento','$usuario')";
			$query=$mysql->query($sql,$mysql->link);
			$id = mysql_insert_id();

			if ($query) {
				echo '<script>
						Inserta_Div_cierre_por_periodo('.$id.');
						MyLoading2("off");
					</script>';
			}
			else{
				echo '<script>
						MyLoading2("off",{icono:"fail",texto:"Error no se inserto el cierre",duracion:3000});
					</script>';
			}
		}



		echo '<script>
				Ext.getCmp("btn_genera_cierre").hide();
				Ext.getCmp("btn_edita_cierre").'.( ($PEC=='true')? 'show' : 'hide' ).'();
				Ext.getCmp("btn_restaura_cierre").hide();
				document.getElementById("td_fecha_inicio").innerHTML = \'<input type=\"text\" readonly class=\"myField\" id=\"fecha_inicio\" value=\"'.$fecha_inicio.'\">\';
				document.getElementById("td_fecha_final").innerHTML  = \'<input type=\"text\" readonly class=\"myField\" id=\"fecha_final\" value=\"'.$fecha_final.'\">\';
				document.getElementById("observacion").readOnly      = true;
			</script>
			';
    }

    function editarCierre($id,$id_sucursal,$id_empresa,$mysql){
		global $PAC,$PEC,$PCC,$PRC;
    	$sql   = "UPDATE cierre_por_periodo SET estado=0 WHERE activo=1 AND id=$id AND id_empresa=$id_empresa AND id_sucursal=$id_sucursal";
		$query = $mysql->query($sql,$mysql->link);
		if ($query) {
			echo "<script>
					document.getElementById('observacion').readOnly=false;
					new Ext.form.DateField({
	    			    format     : 'Y-m-d',
	    			    width      : 130,
	    			    applyTo    : 'fecha_inicio',
	    			    editable   : false,
	    			});

	    			new Ext.form.DateField({
	    			    format     : 'Y-m-d',
	    			    width      : 130,
	    			    applyTo    : 'fecha_final',
	    			    editable   : false,
	    			});

					Ext.getCmp('btn_genera_cierre').".( ($PEC=='true')? 'show' : 'hide' )."();
					Ext.getCmp('btn_edita_cierre').hide();
					Ext.getCmp('btn_elimina_cierre').hide();

					MyLoading2('off');
				</script>";
		}
		else{
			echo "<script>
					MyLoading2('off');
					Ext.getCmp('btn_genera_cierre').hide();
					Ext.getCmp('btn_restaura_cierre').hide();
					Ext.getCmp('btn_edita_cierre').".( ($PEC=='true')? 'show' : 'hide' )."();
					Ext.getCmp('btn_elimina_cierre').".( ($PCC=='true')? 'show' : 'hide' )."();
				</script>";
		}
    }

    function eliminaCierre($id,$id_sucursal,$id_empresa,$mysql){

    	// CONSULTAR SI TIENE CONSECUTIVO
    	$sql="SELECT consecutivo FROM cierre_por_periodo WHERE activo=1 AND id=$id";
    	$query=$mysql->query($sql,$mysql->link);
    	$consecutivo = mysql_result($query,0,'consecutivo');

    	if ($consecutivo>0) {
    		$sql   = "UPDATE cierre_por_periodo SET estado=3 WHERE activo=1 AND id=$id AND id_empresa=$id_empresa AND id_sucursal=$id_sucursal";
			$query = $mysql->query($sql,$mysql->link);
    	}
    	else{
    		$sql   = "UPDATE cierre_por_periodo SET activo=0 WHERE activo=1 AND id=$id AND id_empresa=$id_empresa AND id_sucursal=$id_sucursal";
			$query = $mysql->query($sql,$mysql->link);
    	}

    	echo "<script>
    				Actualiza_Div_cierre_por_periodo(".$id.");
					Win_Ventana_AgregarEditar.close();
					MyLoading2('off');
				</script>";
    }

    function restauraCierre($id,$id_sucursal,$id_empresa,$mysql){
		$sql   = "UPDATE cierre_por_periodo SET estado=0 WHERE activo=1 AND id=$id AND id_empresa=$id_empresa AND id_sucursal=$id_sucursal";
		$query = $mysql->query($sql,$mysql->link);

    	echo "<script>
    				Actualiza_Div_cierre_por_periodo(".$id.");
					Win_Ventana_AgregarEditar.close();
					MyLoading2('off');
				</script>";
    }

 ?>