<?php
	session_start();
	include('../../configuracion/define_variables.php');
?>
<style>
	html, body{
		width				: 100%;
		height				: 100%;
		margin				: 0px;
		background          : rgba(<?php echo $_SESSION["COLOR_MENU"]?>,1);
        background          : radial-gradient(ellipse at center, rgba(255,255,255,1) 0%, rgba(<?php echo $_SESSION["COLOR_MENU"]?>,1) 100%);
	}
	#logo{
		position			: absolute;
		top					: 50%;
		left				: 50%;
		width				: 500px;
		height				: 146px;
		margin				: -123px 0 0 -250px;
		cursor				: pointer
	}
	#Boton{
		position			: absolute;
		top					: 50%;
		left				: 50%;
		margin				: 60px 0 0 -125px;
    	display				: block;
		width				: 250px;
		height				: 60px;
		padding				: 10px 0 0 0;
		cursor				: pointer;

    	background			: #398525; /* viejos navegadores */
    	background			: -moz-linear-gradient(top, #8DD297 0%, #398525 100%); /* firefox */
    	background			: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#8DD297), color-stop(100%,#398525)); /* webkit */

    	box-shadow			: inset 0px 0px 6px #fff;
    	-webkit-box-shadow	: inset 0px 0px 6px #fff;
   		border				: 1px solid #5ea617;
    	border-radius		: 10px;

		font-family			: Verdana, Geneva, sans-serif;
		font-size			: 20px;
		font-weight			: bold;
		color				: #FFF;
		text-align			: center;

    	text-shadow			: 1px 1px 2px #333;
	}

</style>
	<div id="logo" onClick="entrar();">
		<img src="images/LogoSupport.png" width="500" height="146">
	</div>
	<div id="Boton" onClick="entrar();">
		ENTRAR AL MODULO<br />DE SOPORTE
	</div>
<script>
	var Base64={_keyStr:"ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=",encode:function(e){var t="";var n,r,i,s,o,u,a;var f=0;e=Base64._utf8_encode(e);while(f<e.length){n=e.charCodeAt(f++);r=e.charCodeAt(f++);i=e.charCodeAt(f++);s=n>>2;o=(n&3)<<4|r>>4;u=(r&15)<<2|i>>6;a=i&63;if(isNaN(r)){u=a=64}else if(isNaN(i)){a=64}t=t+this._keyStr.charAt(s)+this._keyStr.charAt(o)+this._keyStr.charAt(u)+this._keyStr.charAt(a)}return t},decode:function(e){var t="";var n,r,i;var s,o,u,a;var f=0;e=e.replace(/[^A-Za-z0-9\+\/\=]/g,"");while(f<e.length){s=this._keyStr.indexOf(e.charAt(f++));o=this._keyStr.indexOf(e.charAt(f++));u=this._keyStr.indexOf(e.charAt(f++));a=this._keyStr.indexOf(e.charAt(f++));n=s<<2|o>>4;r=(o&15)<<4|u>>2;i=(u&3)<<6|a;t=t+String.fromCharCode(n);if(u!=64){t=t+String.fromCharCode(r)}if(a!=64){t=t+String.fromCharCode(i)}}t=Base64._utf8_decode(t);return t},_utf8_encode:function(e){e=e.replace(/\r\n/g,"\n");var t="";for(var n=0;n<e.length;n++){var r=e.charCodeAt(n);if(r<128){t+=String.fromCharCode(r)}else if(r>127&&r<2048){t+=String.fromCharCode(r>>6|192);t+=String.fromCharCode(r&63|128)}else{t+=String.fromCharCode(r>>12|224);t+=String.fromCharCode(r>>6&63|128);t+=String.fromCharCode(r&63|128)}}return t},_utf8_decode:function(e){var t="";var n=0;var r=c1=c2=0;while(n<e.length){r=e.charCodeAt(n);if(r<128){t+=String.fromCharCode(r);n++}else if(r>191&&r<224){c2=e.charCodeAt(n+1);t+=String.fromCharCode((r&31)<<6|c2&63);n+=2}else{c2=e.charCodeAt(n+1);c3=e.charCodeAt(n+2);t+=String.fromCharCode((r&15)<<12|(c2&63)<<6|c3&63);n+=3}}return t}}

	window.openPost = function(url,target,op){
		var form = document.createElement("form");
		form.setAttribute("method", "post");
		form.setAttribute("action", url);
		form.setAttribute("target", target);

		var hiddenField = document.createElement("input");
		hiddenField.setAttribute("name", "op");
		hiddenField.setAttribute("value", op);
		hiddenField.setAttribute("type", "hidden");
		form.appendChild(hiddenField);
		document.body.appendChild(form);
		form.submit();
	}

	function validarEmail( email ) {
		expr = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
		if ( !expr.test(email) )
		alert("Error: No puede ingresar al modulo de soporte, debido a que su usuario \nno tiene configurada una direccion de correo valida.\n\n\nPor favor comuniquese con el funcionario encargado de la administracion de usuarios para que realice la correcion.");return false;
	}

	// PASO DE VARIABLES DESDE LA SESSION DEL HOTEL A LA SESSION DE SOPORTE
	function entrar(){
		var producto 		= '<?php echo $_SESSION['PRODUCTO']?>';
		var licencia		= '<?php echo $_SESSION['LICENCIASOPORTE']?>';
		var version 		= '2.0.1';
		var app				= 'Logicalsoft-erp';
		var sucursal		= '<?php echo $_SESSION['NOMBRESUCURSAL']?>';
		var empresa 		= '<?php echo $_SESSION['NOMBREEMPRESA']?>';
		var usuario 		= '<?php echo $_SESSION['NOMBREUSUARIO']?>';
		var email 			= '<?php echo $_SESSION['EMAIL']?>';
		var id_sucursal 	= '<?php echo $_SESSION['SUCURSAL']?>';
		var id_empresa 		= '<?php echo $_SESSION['EMPRESA']?>';
		var id_usuario 		= '<?php echo $_SESSION['IDUSUARIO']?>';
		var funcionario 	= '<?php echo $_SESSION['NOMBREFUNCIONARIO']?>';
		var colormenu		= '<?php echo $_SESSION['COLOR_MENU'] ?>';

		validarEmail(email);

		var op = Base64.encode(producto+"{.}"+licencia+"{.}"+version+"{.}"+app+"{.}"+sucursal+"{.}"+empresa+"{.}"+usuario+"{.}"+email+"{.}"+id_sucursal+"{.}"+id_empresa+"{.}"+id_usuario+"{.}"+funcionario+"{.}"+colormenu);

		window.openPost("https://soporte.logicalsoft.co/soporte/index.php","Soporte",op);
	}
</script>