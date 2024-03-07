<?php 
if($_POST['IdEmpresa']!=0){
	include("../configuracion/conectar.php");
	$consul = mysql_query("SELECT id,nombre FROM empresas_sucursales WHERE id_empresa = '$_POST[IdEmpresa]' AND activo = 1",$link);
?>
    <select name="sucursal" class="mytext"  id="sucursal" style="width:222px;" onChange="VerificaCampo2();">
        <option value="0">Sucursal ...</option>
        <?php while($row = mysql_fetch_array($consul)){ ?>
            <option value="<?php echo $row['id']; ?>" ><?php echo $row['nombre']; ?></option>
      	<?php }?>
    </select>
    
<?php }else{ ?>

    <select name="sucursal" class="mytext"  id="sucursal" style="width:222px;" onChange="VerificaCampo2();">
        <option value="0">Sucursal ...</option>
    </select>

<?php }?>

<script>
	VerificaCampo2();
</script>

