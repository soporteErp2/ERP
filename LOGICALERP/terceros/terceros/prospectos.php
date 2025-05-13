<?php
	include("../../../configuracion/conectar.php");
	include("../../../configuracion/define_variables.php");
	include("../../../misc/MyGrilla/class.MyGrilla.php");

	//ASIGNA EL FUNCIONARIO UNA VEZ SE CREAL EL PROSPECTO /////////////////////////////////////////////////////////////
		if($opcion == 'insert'){mysql_query("INSERT INTO terceros_asignados (id_empresa,id_tercero,id_asignado) VALUES ($_SESSION[EMPRESA],$elid,$_SESSION[IDUSUARIO])",$link);	}
	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	/**//////////////////////////////////////////////**/
	/**///		   INICIALIZACION DE LA CLASE  	    ///**/
	/**/																						/**/
	/**/	$grilla = new MyGrilla();									/**/
	/**/																						/**/
	/**//////////////////////////////////////////////**/
	$fecha_creacion = date("Y-m-d");
	$idPais         = $_SESSION['PAIS'];
	$id_empresa 	= $_SESSION['EMPRESA'];

	//CONFIGURACION//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

		//NOMBRE DE LA GRILLA
			$grilla->GrillaName	 		= 'Prospectos';  	//NOMBRE DE LA GRILLA (DEBE SER UNICO POR CADA GRILLA DE LA APLICACION)
		//QUERY
			$grilla->ConsulCustom		= 'SELECT *,(SELECT asignado FROM terceros_asignados WHERE id_tercero = terceros.id AND id_empresa = '.$_SESSION["EMPRESA"].') AS FuncionarioAsignado, (SELECT id_asignado FROM terceros_asignados WHERE id_tercero = terceros.id AND id_empresa = '.$_SESSION["EMPRESA"].') AS IdFuncionarioAsignado FROM';
			$grilla->TableName			= 'terceros';		//NOMBRE DE LA TABLA DE CONSULTA EN LA BASE DE DATOS DE
			$grilla->MyWhere				= "activo = 1 and tercero = 0 AND id_empresa='$id_empresa'";// and terceros = 1';	//WHERE DE LA CONSULTA A LA TABLA "$TableName"
			$grilla->MySqlLimit			= '0,100';			//LIMITE DE LA CONSULTA
			$grilla->OrderBy				= 'nombre_comercial ASC';			//LIMITE DE LA CONSULTA

			if(isset($id_asignado) AND $id_asignado > 0){
				$grilla->MysqlHaving = "IdFuncionarioAsignado = '$id_asignado'";//BUSCA POR UN CAMPO QUE PERTENECE A OTRA TABLA
			}

		//TAMANO DE LA GRILLA
			$grilla->AutoResize	 					= 'true';		//SI LA GRILLA ES AUTORESIZABLE (LIQUIDA) -> "true" SI NO -> "false"
			$grilla->Ancho		 						= 800;			//ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'
			$grilla->Alto		 							= 220;			//ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'
			$grilla->QuitarAncho					= 30;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'true'
			$grilla->QuitarAlto						= 170;			//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'true'
			$grilla->SombraSuperiorFondo	= 'true';
		//TOOLBAR Y CAMPO DE BUSQUEDA Y FILTROS
			$grilla->Gtoolbar							= 'true';			//SI LA GRILLA LLEVA EL TOOLBAR DE BUSQUEDA
			$grilla->CamposBusqueda				= 'numero_identificacion,nombre,nombre_comercial,direccion,fecha_creacion, pais, departamento, ciudad,telefono1,telefono2,celular1,celular2,email';			//VARIABLE QUE DEFINE LOS CAMPOS DE LA BD DONDE SE BUSCARA
			$grilla->Gfilters							= 'true';
			$grilla->GfiltersAutoOpen			= 'true';
			$grilla->AddFilter('Prioridad','prioridad_prospecto','prioridad_prospecto');
			$grilla->AddFilter('Ejecutivo Comercial','IdFuncionarioAsignado','FuncionarioAsignado');
			$grilla->AddFilter('Ciudad','id_ciudad','ciudad');
			$grilla->AddRowImage('','<center><img src="../terceros/images/prioridades/prioridad_[prioridad_prospecto].png" style="" width="16" height="16" onclick=""></center>','32','');
			$grilla->AddRow('Nombre Comercial','nombre_comercial',200);
			$grilla->AddRow('Tipo doc.','tipo_identificacion',55);
			$grilla->AddRow('Identificacion','numero_identificacion',90);
			$grilla->AddRow('Telefono','telefono1',80);
			$grilla->AddRow('Celular','celular1',80);
			$grilla->AddRow('Correo Electronico','email',180);
			$grilla->AddRow('Funcionario Asignado','FuncionarioAsignado',180);

		//CONFIGURACION FORMULARIO
			$grilla->FContenedorAncho		= 760;
			$grilla->FColumnaGeneralAncho	= 380;
			$grilla->FColumnaGeneralAlto	= 25;
			$grilla->FColumnaLabelAncho		= 150;
			$grilla->FColumnaFieldAncho		= 230;

		//CONFIGURACION LAS INSERT UPDATE
			//$grilla->LastInsert		= 'sinc_tercero_erp(id_registro,"false");';
			//$grilla->LastUpdate		= 'sinc_tercero_erp(id_registro,"false");';

		//CONFIGURACION DE LA VENTANA DE INSERT, UPDATE Y DELETE
			$grilla->VentanaAuto		= 'false';			//SI LA VENTANA DE INSERT, UPDATE Y DELETE ES AUTOMATICA O MANUAL
			$grilla->TituloVentana		= 'Clientes'; 		//NOMBRE DE LA VENTANA DE INSER, UPDATE Y DELETE
			$grilla->CerrarDespuesDeAgregar = 'false';
			$grilla->VBarraBotones		= 'true';			//SI HAY O NO BARRA DE BOTONES
			$grilla->VBotonNuevo		= 'false';			//SI LLEVA EL BOTON DE AGREGAR REGISTRO
			$grilla->AddBotton('Agregar Prospectos','addcliente','VentanaAgregarCliente2("false")');
			$grilla->AddBotton('Importar Prospectos','upload_file32','VentanaSubirExcelProspecto()');
			$grilla->AddBotton('Exportar Prospectos','xls32','exportarProspectos()');

		//CONFIGURACION DEL MENU CONTEXTUAL
 			$grilla->MenuContext		= 'true';
	 		$grilla->MenuContextEliminar= 'false';
		//OPCIONES ADICIONALES EN EL MENU CONTEXTUAL
 			$grilla->AddMenuContext('Convertir Prospecto en Cliente','cliente16','VentanaAgregarCliente3([id])');
			$grilla->AddMenuContext('Cambiar Funcionario Asignado','carpeta_personal16','VentanaCambiaFuncionario([id])');
			$grilla->AddMenuContext('Enviar Email','enviaremail16','VentanaEnviarEmail([id])');

			$grilla->AddTextField('','id_empresa',240,'true','hidden', $id_empresa);

		//CONFIGURACION DE LOS FORMULARIOS DE CAPTURA Y EDICION
		//TIPO DE TERCERO
 			//$grilla->AddSeparator('Tipo de Tercero');
			//$grilla->AddComboBox ('Cliente','tipo_cliente',200,'true','false','Si:Si,No:No');
			//$grilla->AddComboBox ('Proveedor','tipo_proveedor',200,'true','false','Si:Si,No:No');
		   $grilla->AddTextField('tipo','tipo',200,'true','true','prospecto');
			$grilla->AddTextField('tercero','tercero',200,'true','true','0');
 			$grilla->AddTextField('','fecha_creacion',200,'false','hidden', $fecha_creacion);

		//DATOS DE TERCEROS
			$grilla->AddSeparator('Datos Tercero');
			$grilla->AddComboBox ('Tipo de Documento','id_tipo_identificacion',200,'true','true','tipo_documento,id,nombre,true','activo = 1');
			$grilla->AddTextField('Numero de documento','numero_identificacion',150,'true','false','false','true');
      		$grilla->AddValidation('numero_identificacion','unico_global');
			$grilla->AddTextField('Nombre Comercial','nombre_comercial',200,'true','false');
			$grilla->AddTextField('Nombre o Razon Social','nombre',200,'false','false');
      		$grilla->AddValidation('nombre','mayuscula');
      		$grilla->AddValidation('nombre_comercial','mayuscula');

		///DIRECCION PRINCIPAL
			$grilla->AddSeparator('Direccion Principal');
			$grilla->AddTextField('Direccion','direccion',200,'false','false');
			//$grilla->AddComboBox ('Tipo Identificacion','tipo_identificacion',200,'true','false','Cc.:Cedula, Nit.:Nit, Ruc.:Ruc');
			$grilla->AddComboBox ('Pais','id_pais',200,'true','true','ubicacion_pais,id,pais,true','activo = 1 ORDER BY pais ASC');
			$grilla->AddComboBox ('Departamento','id_departamento',200,'true','true','ubicacion_departamento,id,departamento,true','activo = 1 AND id_pais=49 ORDER BY departamento ASC');
			$grilla->AddComboBox ('Ciudad','id_ciudad',200,'true','true','ubicacion_ciudad,id,ciudad,true','activo = 2 ORDER BY ciudad ASC');
			$grilla->AddTextField('Telefono 1','telefono1',200,'false','false');
			$grilla->AddTextField('Telefono 2','telefono2',200,'false','false');
			//$grilla->AddTextField('Telefono 2','telefono2',200,'false','false');
			$grilla->AddTextField('Celular 1','celular1',200,'false','false');
			$grilla->AddTextField('Celular 2','celular2',200,'false','false');
			//$grilla->AddTextField('Celular 2','celular2',200,'false','false');
			$grilla->AddComboBox ('Sector Empresarial','id_sector_empresarial',200,'false','true','configuracion_sector_empresarial,id,nombre,true','activo = 1');
			//$grilla->AddTextField('Pagina Web','pagina_web',200,'false','false');
			//$grilla->AddTextField('Recomendado','configuracion_origen',200,'false','false');
			$grilla->AddTextField('','id_configuracion_origen',200,'false','hidden');
			$grilla->AddComboBox ('Prioridad','prioridad_prospecto',200,'true','false','alta:Alta,media:Media,baja:Baja');

			$grilla->AddTextField('E-mail','email',200,'false','false');
			$grilla->AddValidation('email','email');


	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


	/**//////////////////////////////////////////////////////////////**/
	/**///				INICIALIZACION DE LA GRILLA	  			  ///**/
	/**/															/**/
	/**/	$grilla->Link = $link;  	//Conexion a la BD			/**/
	/**/	$grilla->inicializa($_POST);//variables POST			/**/
	/**/	$grilla->GeneraGrilla(); 	// Inicializa la Grilla		/**/
	/**/															/**/
	/**//////////////////////////////////////////////////////////////**/

if($opcion == 'Vagregar'){
?>
	<script>
	</script>
<?php }

if($opcion == 'Vupdate'){ ?>
	<script>
	</script>
<?php }

if($opcion == 'Vupdate' || $opcion == 'Vagregar'){?>

	<script>
		/*------------------------------------------ Pais, Departamentos, Ciudad -------------------------------------------*/
		var ComboPaisProspectos = Ext.get('Prospectos_id_pais');

		ComboPaisProspectos.addListener(
			'change',
			function(event,element,options){
				id_pais = document.getElementById('Prospectos_id_pais').value;
				ActualizaDepartamentoProspectos(id_pais);
			},
			this
		);

		function ActualizaDepartamentoProspectos(id_pais){

			var MyParentDepartamentoProspectos = document.getElementById('Prospectos_id_departamento').parentNode;
			Ext.get(MyParentDepartamentoProspectos).load({
				url		: '../terceros/bd/bd.php',
				timeout : 180000,
				scripts	: true,
				nocache	: true,
				params	:
				{
					op         : 'OptionSelectDepartamento',
					clase	   : 'Prospectos',
					id_cliente : '<?php echo $id?>',
					id_pais    : id_pais
				}
			});

		};

		function YaEstaPaisProspectos(){
			id_pais_Prospectos = document.getElementById('Prospectos_id_pais').value;
			if(id_pais_Prospectos!=""){
				ActualizaDepartamentoProspectos(id_pais_Prospectos);
			}else{
				setTimeout('YaEstaPaisProspectos()',100);
			}
		}


		function YaEstaDepartamentoProspectos(){
			if(document.getElementById('Prospectos_id_departamento')){
				id_departamento_Prospectos = document.getElementById('Prospectos_id_departamento').value;
				//console.log(id_departamento);
				if(id_departamento_Prospectos!=""){
					setTimeout('ActualizaCiudadProspectos('+id_departamento_Prospectos+')',500);
					//ActualizaCiudadProspectos(id_departamento_Prospectos);
				}else{
					setTimeout('YaEstaDepartamentoProspectos()',100);
				}
			}else{
				setTimeout('YaEstaDepartamentoProspectos()',100);
			}
		}
		<?php
			if($opcion=='Vupdate'){
				echo 	"
							YaEstaPaisProspectos();
							YaEstaDepartamentoProspectos();

						";
			}

			else if($opcion=='Vagregar'){
				echo 	"
					   		document.getElementById('Prospectos_id_pais').value = '$idPais';
							YaEstaPaisProspectos();
					   ";
			}

		?>
		/*------------------------------------------------ Departamento, Ciudad----------------------------------------------*/
		function ActualizaCiudadProspectos(id_departamento){
			var MyParentCiudadProspectos = document.getElementById('Prospectos_id_ciudad').parentNode;
			id_departamento = document.getElementById('Prospectos_id_departamento').value;
			Ext.get(MyParentCiudadProspectos).load({
				url		: '../terceros/bd/bd.php',
				timeout : 1800000,
				scripts	: true,
				nocache	: true,
				params	:
				{
					op              : 'OptionSelectCiudad',
					id_cliente      : '<?php echo $id?>',
					clase	   		: 'Prospectos',
					id_departamento : id_departamento
				}
			});
		};



	</script>
<?php }

if(!isset($opcion)) { ?>
	<script>

		if(document.getElementById('panelBuscadorProspectos') == null){
			var toolbar = Ext.getCmp('ToolBar_Prospectos').getTopToolbar();
			toolbar.add({
        	                xtype       : 'panel',
        	                border      : false,
        	                width       : 300,
        	                height      : 68,
        	                id          : 'panelBuscadorProspectos',
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
									grillaName      : 'prospectos',
									id_asignado     : '<?php echo $id_asignado; ?>',
									nombre_asignado : '<?php echo $nombre_asignado; ?>'
        	                    }
        	                }
        	            });
		}

		toolbar.doLayout();

		function Editar_Prospectos(id){ VentanaAgregarCliente2(id); }

		function VentanaCambiaFuncionario(id){
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
							cual : 'Prospectos'
						}
					}
				}).show();
		}

		function VentanaAgregarCliente2(cual){
			var myalto  = Ext.getBody().getHeight();
			var myancho = Ext.getBody().getWidth();
			if(cual == 'false'){

				Win_Agregar_Prospectos = new Ext.Window({
					width		: 820,
					id			: 'Win_Ventana_Prospectos',
					height		: myalto - 100,
					title		: 'Prospectos',
					modal		: true,
					autoScroll	: false,
					resizable 	: false,
					closable	: true,
					autoDestroy : true,
					autoLoad	:
					{
						url		: '../terceros/terceros/contenedor_prospectos.php',
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

				Win_Editar_Prospectos = new Ext.Window({
					width		: 820,
					id			: 'Win_Ventana_Prospectos',
					height		: myalto - 100,
					title		: 'Prospectos',
					modal		: true,
					autoScroll	: false,
					resizable 	: false,
					closable	: true,
					autoDestroy : true,
					autoLoad	:
					{
						url		: '../terceros/terceros/contenedor_prospectos.php',
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
		//FUNCION PARA CONVERTIR PROSPECTO EN CLIENTE
		function VentanaAgregarCliente3(cual,cerrar){
			if(cerrar == 'true'){
				Win_Editar_Prospectos.close();
			}
			var este = Ext.getCmp('tabPanelComercial');
			este.setActiveTab(2);

			var myalto  = Ext.getBody().getHeight();
			var myancho = Ext.getBody().getWidth();

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
						cual   				: cual,
						opcion 				: 'Vupdate',
						Convierte_Prospecto	: 'true'
					}
				}
			}).show();
		}

		/*------------------------------------------------ ventana contactos cliente----------------------------------------------*/
		function TituloSubVentanaProspectos(id,ventana){
			Ext.Ajax.request({
				url		: "../panel_de_control/bd/bd.php",
				success	: function(req){
							var respuesta=req.responseText;
							if(ventana=="contactos"){ VentanaContactosProspectos(id,respuesta); }
							if(ventana=="direcciones"){ BuscarIdPais(id,respuesta); }
						},
				params: { op: 'TituloVentana', tabla: 'terceros', titulo_nueva_ventana: 'nombre', id: id}
			});

		}

		function BuscarIdPais(id,titulo){
			Ext.Ajax.request({
				url		: "../panel_de_control/bd/bd.php",
				success	: function(req){
							var respuesta=req.responseText;
							VentanaDireccionesTerceros(id,titulo,respuesta);
						},
				params: { op: 'TituloVentana', tabla: 'terceros', titulo_nueva_ventana: 'id_pais', id: id}
			});

		}


		function VentanaContactosProspectos(id,titulo){
			//var myalto  = Ext.getBody().getHeight();
			//var myancho  = Ext.getBody().getWidth();
			Win_Ventana_Prospectos_Contactos = new Ext.Window
			(
				{
					width		: 800,
					id			: 'Win_Ventana_Prospectos_Contactos',
					height		: myalto - 240,
					title		: 'Administracion de Contactos &nbsp;&nbsp;-Cliente '+titulo,
					modal		: true,
					autoScroll	: false,
					closable	: true,
					autoDestroy : true,
					autoLoad	:
					{
						url		:'../terceros/terceros/contactos.php',
						scripts	:true,
						nocache	:true,
						params	:
								{
									elid		: id
								}
					}
				}
			).show();
		}

		/*function VentanaDireccionesTerceros(id,titulo,id_pais){
			var myalto  = Ext.getBody().getHeight();
			var myancho  = Ext.getBody().getWidth();
			Win_Ventana_Prospectos_Direcciones = new Ext.Window
			(
				{
					width		: 700,
					id			: 'Win_Ventana_Prospectos_Direcciones',
					height		: 400,
					title		: 'Administracion de Direcciones &nbsp;&nbsp;-Cliente '+titulo,
					modal		: true,
					autoScroll	: false,
					closable	: false,
					autoDestroy : true,
					autoLoad	:
					{
						url		:'terceros/terceros/direcciones.php',
						scripts	:true,
						nocache	:true,
						params	:
								{
									elid		: id,
									id_pais 	: id_pais
								}
					}
				}
			).show();
		}*/

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
				id			: 'Win_Ventana_Prospectos_Contactos',
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

		function VentanaSubirExcelProspecto(){
			var myalto  = Ext.getBody().getHeight();
			var myancho = Ext.getBody().getWidth();

			Win_Ventana_subir_excel_prospectos = new Ext.Window({
			    width       : 800,
			    height      : 530,
			    id          : 'Win_Ventana_subir_excel_prospectos',
			    title       : 'Subir Prospectos',
			    modal       : true,
			    autoScroll  : false,
			    closable    : false,
			    autoDestroy : true,
			    autoLoad    :
			    {
			        url     : '../terceros/terceros/subir_prospectos.php',
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
			                	handler   : function(){ BloqBtn(this); download_formatos_prospectos("formato_prospecto_upload.xls"); },
			                    menu:
			                    [
			                		{
			                			text    : "Formato Prospectos",
			                			iconCls : "xls16",
			                			handler : function(){ BloqBtn(this); download_formatos_prospectos("formato_prospecto_upload.xls"); }
			                		},
			                		{
			                			text    : "Codigo ubicacion Colombia",
			                			iconCls : "xls16",
			                			handler : function(){ BloqBtn(this); download_formatos_prospectos("ubicacion_ciudad_colombia.xls"); }
			                		},
			                		{
			                			text    : "Codigo ubicacion global",
			                			iconCls : "xls16",
			                			handler : function(){ BloqBtn(this); download_formatos_prospectos("ubicacion_ciudad_global.xls"); }
			                		},
			                		{
			                			text    : "Otros Codigos",
			                			iconCls : "xls16",
			                			handler : function(){ BloqBtn(this); download_formatos_prospectos("codigos_documentos.xls"); }
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
			                    handler     : function(){ BloqBtn(this); Win_Ventana_subir_excel_prospectos.close(id) }
			                }
			            ]
			        }
			    ]
			}).show();
		}

		function download_formatos_prospectos(nameFile){ window.open("../terceros/terceros/bd/bd.php?opc=downloadFile&nameFile="+nameFile); }

		function exportarProspectos() {
			window.open("../terceros/terceros/exportarProspectos.php");
		}

	</script>
<?php } ?>
