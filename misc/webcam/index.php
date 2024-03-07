<?php
	include("../inc/define_variables.php");
?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>example1</title>
<script language="javascript">AC_FL_RunContent = 0;</script>
<script src="AC_RunActiveContent.js" language="javascript"></script>
<script>
function enviarId()
{
	var idFoto = '<?PHP echo $ID; ?>';
	document.example1.SetVariable("id", idFoto);
}
</script>
<style type="text/css">
<!--
body {
	margin-left: 16px;
	margin-top: 6px;
	margin-right: 0px;
	margin-bottom: 0px;
}
-->
</style></head>

<body bgcolor="#dfe8f6" onLoad="enviarId()">

<script language="javascript">
	if (AC_FL_RunContent == 0) 
	alert("This page requires AC_RunActiveContent.js.");
	else 
	{
		AC_FL_RunContent
		(
			'codebase', 'http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=8,0,0,0',
			'width', '285',
			'height', '140',
			'src', 'example1',
			'quality', 'high',
			'pluginspage', 'http://www.macromedia.com/go/getflashplayer',
			'align', 'middle',
			'play', 'true',
			'loop', 'true',
			'scale', 'showall',
			'wmode', 'window',
			'devicefont', 'false',
			'id', 'example1',
			'bgcolor', '#dfe8f6',
			'name', 'example1',
			'menu', 'true',
			'allowFullScreen', 'false',
			'allowScriptAccess','sameDomain',
			'movie', 'example1',
			'salign', ''
		); 
	}
</script>
<noscript>
	<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=8,0,0,0" width="285" height="140" id="example1" align="middle">
	<param name="allowScriptAccess" value="sameDomain" />
	<param name="allowFullScreen" value="false" />
	<param name="movie" value="example1.swf" /><param name="quality" value="high" /><param name="bgcolor" value="#dfe8f6" />	<embed src="example1.swf" quality="high" bgcolor="#dfe8f6" width="285" height="140" name="example1" align="middle" allowScriptAccess="sameDomain" allowFullScreen="false" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" />
	</object>
    

</noscript>
</body>
</html>
