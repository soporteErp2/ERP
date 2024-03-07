<?php
	include("../../../../configuracion/conectar.php");
	include("../../../../configuracion/define_variables.php");
	include("../../../../misc/MyGrilla/class.MyGrilla.php");
	include '../../../erp_paises_global/config_paises.php';

	$id_empresa = $_SESSION['EMPRESA'];
	$id_pais    = $_SESSION['PAIS'];

	// VALIDAR EL PLAN Y LA CANTIDAD DE SUCURSALES PERMITIDAS
	$sql="SELECT COUNT(id) AS cont FROM empresas_sucursales WHERE activo=1 AND id_empresa=$id_empresa";
	$query=mysql_query($sql,$link);
	$numero_sucursales=mysql_result($query,0,'cont');

	if ($_SESSION['PLAN_SUCURSALES']<=$numero_sucursales) {
		$btnNuevo='false';
	}
	else{
		$btnNuevo='true';
	}

	/**//////////////////////////////////////////////**/
	/**///		 INICIALIZACION DE LA CLASE  	  ///**/
	/**/											/**/
	/**/	$grilla = new MyGrilla();				/**/
	/**/											/**/
	/**//////////////////////////////////////////////**/


	//CONFIGURACION//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

		//NOMBRE DE LA GRILLA
			$grilla->GrillaName	 		= 'Sucursal';  	//NOMBRE DE LA GRILLA (DEBE SER UNICO POR CADA GRILLA DE LA APLICACION)
		//QUERY
			$grilla->TableName			= 'empresas_sucursales';			//NOMBRE DE LA TABLA EN LA BASE DE DATOS
			$grilla->MyWhere			= "activo = 1 AND id_empresa = '$id_empresa'";		//WHERE DE LA CONSULTA A LA TABLA "$TableName"
			$grilla->MySqlLimit			= '0,50';			//LIMITE DE LA CONSULTA

		//TAMANO DE LA GRILLA
			//$grilla->AutoResize	 	= 'true';			//SI LA GRILLA ES AUTORESIZABLE (LIQUIDA) -> "true" SI NO -> "false"
			$grilla->Ancho		 		= 610;				//ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'
			$grilla->Alto		 		= 360;				//ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'
			//$grilla->QuitarAncho		= 750;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'true'
			//$grilla->QuitarAlto		= 210;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'true'

		//TOOLBAR Y CAMPO DE BUSQUEDA
			$grilla->Gtoolbar			= 'true';			//SI LA GRILLA LLEVA EL TOOLBAR DE BUSQUEDA
			$grilla->CamposBusqueda		= 'nombre';		//VARIABLE QUE DEFINE LOS CAMPOS DE LA BD DONDE SE BUSCARA

		//CONFIGURACION DE CAMPOS EN LA GRILLA
			// $grilla->AddRow('Codigo','id',60,'codigo'); //(Nombre Columna, Nombre del Campo, Largo de la Celda, Funcion PHP definida dentro de la clase para dar formato)
			$grilla->AddRow('id','id',50);
			$grilla->AddRow('Sucursal','nombre',250);
			$grilla->AddRowImage('Bodegas','<div style="float:left; margin:0 0 0 10px; cursor:pointer" onClick="ventana_sucursal_bodega([id])"><div style="float:left"><img src="../../temas/clasico/images/BotonesTabs/sucursales16.png" ></div><div style="float:left">&nbsp;([bodegas])</div></div>',65)	;

		//CONFIGURACION FORMULARIO
			$grilla->FContenedorAncho		= 300;
			$grilla->FColumnaGeneralAncho	= 290;
			$grilla->FColumnaGeneralAlto	= 25;
			$grilla->FColumnaLabelAncho		= 75;
			$grilla->FColumnaFieldAncho		= 200;

		//CONFIGURACION DE LA VENTANA DE INSERT, UPDATE Y DELETE
			$grilla->VentanaAuto		= 'true';			//SI LA VENTANA DE INSERT, UPDATE Y DELETE ES AUTOMATICA O MANUAL
			$grilla->TituloVentana		= 'Administracion Sucursal'; //NOMBRE DE LA VENTANA DE INSER, UPDATE Y DELETE
			$grilla->VBarraBotones		= 'true';			//SI HAY O NO BARRA DE BOTONES
			$grilla->VBotonNuevo		= $btnNuevo;			//SI LLEVA EL BOTON DE AGREGAR REGISTRO
			$grilla->VBotonNText		= 'Nueva Sucursal'; //TEXTO DEL BOTON DE NUEVO REGISTRO
			$grilla->VBotonNImage		= 'addsucursal';			//IMAGEN CSS DEL BOTON
			$grilla->AddBotton('Estructura','sucursal','VentanaEstructuraEmpresa();');
			$grilla->VAutoResize		= 'true';			//SI LA VENTANA ES AUTORESIZABLE (LIQUIDA) -> "true" SI NO -> "false"
			$grilla->VAncho		 		= 300;				//ANCHO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'false'
			$grilla->VAlto		 		= 210;				//ALTO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'false'
			$grilla->VQuitarAncho		= 70;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ANCHO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'true'
			$grilla->VQuitarAlto		= 160;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ALTO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'true'
			$grilla->VAutoScroll		= 'false';			//SI LA VENTANA TIENE O NO AUTOSCROLL
			$grilla->VBotonEliminar		= 'true';			//SI MUESTRA BOTON DE ELIMINAR EN LA VENTANA DE EDICION
			$grilla->VComporEliminar	= 'true';			//COMPORTAMIENTO DEL BOTON DE ELIMINAR ("true" ES CAMPO ACTIVO DE 1 A 0) ("false" -> ELIMINA EL REGISTRO DE LA BASE DE DATOS)

		//CONFIGURACION DE LO FORMULARIOS DE CAPTURA Y EDICION
			$grilla->AddTextField('','id_empresa',200,'false','hidden', $id_empresa);
			$grilla->AddTextField('Sucursal','nombre',180,'true','false');
			$grilla->AddComboBox($labelDepto,'id_departamento',180,'false','true','ubicacion_departamento,id,departamento,true','activo=1 AND id_pais='.$id_pais);
			$grilla->AddComboBox($labelCiudad,'id_ciudad',180,'false','true','0:Problema al Cargar la base de datos');
			$grilla->AddComboBox($labelMunicipio,'id_comuna',180,'false','true','0:Problema al Cargar la base de datos');

	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	/**//////////////////////////////////////////////////////////////**/
	/**///				INICIALIZACION DE LA GRILLA	  			  ///**/
	/**/															/**/
	/**/	$grilla->Link = $link;  	//Conexion a la BD			/**/
	/**/	$grilla->inicializa($_POST);//variables POST			/**/
	/**/	$grilla->GeneraGrilla(); 	// Inicializa la Grilla		/**/
	/**/															/**/
	/**//////////////////////////////////////////////////////////////**/

if(!isset($opcion)){ ?>

	<script>
		function ventana_sucursal_bodega(id){
			var myalto  = Ext.getBody().getHeight()
			,	myancho = Ext.getBody().getWidth()
			, 	titulo  = document.getElementById('div_Sucursal_nombre_'+id).innerHTML;

			Win_Ventana_Bodega = new Ext.Window({
				width		: 490,
				id			: 'Win_Ventana_Bodega',
				height		: 400,
				title		: 'Bodegas &nbsp;&nbsp;-Sucursal '+titulo,
				modal		: true,
				autoScroll	: false,
				autoDestroy : true,
				autoLoad	:
				{
					url		: '../erp_paises_global/panel_de_control/sucursales/bodega.php',
					scripts	: true,
					nocache	: true,
					params	:
					{
						filtro_empresa  : "<?php echo $id_empresa;?>",
						filtro_sucursal : id
					}
				}
			}).show();

		};

		function VentanaEstructuraEmpresa(){
			var myalto  = Ext.getBody().getHeight()
			,	myancho = Ext.getBody().getWidth();

			Win_Ventana_Estructura_empresa = new Ext.Window
			(
				{
					width		: 350,
					id			: 'Win_Ventana_Estructura_empresa',
					height		: myalto - 80,
					title		: 'Estructura Sucursales&nbsp; -Bodegas',
					modal		: true,
					autoScroll	: true,
					closable	: false,
					autoDestroy : true,
					autoLoad	:
					{
						url		: '../erp_paises_global/sucursales/estructura.php',
						scripts	: true,
						nocache	: true,
						params	:
								{ opc : "ver" }
					},
					tbar		:
					[
						{
							xtype		: 'buttongroup',
							title		: 'Opciones',
							columns		: 4,
							items		:
							[
								{
									xtype		: 'button',
									width		: 80,
									text		: 'Imprimir',
									scale		: 'large',
									iconCls		: 'genera',
									iconAlign	: 'top',
									handler 	: function(){window.open("sucursales/estructura.php?opc=imprimir")}
								},
								{
									xtype		: 'button',
									width		: 80,
									text		: 'Regresar',
									scale		: 'large',
									iconCls		: 'regresar',
									iconAlign	: 'top',
									handler 	: function(){Win_Ventana_Estructura_empresa.close(id)}
								}
							]
						}
					]
				}
			).show();
		}


	</script>
<?php
}
else if($opcion =='Vupdate' || $opcion == 'Vagregar'){  ?>
	<script>

		//==================== UPDATE COMBO CIUDAD ====================//
		var comboDepartamento = Ext.get('Sucursal_id_departamento');
		comboDepartamento.addListener(
			'change',
			function(event,element,options){
				id_departamento = document.getElementById('Sucursal_id_departamento').value;
				ActualizaComboCiudad(id_departamento);
			},this
		);

		ActualizaComboCiudad(document.getElementById('Sucursal_id_departamento').value);

		function ActualizaComboCiudad(id_departamento){
			var MyParent = document.getElementById('Sucursal_id_ciudad').parentNode;
			Ext.get(MyParent).load({
				url		: '../erp_paises_global/panel_de_control/sucursales/bd/bd.php',
				// url		: 'sucursales/bd/bd.php',
				timeout : 180000,
				scripts	: true,
				nocache	: true,
				params	:
				{
					opc             : 'optionCiudad',
					id_sucursal     : '<?php echo $id?>',
					id_departamento : id_departamento
				}
			});
		};

		// console.log(window.location);
		// var comboCiudad = Ext.get('Sucursal_id_ciudad');
		// comboCiudad.addListener(
		// 	'change',
		// 	function(event,element,options){
		// 		id_ciudad = document.getElementById('Sucursal_id_ciudad').value;
		// 		ActualizaComboComuna(id_ciudad);
		// 	},this
		// );

		// ActualizaComboComuna(document.getElementById('Sucursal_id_ciudad').value);

		function ActualizaComboComunaSucursal(id_ciudad){
			var MyParent = document.getElementById('Sucursal_id_comuna').parentNode;
			Ext.get(MyParent).load({
				url		: '../erp_paises_global/panel_de_control/sucursales/bd/bd.php',
				timeout : 180000,
				scripts	: true,
				nocache	: true,
				params	:
				{
					opc             : 'optionComuna',
					id_sucursal     : '<?php echo $id?>',
					id_ciudad 		: id_ciudad
				}
			});
		};

    </script>

<?php } ?>