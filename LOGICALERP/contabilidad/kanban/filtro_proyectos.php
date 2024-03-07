<?php
	include("../../../configuracion/conectar.php");
	include("../../../configuracion/define_variables.php");

	$cont      = 0;
	$option    = '<optgroup label="Git">';
	$ruta      = 'C:\PROYECTOS/';
	$manejador = @dir($ruta);

    while ($nombreArchivo = $manejador->read()){
    	$cont++;
        $dirArchivo = "$ruta$nombreArchivo";

        if(strlen($nombreArchivo)<=2 || substr($nombreArchivo, 1)=='.' || $nombreArchivo=='.' || !file_exists($dirArchivo.'/.git/logs/HEAD') || !is_readable($dirArchivo.'/.git/logs/HEAD')){ continue; }          //SI NO EXISTE EL ARCHIVO
        $option .= '<option value="git_'.$cont.'">'.$nombreArchivo.'</option>';

    }

	$option    .= '</optgroup>';

    $manejador->close();

    echo'<div style="margin:5px;">
    		<select id="proyectosKanban" onchange="cambiarTableroKanban(this);">
    			'.$option.'
    		</select>
    	</div>'


?>

<script type="text/javascript">

	function cambiarTableroKanban(select){
		var proyecto = select.options[select.selectedIndex].text;

		Ext.get('contenedor_kanban').load({
			url     : 'kanban/kanban.php',
			scripts : true,
			nocache : true,
			params  :
			{
				proyecto : proyecto,
			}
		});
	}
	cambiarTableroKanban(document.querySelector('#proyectosKanban'));

</script>