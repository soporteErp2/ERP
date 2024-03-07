
<div id="ContenidoMenuInicio">

    <div style="float:left; width: 100%; margin: 0 0 0 0; cursor:pointer;" onclick="logout(); menu();">
        <div style="float:left; width:44px; height:44px; margin: 3px 0 10px 0;">
             <img src="temas/clasico/images/iconos/salir.png" alt="." >
        </div>
        <div style="float:left; margin:15px 0 0 10px; width:240px;  height:26px; float:left;">Salir del Aplicativo</div>
    </div>

    <div style="float:left; width: 100%; margin: 0 0 0 0; cursor:pointer; border-bottom: 1px solid #999" onclick="reiniciar('ventana'); menu();">
        <div style="float:left; width:44px; height:44px; margin: 3px 0 10px 0;">
             <img src="temas/clasico/images/iconos/reload.png" alt="." >
        </div>
        <div style="float:left; margin:15px 0 0 10px; width:240px;  height:26px; float:left;">Reiniciar en otra sucursal</div>
    </div>

	<?php while($row = mysql_fetch_array($consul)){	?>

        <div style="float:left; width: 100%; margin: 10px 0 0 0; cursor:pointer;" <?php if($PERMISO[$row['id']] == 'true'){echo' onClick="abre('.$row['id'] .',\'true\',\''.$row['ejecuta'].'\',\''.$PERMISO[$row['id']].'\')"  ';}?>>
       	  	<div style="float:left">
            	<div style="float:left; width:44px; height:44px; margin-top:3px; <?php if($PERMISO[$row['id']] != 'true'){echo' FILTER: Alpha (Opacity=20); -moz-opacity:.30; opacity:.30"';}?>">
                    <img alt="." src="temas/clasico/images/iconos/<?php echo $row['icono44'] ?>" >
                </div>
            	<div style="float:left; margin:15px 0 0 10px; width:240px; height:26px; float:left; <?php if($PERMISO[$row['id']] != 'true'){echo ' color:#AAA;';} ?>"><?php echo $row['nombre'] ?></div>
        	</div>
        </div>


	<?php } ?>

</div>
<div class="MenuEsquina"></div>

