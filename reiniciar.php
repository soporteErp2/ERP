<?php
include("configuracion/conectar.php");
include("configuracion/define_variables.php");

if($opcion == 'GeneraCombo'){

	$consul_dat_user = mysql_query("SELECT id,id_sucursal,id_empresa,id_rol FROM empleados WHERE id = $_SESSION[IDUSUARIO]",$link);
	if(mysql_num_rows($consul_dat_user)){
		$sucursal = mysql_result($consul_dat_user,0,'id_sucursal');
		$empresa = mysql_result($consul_dat_user,0,'id_empresa');
		$rol = mysql_result($consul_dat_user,0,'id_rol');
		$ID	= mysql_result($consul_dat_user,0,'id');

		//DEFINE SI TIENE PERMISOS PARA ENTRA A LAS SUCURSALES Y EMPRESAS//////////////////////////////////////////////
		$consul_permisos = mysql_query("SELECT id_permiso FROM empleados_roles_permisos WHERE id_rol = $rol",$link);
		$permisos = array();
		while($row_permi = mysql_fetch_array($consul_permisos)){
			$permisos[]=$row_permi['id_permiso'];
		}
		if (in_array(1, $permisos)) { $MSucursales = 'true'; }
		else{ $MSucursales = 'false'; }

		if($MSucursales == 'true'){ $filtros = 'WHERE id_empresa = '.$empresa; }
		if($MSucursales == 'false'){ $filtros = 'WHERE id_sucursal = '.$sucursal.' AND id_empresa = '.$empresa; }

		$consulEmpre = $consul2 = mysql_query("SELECT * FROM vista_sucursales_empresas $filtros GROUP BY id_empresa",$link);
?>
		<style>
            .myFieldSucursal{
                color               : #333;
                font-weight         : bold;
                text-shadow         : 1px 1px 1px #FFF;
                font-family         : Verdana,sans-serif,Tahoma;
                width				: 260px;
            }
        </style>
    	<div style="float:left; width:350px; margin:20px;position: absolute;">
        	<div style="float:left; width:300px; margin:0 0 0 0;">
            	<div style="float:left; width:300px; font-size:12px">
                Seleccione la sucursal
                </div>
                <div style="float:left; width:300px; margin:5px 0 0 0;">
                <select name="FieldReiniciarEmpresa" class="myFieldSucursal"  id="FieldReiniciarEmpresa">
                    <?php while($rowEmpre = mysql_fetch_array($consulEmpre)){ ?>
                        <optgroup style="font-size:14px; font-style:normal; padding:0 0 0 5px;" label="<?php echo $rowEmpre['empresa']; ?>">
                        <?php
                            if($MSucursales == 'true'){ $filtros = 'WHERE id_empresa = '.$rowEmpre['id_empresa']; }
                            if($MSucursales == 'false'){ $filtros = 'WHERE id_sucursal = '.$sucursal.' AND id_empresa = '.$rowEmpre['id_empresa']; }
                            $consul2 = mysql_query("SELECT * FROM vista_sucursales_empresas $filtros ORDER BY empresa",$link);

                            while($row2 = mysql_fetch_array($consul2)){
                        ?>
                            <option style="font-size:14px; padding:0 0 0 20px;" value="<?php echo $row2['id_sucursal']; ?>" <?php if($row2['id_sucursal']==$sucursal){echo 'selected'; } ?> > <?php echo $row2['sucursal'] ?></option>
                        <?php } ?>
                        </optgroup>
                    <?php } ?>
                </select>
                <script>document.getElementById('FieldReiniciarEmpresa').value='<?php echo $_SESSION[SUCURSAL]; ?>';</script>
                </div>
        	</div>
            <div style="float:left; width:50px; cursor:pointer" onclick="reiniciar('reiniciar')">
                <img src="images/next.png" width="48" height="48" />
            </div>
        </div>

<?php
	}
}
	if($opcion == 'Reiniciar'){

		if(is_nan($_POST['sucursal'])){ exit; }
		$consul3 = mysql_query("SELECT *, COUNT(id_sucursal) AS contSucursal FROM vista_sucursales_empresas WHERE id_sucursal = $_POST[sucursal] AND id_empresa=$_SESSION[EMPRESA]",$link);

		if(mysql_result($consul3,0,"contSucursal") == 0){ exit; }

		$_SESSION["SUCURSAL"] = $_POST['sucursal'];
		$_SESSION["NOMBRESUCURSAL"] = mysql_result($consul3,0,"sucursal");
		$_SESSION["PAIS"] = mysql_result($consul3,0,"id_pais");
		$_SESSION["MONEDA"] = mysql_result($consul3,0,"id_moneda");
	}
?>
