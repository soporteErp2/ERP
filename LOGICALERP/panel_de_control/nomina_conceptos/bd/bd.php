<?php
	include("../../../../configuracion/conectar.php");
	include("../../../../configuracion/define_variables.php");

	$id_empresa  = $_SESSION['EMPRESA'];

	switch ($opc) {
		case 'consultarCuentaColgaap':
			consultarCuentaColgaap($tablaAsientos,$cuenta,$id_empresa,$link);
			break;

		case 'sincronizaPucPagoNiif':
			sincronizaPucPagoNiif($campoId,$campoText,$cuenta,$id_empresa,$link);
			break;
		case 'ventanaFormulaConcepto':
			ventanaFormulaConcepto($opcion,$id,$formula,$id_empresa,$link);
			break;
		case 'ventanaFormulaConceptoLiquidacion':
			ventanaFormulaConceptoLiquidacion($opcion,$id,$formula,$id_empresa,$link);
			break;
		case 'niveles_formula':
			niveles_formula($opcion,$id,$id_empresa,$link);
			break;
		case 'niveles_formula_liquidacion':
			niveles_formula_liquidacion($opcion,$id,$id_empresa,$link);
			break;
		case 'cargaConceptosNiveles':
			cargaConceptosNiveles($opcion,$id,$nivel,$id_empresa,$link);
			break;
		case 'cargaConceptosNivelesLiquidacion':
			cargaConceptosNivelesLiquidacion($opcion,$id,$nivel,$id_empresa,$link);
			break;
		case 'select_restar_ct':
			select_restar_ct($id_empresa,$link);
			break;
		case 'updateRestarCt':
			updateRestarCt();
			break;
		case 'agregar_concepto_liquidacion':
			agregar_concepto_liquidacion($id_concepto,$id_concepto_base,$id_empresa,$link);
			break;
		case 'eliminar_concepto_liquidacion':
			eliminar_concepto_liquidacion($id,$id_empresa,$link);
			break;
		case 'validaCuentas':
			validaCuentas($json_cuentas,$mysql);
			break;

	}

	function consultarCuentaColgaap($tablaAsientos,$cuenta,$id_empresa,$link){

		$sqlCuentaColgaap = "SELECT id
							FROM $tablaAsientos
							WHERE activo = 1
								AND id_empresa = '$id_empresa'
								AND codigo_cuenta = '$cuenta'
							LIMIT 0,1";
		$contCuenta = mysql_result(mysql_query($sqlCuentaColgaap,$link),0,'id');
		if($contCuenta > 0){ echo 'true'; return; }

		// $and_where = ($cuenta > 999999)? "AND codigo_cuenta = LEFT('$cuenta',6)": "AND LEFT(codigo_cuenta,6) = '$cuenta'";
		// $sqlValidacion = "SELECT id
							// FROM $tablaAsientos
							// WHERE activo = 1
								// AND id_empresa = '$id_empresa'
								// $and_where
							// LIMIT 0,1";

		// $contValidacion = mysql_result(mysql_query($sqlValidacion,$link),0,'id');
		// if($contValidacion > 0){ echo 'false'; return; }
		echo 'true'; return;
	}

	function sincronizaPucPagoNiif($campoId,$campoText,$cuenta,$id_empresa,$link){
		$sqlNiif   = "SELECT COUNT(PN.id) AS cont_niif, PN.descripcion, P.cuenta_niif,PN.id
						FROM puc AS P, puc_niif AS PN
						WHERE P.activo=1
							AND P.cuenta='$cuenta'
							AND P.id_empresa='$id_empresa'
							AND PN.activo=1
							AND PN.id_empresa=P.id_empresa
							AND PN.cuenta=P.cuenta_niif
							LIMIT 0,1";
		$queryNiif = mysql_query($sqlNiif,$link);

		$contNiif        = mysql_result($queryNiif,0,'cont_niif');
		$cuentaNiif      = mysql_result($queryNiif,0,'cuenta_niif');
		$descripcionNiif = mysql_result($queryNiif,0,'descripcion');
		$id_niif = mysql_result($queryNiif,0,'id');

		if($contNiif == 0){ echo'<script>alert("No existe una cuenta niif asociada a la cuenta colgaap No. '.$cuenta.'");</script>'; }
		else{ echo '<script>
						document.getElementById("'.$campoText.'").value = "'.$cuentaNiif.'";
						document.getElementById("'.$campoId.'").value    = "'.$id_niif.'";
					</script>'; }

		echo'<img src="img/refresh.png" />';
	}

	// VENTANA PARA CONFIGURACION DE LAS FORMULAS DE LOS CONCEPTOS
	function ventanaFormulaConcepto($opcion,$id,$formula,$id_empresa,$link){
		$whereId='';
		$nivel=1;
		$whereNivel=' AND nivel_formula<'.$nivel;
		if ($opcion=='Vupdate') {
			$whereId=' AND id<>'.$id;
			//SI VA A ACTUALIZAR, CONSULTAR LOS CONCEPTOS CON NIVELES INFERIORES AL QUE TIENE
			$sql="SELECT nivel_formula FROM nomina_conceptos WHERE activo=1 AND id_empresa=$id_empresa AND id=$id";
			$query=mysql_query($sql,$link);
			$nivel=mysql_result($query,0,'nivel_formula');
			$whereNivel=' AND nivel_formula<'.$nivel;
		}
		// CONSULTAR LOS CONCEPTOS GUARDADOS EN EL SISTEMA
		echo $sql="SELECT codigo,descripcion,tipo_concepto FROM nomina_conceptos WHERE activo=1 AND id_empresa=$id_empresa AND nivel_formula>0 $whereId $whereNivel";
		$query=mysql_query($sql,$link);
		$bodyGeneral='';
		$bodyPersonal='';
		while ($row=mysql_fetch_array($query)) {
			if ($row['tipo_concepto']=='General') {
				$bodyGeneral.='<div class="filaBoleta" style="">
									<div class="campo1" ondblclick="insertarConceptos(\'['.$row['codigo'].']\')" style="height:20px;border-right:none;width:41px;border-bottom:1px solid #d4d4d4;border-right:1px solid #d4d4d4;">'.$row['codigo'].'</div>
									<div class="campo1" ondblclick="insertarConceptos(\'['.$row['codigo'].']\')" style="height:20px;border-right:none;width:calc(100% - 42px);border-bottom:1px solid #d4d4d4;">'.$row['descripcion'].'</div>
								</div>';
			}
			else{
				$bodyPersonal.='<div class="filaBoleta">
									<div class="campo1" ondblclick="insertarConceptos(\'['.$row['codigo'].']\')" style="height:20px;border-right:none;width:41px;border-bottom:1px solid #d4d4d4;border-right:1px solid #d4d4d4;">'.$row['codigo'].'</div>
									<div class="campo1" ondblclick="insertarConceptos(\'['.$row['codigo'].']\')" style="height:20px;border-right:none;width:calc(100% - 42px);border-bottom:1px solid #d4d4d4;">'.$row['descripcion'].'</div>
								</div>';
			}
		}

		echo '<style>
				.filaBoleta:hover{background-color:#F3F3F3;}
				spam{
					color:blue;
					font-weight:bold;
				}
				spam:hover .close{
					display:block;
				}
				spam:hover {
					display:none;
				}
				.close {
					display:none;
					margin-top: -15;
					margin-left: 24;
					position: fixed;
					cursor: pointer;
					color: #000;
					text-align: center;
					width: 18px;
					text-decoration: none;
					font-weight: bold;
					border-radius: 3px;
					font-size: 15px;
					background-image: url(img/close.png);
					background-repeat: no-repeat;
					height: 15px;
					background-size: contain;
				}


			</style>
			<div style="width:100%;height:100%;" id="contenedorConceptosFormula">
				<div style="margin:auto;width:90%;height:100%;/*border:1px solid;*/">
					<div>
						<!--	<div contenteditable="true" spellcheck="false" style="width:100%;height:25px;background-color:#FFF;padding-top: 10px;" onchange="validaCaracterFormula(event,this)" onkeyup="validaCaracterFormula(event,this)">
							ddd <spam contenteditable="false"><div title="Eliminar Concepto" class="close"></div>[HE]</spam>&nbsp;
						</div>-->
						<input type="search" class="myfield" style="width:100%;height:35px;" placeholder="Ingrese la formula..." value="'.$formula.'" id="formula_concepto" onkeydown="validaCaracterFormula(event,this)">
					</div>
					<div style="margin-top: 10px;height: calc(100% - 50px);margin-top: 10px;border:1px solid #d4d4d4;">
						<div style="width:50%;height:100%;float:left;border-right:1px solid #d4d4d4;background-color:#FFF;">
							<div class="headTablaBoletas">
								<div class="filaBoleta">
									<div class="campo1" style="width:100%;border-bottom:1px solid #d4d4d4;">Conceptos Generales</div>
									<div class="campo1" style="width:41px;">Cod.</div>
									<div class="campo1" style="width:calc(100% - 42px);border-right:none;">Concepto</div>
								</div>
							</div>
							<div class="bodyTablaBoletas" style="height: calc(100% - 30px);overflow: auto;">
								'.$bodyGeneral.'
							</div>
						</div>
						<div style="width:calc(50% - 1px);height:100%;float:left;background-color:#FFF;">
							<div class="headTablaBoletas">
								<div class="filaBoleta">
									<div class="campo1" style="width:100%;border-bottom:1px solid #d4d4d4;">Variables del Sistema</div>
									<div class="campo1" style="width:41px;">Cod.</div>
									<div class="campo1" style="width:calc(100% - 42px);border-right:none;">Concepto</div>
								</div>
							</div>
							<div class="bodyTablaBoletas" style="height: 84px;overflow: auto;">
								<div class="filaBoleta">
									<div class="campo1" ondblclick="insertarConceptos(\'{DL}\')" style="height:20px;border-right:none;width:41px;border-bottom:1px solid #d4d4d4;border-right:1px solid #d4d4d4;">DL</div>
									<div class="campo1" ondblclick="insertarConceptos(\'{DL}\')" style="height:20px;border-right:none;width:calc(100% - 42px);border-bottom:1px solid #d4d4d4;">DIAS LABORADOS PLANILLA</div>
								</div>
								<div class="filaBoleta">
									<div class="campo1" ondblclick="insertarConceptos(\'{SC}\')" style="height:20px;border-right:none;width:41px;border-bottom:1px solid #d4d4d4;border-right:1px solid #d4d4d4;">SC</div>
									<div class="campo1" ondblclick="insertarConceptos(\'{SC}\')" style="height:20px;border-right:none;width:calc(100% - 42px);border-bottom:1px solid #d4d4d4;">SALARIO DEL CONTRATO</div>
								</div>
								<div class="filaBoleta">
									<div class="campo1" ondblclick="insertarConceptos(\'{NRL}\')" style="height:20px;border-right:none;width:41px;border-bottom:1px solid #d4d4d4;border-right:1px solid #d4d4d4;">NRL</div>
									<div class="campo1" ondblclick="insertarConceptos(\'{NRL}\')" style="height:20px;border-right:none;width:calc(100% - 42px);border-bottom:1px solid #d4d4d4;">NIVEL DE RIESGO LABORAl</div>
								</div>
								<div class="filaBoleta">
									<div class="campo1" ondblclick="insertarConceptos(\'{CT}\')" style="height:20px;border-right:none;width:41px;border-right:1px solid #d4d4d4;">CT</div>
									<div class="campo1" ondblclick="insertarConceptos(\'{CT}\')" style="height:20px;border-right:none;width:calc(100% - 42px);">CAMPO DE TEXTO</div>
								</div>
							</div>

							<div class="headTablaBoletas" style="border-top:1px solid #d4d4d4;">
								<div class="filaBoleta">
									<div class="campo1" style="width:100%;border-bottom:1px solid #d4d4d4;">Conceptos Personales</div>
									<div class="campo1" style="width:41px;">Cod.</div>
									<div class="campo1" style="width:calc(100% - 42px);border-right:none;">Concepto</div>
								</div>
							</div>
							<div class="bodyTablaBoletas" style="height: calc(100% - 200px);overflow: auto;">
								'.$bodyPersonal.'
							</div>
						</div>
					</div>
				</div>
			</div>

			<script>
				document.getElementById("formula_concepto").onkeypress = function(event){return validaCaracterFormula(event);};
				// console.log("'.$nivel.'");

				function insertarConceptos(codigo){
					document.getElementById("formula_concepto").value+=codigo+" ";
					document.getElementById("formula_concepto").focus();
				}

				function validaCaracterFormula(e){
					tecla = (document.all)?e.keyCode:e.which;
					if (tecla==8 		//BACKSPACE
					 	|| tecla==9 	//TAB
					 	|| tecla==0 	//TAB
					 	|| tecla==13 	//ENTER
					 	|| tecla==46 	//.
					 	|| tecla==40 	//(
					 	|| tecla==41 	//)
					 	|| tecla==43 	//+
					 	|| tecla==45 	//-
					 	|| tecla==47 	///
					 	|| tecla==42 	//*
					 	) return true;
					patron = /\d/;
					te = String.fromCharCode(tecla);
					return patron.test(te);
				}

				function cargarConceptosNiveles(nivel)
				{
					Ext.get("contenedorConceptosFormula").load({
						url     : "nomina_conceptos/bd/bd.php",
						scripts : true,
						nocache : true,
						params  :
						{
							opc    : "cargaConceptosNiveles",
							opcion : "'.$opcion.'",
							id     : "'.$id.'",
							nivel  : document.getElementById("nivel_formula").value,
						}
					});
				}

			</script>

			';
	}

	// VENTANA PARA CONFIGURACION DE LAS FORMULAS DE LOS CONCEPTOS
	function ventanaFormulaConceptoLiquidacion($opcion,$id,$formula,$id_empresa,$link){
		$whereId    = '';
		$nivel      = 1;
		$whereNivel = ' AND nivel_formula_liquidacion<'.$nivel;
		if ($opcion=='Vupdate') {
			$whereId=' AND id<>'.$id;
			//SI VA A ACTUALIZAR, CONSULTAR LOS CONCEPTOS CON NIVELES INFERIORES AL QUE TIENE
			$sql="SELECT nivel_formula_liquidacion FROM nomina_conceptos WHERE activo=1 AND id_empresa=$id_empresa AND id=$id";
			$query=mysql_query($sql,$link);
			$nivel=mysql_result($query,0,'nivel_formula_liquidacion');
			$whereNivel=' AND nivel_formula_liquidacion<'.$nivel;
		}
		// CONSULTAR LOS CONCEPTOS GUARDADOS EN EL SISTEMA
		$sql="SELECT codigo,descripcion,tipo_concepto,naturaleza FROM nomina_conceptos WHERE activo=1 AND id_empresa=$id_empresa $whereId $whereNivel";
		$query=mysql_query($sql,$link);
		$bodyGeneral='';
		$bodyPersonal='';
		while ($row=mysql_fetch_array($query)) {
			if ($row['naturaleza']=='Provision') {
				$bodyGeneral.='<div class="filaBoleta" style="">
									<div class="campo1" ondblclick="insertarConceptos(\'['.$row['codigo'].']\')" style="height:20px;border-right:none;width:41px;border-bottom:1px solid #d4d4d4;border-right:1px solid #d4d4d4;">'.$row['codigo'].'</div>
									<div class="campo1" ondblclick="insertarConceptos(\'['.$row['codigo'].']\')" style="height:20px;border-right:none;width:calc(100% - 42px);border-bottom:1px solid #d4d4d4;">'.$row['descripcion'].'</div>
								</div>';
			}
			else{
				$bodyPersonal.='<div class="filaBoleta">
									<div class="campo1" ondblclick="insertarConceptos(\'['.$row['codigo'].']\')" style="height:20px;border-right:none;width:41px;border-bottom:1px solid #d4d4d4;border-right:1px solid #d4d4d4;">'.$row['codigo'].'-L</div>
									<div class="campo1" ondblclick="insertarConceptos(\'['.$row['codigo'].']\')" style="height:20px;border-right:none;width:calc(100% - 42px);border-bottom:1px solid #d4d4d4;">'.$row['descripcion'].'</div>
								</div>';
			}
		}

		echo '<style>
				.filaBoleta:hover{background-color:#F3F3F3;}
				spam{
					color:blue;
					font-weight:bold;
				}
				spam:hover .close{
					display:block;
				}
				spam:hover {
					display:none;
				}
				.close {
					display           : none;
					margin-top        : -15;
					margin-left       : 24;
					position          : fixed;
					cursor            : pointer;
					color             : #000;
					text-align        : center;
					width             : 18px;
					text-decoration   : none;
					font-weight       : bold;
					border-radius     : 3px;
					font-size         : 15px;
					background-image  : url(img/close.png);
					background-repeat : no-repeat;
					height            : 15px;
					background-size   : contain;
				}


			</style>
			<div style="width:100%;height:100%;" id="contenedorConceptosFormula">
				<div style="margin:auto;width:90%;height:100%;/*border:1px solid;*/">
					<div>
						<!--	<div contenteditable="true" spellcheck="false" style="width:100%;height:25px;background-color:#FFF;padding-top: 10px;" onchange="validaCaracterFormula(event,this)" onkeyup="validaCaracterFormula(event,this)">
							ddd <spam contenteditable="false"><div title="Eliminar Concepto" class="close"></div>[HE]</spam>&nbsp;
						</div>-->
						<input type="search" class="myfield" style="width:100%;height:35px;" placeholder="Ingrese la formula..." value="'.$formula.'" id="formula_concepto" onkeydown="validaCaracterFormula(event,this)">
					</div>
					<div style="margin-top: 10px;height: calc(100% - 50px);margin-top: 10px;border:1px solid #d4d4d4;">
						<div style="width:100%;height:100%;float:left;border-right:1px solid #d4d4d4;background-color:#FFF;">
							<div class="headTablaBoletas">
								<div class="filaBoleta">
									<div class="campo1" style="width:100%;border-bottom:1px solid #d4d4d4;">Variables para la formula</div>
									<div class="campo1" style="width:41px;">Cod.</div>
									<div class="campo1" style="width:calc(100% - 42px);border-right:none;">Concepto</div>
								</div>
							</div>
							<div class="bodyTablaBoletas" style="height: calc(100% - 30px);overflow: auto;">
								<div class="filaBoleta" style="">
									<div class="campo1" ondblclick="insertarConceptos(\'{DL}\')" style="height:20px;border-right:none;width:41px;border-bottom:1px solid #d4d4d4;border-right:1px solid #d4d4d4;">DL</div>
									<div class="campo1" ondblclick="insertarConceptos(\'{DL}\')" style="height:20px;border-right:none;width:calc(100% - 42px);border-bottom:1px solid #d4d4d4;">ACUMULADO DIAS LABORADOS</div>
								</div>
								<div class="filaBoleta" style="">
									<div class="campo1" ondblclick="insertarConceptos(\'{BL}\')" style="height:20px;border-right:none;width:41px;border-bottom:1px solid #d4d4d4;border-right:1px solid #d4d4d4;">BL</div>
									<div class="campo1" ondblclick="insertarConceptos(\'{BL}\')" style="height:20px;border-right:none;width:calc(100% - 42px);border-bottom:1px solid #d4d4d4;">BASE DE LIQUIDACION DEL CONCEPTO</div>
								</div>
								'.$bodyGeneral.'
							</div>
						</div>
					</div>
				</div>
			</div>

			<script>
				document.getElementById("formula_concepto").onkeypress = function(event){return validaCaracterFormula(event);};
				// console.log("'.$nivel.'");

				function insertarConceptos(codigo){
					document.getElementById("formula_concepto").value+=codigo+" ";
					document.getElementById("formula_concepto").focus();
				}

				function validaCaracterFormula(e){
					tecla = (document.all)?e.keyCode:e.which;
					if (tecla==8 		//BACKSPACE
					 	|| tecla==9 	//TAB
					 	|| tecla==0 	//TAB
					 	|| tecla==13 	//ENTER
					 	|| tecla==46 	//.
					 	|| tecla==40 	//(
					 	|| tecla==41 	//)
					 	|| tecla==43 	//+
					 	|| tecla==45 	//-
					 	|| tecla==47 	///
					 	|| tecla==42 	//*
					 	) return true;
					patron = /\d/;
					te = String.fromCharCode(tecla);
					return patron.test(te);
				}

				function cargarConceptosNiveles(nivel)
				{
					Ext.get("contenedorConceptosFormula").load({
						url     : "nomina_conceptos/bd/bd.php",
						scripts : true,
						nocache : true,
						params  :
						{
							opc    : "cargaConceptosNivelesLiquidacion",
							opcion : "'.$opcion.'",
							id     : "'.$id.'",
							nivel  : document.getElementById("nivel_formula_liquidacion").value,
						}
					});
				}

			</script>

			';
	}

	//FUNCION PARA MOSTRAR LOS NIVELES DE LAS FORMULAS
	function niveles_formula($opcion,$id,$id_empresa,$link){

		if ($opcion=='Vupdate') {
			//SI VA A ACTUALIZAR, CONSULTAR LOS CONCEPTOS CON NIVELES INFERIORES AL QUE TIENE
			$sql="SELECT nivel_formula FROM nomina_conceptos WHERE activo=1 AND id_empresa=$id_empresa AND id=$id";
			$query=mysql_query($sql,$link);
			$nivel=(mysql_result($query,0,'nivel_formula')==0)? 1 : mysql_result($query,0,'nivel_formula') ;
		}
		else{
			$nivel=1;
		}

		$sql="SELECT nivel_formula FROM nomina_conceptos WHERE activo=1 AND id_empresa=$id_empresa AND nivel_formula>0  GROUP BY nivel_formula ORDER BY nivel_formula DESC LIMIT 0,1";
		$query=mysql_query($sql,$link);
		$cont=(mysql_result($query,0,'nivel_formula')=='')? 0 : mysql_result($query,0,'nivel_formula') ;
		$select='<select id="nivel_formula" style="width:100px;margin-top:10px;" class="myfield" onchange="cargarConceptosNiveles(this.value)">';
		for ($i=1; $i <=$cont ; $i++) {
			$select.='<option value="'.$i.'">'.$i.'</option>';
		}
		// while ($row=mysql_fetch_array($query)) {

		// 	$cont=$row['nivel_formula'] ;
		// 	$select.='<option value="'.$cont.'">'.$cont.'</option>';

		// }
		$cont++;
		$select.='<option value="'.$cont.'">'.$cont.'</option>';
		$select.='</select>';
		echo '<div style="width:100%;height:100%;text-align:center;">'.$select.'</div>
				<script>document.getElementById("nivel_formula").value="'.$nivel.'";</script>';
	}

	//FUNCION PARA MOSTRAR LOS NIVELES DE LAS FORMULAS DE LIQUIDACION
	function niveles_formula_liquidacion($opcion,$id,$id_empresa,$link){

		if ($opcion=='Vupdate') {
			//SI VA A ACTUALIZAR, CONSULTAR LOS CONCEPTOS CON NIVELES INFERIORES AL QUE TIENE
			$sql="SELECT nivel_formula_liquidacion FROM nomina_conceptos WHERE activo=1 AND id_empresa=$id_empresa AND id=$id";
			$query=mysql_query($sql,$link);
			$nivel=(mysql_result($query,0,'nivel_formula_liquidacion')==0)? 1 : mysql_result($query,0,'nivel_formula_liquidacion') ;
		}
		else{
			$nivel=1;
		}

		$sql="SELECT nivel_formula_liquidacion FROM nomina_conceptos WHERE activo=1 AND id_empresa=$id_empresa AND nivel_formula_liquidacion>0  GROUP BY nivel_formula_liquidacion ORDER BY nivel_formula_liquidacion DESC LIMIT 0,1";
		$query=mysql_query($sql,$link);
		$cont=(mysql_result($query,0,'nivel_formula_liquidacion')=='')? 0 : mysql_result($query,0,'nivel_formula_liquidacion') ;
		$select='<select id="nivel_formula_liquidacion" style="width:100px;margin-top:10px;" class="myfield" onchange="cargarConceptosNiveles(this.value)">';
		for ($i=1; $i <=$cont ; $i++) {
			$select.='<option value="'.$i.'">'.$i.'</option>';
		}
		// while ($row=mysql_fetch_array($query)) {

		// 	$cont=$row['nivel_formula_liquidacion'] ;
		// 	$select.='<option value="'.$cont.'">'.$cont.'</option>';

		// }
		$cont++;
		$select.='<option value="'.$cont.'">'.$cont.'</option>';
		$select.='</select>';
		echo '<div style="width:100%;height:100%;text-align:center;">'.$select.'</div>
				<script>document.getElementById("nivel_formula_liquidacion").value="'.$nivel.'";</script>';
	}

	function cargaConceptosNiveles($opcion,$id,$nivel,$id_empresa,$link){
		$whereId='';
		$whereNivel=' AND nivel_formula<'.$nivel;
		if ($opcion=='Vupdate') {
			$whereId=' AND id<>'.$id;
		}
		// CONSULTAR LOS CONCEPTOS GUARDADOS EN EL SISTEMA
		$sql="SELECT codigo,descripcion,tipo_concepto FROM nomina_conceptos WHERE activo=1 AND id_empresa=$id_empresa AND nivel_formula>0 $whereId $whereNivel";
		$query=mysql_query($sql,$link);
		$bodyGeneral='';
		$bodyPersonal='';
		while ($row=mysql_fetch_array($query)) {
			if ($row['tipo_concepto']=='General') {
				$bodyGeneral.='<div class="filaBoleta" style="">
									<div class="campo1" ondblclick="insertarConceptos(\'['.$row['codigo'].']\')" style="height:20px;border-right:none;width:41px;border-bottom:1px solid #d4d4d4;border-right:1px solid #d4d4d4;">'.$row['codigo'].'</div>
									<div class="campo1" ondblclick="insertarConceptos(\'['.$row['codigo'].']\')" style="height:20px;border-right:none;width:calc(100% - 42px);border-bottom:1px solid #d4d4d4;">'.$row['descripcion'].'</div>
								</div>';
			}
			else{
				$bodyPersonal.='<div class="filaBoleta">
									<div class="campo1" ondblclick="insertarConceptos(\'['.$row['codigo'].']\')" style="height:20px;border-right:none;width:41px;border-bottom:1px solid #d4d4d4;border-right:1px solid #d4d4d4;">'.$row['codigo'].'</div>
									<div class="campo1" ondblclick="insertarConceptos(\'['.$row['codigo'].']\')" style="height:20px;border-right:none;width:calc(100% - 42px);border-bottom:1px solid #d4d4d4;">'.$row['descripcion'].'</div>
								</div>';
			}
		}

		echo '<style>
				.filaBoleta:hover{background-color:#F3F3F3;}
				spam{
					color:blue;
					font-weight:bold;
				}
				spam:hover .close{
					display:block;
				}
				spam:hover {
					display:none;
				}
				.close {
					display:none;
					margin-top: -15;
					margin-left: 24;
					position: fixed;
					cursor: pointer;
					color: #000;
					text-align: center;
					width: 18px;
					text-decoration: none;
					font-weight: bold;
					border-radius: 3px;
					font-size: 15px;
					background-image: url(img/close.png);
					background-repeat: no-repeat;
					height: 15px;
					background-size: contain;
				}


			</style>
			<div style="width:100%;height:100%;" id="contenedorConceptosFormula">
				<div style="margin:auto;width:90%;height:100%;/*border:1px solid;*/">
					<div>
						<!--	<div contenteditable="true" spellcheck="false" style="width:100%;height:25px;background-color:#FFF;padding-top: 10px;" onchange="validaCaracterFormula(event,this)" onkeyup="validaCaracterFormula(event,this)">
							ddd <spam contenteditable="false"><div title="Eliminar Concepto" class="close"></div>[HE]</spam>&nbsp;
						</div>-->
						<input type="text" class="myfield" style="width:100%;height:35px;" placeholder="Ingrese la formula..." value="" id="formula_concepto" onkeydown="validaCaracterFormula(event,this)">
					</div>
					<div style="margin-top: 10px;height: calc(100% - 50px);margin-top: 10px;border:1px solid #d4d4d4;">
						<div style="width:50%;height:100%;float:left;border-right:1px solid #d4d4d4;background-color:#FFF;">
							<div class="headTablaBoletas">
								<div class="filaBoleta">
									<div class="campo1" style="width:100%;border-bottom:1px solid #d4d4d4;">Conceptos Generales</div>
									<div class="campo1" style="width:41px;">Cod.</div>
									<div class="campo1" style="width:calc(100% - 42px);border-right:none;">Concepto</div>
								</div>
							</div>
							<div class="bodyTablaBoletas" style="height: calc(100% - 30px);overflow: auto;">
								'.$bodyGeneral.'
							</div>
						</div>
						<div style="width:calc(50% - 1px);height:100%;float:left;background-color:#FFF;">
							<div class="headTablaBoletas">
								<div class="filaBoleta">
									<div class="campo1" style="width:100%;border-bottom:1px solid #d4d4d4;">Variables del Sistema</div>
									<div class="campo1" style="width:41px;">Cod.</div>
									<div class="campo1" style="width:calc(100% - 42px);border-right:none;">Concepto</div>
								</div>
							</div>
							<div class="bodyTablaBoletas" style="height: 84px;overflow: auto;">
								<div class="filaBoleta">
									<div class="campo1" ondblclick="insertarConceptos(\'{DL}\')" style="height:20px;border-right:none;width:41px;border-bottom:1px solid #d4d4d4;border-right:1px solid #d4d4d4;">DL</div>
									<div class="campo1" ondblclick="insertarConceptos(\'{DL}\')" style="height:20px;border-right:none;width:calc(100% - 42px);border-bottom:1px solid #d4d4d4;">DIAS LABORADOS PLANILLA</div>
								</div>
								<div class="filaBoleta">
									<div class="campo1" ondblclick="insertarConceptos(\'{SC}\')" style="height:20px;border-right:none;width:41px;border-bottom:1px solid #d4d4d4;border-right:1px solid #d4d4d4;">SC</div>
									<div class="campo1" ondblclick="insertarConceptos(\'{SC}\')" style="height:20px;border-right:none;width:calc(100% - 42px);border-bottom:1px solid #d4d4d4;">SALARIO DEL CONTRATO</div>
								</div>
								<div class="filaBoleta">
									<div class="campo1" ondblclick="insertarConceptos(\'{NRL}\')" style="height:20px;border-right:none;width:41px;border-bottom:1px solid #d4d4d4;border-right:1px solid #d4d4d4;">NRL</div>
									<div class="campo1" ondblclick="insertarConceptos(\'{NRL}\')" style="height:20px;border-right:none;width:calc(100% - 42px);border-bottom:1px solid #d4d4d4;">NIVEL DE RIESGO LABORAl</div>
								</div>
								<div class="filaBoleta">
									<div class="campo1" ondblclick="insertarConceptos(\'{CT}\')" style="height:20px;border-right:none;width:41px;border-right:1px solid #d4d4d4;">CT</div>
									<div class="campo1" ondblclick="insertarConceptos(\'{CT}\')" style="height:20px;border-right:none;width:calc(100% - 42px);">CAMPO DE TEXTO</div>
								</div>
							</div>

							<div class="headTablaBoletas" style="border-top:1px solid #d4d4d4;">
								<div class="filaBoleta">
									<div class="campo1" style="width:100%;border-bottom:1px solid #d4d4d4;">Conceptos Personales</div>
									<div class="campo1" style="width:41px;">Cod.</div>
									<div class="campo1" style="width:calc(100% - 42px);border-right:none;">Concepto</div>
								</div>
							</div>
							<div class="bodyTablaBoletas" style="height: calc(100% - 200px);overflow: auto;">
								'.$bodyPersonal.'
							</div>
						</div>
					</div>
				</div>
			</div>';
	}

	function cargaConceptosNivelesLiquidacion($opcion,$id,$nivel,$id_empresa,$link){
		$whereId='';
		$whereNivel=' AND nivel_formula_liquidacion<'.$nivel;
		if ($opcion=='Vupdate') {
			$whereId=' AND id<>'.$id;
		}
		// CONSULTAR LOS CONCEPTOS GUARDADOS EN EL SISTEMA
		$sql="SELECT codigo,descripcion,tipo_concepto,naturaleza FROM nomina_conceptos WHERE activo=1 AND id_empresa=$id_empresa AND nivel_formula_liquidacion>0 $whereId $whereNivel";
		$query=mysql_query($sql,$link);
		$bodyGeneral='';
		$bodyPersonal='';
		while ($row=mysql_fetch_array($query)) {
			if ($row['naturaleza']=='Provision') {
				$bodyGeneral.='<div class="filaBoleta" style="">
									<div class="campo1" ondblclick="insertarConceptos(\'['.$row['codigo'].']\')" style="height:20px;border-right:none;width:41px;border-bottom:1px solid #d4d4d4;border-right:1px solid #d4d4d4;">'.$row['codigo'].'</div>
									<div class="campo1" ondblclick="insertarConceptos(\'['.$row['codigo'].']\')" style="height:20px;border-right:none;width:calc(100% - 42px);border-bottom:1px solid #d4d4d4;">'.$row['descripcion'].' </div>
								</div>';
			}
			else{
				$bodyPersonal.='<div class="filaBoleta">
									<div class="campo1" ondblclick="insertarConceptos(\'['.$row['codigo'].']\')" style="height:20px;border-right:none;width:41px;border-bottom:1px solid #d4d4d4;border-right:1px solid #d4d4d4;">'.$row['codigo'].'</div>
									<div class="campo1" ondblclick="insertarConceptos(\'['.$row['codigo'].']\')" style="height:20px;border-right:none;width:calc(100% - 42px);border-bottom:1px solid #d4d4d4;">'.$row['descripcion'].'</div>
								</div>';
			}
		}

		echo '<style>
				.filaBoleta:hover{background-color:#F3F3F3;}
				spam{
					color:blue;
					font-weight:bold;
				}
				spam:hover .close{
					display:block;
				}
				spam:hover {
					display:none;
				}
				.close {
					display:none;
					margin-top: -15;
					margin-left: 24;
					position: fixed;
					cursor: pointer;
					color: #000;
					text-align: center;
					width: 18px;
					text-decoration: none;
					font-weight: bold;
					border-radius: 3px;
					font-size: 15px;
					background-image: url(img/close.png);
					background-repeat: no-repeat;
					height: 15px;
					background-size: contain;
				}


			</style>
			<div style="width:100%;height:100%;" id="contenedorConceptosFormula">
				<div style="margin:auto;width:90%;height:100%;/*border:1px solid;*/">
					<div>
						<!--	<div contenteditable="true" spellcheck="false" style="width:100%;height:25px;background-color:#FFF;padding-top: 10px;" onchange="validaCaracterFormula(event,this)" onkeyup="validaCaracterFormula(event,this)">
							ddd <spam contenteditable="false"><div title="Eliminar Concepto" class="close"></div>[HE]</spam>&nbsp;
						</div>-->
						<input type="text" class="myfield" style="width:100%;height:35px;" placeholder="Ingrese la formula..." value="" id="formula_concepto" onkeydown="validaCaracterFormula(event,this)">
					</div>
					<div style="margin-top: 10px;height: calc(100% - 50px);margin-top: 10px;border:1px solid #d4d4d4;">
						<div style="width:100%;height:100%;float:left;border-right:1px solid #d4d4d4;background-color:#FFF;">
							<div class="headTablaBoletas">
								<div class="filaBoleta">
									<div class="campo1" style="width:100%;border-bottom:1px solid #d4d4d4;">Variables para la formula</div>
									<div class="campo1" style="width:41px;">Cod.</div>
									<div class="campo1" style="width:calc(100% - 42px);border-right:none;">Concepto</div>
								</div>
							</div>
							<div class="bodyTablaBoletas" style="height: calc(100% - 30px);overflow: auto;">
								<div class="filaBoleta" style="">
									<div class="campo1" ondblclick="insertarConceptos(\'{DL}\')" style="height:20px;border-right:none;width:41px;border-bottom:1px solid #d4d4d4;border-right:1px solid #d4d4d4;">DL</div>
									<div class="campo1" ondblclick="insertarConceptos(\'{DL}\')" style="height:20px;border-right:none;width:calc(100% - 42px);border-bottom:1px solid #d4d4d4;">ACUMULADO DIAS LABORADOS</div>
								</div>
								<div class="filaBoleta" style="">
									<div class="campo1" ondblclick="insertarConceptos(\'{BL}\')" style="height:20px;border-right:none;width:41px;border-bottom:1px solid #d4d4d4;border-right:1px solid #d4d4d4;">BL</div>
									<div class="campo1" ondblclick="insertarConceptos(\'{BL}\')" style="height:20px;border-right:none;width:calc(100% - 42px);border-bottom:1px solid #d4d4d4;">BASE DE LIQUIDACION DEL CONCEPTO</div>
								</div>
								'.$bodyGeneral.'
							</div>
						</div>
					</div>
				</div>
			</div>';
	}


	function select_restar_ct($id_empresa,$link){
		echo '<div style="text-align:center;float:left;width:100%;">
				<div style="font-weight:bold;">Restar valor de CT <br>de dias laborados?<div>
				<div style="float:left;width:100%;">
					<select style="width:80px;margin-top:5px;" class="myfield" onchange="updateRestarCt(this.value)">
						<option value="false" >No</option>
						<option value="true" >Si</option>
					</select>
				</div>
				<div id="divLoadRestarCt" style="width:20px;overflow:hidden;float:left;height:20px;margin-top:-21px;margin-left:10;"></div>

			</div>';
	}

	// AGREGAR LOS CONCEPTOS PARA EL CALCULO DE LA LIQUIDACION
	function agregar_concepto_liquidacion($id_concepto,$id_concepto_base,$id_empresa,$link){
		$sql="INSERT INTO nomina_conceptos_base_liquidacion (id_concepto,id_concepto_base,id_empresa)
				VALUES ('$id_concepto','$id_concepto_base','$id_empresa')";
		$query=mysql_query($sql,$link);

		if ($query) {
			$id_insert = mysql_insert_id();
			echo '<script>
					actualiza_fila_ventana_busqueda('.$id_concepto_base.');
					Inserta_Div_nominaConceptosBaseLiquidacion('.$id_insert.');
				</script>';
		}
		else{
			echo '<script>alert("Error\nNo se agrego el Concepto, intentelo de nuevo");</script>';
		}
	}

	function eliminar_concepto_liquidacion($id,$id_empresa,$link){
		$sql="DELETE FROM nomina_conceptos_base_liquidacion WHERE activo=1 AND id_empresa=$id_empresa AND id=$id";
		$query=mysql_query($sql,$link);

		if ($query) {
			echo '<script>
					document.getElementById("item_nominaConceptosBaseLiquidacion_'.$id.'").parentNode.removeChild(document.getElementById("item_nominaConceptosBaseLiquidacion_'.$id.'"));
					//Elimina_Div_nominaConceptosBaseLiquidacion('.$id.');
				</script>';
		}
		else{
			echo '<script>alert("Error\nNo se elimino el registro, intentelo de nuevo");</script>';
		}
	}

	function validaCuentas($json_cuentas,$mysql){

		// $json_cuentas = json_decode(json_encode($json_cuentas), True);
		$json_cuentas = json_decode($json_cuentas,true);
		// print_r($json_cuentas);
		$wherePuc     = '';
		$wherePucNiif = '';
		foreach ($json_cuentas as $key => $arrayResult) {
			if ($arrayResult['id_cuenta']=='') { continue; }
			if ($arrayResult['puc']=='puc') {
				$wherePuc .=($wherePuc=='')? " id=$arrayResult[id_cuenta] " : " OR id=$arrayResult[id_cuenta] " ;
			}
			else{
				$wherePucNiif .=($wherePucNiif=='')? " id=$arrayResult[id_cuenta] " : " OR id=$arrayResult[id_cuenta] " ;
			}
		}

		// CONSULTAR PUC LOCAL
		$sql="SELECT id,cuenta FROM puc WHERE activo=1 AND ($wherePuc) ";
		$query=$mysql->query($sql,$mysql->link);
		while ($row=$mysql->fetch_array($query)) {
			$arrayPuc['puc'][$row['id']] = $row['cuenta'];
		}

		// CONSULTAR PUC NIIF
		$sql="SELECT id,cuenta FROM puc_niif WHERE activo=1 AND ($wherePucNiif) ";
		$query=$mysql->query($sql,$mysql->link);
		while ($row=$mysql->fetch_array($query)) {
			$arrayPuc['puc_niif'][$row['id']] = $row['cuenta'];
		}

		// REASIGNAR EL ARRAY
		foreach ($json_cuentas as $key => $arrayResult) {
			if($arrayPuc['puc'][$arrayResult['id_cuenta']]==NULL){$arrayPuc['puc'][$arrayResult['id_cuenta']]=false;}
			if($arrayPuc['puc_niif'][$arrayResult['id_cuenta']]==NULL){$arrayPuc['puc_niif'][$arrayResult['id_cuenta']]=false;}
			$json_cuentas[$key]['cuenta'] = ($arrayResult['puc']=='puc')? $arrayPuc['puc'][$arrayResult['id_cuenta']] : $arrayPuc['puc_niif'][$arrayResult['id_cuenta']] ;
		}

		echo json_encode($json_cuentas);
	}

?>