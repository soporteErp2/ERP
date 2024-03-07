<?php
    if(!isset($_SESSION)){ session_start(); }
    // include('configuracion/conectar.php');


    if(isset($_COOKIE['LogicalERP']) && $_COOKIE['LogicalERP'] != 'false'){
    	$la_cookie = $_COOKIE['LogicalERP'];
    	//echo $la_cookie;
        $la_cookieArray = explode("-", $la_cookie);
    	$campo1 = $la_cookieArray[0];
    	$campo2 = $la_cookieArray[1];
    	$campo3 = $la_cookieArray[2];
    	$campo4 = $la_cookieArray[3];
        setcookie("LogicalERP",$campo1.'-'.$campo2.'-'.$campo3.'-'.$campo4 , time() + (7 * 86400) );
    }
    else{
    	$campo1 = '';
        $campo2 = '';
        $campo3 = '';
    	$campo4 = '';
    }

    if(@$_SESSION['EMPRESA'] > 0 && $_SESSION['SUCURSAL'] > 0 && $_SESSION['ROL'] > 0 && $_SESSION['ROLVALOR'] > 0 && $_SESSION['IDUSUARIO'] > 0){
        header('Location: http://'.$_SERVER['HTTP_HOST'].'/escritorio.php');
    }


    function info_user_agent()
    {
        $browser = array("IE","OPERA","MOZILLA","NETSCAPE","FIREFOX","SAFARI","CHROME");
        $os      = array("WIN","MAC","LINUX");

        # definimos unos valores por defecto para el navegador y el sistema operativo
        $info_cliente['browser'] = "OTHER";
        $info_cliente['os'] = "OTHER";

        # buscamos el navegador con su sistema operativo
        foreach($browser as $parent)
        {
            $s = strpos(strtoupper($_SERVER['HTTP_USER_AGENT']), $parent);
            $f = $s + strlen($parent);
            $version = substr($_SERVER['HTTP_USER_AGENT'], $f, 15);
            $version = preg_replace('/[^0-9,.]/','',$version);
            if ($s)
            {
                $info_cliente['browser'] = $parent;
                $info_cliente['version'] = $version;
            }
        }

        # obtenemos el sistema operativo
        foreach($os as $val)
        {
            if (strpos(strtoupper($_SERVER['HTTP_USER_AGENT']),$val)!==false)
                $info_cliente['os'] = $val;
        }

        # devolvemos el array de valores
        return $info_cliente;
    }

    $info_cliente = info_user_agent();
    $soporte_web  = true;

    //WINDOWS, OPERA, FIREFOX, CHROME
    $arrayIcons = array();
    if($info_cliente['browser']!='OPERA' && $info_cliente['browser']!='FIREFOX' && $info_cliente['browser']!='CHROME' && ($info_cliente['os']=='WIN' || $info_cliente['os']=='LINUX')){
        $soporte_web = true;

        $arrayIcons[1] = array('img'=>'images/chrome.png', 'url'=>'http://www.google.com/intl/es-419/chrome/', 'title'=>'Chrome');
        $arrayIcons[2] = array('img'=>'images/firefox.png', 'url'=>'https://www.mozilla.org/es-ES/firefox/new/', 'title'=>'Mozilla Firefox');
        $arrayIcons[3] = array('img'=>'images/opera.png', 'url'=>'http://www.opera.com/es-419/computer', 'title'=>'Opera');
    }
    else if($info_cliente['os']=='MAC' && $info_cliente['browser']!='OPERA' && $info_cliente['browser']!='FIREFOX' && $info_cliente['browser']!='CHROME' && $info_cliente['browser']!='SAFARI'){
        $soporte_web = true;

        $arrayIcons[1] = array('img'=>'images/chrome.png', 'url'=>'http://www.google.com/intl/es-419/chrome/', 'title'=>'Chrome');
        $arrayIcons[2] = array('img'=>'images/firefox.png', 'https://www.mozilla.org/es-ES/firefox/new/', 'title'=>'Mozilla Firefox');
        $arrayIcons[3] = array('img'=>'images/opera.png', 'url'=>'http://www.opera.com/es-419/computer', 'title'=>'Opera');
        // $arrayIcons[4] = array('img'=>'images/safari.png', 'url'=>'#');
    }

    $contIcons = count($arrayIcons);
?>

<!DOCTYPE HTML>
<html>
    <head>
        <link rel="SHORTCUT ICON" href="favicon.ico">
        <title>LogicalSoft-ERP</title>
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <link href="login/login.css" rel="stylesheet" type="text/css">
        <!--[if lt IE 9]><script src="//html5shim.googlecode.com/svn/trunk/html5.js"></script><![endif]-->
        <script type="text/javascript" src="misc/extjs3/ext-base.js?v1.0.0.19-06-2013"></script>
        <script type="text/javascript" src="misc/extjs3/ext-all.js?v1.0.0.19-06-2013"></script>
        <script type="text/javascript" src="misc/lib.js?v1.0.0.19-06-2013"></script>
        <script type="text/javascript" src="misc/jquery/1.7.1/jquery.min.js?v1.0.0.19-06-2013"></script>
    	<script type="text/javascript" src="misc/alertify.js/alertify.js"></script>
        <script type="text/javascript" src="login/login.js"></script>
    	<link rel="stylesheet" href="misc/alertify.js/alertify.core.css" />
    	<link rel="stylesheet" href="misc/alertify.js/alertify.default.css" />
        <link rel="stylesheet" type="text/css" href="misc/extjs3/resources/css/ext-all.css"/>
        <style>
            .divInput{
                padding            : 10px 10px 0px 10px;
                width              : 230px;
                height             : 30px;
                background         : #DDD;
                font-size          : 14px;
                border             : 0;
                border-radius      : 0px 5px 5px 0;
                -webkit-box-shadow : inset 0 1px 4px #333, 0 1px #999;
                box-shadow         : inset 0 1px 4px #999, 0 1px #999;
            }

            .icons_navigators{
                text-align   : right;
                margin-right : 10px;
            }

            .icons_navigators a{
                margin-left : 5px;
                width       : 30px;
                height      : 30px;
            }

            .icons_navigators img{ border: none; }

            .icons_navigators div{
                margin-left : 5px;
                width       : 30px;
                height      : 30px;
            }

            body .alertify{
                top                   : 25px;
                border                : 1px solid #333;
                border-radius         : 6px;
                -webkit-border-radius : 6px;
            }

            body .alertify-dialog { padding: 10px; }
            body .alertify-buttons, body p{ display: none; }
            body .mytext{ color: #848484; }
		</style>
    </head>
    <body style="background: #EEE url(login/images/fondo<?php echo rand(1,10) ?>.png) no-repeat center center fixed; 		-webkit-background-size	: cover;
		-moz-background-size	: cover;
		-o-background-size		: cover;
		background-size			: cover;" >
        <div id="PopUp"></div>
   		<div class="ribete"></div>
        <div class="LogoLogin"></div>
        <div class="DivLogin">
            <div class="CuadritoBlanco1"></div>
            <div class="CuadritoBlanco2"></div>
            <div class="CuadritoBlanco3"></div>
            <div class="CuadritoBlanco4"></div>

            <div class="ContentField">
                <div class="FieldImage"><img alt="Empresa" src="login/images/empresa.png" width="32" height="32"/></div>
                <div class="FieldDiv">
                    <input type="text" class="mytext"  name="empresa" id="empresa" placeholder="ID Empresa" onfocus="EmpresaFocus=1" onblur="EmpresaFocus=0" onChange="VaciarDatos();consulta_empresa()" value="<?php if($campo1 != 'false'){echo $campo1;}?>">
                </div>
            </div>

             <div class="ContentField">
                <div class="FieldImage"><img alt="Empresa" src="login/images/empresa.png" width="32" height="32"/></div>
                <div id="loguinSucursal" class="FieldDiv">
                	<input type="text" class="mytext"  name="sucursal" id="sucursal" placeholder="Agencia &oacute; Sucursal" onBlur="" value="" readonly>
                </div>

            </div>

            <div class="ContentField">
                <div class="FieldImage"><img alt="Usuario" src="login/images/usuario.png" width="32" height="32"/></div>
                <div class="FieldDiv">
                    <input type="text" class="mytext" name="usuario" id="usuario" placeholder="Usuario" onBlur="consulta_usuario()" value="<?php if($campo3 != 'false'){echo $campo3;}?>" readonly>
                </div>
            </div>

            <div class="ContentField">
                <div class="FieldImage"><img alt="Contrase&ntilde;a" src="login/images/password.png" width="32" height="32"/></div>
                <div class="FieldDiv">
                    <input type="password" class="mytext"  name="password" id="password" placeholder="Contrase&ntilde;a" readonly>
                </div>
            </div>

            <div class="DivRecordar">
                <div style="float:right; padding: 3px;" class="mytext">Recordar mi Usuario</div>
                <div style="float:right;"><input id="recordarme" name="recordarme" type="checkbox" value="true" ></div>
            </div>
            <div class="DivBoton">
                <input type="button" class="login-button login-button-background" onClick="verificar_final()">
            </div>

        </div>
        <!--<div id="loguin-error" class="loguin-error"></div>-->

    </body>
</html>
<?php
    echo '<script>var campo1 = "'.$campo1.'"</script>';
    echo '<script>var campo2 = "'.$campo2.'"</script>';
	echo '<script>var campo3 = "'.$campo3.'"</script>';
	echo '<script>var campo4 = "'.$campo4.'"</script>';
?>

<script>
	VerificarCookie();

	<?php

		if(!$soporte_web){

			$divIconos = '<div class="icons_navigators">';
			for ($i=1; $i <= $contIcons ; $i++) {
				$divIconos .= '<a href="'.$arrayIcons[$i]['url'].'" target="_blank" title="'.$arrayIcons[$i]['title'].'"><img alt="Usuario" src="'.$arrayIcons[$i]['img'].'" width="30" height="30"/></a>';
			}
			$divIconos .= '</div>';
	?>
			alertify.alert('<div>Para una mejor experiencia de usuario recomendamos los siguientes navegadores!<div><?php echo $divIconos ?>');
            document.getElementById('empresa').disabled  = true;
            document.getElementById('sucursal').disabled = true;
            document.getElementById('usuario').disabled  = true;
            document.getElementById('password').disabled = true;
	<?php } ?>
</script>
