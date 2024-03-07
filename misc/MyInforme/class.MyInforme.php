<?php

class MyInforme {

	/*----------------------------------------------------------------------------------------------------------------------------------------------------------------------------*/
	//VARIABLES PUBLICAS
		//CONFIGURACION DEL TOOLBAR
		public  $InformeName     			= "";		//NOMBRE DEL INFORME
		public	$InformeTitle				= "";		//TITULO DEL INFORME
		public	$InformeResult				= ""; 		//NOMBRE DEL ARCHIVO QUE GENERA EL INFORME RESULTANTE
		public	$BtnGenera					= "true";	//BOTON GENERA
		public	$AddElements				= "";		//BOTON INSERT ELEMENTS

		public  $PermisoMultiEmpresa		= 'false';  //PERMISO DE VER INFORMES MULTI EMPRESA
		public  $PermisoMultiSucursal		= 'false';	//PERMISO DE VER INFORMES MULTISUCURSAL

		public	$InformeFechaInicioFin		= "false";	//SI LLEVA FILTRO DE FECHA DE INICIO Y FECHA FIN
		public	$InformeFechaInicio			= "true";	//SI LLEVA FILTRO DE FECHA DE INICIO
		public	$InformeFechaFin			= "true";	//SI LLEVA FILTRO DE FECHA DE FIN
		public  $FiltroClientes				= "false";  //FILTRO PARA SELECCIONAR CLIENTES
		public  $FiltroClientesProspectos   = "false";  //FILTRO PARA SELECCIONAR CLIENTES QUE INCLUYEN PROSPECTOS
		public  $FiltroFuncionarios  		= "false";  //FILTRO PARA SELECCIONAR FUNCIONARIOS

		public	$ElPathDeLaClase			= "/misc/MyInforme";  //DIRECCION DE LA CARPETA DE LA CLASE
		public	$InformeExportarPDF			= "false";	//SI EXPORTA A PDF
		public	$InformeExportarXLS			= "false";	//SI EXPORTA A XLS
		public	$InformeDebug				= "false";	//MUESTRA UN ALERT CON EL NOMBRE DE TODOS LOS CAMPOS
		public  $FuncionGenerarCustom;		// FUNCION DEL BOTON DE GENERAR INFORME PERSONALIZADA

		//CONFIGURACION DEL TAMANO DEL INFORME
		public	$InformeAnchoPersonalizado;
		public  $InformeTamano				= "CARTA-VERTICAL"; //TAMANOS DE HOJA (CARTA-VERTICAL, CARTA-HORIZONTAL, OFICIO-VERTICAL, OFICIO-HORIZONTAL)

		//CONFIGURACION DEL AREA DEL INFORME
		public 	$AreaInforme				= 'true';	//SI LLEVA EL AREA DE INFORME
		public	$AreaInformeAncho			= 300;
		public	$AreaInformeAlto			= 300;
		public	$AreaInformeQuitaAncho;
		public	$AreaInformeQuitaAlto;

		public  $DefaultCls					= "x-btn-as-arrow";
		public 	$HeightToolbar    			= 100;

		//VARIABLES PRIVADAS
		private	$CamposPorLeer				= array();
		private	$CuantosCamposPorLeer		= 0;

		private $MyFiltros					= false;
		private $MyFiltrosLabel				= array();
		private $MyFiltrosTextoVacio		= array();
		private $MyFiltrosArray				= array();
		private $MyFiltrosDatoDefault		= array();
		private $MyFiltrosCuantos			= 0;
		private $MyFiltrosComboBox			= array();
		private $FiltroEspecialClientes		= array();
		private $FiltroEspecialFuncionarios = array();
		private $CualesFiltrosEmpresas		= '';

		private $NewButtons 				= ''; //AGREGA NUEVOS BOTONES AL INFORME

		private $Filtro_Empresa 			= 'false';
		private $Filtro_Zona 				= 'false';
		private $Filtro_Sucursal 			= 'false';
		private $Filtro_Bodega 				= 'false';
		private $Filtro_Todos 				= 'false';

		private $FiltroEspecialClientesProspectos = array();
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////



		public function inicializa($MyPost){
			$VarPost = '';
			foreach($MyPost as $nombre_campo => $valor){
				$VarPost .=	$nombre_campo.':'.utf8_decode($valor).'{.}';
				$this->$nombre_campo = utf8_decode($valor);
			}
			$this->VarPost = $VarPost;
		}
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

		public function AddFiltro($Label='',$TextoVacio='',$DatosArray='',$DatoDefault='false'){

			$this->MyFiltrosLabel[$this->MyFiltrosCuantos]                   = $Label;
			$this->MyFiltrosTextoVacio[$this->MyFiltrosCuantos]              = $TextoVacio;
			$this->MyFiltrosArray[$this->MyFiltrosCuantos]                   = $DatosArray;
			$this->MyFiltrosDatoDefault[$this->MyFiltrosCuantos]             = $DatoDefault;
			$this->MyFiltrosComboBox[$this->MyFiltrosCuantos]                = 'true';
			$this->FiltroEspecialClientes[$this->MyFiltrosCuantos]           = 'false';
			$this->FiltroEspecialClientesProspectos[$this->MyFiltrosCuantos] = 'false';
			$this->FiltroEspecialFuncionarios[$this->MyFiltrosCuantos]       = 'false';
			$this->MyFiltros = 'true';
			$this->MyFiltrosCuantos++;
		}
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

		public function AddFiltroField($Label='',$TextoVacio='',$DatoDefault='false',$FiltroEspecialCliente='false',$FiltroClientesProspectos='false',$FiltroEspecialFuncionarios='false'){

			if($FiltroEspecialCliente == 'false'){ //SI NO ES FILTRO ESPECIAL DE CLIENTES LOS AGREGA AL ARRAY NORMALMENTE (DE ULTIMO)
				$this->MyFiltrosLabel[$this->MyFiltrosCuantos]                   = $Label;
				$this->MyFiltrosTextoVacio[$this->MyFiltrosCuantos]              = $TextoVacio;
				$this->MyFiltrosArray[$this->MyFiltrosCuantos]                   = '';
				$this->MyFiltrosDatoDefault[$this->MyFiltrosCuantos]             = $DatoDefault;
				$this->MyFiltrosComboBox[$this->MyFiltrosCuantos]                = 'false';
				$this->FiltroEspecialClientes[$this->MyFiltrosCuantos]           = $FiltroEspecialCliente;
				$this->FiltroEspecialClientesProspectos[$this->MyFiltrosCuantos] = $FiltroClientesProspectos;//PERMITE VER TAMBIEN PROSPECTOS
				$this->FiltroEspecialFuncionarios[$this->MyFiltrosCuantos]       = $FiltroEspecialFuncionarios;


			}else{ //SI ES FILTRO ESPECIAL DE CLIENTES, QUIERE DECIR QUE EL EL FIELD DE CLIENTE, ENTONCES LO AGREGA DE PRIMERO
				array_unshift($this->MyFiltrosLabel, $Label);
				array_unshift($this->MyFiltrosTextoVacio, $TextoVacio);
				array_unshift($this->MyFiltrosArray, '');
				array_unshift($this->MyFiltrosDatoDefault, $DatoDefault);
				array_unshift($this->MyFiltrosComboBox, 'false');
				array_unshift($this->FiltroEspecialClientes, 'true');
				array_unshift($this->FiltroEspecialClientesProspectos, $FiltroClientesProspectos);
				array_unshift($this->FiltroEspecialFuncionarios, 'false');//CON ESTO ME ASEGURO DE QUE SI EL FILTRO DE CLIENTES ESTA ACTIVADO PONE EN FALSO EL DE FUNCIONARIOS SI SE PONE LOS DOS PARAMETROS EN TRUE

			}

			$this->MyFiltros = 'true';
			$this->MyFiltrosCuantos++;
		}
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

		public function AddCamposPorLeer($campo){
			$this->CamposPorLeer[$this->CuantosCamposPorLeer] = $campo;
			$this->CuantosCamposPorLeer++;
		}
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

		public function AddFiltroFechaInicioFin($inicio='true',$fin='true'){
			$this->InformeFechaInicioFin 	= 'true';
			$this->InformeFechaInicio		= $inicio;
			$this->InformeFechaFin			= $fin;
		}
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

		public function AddBotton($text,$icon,$function,$id=''){
			$this->NewButtons .='{
									xtype     : \'button\',
									text      : \''.$text.'\',
									id        : \''.$id.'\',
									scale     : \'large\',
									iconCls   : \''.$icon.'\',
									iconAlign : \'top\',
									cls		  : \''.$this->DefaultCls.'\',
									handler   : function(){ BloqBtn(this); '.$function.' }
								},';
		}
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

		public function AddFiltroEmpresa($Filtro_Empresa='false',$Filtro_Zona='false',$Filtro_Sucursal='false',$Filtro_Bodega='false',$Filtro_Todos='false'){
			$this->Filtro_Empresa 	= $Filtro_Empresa;
			$this->Filtro_Zona 		= $Filtro_Zona;
			$this->Filtro_Sucursal 	= $Filtro_Sucursal;
			$this->Filtro_Bodega 	= $Filtro_Bodega;
			$this->Filtro_Todos 	= $Filtro_Todos;
		}
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

		public function GeneraFiltroEmpresa(){
			echo '
					InformeDebug = InformeDebug+"MyInformeFiltroEmpresa_'.$this->InformeName.'\n";
					InformeDebug = InformeDebug+"MyInformeFiltroZona_'.$this->InformeName.'\n";
					InformeDebug = InformeDebug+"MyInformeFiltroSucursal_'.$this->InformeName.'\n";
					InformeDebug = InformeDebug+"MyInformeFiltroBodega_'.$this->InformeName.'\n";
				 ';

			$cuales = '';

			//////////////////////////FILTRO_DE_EMPRESA//////////////////////////
			if($this->Filtro_Empresa == 'true'){
				echo 	'var EmpresaStore = 	new Ext.data.JsonStore(
							{
								url			:\''.$_SERVER['SCRIPT_NAME'].'\',
								root		:\'data\',
								fields		:[\'value\',\'label\'],
								baseParams  :{MyFiltro:\'true\', opcion:\'empresa\'}
							}
						); ';
				$cuales .= 'ComboEmpresa';
				$this->AddCamposPorLeer('MyInformeFiltroEmpresa_'.$this->InformeName);
				echo '	var ComboEmpresa = new Ext.form.ComboBox(
							{
								store			:	EmpresaStore,
								id				: 	\'MyInformeFiltroEmpresa_'.$this->InformeName.'\',
								editable 		: 	false,
								forceSelection	:	true,
								width			: 	150,
								listWidth 		: 	350,
								valueField		: 	\'value\',
								displayField	: 	\'label\',
								emptyText		: 	\'Seleccione la Empresa\',
								fieldLabel		: 	\'Empresa\',
								listeners: {
									render: function() {
										this.store.load(
											{
												callback: function() {
													ComboEmpresa.setValue(LaEmpresaGuardada_'.$this->InformeName.');
						';
													if($this->Filtro_Zona == 'true'){
														echo'	ZonaStore.load({params:{id:LaEmpresaGuardada_'.$this->InformeName.',opcion:\'zonas\',MyFiltro:\'true\'}});
																setTimeout(\'ComboZona.setValue(LaZonaGuardada_'.$this->InformeName.')\',600);	';

														if($this->Filtro_Sucursal == 'true'){
															echo'	SucursalStore.load({params:{id:LaZonaGuardada_'.$this->InformeName.',opcion:\'sucursal\',MyFiltro:\'true\'}});
																	setTimeout(\'ComboSucursal.setValue(LaSucursalGuardada_'.$this->InformeName.')\',600);	';

															if($this->Filtro_Bodega == 'true'){
																echo'	BodegaStore.load({params:{id:LaSucursalGuardada_'.$this->InformeName.',opcion:\'bodega\',MyFiltro:\'true\'}});
																		setTimeout(\'ComboBodega.setValue(LaBodegaGuardada_'.$this->InformeName.')\',800);	';
															}
														}
													}

				echo'
												},
												params:{MyFiltro:\'true\'}
											}
										);
									}
								}
							}
						); ';
			}

			//////////////////////////FILTRO_DE_ZONA///////////////////////////////////
			if($this->Filtro_Zona == 'true'){
				echo	'var ZonaStore = new Ext.data.JsonStore(
							{
								url		:\''.$_SERVER['SCRIPT_NAME'].'\',
								root	:\'data\',
								fields	:[\'value\',\'label\']
							}
						);';
				$cuales .= ',ComboZona';
				$this->AddCamposPorLeer('MyInformeFiltroZona_'.$this->InformeName);
				echo '	var ComboZona = new Ext.form.ComboBox(
							{
								store			: 	ZonaStore,
								id				: 	\'MyInformeFiltroZona_'.$this->InformeName.'\',
								editable 		: 	false,
								forceSelection	:	true,
								width			: 	150,
								valueField		: 	\'value\',
								displayField	:	\'label\',
								triggerAction	: 	\'all\',
								mode			: 	\'local\',
								emptyText		:	\'Seleccione la Zona\',
								fieldLabel		: 	\'Zona\'
							}
						);	';

				echo '	ComboEmpresa.on(\'select\',function(cmb,record,index)
							{
					';
							if($this->Filtro_Sucursal == 'true'){
								echo 'ComboZona.enable();
									  ComboZona.clearValue();';
							}
							if($this->Filtro_Sucursal == 'true'){
								echo 'ComboSucursal.enable();
								      ComboSucursal.clearValue();';
							}
							if($this->Filtro_Bodega == 'true'){
								echo 'ComboBodega.enable();
								      ComboBodega.clearValue();';
							}

				echo '			ZonaStore.load({params:{id:record.get(\'value\'),opcion:\'zonas\',MyFiltro:\'true\'}});
							}
						);	';
			}

			/////////////////////////FILTRO_DE_SUCURSAL////////////////////////////
			if($this->Filtro_Sucursal == 'true'){
				echo	'var SucursalStore = new Ext.data.JsonStore(
							{
								url		:\''.$_SERVER['SCRIPT_NAME'].'\',
								root	:\'data\',
								fields	:[\'value\',\'label\']
							}
						);	';
				$cuales .= ',ComboSucursal';
				$this->AddCamposPorLeer('MyInformeFiltroSucursal_'.$this->InformeName);
				echo '	var ComboSucursal = new Ext.form.ComboBox(
							{
								store			: 	SucursalStore,
								id				: 	\'MyInformeFiltroSucursal_'.$this->InformeName.'\',
								editable 		: 	false,
								forceSelection	:	true,
								width			: 	150,
								valueField		: 	\'value\',
								displayField	:	\'label\',
								triggerAction	: 	\'all\',
								mode			: 	\'local\',
								emptyText		:	\'Seleccione la Sucursal\',
								fieldLabel		: 	\'Sucursal\'
							}
						);	';

				echo '	ComboZona.on(\'select\',function(cmb,record,index)
							{
					';
								if($this->Filtro_Sucursal == 'true'){
									echo 'ComboSucursal.enable();
									      ComboSucursal.clearValue();';
								}
								if($this->Filtro_Bodega == 'true'){
									echo 'ComboBodega.enable();
										  ComboBodega.clearValue();';
								}

				echo '			SucursalStore.load({params:{id:record.get(\'value\'),opcion:\'sucursal\',MyFiltro:\'true\'}});
								//setTimeout(\'ComboZona.setValue(LaBodegaGuardada_'.$this->InformeName.')\',800);
							}
						);	';
			}

			////////////////////////////FILTRO_DE_BODEGA/////////////////////////
			if($this->Filtro_Bodega == 'true'){
				echo 	'var BodegaStore = 	new Ext.data.JsonStore(
							{
								url		:\''.$_SERVER['SCRIPT_NAME'].'\',
								root	:\'data\',
								fields	:[\'value\',\'label\']
							}
						);	';
				$cuales .= ',ComboBodega';
				$this->AddCamposPorLeer('MyInformeFiltroBodega_'.$this->InformeName);
				echo '	var ComboBodega = new Ext.form.ComboBox(
							{
								store			:	BodegaStore,
								id				:	\'MyInformeFiltroBodega_'.$this->InformeName.'\',
								editable 		: 	false,
								forceSelection	:	true,
								width			: 	150,
								listWidth 		:	350,
								valueField		: 	\'value\',
								displayField	: 	\'label\',
								triggerAction	: 	\'all\',
								mode			: 	\'local\',
								emptyText		: 	\'Seleccione la Bodega\',
								fieldLabel		: 	\'Bodega\'
							}
						);	';

				echo '	ComboSucursal.on(\'select\',function(cmb,record,index)
							{
					 ';
								if($this->Filtro_Bodega == 'true'){
									echo 'ComboBodega.enable();
										  ComboBodega.clearValue();';
								}
				echo '			BodegaStore.load({params:{id:record.get(\'value\'),opcion:\'bodega\',MyFiltro:\'true\'}});
								//setTimeout(\'ComboBodega.setValue(LaBodegaGuardada_'.$this->InformeName.')\',800);
							}
						);	';
			}

			return $cuales;
		}
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

		public function GeneraInforme(){
			$this->InformeResult = str_replace ('.php', '_Result.php', $_SERVER['SCRIPT_NAME']);

			// SI LA VARIABLE MyFiltro EXISTE QUIERE DECIR QUE ESTA CONSULTANDO EL FILTRO DE EMPRESAS
			if(isset($this->MyFiltro)){

					if($this->opcion == 'BusquedaClientes'){
						include("class.MyInforme.Clientes.php");
					}

					if($this->opcion == 'empresa' || $this->opcion == 'zonas' || $this->opcion == 'sucursal'){//SI ES EMPRESA O SUCURSAL -> SOLO SIIP//
						$MEmpresas   = user_permisos(102);
						$MSucursales = user_permisos(103);

						if($MEmpresas == 'true'  && $MSucursales == 'true' ){$filtroE = ''; $filtroS = '';}
						if($MEmpresas == 'false' && $MSucursales == 'true' ){$filtroE = 'AND id = '.$_SESSION['EMPRESA']; $filtroS = '';}
						if($MEmpresas == 'false' && $MSucursales == 'false'){$filtroE = 'AND id = '.$_SESSION['EMPRESA']; $filtroS = 'AND id = '.$_SESSION['SUCURSAL'];}
						if($MEmpresas == 'true'  && $MSucursales == 'false'){$filtroE = ''; $filtroS = '';}
					}

					if($this->opcion == 'empresa'){
						$consul1 = mysql_query("SELECT id,nombre FROM empresas WHERE activo = 1 $filtroE",$this->Link);
						if($MEmpresas == 'true'  && $this->Filtro_Todos == 'true') {$FilTodos = 'true';}
						if($MEmpresas == 'false' && $this->Filtro_Todos == 'true') {$FilTodos = 'false';}
						if($MEmpresas == 'true'  && $this->Filtro_Todos == 'false'){$FilTodos = 'false';}
						if($MEmpresas == 'false' && $this->Filtro_Todos == 'false'){$FilTodos = 'false';}
						$PalabraTodos = 'TODAS';
					}

					if($this->opcion == 'zonas'){
						$consul1 = mysql_query("SELECT id,nombre FROM configuracion_zonas WHERE activo = 1 AND id_empresa = $this->id",$this->Link);
						if($MEmpresas == 'true'  && $MSucursales == 'true'  && $this->Filtro_Todos == 'true') {$FilTodos = 'true';}
						if($MEmpresas == 'true'  && $MSucursales == 'false' && $this->Filtro_Todos == 'true') {$FilTodos = 'true';}
						if($MEmpresas == 'true'  && $MSucursales == 'true'  && $this->Filtro_Todos == 'false'){$FilTodos = 'false';}
						if($MEmpresas == 'true'  && $MSucursales == 'false' && $this->Filtro_Todos == 'false'){$FilTodos = 'false';}
						if($MEmpresas == 'false' && $MSucursales == 'true'  && $this->Filtro_Todos == 'true') {$FilTodos = 'true';}
						if($MEmpresas == 'false' && $MSucursales == 'false' && $this->Filtro_Todos == 'true') {$FilTodos = 'false';}
						if($MEmpresas == 'false' && $MSucursales == 'true'  && $this->Filtro_Todos == 'false'){$FilTodos = 'false';}
						if($MEmpresas == 'false' && $MSucursales == 'false' && $this->Filtro_Todos == 'false'){$FilTodos = 'false';}
						$PalabraTodos = 'TODAS';
					}

					if($this->opcion == 'sucursal'){
						$consul1 = mysql_query("SELECT id_sucursal AS id,sucursal AS nombre FROM vista_sucursales_empresas WHERE zona = $this->id $filtroS",$this->Link);
						if($MEmpresas == 'true'  && $MSucursales == 'true'  && $this->Filtro_Todos == 'true') {$FilTodos = 'true';}
						if($MEmpresas == 'true'  && $MSucursales == 'false' && $this->Filtro_Todos == 'true') {$FilTodos = 'true';}
						if($MEmpresas == 'true'  && $MSucursales == 'true'  && $this->Filtro_Todos == 'false'){$FilTodos = 'false';}
						if($MEmpresas == 'true'  && $MSucursales == 'false' && $this->Filtro_Todos == 'false'){$FilTodos = 'false';}
						if($MEmpresas == 'false' && $MSucursales == 'true'  && $this->Filtro_Todos == 'true') {$FilTodos = 'true';}
						if($MEmpresas == 'false' && $MSucursales == 'false' && $this->Filtro_Todos == 'true') {$FilTodos = 'false';}
						if($MEmpresas == 'false' && $MSucursales == 'true'  && $this->Filtro_Todos == 'false'){$FilTodos = 'false';}
						if($MEmpresas == 'false' && $MSucursales == 'false' && $this->Filtro_Todos == 'false'){$FilTodos = 'false';}
						$PalabraTodos = 'TODAS';
					}
					if($this->opcion == 'bodega'){
						$consul1 = mysql_query("SELECT id,nombre FROM empresas_sucursales_bodegas WHERE activo = 1 AND id_sucursal = $this->id ",$this->Link);
						$FilTodos = $this->Filtro_Todos;
						$PalabraTodos = 'TODAS';
					}

					if($this->opcion != 'BusquedaClientes'){

						$data  = array();
						$total = mysql_num_rows($consul1);

						$cont = 0;
						if($FilTodos=='true'){
							array_push($data,array
								(
									'value'=>'%',
									'label'=>$PalabraTodos
								)
							);
						}

						$cont = 0;
						while($row = mysql_fetch_array($consul1)){
							array_push($data,array
								(
									'value'=>$row['id'],
									'label'=>utf8_encode($row['nombre'])
								)
							);
						}

						echo json_encode(array
							(
								'total'=>$total,
								'data'=>$data
							)
						);
					}
			}

			if(!isset($this->MyFiltro)){

					//CAPAS RECIBIDORAS DEL TOOLBAR Y EL AREA DEL INFORME
					echo '<link rel="stylesheet" type="text/css" href="'.$this->ElPathDeLaClase.'/MyInforme.css"/>';
					echo '<div id="InfoToolBar_'.$this->InformeName.'" style=""></div>';

					if($this->AreaInforme == 'true'){ //SI LLEVA AREA DEL INFORME
						echo '<div id="RecibidorPrincipalInforme_'.$this->InformeName.'" class="my_informe_fondo" style="float:left; width:100%; height:100%; overflow:auto; box-shadow:inset 0 4px 6px rgba(0,0,0,.5)">
									<div id="RecibidorInforme_'.$this->InformeName.'" class="my_informe_sombra" style="float:left; width:100%; height:auto; margin:20px; background-color:#fff; overflow:auto"></div>
							  </div>';

						echo '<script>';

						//SE DEFINE EL ANCHO Y ALTO DEL INFORME ////////////////////////////////////////////////////////////////////
						if($this->InformeAnchoPersonalizado==""){
							switch ($this->InformeTamano) {
								case 'CARTA-VERTICAL'   : echo "var MyInformeAnchoResult = 740;";	break;
								case 'CARTA-HORIZONTAL' : echo "var MyInformeAnchoResult = 1015;"; break;
								case 'OFICIO-VERTICAL'	: echo "var MyInformeAnchoResult = 740;";	break;
								case 'OFICIO-HORIZONTAL': echo "var MyInformeAnchoResult = 1200;";break;
							}
						}else{
							echo "var MyInformeAnchoResult = ".$this->InformeAnchoPersonalizado.";";
						}

						echo '
									var AreaInformeAncho		= \''.$this->AreaInformeAncho.'\';
									var AreaInformeAlto			= \''.$this->AreaInformeAlto.'\';
									var AreaInformeQuitaAncho	= \''.$this->AreaInformeQuitaAncho.'\';
									var AreaInformeQuitaAlto	= \''.$this->AreaInformeQuitaAlto.'\';

									var myalto  = Ext.getBody().getHeight();
									var myancho  = Ext.getBody().getWidth();

									if(AreaInformeQuitaAncho!=""){
										MyInformeAncho = myancho - AreaInformeQuitaAncho;
									}else{
										MyInformeAncho = AreaInformeAncho;
									}
									if(AreaInformeQuitaAlto!=""){
										MyInformeAlto = myalto	- AreaInformeQuitaAlto;
									}else{
										MyInformeAlto = AreaInformeAlto
									}

									document.getElementById("RecibidorPrincipalInforme_'.$this->InformeName.'").style.width = MyInformeAncho;
									document.getElementById("RecibidorPrincipalInforme_'.$this->InformeName.'").style.height = MyInformeAlto;
									document.getElementById("RecibidorInforme_'.$this->InformeName.'").style.width = MyInformeAnchoResult;
							 ';
						echo '</script>';

					}

					///////////////////////////////////////////////////////////////////////////////////////////////////////////
					echo '<script>';
					echo 'var InformeDebug = "";';

					$cuales = $this->CualesFiltrosEmpresas;


					if($this->Filtro_Empresa == "true"){
						$cuales = $this->GeneraFiltroEmpresa();
					}

					if($this->FiltroClientes == "true" && $this->FiltroClientesProspectos == "false"){
						$this->AddFiltroField('Cliente','Seleccione un Cliente','','true');//NO INCLUYE PROSPECTOS
 					}

					//CON ESTO ME ASEGURO DE QUE NO SE VAYA A CREAR FILTROS REDUNDANTES, SI ACTIVA FILTRO CLIENTES Y FILTRO PROSPECTOS ENTONCES PONE UN SOLO FILTRO CON PROSPECTOS
					if($this->FiltroClientesProspectos == "true" || ($this->FiltroClientes == "true" && $this->FiltroClientesProspectos == "true")){
						$this->AddFiltroField('Cliente','Seleccione un Cliente','','true','true');
					}

					if($this->FiltroFuncionarios == "true"){
						$this->AddFiltroField('Funcionario','Seleccione un Funcionario','','false','false','true');
					}

					//SE DEFINEN VARIABLES GLOBALES PARA GUARDAR LAS FECHAS
					echo '
							if(typeof(LaFechaInicialGuardada_'.$this->InformeName.') == "undefined"){var LaFechaInicialGuardada_'.$this->InformeName.' = "'.date('Y-m-d').'";}
							if(typeof(LaFechaFinalGuardada_'.$this->InformeName.') == "undefined"){var LaFechaFinalGuardada_'.$this->InformeName.' = "'.date('Y-m-d').'";}
							if(typeof(LaEmpresaGuardada_'.$this->InformeName.') == "undefined"){var LaEmpresaGuardada_'.$this->InformeName.' = "'.$_SESSION['EMPRESA'].'";}
							if(typeof(LaZonaGuardada_'.$this->InformeName.') == "undefined"){var LaZonaGuardada_'.$this->InformeName.' = "'.$_SESSION['ZONA'].'";}
							if(typeof(LaSucursalGuardada_'.$this->InformeName.') == "undefined"){var LaSucursalGuardada_'.$this->InformeName.' = "'.$_SESSION['SUCURSAL'].'";}
							if(typeof(LaBodegaGuardada_'.$this->InformeName.') == "undefined"){
					';
								if($this->Filtro_Todos == 'true'){
									echo 'var LaBodegaGuardada_'.$this->InformeName.' = "%";';
								}else{
									echo 'var LaBodegaGuardada_'.$this->InformeName.' = "";';
								}
					echo'	}';

					if($this->InformeTitle!=""){$NombreInforme = '<H1 align="center">'.$this->InformeTitle.'</H1>';}else{$NombreInforme = '';}

					echo '		new Ext.Panel(
									{

										renderTo	: \'InfoToolBar_'.$this->InformeName.'\',
										title		: \''.$NombreInforme.'\',
										border		: false,
										tbar		:
										[
						';

									//////////////////////////////////////////////////////////////////////////////////////////
									/////////////////////////////////////////////////////////////////////////////////////////
									//if($this->InformeEmpreZonaSucuBode == 'true' || $this->InformeEmpreSucuBode == 'true' || $this->InformeEmpreSucu == 'true'){
									if($cuales != ''){

										echo'	{
													xtype		: \'buttongroup\',
													height		: 125,
													title		: \'Filtros de Empresa\',
													items		:
													[

														{
															xtype			: 	\'form\',
															width			: 	265,
															id				:	\'Form_Filtros_ESB_'.$this->InformeName.'\',
															bodyStyle 		:	\'background-color:'.$_SESSION['COLOR_CONTRASTE'].';padding:5px 5px 0 5px\',
															border			: 	false,
															items			:	['.$cuales.']
														}

													]
												},
											';
									}
									//////////////////////////////////////////////////////////////////////////////////////////
									//////////////////////////////////////////////////////////////////////////////////////////
									if($this->InformeZonaTipoSubtipo == 'true' ){

										echo'	{
													xtype		: \'buttongroup\',
													//columns		: 3,
													height		: 100,
													title		: \'Filtros de Zona y Tipo\',
													items		:
													[
														{
															xtype			: \'panel\',
															border			: false,
															width			: 265,
															border			: false,
															bodyStyle 		: \'background-color:'.$_SESSION['COLOR_CONTRASTE'].';\',
															items		:
															[
																{
																	xtype			: 	\'form\',
																	id				:	\'Form_Filtros_ESB_'.$this->InformeName.'\',
																	bodyStyle 		:	\'background-color:'.$_SESSION['COLOR_CONTRASTE'].';padding:5px 5px 0 5px\',
																	border			: 	false,
																	items			:	['.$cuales.']
																}
															]
														}
													]
												},
											';
									}
									/////////////////////////////////////////////////////////////////////////////////////////
									/////////////////////////////////////////////////////////////////////////////////////////
									if($this->InformeFechaInicioFin == 'true'){

										$CuantosFilFech = 0;

										if($this->InformeFechaInicio=='true'){
											$this->AddCamposPorLeer('MyInformeFiltroFechaInicio_'.$this->InformeName);
											$CuantosFilFech = $CuantosFilFech + 1;
										}
										if($this->InformeFechaFin=='true'){
											$this->AddCamposPorLeer('MyInformeFiltroFechaFinal_'.$this->InformeName);
											$CuantosFilFech = $CuantosFilFech + 1;
										}


										echo'	{
													xtype		: \'buttongroup\',
													//columns		: 3,
													height		: 125,
													title		: \'Filtros de Fecha\',
													items		:
													[
														{
															xtype			: \'panel\',
															border			: false,
															width			: 210,
															border			: false,
															bodyStyle 		: \'background-color:'.$_SESSION['COLOR_CONTRASTE'].';\',
															items		:
															[
																{
																	xtype			: 	\'form\',
																	bodyStyle 		:	\'background-color:'.$_SESSION['COLOR_CONTRASTE'].';padding:5px 5px 0 5px\',
																	border			: 	false,
																	items			:
																		[ ';

																		if($this->InformeFechaInicio=='true'){
																			echo '
																			{
																				xtype			: \'datefield\',
																				format 			: \'Y-m-d\',
																				fieldLabel		: \'Fecha Inicial\',
																				id				: \'MyInformeFiltroFechaInicio_'.$this->InformeName.'\',
																				name			: \'MyInformeFiltroFechaInicio_'.$this->InformeName.'\',
																				editable 		: false,
																				width			: 90,
																				value			: LaFechaInicialGuardada_'.$this->InformeName.',
																				allowBlank		: false,';
																				if($this->InformeFechaFin=='true'){
																					echo '	vtype			: \'daterange\',
																							endDateField	: \'MyInformeFiltroFechaFinal_'.$this->InformeName.'\'
																						 ';
																				}
																			echo'}';
																		}

																		if($CuantosFilFech==2){echo',';}

																		if($this->InformeFechaFin=='true'){
																			echo '
																			{
																				xtype			: \'datefield\',
																				format 			: \'Y-m-d\',
																				fieldLabel		: \'Fecha Final\',
																				id				: \'MyInformeFiltroFechaFinal_'.$this->InformeName.'\',
																				name			: \'MyInformeFiltroFechaFinal_'.$this->InformeName.'\',
																				editable 		: false,
																				width			: 90,
																				value			: LaFechaFinalGuardada_'.$this->InformeName.',
																				allowBlank		: false
																			}';
																		}
																echo'	]
																}
															]
														}
													]
												},
											';
									}


									////////////////////////////////////////////////////////////////////////////////////////
									////////////////////////////////////////////////////////////////////////////////////////

									if($this->MyFiltros == 'true'){

										$i = 0;//SI YA AGREGO EL FILTRO DE FUNCIONARIOS CONTINUARA AGREGANDO LOS OTROS FILTROS

										for($cu=0;$cu<$this->MyFiltrosCuantos;$cu++){

											if($cu == 0){
												if($this->FiltroClientes == 'true' || $this->FiltroClientesProspectos == 'true'){
													$this->AddCamposPorLeer('MyInformeFiltro_Clientes_'.$this->InformeName);
												}else if(($this->FiltroClientes == 'false' && $this->FiltroClientesProspectos == 'false') && $this ->FiltroFuncionarios == 'true'){//SI SOLO ESTA EL DE FUNCIONARIOS LO COLOCA DE PRIMERO
													$this->AddCamposPorLeer('MyInformeFiltro_Funcionarios_'.$this->InformeName);
													$i = 1;//YA FUE AGREGADO AHORA SIGUE CON LOS OTROS FILTROS
												}
												else{
													$this->AddCamposPorLeer('MyInformeFiltro_'.$cu.'_'.$this->InformeName);
												}
											}else{
												if($this->FiltroFuncionarios == 'true' && $i == 0){
													$this->AddCamposPorLeer('MyInformeFiltro_Funcionarios_'.$this->InformeName);
													$i = 1;//YA FUE AGREGADO HORA SIGUE CON LOS OTROS FILTROS
												}
												else{
													$this->AddCamposPorLeer('MyInformeFiltro_'.$cu.'_'.$this->InformeName);
												}
											}
										}

										$termina = array();
										if($this->MyFiltrosCuantos==1 || $this->MyFiltrosCuantos==2 || $this->MyFiltrosCuantos==3 || $this->MyFiltrosCuantos==4){		$columnasForm = 1; $termina[0] = $this->MyFiltrosCuantos;}
										if($this->MyFiltrosCuantos==5 || $this->MyFiltrosCuantos==6 || $this->MyFiltrosCuantos==7 || $this->MyFiltrosCuantos==8){		$columnasForm = 2; $termina[1] = $this->MyFiltrosCuantos; $termina[0] = 4;}
										if($this->MyFiltrosCuantos==9 || $this->MyFiltrosCuantos==10 || $this->MyFiltrosCuantos==11 || $this->MyFiltrosCuantos==12){	$columnasForm = 3; $termina[2] = $this->MyFiltrosCuantos; $termina[0] = 4; $termina[1] = 8;}
										if($this->MyFiltrosCuantos==13 || $this->MyFiltrosCuantos==14 || $this->MyFiltrosCuantos==15 || $this->MyFiltrosCuantos==16){	$columnasForm = 4; $termina[3] = $this->MyFiltrosCuantos; $termina[0] = 4; $termina[1] = 8; $termina[2] = 12;}

										//$anchoPanel = $columnasForm * 265;

										echo'	{
													xtype		: \'buttongroup\',
													height		: 125,
													title		: \'Otros Filtros\',
													items		:
													[
											';

										$inicia = 0;
										$cualFil = 0;
										if($this->FiltroFuncionarios=='true'){
											$cualFil++;//si esta el filtro de funcionarios le aumenta un numero a los demas filtros
										}

										for($col=0;$col<$columnasForm;$col++){

											if($col == 0){$Heig = $termina[$col];}
											if($col == 1){$Heig = $termina[$col] - 4;}
											if($col == 2){$Heig = $termina[$col] - 8;}
											if($col == 3){$Heig = $termina[$col] - 12;}
											$height = 25 * $Heig;

											echo '
																{
																	xtype			: 	\'form\',
																	width			: 	265,
																	height			:  	100,
																	//layout		:   \'form\',
																	bodyStyle 		:	\'background-color:'.$_SESSION['COLOR_CONTRASTE'].';padding:5px 5px 0 5px\',
																	border			:	false,
																	items			:
																		[


												 ';
												 							for($i=$inicia;$i<$termina[$col];$i++){
																				if($this->MyFiltrosComboBox[$i] == 'true'){

																					echo '
																						{
																							xtype			:	\'combo\',
																							store			:	new Ext.data.ArrayStore({
																													fields	: [\'value\',\'label\'],
																													data 	: ['.$this->MyFiltrosArray[$i].']
																												}),
																							id				: 	\'MyInformeFiltro_'.$cualFil.'_'.$this->InformeName.'\',
																							editable 		: 	true,
																							forceSelection	:	true,
																							minChars		:   30,
																							width			: 	150,
																							listWidth 		: 	350,
																							mode			:	\'local\',
																							valueField		: 	\'value\',
																							displayField	: 	\'label\',
																							triggerAction	: 	\'all\',
																							emptyText		: 	\''.utf8_decode($this->MyFiltrosTextoVacio[$i]).'\',
																							fieldLabel		: 	\''.utf8_decode($this->MyFiltrosLabel[$i]).'\',
																							listeners: {
																								render: function() {
																					';
																									if($this->MyFiltrosDatoDefault[$i]!='false'){
																										echo 'Ext.getCmp(\'MyInformeFiltro_'.$cualFil.'_'.$this->InformeName.'\').setValue(\''.$this->MyFiltrosDatoDefault[$i].'\');';
																									};
																					echo '
																								},
																								keyup: function() {
																									   this.store.filter(\'label\', this.getRawValue(), true, false);
																								}
																							}
																						},
																					';
																				}else{

																					echo '
																						{
																							xtype			:	\'field\',
																					';
																						if($this->FiltroEspecialClientes[$i] == 'true'){
																							echo 'id				: 	\'MyInformeFiltro_Clientes_'.$this->InformeName.'\',';
																						}else if($this->FiltroEspecialFuncionarios[$i] == 'true'){
																							echo 'id				: 	\'MyInformeFiltro_Funcionarios_'.$this->InformeName.'\',';
																						}
																						else{
																							echo 'id				: 	\'MyInformeFiltro_'.$cualFil.'_'.$this->InformeName.'\',';
																						}
																					echo '
																							minChars		:   30,
																							width			: 	150,
																							displayField	: 	\'label\',
																							emptyText		: 	\''.utf8_decode($this->MyFiltrosTextoVacio[$i]).'\',
																							fieldLabel		: 	\''.utf8_decode($this->MyFiltrosLabel[$i]).'\',
																					';
																						if($this->FiltroEspecialClientes[$i] == 'true'){

																							$prospectos = 'false';
																							if($this->FiltroEspecialClientesProspectos[$i] == 'true'){
																								$prospectos = 'true';
																							}

																							echo '	listeners: {
																										focus: function() {
																											Win_Busca_COP = new Ext.Window(
																												{
																													id			: \'win_bus_cli_'.$this->InformeName.'\',
																													border		: false,
																													plain		: false,
																													width		: 625,
																													height		: Ext.getBody().getHeight() - 20,
																													autoDestroy : true,
																													modal		: true,
																													autoScroll	: true,
																													closable	: false,
																													bodyStyle	: "background-color:#FFF",
																													autoLoad	: {
																														url		:\''.$_SERVER['SCRIPT_NAME'].'\',
																														//url		:\'class.MyInforme.Clientes.php\',
																														scripts	:true,
																														nocache	:true,
																														params	:{MyFiltro:\'true\', opcion:\'BusquedaClientes\', prospectos : \''.$prospectos.'\'}
																													}
																												}
																											 ).show();
																										}
																									}';
																						}
																						else if($this->FiltroEspecialFuncionarios[$i] == 'true'){
																							echo '	listeners: {
																										 render: function(cmp) { cmp.getEl().on(\'click\', function() {
																												Win_BuscarFuncionario = new Ext.Window({
																													id			: \'Win_BuscarFuncionario\',
																													width		: 600,
																													height		: 450,
																													/*boxMaxHeight: 550,*/
																													title		: \'Buscar Funcionario\' ,
																													iconCls 	: \'user16\',
																													modal		: true,
																													autoScroll	: true,
																													closable	: true,
																													autoDestroy : true,
																													autoLoad	:
																													{
																														url		: \'../funcionarios/busqueda_funcionarios.php\',
																														scripts	: true,
																														nocache	: true,
																														params	:
																																{filtro_informe : \'true\',id_filtro : \'MyInformeFiltro_Funcionarios_'.$this->InformeName.'\',permisos : \'true\'}
																													}
																												}).show();
																											});
																										}
																									}';
																						}
																						else{
																							echo 'listeners: {
																									render: function() {
																						';
																										if($this->MyFiltrosDatoDefault[$i]!='false'){
																											echo 'Ext.getCmp(\'MyInformeFiltro_'.$cualFil.'_'.$this->InformeName.'\').setValue(\''.$this->MyFiltrosDatoDefault[$i].'\');';
																										};
																							echo '
																									},
																									keyup: function() {
																										   this.store.filter(\'label\', this.getRawValue(), true, false);
																									}
																								}';
																						}
																					echo '

																						},
																					';
																				}
																				$cualFil++;

																			}

											echo '
																		]
																}
											';
											if($col<$columnasForm-1){echo ',';}
											$inicia = $inicia+4;
										}
										echo'		]
												},
											';
									}

							$MyPathScript = str_replace ('.php', '_Result.php', $_SERVER['SCRIPT_NAME']);
							////////////////////////////////////////////////////////////////////////////////////////////////
							////////////////////////////////////////////////////////////////////////////////////////////////
							if($this->BtnGenera == 'true'){$BtnDisable = 'false';}else{$BtnDisable = 'true';}

							if($this->AddElements <> ''){ echo $this->AddElements; }
							echo'			{
												xtype		: \'buttongroup\',
												columns		: 3,
												height		: '.$this->HeightToolbar.',
												title		: \'Generacion\',
												items		:
												[
													{
														text		: \'Generar Informe\',
														scale		: \'large\',
														iconCls		: \'genera_informe\',
														iconAlign	: \'top\',
														cls			: \''.$this->DefaultCls.'\',
														hidden		: '.$BtnDisable.',
														handler		: function(){
								';
																		if($this->FuncionGenerarCustom==""){

																			if($this->Filtro_Empresa == 'true' ){
																				echo 'LaEmpresaGuardada_'.$this->InformeName.'  = Ext.getCmp("MyInformeFiltroEmpresa_'.$this->InformeName.'").value;';
																			}
																			if($this->Filtro_Zona == 'true'  ){
																				echo 'LaZonaGuardada_'.$this->InformeName.'  = Ext.getCmp("MyInformeFiltroZona_'.$this->InformeName.'").value;';
																			}
																			if($this->Filtro_Sucursal == 'true'  ){
																				echo 'LaSucursalGuardada_'.$this->InformeName.' = Ext.getCmp("MyInformeFiltroSucursal_'.$this->InformeName.'").value;';
																			}
																			if($this->Filtro_Bodega == 'true'  ){
																				echo 'LaBodegaGuardada_'.$this->InformeName.'   = Ext.getCmp("MyInformeFiltroBodega_'.$this->InformeName.'").value;';
																			}
																			if($this->InformeFechaInicioFin == 'true'){
																				if($this->InformeFechaInicio == 'true'){
																					echo 'LaFechaInicialGuardada_'.$this->InformeName.' = Ext.getCmp("MyInformeFiltroFechaInicio_'.$this->InformeName.'").value;';
																				}
																				if($this->InformeFechafin == 'true'){
																					echo 'LaFechaFinalGuardada_'.$this->InformeName.' 	 = Ext.getCmp("MyInformeFiltroFechaFinal_'.$this->InformeName.'").value;';
																				}
																			}

																			for($i=0;$i<count($this->CamposPorLeer);$i++){
																				$ElNombreDeLaVariable = str_replace ('_'.$this->InformeName,'',$this->CamposPorLeer[$i]);
																				echo ' if(Ext.getCmp("'.$this->CamposPorLeer[$i].'").getXType() == "field"){
																							var '.$ElNombreDeLaVariable.'  = Ext.getCmp("'.$this->CamposPorLeer[$i].'").getValue();
																						}else{
																							var '.$ElNombreDeLaVariable.'  = Ext.getCmp("'.$this->CamposPorLeer[$i].'").value;
																						}
																					';

																			}

																			echo'
																				Ext.get("RecibidorInforme_'.$this->InformeName.'").load(
																					{
																						url		:	\''.$this->InformeResult.'\',
																						text	:	\'Generando Informe...\',
																						scripts	:	true,
																						nocache	:	true,
																						params	:	{
																							nombre_informe : \''.$this->InformeTitle.'\',
																				';
																							for($i=0;$i<count($this->CamposPorLeer);$i++){
																								$ElNombreDeLaVariable = str_replace ('_'.$this->InformeName,'',$this->CamposPorLeer[$i]);
																								echo $ElNombreDeLaVariable.'  : '.$ElNombreDeLaVariable;
																								if($i<(count($this->CamposPorLeer)-1)){echo ',';}
																							}
																			echo'
																						}
																					}
																				);
																				document.getElementById("RecibidorInforme_'.$this->InformeName.'").style.padding = 20;
																			';
																		}else{
																			echo $this->FuncionGenerarCustom;
																		}

								echo '								  }
													}
								';
							if($this->InformeExportarPDF=='true'){
								echo'					,{
															text		: \'Exportar a PDF\',
															scale		: \'large\',
															iconCls		: \'genera_pdf\',
															iconAlign	: \'top\',
															cls			: \''.$this->DefaultCls.'\',
															handler		: function(){
																				var ElEnvioPost = "&nombre_informe='.$this->InformeTitle.'";
									';
																				for($i=0;$i<count($this->CamposPorLeer);$i++){
																					$ElNombreDeLaVariable = str_replace ('_'.$this->InformeName,'',$this->CamposPorLeer[$i]);
																					echo ' if(Ext.getCmp("'.$this->CamposPorLeer[$i].'").getXType() == "field"){
																								var '.$ElNombreDeLaVariable.'  = Ext.getCmp("'.$this->CamposPorLeer[$i].'").getValue();
																							}else{
																								var '.$ElNombreDeLaVariable.'  = Ext.getCmp("'.$this->CamposPorLeer[$i].'").value;;
																							}
																					';

																					//echo 'var '.$ElNombreDeLaVariable.'  = Ext.getCmp("'.$this->CamposPorLeer[$i].'").value;';
																					echo 'ElEnvioPost = ElEnvioPost+"&'.$ElNombreDeLaVariable.'="+'.$ElNombreDeLaVariable.';';
																				}
																				echo 'window.open("'.$this->InformeResult.'?IMPRIME_PDF=true"+ElEnvioPost);';
								echo'										  }
														}
									';
							}
							if($this->InformeExportarXLS=='true'){
								echo'					,{
															text		: \'Exportar a Excel\',
															scale		: \'large\',
															iconCls		: \'excel32\',
															iconAlign	: \'top\',
															cls			: \''.$this->DefaultCls.'\',
															handler		: function(){
																				var ElEnvioPost = "&nombre_informe='.$this->InformeTitle.'";
									';
																				for($i=0;$i<count($this->CamposPorLeer);$i++){
																					$ElNombreDeLaVariable = str_replace ('_'.$this->InformeName,'',$this->CamposPorLeer[$i]);

																					echo ' if(Ext.getCmp("'.$this->CamposPorLeer[$i].'").getXType() == "field"){
																								var '.$ElNombreDeLaVariable.'  = Ext.getCmp("'.$this->CamposPorLeer[$i].'").getValue();
																							}else{
																								var '.$ElNombreDeLaVariable.'  = Ext.getCmp("'.$this->CamposPorLeer[$i].'").value;;
																							}
																					';

																					echo 'ElEnvioPost = ElEnvioPost+"&'.$ElNombreDeLaVariable.'="+'.$ElNombreDeLaVariable.';';
																				}
																				echo 'window.open("'.$this->InformeResult.'?IMPRIME_XLS=true"+ElEnvioPost);';
								echo'							}
														}
									';
							}
							echo'				]
											},';

							// Si existen nuevos botones
							if($this->NewButtons <> ''){

								echo'{
							            xtype   : \'buttongroup\',
										height	: '.$this->HeightToolbar.',
							            columns : 3,
							            title   : \'Opciones\',
							            style   : \'border-right:none;\',
							            items   :
							            ['.$this->NewButtons.']
	        						}';
							}

							echo '		]
									}
								);
						';
								if($this->InformeFechaInicioFin == 'true'){
									if($this->InformeFechaInicio == 'true'){
										echo 'InformeDebug = InformeDebug+"MyInformeFiltroFechaInicio_'.$this->InformeName.'\n";';
									}
									if($this->InformeFechaInicio == 'true'){
										echo 'InformeDebug = InformeDebug+"MyInformeFiltroFechaFinal_'.$this->InformeName.'\n";';
									}
								}

								if($this->MyFiltros == "true"){
									for($cu=0;$cu<$this->MyFiltrosCuantos;$cu++){
										echo 'InformeDebug = InformeDebug+"MyInformeFiltro_'.$cu.'_'.$this->InformeName.'\n";';
									}
								}

								if($this->InformeDebug=='true'){echo 'alert(InformeDebug)';}

						echo'
						</script>
					';
			}
		}
}

?>