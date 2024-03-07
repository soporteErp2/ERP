<?php
	include("../../../configuracion/conectar.php");
	include("../../../configuracion/define_variables.php");
	//include("../misc/MyGrilla/class.MyGrilla.php");
?>

<style>
	.EmpConte	{float:left; margin:0 0 0 0; width:340px; height:25px}
	.EmpLabel	{float:left; width:150px;}
	.EmpField	{float:left; width:170px;}
</style>

<div id="toolbar_correo" style="height:85px"></div>

<div id="Recibidor_Panel_Global_Correo" style="float:left; margin:0 0 0 20px; width:100%;">
	<div class="EmpConte">
		<div class="EmpField">
			<input class="myfieldObligatorio" name="empresa"   TYPE="HIDDEN" id="empresa" value="" style="width:200px;"/>
		</div>
	</div>
	<div class="EmpConte">
		<div class="EmpLabel">
			Servidor SMTP
		</div>
		<div class="EmpField">
			<input class="myfieldObligatorio" name="servidor"  type="text" id="servidor" value="" style="width:200px;"/>
		</div>
	</div>

	<div class="EmpConte">
		<div class="EmpLabel">
			Nombre Usuario
		</div>
		<div class="EmpField">
			<input class="myfieldObligatorio" name="usuario"  type="text" id="usuario" value="" style="width:200px;" onBlur=""  />
		</div>
	</div>

	 <div class="EmpConte">
		<div class="EmpLabel">
			Contraseña
		</div>
		<div class="EmpField">
			<input class="myfieldObligatorio" name="password"  type="text" id="password" value="" style="width:200px;" onBlur=""/>
		</div>
	</div>

	 <div class="EmpConte">
		<div class="EmpLabel">
			Puerto
		</div>
		<div class="EmpField" id="campo_div">
			<input class="myfieldObligatorio" name="puerto"  type="text" id="puerto" value="" style="width:200px;" onBlur=""/>
		</div>
	</div>

	<div class="EmpConte">
		<div class="EmpLabel">
			Seguridad
		</div>
		<div class="EmpField">
			<select class="myfield" name="seguridad" id="seguridad" onChange="" style="width:200px" onBlur="">
				<option value="no" >ninguna</option>
				<option value="ssl" >SSL</option>
				<option value="tls" >TLS</option>
			</select>
		</div>
	</div>

	 <div class="EmpConte">
		<div class="EmpLabel">
			Autenticacion
		</div>
		<div class="EmpField">
			<select class="myfield" name="autenticacion" id="autenticacion" style="width:200px">
				<option value="no">No</option>
				<option value="si">Si</option>
			</select>
		</div>
	</div>
</div>

<script>
	new Ext.Panel
	(
		{
			renderTo	:'toolbar_correo',
			frame		:false,
			border		:false,
			tbar		:
			[
				{
					xtype	: 'buttongroup',
					columns	: 3,
					title	: 'Opciones',
					items	:
					[
						{
							xtype		: 'button',
							//id			: 'btn2',
							text		: 'Guardar',
							scale		: 'large',
							iconCls		: 'guardar',
							iconAlign	: 'top',
							handler 	: function(){BloqBtn(this); Guarda_Config_Correo();}
						},
						{
							xtype		: 'button',
							//id			: 'btn2',
							text		: 'Probar Configuracion',
							scale		: 'large',
							iconCls		: 'enviar',
							iconAlign	: 'top',
							handler 	: function(){BloqBtn(this); Probar_Config_Correo();}
						}
					]
				}
			]
		}
	);

	function Probar_Config_Correo(){
		var VARGET = '';

		var servidor		=	document.getElementById('servidor').value;
		var correo			=	document.getElementById('usuario').value;
		var password		=	document.getElementById('password').value;
		var puerto			=	document.getElementById('puerto').value;
		var seguridad		=	document.getElementById('seguridad').value;
		var autenticacion	=	document.getElementById('autenticacion').value;

		VARGET += 'CORREO='+ correo;
		VARGET += '&SERVIDOR=' + servidor;
		VARGET += '&USUARIO=' + correo;
		VARGET += '&PASSWORD=' + password;
		VARGET += '&SEGURIDAD=' + seguridad;
		VARGET += '&PUERTO=' + puerto;
		VARGET += '&AUTENTICACION=' + autenticacion;

		var win_test_zeus = new Ext.Window
		(
			{
				title		: 'Prueba de Conexión SMTP',
				iconCls		: 'smtp',
				bodyStyle	: 'background-color:#000000;',
				labelAlign	: 'top',
				animate		: true,
				autoDestroy : true,
				autoScroll	: true,
				draggable	: false,
				closable	: true,
				frame		: true,
				border		: false,
				modal 		: true,
				resizable	: false,
				width 		: 500,
				height 		: 500,
				autoLoad	:
				{
					url		: 'configuracion_correo_SMTP/test_smtp.php?'+VARGET,
					nocache	: true,
					scripts	: true
				}
			}
		).show();

		VARGET = '';

	}

	function Guarda_Config_Correo(filtro_empresa){
		var filtro_empresa	=	document.getElementById('empresa').value;
		var servidor		=	document.getElementById('servidor').value;
		var correo			=	document.getElementById('usuario').value;
		var password		=	document.getElementById('password').value;
		var puerto			=	document.getElementById('puerto').value;
		var seguridad		=	document.getElementById('seguridad').value;
		var autenticacion	=	document.getElementById('autenticacion').value;

		Ext.Ajax.request
			(
				{
				url		: 'configuracion_correo_SMTP/bd/bd.php',
				method	: 'post',
				timeout : 180000,
				params	:
					{
						op				:	'guardaConfig',
						filtro_empresa	:	filtro_empresa,
						servidor		:	servidor,
						correo			:	correo,
						password		:	password,
						puerto			:	puerto,
						seguridad		:	seguridad,
						autenticacion	:	autenticacion,
					},
				success: function (result, request)
					{
						var resultado  =  result.responseText.split("{.}");
						var respuesta = resultado[0];
						if(respuesta == 'true'){
							var resp = resultado[1];
							MyLoading();
							//alert(resp);
							<?php
								/*
								if(isset($filtro_empresa))
									echo "Win_Panel_Empresa.close();";
								else
									echo "Win_Panel_Global.close();";
								*/
							?>
						}else{
							var resp = resultado[1];
							alert(resp);
						}
					}
				}
			);
	}

	function loadConfig(){
			Ext.Ajax.request
			(
				{
				url		: 'configuracion_correo_SMTP/bd/bd.php',
				method	: 'post',
				timeout : 180000,
				params	:
					{
						op             : 'cargaConfig',
					},
				success: function (result, request)
					{
						var resultado  =  result.responseText.split("{.}");
						var respuesta = resultado[0];
						if(respuesta == 'true'){
							var servidor 	= resultado[1];
							var correo 		= resultado[2];
							var password	= resultado[3];
							var puerto 		= resultado[4];
							var seguridad	= resultado[5];
							var autenticacion = resultado[6];
							document.getElementById('servidor').value		=servidor;
							document.getElementById('usuario').value		=correo;
							document.getElementById('password').value		=password;
							document.getElementById('puerto').value			=puerto;
							document.getElementById('seguridad').value		=seguridad;
							document.getElementById('autenticacion').value	=autenticacion;
						}else{
							var resp = resultado[1];
							alert(resp);
							document.getElementById('servidor').value		="";
							document.getElementById('usuario').value		="";
							document.getElementById('password').value		="";
							document.getElementById('puerto').value			="";
						}
					}
				}
			);
	}

	function cargaConfigCorreo(){
		// var filtro_empresa	= document.getElementById('filtro_empresa_correo').value;
		// document.getElementById('empresa').value = filtro_empresa;
		loadConfig();
	}

	function cargaConfig(){
		// var filtro_empresa	= document.getElementById('filtro_empresa').value;
		// document.getElementById('empresa').value = filtro_empresa;
		loadConfig();
	}
	cargaConfig()
<?php
	if(isset($filtro_empresa))
		echo "cargaConfig();";
?>
</script>