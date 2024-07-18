<?php
	include("../../configuracion/conectar.php");
	include("../../configuracion/define_variables.php");

	$id_usuario = $_SESSION['IDUSUARIO'];

	$select     = "SELECT email_empresa FROM empleados WHERE id=$id_usuario LIMIT 0,1";
	$query      = mysql_query($select,$link);
	$email =mysql_result($query,0,'email_empresa');

?>

<div name="toolbar" id="toolbar" ></div>
<div style="margin-top:10px;margin-bottom:10px;margin-left:15px;">
	Email
	<input type="text" id="miCorreo" class="myfieldObligatorio" value="<?php echo $email; ?>" style="width:150px;margin-left:20px;">
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
							text		: 'Guardar',
							scale		: 'large',
							iconCls		: 'guardar',
							iconAlign	: 'top',
							handler 	: function(){BloqBtn(this);validaEmail(document.getElementById('miCorreo').value);}
						}
					]
				}
			]
		}
	);

function validaEmail(email)
{
var re  = /^([a-zA-Z0-9_.-])+@(([a-zA-Z0-9-])+.)+([a-zA-Z0-9]{2,4})+$/;
if (!re.test(email)) {
    alert ("Direccin de email invalida");
    return false;
}
guardarCorreo(email);
}

function guardarCorreo(email){

	MyLoading();
	Ext.Ajax.request
	(
		{
		url		: 'bd/bd.php',
		method	: 'post',
		timeout : 180000,
		params	:
			{
				op			:	"guardaCorreo",
				email		:	email
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
					alert('Cambio de correo realizado');
					Win_Panel_Global.close();
				}
			}
		}
	);

}

</script>

