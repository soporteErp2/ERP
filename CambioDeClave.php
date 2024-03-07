<?php
	include("configuracion/conectar.php");
	include("configuracion/define_variables.php");
	echo "<script> var idusuario='".$_SESSION['IDUSUARIO']."';</script>";
?>

<div name="toolbar" id="toolbar" ></div>
		
<div style="margin-left:30px;position: relative;">
	<div style="padding:20px" >
		<div style="float:left;">
			Nuevo Password:
		</div>
		<div style="float:left;">
			<input class="myfield" name="pass1" type="password" id="pass1" style="width:170px;" />
		</div>
	</div>
	<div style="padding:20px" >
		<div style="float:left; ">
			Confirmacion Nuevo Password:
		</div>
		<div style="float:left;	">
			<input class="myfield" name="pass2" type="password" id="pass2" style="width:170px; " />
		</div>
	</div>
</div>	
<script>
new Ext.Panel
	(
		{
			renderTo	:'toolbar',
			frame		:false,
			border		:false,
			tbar		:
			[
				{
					xtype	: 'buttongroup',
					columns	: 3,
					items	: 
					[			
						{
							xtype		: 'button',
							//id			: 'btn2',
							text		: 'Cambiar',
							scale		: 'large',
							iconCls		: 'guardar',
							iconAlign	: 'top',
							handler 	: function(){guardarPass();}
						}
					]
				}
			]
		}
	);

function guardarPass(){
	
	var pass1	= document.getElementById('pass1').value;
	var pass2	= document.getElementById('pass2').value;
	
	if(pass1!=pass2){
		alert("El Nuevo Password no coincide.");
		document.getElementById('pass1').value="";
		document.getElementById('pass2').value="";		
	}else{
		if(pass1=="" || pass2=="" ){
			alert("Por favor escriba el nuevo Password");			
		}else{
			//MyLoading();
			Ext.Ajax.request
			(
				{
				url		: 'LOGICALERP/miapp/bd/bd.php',
				method	: 'post',
				timeout : 180000,
				params	:
					{
						op			:	"cambiaPass",
						pass		:	pass1,
						id			:	idusuario
					},
				success: function (result, request)
					{	
						var resultado  =  result.responseText.split("{.}");
						var respuesta = resultado[0];
						if(respuesta == 'false'){
						var observacion = resultado[1];
							alert('Error Enviando la Solicitud!\n\n'+observacion);	
						}
						if(respuesta == 'true'){
							alert('Cambio de Password Realizado!.');
							CambioDeClave.close();
						}						
					}
				}
			);
		}
	}
}
</script>