<?php

/////////////////////////////////////////////////////////////
		if($this->LaOpcion == 'insert'){
				if($this->ConsulCustom == 'false'){
					$ConsultaCustom = "SELECT * FROM";
				}else{
					$ConsultaCustom = $this->ConsulCustom;
				}

				if($this->TableName != ''){
					$this->MySqlInUpDe =  $ConsultaCustom." ".$this->TableName." WHERE id='$this->VariableInUpDe'";
				}

				if($this->AddTooltipGeneral != ''){
					$array_variables = $this->EncuentraVariablesCadena($this->AddTooltipGeneral);
				}

				$contador = $this->ElContador+1;
				$consul = mysql_query($this->MySqlInUpDe);
				$row = mysql_fetch_array($consul);
				$id  = $row['id'];

				//SI EL PARAMETRO DEL TOOLTIP ES DIFERENTE A "" ENTONCES VERIFICA EL CAMBIO DE VARIABLES [VARIABLE]
				if($this->AddTooltipGeneral != ''){
					$mensaje = $this->AddTooltipGeneral;
					//if(in_array($this->ElDato[$i],$array_variables,true)){
					for($h=0;$h<count($array_variables);$h++){
						//$mensaje = str_replace("[".$this->ElDato[$i]."]",$Dato,$mensaje);
						$mensaje = str_replace("[".$array_variables[$h]."]",$row[$array_variables[$h]],$mensaje);
					}
				}
				$this->LargTotal = array_sum($this->ElLargo)+90;
				echo '		<div class="my_grilla_celdas2" id="item_'.$this->GrillaName.'_'.$row['id'].'" style="float:left; min-width:'.$this->LargTotal.'px; width:100%" divid="'.$row['id'].'">
								<div id="MuestraToltip_General_'.$this->GrillaName.'_'.$row['id'].'" ondblclick="MyEditar_'.$this->GrillaName.'(\''.$row['id'].'\',\''.$contador.'\')">
								<div id="MuestraToltip_'.$this->GrillaName.'_'.$row['id'].'" class="my_grilla_columna_insert" style="float:left; width:30px;">'.$contador.'</div>
						 ';
							for($i=0;$i<count($this->ElTitulo);$i++){
								if($this->LaFuncion[$i]!=""){

										switch($this->LaFuncion[$i]){
											case "codigo":
											$Dato = $this->Codigo($row[$this->ElDato[$i]]);
											break;

											case "moneda":
											$Dato = $this->Moneda($row[$this->ElDato[$i]]);
											break;

											case "fecha":
											$Dato = $this->Fecha($row[$this->ElDato[$i]]);
											break;
										}

								}else{
										if($this->ElDato[$i]!=false){
											$Dato = $row[$this->ElDato[$i]];
										}else{
											$array_variables_imagen = $this->EncuentraVariablesCadena($this->LaImagen[$i]);
											$laimagen = $this->LaImagen[$i];
											for($h=0;$h<count($array_variables_imagen);$h++){
												$laimagen = str_replace("[".$array_variables_imagen[$h]."]",$row[$array_variables_imagen[$h]],$laimagen);
											}
											$Dato = $laimagen;
										}
								}
								//SI EL LARGO ES DIFERENTE A "0" ENTONCES IMPRIME LA CELDA
								if($this->ElLargo[$i] > 0){
									$MyLarge = $this->ElLargo[$i]+1;
										echo '<div id="div_'.$this->GrillaName.'_'.$this->ElDato[$i].'_'.$row['id'].'" class="my_grilla_celdas" style="float:left; width:'.$MyLarge.'px;'.$this->colStyle[$this->ElDato[$i]].'">'.$Dato.'</div>';
								}
							}
				echo '		</div>
						</div>
						<div id="Recibidor_Celda_'.$this->GrillaName.$contador.'"></div>
						<script>Contador_'.$this->GrillaName.'++</script>
				';
				if($this->AddTooltipGeneral != ''){
					echo $this->CreateTooltipGeneral($mensaje,'General_'.$this->GrillaName.'_'.$row['id']);
				}

				if($this->MenuContextEliminar == 'true'){$MCEliminar = '';}else{$MCEliminar = '//';}
				if($this->MenuContext == 'true'){
					echo 	"<script>
								Ext.get('item_".$this->GrillaName."_".$row['id']."').on('contextmenu', function(eventObj, elRef_".$this->GrillaName."_".$row['id'].")
									{
										eventObj.stopEvent();

										var divid = document.getElementById(\"item_".$this->GrillaName."_".$row['id']."\").getAttribute('divid');

										if (!this.ctxMenu) {
											this.ctxMenu = new Ext.menu.Menu(
												{
													items :
													[
														".$MCEliminar."{
														".$MCEliminar."	text 	: 'Eliminar',
														".$MCEliminar."	iconCls : 'delete',
														".$MCEliminar."	handler : function(){
														".$MCEliminar."				elimina_desde_contextmenu_".$this->GrillaName."(divid);
														".$MCEliminar."			}
														".$MCEliminar."},
														'-'";

														if($this->CuantosContextMenu > 0){
															for($i=0;$i<$this->CuantosContextMenu;$i++){
																$array_variables_context_menu = $this->EncuentraVariablesCadena($this->ContextMenuFunction[$i]);
																$lafuncion_context = $this->ContextMenuFunction[$i];
																for($h=0;$h<count($array_variables_context_menu);$h++){
																	$lafuncion_context = str_replace("[".$array_variables_context_menu[$h]."]",$row[$array_variables_context_menu[$h]],$lafuncion_context);
																}


																echo '
																	,{
																		text 	: \''.$this->ContextMenuText[$i].'\',
																		iconCls : \''.$this->ContextMenuIcon[$i].'\',
																		handler : function(){
																					'.$lafuncion_context.'
																				}
																	}
																';
															}
														}

					echo "							]
												}
											);
										}
										this.ctxMenu.show(elRef_".$this->GrillaName."_".$row['id'].");
									}
								);
							</script>
							";
				}
		}


///////////////////////////////////////////////////////////////////////////
		if($this->LaOpcion == 'update'){

				if($this->ConsulCustom == 'false'){
					$ConsultaCustom = "SELECT * FROM";
				}else{
					$ConsultaCustom = $this->ConsulCustom;
				}

				if($this->TableName != ''){
					$this->MySqlInUpDe =  $ConsultaCustom." ".$this->TableName." WHERE id='$this->VariableInUpDe'";
				}

				if($this->AddTooltipGeneral != ''){
					$array_variables = $this->EncuentraVariablesCadena($this->AddTooltipGeneral);
				}
				//echo $this->MySqlInUpDe;
				$consul = mysql_query($this->MySqlInUpDe);
				$row = mysql_fetch_array($consul);
				$id  = $row['id'];

				//SI EL PARAMETRO DEL TOOLTIP ES DIFERENTE A "" ENTONCES VERIFICA EL CAMBIO DE VARIABLES [VARIABLE]
				if($this->AddTooltipGeneral != ''){
					$mensaje = $this->AddTooltipGeneral;
					//if(in_array($this->ElDato[$i],$array_variables,true)){
					for($h=0;$h<count($array_variables);$h++){
						//$mensaje = str_replace("[".$this->ElDato[$i]."]",$Dato,$mensaje);
						$mensaje = str_replace("[".$array_variables[$h]."]",$row[$array_variables[$h]],$mensaje);
					}
				}

				echo '	<div class="my_grilla_celdas2" id="item_'.$this->GrillaName.'_'.$row['id'].'" style="float:left; min-width:'.$this->LargTotal.'px; width:100%; border:none" >
							<div id="aquiMuestraToltip_General_'.$this->GrillaName.'_'.$row['id'].'" ondblclick="MyEditar_'.$this->GrillaName.'(\''.$row['id'].'\',\''.$this->ElContador.'\')">
							<div id="MuestraToltip_'.$this->GrillaName.'_'.$row['id'].'" class="my_grilla_columna_update" style="float:left; width:30px;">'.$this->ElContador.'</div>
					 ';
							for($i=0;$i<count($this->ElTitulo);$i++){
								if($this->LaFuncion[$i]!=""){

										switch($this->LaFuncion[$i]){
											case "codigo":
											$Dato = $this->Codigo($row[$this->ElDato[$i]]);
											break;

											case "moneda":
											$Dato = $this->Moneda($row[$this->ElDato[$i]]);
											break;
										}

								}else{
										if($this->ElDato[$i]!=false){
											$Dato = $row[$this->ElDato[$i]];
										}else{
											$array_variables_imagen = $this->EncuentraVariablesCadena($this->LaImagen[$i]);
											$laimagen = $this->LaImagen[$i];
											for($h=0;$h<count($array_variables_imagen);$h++){
												$laimagen = str_replace("[".$array_variables_imagen[$h]."]",$row[$array_variables_imagen[$h]],$laimagen);
											}
											$Dato = $laimagen;
										}
								}
								//SI EL LARGO ES DIFERENTE A "0" ENTONCES IMPRIME LA CELDA
								if($this->ElLargo[$i] > 0){
									$MyLarge = $this->ElLargo[$i]+1;
									echo '<div id="div_'.$this->GrillaName.'_'.$this->ElDato[$i].'_'.$row['id'].'" class="my_grilla_celdas" style="float:left; width:'.$MyLarge.'px;'.$this->colStyle[$this->ElDato[$i]].'">'.$Dato.'</div>';
								}
							}
				echo '		</div>
						</div>
					 ';
				if($this->AddTooltipGeneral != ''){
					echo $this->CreateTooltipGeneral($mensaje,'General_'.$this->GrillaName.'_'.$row['id']);
				}

		}


////////////////////////////////////////////////////////////////////////////
		if($this->LaOpcion == 'delete'){

				if($this->ConsulCustom == 'false'){
					$ConsultaCustom = "SELECT * FROM";
				}else{
					$ConsultaCustom = $this->ConsulCustom;
				}
				if($this->TableName != ''){
					$this->MySqlInUpDe =  $ConsultaCustom." ".$this->TableName." WHERE id='$this->VariableInUpDe'";
				}

				//SI EL PARAMETRO DEL TOOLTIP ES DIFERENTE A "" ENTONCES VERIFICA EL CAMBIO DE VARIABLES [VARIABLE]
				if($this->AddTooltipGeneral != ''){
					$mensaje = 'Eliminado!';
				}

				$consul = mysql_query($this->MySqlInUpDe);
				$row = mysql_fetch_array($consul);
				$id  = $row['id'];
				echo '	<div class="my_grilla_celdas2" id="item_'.$this->GrillaName.'_'.$row['id'].'" style="float:left; min-width:'.$this->LargTotal.'px; width:100%; border:none" >
							<div id="MuestraToltip_General_'.$this->GrillaName.'_'.$row['id'].'" ondblclick="">
							<div id="MuestraToltip_'.$this->GrillaName.'_'.$row['id'].'" class="my_grilla_columna_delete" style="float:left; width:30px;"></div>
					 ';
							for($i=0;$i<count($this->ElTitulo);$i++){
								if($this->LaFuncion[$i]!=""){

										switch($this->LaFuncion[$i]){
											case "codigo":
											$Dato = $this->Codigo($row[$this->ElDato[$i]]);
											break;

											case "moneda":
											$Dato = $this->Moneda($row[$this->ElDato[$i]]);
											break;
										}

								}else{
										if($this->ElDato[$i]!=false){
											$Dato = $row[$this->ElDato[$i]];
										}else{
											$Dato = '';
										}
								}
								//SI EL LARGO ES DIFERENTE A "0" ENTONCES IMPRIME LA CELDA
								if($this->ElLargo[$i] > 0){
									$MyLarge = $this->ElLargo[$i]+1;
									echo '<div class="my_grilla_celdas_delete" style="float:left; width:'.$MyLarge.'px;">'.$Dato.'</div>';
								}
							}
				echo '		</div>
						</div>
						<script>
							Ext.get("item_'.$this->GrillaName.'_'.$row["id"].'").on("contextmenu", function(eventObj, elRef) {
						        eventObj.stopEvent();
						        if(this.ctxMenu){ this.ctxMenu.hide(elRef); }
						    });
						</script>';

				if($this->AddTooltipGeneral != ''){
					echo $this->CreateTooltipGeneral($mensaje,'General_'.$this->GrillaName.'_'.$row['id']);
				}
		}


////////////////////////////////////////////////////////////////////////////
		if($this->LaOpcion == 'VerificarUnico'){
			$var1  = explode('{.}',$this->VarPost);
			$value = explode(':',$var1[0]);
			$campo = explode(':',$var1[1]);

			$SQL    = "SELECT $campo[1] FROM $this->TableName WHERE $campo[1]='$value[1]' AND activo = 1 AND id <> '$this->VariableInUpDe' ".$this->ValidacionGlobalSql;
			$consul = mysql_query($SQL);

			if(mysql_num_rows($consul)){ echo '{.}true{.}'.$campo[1].'{.}'.$value[1]; }
			else if($consul){ echo '{.}false{.}'; }
			else{ echo '{.}false{.}'.$SQL.mysql_error(); }
		}
?>
