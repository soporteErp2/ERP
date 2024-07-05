<?php
	include("../../configuracion/conectar.php");
	include("../../configuracion/define_variables.php");
	include("../../misc/MyGrilla/class.MyGrilla.php");
	$id_empresa = $_SESSION['EMPRESA'];
	$grupo      = array();
	if($opcion == 'Vupdate' || $opcion == 'Vagregar'){
		$sql="SELECT
				inventario_grupo.id AS codigo_grupo,
				id_cuenta_depreciacion_colgaap_debito,
				cuenta_depreciacion_colgaap_debito,
				id_cuenta_depreciacion_colgaap_credito,
				cuenta_depreciacion_colgaap_credito,
				id_cuenta_depreciacion_niif_debito,
				cuenta_depreciacion_niif_debito,
				id_cuenta_depreciacion_niif_credito,
				cuenta_depreciacion_niif_credito,
				id_cuenta_deterioro_debito,
				cuenta_deterioro_debito,
				id_cuenta_deterioro_credito,
				cuenta_deterioro_credito
			FROM
				cuentas_default_activos_fijos, inventario_grupo
			WHERE
				inventario_grupo.codigo_grupo = cuentas_default_activos_fijos.codigo_grupo
			AND
				cuentas_default_activos_fijos.activo = 1
			AND cuentas_default_activos_fijos.id_empresa = $id_empresa
			GROUP BY	cuentas_default_activos_fijos.id";
			$query =  $mysql->query($sql,$mysql->link);
			while($row = $mysql->fetch_assoc($query)){
				$grupo[$row['codigo_grupo']] = array(
											'id_cuenta_depreciacion_colgaap_debito'  =>$row['id_cuenta_depreciacion_colgaap_debito'],
											'cuenta_depreciacion_colgaap_debito'     =>$row['cuenta_depreciacion_colgaap_debito'],

											'id_cuenta_depreciacion_colgaap_credito' =>$row['id_cuenta_depreciacion_colgaap_credito'],
											'cuenta_depreciacion_colgaap_credito'    =>$row['cuenta_depreciacion_colgaap_credito'],

											'id_cuenta_depreciacion_niif_credito'    =>$row['id_cuenta_depreciacion_niif_credito'],
											'cuenta_depreciacion_niif_credito'       =>$row['cuenta_depreciacion_niif_credito'],

											'id_cuenta_depreciacion_niif_debito'     =>$row['id_cuenta_depreciacion_niif_debito'],
											'cuenta_depreciacion_niif_debito'        =>$row['cuenta_depreciacion_niif_debito'],

											'id_cuenta_deterioro_debito'             =>$row['id_cuenta_deterioro_debito'],
											'cuenta_deterioro_debito'                =>$row['cuenta_deterioro_debito'],

											'id_cuenta_deterioro_credito'            =>$row['id_cuenta_deterioro_credito'],
											'cuenta_deterioro_credito'               =>$row['cuenta_deterioro_credito']
											);
			}
			// print_r($grupo);
			$grupo_inventario = json_encode($grupo);
	}

	/**//////////////////////////////////////////////**/
	/**///		 INICIALIZACION DE LA CLASE  	  ///**/
	/**/	      $grilla = new MyGrilla();	      ///**/
	/**//////////////////////////////////////////////**/
	//CONFIGURACION//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

		//NOMBRE DE LA GRILLA
			$grilla->GrillaName	 		= 'ActivosFijos';  	//NOMBRE DE LA GRILLA (DEBE SER UNICO POR CADA GRILLA DE LA APLICACION)
		//QUERY
			$grilla->TableName			= 'activos_fijos';		//NOMBRE DE LA TABLA DE CONSULTA EN LA BASE DE DATOS DE
			$grilla->TableName2			= 'activos_fijos';			//NOMBRE DE LA TABLA DE INSERT Y UPDATE EN LA BASE DE DATOS DE
			$grilla->MyWhere			= "activo = 1 AND id_empresa = '$id_empresa' AND id_sucursal=$filtro_sucursal AND id_bodega=$filtro_ubicacion";			//WHERE DE LA CONSULTA A LA TABLA "$TableName"
			$grilla->OrderBy 			= 'estado ASC, id DESC';
			$grilla->MySqlLimit			= '0,100';			//LIMITE DE LA CONSULTA
		//TAMANO DE LA GRILLA
			$grilla->AutoResize	 		= 'true';			//SI LA GRILLA ES AUTORESIZABLE (LIQUIDA) -> "true" SI NO -> "false"
			//$grilla->Ancho		 	= 800;				//ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'
			//$grilla->Alto		 		= 220;				//ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'
			$grilla->QuitarAncho		= 40;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'true'
			$grilla->QuitarAlto			= 260;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'true'
		//TOOLBAR Y CAMPO DE BUSQUEDA
			$grilla->Gtoolbar			= 'true';			//SI LA GRILLA LLEVA EL TOOLBAR DE BUSQUEDA
			$grilla->CamposBusqueda		= 'nombre_equipo,grupo,subgrupo,centro_costos,bodega';		//VARIABLE QUE DEFINE LOS CAMPOS DE LA BD DONDE SE BUSCARA
			$grilla->DivActualiBusqueda = '' ;				//VARIABLE QUE DEFINE LA CAPA DONDE SE ACTUALIZA LA GRILLA DESPUES DE UNA BUSQUEDA
		//CONFIGURACION DE CAMPOS EN LA GRILLA
	 		$grilla->AddRowImage('Estado','<center><img src="images\estado_activo/[estado].png" style="cursor:pointer" width="16" height="16" id="imgEstadoFacturaCompra_[id]" /></center>','40');
	 		$grilla->AddRowImage('Documento','[documento_referencia] - [documento_referencia_consecutivo]','70');
			$grilla->AddRow('Codigo item','code_bar',80);
			$grilla->AddRow('Codigo Activo','codigo_activo',80);
			$grilla->AddRow('Nombre del Activo','nombre_equipo',250);
			$grilla->AddRow('Centro de Costo','centro_costos',100);
			$grilla->AddRow('Grupo','grupo',150);
			$grilla->AddRow('Subgrupo','subgrupo',150);
			$grilla->AddRow('Bodega','bodega',200);

			$grilla->FContenedorAncho		= 500;
			$grilla->FColumnaGeneralAncho	= 250;
			$grilla->FColumnaGeneralAlto	= 25;
			$grilla->FColumnaLabelAncho		= 250;
			$grilla->FColumnaFieldAncho		= 250;
		//CONFIGURACION DE LA VENTANA DE INSERT, UPDATE Y DELETE
			$grilla->VentanaAuto			= 'false';			//SI LA VENTANA DE INSERT, UPDATE Y DELETE ES AUTOMATICA O MANUAL
			$grilla->TituloVentana			= 'Ventana Activo Fijo'; //NOMBRE DE LA VENTANA DE INSER, UPDATE Y DELETE
			$grilla->CerrarDespuesDeAgregar = 'false';
			$grilla->VBarraBotones			= 'true';			//SI HAY O NO BARRA DE BOTONES
			$grilla->VBotonNuevo			= 'true';			//SI LLEVA EL BOTON DE AGREGAR REGISTRO
			$grilla->VBotonNText			= 'Nuevo Activo Fijo'; 	//TEXTO DEL BOTON DE NUEVO REGISTRO
			$grilla->VBotonNImage			= 'addequipo';		//IMAGEN CSS DEL BOTON
			$grilla->VAutoResize			= 'false';			//SI LA VENTANA ES AUTORESIZABLE (LIQUIDA) -> "true" SI NO -> "false"
			$grilla->VAncho		 			= 100;				//ANCHO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'false'
			$grilla->VAlto		 			= 100;				//ALTO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'false'
			// $grilla->VQuitarAncho		= 100;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ANCHO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'true'
			// $grilla->VQuitarAlto			= 50;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ALTO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'true'
			$grilla->VAutoScroll			= 'true';			//SI LA VENTANA TIENE O NO AUTOSCROLL
			$grilla->VBotonEliminar			= 'false';			//SI MUESTRA BOTON DE ELIMINAR EN LA VENTANA DE EDICION
			$grilla->VComporEliminar		= 'true';			//COMPORTAMIENTO DEL BOTON DE ELIMINAR ("true" ES CAMPO ACTIVO DE 1 A 0) ("false" -> ELIMINA EL REGISTRO DE LA BASE DE DATOS)

		//BOTONES ADICIONALES EN EL TOOLBAR DE LA VENTANA DE INSERT DELETE Y UPDATE
 			$grilla->AddBottonVentana('Eliminar','eliminar','ventana_eliminar_campo_inventario()','false','true');

 		//CONFIGURACION DEL MENU CONTEXTUAL
 			$grilla->MenuContext		= 'true';		//MENU CONTEXTUAL
	 		$grilla->MenuContextEliminar= 'false';

		//OPCIONES ADICIONALES EN EL MENU CONTEXTUAL
			$grilla->AddMenuContext('Imprimir Codigo de Barras','barcode16','ventana_codigo_barras([id])');
			$grilla->AddMenuContext('Ficha tecnica','informe0','ventana_ficha_tecnica([id])');
			$grilla->AddMenuContext('Historico del Activo','informe0','historico_activo([id])');
			$grilla->AddMenuContext('Dar de baja el activo','baja_activo','ventana_baja_activo([id],\'[nombre_equipo]\')');
			// $grilla->AddMenuContext('Documento Inventario','doc','inventario_documentos([id])');
			// $grilla->AddMenuContext('Historico Inventario','doc','historico_inventario([id])');

		//CONFIGURACION DE LOS FORMULARIOS DE CAPTURA Y EDICION

			/*--------campos encadenados Grupo-SubGrupo-Tipo-------------*/
			$grilla->AddSeparator('Clasificacion');

			$grilla->AddTextField('','id_empresa',240,'false','hidden', $_SESSION['EMPRESA']);
			$grilla->AddTextField('','id_usuario_creacion',240,'false','hidden', $_SESSION['IDUSUARIO']);

			$grilla->AddComboBox ('Grupo de Inventario','id_grupo',240,'true','true','inventario_grupo,id,nombre_grupo','activo = 1');
			$grilla->AddComboBox ('Subgrupo de Inventario','id_subgrupo',240,'true','true','inventario_grupo_subgrupo,id,nombre_subgrupo,true','activo = 1');

			$grilla->AddTextField('','id_centro_costos',240,'false','hidden', '');
			$grilla->AddTextField('Centro de Costos','centro_costos',240,'false','false');
			// $grilla->AddTextField('Depreciable','depreciable',240,'false','false');
			$grilla->AddComboBox('Depreciable','depreciable',240,'true','false','Si:Si,No:No');

			/*------------------------- otros Campos ------------------------*/
			$arrayTipoActivoFijo = 'terreno:Terreno,'.
								   'equipo_oficina:Equipo de oficina,'.
								   'maquinaria:Maquinaria y Equipo, '.
								   'equipo_computo_comunicacion:Equipo de Computo y Comunicacion,'.
								   'construcciones_edificaciones:Construcciones y edificaciones';

			$grilla->AddSeparator('Informacion General');
			$grilla->AddTextField('Nombre Del Activo','nombre_equipo',240,'true','false');
			$grilla->AddComboBox('Tipo','tipo',240,'true','false',$arrayTipoActivoFijo);
			$grilla->AddTextField('Fecha Compra','fecha_compra',240,'true');
			$grilla->AddTextField('Documento de ingreso','documento_referencia',240,'true','false');
			$grilla->AddTextField('Consecutivo documento de ingreso','documento_referencia_consecutivo',240,'true','false');
			$grilla->AddTextField('Costo','costo',240,'true','false');

			$grilla->AddTextField('Codigo de Barras','code_bar',240,'false','false');
			// $grilla->AddTextField('Codigo del Activo','codigo_activo',240,'false','false');
			$grilla->AddTextField('Codigo del Activo','codigo_activo',240,'true','false');
			$grilla->AddTextField('Fecha Vencimiento Garantia','fecha_vencimiento_garantia',240,'true');
			$grilla->AddTextField('Marca','marca',240,'false','false');
			$grilla->AddTextField('Modelo','modelo',240,'false','false');
			$grilla->AddComboBox ('Unidad de medida','unidad',240,'true','true','inventario_unidades,nombre,nombre','activo = 1 AND id_empresa='.$id_empresa);
			$grilla->AddTextField('Numero de Piezas','numero_piezas',240,'false','false');
			$grilla->AddTextField('Color','color',240,'false','false');
			$grilla->AddTextArea('Descripci&oacute;n 1','descripcion1',240,50,'false');
			$grilla->AddTextArea('Descripci&oacute;n 2','descripcion2',240,50,'false');

			/*==================== DEPRECIACION ====================*/
			$grilla->AddSeparator('Informacion Contable Colgaap');
			$grilla->AddTextField('Fecha Inicio Depreciacion','fecha_inicio_depreciacion',240,'true');
			$grilla->AddValidation('fecha_inicio_depreciacion','fecha');
			$grilla->AddComboBox('Metodo Depreciacion Colgaap','metodo_depreciacion_colgaap',240,'true','false','linea_recta:Linea Recta,reduccion_saldos:Reduccion de Saldos,suma_digitos_year:Suma de los Digitos del A&ntilde;o');
			$grilla->AddValidation('fecha_compra','fecha');
			$grilla->AddValidation('fecha_vencimiento_garantia','fecha');

			$grilla->AddTextField('Vida Util (en a&ntilde;os)','vida_util',240,'true','false');
			$grilla->AddTextField('Valor de Salvamento','valor_salvamento',240,'false','false');
			//:::::::::::::::::::::::::::::::::::::::: CUENTAS
			$grilla->AddTextField('Cuenta colgaap depreciacion (activo)','cuenta_depreciacion',240,'true','false');
			$grilla->AddTextField('ContraPartida colgaap Depreciacion (gasto)','contrapartida_depreciacion',240,'true','false');

			$grilla->AddSeparator('Informacion Contable Niif');

			$grilla->AddComboBox('Metodo Depreciacion Niif','metodo_depreciacion_niif',240,'true','false','linea_recta:Linea Recta,reduccion_saldos:Reduccion de Saldos,suma_digitos_year:Suma de los Digitos del A&ntilde;o');
			// $grilla->AddComboBox('Metodo Depreciacion Niif','metodo_depreciacion_niif',240,'true','false','linea_recta:Linea Recta,reduccion_saldos:Reduccion de Saldos');
			$grilla->AddTextField('Fecha Inicio Depreciacion','fecha_inicio_depreciacion_niif',240,'true');
			$grilla->AddValidation('fecha_inicio_depreciacion_niif','fecha');
			$grilla->AddTextField('Vida Util (en a&ntilde;os)','vida_util_niif',240,'true','false');
			$grilla->AddTextField('Valor de Salvamento','valor_salvamento_niif',240,'false','false');

			$grilla->AddTextField('Cuenta niif depreciacion (activo)','cuenta_depreciacion_niif',240,'true','false');
			$grilla->AddTextField('ContraPartida niif Depreciacion (gasto)','contrapartida_depreciacion_niif',240,'true','false');

			$grilla->AddTextField('','id_cuenta_depreciacion_niif',240,'true','true');//campo oculto
			$grilla->AddTextField('','id_contrapartida_depreciacion_niif',240,'true','true');//campo oculto

			$grilla->AddTextField('Cuenta niif deterioro (debito)','cuenta_deterioro_niif_debito',240,'true','false');
			$grilla->AddTextField('ContraPartida niif deterioro (credito)','cuenta_deterioro_niif_credito',240,'true','false');

			$grilla->AddTextField('','id_cuenta_deterioro_niif_debito',240,'true','true');//campo oculto
			$grilla->AddTextField('','id_cuenta_deterioro_niif_credito',240,'true','true');//campo oculto

			// $grilla->AddComboBox('Cuenta depreciacion (activo)','cuenta_depreciacion',150,'true','true','puc,id,cuenta,true',' id_empresa='.$id_empresa.' AND activo=1 AND cuenta like "1592%" AND cuenta>99999');
			// $grilla->AddComboBox('ContraPartida Depreciacion (gasto)','contrapartida_depreciacion',150,'true','true','puc,id,cuenta,true',' id_empresa='.$id_empresa.' AND activo=1 AND cuenta like "5160%" AND cuenta>99999');

			$grilla->AddValidation('vida_util','numero');
			$grilla->AddValidation('vida_util_niif','numero');
			$grilla->AddValidation('valor_salvamento','numero-real');
			$grilla->AddValidation('valor_salvamento_niif','numero-real');
			$grilla->AddValidation('documento_referencia_consecutivo','numero');
	/**//////////////////////////////////////////////////////////////**/
	/**///				INICIALIZACION DE LA GRILLA	  			  ///**/
	/**/	$grilla->Link = $link;  	//Conexion a la BD			/**/
	/**/	$grilla->inicializa($_POST);//variables POST			/**/
	/**/	$grilla->GeneraGrilla(); 	// Inicializa la Grilla		/**/
	/**/															/**/
	/**//////////////////////////////////////////////////////////////**/
/***********************************************************************************/
/////////////////////////////////////////////////////////////////////////////////////
if($opcion == 'Vupdate' || $opcion == 'Vagregar'){ ?>

<script>
	var grupo      = document.getElementById("ActivosFijos_id_grupo");
	grupo.onchange = handler;
	function handler(obj){
		var grupo    = document.getElementById("ActivosFijos_id_grupo");
		var id_gurpo = grupo.options[grupo.selectedIndex].value;
		var json     = JSON.parse(JSON.stringify(<?php echo $grupo_inventario ?>));
		for (var item in json) {
			if(id_gurpo == item){
				var json = json[item];
				//::::::::::::::::::::::::: PARA COLGAAP
				document.getElementById('ActivosFijos_cuenta_depreciacion').value        = json.cuenta_depreciacion_colgaap_credito;
				document.getElementById('ActivosFijos_contrapartida_depreciacion').value = json.cuenta_depreciacion_colgaap_debito;
				//::::::::::::::::::::::::: PARA NIFF
				document.getElementById('ActivosFijos_cuenta_depreciacion_niif').value           = json.cuenta_depreciacion_niif_credito;
				document.getElementById('ActivosFijos_id_cuenta_depreciacion_niif').value        = json.id_cuenta_depreciacion_niif_credito;
				document.getElementById('ActivosFijos_contrapartida_depreciacion_niif').value    = json.cuenta_depreciacion_niif_debito;
				document.getElementById('ActivosFijos_id_contrapartida_depreciacion_niif').value = json.id_cuenta_depreciacion_niif_debito;
				//::::::::::::::::::::::::: PARA DETERIOROR NIIF
				document.getElementById('ActivosFijos_cuenta_deterioro_niif_debito').value     = json.cuenta_deterioro_debito;
				document.getElementById('ActivosFijos_id_cuenta_deterioro_niif_debito').value  = json.id_cuenta_deterioro_debito;
				document.getElementById('ActivosFijos_cuenta_deterioro_niif_credito').value    = json.cuenta_deterioro_credito;
				document.getElementById('ActivosFijos_id_cuenta_deterioro_niif_credito').value = json.id_cuenta_deterioro_credito;
			}
		}
	}


	//========================== CUENTAS DEPRECIACION ==========================//
	agregarBtnBuscarCuenta(document.getElementById('ActivosFijos_cuenta_depreciacion'));
	agregarBtnBuscarCuenta(document.getElementById('ActivosFijos_contrapartida_depreciacion'));
	agregarBtnBuscarCuenta(document.getElementById('ActivosFijos_cuenta_depreciacion_niif'));
	agregarBtnBuscarCuenta(document.getElementById('ActivosFijos_contrapartida_depreciacion_niif'));
	agregarBtnBuscarCuenta(document.getElementById('ActivosFijos_cuenta_deterioro_niif_debito'));
	agregarBtnBuscarCuenta(document.getElementById('ActivosFijos_cuenta_deterioro_niif_credito'));

	agregarBtnSincronizarCuenta(document.getElementById('ActivosFijos_cuenta_depreciacion'));
	agregarBtnSincronizarCuenta(document.getElementById('ActivosFijos_contrapartida_depreciacion'));

	inputCentroCostos          = document.getElementById('ActivosFijos_centro_costos');
	inputCentroCostos.readOnly = true;
	inputCentroCostos.setAttribute("onclick","ventanaBuscarCentroCostos()");

	function ventanaBuscarCentroCostos() {

		Win_Ventana_buscar_centro_costos = new Ext.Window({
		    width       : 540,
		    height      : 450,
		    id          : 'Win_Ventana_buscar_centro_costos',
		    title       : 'Buscar Centro de Costos',
		    modal       : true,
		    autoScroll  : false,
		    closable    : false,
		    autoDestroy : true,
		    autoLoad    :
		    {
		        url     : '../funciones_globales/grillas/grillaBuscarCentroCostos.php',
		        scripts : true,
		        nocache : true,
		        params  :
		        {
		            opc : 'ActivosFijos',
		            carpeta_img : 'images',
		        }
		    },
		    tbar        :
		    [
		        {
		            xtype   : 'buttongroup',
		            columns : 3,
		            title   : 'Opciones',
		            items   :
		            [
		                {
		                    xtype       : 'button',
		                    width       : 60,
		                    height      : 56,
		                    text        : 'Regresar',
		                    scale       : 'large',
		                    iconCls     : 'regresar',
		                    iconAlign   : 'left',
		                    handler     : function(){ Win_Ventana_buscar_centro_costos.close(id) }
		                }
		            ]
		        }
		    ]
		}).show();
	}

	ActualizaCentroCostosItem();
	function ActualizaCentroCostosItem(){
		var MyParent = document.getElementById('ActivosFijos_centro_costos').parentNode;
		Ext.get(MyParent).load({
			url		: 'bd/bd.php',
			timeout : 180000,
			scripts	: true,
			nocache	: true,
			params	:
			{
				idItem : '<?php echo $id?>',
				op     : 'OptionCentroCostos'
			}
		});
	}

	//ELIMINAR EL CENTRO DE COSTOS
	function eliminaCcosItem () {
		document.getElementById('imgEliminarCcos').setAttribute('onclick','ventanaBuscarCentroCostos()');
		document.getElementById('imgEliminarCcos').style.backgroundImage="url('images/buscar20.png')";
		document.getElementById('imgEliminarCcos').setAttribute('title','Buscar Centro de Costos');

		document.getElementById('ActivosFijos_id_centro_costos').value = '';
		document.getElementById('ActivosFijos_centro_costos').value    = '';

	}

	function agregarBtnBuscarCuenta(input){
		input.readOnly = true;
		input.setAttribute("style","float:left; width:175px;");

		var idInput     = input.id;
		var arrayId     = idInput.split('_');
		var opcion      = arrayId[1];
		var typeVentana = arrayId[3];
		// console.log('"'+opcion+'","'+typeVentana+'","'+idInput+'"');
		var btnBuscarCuenta = document.createElement('div');
		btnBuscarCuenta.setAttribute('class','divBtnBuscarPuc');
		btnBuscarCuenta.setAttribute('title','Buscar cuenta');
		btnBuscarCuenta.setAttribute('onclick','ventanaBuscarCuenta("'+opcion+'","'+typeVentana+'","'+idInput+'")');
		btnBuscarCuenta.innerHTML = '<img src="images/buscar20.png" />';
		document.getElementById('DIV_'+idInput).appendChild(btnBuscarCuenta);
	}

	function agregarBtnSincronizarCuenta(input){
		var idInput = input.id
		,	opcion = idInput.split('_')[1];

		var btnSincronizarCuenta = document.createElement('div');
		btnSincronizarCuenta.setAttribute('class','divBtnBuscarPuc');
		btnSincronizarCuenta.setAttribute('id','btn_sincroniza_'+opcion);
		btnSincronizarCuenta.setAttribute('title','Sincronizar cuenta en niif');
		btnSincronizarCuenta.innerHTML = '<img src="images/refresh.png" onclick="sincronizaCuentaEnNiif(\''+opcion+'\',\''+idInput+'\')"/>';
		document.getElementById('DIV_'+idInput).appendChild(btnSincronizarCuenta);
	}


	<?php if($opcion == 'Vagregar'){ echo 'document.getElementById("ActivosFijos_documento_referencia").value = "NC";'; } ?>
	<?php if($opcion == 'Vupdate'){
		echo'if( document.getElementById("ActivosFijos_documento_referencia").value == "FC" ){
				document.getElementById("ActivosFijos_documento_referencia_consecutivo").setAttribute("readonly","readonly");
				document.getElementById("ActivosFijos_costo").setAttribute("readonly","readonly");
			}';
	} ?>

	document.getElementById("ActivosFijos_documento_referencia").setAttribute('readonly','readonly');
	var id_grupo_db = 0;

	function cargarProveedor(){
		var myalto  = Ext.getBody().getHeight();
		var myancho = Ext.getBody().getWidth();
		Win_Ventana_Proveedor_Inventario = new Ext.Window({
			width		: myancho-100,
			height		: myalto-50,
			id			: 'Win_Ventana_Proveedor_Inventario',
			title		: 'Proveedores',
			modal		: true,
			autoScroll	: false,
			closable	: true,
			autoDestroy : true,
			autoLoad	:
			{
				url		: 'proveedor/BusquedaTerceros.php',
				scripts	: true,
				nocache	: true
			}
		}).show();
	}

	function sincronizaCuentaEnNiif(estado,idInput){
		var cuenta = document.getElementById(idInput).value;
		if(isNaN(cuenta) || cuenta < 100000){ alert('Aviso\nSeleccione un numero de cuenta Colgaap valido!'); return; }
		Ext.get('btn_sincroniza_'+estado).load({
			url     : 'bd/bd.php',
			scripts : true,
			nocache : true,
			params  :
			{
				op      : 'sincronizaCuentaNiif',
				estado  : estado,
				cuenta  : cuenta,
				idInput : idInput,
			}
		});
	}

	function ventanaBuscarCuenta(opc,typeVentana,idInput){
		var myalto  = Ext.getBody().getHeight();
		var myancho = Ext.getBody().getWidth();

		title       = (typeVentana == 'niif')? 'Seleccione la cuenta Niif': 'Seleccione la cuenta Colgaap';
		typeVentana = (typeVentana == 'niif')? '_niif': '';

		if (idInput=='ActivosFijos_cuenta_deterioro_niif_credito' || idInput=='ActivosFijos_cuenta_deterioro_niif_debito') {
			opc = 'todas';
			typeVentana = '_niif';
		}

		Win_Ventana_Buscar_cuenta = new Ext.Window({
		    width       : 500,
		    height      : 480,
		    id          : 'Win_Ventana_Buscar_cuenta',
		    title       : title,
		    modal       : true,
		    autoScroll  : false,
		    closable    : false,
		    autoDestroy : true,
		    autoLoad    :
		    {
		        url     : 'buscar_cuenta.php',
		        scripts : true,
		        nocache : true,
		        params  :
		        {
					opc         : opc,
					idInput     : idInput,
					typeVentana : typeVentana,
		        }
		    },
		    tbar        :
		    [
		        {
		            xtype   : 'buttongroup',
		            columns : 3,
		            title   : 'Opciones',
		            items   :
		            [
		                {
		                    xtype       : 'button',
		                    width       : 60,
		                    height      : 56,
		                    text        : 'Regresar',
		                    scale       : 'large',
		                    iconCls     : 'regresar',
		                    iconAlign   : 'left',
		                    handler     : function(){ Win_Ventana_Buscar_cuenta.close(id) }
		                }
		            ]
		        }
		    ]
		}).show();
	}

	/*---------------------------------------------------- funcion del combo terreno --------------------------------------------------------*/
	/* Un terreno no se deprecia, asi que no se requiere todo la informacion del forumulario */



/*------------------------------------------------ Grupo, Subgrupo----------------------------------------------*/
	var ComboGrupo = Ext.get('ActivosFijos_id_grupo');
	ComboGrupo.addListener(
		'change',
		function(event,element,options){
			var id_grupo = document.getElementById('ActivosFijos_id_grupo').value;
			ActualizaSubgrupo(id_grupo);
		},
		this
	);

	function ActualizaSubgrupo(id_grupo,id_subgrupo){
		var MyParent = document.getElementById('ActivosFijos_id_subgrupo').parentNode;
		Ext.get(MyParent).load({
			url		: 'bd/bd.php',
			timeout : 180000,
			scripts	: true,
			nocache	: true,
			params	:
			{
				opcion        : '<?php echo $opcion?>',
				op            : 'OptionSubgrupo',
				id_grupo      : id_grupo,
				id_inventario : '<?php echo $id?>',
			}
		});
	};

</script>


<?php }

if(!isset($opcion)){

	if(!isset($VBarraBotones)){
?>

		<script>

			var toolbar = Ext.getCmp('ToolBar_ActivosFijos').getTopToolbar();

			toolbar.add(

				{
					xtype: 'buttongroup',
					columns: 3,
					title: 'Herramientas',
					items: [
						{
							text		: 'Impresion en Lote</br /> Codigo de Barras',
							scale		: 'small',
							iconCls		: 'barcode16',
							iconAlign	: 'top',
							handler		: function(){nueva_ventana_Grupo_codigo_barras("varios_codigos_barras");}
						}
					]
				},
				{
					xtype: 'buttongroup',
					columns: 3,
					title: 'Actas',
					items: [
						{
							text		: '<br>Acta Activos Fijos',
							scale		: 'small',
							iconCls		: 'genera',
							iconAlign	: 'top',
							handler		: function(){window.open("imprimir_acta_inventario.php?filtro_empresa=<?php echo $id_empresa; ?>"+"&opc=imprimirActaParcial")}
						},
						{
							text		: '<br>Acta Parcial',
							scale		: 'small',
							iconCls		: 'genera',
							iconAlign	: 'top',
							handler		: function(){ver_acta_parcial_inventario();}
						}
					]
				},
				{
					xtype: 'buttongroup',
					columns: 3,
					title: 'Cargar',
					items: [
						{
							text		: '<br>Cargar activos fijos',
							scale		: 'small',
							iconCls		: 'upload_file32',
							iconAlign	: 'top',
							handler		: function(){VentanaSubirExcel();}
						},
					]
				}
			);
			toolbar.doLayout();

		</script>

<?php } ?>

	<script>
		var globalNameFileUpload = '';
		function historico_activo(id) {

			Win_Ventana_historico_activo = new Ext.Window({
			    height      : 500,
			    width       : 500,
			    id          : 'Win_Ventana_historico_activo',
			    title       : 'Historico Activo Fijo',
			    modal       : true,
			    autoScroll  : false,
			    closable    : true,
			    autoDestroy : true,
			    bodyStyle   : 'background-color:<?php echo $_SESSION['COLOR_FONDO'] ?>;',
			    items       :
			    [
			        {
						region		: 'north',
						xtype		: 'panel',
						height		: 33,
						border		: false,
						margins		: '0 0 0 0',
						bodyStyle 	: 'background-image:url(../../temas/clasico/images/fondo_cabecera.png);'
					},
			        {
				        xtype			: 'tabpanel',
						margins			: '0 0 0 0',
						deferredRender	: true,
						border			: false,
						activeTab		: 0,
						bodyStyle 		: 'background-color:<?php echo $_SESSION['COLOR_FONDO'] ?>;',
						items			:
						[
							{
								closable	: false,
								autoScroll	: false,
								title		: 'Historico Colgaap',
								// iconCls 	: 'ventas16',
								// disabled	: <?php echo $permiso_movimientos_contables; ?>,
								bodyStyle 	: 'background-color:<?php echo $_SESSION['COLOR_FONDO'] ?>;',
								items		:
								[
									{
										xtype		: "panel",
										id			: 'contenedor_historico_colgaap',
										bodyStyle 	: 'background-color:<?php echo $_SESSION['COLOR_FONDO'] ?>;',
										border		: false,
										autoLoad:
										{
												url		: 'historico_activo/historico_colgaap.php',
												scripts	: true,
												nocache	: true,
												params	:
												{
													id_activo : id,
												}
										}
									}

								]
							},
							{
								closable	: false,
								autoScroll	: false,
								title		: 'Historico Niif',
								// iconCls 	: 'ventas16',
								// disabled	: <?php echo $permiso_movimientos_contables; ?>,
								bodyStyle 	: 'background-color:<?php echo $_SESSION['COLOR_FONDO'] ?>;',
								items		:
								[
									{
										xtype		: "panel",
										id			: 'contenedor_hisotirico_niif',
										bodyStyle 	: 'background-color:<?php echo $_SESSION['COLOR_FONDO'] ?>;',
										border		: false,
										autoLoad:
										{
												url		: 'historico_activo/historico_niif.php',
												scripts	: true,
												nocache	: true,
												params	:
												{
													id_activo : id,
												}
										}
									}

								]
							},
						]
					}
			    ]

			}).show();
		}

		function ventana_ficha_tecnica(id) {
			var myalto  = Ext.getBody().getHeight();
			var myancho = Ext.getBody().getWidth();

			Win_Ventana_ficha_tecnica = new Ext.Window({
			    width       : 500,
			    height      : 600,
			    id          : 'Win_Ventana_ficha_tecnica',
			    title       : '',
			    modal       : true,
			    autoScroll  : false,
			    closable    : false,
			    autoDestroy : true,
			    autoLoad    :
			    {
			        url     : 'ficha_tecnica/ficha_tecnica.php',
			        scripts : true,
			        nocache : true,
			        params  :
			        {
			            id_activo : id,
			        }
			    },
			    // tbar        :
			    // [
			    //     {
			    //         xtype   : 'buttongroup',
			    //         columns : 3,
			    //         title   : 'Opciones',
			    //         style   : 'border-right:none;',
			    //         items   :
			    //         [
			    //             {
			    //                 xtype       : 'button',
			    //                 width       : 60,
			    //                 height      : 56,
			    //                 text        : 'Regresar',
			    //                 scale       : 'large',
			    //                 iconCls     : 'regresar',
			    //                 iconAlign   : 'top',
			    //                 hidden      : false,
			    //                 handler     : function(){ BloqBtn(this); Win_Ventana_ficha_tecnica.close(id) }
			    //             }
			    //         ]
			    //     }
			    // ]
			}).show();
		}

		function ventana_baja_activo(id,nombre){

			var myalto  = Ext.getBody().getHeight();
			var myancho = Ext.getBody().getWidth();
			Win_Ventana_baja_activo = new Ext.Window
			(
				{
					width		: 400,
					id			: 'Win_Ventana_baja_activo',
					height		: 320,
					title		: 'Dar de baja al activo '+nombre,
					modal		: true,
					autoScroll	: false,
					closable	: false,
					autoDestroy : true,
					autoLoad	:
					{
						url		: 'movimiento_nota/movimiento_nota.php',
						scripts	: true,
						nocache	: true,
						params	:
						{
							id_item      : id

						}
					},
					tbar		:
					[
						{
							xtype		: 'button',
							text		: 'Guardar',
							scale		: 'large',
							iconCls		: 'guardar',
							iconAlign	: 'left',
							handler 	: function(){guardarMovimientoNota()}
						},
						{
							xtype		: 'button',
							text		: 'Regresar',
							scale		: 'large',
							iconCls		: 'regresar',
							iconAlign	: 'left',
							handler 	: function(){Win_Ventana_baja_activo.close()}
						},'-'
					]
				}
			).show();
		}

		// reasaltarFilasGrilla_PanelControl('ActivosFijos','grupo'); 	//FUNCION PARA RESALTAR LAS FILAS DE LA GRILLA

		//Nombre de la grilla
		//campoBd a evaluar en la grilla
		// function reasaltarFilasGrilla_PanelControl(nameGrilla,campoBd){
		// 	var cont         = 0;
		// 	var date         = "<?php echo date('Y-m-d'); ?>";
		// 	var style        = "";
		// 	var id_fila_item = "";

		// 	var arrayFilas=document.getElementById('DIV_listado_'+nameGrilla).childNodes;		//Se atrapa todo los elementos hijos de la grilla

		// 	for(filas in arrayFilas){
		// 		if(arrayFilas[filas].className=="my_grilla_celdas2"){
		// 			id_fila_item=(arrayFilas[filas].id).replace('item_'+nameGrilla+'_',"");

		// 			if(document.getElementById('div_'+nameGrilla+'_'+campoBd+'_'+id_fila_item).innerHTML == ''){
		// 				cont++;
		// 				style=(arrayFilas[filas].getAttribute('style'))+"; font-weight:bold; color:#c72b28;";
		// 				arrayFilas[filas].setAttribute('style',style);
		// 			}
		// 			else{ break; }
		// 		}
		// 	}
		// 	// if(cont > 0){ alert("Aviso!\nExisten "+cont+" Activos por terminar de registrar\nPor favor finalice el registro de los activos marcados con rojo"); }
		// 	return;
		// }

		function ver_acta_parcial_inventario(){
			Win_Ventana_Acta_Parcial_Inventario = new Ext.Window
			(
				{
					width		: 300,
					id			: 'Win_Ventana_Acta_Parcial_Inventario',
					height		: 150,
					title		: 'Filtros Acta Parcial De Inventario',
					modal		: true,
					autoScroll	: false,
					closable	: false,
					autoDestroy : true,
					autoLoad	:
					{
						url		: 'bd/bd.php',
						scripts	: true,
						nocache	: true,
						params	: { op: 'filtro_inventario_parcial' }
					},
					tbar		:
					[
						{
							xtype		: 'button',
							text		: 'Imprimir',
							scale		: 'large',
							iconCls		: 'genera',
							iconAlign	: 'left',
							handler		: function(){ imprimir_acta_parcial_inventario() }
						},
						{
							xtype		: 'button',
							text		: 'Regresar',
							scale		: 'large',
							iconCls		: 'regresar',
							iconAlign	: 'left',
							handler 	: function(){ Win_Ventana_Acta_Parcial_Inventario.close(id) }
						}

					]
				}
			).show();
		}

		function imprimir_acta_parcial_inventario(){
			var cont=0;

			fecha_ini = document.getElementById('fecha_ini').value;
			fecha_fin = document.getElementById('fecha_fin').value;

			if(!fecha_ini){alert("ERROR, Campo Fecha Inicial Obligatorio"); cont++;}
			if(!fecha_fin){alert("ERROR, Campo Fecha Final Obligatorio"); cont++;}
			if(fecha_fin<fecha_ini){alert("ERROR, Campo Fecha Final No Puede Ser Mayor A La Inicial"); cont++;}
			if(cont==0){
				Win_Ventana_Acta_Parcial_Inventario.close(id);
				window.open("imprimir_acta_inventario.php?filtro_empresa=<?php echo $id_empresa; ?>&fecha_ini="+fecha_ini+"&fecha_fin="+fecha_fin+"&opc=imprimirActaParcialBetween");
			}
		}

		function ventana_codigo_barras(id){
			// var titulo  = (document.getElementById('div_ActivosFijos_codigo_'+id).innerHTML)+' '+(document.getElementById('div_ActivosFijos_nombre_equipo_'+id).innerHTML);
			//var myalto  = Ext.getBody().getHeight();
			//var myancho  = Ext.getBody().getWidth();
			Win_Ventana_CodigoBarras = new Ext.Window({
				width		: 400,
				id			: 'Win_Ventana_CodigoBarras',
				height		: 300,
				title		: 'Codigo de Barras Equipo &nbsp;&nbsp;-',
				modal		: true,
				autoScroll	: false,
				closable	: true,
				autoDestroy : true,
				autoLoad	:
				{
					url		: 'CodigoBarras.php',
					scripts	: true,
					nocache	: true,
					params	:
					{
						elid : id
					}
				}
			}).show();
		}

		function nueva_ventana_Grupo_codigo_barras(){
			//var myalto  = Ext.getBody().getHeight();
			//var myancho  = Ext.getBody().getWidth();
			Win_Ventana_Grupo_codigo_barras = new Ext.Window
			(
				{
					width		: 450,
					id			: 'Win_Ventana_Grupo_codigo_barras',
					height		: 250,
					title		: 'Impresion Grupo De Codigos De Barras',
					modal		: true,
					autoScroll	: false,
					closable	: true,
					autoDestroy : true,
					autoLoad	:
					{
						url		: 'impresion_grupo_codigos_barras.php',
						scripts	: true,
						nocache	: true,
						params	: { }
					},
					tbar		:
					[
						{
							xtype		: 'button',
							text		: 'Imprimir',
							scale		: 'large',
							iconCls		: 'genera',
							iconAlign	: 'left',
							handler 	: function(){Buscar_grupo_codigos_barras()}
						},
						{
							xtype		: 'button',
							text		: 'Regresar',
							scale		: 'large',
							iconCls		: 'regresar',
							iconAlign	: 'left',
							handler 	: function(){Win_Ventana_Grupo_codigo_barras.close(id)}
						}
					]
				}
			).show();
		}

		function Buscar_grupo_codigos_barras(){
			var campo;
			opcion_imprimir_barras = document.getElementById('opcion_imprimir_barras').value;
			if(opcion_imprimir_barras==="buscar_rango_barras"){
				campo="code_bar";
				limite_inferior = document.getElementById('desde').value;
				limite_superior = document.getElementById('hasta').value;
			}
			else{
				campo="fecha_creacion_en_inventario";
				limite_inferior = document.getElementById('desde_fecha').value;
				limite_superior = document.getElementById('hasta_fecha').value;
			}

			if (limite_inferior==""||limite_superior==""){
			alert("ERROR, Los 2 Campor Son Obligatorios Para La Impresion");
			}

			else{
				Win_Ventana_CodigoBarrasRango = new Ext.Window
				(
					{
						width		: 400,
						id			: 'Win_Ventana_CodigoBarrasRango',
						height		: 300,
						title		: 'Rango de Codigo de Barras',
						modal		: true,
						autoScroll	: false,
						closable	: true,
						autoDestroy : true,
						autoLoad	:
						{
							url		: 'CodigoBarrasRango.php',
							scripts	: true,
							nocache	: true,
							params	:
							{
								campo           : campo,
								limite_inferior : limite_inferior,
								limite_superior : limite_superior,
								elid            : id
							}
						}
					}
				).show();
			}
		}


		function ventana_eliminar_campo_inventario(){
			Win_Ventana_EliminarCampoInventario = new Ext.Window
				(
					{
						width		: 400,
						id			: 'Win_Ventana_EliminarCampoInventario',
						height		: 300,
						title		: 'Observaciones Eliminacion Campo Inventario',
						modal		: true,
						autoScroll	: false,
						closable	: false,
						autoDestroy : true,
						autoLoad	:
						{
							url		: 'bd/bd.php',
							scripts	: true,
							nocache	: true,
							params	:
							{
								op : "ventana_eliminar_campo_inventario",
								id : "<?php echo $id;?>"
							}
						},
						tbar		:
						[
							{
								xtype		: 'button',
								text		: 'Eliminar Campo',
								scale		: 'large',
								iconCls		: 'eliminar',
								iconAlign	: 'left',
								handler 	: function(){eliminar_campo_inventario()}
							},
							{
								xtype		: 'button',
								text		: 'Regresar',
								scale		: 'large',
								iconCls		: 'regresar',
								iconAlign	: 'left',
								handler 	: function(){Win_Ventana_EliminarCampoInventario.close(id)}
							}
						]
					}
				).show();
		}

		function eliminar_campo_inventario(){
			observaciones_eliminar_inventario = document.getElementById('observaciones_eliminar_inventario').value;
			if(observaciones_eliminar_inventario==""){	alert("ERROR; Campo Observaciones Obligatorio");}
			else {
				Ext.get("div_guadar_eliminacion").load({
					url		: "bd/bd.php",
					scripts	: true,
					nocache	: true,
					params	:
					{
						id            : '<?php echo $id;?>',
						op            : "eliminar_campo_inventario",
						observaciones : observaciones_eliminar_inventario
					}
				});
			}
		}
	//////////////////////////ventana inventarios documentos////////////////////////////////////
		function inventario_documentos(id){

			var myalto  = Ext.getBody().getHeight();
			var myancho = Ext.getBody().getWidth();
			var title= document.getElementById('div_ActivosFijos_codigo_'+id).innerHTML;
			title += ' '+document.getElementById('div_ActivosFijos_nombre_equipo_'+id).innerHTML;

			Win_Ventana_inventario_documentos = new Ext.Window({
				width		: myancho-350,
				id			: 'Win_Ventana_inventario_documentos',
				height		: myalto-50,
				title		: 'Documentos -Inventario '+title,
				modal		: true,
				autoScroll	: false,
				closable	: false,
				autoDestroy : true,
				autoLoad	:
				{
					url		: 'inventario_documentos.php',
					scripts	: true,
					nocache	: true,
					params	: { elid : id }
				},
				tbar		:
				[
					{
						xtype	: 'buttongroup',
						columns	: 2,
						title	: 'Opciones',
						items	:
						[
							{
								xtype		: 'button',
								text		: 'Agregar Documento',
								scale		: 'large',
								iconCls		: 'add',
								iconAlign	: 'left',
								handler 	: function(){agregar_documento(id)}
							},
							{
								xtype		: 'button',
								text		: 'Regresar',
								scale		: 'large',
								iconCls		: 'regresar',
								iconAlign	: 'left',
								handler 	: function(){Win_Ventana_inventario_documentos.close(id)}
							}
					 	]
					}
				]
			}).show();
		}

	//////////////////////////ventana seleccion documento(Select)////////////////////////////////////
		function agregar_documento(id){
			Win_select_inventario_documentos = new Ext.Window({
				width		: 310,
				id			: 'Win_select_inventario_documentos',
				height		: 140,
				title		: 'Seleccione Inventario',
				modal		: true,
				autoScroll	: false,
				closable	: true,
				autoDestroy : true,
				autoLoad	:
				{
					url		: 'select_inventario_documentos.php',
					scripts	: true,
					nocache	: true,
					params	: { id : id }
				},
			}).show();
		}

		function Editar_ActivosFijos(id){ VentanaAgregarActivosFijos(id); }
		function Agregar_ActivosFijos(){ VentanaAgregarActivosFijos('false'); }

		function VentanaAgregarActivosFijos(cual){
			var myalto  = Ext.getBody().getHeight();
			var myancho = Ext.getBody().getWidth();
			if(cual == 'false'){
				Win_Agregar_ActivosFijos = new Ext.Window({
					width		: 600,
					id			: 'Win_Ventana_ActivosFijos',
					height		: myalto - 80,
					title		: 'Activo Fijo',
					modal		: true,
					autoScroll	: false,
					resizable 	: false,
					closable	: true,
					autoDestroy : true,
					autoLoad	:
					{
						url		: 'contenedor_inventario.php',
						scripts	: true,
						nocache	: true,
						params	:
						{
							cual             : cual,
							opcion           : 'Vagregar',
							filtro_empresa   : '<?php echo $filtro_empresa ?>',
							filtro_sucursal  : '<?php echo $filtro_sucursal ?>',
							filtro_ubicacion : '<?php echo $filtro_ubicacion ?>'
						}
					}
				}).show();
			}
			else{

				Win_Editar_ActivosFijos = new Ext.Window({
					width		: 600,
					id			: 'Win_Ventana_ActivosFijos',
					height		: myalto - 80,
					title		: 'Activo Fijo',
					modal		: true,
					autoScroll	: false,
					resizable 	: false,
					closable	: true,
					autoDestroy : true,
					autoLoad	:
					{
						url		: 'contenedor_inventario.php',
						scripts	: true,
						nocache	: true,
						params	:
						{
							cual             : cual,
							opcion           : 'Vupdate',
							filtro_empresa   : '<?php echo $filtro_empresa ?>',
							filtro_sucursal  : '<?php echo $filtro_sucursal ?>',
							filtro_ubicacion : '<?php echo $filtro_ubicacion ?>'
						}
					}
				}).show();
			}
		}


		function insertarEnBodegas(id){ alert("guardado "+id); }

		function VentanaSubirExcel(){
			var myalto  = Ext.getBody().getHeight();
			var myancho = Ext.getBody().getWidth();

			Win_Ventana_subir_excel_terceros = new Ext.Window({
			    width       : 800,
			    height      : 530,
			    id          : 'Win_Ventana_subir_excel_terceros',
			    title       : 'Subir Activos',
			    modal       : true,
			    autoScroll  : false,
			    closable    : false,
			    autoDestroy : true,
			    autoLoad    :
			    {
			        url     : '../activo_fijo/subir_activos.php',
			        scripts : true,
			        nocache : true,
			        params  : { }
			    },
			    tbar        :
			    [
			        {
			            xtype   : 'buttongroup',
			            columns : 3,
			            title   : 'Opciones',
			            style   : 'border-right:none;',
			            items   :
			            [
			                {
			                    xtype       : 'button',
			                    width       : 60,
			                    height      : 56,
			                    text        : 'Upload Excel',
			                    scale       : 'large',
			                    iconCls     : 'upload_file32',
			                    iconAlign   : 'top',
			                    hidden      : false,
			                    handler     : function(){ BloqBtn(this); ventana_upload_excel() }
			                },
			                {
			                    xtype       : 'button',
			                    width       : 60,
			                    height      : 56,
			                    text        : 'Descargar Formato',
			                    scale       : 'large',
			                    iconCls     : 'xls32',
			                    iconAlign   : 'top',
			                    hidden      : false,
			                    handler     : function(){ BloqBtn(this); window.open('bd/formato_carga_activos.xls'); }
			                },
			                // {
			                // 	xtype     : "splitbutton",
			                // 	id        : 'Btn_formato_upload_terceros',
			                // 	tooltip   : 'Descargar Formato',
			                // 	iconCls   : "xls32",
			                // 	scale     : "large",
			                // 	iconAlign : 'top',
			                // 	text      : 'Imprimir',
			                // 	handler   : function(){ BloqBtn(this); dowload_formatos_terceros("formato_tercero_upload.xls"); },
			                //     menu:
			                //     [
			                // 		{
			                // 			text    : "Formato Activos",
			                // 			iconCls : "xls16",
			                // 			handler : function(){ BloqBtn(this); dowload_formatos_terceros("formato_tercero_upload.xls"); }
			                // 		},
			                // 		{
			                // 			text    : "Codigo ubicacion Colombia",
			                // 			iconCls : "xls16",
			                // 			handler : function(){ BloqBtn(this); dowload_formatos_terceros("ubicacion_ciudad_colombia.xls"); }
			                // 		},
			                // 		{
			                // 			text    : "Codigo ubicacion global",
			                // 			iconCls : "xls16",
			                // 			handler : function(){ BloqBtn(this); dowload_formatos_terceros("ubicacion_ciudad_global.xls"); }
			                // 		},
			                // 		{
			                // 			text    : "Otros Codigos",
			                // 			iconCls : "xls16",
			                // 			handler : function(){ BloqBtn(this); dowload_formatos_terceros("codigos_documentos.xls"); }
			                // 		}
			                //   	]
			                // },
			                {
			                    xtype       : 'button',
			                    width       : 60,
			                    height      : 56,
			                    text        : 'Regresar',
			                    scale       : 'large',
			                    iconCls     : 'regresar',
			                    iconAlign   : 'top',
			                    hidden      : false,
			                    handler     : function(){ BloqBtn(this); Win_Ventana_subir_excel_terceros.close(id) }
			                }
			            ]
			        }
			    ]
			}).show();
		}

	</script>
<?php
} ?>



