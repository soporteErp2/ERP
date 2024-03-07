<?php
	include("../../../../configuracion/conectar.php");
	include("../../../../configuracion/define_variables.php");
	include("../../../../misc/MyGrilla/class.MyGrilla.php");

	$id_empresa = $_SESSION['EMPRESA'];

	switch ($opc) {

		case 'busquedaTerceroPaginacion':
			busquedaTerceroPaginacion($opcGrillaContable,$pagina,$limite,$limit,$rows_registros,$paginas,$id_tercero,$tercero,$nit,$whereSum,$tabla,$estado,$imprimeVar,$filtro,$link,$id_empresa,$where,$mysql);
			break;

		case "downloadFile":
			downloadFile($nameFile,$id_empresa);
			break;

		case "ventana_tercerosPDF":
			ventana_tercerosPDF($data);
			break;

		case 'cuerpoVentanaConfiguracionTercerosContactos':
			cuerpoVentanaConfiguracionTercerosContactos();
			break;

		case 'filtro_ubicacion':
			filtro_ubicacion($modulo);
			break;

		case 'OptionSelectDepartamento':
			OptionSelectDepartamento($id_pais,$link);
			break;

		case 'OptionSelectCiudad':
			OptionSelectCiudad($id_departamento,$link);
			break;

		default:
			# code...
			break;
	}

	//======================= FUNCION PARA PAGINAR LA BUSQUEDA DE LA VENTANA ========================================//
	function busquedaTerceroPaginacion($opcGrillaContable,$pagina,$limite,$limit,$rows_registros,$paginas,$id_tercero,$tercero,$nit,$whereSum,$tabla,$estado,$imprimeVar,$filtro,$link,$id_empresa,$where,$mysql){

		$whereEmpresa = '';
		if ($tabla=='terceros') { $nit='numero_identificacion AS nit';  }
		else if ($tabla=='empleados') { $nit='documento AS nit'; $whereEmpresa = 'AND id_empresa='.$id_empresa;}

		//SI LA VARIABLE FILTRO NO ESTA VACIA, RECONTAMOS EL LIMITE DE LOS REGISTROS
		if ($filtro!='') {
			$sql="SELECT COUNT(id) as cont $whereSum  FROM $tabla WHERE activo=1 $estado $filtro $whereEmpresa $where";
			$query=$mysql->query($sql,$link);
			$rows_registros=$mysql->result($query,0,'cont');
			$paginas=ceil( $rows_registros/$limite );

			//CREAR ARRAY CON LOS LIMITES DE LAS CONSULTAS
			$limit1     = 0;
			$limit2     = $limite;
			$acumScript = '';
			for ($i=1; $i <= $paginas; $i++) {
				$acumScript .='arrayLimitGrilla'.$opcGrillaContable.'['.$i.']="'.$limit1.','.$limit2.'";';
				$limit1     =$limit2+1;
				$limit2     =$limit2+$limite;
			}
		}
		//SI NO SE HACE LA BUSQUEDA CON FILTRO SINO DE FORMA NORMAL
		else{
			$sql="SELECT COUNT(id) as cont $whereSum  FROM $tabla WHERE activo=1 $estado $whereEmpresa $where";
			$query=$mysql->query($sql,$link);
			$rows_registros=$mysql->result($query,0,'cont');
			$paginas=ceil( $rows_registros/$limite );

			//CREAR ARRAY CON LOS LIMITES DE LAS CONSULTAS
			$limit1     = 0;
			$limit2     = $limite;
			$acumScript = '';
			for ($i=1; $i <= $paginas; $i++) {
				$acumScript .='arrayLimitGrilla'.$opcGrillaContable.'['.$i.']="'.$limit1.','.$limit2.'";';
				$limit1     =$limit2+1;
				$limit2     =$limit2+$limite;
			}
		}

		//SI SE BUSCA DESDE UNA PAGINA DIFERENTE A LA 1, VALIDAR SI EL RESULTADO DA LA MISMA CANTIDAD DE PAGINAS, SINO, PONER EN PAGINA 1 EJ(9 PAGINAS CONTRA EL RESULTADO DE 1 PAGINA)
		if ($pagina>$paginas) {
			$limit='0,'.$limite;
			$pagina=1;
		}

		$sqlCuentas   = "SELECT $id_tercero,$tercero,$nit $whereSum FROM $tabla WHERE activo=1 $estado $filtro $whereEmpresa $where GROUP BY $id_tercero ASC LIMIT $limit";
		$queryCuentas = $mysql->query($sqlCuentas,$link);
		while ($rowCuentas = $mysql->fetch_array($queryCuentas)) {
			$contFilaCuenta++;

			$divSaldoPendiente=($tabla!='terceros' && $tabla!="empleados")? '<div class="campo3" id="saldo_'.$contFilaCuenta.'">'.$rowCuentas['saldo'].'</div>' : '' ;

			$filaInsertBoleta .= '<div class="filaBoleta" id="fila_boleta_'.$opcGrillaContable.'_'.$contFilaCuenta.'">
									<div class="campo0 campoInforme0">'.$contFilaCuenta.'</div>
									<div class="campo1 campoInforme1" id="nit_'.$rowCuentas[$id_tercero].'">'.$rowCuentas['nit'].'</div>
									<div class="campo2 campoInforme2" style="border-left:0px;" id="tercero_'.$rowCuentas[$id_tercero].'" title="'.$rowCuentas[$tercero].'">'.$rowCuentas[$tercero].'</div>
									'.$divSaldoPendiente.'
									<div class="campo4 campoInforme4" id="valor_anticipo_'.$opcGrillaContable.'_'.$contFilaCuenta.'">
										<input type="checkbox" id="checkbox_'.$rowCuentas[$id_tercero].'" onchange="checkGrilla(this,\''.$rowCuentas[$id_tercero].'\',\''.$tabla.'\')" value="'.$rowCuentas[$id_tercero].'" >
									</div>
								  </div>';
		}

		$filaInsertBoleta .= '<script>
								// console.log("'.$sqlCuentas.'");
								// console.log(arrayLimitGrilla'.$opcGrillaContable.');
								document.getElementById("labelPaginacion").innerHTML="Pagina '.$pagina.' de '.$paginas.' ";
								PaginaActual'.$opcGrillaContable.'='.$pagina.';
								MaxPage'.$opcGrillaContable.'='.$paginas.';
								arrayLimitGrilla'.$opcGrillaContable.'.length=0;
								'.$acumScript.'
								// console.log(arrayLimitGrilla'.$opcGrillaContable.');
								// console.log("'.$limit.'");
								'.$imprimeVar.'
								document.getElementById("contenedor_tabla_boletas").width

							</script>
							<style>
								#contenedor_formulario{
									overflow   : hidden;
									width      : calc(100% - 30px);
									margin     : 15px;
									margin-top : 0px;
								}
							</style>';

			echo $filaInsertBoleta;

	}

	function downloadFile($nameFile,$id_empresa){
		$enlace = $_SERVER['DOCUMENT_ROOT'].'/ARCHIVOS_PROPIOS/archivos_erp/formatos_upload_terceros/'.$nameFile;

		if (file_exists($enlace)) {
			//header('Content-Disposition: attachment; filename='.basename($nameFile));
			header('Content-Disposition: attachment; filename='.$nameFile);
		    header('Content-Description: File Transfer');
		    header('Content-Type: application/octet-stream');
		    header('Expires: 0');
		    header('Cache-Control: must-revalidate');
		    header('Pragma: public');
		    header('Content-Length: '.filesize($enlace));
		    ob_clean();
		    flush();
		    readfile($enlace);
	    }
	    else{ echo "Error, el archivo no se encuentra almacenado"; }
	    exit;
	}

	function ventana_tercerosPDF($data){
		echo '<iframe style="width:100%; height:100%;" src="../terceros/terceros/reporte_terceros.php?IMPRIME_PDF=true&'.$data.'"></iframe>';

	}

	//======================filtro proveedores=====================//

	function cuerpoVentanaConfiguracionTercerosContactos(){
		$date = strtotime(date("Y-m-d"));
	    $anio = date("Y", $date);
	    $mes  = date("m", $date);
	    $dia  = date("d",$date);

	    //CALCULAR EL FINAL DEL MES
	    $fechaInicial = date("Y-m-d",(mktime(0,0,0,$mes,1,$anio)-1));
	    
	?>
		<div style="border-top:1px solid #8DB2E3; width:100%; overflow:hidden;" id="informeBalancePrueba">
			<!-- DIV MENU IZQUIERDO -->
			<div style="width: calc(100% - 215px - 3px); padding:0; float:left; height:270px; margin-left:5px;float:left;">
	
				<div style="overflow:visible;float:left; width:100%;margin-bottom:10px; margin-top:10px; text-align:center;border-bottom: 1px solid #99BBE8;height:39px">
					<div id="tab_divProveedores" class="x-panel-header" style="margin:2px 1px 0 0;font-size:9px !important;height:30px;width:97px;cursor:hand;float:left;border-bottom: none;padding: 3px 5px;" onClick="">TERCEROS
					</div>
					<div id="tab_divCcos" class="x-panel-header" style="display:none;margin:2px 1px 0 1;font-size:9px !important;height:30px;width:97px;cursor:hand;float:left;border-bottom: none;padding: 3px 5px;" onClick="abrirGrilla('divCcos')">CENTROS DE COSTOS
					</div>
				</div>
	
				<div style="width:100%;display:block" id="divProveedores">
					<div style="float:left; width:100%; font-weight:bolder; font-size:12px; margin-bottom:10px; margin-top:10px; text-align:center;">FILTRAR POR TERCERO(S)</div>
	
					<!-- VENTANA BUSCAR TERCERO -->
					<div style="display:none;background-color: #F3F3F3;border-right: 1px solid #D4D4D4;border-top: 1px solid #D4D4D4;float: left;height: 26px;width: 35px;border-top-right-radius: 5px;padding: 7 0 0 7;" >
						<img src="img/buscar20.png" onclick="ventanaBusquedaTercero();" style="cursor: pointer;width:21px;height:21px;margin-top:0px;" title="Buscar tercero" id="imgBuscarTerceroBC5">
					</div>
	
					<div style="width:100%;height:180px;background-color: #CDDBF0;overflow:hidden;">
						<div id="contenedor_formulario_configuracion" >
							<div id="contenedor_tabla_configuracion" style="height:178px;">
								<div class="headTablaBoletas">
	
									<div class="campo1" style="width: 129px;">Nit</div>
									<div class="campo2" style="width: 200px;">Nombre</div>
									<div class="campo4" style="width:25px;"><img src="img/buscar20.png" onclick="ventanaBusquedaTerceros('proveedores');" style="cursor: pointer;width:16px;height:16px;margin-top:0px;" title="Buscar tercero" id="imgBuscarTerceroBC"></div>
								</div>
								<div id="bodyTablaConfiguracionTerceros" style="height:140px;">
	
								</div>
							</div>
						</div>
					</div>
				</div>
				<div style="width:100%;display:none" id="divCcos">
					<div style="float:left; width:100%; font-weight:bolder; font-size:12px; margin-bottom:10px; margin-top:10px; text-align:center;">FILTRAR POR CENTROS DE COSTOS</div>
	
					<!-- VENTANA BUSCAR CENTRO DE COSTOS -->
					<div style="display:none;background-color: #F3F3F3;border-right: 1px solid #D4D4D4;border-top: 1px solid #D4D4D4;float: left;height: 26px;width: 35px;border-top-right-radius: 5px;padding: 7 0 0 7;" >
						<img src="img/buscar20.png" onclick="ventanaBusquedaCentroCostos();" style="cursor: pointer;width:21px;height:21px;margin-top:0px;" title="Buscar tercero" id="imgBuscarTerceroBC5">
					</div>
	
					<div style="width:100%;height:180px;background-color: #CDDBF0;overflow:hidden;">
						<div id="contenedor_formulario_configuracion" >
							<div id="contenedor_tabla_configuracion" style="height:178px;">
								<div class="headTablaBoletas">
									<div class="campo0"><img src="img/buscar20.png" onclick="ventanaBusquedaTerceroTurnos('centro_costos');" style="cursor: pointer;width:16px;height:16px;margin-top:0px;" title="Buscar centro de costos" id="imgBuscarTerceroBC"></div>
									<div class="campo1">Codigo</div>
									<div class="campo2" style="width: 200px;">Centro de Costos</div>
									<div class="campo4" style="width:25px;">&nbsp;</div>
								</div>
								<div id="bodyTablaConfiguracionCentroCostos" style="height:140px;">
	
								</div>
	
							</div>
						</div>
					</div>
				</div>
			</div>
			<!-- DIV MENU DERECHO -->
			<div style="float:right; width:210px; background-color: #eff3fa; height: 100%; border-left:1px solid #8DB2E3;">	
				<div style="margin-bottom:25px; font-weight: bolder; font-size:12px; text-align:center; border-right:0px; border-left:none;border-top:none;" class="x-panel-header">Fechas de Creacion</div>
				<div style="display:table; margin:auto;">
					<div style="overflow:hidden;" id="divFechaInicio">
						<div style="float:left; width:70px; margin-right:5px">Fecha Inicial:</div>
						<div style="float:left; width:120px;"><input type="text" id="MyInformeFiltroFechaInicio"></div>
					</div>
					<div style="overflow:hidden; margin-top:20px;">
						<div style="float:left; width:70px; margin-right:5px">Fecha Final:</div>
						<div style="float:left; width:120px;"><input type="text" id="MyInformeFiltroFechaFinal"></div>
					</div>
				</div>			
				<div style="margin-bottom:15px;margin-top: 20px; font-weight: bolder; font-size:12px; text-align:center; border-right:0px; border-left:none;" class="x-panel-header">Filtros Adicionales</div>
				<div style="margin-left:10px; overflow:auto;height:220px;">
	
					<div style="margin-bottom:6px; overflow:hidden;">
						<input type="radio" name="tipo_tercero" value="todos"  style="float:left; width:30px" >
						<div style="float:left;">Todos</div>
					</div>
	
					<div style="margin-bottom:6px; overflow:hidden;">
						<input type="radio" name="tipo_tercero" value="clientes"  style="float:left; width:30px" >
						<div style="float:left;">Clientes</div>
					</div>				
	
					<div style="margin-bottom:8px; overflow:hidden;">
						<input type="radio" name="tipo_tercero" value="proveedores"  style="float:left; width:30px" >
						<div style="float:left;">Proveedores</div>
					</div>				
	
					<div style="padding-left:8px;overflow:hidden;">
						<div style="padding-top:3px;height:14px">
							<input type="checkbox" id="sin_contactos" onclick="checksSinContactos(this);">&nbsp;&nbsp; Solo Terceros sin Contactos
						</div>
					</div>	
					<div style="padding-left:8px;overflow:hidden;">
						<div style="padding-top:3px;height:14px">
							<input type="checkbox" id="con_contactos" onclick="checksConContactos(this);">&nbsp;&nbsp; Solo Terceros con Contactos
						</div>
					</div>				
				</div>		
			</div>
	<script>
	
		new Ext.form.DateField({
			format     : "Y-m-d",
			width      : 120,
			id         :"cmpFechaInicio",
			allowBlank : false,
			showToday  : false,
			applyTo    : "MyInformeFiltroFechaInicio",
			editable   : false,
			value      : '<?php echo $fechaInicial ?>'
	  	    // listeners  : { select: function() {   } }
	  	});
	
		new Ext.form.DateField({
			format     : "Y-m-d",
			width      : 120,
			allowBlank : false,
			showToday  : false,
			applyTo    : "MyInformeFiltroFechaFinal",
			editable   : false,
			value      : new Date(),
	  	    // listeners  : { select: function() {   } }
	  	});
	
	  	if (typeof(localStorage.MyInformeFiltroFechaInicioTC)!="undefined") {
			if (localStorage.MyInformeFiltroFechaInicioTC!="") {
				document.getElementById("MyInformeFiltroFechaInicio").value=localStorage.MyInformeFiltroFechaInicioTC;
			}
		}
	
		if (typeof(localStorage.MyInformeFiltroFechaFinalTC)!="undefined") {
			if (localStorage.MyInformeFiltroFechaFinalTC!="") {
				document.getElementById("MyInformeFiltroFechaFinal").value=localStorage.MyInformeFiltroFechaFinalTC;
			}
		}
	
		//CREAMOS LOS DIV DE LOS PROVEEDORES AÑADIDOS
		for ( i = 0; i < arrayproveedoresTC.length; i++) {
			if (typeof(arrayproveedoresTC[i])!="undefined" && arrayproveedoresTC[i]!="") {
	
				//CREAMOS EL DIV DE LA FILA EN LA TABLA DE CONFIGURAR
	    		var div   = document.createElement("div");
	    		div.setAttribute("id","fila_cartera_tercero_"+i);
	    		div.setAttribute("class","filaBoleta");
	    		document.getElementById("bodyTablaConfiguracionTerceros").appendChild(div);
	
	    		//CREAMOS LOS ELEMENTOS DEL ELEMENTO CREADO
	    		document.getElementById("fila_cartera_tercero_"+i).innerHTML=proveedoresConfiguradosTC[i];
	
			}
		}
			
	
		activeTab = "";
	
		function abrirGrilla(valor){
	
			if(activeTab == ""){
				activeTab = "divCcos";
			}
			document.getElementById(activeTab).style.display       = "none";
			document.getElementById(valor).style.display           = "block";
			document.getElementById("tab_"+valor).style.margin     = "3px 1px 0 1px";
			document.getElementById("tab_"+activeTab).style.margin = "2px 1px 0 1px";
	
			document.getElementById("tab_"+activeTab).style.setProperty("background-color", "", "important");
			document.getElementById("tab_"+activeTab).style.setProperty("background-image", "", "important");
			document.getElementById("tab_"+activeTab).style.setProperty("background", "","important");
			document.getElementById("tab_"+activeTab).style.setProperty("font-size", "bold", "important");
	
			activeTab = valor;
		}
		abrirGrilla("divProveedores");
		
	
		function checksSinContactos(valor){
			if(valor.checked == true){
				checkboxSinContactos="true";
			}
			else{
				checkboxSinContactos="";
			}
		}	
	
		function checksConContactos(valor){
			if(valor.checked == true){
				checkboxConContactos="true";
			}
			else{
				checkboxConContactos="";
			}
		}
	
		
		if (checkboxSinContactos=="true") {
			document.getElementById("sin_contactos").checked=true;		
		}
	
		if (checkboxConContactos=="true") {
			document.getElementById("con_contactos").checked=true;		
		}
	
		var elementos = document.getElementsByName("tipo_tercero");
	
		if (typeof(localStorage.tipo_tercero)!="undefined") {
			if (localStorage.tipo_tercero!="") {
				for(var i=0; i<elementos.length; i++) {
					if (elementos[i].value==localStorage.tipo_tercero) {elementos[i].checked=true;}
				}			
			}else{
				elementos[0].checked=true;
			}
		}
		else{
			elementos[0].checked=true;
		}
	
	</script>
	
	<?php
	}

	function filtro_ubicacion($modulo){

		$sql2    = "SELECT id_pais FROM empresas WHERE id = '$_SESSION[EMPRESA]'";
		$query2  = $GLOBALS['mysql']->query($sql2);
		$id_pais = $GLOBALS['mysql']->result($query2,0,'id_pais');

		$sql   = "SELECT id,pais FROM ubicacion_pais WHERE activo = 1  ORDER BY pais ASC";
		$query = $GLOBALS['mysql']->query($sql);
		while($rowT = $GLOBALS['mysql']->fetch_array($query)){
			$selected = ($id_pais == $rowT['id'])? 'selected': '';

        	$options .= '<option value="'.$rowT['id'].'" '.$selected.'>'.$rowT['pais'].'</option>';

    	} 	



		echo '<div style="width:100%">	
				  <div style="width:100%;height:24px;padding-bottom:3px;">
				  	<div style="float:left;padding-bottom:3px;padding-top:5px;width:75px">Pais</div>
				  		<select  name="select_pais" value="todas" id="select_pais"  style="border-color:#A09E9E;border-radius:5px;font-size: 12;float:left; width:180px" onchange="changeDepartamento(this.value)">
				  			<option value="">Seleccione..</option>
				  			'.$options.'
				  		</select>
				  	</div>
				  </div>
				  <div style="width:100%;padding-bottom:3px;height:24px">
				  	<div style="float:left;padding-bottom:3px;padding-top:5px;width:75px">Departamento</div>
				  	<div style="float:left" id="divDepartamento">
				  		<select  name="select_departamento" value="todas" id="select_departamento"  style="border-color:#A09E9E;border-radius:5px;font-size: 12;float:left; width:180px">
				  			<option value="todos">TODOS</option>						
				  		</select>
				  	</div>
				  </div>
				  <div style="width:100%;height:24px">
				  	<div style="float:left;padding-bottom:3px;padding-top:5px;width:75px">Ciudad</div>
				  	<div style="float:left" id="divCiudad">
				  		<select  name="select_ciudad" value="todas" id="select_ciudad"  style="border-color:#A09E9E;border-radius:5px;font-size: 12;float:left; width:180px">
				  			<option value="todos">TODAS</option>						
				  		</select>
				  	</div>
				  </div>
			   </div>
			   <script>
			   		function changeDepartamento(id_pais){			   			
						Ext.get("divDepartamento").load({
							url		: "../informes/informes/crm/bd.php",
							timeout : 180000,
							scripts	: true,
							nocache	: true,
							params	:
							{
								opc      : "OptionSelectDepartamento",							
								id_pais  : id_pais,
								modulo   : \''.$modulo.'\'
							}
						});
			   		}
			   		function changeCiudad(id_departamento){			   			
						Ext.get("divCiudad").load({
							url		: "../informes/informes/crm/bd.php",
							timeout : 180000,
							scripts	: true,
							nocache	: true,
							params	:
							{
								opc             : "OptionSelectCiudad",							
								id_departamento : id_departamento,
								modulo          : \''.$modulo.'\'
							}
						});
			   		}			   		
			   		changeDepartamento(document.getElementById("select_pais").value);
			   </script>';
	}

	function OptionSelectDepartamento($id_pais,$link){

		echo'<select  name="select_departamento" value="todas" id="select_departamento"  style="border-color:#A09E9E;border-radius:5px;font-size: 12;float:left; width:180px" onchange="changeCiudad(this.value)">
				<option value="todos">TODOS</option>';

		if($id_pais>=1){
			$SQL    = "SELECT id,departamento FROM ubicacion_departamento WHERE id_pais= $id_pais AND activo=1  ORDER BY departamento ASC";
			$consul = $GLOBALS['mysql']->query($SQL,$link);
			while($row = $GLOBALS['mysql']->fetch_array($consul)){
				//$selected = ($id_departamentoDB == $row['id'])? 'selected': '';
				echo '<option value="'.$row['id'].'" '.$selected.'>'.$row['departamento'].'</option>';
			}
		}		
	}

	function OptionSelectCiudad($id_departamento,$link){

		echo'<select  name="select_ciudad" value="todas" id="select_ciudad"  style="border-color:#A09E9E;border-radius:5px;font-size: 12;float:left; width:180px" >
				<option value="todos">TODAS</option>';

		if($id_departamento>=1){
			$SQL    = "SELECT id,ciudad FROM ubicacion_ciudad WHERE id_departamento= $id_departamento AND activo=1  ORDER BY ciudad ASC";
			$consul = $GLOBALS['mysql']->query($SQL,$link);
			while($row = $GLOBALS['mysql']->fetch_array($consul)){			
				echo '<option value="'.$row['id'].'" '.$selected.'>'.$row['ciudad'].'</option>';
			}
		}		
	}

?>