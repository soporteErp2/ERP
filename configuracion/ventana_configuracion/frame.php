<?php $DIRECTORIO = explode ("/", $_SERVER['REQUEST_URI']); ?>
<iframe width="585" height="368" frameborder="0" src="http://<?php echo $_SERVER["SERVER_NAME"].':'.$_SERVER["SERVER_PORT"].'/'.$DIRECTORIO[1]; ?>/configuracion/ventana_configuracion/index_conf.php" scrolling="no">
</iframe>