<script>

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	///   ENVIO DE MAILS
	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    //alert("<?php echo $opc ?>");

	var WIN_ENVIO_MAIL;
	function win_envio_mail(){
		WIN_ENVIO_MAIL = new Ext.Window
		(
			{
				title		: 'Seleccion de Contactos',
				id			: 'VEN_ENVIO_MAIL',
				contentEl	: 'VENTANA_SELECCION_MAILS',
				shadowOffset: 12,
				layout		: 'fit',
				width		: 950,
				modal		: true,
				height		: 600,
				closable 	: true,
				closeAction	: 'hide',
				plain		: true
			 }
		).show();
	}

	function win_envio_mail_close(){
		Ext.getCmp('WIN_ENVIO_MAIL').close();
	}

	var WIN_ANIMACION_MAIL;
	function win_animacion_mail(){
		WIN_ANIMACION_MAIL = new Ext.Window
		(
			{
				title		: 'Enviando e-mail......',
				id			: 'WIN_ANIMACION_MAIL',
				html		: 	'<div id="animacion_mail" name="animacion_mail" ><img src="../../temas/clasico/images/comercial/email.gif" /></div>'+
								'<div id="anuncios_mail" name="anuncios_mail" style="text-align:center; margin:10px 0 0 5px; width:290px; height:30px;" ></div>',
				autoScroll 	: false,
				closable 	: true,
				modal		: true,
				shadowOffset: 12,
				layout		: 'fit',
				width		: 315,
				height		: 260,
				plain		: true
			 }
		).show();
	}

	function win_animacion_mail_close(){
		//cierra =Ext.getCmp('WIN_ANIMACION_MAIL');
		//cierra.hide();
		WIN_ANIMACION_MAIL.close();
	}

	//FIN DE LA VENTANA
/*
	function ENVIA_EMAIL(CUAL,id_intercambio,idioma){

		alert("ENVIA_EMAIL");
		var VARPOST = 'CUAL='+CUAL;
		VARPOST += '&id_intercambio='+id_intercambio;
		if(idioma && idioma != ''){
			VARPOST += '&idioma='+idioma;
		}

		Ext.get("VENTANA_SELECCION_MAILS").load
		(
			{
				url 	: 'eje_seleccionar_emails.php?'+VARPOST,
				timeout : 180000,
				scripts	: true,
				nocache	: true
			}
		);
		win_envio_mail();
	}
*/
	function TERMINA_ENVIAR_MAIL(CUAL,id_intercambio){


		window.frames['frame_mail'].document.body.innerHTML = "";
		document.getElementById('finaliza_email').value = 'false';
		var desti     = "";
		var adjuntos1 = "";

		var count_env_mails = document.getElementById('count_env_mails').value;
		var env_email_princ = document.getElementById('env_email_princ').value;		
		var count_env_adjun = document.getElementById('count_env_adjun').value;
		var idioma          = document.getElementById('recibe_idioma_email').value;

		/*
		if(CUAL == 1){// SI VIENE DE COTIZACION
			//if(document.getElementById('cot_1')){
				if(document.getElementById('cot_1').checked == true){var cot_tipo_1 = 'true';}else{var cot_tipo_1 = 'false';}
				if(document.getElementById('cot_2').checked == true){var cot_tipo_2 = 'true';}else{var cot_tipo_2 = 'false';}
				if(document.getElementById('cot_3').checked == true){var cot_tipo_3 = 'true';}else{var cot_tipo_3 = 'false';}

				if(cot_tipo_1 == 'false' && cot_tipo_2 == 'false' && cot_tipo_3 == 'false'){
					alert('Debe Seleccionar al menos un tipo de Documento'); return false;
				}
			//}
		}*/

		if(document.getElementById('env_email_cartera').checked == true)
		{
			desti += document.getElementById('env_email_cartera').value+',';
		}


		///// REUNE TODOS LOS CORREOS DESTINOS
		for(i=0;i<count_env_mails;i++)
		{
			if(document.getElementById('env_email'+i).checked == true)
			{
				desti += document.getElementById('env_email'+i).value+',';
			}
		}

		for(i=0;i<env_email_princ;i++)
		{
			if(document.getElementById('env_email_princ_'+i).checked == true)
			{
				desti += document.getElementById('env_email_princ_'+i).value+',';
			}
		}

		if(document.getElementById('emails_cc').value != '')
		{
			desti += document.getElementById('emails_cc').value;
		}

		////// REEMPLAZA LAS PUNTO Y COMA Y QUITA ESPACIOS AL LISTADO DE CORREOS
		desti=ReemplazaTodas(desti,";",",");
		desti=ReemplazaTodas(desti," ","");
		document.getElementById('listado_email').value = desti;
		//alert(document.getElementById('listado_email').value);

		if(desti!=""){
			<?php //PERMISO***PERMISO***PERMISO***PERMISO***PERMISO***PERMISO***PERMISO***PERMISO***PERMISO***PERMISO// ?>
			<?php // if(variables_globales('configuracion','debug2',$query_configuracion,'true') == 'false'){?>
				//WIN_ENVIO_MAIL.hide();
			<?php // } ?>

			win_animacion_mail();
			var CUALABRO     = '';
			var url          = '';
			var typeDocument = '';


			typeDocument = '<?php echo $opcGrillaContable; ?>';
			CUALABRO     = '../terceros/terceros/envio_mail/documento_mail.php';
			//url          = '<?php echo $urlImpresion ?>';


			//document.getElementById('anuncios_mail').innerHTML = "<b>Generando Documento PDF...</b>";	//////////////////////////////////// ANUNCIO EN VENTANA DE ENVIANDO MAIL
			
			anuncios = document.getElementById('anuncios_mail');
			if(anuncios!="null" ){
				anuncios.innerHTML = "<b>Enviando E-mail...</b>";	//////////////////////////////////// ANUNCIO EN VENTANA DE ENVIANDO MAIL
			}

			InstanciaName	= document.getElementById("InstanciaName").value;
			editor 			= CKEDITOR.instances[InstanciaName];
			body			= editor.getData();

			body=escape(body);

			var VARPOST = 'id_intercambio='+id_intercambio;
			VARPOST += '&destinatarios='+desti;
			VARPOST += '&idioma='+idioma;
			VARPOST += '&body='+body;
			VARPOST += '&typeDocument='+typeDocument;

			/*
			if(CUAL == 1){ // SI VIENE DE COTIZACION
				VARPOST += '&cot_tipo_1='+cot_tipo_1;
				VARPOST += '&cot_tipo_2='+cot_tipo_2;
				VARPOST += '&cot_tipo_3='+cot_tipo_3;
			}*/

			//LEE ARCHIVOS ADJUNTOS
			if(count_env_adjun != 'false'){
				for(ad=0;ad<count_env_adjun;ad++){
					if(document.getElementById('env_adj'+ad).checked == true){
						adjuntos1 += document.getElementById('env_adj'+ad).value+',';
					}
				}
				VARPOST += '&adjuntos1='+adjuntos1;				
			}

			var adjuntos2 = document.getElementById("adjuntos2").value;
			if(adjuntos2 != ''){
				VARPOST += '&adjuntos2='+adjuntos2;				
			}

			var formulario = $('#form_mail');
			formulario.attr("action", CUALABRO+'?'+VARPOST);
			//alert(VARPOST)
			/*
			var formulario = document.forms["form_mail"];
			alert (formulario);
			formulario.action = CUALABRO+'?'+VARPOST;*/

			formulario.submit();
			finaliza_mail();				  
								

		}else{
			alert('Debe Seleccionar al menos un destino de correo'); return false;
		}
	}

	function finaliza_mail(){
		//alert(CUAL+"-"+id_intercambio+"-"+document.getElementById('finaliza_email').value);
		//alert("-"+document.getElementById('finaliza_email').value);
		if(document.getElementById('finaliza_email').value != 'false'){
			//win_animacion_mail();
			//alert(temp[0]);
			//alert(temp[1]);
			//alert(temp[2]);

			poner_mensaje_sur();

		}else{
			setTimeout('finaliza_mail()',1500);
		}
	}

	//FUNCION QUE PONE MENSAJE EN LA BARRA SUR DEL LAYOUT*************
	function poner_mensaje_sur(){
		var destinos = document.getElementById('listado_email').value;
		destinos=ReemplazaTodas(destinos,",","</br>");
		fin = document.getElementById('finaliza_email').value;

		if(fin == 'true'){
			imagen = 'correcto.gif';
			var cfondo = '#FFCCCC'; var clinea = '#FF6699'; //ROJO
			mensaje="<b>El correo no fue enviado, por verifica que las direcciones se encuentren sin errores.</b>";
			cerrar	= 'win_animacion_mail_close();';
		}else{
			imagen = 'error.gif';
			var cfondo = '#FFFFCC'; var clinea = '#FFCC33'; //AMARILLO
			mensaje = "<b>El correo fue enviado.</b>";
			cerrar	= 'win_animacion_mail_close();ventana_email.close();';
		}

		var mensaje2 = 	'<div id="marco_mensaje2" style="float:left; width:90%; height:70%; margin:5%; background-color:'+cfondo+'; border:1px solid '+clinea+'; color:#000 !important;">'+
							'<div style="float:left; margin:3%; width:20px; ">'+
								'<img src="../../temas/clasico/images/formularios/'+imagen+'" />'+
							'</div>'+
							'<div style="float:left; width:75%; margin-top: 10px; font-size:12px; font-weight:bold;">'+mensaje+"</br>"+"</br>Direcciones Email:</br>"+destinos+
							'</div>'+
						'</div>'+
						'<div style=" width:100%; height:30%; float:left; left:50%;  text-align:center;">'+
							'<input style="width:100px; height:30px;" name="termina_mail" type="button" id="termina_mail" value="Cerrar" onClick="'+cerrar+'" />'+
						'</div>';

		document.getElementById('animacion_mail').innerHTML = mensaje2;

		var adjuntos2 = document.getElementById("adjuntos2").value;

		// ELIMINAR LOS ARCHIVOS CREADOS EN LA CARPETA
		Ext.Ajax.request(
			{
				url    : '../terceros/terceros/bd/bd.php',
				params : {
							opc     : "eliminarArchivosTemporales",
							id_mail : id_mail,
							files   : adjuntos2
						  },
				success	: function (result, request)
						  {
							var resultado   =  result.responseText.split("{.}");
							var respuesta   = resultado[0];
							var observacion = resultado[1];

							console.log(result.responseText);
							if(respuesta == 'false'){ alert(observacion); }
							else if(respuesta == 'true'){ editor.setData( observacion ); }


						  },
				failure : function()
						  {
							alert('Error cargando cuerpo de Mail : '+result);
						  }
			}
		);
	}
</script>