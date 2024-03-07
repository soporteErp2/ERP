<?php
	include("../../configuracion/conectar.php");
	include("../../configuracion/define_variables.php");

  //Nombre de la instancia editor CKEDITOR
	$InstanciaName = 'editorMail';
?>
<!-- Botones Principales -->
<div style="float:left; width:100%; height:65px; border-bottom:1px solid <?php echo $_SESSION['COLOR_LINEA']?>; background-color:<?php echo $_SESSION['COLOR_CONTRASTE']?>" >
	<div style="float:left; margin:1px 0 0 2px;width:128px; height:60px; background-image:url(../../temas/clasico/images/formularios/boton_enviar_email.png)">
		<div style="float:left; width:60px; height:42px; margin:15px 0 0 2px; cursor:pointer" <?php if(isset($error) == ""){?>onclick=TERMINA_ENVIAR_MAIL('<?php echo $id_cliente; ?>','<?php echo $nombre_informe; ?>','<?php echo $url_result; ?>');<?php } ?> onmouseover="this.style.backgroundImage='url(../../temas/clasico/images/formularios/fondo_boton.png?v1.4)'" onmouseout="this.style.backgroundImage=''"></div>
	</div>
</div>
<!-- Iframe para ocultar el fondo -->
<div style="float:right; width:500px; height:0px; overflow:hidden;">
	<iframe frameborder="0" allowtransparency name="frame_mail" id="frame_mail" style="width:500px; height:65px; overflow-x:hidden"></iframe>
</div>
<!-- Formulario principal -->
<form action="" enctype="multipart/form-data" id="form_mail" name="form_mail" method="post" target="frame_mail" accept-charset="UTF-8">

  <!-- Lado izquierdo de la ventana -->
  <div style="margin:0px 0 0 5px; width:300px; float:left">

		<fieldset  style="width:290px; height:50px; padding:0 0 5px 2px; margin:10px 0 0 0; border:1px solid #006699;display:none">
			<legend align="left" style="font-weight:bold">Tipo de Documento</legend>
			<div style="width:255px; margin:10px 0 5px 12px; font-size:11px; text-align:justify ">
			  <select style=" width:250px;" class="myfield" name="id_mail" id="id_mail" onChange="setMailTexto();" >
				</select>
			</div>
		</fieldset>

		<fieldset  style="width:290px; height:236px; padding:0 0 0 0; margin:10px 0 0 0;  overflow:hidden;  border:1px solid #006699">
			<legend align="left" style="font-weight:bold">&nbsp;&nbsp;Destinatarios&nbsp;&nbsp;</legend>
			<div style="float:left; width:280px; height:236px; margin:0 0 0 10px; overflow:auto; overflow-x:hidden">
				<?php

					$varInputsCheck = "";
					$varIdContactos = "";
					$count          = 0;

					$SqlEmail =  "SELECT email
                        FROM terceros
      									WHERE id = $id_cliente
      									AND activo = 1
      									LIMIT 0,1";
					$QueryEmail = mysql_query($SqlEmail,$link);
					$email      = mysql_result($QueryEmail,0,"email");

					$SqlFicha =  "SELECT nombre_contacto_cartera,email_contacto_cartera
      									FROM terceros_ficha_tecnica
      									WHERE id_tercero = $id_cliente
      									AND activo = 1
      									LIMIT 0,1";
					$QuerySqlFicha  = mysql_query($SqlFicha,$link);
					$nombre_cartera = mysql_result($QuerySqlFicha,0,"nombre_contacto_cartera");
					$email_cartera  = mysql_result($QuerySqlFicha,0,"email_contacto_cartera");

					if($email_cartera ==''){
						$style = 'display:none';
					}

					$varInputsCheck .= '<div style="margin:12px 0 0 0;width:280px; float:left;">
    														<div style="width:20px; float:left; "><img src="../../temas/clasico/images/busqueda/house.png" /></div>
    														<div style="width:240px; float:left;  font-weight:bold">' . $nombre_cliente . '</div>
    													</div>
					                    <div style="float:left; margin:2px 0 0 20px; width:100%">
    														<div style="float:left"><input name="env_email_cartera" type="checkbox" id="env_email_cartera" value="' . $email_cartera . '" checked /></div>
    														<div style="margin-left:5px; float:left">' . $email . '</div>
    													</div>
                              <div style="margin:12px 0 0 0;width:280px; float:left;' . $style . '">
    														<div style="width:20px; float:left; "><img src="../../temas/clasico/images/busqueda/vcard.png" /></div>
    														<div style="width:240px; float:left;  font-weight:bold">' . $nombre_cartera . '</div>
    													</div>
                              <div style="float:left; margin:2px 0 0 20px; width:100%;' . $style . '">
    														<div style="float:left"><input name="env_email_cartera" type="checkbox" id="env_email_cartera" value="' . $email_cartera . '" checked /></div>
    														<div style="margin-left:5px; float:left;">' . $email_cartera . '</div>
    													</div>';

          $SqlCheck =  "SELECT c.nombre AS nombre_contacto,c.id AS id_contacto,e.email AS email,c.ContactoAuto AS isPropio
								        FROM terceros_contactos AS c,terceros_contactos_email AS e
								        WHERE c.id_tercero=$id_cliente
    										AND c.id = e.id_contacto
    										AND e.activo = 1
    										AND c.activo = 1
    									  ORDER BY c.ContactoAuto DESC";
					$QuerySqlCheck = mysql_query($SqlCheck,$link);

					while($row = mysql_fetch_array($QuerySqlCheck)){
						//Condicional input checked cuando el e-mail sea el primero del cliente
						if($count == 0 && $row['isPropio'] == 1){
              $valueChecked='checked="checked"';
            }

						//Condicional que carga la cabezera con el nombre del contacto si no pertenece al cliente
						if($varIdContactos != $row['id_contacto'] && $row['isPropio'] != 1){
							$varIdContactos = $row['id_contacto'];
							$varInputsCheck .= '<div style="margin:12px 0 0 0;width:280px; float:left;">
        														<div style="width:20px; float:left; "><img src="../../temas/clasico/images/busqueda/vcard.png" /></div>
        														<div style="width:240px; float:left;  font-weight:bold">' . $row['nombre_contacto'] . '</div>
        													</div>';
						}

						//Carga los input type checklist
						$varInputsCheck .= '<div style="float:left; margin:2px 0 0 20px; width:100%">
      														<div style="float:left"><input name="env_email' . $count . '" type="checkbox" id="env_email' . $count . '" value="' . $row['email'] . '"  /></div>
      														<div style="margin-left:5px; float:left">' . $row['email'] . '</div>
      													</div>';
						$count ++;

					}
					echo $varInputsCheck;
				?>
			</div>
		</fieldset>

		<fieldset  style="width:290px; height:160px; padding:0 0 5px 2px; margin:10px 0 0 0; border:1px solid #006699">
			<legend align="left" style="font-weight:bold">&nbsp;&nbsp;Otros Destinatarios (C.C.:)&nbsp;&nbsp;</legend>
			<div style="width:255px; margin:10px 0 5px 12px; font-size:11px; text-align:justify ">
				Si desea enviar este e-mail a uno o varios contactos que no se encuentren en el listado de "Destinatarios", escribalos en la siguiente casilla separados por coma(,) o punto y coma(;)
			</div>
			<textarea name="emails_cc" cols="" rows="2" class="inputs" style="width:260px; margin:10px 0 0 10px" id="emails_cc"></textarea>

		</fieldset>
	</div>

  <!-- Lado derecho de la ventana -->
	<div style="margin:10px 0 0 5px; width:425px; float:left">
		<div id="div_htmleditor_enca" style="width:100%; margin:5px 0 0 0">
			<?php
				include("../../misc/ckeditor/ckeditor_php5.php");
				$CKEditor = new CKEditor();
				$config['height']	 = 150;
				$config['width']	 = 580;
				$config['toolbar'] = array(
            												array('Format','Font','FontSize','Bold','Italic','Underline','Strike','Outdent','Indent','Blockquote'),
            												'/',
            												array('Cut','Copy','Paste','PasteText','Undo','Redo','TextColor','BGColor','SelectAll','RemoveFormat','Maximize', 'ShowBlocks','HorizontalRule','JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock')
            											);

				$CKEditor->editor($InstanciaName,"</br>&nbsp;", $config);
			?>
		</div>

		<div style="margin:10px 0 0 0; width:312px; float:left">
			<fieldset  style="width:290px; height:165px; padding:0 0 0 10px; overflow:auto; overflow-x:hidden; border:1px solid #006699">
			<legend align="left" style="font-weight:bold">&nbsp;&nbsp;Archivos Adjuntos Adicionales&nbsp;&nbsp;</legend>
				<div style="width:290px; height:150px; overflow:auto; overflow-x:hidden;">
					<iframe src="bd/eje_upload_archivo.php?id=0&dir=<?php echo $dir_adjunto ?>" height="30" width="270" frameborder="0" scrolling="no"></iframe>
					<iframe src="bd/eje_upload_archivo.php?id=1&dir=<?php echo $dir_adjunto ?>" height="30" width="280" frameborder="0" scrolling="no"></iframe>
					<iframe src="bd/eje_upload_archivo.php?id=2&dir=<?php echo $dir_adjunto ?>" height="30" width="290" frameborder="0" scrolling="no"></iframe>
					<iframe src="bd/eje_upload_archivo.php?id=3&dir=<?php echo $dir_adjunto ?>" height="30" width="260" frameborder="0" scrolling="no"></iframe>
					<iframe src="bd/eje_upload_archivo.php?id=4&dir=<?php echo $dir_adjunto ?>" height="30" width="260" frameborder="0" scrolling="no"></iframe>
					<iframe src="bd/eje_upload_archivo.php?id=5&dir=<?php echo $dir_adjunto ?>" height="30" width="260" frameborder="0" scrolling="no"></iframe>
					<iframe src="bd/eje_upload_archivo.php?id=6&dir=<?php echo $dir_adjunto ?>" height="30" width="260" frameborder="0" scrolling="no"></iframe>
				</div>
			</fieldset>
		</div>
	</div>

</form>
<input name="InstanciaName" type="hidden" id="InstanciaName" value="<?php echo $InstanciaName ?>" />
<input name="count_env_mails" type="hidden" id="count_env_mails" value="<?php echo $count ?>" />
<input name="count_env_adjun" type="hidden" id="count_env_adjun" value="<?php echo $count2 ?>" />
<input name="recibe_idioma_email" type="hidden" id="recibe_idioma_email" value="<?php echo $idioma ?>" />
<input name="finaliza_email" type="hidden" id="finaliza_email" value="false" />
<input name="listado_email" type="hidden" id="listado_email" value="" />
<input name="adjuntos2" type="hidden" id="adjuntos2" value="" />
<script>
	Win_Ventana_enviar_informe.on('close',function(w){
		editor = CKEDITOR.instances['<?php echo $InstanciaName ?>'];
		editor.destroy();
	});

  setMailTexto();

	function setMailTexto(){
		id_mail	= document.getElementById("id_mail").value;
		editor  = CKEDITOR.instances['<?php echo $InstanciaName ?>'];

		if(id_mail != ""){
			Ext.Ajax.request(
				{
					url     : 'bd/bd.php',
					params  : {
											op      : "cargaTextoDocumentoMail",
											id_mail : id_mail
									  },
					success	: function(result,request){
											var resultado   =  result.responseText.split("{.}");
											var respuesta   = resultado[0];
											var observacion = resultado[1];

											if(respuesta == 'false'){
												alert(observacion);
											}
											else if(respuesta == 'true'){
												editor.setData( observacion );
											}
									  },
					failure : function(){
											alert('Error cargando cuerpo de Mail : ' + result);
									  }
				}
			);
		}else{
			editor.setData("");
		}
	}

	function win_envio_mail(){
		WIN_ENVIO_MAIL = new Ext.Window
		(
			{
				title		     : 'Seleccion de Contactos',
				id			     : 'VEN_ENVIO_MAIL',
				contentEl	   : 'VENTANA_SELECCION_MAILS',
				shadowOffset : 12,
				layout		   : 'fit',
				width		     : 950,
				modal		     : true,
				height		   : 600,
				closable 	   : true,
				closeAction	 : 'hide',
				plain		     : true
			}
		).show();
	}

	function win_envio_mail_close(){
		Ext.getCmp('WIN_ENVIO_MAIL').close();
	}

	function win_animacion_mail(){
		WIN_ANIMACION_MAIL = new Ext.Window
		(
			{
				title					: 'Enviando e-mail......',
				id						: 'WIN_ANIMACION_MAIL',
				html					:  `<div id="animacion_mail" name="animacion_mail">
													<img src="../../temas/clasico/images/comercial/email.gif" />
												</div>
											 	<div id="anuncios_mail" name="anuncios_mail" style="text-align:center; margin:10px 0 0 5px; width:290px; height:30px;">
											 	</div>`,
				autoScroll 		: false,
				closable 			: true,
				modal					: true,
				shadowOffset	: 12,
				layout				: 'fit',
				width					: 315,
				height				: 260,
				plain					: true
			}
		).show();
	}

	function win_animacion_mail_close(){
		WIN_ANIMACION_MAIL.close();
	}

	function TERMINA_ENVIAR_MAIL(id_cliente,nombre_informe,url_result){  

		window.frames['frame_mail'].document.body.innerHTML = "";
		document.getElementById('finaliza_email').value 		= 'false';

		var desti     			= "";
		var adjuntos1 			= "";
		var count_env_mails = document.getElementById('count_env_mails').value;
		var count_env_adjun = document.getElementById('count_env_adjun').value;
		var idioma          = document.getElementById('recibe_idioma_email').value;

		if(document.getElementById('env_email_cartera').checked == true){
			desti += document.getElementById('env_email_cartera').value + ',';
		}

		//REUNE TODOS LOS CORREOS DESTINOS
		for(i = 0; i < count_env_mails; i++){
			if(document.getElementById('env_email' + i).checked == true){
				desti += document.getElementById('env_email' + i).value + ',';
			}
		}

		if(document.getElementById('emails_cc').value != ''){
			desti += document.getElementById('emails_cc').value;
		}

		//REEMPLAZA LAS PUNTO Y COMA Y QUITA ESPACIOS AL LISTADO DE CORREOS
		desti = ReemplazaTodas(desti,";",",");
		desti = ReemplazaTodas(desti," ","");
		document.getElementById('listado_email').value = desti;

		if(desti != ""){

			win_animacion_mail();
			var php_mailer = '';

			//URL DEL ARCHIVO QUE CARGA LA LIBRERIA
			php_mailer = 'bd/documento_mail.php';

			//PARAMETROS DEL INFORME
	    var data = JSON.parse('<?php echo $data; ?>');

	    //URL DEL ARCHIVO QUE GENERA EL INFORME
	    var url  = url_result;
			
			//ANUNCIO EN VENTANA DE ENVIANDO MAIL
			document.getElementById('anuncios_mail').innerHTML = "<b>Generando Documento PDF...</b>";
			Ext.Ajax.request(
				{
					url    	: url,
					params 	: data,
					success	: function(result,request){ 
											anuncios = document.getElementById('anuncios_mail');

											//ANUNCIO EN VENTANA DE ENVIANDO MAIL
											if(anuncios != "null" ){
												anuncios.innerHTML = "<b>Enviando E-mail...</b>";
											}

											InstanciaName	= document.getElementById("InstanciaName").value;
											editor 				= CKEDITOR.instances[InstanciaName];
											body					= editor.getData();
											body 					= escape(body);

											var varPost =  `id_cliente=${id_cliente}
																			&destinatarios=${desti}
																			&idioma=${idioma}
																			&body=${body}
																			&nombre_informe=${nombre_informe}`;

											//LEE ARCHIVOS ADJUNTOS
											if(count_env_adjun != 'false'){
												for(ad = 0; ad < count_env_adjun; ad++){
													if(document.getElementById('env_adj' + ad).checked == true){
														adjuntos1 += document.getElementById('env_adj' + ad).value + ',';
													}
												}
												varPost += `&adjuntos1=${adjuntos1}`;
											}

											var adjuntos2 = document.getElementById("adjuntos2").value;
											if(adjuntos2 != ''){
												varPost += `&adjuntos2=${adjuntos2}`;
											}

											var formulario = $('#form_mail');
											formulario.attr("action", php_mailer + '?' + varPost);
											formulario.submit();
											finaliza_mail();
									  },
					failure : function(){
											document.getElementById('anuncios_mail').innerHTML = "<b>Error Generando Documento PDF...</b>";	////////////////////////////////////
									  }
				}
			);

		} else{
			alert('Debe Seleccionar al menos un destino de correo');
			return false;
		}
	}

	function finaliza_mail(){
		if(document.getElementById('finaliza_email').value != 'false'){
			poner_mensaje_sur();
		} else{
			setTimeout('finaliza_mail()',1500);
		}
	}

	function poner_mensaje_sur(){
		var destinos = document.getElementById('listado_email').value
		,   destinos = ReemplazaTodas(destinos,",","</br>")
		,        fin = document.getElementById('finaliza_email').value;

		if(fin == 'true'){
			var imagen = 'correcto.gif'
			,   cfondo = '#FFCCCC'
			,   clinea = '#FF6699'
			,  mensaje = '<b>El correo no fue enviado, por verifica que las direcciones se encuentren sin errores.</b>'
			,   cerrar = 'win_animacion_mail_close();';
		}
		else{
			var imagen = 'error.gif'
			,   cfondo = '#FFFFCC'
			,   clinea = '#FFCC33'
			,  mensaje = '<b>El correo fue enviado.</b>'
			,   cerrar = 'win_animacion_mail_close(); Win_Ventana_enviar_informe.close();';
		}

		var mensaje2 = `<div id="marco_mensaje2" style="float:left; width:90%; height:70%; margin:5%; background-color:${cfondo}; border:1px solid ${clinea}; color:#000 !important;">
											<div style="float:left; margin:3%; width:20px; ">
												<img src="../../temas/clasico/images/formularios/${imagen}">
											</div>
											<div style="float:left; width:75%; margin-top: 10px; font-size:12px; font-weight:bold;">
												${mensaje}
												</br></br>Direcciones Email:</br>${destinos}
											</div>
										</div>
										<div style="width:100%; height:30%; float:left; left:50%; text-align:center;">
											<input style="width:100px; height:30px;" name="termina_mail" type="button" id="termina_mail" value="Cerrar" onClick="${cerrar}">
										</div>`;

		document.getElementById('animacion_mail').innerHTML = mensaje2;
	}
</script>
