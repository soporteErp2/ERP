<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Documento sin t&iacute;tulo</title>
</head>

<body>
<div style="float:left; text-align:center; width:80px; height:64px; margin:10px; cursor:pointer" onclick="abrir(0)">
	<div style="text-align:center; width:100%; height:44px">
    	<img src="../../temas/clasico/images/iconos/panel_control44.png" />
    </div>
    <div style="text-align:center; width:100%; height:24px; color:#FFFFFF">
    	Conexion
    </div>
</div>
<div style="float:left; text-align:center; width:80px; height:64px; margin:10px; cursor:pointer" onclick="abrir(1)">
	<div style="text-align:center; width:100%; height:44px">
    	<img src="../../temas/clasico/images/iconos/panel_control44.png" />
    </div>
    <div style="text-align:center; width:100%; height:24px; color:#FFFFFF">
    	Traducciones
    </div>
</div>
</body>
</html>

<script>
function abrir(id){

if(id==0){document.location="configuracion_conexion.php";}
if(id==1){document.location="configuracion_idioma.php";}

}
</script>
