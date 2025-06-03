<?php
	include("../../../configuracion/conectar.php");
	include("../../../configuracion/define_variables.php");
	include("../../../misc/MyGrilla/class.MyGrilla.php");

	$fecha_creacion = date("Y-m-d");
	$id_pais        = $_SESSION['PAIS'];
	$id_empresa     = $_SESSION['EMPRESA'];


	// VALIDAR SI TIENE MOVIMIENTOS CONTABLES delete
	if ($opcion=='EliminaBD') {
		$sql="SELECT COUNT(id) AS cont FROM asientos_colgaap WHERE activo=1 AND id_empresa=$id_empresa AND id_tercero=$id ";
		$query=$mysql->query($sql,$mysql->link);
		$cont = $mysql->result($query,0,'cont');
		if ($cont>0) {
			echo 'return;';
			exit;
		}

		$sql="SELECT COUNT(id) AS cont FROM asientos_niif WHERE activo=1 AND id_empresa=$id_empresa AND id_tercero=$id ";
		$query=$mysql->query($sql,$mysql->link);
		$cont = $mysql->result($query,0,'cont');
		if ($cont>0) {
			echo 'return;';
			exit;
		}
	}
	/**//////////////////////////////////////////////**/
	/**///		 INICIALIZACION DE LA CLASE  	  ///**/
	/**/											/**/
	/**/	$grilla = new MyGrilla();				/**/
	/**/											/**/
	/**//////////////////////////////////////////////**/



	//CONFIGURACION//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

		//NOMBRE DE LA GRILLA
			$grilla->GrillaName	 		= 'Terceros';  	//NOMBRE DE LA GRILLA (DEBE SER UNICO POR CADA GRILLA DE LA APLICACION)
		//QUERY
			$grilla->ConsulCustom		= 'SELECT *,(SELECT asignado FROM terceros_asignados WHERE id_tercero = terceros.id AND id_empresa = '.$_SESSION["EMPRESA"].') AS FuncionarioAsignado, (SELECT id_asignado FROM terceros_asignados WHERE id_tercero = terceros.id AND id_empresa = '.$_SESSION["EMPRESA"].') AS IdFuncionarioAsignado FROM';
			$grilla->TableName			= 'terceros';		//NOMBRE DE LA TABLA DE CONSULTA EN LA BASE DE DATOS DE
			// $grilla->MyWhere			= "activo = 1 AND id_empresa='$id_empresa' AND tercero_empleado='false' ";	//WHERE DE LA CONSULTA A LA TABLA "$TableName"
			$grilla->MyWhere			= "activo = 1 AND id_empresa='$id_empresa' AND tercero = 1";	//WHERE DE LA CONSULTA A LA TABLA "$TableName"
			$grilla->OrderBy			= 'codigo ASC';			//LIMITE DE LA CONSULTA
			$grilla->MySqlLimit			= '0,100';			//LIMITE DE LA CONSULTA
			if(isset($id_asignado) AND $id_asignado > 0){
				$grilla->MysqlHaving = "IdFuncionarioAsignado = '$id_asignado'";//BUSCA POR UN CAMPO QUE PERTENECE A OTRA TABLA
			}

		//TAMANO DE LA GRILLA
			$grilla->AutoResize	 		= 'true';			//SI LA GRILLA ES AUTORESIZABLE (LIQUIDA) -> "true" SI NO -> "false"
			// $grilla->Ancho		 		= 800;				//ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'
			// $grilla->Alto		 		= 220;				//ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'
			$grilla->QuitarAncho		= 30;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'true'
			$grilla->QuitarAlto			= 145;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'true'

		//TOOLBAR Y CAMPO DE BUSQUEDA Y FILTROS
			$grilla->Gtoolbar			= 'true';			//SI LA GRILLA LLEVA EL TOOLBAR DE BUSQUEDA
			$grilla->CamposBusqueda		= 'codigo,numero_identificacion,nombre,nombre_comercial,direccion,fecha_creacion,pais,departamento,ciudad,telefono1,telefono2,celular1,celular2,email';			//VARIABLE QUE DEFINE LOS CAMPOS DE LA BD DONDE SE BUSCARA
			$grilla->Gfilters			= 'true';
			$grilla->GfiltersAutoOpen	= 'true';
			$grilla->AddFilter('Tipo Tercero','tipo_cliente','tipo_cliente');
			$grilla->AddFilter('Pais','id_pais','pais');
			$grilla->AddFilter('Ciudad','id_ciudad','ciudad');
			$grilla->AddFilter('Tipo de Tercero','tipo','tipo');
			$grilla->AddFilter('Tipo de Documento','id_tipo_identificacion','tipo_identificacion');
			$grilla->AddFilter('Ejecutivo Comercial','IdFuncionarioAsignado','FuncionarioAsignado');
			// $grilla->AddFilter('TipoTercero','tipo_proveedor','tipo_proveedor');

		//CONFIGURACION DE CAMPOS EN LA GRILLA
      		$grilla->AddRow('Codigo','codigo',70);
			$grilla->AddRowImage('','<img src="../terceros/images/[tipo].png" style="cursor:pointer" width="16" height="16">',16);
      		$grilla->AddRow('','tipo_identificacion',70);
			$grilla->AddRow(utf8_decode('N° Identificacion'),'numero_identificacion',130);
			$grilla->AddRow('Nombre Comercial','nombre_comercial',200);
			$grilla->AddRow('Nombre o Razon Social','nombre',200);
			$grilla->AddRow('Telefono 1','telefono1',100);
			$grilla->AddRow('Telefono 2','telefono2',100);
			$grilla->AddRow('Celular 1','celular1',100);
			$grilla->AddRow('Celular 2','celular2',100);
			$grilla->AddRow('Correo Electronico','email',180);
			$grilla->AddRow('Direccion','direccion',180);
			$grilla->AddRow('Funcionario Asignado','FuncionarioAsignado',180);

			$grilla->AddRowImage('Contactos','<center><div style="float:left; margin: 0 0 0 7px"><img src="../../temas/clasico/images/BotonesTabs/contactos16.png?" style="cursor:pointer" width="16" height="16" onclick="TituloSubVentanaTerceros([id],\'contactos\');"></div><div style="float:left">&nbsp;([contactos])</div></center>',60);
			$grilla->AddRowImage('Pais','<img src="../../temas/clasico/images/Banderas/[iso2].png" width="16" height="12">&nbsp;&nbsp;[pais]',130);
			$grilla->AddRow('Estado/Departamento','departamento',150);
			$grilla->AddRow('Ciudad','ciudad',130);
			$grilla->AddRow('Fecha Creacion','fecha_creacion',100);
			$grilla->AddRowImage('Ficha Proveedor','<center><img src="../terceros/terceros/images/[ficha_tecnica].png" style="cursor:pointer" width="16" height="15"></center>',100);

		//CONFIGURACION FORMULARIO
			$grilla->FContenedorAncho		= 760;
			$grilla->FColumnaGeneralAncho	= 380;
			$grilla->FColumnaGeneralAlto	= 25;
			$grilla->FColumnaLabelAncho		= 150;
			$grilla->FColumnaFieldAncho		= 230;

		//CONFIGURACION DE LA VENTANA DE INSERT, UPDATE Y DELETE
			$grilla->VentanaAuto		= 'false';			//SI LA VENTANA DE INSERT, UPDATE Y DELETE ES AUTOMATICA O MANUAL
			$grilla->TituloVentana		= 'Clientes'; 		//NOMBRE DE LA VENTANA DE INSER, UPDATE Y DELETE
			$grilla->CerrarDespuesDeAgregar = 'false';
			$grilla->VBarraBotones		= 'true';			//SI HAY O NO BARRA DE BOTONES
			$grilla->VBotonNuevo		= 'false';			//SI LLEVA EL BOTON DE AGREGAR REGISTRO
			// $grilla->VBotonNText		= 'Agregar Cliente'; 	//TEXTO DEL BOTON DE NUEVO REGISTRO
			// $grilla->VBotonNImage	= 'addcliente';		//IMAGEN CSS DEL BOTON
			// $grilla->VAutoResize		= 'true';			//SI LA VENTANA ES AUTORESIZABLE (LIQUIDA) -> "true" SI NO -> "false"
			// $grilla->VAncho		 	= 800;				//ANCHO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'false'
			// $grilla->VAlto		 	= 570;				//ALTO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'false'
			// $grilla->VQuitarAncho		= 160;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ANCHO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'true'
			// $grilla->VQuitarAlto		= 160;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ALTO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'true'
			// $grilla->VAutoScroll		= 'true';			//SI LA VENTANA TIENE O NO AUTOSCROLL
			// $grilla->VBotonEliminar	= 'true';			//SI MUESTRA BOTON DE ELIMINAR EN LA VENTANA DE EDICION
			// $grilla->VComporEliminar	= 'true';			//COMPORTAMIENTO DEL BOTON DE ELIMINAR ("true" ES CAMPO ACTIVO DE 1 A 0) ("false" -> ELIMINA EL REGISTRO DE LA BASE DE DATOS)
			// $grilla->AddBottonVentana('Contactos','admincontactos','TituloSubVentanaTerceros(elid,"contactos");');
			// $grilla->AddBottonVentana('Direcciones','direcciones','TituloSubVentanaTerceros(elid,"direcciones");');
			$grilla->AddBotton('Agregar Tercero','addcliente','VentanaAgregarCliente("false")');
			$grilla->AddBotton('Importar Terceros','upload_file32','VentanaSubirExcelCliente()');
			$grilla->AddBotton('Exportar Terceros','xls32','exportarTerceros()');



		//CONFIGURACION DEL MENU CONTEXTUAL
 			$grilla->MenuContext		= 'true';
	 		$grilla->MenuContextEliminar= 'true';
		//OPCIONES ADICIONALES EN EL MENU CONTEXTUAL
 			$grilla->AddMenuContext('Enviar Email','enviaremail16','VentanaEnviarEmail([id])');

		//CAMPOS OCULTOS
 			$grilla->AddTextField('','id_empresa',240,'true','hidden', $id_empresa);

 		//TIPO DE TERCERO
 			$grilla->AddSeparator('Tipo de Tercero');
 			$grilla->AddTextField('tercero','tercero',200,'true','true','1');
			$grilla->AddComboBox ('Cliente','tipo_cliente',200,'true','false','Si:Si,No:No');
			$grilla->AddComboBox ('Proveedor','tipo_proveedor',200,'true','false','Si:Si,No:No');

 		//CONFIGURACION DE LOS FORMULARIOS DE CAPTURA Y EDICION
 			$grilla->AddTextField('','fecha_creacion',200,'false','hidden', $fecha_creacion);

			$grilla->AddSeparator('Datos Tercero');
			$grilla->AddComboBox ('Tipo Identificacion','id_tipo_identificacion',200,'true','true','tipo_documento,id,nombre,true','activo = 1 AND id_empresa="'.$id_empresa.'"');
			$grilla->AddTextField('Identificacion','numero_identificacion',150,'true','false','false','true');

			$grilla->AddValidation('numero_identificacion','unico_global','id_empresa="'.$id_empresa.'"');
			$grilla->AddValidation("numero_identificacion", "trim");
			$grilla->AddValidation("numero_identificacion", "blankspaces");

			// $grilla->AddValidation('numero_identificacion','numero');

			$grilla->AddTextField('Ciudad de Identificacion','ciudad_identificacion',200,'false','false');
			$grilla->AddTextField('Nombre o Razon Social','nombre',200,'true','false');
			$grilla->AddTextField('Nombre Comercial','nombre_comercial',200,'true','false');
            $grilla->AddValidation('nombre','mayuscula');
            $grilla->AddValidation('nombre_comercial','mayuscula');

		///DIRECCION PRINCIPAL
			$grilla->AddSeparator('Direccion Principal');
			$grilla->AddTextField('Direccion','direccion',200,'true','false');
			$grilla->AddComboBox ('Pais','id_pais',200,'true','true','ubicacion_pais,id,pais,true','activo = 1 ORDER BY pais ASC');
			$grilla->AddComboBox ('Departamento','id_departamento',200,'true','true','ubicacion_departamento,id,departamento,true','activo = 1 AND id_pais=49 ORDER BY departamento ASC');
			$grilla->AddComboBox ('Ciudad','id_ciudad',200,'true','true','ubicacion_ciudad,id,ciudad,true','activo = 2 ORDER BY ciudad ASC');
			$grilla->AddTextField('Telefono 1','telefono1',200,'true','false');
			$grilla->AddTextField('Telefono 2','telefono2',200,'false','false');
			$grilla->AddTextField('Celular 1','celular1',200,'false','false');
			$grilla->AddTextField('Celular 2','celular2',200,'false','false');
			$grilla->AddComboBox ('Sector Empresarial','id_sector_empresarial',200,'false','true','configuracion_sector_empresarial,id,nombre,true','activo = 1 AND id_empresa="'.$id_empresa.'"');
			$grilla->AddTextField('Pagina Web','pagina_web',200,'false','false');
			$grilla->AddTextField('Recomendado','configuracion_origen',200,'false','false');
			$grilla->AddTextField('','id_configuracion_origen',200,'false','hidden');
			$grilla->AddTextField('E-mail','email',200,'false','false');
			// $grilla->AddValidation('email','email');


		///DATOS TRIBUTARIOS
			$grilla->AddSeparator('Informacion Tributaria');
			$grilla->AddTextField('Primer Nombre','nombre1',200,'true','false');
			$grilla->AddTextField('Segundo Nombre','nombre2',200,'false','false');
			$grilla->AddTextField('Primer Apellido','apellido1',200,'true','false');
			$grilla->AddTextField('Segundo Apellido','apellido2',200,'false','false');

            $grilla->AddValidation('nombre1','mayuscula');
            $grilla->AddValidation('nombre2','mayuscula');
            $grilla->AddValidation('apellido1','mayuscula');
            $grilla->AddValidation('apellido2','mayuscula');

			$grilla->AddComboBox ('Tipo Regimen','id_tercero_tributario',200,'true','true','terceros_tributario,id,nombre,true',"activo = 1 AND id_pais='$id_pais'");
			$grilla->AddComboBox ('Tipo Persona','id_tipo_persona_dian',200,'true','false','1:PERSONA JURIDICA,2:PERSONA NATURAL');
			$grilla->AddComboBox ('Exento de iva (venta)','exento_iva',200,'true','false','Si:Si,No:No');
			$grilla->AddComboBox ('Forma de pago(por defecto)','id_forma_pago',200,'false','true','configuracion_formas_pago,id,nombre,true',"activo = 1 AND id_empresa='$id_empresa'");
			$grilla->AddComboBox ('Metodo de pago(por defecto)','id_metodo_pago',200,'false','true','configuracion_metodos_pago,id,nombre,true',"activo = 1 AND id_empresa='$id_empresa'");
			// $grilla->AddComboBox ('Forma de Cobro(por defecto)','id_forma_cobro',200,'false','true','configuracion_formas_pago,id,nombre,true',"activo = 1 AND id_empresa='$id_empresa'");
			//$grilla->AddComboBox ('Gravable','gravable',200,'true','false','true:Si, false:No');
			//$grilla->AddComboBox ('Reter Iva','retener_iva',200,'true','false','true:Si, false:No');
			//$grilla->AddComboBox ('Retener Ica','retener_ica',200,'true','false','true:Si, false:No');
			//$grilla->AddComboBox ('Agente Retenedor','agente_retenedor',200,'true','false','true:Si, false:No');

		///REPRESENTANTE LEGAL
			$grilla->AddSeparator('Datos del Representante Legal');
			$grilla->AddTextField('Nombre Completo','representante_legal',200,'false','false');
			$grilla->AddTextField('Ciudad Domicilio','ciudad_representante',200,'false','false');
			$grilla->AddComboBox ('Tipo Identificacion','id_tipo_identificacion_representante',200,'false','true','tipo_documento,id,nombre,true','activo = 1 AND id_empresa="'.$id_empresa.'"');
			$grilla->AddTextField('Identificacion','identificacion_representante',200,'false','false');
			$grilla->AddTextField('Ciudad Identificacion','ciudad_id_representante',200,'false','false');
		///ESCRITURA
			/*$grilla->AddSeparator('Datos de Constitucion');
			$grilla->AddTextField('Numero de Escritura','numero_escritura',200,'false','false');
			$grilla->AddTextField('Fecha de Escritura','fecha_escritura',200,'false','false');
			$grilla->AddValidation('fecha_escritura','fecha');
			$grilla->AddTextField('Notaria','notaria_escritura',200,'false','false');
			$grilla->AddTextField('Ciudad de Notaria','ciudad_notaria',200,'false','false');*/
		///CAMARA COMERCIO
			/*$grilla->AddSeparator('Datos de Camara y Comercio');
			$grilla->AddTextField('Nombre Establecimiento Cial.','nombre_establecimiento',200,'false','false');
			$grilla->AddTextField('Numero Matricula','numero_matricula_camara',200,'false','false');
			$grilla->AddTextField('Fecha de Matricula','fecha_matricula_camara',200,'false','false');
			$grilla->AddValidation('fecha_matricula_camara','fecha');
			$grilla->AddTextField('Libro Numero','libro_camara',200,'false','false');
			$grilla->AddTextField('Ciudad','ciudad_camara',200,'false','false');*/


	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


	/**//////////////////////////////////////////////////////////////**/
	/**///				INICIALIZACION DE LA GRILLA	  			  ///**/
	/**/															/**/
	/**/	$grilla->Link = $link;  	//Conexion a la BD			/**/
	/**/	$grilla->inicializa($_POST);//variables POST			/**/
	/**/	$grilla->GeneraGrilla(); 	// Inicializa la Grilla		/**/
	/**/															/**/
	/**//////////////////////////////////////////////////////////////**/
if($opcion == 'Vupdate' || $opcion == 'Vagregar'){ ?>

	<script>
		// BLOQUEAR EL NIT PARA QUE NO SE PUEDA MODIFICAR
		if ("<?php echo $opcion; ?>" == "Vupdate" && "<?php echo $Convierte_Prospecto; ?>" != "true") {
			document.getElementById('Terceros_numero_identificacion').readOnly=true;
		}

		// si el registro no corresponde a un proveedor entonces el boton de retenciones se deshabilita.
		var es_proveedor = document.getElementById('Terceros_tipo_proveedor').value;
		if (es_proveedor=='No') {Ext.getCmp('ElBtnRetenciones').enable();}
		else{Ext.getCmp('ElBtnRetenciones').enable();}

		//le agregamos un evento al select de tipo de proveedor para que cuando sea si se habilite el boton de retenciones
		document.getElementById('Terceros_tipo_proveedor').setAttribute("onchange","verificaBoton()");

		<?php if(isset($Convierte_Prospecto)){ ?>
			Elimina_Div_Prospectos(<?php echo $id ?>);
		<?php } ?>

		//========================// SI ES EMPRESA O PERSONA //========================//
		//*****************************************************************************//
		<?php
			$sqlTipoTercero   = "SELECT id,tipo FROM tipo_documento WHERE id_empresa='$id_empresa' AND activo=1";
			$queryTipoTercero = mysql_query($sqlTipoTercero,$link);

			echo 'var arrayTypeTercero = [];';
			while ($row = mysql_fetch_assoc($queryTipoTercero)) { echo 'arrayTypeTercero['.$row['id'].'] = "'.$row['tipo'].'";'; }
		?>

		function tipoTercero(){
			var idTypeTercero = document.getElementById('Terceros_id_tipo_identificacion').value;
			return arrayTypeTercero[idTypeTercero];
		}

		//====================// OCULTA INPUT TERCERO TRIBUTARIO //====================//
		//*****************************************************************************//

		var arrayTercero = []
		,	arrayCampos  = ["nombre1","nombre2","apellido1","apellido2"]
		, 	contArray    = arrayCampos.length;

		for (var i = 0; i < arrayCampos.length; i++) {
			arrayTercero[arrayCampos[i]] = document.getElementById('Terceros_'+arrayCampos[i]).value;
		};

		hidenTerceroTributario();
		function hidenTerceroTributario(){
			var	typeTercero = tipoTercero()
			,	display     = (typeTercero == 'Persona')? 'block': 'none';

			for (var i = 0; i < contArray; i++) {
				document.getElementById('Terceros_'+arrayCampos[i]).value = (typeTercero == 'Persona')? arrayTercero[arrayCampos[i]]: ' ';
				document.getElementById('EmpConte_Terceros_'+arrayCampos[i]).style.display = display;
			};
		}

		//================// LISTENER CAMPO CIUDAD DE IDENTIFICACION //================//
		/*******************************************************************************/
		var ComboTipoIdentificacion = Ext.get('Terceros_id_tipo_identificacion');
		ComboTipoIdentificacion.addListener(
			'change',
			function(event,element,options){
				hidenTerceroTributario();
				InputCiudadIdentificacion();
			},
			this
		);

		InputCiudadIdentificacion();
		function InputCiudadIdentificacion(){
			var typeTercero = tipoTercero();

			if(typeTercero != "Persona"){
				document.getElementById('Terceros_ciudad_identificacion').value= "";
				document.getElementById('EmpConte_Terceros_ciudad_identificacion').style.display = 'none';
			}
			else { document.getElementById('EmpConte_Terceros_ciudad_identificacion').style.display = 'block'; }
		}

		/*------------------------------------ LISTENER PROYECTOS Y ACTIVIDADES --------------------------------------*/
		/**************************************************************************************************************/
		// var ComboProyecto = Ext.get('Terceros_id_proyecto');

		// ComboProyecto.addListener(
		// 	'change',
		// 	function(event,element,options){ ActualizaComboActividades(element.value); },
		// 	this
		// );

		// function ActualizaComboActividades(id_proyecto){
		// 	var MyParentActividad = document.getElementById('Terceros_id_actividad').parentNode;
		// 	Ext.get(MyParentActividad).load({
		// 		url		: 'bd/bd.php',
		// 		timeout : 180000,
		// 		scripts	: true,
		// 		nocache	: true,
		// 		params	:
		// 		{
		// 			op			: 'OptionSelectActividad',
		// 			id_cliente	: '<?php echo $id?>',
		// 			id_proyecto	: id_proyecto
		// 		}
		// 	});
		// }

		// <?php if($opcion=='Vupdate'){ echo "ActualizaComboActividades(document.getElementById('Terceros_id_proyecto').value);"; } ?>

		/*-------------------------------------- PAIS, DEPARTAMENTO Y CUIDAD -----------------------------------------*/
		/**************************************************************************************************************/

		// EVENTO SOBRE EL COMBO DEL PAIS

		var ComboPais = Ext.get('Terceros_id_pais');

		ComboPais.addListener(
			'change',
			function(event,element,options){
				id_pais = document.getElementById('Terceros_id_pais').value;
				ActualizaDepartamento(id_pais);
			},
			this
		);

		var Combodepartamento = Ext.get('Terceros_id_departamento');

		Combodepartamento.addListener(
			'change',
			function(event,element,options){
				id_departamento = document.getElementById('Terceros_id_departamento').value;
				ActualizaCiudadTerceros(id_departamento);
			},
			this
		);


		function ActualizaDepartamento(id_pais){
			var MyParentDepartamento = document.getElementById('Terceros_id_departamento').parentNode;
			Ext.get(MyParentDepartamento).load({
				url		: '../terceros/bd/bd.php',
				timeout : 180000,
				scripts	: true,
				nocache	: true,
				params	:
				{
					op         : 'OptionSelectDepartamento',
					id_cliente : '<?php echo $id?>',
					id_pais    : id_pais
				}
			});
		};

		<?php
			if($opcion=='Vupdate'){
				echo "	id_pais= document.getElementById('Terceros_id_pais').value;
						ActualizaDepartamento(id_pais);

						var CampoRecomendado=document.getElementById('Terceros_configuracion_origen');
						CampoRecomendado.disabled = true;";
			}
			else if($opcion == 'Vagregar'){
				echo "	document.getElementById('Terceros_id_pais').value='$id_pais';
						ActualizaDepartamento('$id_pais');

						var InputRecomendado = Ext.get('Terceros_configuracion_origen');
						InputRecomendado.addListener('focus',function(event,element,options){
							ValorInputRecomendado();
						},this);

						function ValorInputRecomendado(){
							VentanaGlobalClientes('Win_Ventana_Terceros_Global', 'Recomendado','Terceros_id_configuracion_origen:id, Terceros_configuracion_origen:div_BusquedaTerceros_nombre_','','');
						}";
			}
		?>
		/*------------------------------------------------ Departamento, Ciudad----------------------------------------------*/
		function ActualizaCiudadTerceros(id_departamento){
			var MyParentCiudadTerceros = document.getElementById('Terceros_id_ciudad').parentNode;
			id_departamento = document.getElementById('Terceros_id_departamento').value;

			Ext.get(MyParentCiudadTerceros).load({
				url		: '../terceros/bd/bd.php',
				timeout : 1800000,
				scripts	: true,
				nocache	: true,
				params	:
				{
					op              : 'OptionSelectCiudad',
					id_cliente      : '<?php echo $id?>',
					id_departamento : id_departamento
				}
			});
		};

		//funcion para habilitar o deshabilitar el boton de reteciones
		function verificaBoton(){
			var es_proveedor = document.getElementById('Terceros_tipo_proveedor').value;
			if (es_proveedor=='No') { Ext.getCmp('ElBtnRetenciones').enable(); }
			else{ Ext.getCmp('ElBtnRetenciones').enable(); }
		}

		/**********************************  ventana de retenciones del proveedor **********************************/

		function retenciones_tercero(id){
			Win_Ventana_retenciones_tercero = new Ext.Window({
				width		: 700,
				id			: 'Win_Ventana_retenciones_tercero',
				height		: 400,
				title		: 'Agregar Retenciones',
				modal		: true,
				autoScroll	: true,
				closable	: false,
				autoDestroy : true,
				autoLoad	:
				{
					url		: '../terceros/retenciones_tercero.php',
					scripts	: true,
					nocache	: true,
					params 	: { id_tercero : id }

				},
				tbar		:
				[
					{
						xtype		: 'button',
						text		: 'Regresar',
						scale		: 'large',
						iconCls		: 'regresar',
						iconAlign	: 'top',
						handler 	: function(){ Win_Ventana_retenciones_tercero.close(id); }
					},'-',
					{
						xtype		: 'button',
						text		: 'Nueva Retencion',
						scale		: 'large',
						iconCls		: 'add',
						iconAlign	: 'top',
						handler 	: function(){ agregarRetencion(id); }
					}

				]
			}).show();
		}

	</script>
<?php }

else if(!isset($opcion)) { ?>

	<script>

		if(document.getElementById('panelBuscadorTerceros') == null){
			var toolbar = Ext.getCmp('ToolBar_Terceros').getTopToolbar();
			toolbar.add({
        	                xtype       : 'panel',
        	                border      : false,
        	                width       : 300,
        	                height      : 68,
        	                id          : 'panelBuscadorTerceros',
        	                bodyStyle   : 'background-color:rgba(255,255,255,0);',
        	                autoLoad    :
        	                {
        	                    url     : '../terceros/bd/bd.php',
        	                    scripts : true,
        	                    nocache : true,
        	                    timeout : 180000,
        	                    params  :
        	                    {
									op              : 'cargaBuscadorAsignadosTerceros',
									grillaName      : 'terceros',
									id_asignado     : '<?php echo $id_asignado; ?>',
									nombre_asignado : '<?php echo $nombre_asignado; ?>'
        	                    }
        	                }
        	            });
		}
		toolbar.doLayout();

		//CAMBIAR EL NOMBRE DE LOS FILTROS DEL GFILTER DE LA GRILLA
		if (document.getElementById('ElFilter_TipoTercero0_Terceros') && document.getElementById('ElFilter_TipoTercero1_Terceros')) {
			var elemento0=document.getElementById('ElFilter_TipoTercero0_Terceros').innerHTML;
			var elemento1=document.getElementById('ElFilter_TipoTercero1_Terceros').innerHTML;

			if (elemento0.split("&")[0]=="No") {document.getElementById('ElFilter_TipoTercero0_Terceros').innerHTML="Proveedores&"+elemento0.split("&")[1];}
			else if(elemento0.split("&")[0]=="Si"){document.getElementById('ElFilter_TipoTercero1_Terceros').innerHTML="Clientes&"+elemento0.split("&")[1];}

			if (elemento1.split("&")[0]=="Si") {document.getElementById('ElFilter_TipoTercero1_Terceros').innerHTML="Clientes&"+elemento1.split("&")[1];}
			else if(elemento1.split("&")[0]=="Si"){document.getElementById('ElFilter_TipoTercero0_Terceros').innerHTML="Clientes&"+elemento1.split("&")[1];}
		}

		function VentanaSubirExcelCliente(){
			var myalto  = Ext.getBody().getHeight();
			var myancho = Ext.getBody().getWidth();

			Win_Ventana_subir_excel_terceros = new Ext.Window({
			    width       : 800,
			    height      : 530,
			    id          : 'Win_Ventana_subir_excel_terceros',
			    title       : 'Subir Terceros',
			    modal       : true,
			    autoScroll  : false,
			    closable    : false,
			    autoDestroy : true,
			    autoLoad    :
			    {
			        url     : '../terceros/terceros/subir_terceros.php',
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
			                    handler     : function(){ BloqBtn(this); windows_upload_file() }
			                },
			                {
			                	xtype     : "splitbutton",
			                	id        : 'Btn_formato_upload_terceros',
			                	tooltip   : 'Imprimir a un documento PDF',
			                	iconCls   : "xls32",
			                	scale     : "large",
			                	iconAlign : 'top',
			                	text      : 'Imprimir',
			                	handler   : function(){ BloqBtn(this); dowload_formatos_terceros("formato_tercero_upload.xls"); },
			                    menu:
			                    [
			                		{
			                			text    : "Formato Terceros",
			                			iconCls : "xls16",
			                			handler : function(){ BloqBtn(this); dowload_formatos_terceros("formato_tercero_upload.xls"); }
			                		},
			                		{
			                			text    : "Codigo ubicacion Colombia",
			                			iconCls : "xls16",
			                			handler : function(){ BloqBtn(this); dowload_formatos_terceros("ubicacion_ciudad_colombia.xls"); }
			                		},
			                		{
			                			text    : "Codigo ubicacion global",
			                			iconCls : "xls16",
			                			handler : function(){ BloqBtn(this); dowload_formatos_terceros("ubicacion_ciudad_global.xls"); }
			                		},
			                		{
			                			text    : "Otros Codigos",
			                			iconCls : "xls16",
			                			handler : function(){ BloqBtn(this); dowload_formatos_terceros("codigos_documentos.xls"); }
			                		}
			                  	]
			                },
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

		function exportarTerceros() {
			window.open("../terceros/terceros/exportarTerceros.php");
		}

		function dowload_formatos_terceros(nameFile){ window.open("../terceros/terceros/bd/bd.php?opc=downloadFile&nameFile="+nameFile); }

		function Editar_Terceros(id){ VentanaAgregarCliente(id); }

		function VentanaAgregarCliente(cual){
			var myalto  = Ext.getBody().getHeight();
			var myancho  = Ext.getBody().getWidth();
			if(cual == 'false'){

				Win_Agregar_Terceros = new Ext.Window({
					width		: 820,
					id			: 'Win_Ventana_Terceros',
					height		: myalto - 100,
					title		: 'Terceros',
					modal		: true,
					autoScroll	: false,
					resizable 	: false,
					closable	: true,
					autoDestroy : true,
					autoLoad	:
					{
						url		: '../terceros/terceros/contenedor_terceros.php',
						scripts	: true,
						nocache	: true,
						params	:
						{
							cual   : cual,
							opcion : 'Vagregar'
						}
					}
				}).show();
			}
			else{
				Win_Editar_Terceros = new Ext.Window({
					width		: 820,
					id			: 'Win_Ventana_Terceros',
					height		: myalto - 100,
					title		: 'Terceros',
					modal		: true,
					autoScroll	: false,
					resizable 	: false,
					closable	: true,
					autoDestroy : true,
					autoLoad	:
					{
						url		: '../terceros/terceros/contenedor_terceros.php',
						scripts	: true,
						nocache	: true,
						params	:
						{
							cual   : cual,
							opcion : 'Vupdate'
						}
					}
				}).show();
			}
		}

		function ventanaFichaTecnica(id){
		    var myalto  = Ext.getBody().getHeight();
		    var myancho = Ext.getBody().getWidth();

            var campoTercero = document.getElementById("Terceros_nombre_comercial");
            if(campoTercero!=null){ tercero = campoTercero.value; }

		    Win_Formulario_FichaProveedor = new Ext.Window({
		        width       : 735,
		        height      : myalto - 150,
		        id          : 'Win_Formulario_FichaProveedor',
		        title       : 'Ficha Tecnica '+tercero,
		        modal       : true,
		        autoScroll  : true,
		        closable    : false,
		        autoDestroy : true,
		        autoLoad    :
		        {
		            url     : '../terceros/terceros/ficha_tecnica_proveedor.php',
		            scripts : true,
		            nocache : true,
		            params  : { id_tercero : id }
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
		                        text        : 'Guardar',
		                        scale       : 'large',
		                        iconCls     : 'guardar',
		                        iconAlign   : 'top',
		                        hidden      : false,
		                        handler     : function(){ BloqBtn(this); guardaFichaProveedor(); actualizarDivTercero(id); }
		                    },
		                    {
		                        xtype       : 'button',
		                        width       : 60,
		                        height      : 56,
		                        text        : 'Imprimir',
		                        scale       : 'large',
		                        iconCls     : 'genera_pdf',
		                        iconAlign   : 'top',
		                        hidden      : false,
		                        handler     : function(){ BloqBtn(this); imprimirFichaTecnica(id) }
		                    },
		                    {
		                        xtype       : 'button',
		                        width       : 60,
		                        height      : 56,
		                        text        : 'Regresar',
		                        scale       : 'large',
		                        iconCls     : 'regresar',
		                        iconAlign   : 'top',
		                        hidden      : false,
		                        handler     : function(){ BloqBtn(this); Win_Formulario_FichaProveedor.close(id) }
		                    }

		                ]
		            }
		        ]
		    }).show();
		}

		function actualizarDivTercero(id){ Actualiza_Div_Terceros(id); }


		function VentanaCambiaFuncionario2(id){
				Win_Cambia_Funcionario = new Ext.Window({
					width		: 450,
					//id			: 'Win_Ventana_Prospectos',
					height		: 180,
					//title		: 'Prospectos',
					modal		: true,
					autoScroll	: false,
					border		: false,
					resizable 	: false,
					closable	: false,
					autoDestroy : true,
					autoLoad	:
					{
						url		: '../terceros/terceros/CambiaFuncionarioAsignado.php',
						scripts	: true,
						nocache	: true,
						params	:
						{
							id   : id,
							cual : 'Terceros'
						}
					}
				}).show();
		}

		function VentanaEnviarEmail(id){
            var myalto  = Ext.getBody().getHeight();
            var myancho = Ext.getBody().getWidth();

            ventana_email = new Ext.Window({
                id          : 'Win_Ventana_EnvioEmail',
                title       : 'Enviar Email',
                iconCls     : 'pie2',
                width       : 950,
                height      : 530,
                modal       : true,
                autoDestroy : true,
                draggable   : false,
                resizable   : false,
                bodyStyle   : 'background-color:#DFE8F6;',
                autoLoad    :
                {
                    url     : "../terceros/terceros/envio_mail/mail_terceros.php",
                    scripts : true,
                    nocache : true,
                    params  :
                    {
                        id_tercero  : id,
                    }
                }

            }).show();
    	}

    	function TituloSubVentanaTerceros(id,ventana){
			Ext.Ajax.request({
				url		: "../panel_de_control/bd/bd.php",
				success	: function(req){
							var respuesta=req.responseText;
							if(ventana=="contactos"){ VentanaContactosTerceros(id,respuesta); }
							if(ventana=="direcciones"){ BuscarIdPais(id,respuesta); }
						},
				params: { op: 'TituloVentana', tabla: 'terceros', titulo_nueva_ventana: 'nombre', id: id}
			});

		}

		function VentanaContactosTerceros(id,titulo){
			//var myalto  = Ext.getBody().getHeight();
			//var myancho  = Ext.getBody().getWidth();
			Win_Ventana_Terceros_Contactos = new Ext.Window({
				width		: 800,
				id			: 'Win_Ventana_Terceros_Contactos',
				height		: myalto - 240,
				title		: 'Administracion de Contactos &nbsp;&nbsp;-Cliente '+titulo,
				modal		: true,
				autoScroll	: false,
				closable	: true,
				autoDestroy : true,
				autoLoad	:
				{
					url		: '../terceros/terceros/contactos.php',
					scripts	: true,
					nocache	: true,
					params	: { elid : id }
				}
			}).show();
		}

	</script>
<?php } ?>
