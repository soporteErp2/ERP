<?php

	include("../../../../configuracion/conectar.php");
	include("../../../../configuracion/define_variables.php");
	//include("bd/functions_bd.php");
	include("js.php");


	$InstanciaName = 'editorMail';		// nombre de la instacia editor CKEDITOR

    /*
    	ESTOS CONDICIONALES CONTROLARAN EL PROCESO DE ENVIO DE DOCUMENTOS VIA EMAIL
	    HAY TRES DE ESTOS CONDICIONALES DE CONTROL:
	    EL DE ESTE ARCHIVO CONTROLA LAS DIRECCIONES DE EMAIL DEL CLIENTE Y LA CARPETA DONDE QUEDAN LOS ADJUNTOS
	    EL CONTROL DE LA GENERACION DEL DOCUMENTO PDF: imprimirGrillaCOntable.php
	    El CONTROL DEL ENVIO DEL CORREO: documento_mail.php
    */		

?>
	<div style="float:left; width:100%; height:65px; border-bottom:1px solid <?php echo $_SESSION['COLOR_LINEA']?>; background-color:<?php echo $_SESSION['COLOR_CONTRASTE']?>" >
		<div style="float:left; margin:1px 0 0 2px;width:128px; height:60px; background-image:url(../../temas/clasico/images/formularios/boton_enviar_email.png)">
			<div style="float:left; width:60px; height:42px; margin:15px 0 0 2px; cursor:pointer" <?php if(isset($error) == ""){?>onclick="TERMINA_ENVIAR_MAIL('<?php echo $CUAL ?>','<?php echo $id ?>');" <?php } ?> onmouseover="this.style.backgroundImage='url(../../temas/clasico/images/formularios/fondo_boton.png?v1.4)'" onmouseout="this.style.backgroundImage=''"></div>
			<!-- <div style="float:left; width:60px; height:42px; margin:15px 0 0 2px; "></div> -->
		</div>
	</div>

		<?php
			if(isset($error) != ""){
		?>
		<div style="float:left; padding:5px 0 0 10px; margin:1px 0 0 5px; width:350px; height:60px; border:1px solid #FF9999; background-color:#FFCCCC; overflow:auto">
			<div style="float:left"><img src="../../temas/clasico/images/formularios/delete.png" /></div>
			<div style="float:left; margin:0 0 0 10px;">
				<span style="font-weight:bold">Error :</span><br />
				<?php echo $error ?>
			</div>
		</div>
		<?php
			}
		?>

		<div style="float:right; width:500px; height:0px; overflow:hidden;">
			<iframe frameborder="0" allowtransparency name="frame_mail" id="frame_mail" style="width:500px; height:65px; overflow-x:hidden"></iframe>
		</div>
	</div>

	<form action="" enctype="multipart/form-data" id="form_mail" name="form_mail" method="post" target="frame_mail" accept-charset="UTF-8">

		<div style="margin:0px 0 0 5px; width:300px; float:left" >
			<fieldset  style="width:290px; height:50px; padding:0 0 5px 2px; margin:10px 0 0 0; border:1px solid #006699;display:none">
				<legend align="left" style="font-weight:bold">Tipo de Documento</legend>
				<div style="width:255px; margin:10px 0 5px 12px; font-size:11px; text-align:justify ">
				   <select style=" width:250px;" class="myfield" name="id_mail" id="id_mail" onChange="setMailTexto();" >
						<?php
							//optionCuerpoMail($CUAL,$id_mail,$_SESSION['EMPRESA'],$_SESSION['SUCURSAL']);
						?>
					</select>
				</div>
			</fieldset>

			<fieldset  style="width:290px; height:236px; padding:0 0 0 0; margin:10px 0 0 0;  overflow:hidden;  border:1px solid #006699">
				<legend align="left" style="font-weight:bold">&nbsp;&nbsp;Destinatarios&nbsp;&nbsp;</legend>
				<div style="float:left; width:280px; height:236px; margin:0 0 0 10px; overflow:auto; overflow-x:hidden">
					<?php

						$varInputsCheck="";
						$varIdContactos="";
						$count   =0;
						$count_1 =0;						

						$SqlEmail="	SELECT 	nombre_comercial,nombre,email FROM terceros
									WHERE 	id=$id_tercero
									  AND activo=1
									LIMIT 0,1";

						$QueryEmail       =  mysql_query($SqlEmail,$link);
						$nombre_proveedor =  mysql_result($QueryEmail,0,"nombre");

						if($nombre_proveedor == ''){
							$nombre_proveedor =  mysql_result($QueryEmail,0,"nombre_comercial");
						}		
											
						$email = mysql_result($QueryEmail,0,"email");

						$SqlFicha="	SELECT 	nombre_contacto_cartera AS nombre_cartera,
											email_contacto_cartera AS email_cartera
									FROM  	terceros_ficha_tecnica
									WHERE 	id_tercero=$id_tercero
									  AND   activo=1
									LIMIT 0,1";

						$QuerySqlFicha=mysql_query($SqlFicha,$link);

						$nombre_cartera = mysql_result($QuerySqlFicha,0,"nombre_cartera");
						$email_cartera  = mysql_result($QuerySqlFicha,0,"email_cartera");
						$email_tercero  = mysql_result($QuerySqlFicha,0,"email_tercero");

						/* Query global q carga los input check, las cabeceras y hace check por default */
						$SqlCheck="	SELECT 	c.nombre AS nombre_contacto,
											c.id AS id_contacto,
											e.email AS email,
											c.ContactoAuto AS isPropio
									FROM  	terceros_contactos AS c,
											terceros_contactos_email AS e
									WHERE 	c.id_tercero=$id_tercero
										AND c.id=e.id_contacto
										AND e.activo=1
										AND c.activo=1
									ORDER BY c.ContactoAuto DESC";

						$QuerySqlCheck=mysql_query($SqlCheck,$link);

						$SqlCheck2="SELECT 	TE.email
									FROM  	terceros_emails AS TE
									INNER JOIN terceros AS T ON (TE.id_tercero = T.id AND T.id = $id_tercero)	
									WHERE TE.activo = 1
									ORDER BY TE.email ASC";

						$QuerySqlCheck2=mysql_query($SqlCheck2,$link);

						if($email_cartera ==''){
							$style = 'display:none';
						}

						/* Cabezera con el nombre del cliente */
						$varInputsCheck.='			<div style="margin:12px 0 0 0;width:280px; float:left;">
														<div style="width:20px; float:left; "><img src="../../temas/clasico/images/busqueda/house.png" /></div>
														<div style="width:240px; float:left;  font-weight:bold">'.$nombre_proveedor.'</div>
													</div>';

						$varInputsCheck.='		    <div style="float:left; margin:2px 0 0 20px; width:100%">
														<div style="float:left"><input name="env_email_cartera" type="checkbox" id="env_email_cartera" value="'.$email.'" checked disabled /></div>
														<div style="margin-left:5px; float:left">'.$email.'</div>
													</div>';

						while($row2 = mysql_fetch_array($QuerySqlCheck2)){
							$varInputsCheck.='		<div style="float:left; margin:2px 0 0 20px; width:100%">
														<div style="float:left"><input name="env_email_princ_'.$count_1.'" type="checkbox" id="env_email_princ_'.$count_1.'" value="'.$row2['email'].'" checked /></div>
														<div style="margin-left:5px; float:left">'.$row2['email'].'</div>
													</div>';
							$count_1++;
						}

						$varInputsCheck.='	        <div style="margin:12px 0 0 0;width:280px; float:left;'.$style.'">
														<div style="width:20px; float:left; "><img src="../../temas/clasico/images/busqueda/vcard.png" /></div>
														<div style="width:240px; float:left;  font-weight:bold">'.$nombre_cartera.'</div>
													</div>';

						$varInputsCheck.='		    <div style="float:left; margin:2px 0 0 20px; width:100%;'.$style.'">
														<div style="float:left"><input name="env_email_cartera" type="checkbox" id="env_email_cartera" value="'.$email_cartera.'" checked /></div>
														<div style="margin-left:5px; float:left;">'.$email_cartera.'</div>
													</div>';



						while($row = mysql_fetch_array($QuerySqlCheck)){
							/* condicional input checked cuando el e-mail sea el primero del cliente */
							if($count==0 && $row['isPropio']==1){ $valueChecked='checked="checked"'; }

							/* condicional q carga la cabezera con el nombre del contacto si no pertenece al cliente */
							if($varIdContactos!=$row['id_contacto'] && $row['isPropio']!=1){
								$varIdContactos=$row['id_contacto'];
								$varInputsCheck.='	<div style="margin:12px 0 0 0;width:280px; float:left;">
														<div style="width:20px; float:left; "><img src="../../temas/clasico/images/busqueda/vcard.png" /></div>
														<div style="width:240px; float:left;  font-weight:bold">'.$row['nombre_contacto'].'</div>
													</div>';
							}
							/* carga los input type checklist */
							$varInputsCheck.='		<div style="float:left; margin:2px 0 0 20px; width:100%">
														<div style="float:left"><input name="env_email'.$count.'" type="checkbox" id="env_email'.$count.'" value="'.$row['email'].'"  checked/></div>
														<div style="margin-left:5px; float:left">'.$row['email'].'</div>
													</div>';
							$count++;

						}
						
						echo $varInputsCheck;
						//echo $SqlCheck;
					?>
				</div>
			</fieldset>

			<fieldset  style="width:290px; height:160px; padding:0 0 5px 2px; margin:10px 0 0 0;; border:1px solid #006699">
				<legend align="left" style="font-weight:bold">&nbsp;&nbsp;Otros Destinatarios (C.C.:)&nbsp;&nbsp;</legend>
				<div style="width:255px; margin:10px 0 5px 12px; font-size:11px; text-align:justify ">
					Si desea enviar este e-mail a uno o varios contactos que no se encuentren en el listado de "Destinatarios", escribalos en la siguiente casilla separados por coma(,) o punto y coma(;)
				</div>
				<textarea name="emails_cc" cols="" rows="2" class="inputs" style="width:260px; margin:10px 0 0 10px" id="emails_cc"></textarea>

			</fieldset>
		</div>

		<div style="margin:10px 0 0 5px; width:625px; float:left">
			<!--<fieldset  style="width:610px; height:215px; padding:0 0 5px 2px;">
			<legend align="left" style="font-weight:bold">Mensaje</legend>-->
				<div id="div_htmleditor_enca" style="width:100%; margin:5px 0 0 0">
					<?php
						include("../../../../misc/ckeditor/ckeditor_php5.php");
						$CKEditor = new CKEditor();
						$config['height']	= 150;
						$config['width']	= 615;
						$config['toolbar'] = array(
													array('Format','Font','FontSize','Bold','Italic','Underline','Strike','Outdent','Indent','Blockquote'),
													'/',
													array('Cut','Copy','Paste','PasteText','Undo','Redo','TextColor','BGColor','SelectAll','RemoveFormat',
														  'Maximize', 'ShowBlocks','HorizontalRule','JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock')
												);

						$CKEditor->editor($InstanciaName,"</br>&nbsp;", $config);
					?>
				</div>
			<!--</fieldset>-->

			<div style="margin:10px 0 0 0; width:312px; float:left">

				<fieldset  style="width:297px; height:165px; padding:0 0 0 2px; overflow:auto; overflow-x:hidden; border:1px solid #006699">
				<legend align="left" style="font-weight:bold">&nbsp;&nbsp;Archivos Adjuntos Predeterminados&nbsp;&nbsp;</legend>
					<div style="width:280px; height:150px; margin:0px 0 0 10px; overflow:auto; overflow-x:hidden">
					</div>
				</fieldset>
			</div>

			<div style="margin:10px 0 0 0; width:312px; float:left">
				<fieldset  style="width:290px; height:165px; padding:0 0 0 10px; overflow:auto; overflow-x:hidden;; border:1px solid #006699">
				<legend align="left" style="font-weight:bold">&nbsp;&nbsp;Archivos Adjuntos Adicionales&nbsp;&nbsp;</legend>
					<div style="width:290px; height:150px; overflow:auto; overflow-x:hidden;">
						<iframe src="../terceros/terceros/envio_mail/eje_upload_archivo.php?id=0&dir=<?php echo $dir_adjunto ?>" height="30" width="270" frameborder="0" scrolling="no"></iframe>
						<iframe src="../terceros/terceros/envio_mail/eje_upload_archivo.php?id=1&dir=<?php echo $dir_adjunto ?>" height="30" width="280" frameborder="0" scrolling="no"></iframe>
						<iframe src="../terceros/terceros/envio_mail/eje_upload_archivo.php?id=2&dir=<?php echo $dir_adjunto ?>" height="30" width="290" frameborder="0" scrolling="no"></iframe>
						<iframe src="../terceros/terceros/envio_mail/eje_upload_archivo.php?id=3&dir=<?php echo $dir_adjunto ?>" height="30" width="260" frameborder="0" scrolling="no"></iframe>
						<iframe src="../terceros/terceros/envio_mail/eje_upload_archivo.php?id=4&dir=<?php echo $dir_adjunto ?>" height="30" width="260" frameborder="0" scrolling="no"></iframe>
						<iframe src="../terceros/terceros/envio_mail/eje_upload_archivo.php?id=5&dir=<?php echo $dir_adjunto ?>" height="30" width="260" frameborder="0" scrolling="no"></iframe>
						<iframe src="../terceros/terceros/envio_mail/eje_upload_archivo.php?id=6&dir=<?php echo $dir_adjunto ?>" height="30" width="260" frameborder="0" scrolling="no"></iframe>
					</div>
				</fieldset>
			</div>
		</div>
	</form>

	<input name="InstanciaName" type="hidden" id="InstanciaName" value="<?php echo $InstanciaName ?>" />
	<input name="count_env_mails" type="hidden" id="count_env_mails" value="<?php echo $count ?>" />
	<input name="env_email_princ" type="hidden" id="env_email_princ" value="<?php echo $count_1 ?>" />
    <input name="count_env_adjun" type="hidden" id="count_env_adjun" value="<?php echo $count2 ?>" />
    <input name="recibe_idioma_email" type="hidden" id="recibe_idioma_email" value="<?php echo $idioma ?>" />
	<input name="finaliza_email" type="hidden" id="finaliza_email" value="false" />
	<input name="listado_email" type="hidden" id="listado_email" value="" />
	<input name="adjuntos2" type="hidden" id="adjuntos2" value="" />

<script>
	ventana_email.on('close',function(w){
		editor = CKEDITOR.instances['<?php echo $InstanciaName ?>'];
		editor.destroy();
	});
	/*
	function cerrarBodydocumento(){
		editor = CKEDITOR.instances['<?php echo $InstanciaName ?>'];
		editor.destroy();
	}
	*/
	
</script>