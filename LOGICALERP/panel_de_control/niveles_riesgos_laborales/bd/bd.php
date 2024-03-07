<?php
	include("../../../../configuracion/conectar.php");
	include("../../../../configuracion/define_variables.php");

	$empresa=$_SESSION['EMPRESA'];

	switch ($op) {

		// INSERT NUEVA CUENTA DE BANCO
		case 'divCuerpoInsert_PanelControlBancos':
			divCuerpoInsert_PanelControlBancos();
			break;

		case 'saveInsertCuentaBancos':
			saveInsertCuentaBancos($newNombre,$empresa,$link);
			break;

		// UPDATE NUEVA CUENTA DE BANCO
		case 'divCuerpoUpdate_PanelControlBancos':
			divCuerpoUpdate_PanelControlBancos($id,$link);
			break;

		case 'saveUpdateCuentaBancos':
			saveUpdateCuentaBancos($newNombre,$id,$link);
			break;

		// ELIMINA CUENTA BANCO
		case 'saveEliminaCuentaBancos':
			saveEliminaCuentaBancos($id,$link);
			break;

		case 'guardarConfiguracionARL':
			guardarConfiguracionARL($codigo,$empresa,$mysql);
			break;
	}

	//================================== INSERT NUEVA CUENTA DE BANCO =======================================//

	function divCuerpoInsert_PanelControlBancos(){
		echo'<div style="float:left; margin: 25px 0 0 20px">
				<div id="renderInserUpdateBanco"></div>
			    <div style="float:left; width:80px; padding:3px 0 0 0">
			        Nombre <br/>de la cuenta
			    </div>
			    <div style="float:left; width:210px">
			    	<textarea class="myfieldObligatorio" id="newNombreBancoPanelControl" style="width:210px" rows="3" placeholder="Detalle aqui la cuenta"></textarea>
				</div>
			</div>';
	}

	function saveInsertCuentaBancos($newNombre,$empresa,$link){
		$cont                   = 11100501;
		$cuentaHabil            = 'false';
		$sqlUltimaCuentaBanco   = "SELECT cuenta FROM puc WHERE cuenta > 11100500 AND cuenta <= 11100599 AND id_empresa='$empresa' AND activo=1 ORDER BY cuenta ASC";
		$queryUltimaCuentaBanco = mysql_query($sqlUltimaCuentaBanco,$link);

		while($row = mysql_fetch_array($queryUltimaCuentaBanco)){
			if($cont != $row['cuenta']){ $cuentaHabil = 'true'; break; }
			$cont++;
		}

		if($cuentaHabil == 'false' && $cont==11100600){
			echo'<div>EXCEDIO SU LIMITE PARA GENERAR CUENTAS BANCARIAS</div>';
		}
		else{
			$sqlInsert      = "INSERT INTO puc (id_empresa,cuenta,descripcion)
								VALUES ('$empresa','$cont','$newNombre')";
			$queryInsert    = mysql_query($sqlInsert,$link);
			$ultimoIdInsert = mysql_insert_id();

			echo '<script>
					Inserta_Div_panelControlBancos("'.$ultimoIdInsert.'");
					Win_Ventana_Agregar_panelControlBancos.close();
				</script>';
		}
	}

	//==================================== UPDATE CUENTA DE BANCO =========================================//

	function divCuerpoUpdate_PanelControlBancos($id,$link){
		$nombreCuenta = '';
		$sql          = "SELECT descripcion FROM puc WHERE id='$id' AND activo = 1 LIMIT 0,1";
		$nombreCuenta = mysql_result(mysql_query($sql,$link),0,'descripcion');

		echo'<div style="float:left; margin: 25px 0 0 20px">
				<div id="renderInserUpdateBanco"></div>
			    <div style="float:left; width:80px; padding:3px 0 0 0">
			        Nombre <br/>de la cuenta
			    </div>
			    <div style="float:left; width:210px">
				    <textarea class="myfieldObligatorio" id="newNombreBancoPanelControl" style="width:210px" rows="3">'.$nombreCuenta.'</textarea>
				</div>
			</div>';
	}

	function saveUpdateCuentaBancos($newNombre,$id,$link){
		$sqlUpdate   = "UPDATE puc SET descripcion='$newNombre' WHERE id='$id' AND activo=1";
		$queryUpdate = mysql_query($sqlUpdate,$link);

		echo'<script>
				Actualiza_Div_panelControlBancos("'.$id.'");
				Win_Ventana_Editar_panelControlBancos.close();
			</script>';
	}

	//==================================== ELIMINA CUENTA DE BANCO =========================================//

	function saveEliminaCuentaBancos($id,$link){
		$sqlDelete   = "UPDATE puc SET activo=0 WHERE id='$id' AND activo=1";
		$queryDelete = mysql_query($sqlDelete,$link);

		echo '<script>
				Elimina_Div_panelControlBancos("'.$id.'");
				Win_Ventana_Editar_panelControlBancos.close();
			</script>';
	}

	function guardarConfiguracionARL($codigo,$id_empresa,$mysql){
		$sql="SELECT id FROM configuracion_arl WHERE activo=1 AND id_empresa=$id_empresa";
		$query=$mysql->query($sql,$mysql->link);
		$id_config = $mysql->result($query,0,'id');

		if ($id_config>0) {
			$sql="UPDATE configuracion_arl SET codigo='$codigo' WHERE activo=1 AND id_empresa=$id_empresa AND id=$id_config";
			$query=$mysql->query($sql,$mysql->link);
		}
		else{
			$sql="INSERT INTO configuracion_arl (codigo,id_empresa) VALUES ('$codigo',$id_empresa) ";
			$query=$mysql->query($sql,$mysql->link);
		}
		if ($query) {
			echo 'true';
		}
		else{
			echo 'false';
		}

	}

?>