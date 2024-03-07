<?php
// ICONOS
while($row2 = mysql_fetch_array($consul2)){?>

	<div id="icono<?php echo $row2['id'] ?>" class="ElIcono" style=" <?php if($PERMISO[$row2['id']] != 'true'){echo ' opacity:0.3;';}?> ">
		<div class="ElIconoContenedor" <?php if($PERMISO[$row2['id']] == 'true'){echo 'onClick="abre('.$row2['id'].' ,\'false\',\''.$row2['ejecuta'].'\',\''.$PERMISO[$row2['id']].'\')"';} ?>>
            <div class="ElIconoImagen">
                <img src="temas/clasico/images/iconos/<?php echo $row2['icono44'] ?>" width="44" height="44">
            </div>
            <div class="ElIconoLabel">
                <?php echo $row2['nombre'] ?>
            </div>
        </div>
	</div>

<?php }
//VENTANAS --------------------------------------------------------------------------------------------------
while($row4 = mysql_fetch_array($consul4)){?>

	<div id="ventana<?php echo $row4['id'] ?>" class="VENTANA Sombra" style="position:absolute; left:438px; top:1669px; width:373px; height:110px; z-index:100; ">
        <div style=" float:left">
            <div class="ClassVentana" id="esquina_izquierda_arriba<?php echo $row4['id'] ?>" style="float:left; width:20px; height:29px; "></div>
            <div class="ClassVentana" id="barra_arriba<?php echo $row4['id'] ?>" style="float:left; width:333px; height:29px; ">
            	<div class="ICONO_VENTANA"><img src="temas/clasico/images/iconos/<?php echo $row4['icono26'] ?>" /></div>
                <div class="ClassVentana FUENTE11BL" style="font-size:12px; float:left; margin:8px; font-weight:bold; background: rgba(255,255,255,0);"><?php echo $row4['nombre'] ?></div>
                <div class="ClassVentana" style="float:right; width:20px; height:20px; margin-left:6px; margin-top:6px; cursor:pointer; background: rgba(255,255,255,0) url('temas/clasico/images/ventana/cierra.png')" onclick="cierra(<?php echo $row4['id'] ?>)"></div>
                <div class="ClassVentana" style="float:right; width:20px; height:20px; margin-left:6px; margin-top:6px; cursor:pointer; background: rgba(255,255,255,0) url('temas/clasico/images/ventana/minimiza.png')" onclick="minimiza(<?php echo $row4['id'] ?>)"></div>
            </div>
            <div class="ClassVentana" id="esquina_derecha_arriba<?php echo $row4['id'] ?>" style="float:left; width:20px; height:29px;"></div>
        </div>
        <div style=" float:left">
            <div class="ClassVentana" id="lateral_izqierdo<?php echo $row4['id'] ?>" style="float:left; width:5px; height:90px; "></div>
            <div class="ClassVentana" id="contenido<?php echo $row4['id'] ?>" style="float:left; width:363px; height:90px;">

				<?php if($PERMISO[$row4['id']] == 'true'){ ?>
                    <div id="LaVentanaLoading<?php echo $row4['id'] ?>" style="float:left; width:100%; height:100%;">
                        <div id="experiment">
                            <div id="cube">
                                    <div class="face one">
                                        <div id="cuadro" class="el1"></div>
                                        <div id="cuadro" class="el2"></div>
                                        <div id="cuadro" class="el1"></div>
                                        <div id="cuadro" class="el1"></div>
                                    </div>
                                    <div class="face two">
                                        <div id="cuadro" class="el1"></div>
                                        <div id="cuadro" class="el3"></div>
                                        <div id="cuadro" class="el3"></div>
                                        <div id="cuadro" class="el3"></div>
                                    </div>
                                    <div class="face three">
                                        <div id="cuadro" class="el2"></div>
                                        <div id="cuadro" class="el2"></div>
                                        <div id="cuadro" class="el3"></div>
                                        <div id="cuadro" class="el2"></div>
                                    </div>
                                    <div class="face four">
                                        <div id="cuadro" class="el2"></div>
                                        <div id="cuadro" class="el3"></div>
                                        <div id="cuadro" class="el2"></div>
                                        <div id="cuadro" class="el2"></div>
                                    </div>
                                    <div class="face five">
                                        <div id="cuadro" class="el3"></div>
                                        <div id="cuadro" class="el3"></div>
                                        <div id="cuadro" class="el3"></div>
                                        <div id="cuadro" class="el1"></div>
                                    </div>
                                    <div class="face six">
                                        <div id="cuadro" class="el2"></div>
                                        <div id="cuadro" class="el1"></div>
                                        <div id="cuadro" class="el1"></div>
                                        <div id="cuadro" class="el1"></div>
                                    </div>
                                    <div class="face seven"></div>
                            </div>
                            <div id="LabelCargando">Cargando...</div>
                        </div>
                    </div>
                <?php } ?>
                <!-- IFRAME ------------------------------------------------- -->
                <div id="LaVentanaIframe<?php echo $row4['id'] ?>" style="width:100%; height:100%; visibility:hidden">
					 <iframe id="IFR_CONTENIDO<?php echo $row4['id'] ?>" frameborder="0" scrolling="auto" AllowTransparency></iframe>
            	</div>
                <!-- IFRAME ------------------------------------------------- -->

            </div>
            <div class="ClassVentana" id="lateral_derecho<?php echo $row4['id'] ?>" style="float:left; width:5px; height:90px; "></div>
        </div>
        <div style=" float:left; height:5px">
            <div class="ClassVentana" id="esquina_izquierda_abajo<?php echo $row4['id'] ?>" style="float:left; width:20px; height:5px;  "></div>
            <div class="ClassVentana" id="barra_abajo<?php echo $row4['id'] ?>"             style="float:left; width:333px; height:5px; "></div>
            <div class="ClassVentana" id="esquina_derecha_abajo<?php echo $row4['id'] ?>"   style="float:left; width:20px; height:5px;  "></div>
        </div>

    </div>

<?php }
//BOTONES DE LA BARRA DE TAREAS --------------------------------------------------------------------------------------
while ($row6 = mysql_fetch_array($consul6)){?>

	<div id="ventana_m<?php echo $row6['id'] ?>" class="BOTON_BARRA_TAREAS" style="position:absolute; left:840px; top:1654px; width:124px; height:<?php echo $CONF_BARRATAREAS_ALTO - 3 ?>px; z-index:8500; cursor:pointer; " onClick="abre(<?php echo $row6['id'] ?>,'false')">
			<div style="float:left"><img src="temas/clasico/images/iconos/<?php echo $row6['icono26'] ?>" /></div>
            <div  id="NOMBRE_VENTANAS_TAREAS<?php echo $row6['id'] ?>" style="float:left; margin-left:3px; margin-top:4px; white-space : nowrap; overflow : hidden; text-overflow : ellipsis;"></div>
	</div>

<?php } ?>