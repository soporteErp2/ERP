<?php
	include("../../configuracion/conectar.php");
	include("../../configuracion/define_variables.php");

	$consul = mysql_query("SELECT * FROM modulos_erp WHERE ejecuta != 'blank.php'",$link);
	$consul_permisos = mysql_query("SELECT * FROM empleados_roles_permisos WHERE id_rol = '$elid'",$link);
	$DatRol = mysql_query("SELECT nombre,valor FROM empleados_roles WHERE id=$elid",$link);
	$nombre = mysql_result($DatRol,0,"nombre");
	$rolvalor = mysql_result($DatRol,0,"valor");
?>

<form id="formPERMISOS" name="formPERMISOS">
    <div style="float:left; margin:10px 0 0 20px; width:450px">

            <div style="float:left; width:100%; margin:5px 0 0 0">
                <div style="float:left; width:50px">
                    Rol :
                </div>
                <div style="float:left; width:200px">
                    <input class="myfieldObligatorio" onBlur="ValidarFieldVacio(this)" name="rol" type="text" id="rol" style="width:190px" value="<?php echo $nombre ?>"  <?php if($_SESSION["ROLVALOR"] >= $rolvalor && $_SESSION["ROLVALOR"] != 0){echo ' disabled';} ?> />
                </div>

                <div style="float:left; width:50px">
                   Nivel :
                </div>
                <div style="float:left; width:100px">
                    <select class="myfieldObligatorio" onBlur="ValidarFieldVacio(this)" name="rolnivel" id="rolnivel" <?php if($_SESSION["ROLVALOR"] >= $rolvalor && $_SESSION["ROLVALOR"] != 0){echo ' disabled';} ?>>
                        <option value="" selected>Seleccione.......</option>
                        <?php
							if($_SESSION["ROLVALOR"] >= $rolvalor && $_SESSION["ROLVALOR"] != 0){
								$i = 0;
							}else{
								if($_SESSION["ROLVALOR"] == 0){
									$i = $_SESSION["ROLVALOR"];
								}else{
									$i = $_SESSION["ROLVALOR"] + 1;
								}
							}

							for($i;$i<21;$i++){
								echo '<option value="'.$i.'">Nivel '.$i.'</option>';
							}
						?>
                    </select>
                </div>
            </div>

    </div>
    <div style="float:left; width:380px; margin:20px 0 0 20px">

                <div style="float:left; width:480px">
                    <div style="float:left; padding:5px 0 0 0">
                        <div style=" width:44px; height:44px" class="user44"></div>
                    </div>
                    <div style="float:left; font-size:18px; padding:5px 0 0 10px">
                        Permisos de Usuario
                    </div>
                </div>
                <div style="float:left; width:480px; padding:0 0 10px 60px; border-bottom:1px solid #CCC;">
                    <?php
						$consul2 = mysql_query("SELECT * FROM empleados_permisos WHERE modulo = 1000",$link);
                        while($row2=mysql_fetch_array($consul2)){
                    ?>
                            <div style="float:left; width:100%">
                                <input id="checks_PERMISOS_<?php echo $row2['id'];?>" name="checks_PERMISOS" type="checkbox" value="<?php echo $row2['id'];?>" disabled/>
                                <?php
									echo $row2['nombre'];
									if(user_permisos($row2['id']) == 'true' && $_SESSION["ROLVALOR"] < $rolvalor){
										echo "<script>if(document.getElementById('checks_PERMISOS_".$row2['id']."')){";
										echo "document.getElementById('checks_PERMISOS_".$row2['id']."').disabled = '';";
										echo "}</script>";
									}
									if($_SESSION["ROLVALOR"] == 0){
										echo "<script>if(document.getElementById('checks_PERMISOS_".$row2['id']."')){";
										echo "document.getElementById('checks_PERMISOS_".$row2['id']."').disabled = '';";
										echo "}</script>";
									}
								?>
                            </div>
                    <?php
                        }
                    ?>
                </div>

        <?php
            while($row=mysql_fetch_array($consul)){
				$consul2 = mysql_query("SELECT * FROM empleados_permisos WHERE modulo = $row[id]  ORDER BY orden",$link);
				$mycss   = explode('.',$row['icono44']);
        ?>
                <div style="float:left; width:480px">
                    <div style="float:left; padding:5px 0 0 0">
						<div style=" width:44px; height:44px" class="<?php echo $mycss[0];?>"></div>
                    </div>
                    <div style="float:left; font-size:18px; padding:5px 0 0 10px">
                        <?php echo $row['nombre']?>
                    </div>
                </div>
                <div style="float:left; width:480px; padding:0 0 10px 60px; border-bottom:1px solid #CCC;">
                    <?php
                        while($row2=mysql_fetch_array($consul2)){
							/*if($row2['root']=='true' && $_SESSION['ROL'] != 1){
							//if($row2['root']=='true' && $_SESSION['ROL'] != 1){

								$esRoot = 'disabled';
							}else{
								$esRoot = '';
							}*/
                    ?>
                            <div style="float:left; width:100%">
                                <input id="checks_PERMISOS_<?php echo $row2['id'];?>" name="checks_PERMISOS" type="checkbox" value="<?php echo $row2['id'];?>" disabled <?php //echo $esRoot ?>/>
                                <?php
									if($row2['nivel']==2){echo '&nbsp;&nbsp;&nbsp;&nbsp;';}
									if($row2['nivel']==3){echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';}
									if($row2['nivel']==4){echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';}
									echo $row2['nombre'];
								?>
                            </div>
                    <?php
							if(user_permisos($row2['id']) == 'true' && $_SESSION["ROLVALOR"] < $rolvalor){
								echo "<script>if(document.getElementById('checks_PERMISOS_".$row2['id']."')){";
								echo "document.getElementById('checks_PERMISOS_".$row2['id']."').disabled = '';";
								echo "}</script>";
							}
							if($_SESSION["ROLVALOR"] == 0){
								echo "<script>if(document.getElementById('checks_PERMISOS_".$row2['id']."')){";
								echo "document.getElementById('checks_PERMISOS_".$row2['id']."').disabled = '';";
								echo "}</script>";
							}

                        }
                    ?>
                </div>
        <?php
            }
        ?>
    </div>
</form>

<script>
	//$elid == 1 && $_SESSION['ROL'] != 1
	<?php if($_SESSION["ROLVALOR"] >= $rolvalor && $_SESSION["ROLVALOR"] != 0){echo 'Ext.getCmp("BtnGuardaPermi").disable(); Ext.getCmp("BtnEliminaPermi").disable();';} ?>
	<?php
		while($row_permisos=mysql_fetch_array($consul_permisos)){
			echo "if(document.getElementById('checks_PERMISOS_". $row_permisos['id_permiso']."')){";
			echo "document.getElementById('checks_PERMISOS_". $row_permisos['id_permiso']."').checked = true;";
			echo "}";
		}
	?>

	if(document.getElementById('rolnivel')){
		document.getElementById('rolnivel').value = '<?php echo $rolvalor; ?>'
	}
</script>