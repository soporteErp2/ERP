<?php
	include("../../../configuracion/define_variables.php");
?>
    <script language="javascript">AC_FL_RunContent = 0;</script>
    <script type="text/javascript" src="AC_RunActiveContent.js"></script>
    
<script>
	function enviarId(){
		var idFoto = '<?php echo $ID; ?>';
		document.webcam_empleados.SetVariable("id", idFoto);
	}
</script>
<div id="flashContent">
<script language="javascript">

		AC_FL_RunContent
		(
			'codebase'			, 'http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=8,0,0,0',
			'width'				, '220',
			'height'			, '140',
			'src'				, 'webcam_empleados',
			'quality'			, 'high',
			'pluginspage'		, 'http://www.macromedia.com/go/getflashplayer',
			'align'				, 'middle',
			'play'				, 'true',
			'loop'				, 'true',
			'scale'				, 'showall',
			'wmode'				, 'transparent',
			'devicefont'		, 'false',
			'id'				, 'webcam_empleados',
			'bgcolor'			, '#dfe8f6',
			'name'				, 'webcam_empleados',
			'menu'				, 'true',
			'allowFullScreen'	, 'false',
			'allowScriptAccess'	, 'sameDomain',
			'movie'				, 'webcam_empleados',
			'salign'			, ''
		); 
	
</script>

<noscript>
	<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=8,0,0,0" width="220" height="140" id="webcam_empleados" align="middle">
        <param name="allowScriptAccess" value="sameDomain" />
        <param name="allowFullScreen" value="false" />
        <param name="movie" value="webcam_empleados.swf" />
        <param name="quality" value="high" />
        <param name="bgcolor" value="#dfe8f6" />
        <param name="WMode" value="Transparent">	
        <embed src="webcam_empleados.swf" quality="high" bgcolor="#dfe8f6" width="220" height="140" name="webcam_empleados" align="middle" allowScriptAccess="sameDomain" allowFullScreen="false" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" />
	</object>
</noscript>
</div>
<script>
	enviarId();
</script>
