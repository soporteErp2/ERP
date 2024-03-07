<?php
	include("../../../../../configuracion/conectar.php");
	include("../../../../../configuracion/define_variables.php");
	include("../../../../../misc/MyGrilla/class.MyGrilla.php");

switch ($opc) {
	case 'cargarCampoBodega':
		cargarCampoBodega();
		break;

	case 'cargarCampoBodegaUpdate':
		cargarCampoBodegaUpdate($id,$link);
		break;

	case 'guardarBodega':
		guardarBodega($bodega,$idSucursal,$idEmpresa,$link);
		break;

	case 'actualizarBodega':
		actualizarBodega($id,$bodega,$idSucursal,$idEmpresa,$link);
		break;

	case 'eliminarBodega':
		eliminarBodega($id,$link);
		break;

	case 'optionCiudad':
		optionCiudad($id_sucursal,$id_departamento,$id_empresa,$link);
		break;

	case 'optionComuna':
		optionComuna($id_sucursal,$id_ciudad,$id_empresa,$link);
		break;

}

//========================== CARGAR VENTANA PARA AGREGAR UNA NUEVA BODEGA ==============================//
	function cargarCampoBodega() {
		echo'<div style="float:left; margin: 25px 0 0 20px">
				<div id="renderInserUpdateBodega" style="float:left; margin-left:-20px; width:16px; overflow:hidden; position:fixed;"></div>
			    <div style="float:left; width:45px; padding:3px 0 0 0">
			        Bodega
			    </div>
			    <div style="float:left; width:210px">
			    	<input type="tex" class="myfieldObligatorio" id="newNombreBodegaPanelControl" style="width:210px" rows="3" />
				</div>
			</div>';
	}

//========================== CARGAR VENTANA PARA ACTUALZAR UNA NUEVA BODEGA ==============================//
	function cargarCampoBodegaUpdate($id,$link) {
		$sql    = "SELECT nombre FROM empresas_sucursales_bodegas WHERE id='$id'";
		$query  = mysql_query($sql,$link);
		$bodega = mysql_result($query,0,'nombre');

		echo '<div style="float:left; margin: 25px 0 0 20px">
				<div id="renderInserUpdateBodega" style="float:left; margin-left:-20px; width:16px; overflow:hidden; position:fixed;"></div>
			    <div style="float:left; width:45px; padding:3px 0 0 0">
			        Bodega
			    </div>
			    <div style="float:left; width:210px">
			    	<input type="tex" class="myfieldObligatorio" id="newNombreBodegaPanelControl" style="width:210px" value="'.$bodega.'" rows="3" />
				</div>
			</div>';
	}
//====================================== GUARDAR UNA NUEVA BODEGA ======================================================//
	function guardarBodega($bodega,$idSucursal,$idEmpresa,$link){

		//----------------------------------------------- GUARDAMOS LA BODEGA -----------------------------------------//
		$sql      = "INSERT INTO empresas_sucursales_bodegas (id_empresa,id_sucursal,nombre) VALUES ('$idEmpresa','$idSucursal','$bodega')";
		$query    = mysql_query($sql,$link);
		$idBodega = mysql_insert_id();

		//--------------------------------------- GUARDAMOS LOS ARTICULOS A LA BODEGA--------------------------------//
		// Al crear una nueva bodega, insertamos en la tabla items totales de esa bodega, todos los articulos que estan
		// registrados en la empresa, y los insertamos con cantidades iguales a cero.

		$sqlArticulos   = "SELECT id FROM items WHERE id_empresa='$idEmpresa' AND activo = 1";
		$queryArticulos = mysql_query($sqlArticulos,$link);

		//tomamos un contador con el numero de articulos que posee la empresa
		$sqlCont   = "SELECT COUNT(id)as cont  FROM items WHERE id_empresa='$idEmpresa' AND activo = 1";
		$queryCont = mysql_query($sqlCont,$link);
		$cont      = mysql_result($queryCont,0,'cont');

		//contador de articulos insertados
		$contArticulos=0;

		// Recorremos todos los articulos de la empresa y los insertamos en la tabla items totales, asignandolos a la sucursal y bodega
		while ($row=mysql_fetch_array($queryArticulos)) {
			$id_articulo          = $row['id'];
			$sqlInsertArticulo    = "INSERT INTO inventario_totales (id_item,id_sucursal,id_ubicacion) VALUES ('$id_articulo','$idSucursal','$idBodega')";
			$queryInsertArticulos = mysql_query($sqlInsertArticulo,$link);

			if ($queryInsertArticulos) { $contArticulos ++; }
		}

		//verificar que se inserto la bodega
		if ($query) { 		//verificar si se insertaron todos los articulos a la bodega
			if($contArticulos==$cont){
				echo '<script>
						Inserta_Div_Bodega('.$idBodega.');
						Win_Ventana_Agregar_panelControlBodega.close();
					</script>';
			}
			else{ 			//Sino se insertaron todos los articulos mostrar error
				echo'<script>
						alert("Error!\nNo se insertaron todos los articulos a la bodega\nArticulos Empresa: '.$cont.', Insertados en bodega: '.$contArticulos.'\nNo insertados: '.($cont-$contArticulos).'\nSi el problema continua contacte al administrador del sistema ");
					</script>';
			}
		}
		else{ 				//sino se inserto la bodega
			echo'<script>
					alert("Se produjo un error y no se guardo la bodega\nSi el problema persiste comuniquese con el administrador del sistema");
				</script>';
		}

	}

//======================================= ACTUALIZAR BODEGA ==========================================//

	function actualizarBodega($id,$bodega,$idSucursal,$idEmpresa,$link){

		$sql   = "UPDATE empresas_sucursales_bodegas SET nombre='$bodega',id_sucursal='$idSucursal',id_empresa='$idEmpresa' WHERE id='$id' ";
		$query = mysql_query($sql,$link);

		if ($query) {
			echo'<script>
					Actualiza_Div_Bodega('.$id.');
					Win_Ventana_Aactualizar_panelControlBodega.close();
				</script>';
		}
		else{
			echo'<script>
					alert("Se produjo un error y no se Actualizo la bodega\nSi el problema persiste comuniquese con el administrador del sistema");
				</script>';
		}
	}

//======================================= ELIMINAR BODEGA ==========================================//
	function eliminarBodega($id,$link){
		// Se verifica que la bodega no tenga ningun articulo con cantidad mayor a 0, si lo tiene, no se elimina la bodega
		$sqlEliminaBodega   = "SELECT COUNT(id)as cont  FROM inventario_totales WHERE id_ubicacion='$id' AND cantidad>0";
		$queryEliminaBodega = mysql_query($sqlEliminaBodega,$link);
		$contArticulos      = mysql_result($queryEliminaBodega,0,'cont');

		if ($contArticulos<1) {
			$sql="UPDATE empresas_sucursales_bodegas SET activo='0' WHERE id='$id' ";
			$query=mysql_query($sql,$link);

			if ($query) {
				echo'<script>
						Elimina_Div_Bodega('.$id.');
						Win_Ventana_Aactualizar_panelControlBodega.close();
					</script>';
			}
			else{
				echo'<script>
						alert("Se produjo un error y no se Elimino la bodega\nSi el problema persiste comuniquese con el administrador del sistema");
					</script>';
			}
		}
		else{
			//si hay articulos con cantidad mayor a cero, no se puede eliminar la bodega
			echo'<script>
					alert("Error!\n No se puede eliminar la bodega\nEsto se debe a que hay articulos existentes en la bodega");
				</script>';
		}
	}

//================ CIUDAD ====================//
	function optionCiudad($id_sucursal,$id_departamento,$id_empresa,$link){
		$id_ciudadBD = 0;

		if($id_sucursal > 0){
			$sqlCiudadBd = "SELECT id_ciudad FROM empresas_sucursales WHERE id='$id_sucursal' LIMIT 0,1";
			$id_ciudadBD = mysql_result(mysql_query($sqlCiudadBd,$link), 0, 'id_ciudad');
		}

		$comboCiudad = '<select class="myfield" name="Sucursal_id_ciudad" id="Sucursal_id_ciudad" style="width:180px" onchange="ActualizaComboComunaSucursal(this.value)" >
							<option value="0">Seleccione...</option>';

		$sqlCiudades = "SELECT id,ciudad FROM ubicacion_ciudad WHERE id_departamento='$id_departamento' AND activo=1";
		$queryCiudades = mysql_query($sqlCiudades,$link);
		while ($row = mysql_fetch_array($queryCiudades)) {
			$selected    = ($id_ciudadBD == $row['id'])? 'selected': '';
			$comboCiudad .= '<option value="'.$row['id'].'" '.$selected.'>'.$row['ciudad'].'</option>';
		}

		echo $comboCiudad .= '</select>';
		// echo "<script>ActualizaComboComunaSucursal(document.getElementById('Sucursal_id_ciudad').value)</script>";

	}

//=========================== COMUNA ===========================================//
	function optionComuna($id_sucursal,$id_ciudad,$id_empresa,$link){
		$id_ciudadBD = 0;

		if($id_sucursal > 0){
			$sqlCiudadBd = "SELECT id_ciudad FROM empresas_sucursales WHERE id='$id_sucursal' LIMIT 0,1";
			$id_ciudadBD = mysql_result(mysql_query($sqlCiudadBd,$link), 0, 'id_ciudad');
		}

		$comboCiudad = '<select class="myfield" name="Sucursal_id_comuna" id="Sucursal_id_comuna" style="width:180px"  >
							<option value="0">Seleccione...</option>';

		$sqlCiudades = "SELECT id,comuna FROM ubicacion_comuna WHERE id_ciudad='$id_ciudad' AND activo=1";
		$queryCiudades = mysql_query($sqlCiudades,$link);
		while ($row = mysql_fetch_array($queryCiudades)) {
			$selected    = ($id_ciudadBD == $row['id'])? 'selected': '';
			$comboCiudad .= '<option value="'.$row['id'].'" '.$selected.'>'.$row['comuna'].'</option>';
		}

		echo $comboCiudad .= '</select>';
		// echo "<script>ActualizaComboComuna(document.getElementById('Sucursal_id_ciudad').value)</script>";

	}

 ?>