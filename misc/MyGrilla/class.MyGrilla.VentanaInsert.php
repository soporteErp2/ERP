<?php
		/////////////////////////////////////////////////////////////
		if($this->LaOpcion == 'Vagregar'){

			$MyConteGnral	= $this->FContenedorAncho;
			$MyConteAncho 	= $this->FColumnaGeneralAncho;
			$MyConteAlto 	= $this->FColumnaGeneralAlto;
			$MyColLabels 	= $this->FColumnaLabelAncho;
			$MyColfields	= $this->FColumnaFieldAncho;

			//VALIDACION DIGITO DE VERIFICACION
			$scriptDigitoVerificacion = (in_array($_SESSION['PAIS'], $this->arrayDigitoVerificacion))? 'DigitoVerificacion_'.$_SESSION['PAIS'].'(this);': '';

			echo'
				<style>
					.EmpConte	{float:left; margin:0 0 2px 0; width:'.$MyConteAncho.'px; min-height:'.$MyConteAlto.'px}
					.EmpLabel	{float:left; width:'.$MyColLabels.'px;}
					.EmpField	{float:left; width:'.$MyColfields.'px;}
					.EmpSeparador{
						float	:left; width:100%;
						color	:#333;
						padding	:2px 0 3px 5px;
						margin	:4px 0 8px -10px;
						font-weight	: bold;
						/*border :1px solid '.$_SESSION["COLOR_LINEA"].';*/
						-moz-border-radius: 3px;
						-webkit-border-radius: 3px;
						-webkit-box-shadow: 1px 1px 3px #666;
						-moz-box-shadow: 1px 1px 2px #666;
						background: -webkit-linear-gradient(#DFE8F6, #CDDBF0);
						background: -moz-linear-gradient(#DFE8F6, #CDDBF0);
						background: -o-linear-gradient(#DFE8F6, #CDDBF0);
						background: linear-gradient(#DFE8F6, #CDDBF0);
					}
				</style>
			';

			echo '<form name="Formulario'.$this->GrillaName.'" id="Formulario'.$this->GrillaName.'" onsubmit="return false;">';
				echo '<div style="float:left; margin:20px 0 0 20px ; width:'.$MyConteGnral.'px">';

					$DigitoVerificacion = 'false';

					for($i=0;$i<count($this->FieldLabel);$i++){

						//////////////////////////////////////////////
						if($this->FieldObligatorio[$i] == 'true'){
							$class = 'myfieldObligatorio';
							$validar = 'ValidarFieldVacio(this)';
						}else{
							$class = 'myfield';
							$validar = '';
						}
						//////////////////////////////////////////////

						switch($this->FieldTipo[$i]){
							case "TextField":
									if($this->FieldHidden[$i] == 'false'){
										echo '
											<div id="EmpConte_'.$this->GrillaName.'_'.$this->FieldField[$i].'"  class="EmpConte">
												<div id="EmpLabel_'.$this->GrillaName.'_'.$this->FieldField[$i].'" class="EmpLabel">
													'.$this->FieldLabel[$i].'
												</div>
												<div class="EmpField" id="DIV_'.$this->GrillaName.'_'.$this->FieldField[$i].'">
													<input class="'.$class.'" name="'.$this->GrillaName.'_'.$this->FieldField[$i].'"  type="text" id="'.$this->GrillaName.'_'.$this->FieldField[$i].'" value="" style="width:'.$this->FieldLargo[$i].'px;" onKeyup="'.$validar.'"/>
												';

												if($this->FieldDigitoVerificacion[$i] == 'true'){
													echo '- <input class="myfield" name="'.$this->GrillaName.'_dv"  type="text" id="'.$this->GrillaName.'_dv" value="'.$row['dv'].'" style="width:40px;" />
															<script>
																document.getElementById("'.$this->GrillaName.'_'.$this->FieldField[$i].'").onchange = function(){ '.$scriptDigitoVerificacion.' };
															</script>';
													$DigitoVerificacion = 'true';
												}
										echo '
												</div>
											</div>
										';
									}else{
										if($this->FieldHiddenValue[$i]!=''){$myvalue = $this->FieldHiddenValue[$i];}else{$myvalue = $row[$this->FieldField[$i]];}
										echo '<input name="'.$this->GrillaName.'_'.$this->FieldField[$i].'"  type="hidden" id="'.$this->GrillaName.'_'.$this->FieldField[$i].'" value="'.$myvalue.'" />';
									}
							break;

							case "ComboBox":
									///////////////////////////////////////
									if($this->FieldBd[$i] == 'false'){
										$MyCombo =	$this->GeneraComboTX($this->FieldArray[$i]);
									}else{
										$MyCombo = 	$this->GeneraComboBD($this->FieldArray[$i],$this->FieldWhere[$i]);
									}
									///////////////////////////////////////
									echo'
										<div id="EmpConte_'.$this->GrillaName.'_'.$this->FieldField[$i].'" class="EmpConte">
											<div id="EmpLabel_'.$this->GrillaName.'_'.$this->FieldField[$i].'" class="EmpLabel">
												'.$this->FieldLabel[$i].'
											</div>
											<div class="EmpField" id="DIV_'.$this->GrillaName.'_'.$this->FieldField[$i].'">
												<select class="'.$class.'" name="'.$this->GrillaName.'_'.$this->FieldField[$i].'" id="'.$this->GrillaName.'_'.$this->FieldField[$i].'" style="width:'.$this->FieldLargo[$i].'px" onchange="'.$validar.'">
													<option value="">Seleccione...</option>
													'.$MyCombo.'
												</select>
											</div>
										</div>
									';
							break;

							case "TextArea":
									echo '
										<div id="EmpConte_'.$this->GrillaName.'_'.$this->FieldField[$i].'" class="EmpConte">
											<div id="EmpLabel_'.$this->GrillaName.'_'.$this->FieldField[$i].'" class="EmpLabel">
												'.$this->FieldLabel[$i].'
											</div>
											<div class="EmpField" id="DIV_'.$this->GrillaName.'_'.$this->FieldField[$i].'">
												<textarea class="'.$class.'" name="'.$this->GrillaName.'_'.$this->FieldField[$i].'" id="'.$this->GrillaName.'_'.$this->FieldField[$i].'" style="width:'.$this->FieldLargo[$i].'px; height:'.$this->FieldAlto[$i].'px;" onKeyup="'.$validar.'" cols="" rows=""></textarea>
											</div>
										</div>
									';
							break;

							case "Separador":
									echo '
										<div class="EmpSeparador">
											<div style="width:80%;float:left">
												'.$this->FieldLabel[$i].'
											</div>
										</div>
									';
							break;
						}
					}

					if($DigitoVerificacion == 'true'){
						$this->FieldLabel[$this->CuantosFields] = '';
						$this->FieldField[$this->CuantosFields] = 'dv';
						$this->FieldLargo[$this->CuantosFields] = 40;
						$this->FieldAlto[$this->CuantosFields] = '';
						$this->FieldObligatorio[$this->CuantosFields] = 'false';
						$this->FieldHidden[$this->CuantosFields] = 'false';
						$this->FieldHiddenValue[$this->CuantosFields] = 'false';
						$this->FieldTipo[$this->CuantosFields] = 'TextField';
						$this->FieldBd[$this->CuantosFields] = '';
						$this->FieldArray[$this->CuantosFields] = '';
						$this->FieldWhere[$this->CuantosFields] = '';
						$this->FieldDigitoVerificacion[$this->CuantosFields] = 'false';
						$this->CuantosFields++;
					}

				echo '</div>';
			echo '</form> ';


			echo '<script>';

				echo 'ValidaFormularioEnCarga(\'Formulario'.$this->GrillaName.'\');';
				/*---------------------------------------------------------------------------------------------------------------*/
				echo'function guarda'.$this->GrillaName.'(){';
					echo " 	var msgError='';";

					if($this->ValidacionEmail == 'true'){
						$campoEmail = $this->ValidacionEmailField;
                        echo '	var value = document.getElementById("'.$this->GrillaName.'_'.$campoEmail.'").value;';
                    	echo '	if(!validarEmail(value)){ msgError="true"; }';
                    }

                    if($this->ValidacionGlobal == 'true'){

                        $campo = $this->ValidacionGlobalField;
                        echo 'var value = document.getElementById("'.$this->GrillaName.'_'.$campo.'").value;';
                        echo 'var campo = "'.$campo.'";';
                        echo 'Ext.Ajax.request
                                (
                                    {
                                    url     : \''.$_SERVER['SCRIPT_NAME'].'\',
                                    method  : \'post\',
                                    params  :
                                        {
											opcion     : \'VerificarUnico\',
											validacion : value,
											campo      : campo
											'.$this->VariablesPost.'
                                        },
                                    success : function (result, request)
                                        {
											var resultado   = result.responseText.split("{.}");
											var respuesta   = resultado[1];
											var campo 		= resultado[2];
											var valor		= resultado[3];

                                            if(respuesta == \'true\' && document.getElementById("EmpConte_'.$this->GrillaName.'_"+campo)){
												var msgLabelError=campo;
												if(document.getElementById("EmpLabel_'.$this->GrillaName.'_"+campo) && document.getElementById("EmpLabel_'.$this->GrillaName.'_"+campo).innerHTML!=""){
													msgLabelError=document.getElementById("EmpLabel_'.$this->GrillaName.'_"+campo).innerHTML;
													msgLabelError=msgLabelError.replace(/[\:\\n\\t]/g,"");
												}
												alert(\'el campo "\'+msgLabelError+\'" con valor "\'+valor+\'" ya existe!\' );
												document.getElementById("'.$this->GrillaName.'_"+campo).focus();
											}
											else if(respuesta == \'true\'){
												alert(\'el campo "\'+campo+\'" con valor "\'+valor+\'" ya existe!\');
												document.getElementById("'.$this->GrillaName.'_"+campo).focus();
											}
                                            else if(msgError==""){
                                                termina_guarda'.$this->GrillaName.'();
                                            }
                                        }
                                    }

                                );';
                    }
                    else{
                        echo 'if(msgError==""){ termina_guarda'.$this->GrillaName.'(); }';
                    }

                    echo'function termina_guarda'.$this->GrillaName.'(){';
    					for($i=0;$i<count($this->FieldLabel);$i++){
    						if($this->FieldTipo[$i] != 'Separador'){
    							echo 'var '.$this->FieldField[$i].' = document.getElementById("'.$this->GrillaName.'_'.$this->FieldField[$i].'").value;';
    							if($this->FieldObligatorio[$i] == 'true'){
    								echo 'if('.$this->FieldField[$i].' == \'\'){alert(\'Campo "'.$this->FieldLabel[$i].'" Incompleto!.\');return false;}';
    							}
    						}
    					}

    					echo'
    						var opcion = \'GuardaBD\';

    						Ext.Ajax.request
    						(
    							{
	    							url		: \''.$_SERVER['SCRIPT_NAME'].'\',
	    							method	: \'post\',
	    							params	:
    								{
										opcion : opcion,
    					';

    					for($i=0;$i<count($this->FieldLabel);$i++){
    						if($this->FieldTipo[$i] != 'Separador'){
    							echo $this->FieldField[$i].' : '.$this->FieldField[$i].',';
    						}
    					}

    						echo'	},
    								success : function (result, request)
									{
										var resultado = result.responseText.split("{.}");
										var respuesta = resultado[0];
										var resp      = resultado[1];
										var resp2     = resultado[2];

										if(respuesta == \'false\'){ alert(resp+\'\n\n\'+resp2); }
										else{
											MyLoading();

    										var id_registro = resp;
											'.$this->LastInsert.'
                           ';
                           				if($this->Formulario=='true' && $this->StopEventLastInsert == 'false'){
                                        	echo  'Win_Formulario_'.$this->GrillaName.'.close();';
                                        }
                           				else if($this->StopEventLastInsert == 'false'){
                                           echo  'Win_Agregar_'.$this->GrillaName.'.close();
													Inserta_Div_'.$this->GrillaName.'(resp);';
                                        }

                                        if($this->CerrarDespuesDeAgregar == 'false'){
                                           echo  'Editar_'.$this->GrillaName.'(resp)';
                                        }
                            echo'


										}
									}
								}
							);
						}
                	}
                ';


				//////////////////////////////////////////////////////////////////////////////
				//////////////////////////////////////////////////////////////////////////////
				//////////////////////////////////////////////////////////////////////////////
				for($i=0;$i<count($this->ValidacionesCampos);$i++){

					switch($this->Validaciones[$i]){
						case "unico_global":
							echo 'document.getElementById("'.$this->GrillaName.'_'.$this->ValidacionesCampos[$i].'").onblur = function(){validar_unico_global(this.value,this.id,\''.$this->ValidacionesCampos[$i].'\', \''.$this->ValidacionGlobalSql.'\')};';
						break;

						case "email":
							echo 'document.getElementById("'.$this->GrillaName.'_'.$this->ValidacionesCampos[$i].'").onblur = function(){validar_campo_email(this.value,this.id,\''.$this->ValidacionesCampos[$i].'\')};';
						break;

						case "numero":
							echo 'document.getElementById("'.$this->GrillaName.'_'.$this->ValidacionesCampos[$i].'").onkeypress = function(event){return ValidarN(event);};';
						break;

						case "numero-real":
							echo'document.getElementById("'.$this->GrillaName.'_'.$this->ValidacionesCampos[$i].'").onkeyup = function(event){ return ValidarNumeroReal(event,this); };';
						break;

						case "numero-texto":
							echo 'document.getElementById("'.$this->GrillaName.'_'.$this->ValidacionesCampos[$i].'").onkeypress = function(event){return ValidarNL(event);};';
						break;

						case "fecha":
							echo  '	new Ext.form.DateField({
										format 		: \'Y-m-d\',
										width		: 120,
										allowBlank	: false,
										showToday	: false,
										editable	: false,
										applyTo		: \''.$this->GrillaName.'_'.$this->ValidacionesCampos[$i].'\'
									});';
						break;

                        case "mayuscula":
                            echo 'document.getElementById("'.$this->GrillaName.'_'.$this->ValidacionesCampos[$i].'").onkeyup = function(){ValidarMayuscula(this.id);};';
                        break;

                        case "minuscula":
                            echo 'document.getElementById("'.$this->GrillaName.'_'.$this->ValidacionesCampos[$i].'").onkeyup = function(){ValidarMinuscula(this.id);};';
                        break;
					}

				}

				echo 	'	var campo_email = "";

							function validar_campo_email(value,id,campo){
								if(!validarEmail(value)){
									setTimeout(function(){
										if(document.getElementById("EmpLabel_"+id) && document.getElementById("EmpLabel_"+id).innerHTML!=""){
											var msgLabelError =campo;
											msgLabelError     =document.getElementById("EmpLabel_"+id).innerHTML;
											msgLabelError     =msgLabelError.replace(/[\:\\n\\t]/g,"");
											
											document.getElementById(id).focus();

											//SOLO MUESTRA EL ALERT UNA SOLA VEZ
											if(campo_email != value){
												alert("No es una cuenta de Email Valida \'"+value+"\' en el campo \'"+msgLabelError+"\'");
												campo_email = value;												
											}
										}
									},200);
								}
							}

							var unico_global = "";

							function validar_unico_global(value,id,campo,sql){

								Ext.Ajax.request
								(
									{
									url		: \''.$_SERVER['SCRIPT_NAME'].'\',
									method	: \'post\',
									params	:
										{
											opcion              : \'VerificarUnico\',
											validacion          : value,
											campo               : campo,
											ValidacionGlobalSql : sql
										},
									success : function (result, request)
										{
											var resultado 	= result.responseText.split("{.}");
											var respuesta 	= resultado[1];
											var campo 		= resultado[2];
											var valor		= resultado[3];

											if(respuesta == \'true\' && document.getElementById("EmpConte_"+id)){
												var msgLabelError=campo;
												if(document.getElementById("EmpLabel_"+id) && document.getElementById("EmpLabel_"+id).innerHTML!=""){
													msgLabelError=document.getElementById("EmpLabel_"+id).innerHTML;
													msgLabelError=msgLabelError.replace(/[\:\\n\\t]/g,"");
												}
												document.getElementById("'.$this->GrillaName.'_"+campo).focus();
												//SOLO MUESTRA EL ALERT UNA SOLA VEZ
												if(unico_global != valor){
													alert(\'el campo "\'+msgLabelError+\'" con valor "\'+valor+\'" ya existe!\' );
													unico_global = valor;
												}	
											}
											else if(respuesta == \'true\'){
												alert(\'el campo "\'+campo+\'" con valor "\'+valor+\'" ya existe!\');
											}
										}
									}
								);
							}
						';

				//////////////////////////////////////////////////////////////////////////////
				//////////////////////////////////////////////////////////////////////////////
				//////////////////////////////////////////////////////////////////////////////

			echo '</script>';


		}
?>