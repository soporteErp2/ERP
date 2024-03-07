<?php
	include("../../configuracion/conectar.php");
	include("../../configuracion/define_variables.php");
	include("../../misc/MyGrilla/class.MyGrilla.php");

	$id_empresa  = $_SESSION['EMPRESA'];
	$id_sucursal = $_SESSION['SUCURSAL'];

	/**//////////////////////////////////////////////**/
	/**///		 INICIALIZACION DE LA CLASE  	  ///**/
	/**/											/**/
	/**/	$grilla = new MyGrilla();				/**/
	/**/											/**/
	/**//////////////////////////////////////////////**/

	//NOMBRE DE LA GRILLA
		$grilla->GrillaName	 		= 'Empleados';  	//NOMBRE DE LA GRILLA (DEBE SER UNICO POR CADA GRILLA DE LA APLICACION)
	//QUERY
		$grilla->TableName			= 'empleados';		//NOMBRE DE LA TABLA EN LA BASE DE DATOS
		$grilla->MyWhere			= "activo = 1 AND id_empresa=$id_empresa AND id_sucursal=$filtro_sucursal";		//WHERE DE LA CONSULTA A LA TABLA "$TableName"
		$grilla->MySqlLimit			= '0,50';			//LIMITE DE LA CONSULTA
	//TAMANO DE LA GRILLA
		$grilla->AutoResize	 		= 'true';			//SI LA GRILLA ES AUTORESIZABLE (LIQUIDA) -> "true" SI NO -> "false"
		$grilla->Ancho		 		= 800;				//ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'
		$grilla->Alto		 		= 220;				//ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'
		$grilla->QuitarAncho		= 25;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'true'
		$grilla->QuitarAlto			= 170;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'true'
	//TOOLBAR Y CAMPO DE BUSQUEDA
		$grilla->Gtoolbar			= 'true';			//SI LA GRILLA LLEVA EL TOOLBAR DE BUSQUEDA
		$grilla->CamposBusqueda		= 'nombre,documento';		//VARIABLE QUE DEFINE LOS CAMPOS DE LA BD DONDE SE BUSCARA
		$grilla->DivActualiBusqueda = '' ;				//VARIABLE QUE DEFINE LA CAPA DONDE SE ACTUALIZA LA GRILLA DESPUES DE UNA BUSQUEDA
	//CONFIGURACION DE CAMPOS EN LA GRILLA
		$grilla->AddRow('Identificacion','documento',100); //(Nombre Columna, Nombre del Campo, Largo de la Celda, Funcion PHP definida dentro de la clase para dar formato)
		$grilla->AddRow('Nombre','nombre',300);
		$grilla->AddRow('Cargo','cargo',150);
		$grilla->AddRow('Rol','rol',150);
		// $grilla->AddRow('Empresa','empresa',150);
		$grilla->AddRow('Sucursal','sucursal',150);
		$grilla->AddRowImage('Tercero','<center><img src="images/[sinc_tercero].png"><center>',50);
		$grilla->AddRow('id','idv',0);
		//$grilla->AddRow('','contactos',13);
		//$grilla->AddRowImage('','<center><div style="float:left">)</div></center>',7);

	//CONFIGURACION FORMULARIO
		$grilla->FContenedorAncho		= 350;
		$grilla->FColumnaGeneralAncho	= 330;
		$grilla->FColumnaGeneralAlto	= 25;
		$grilla->FColumnaLabelAncho		= 120;
		$grilla->FColumnaFieldAncho		= 200;

	//CONFIGURACION DE LA VENTANA DE INSERT, UPDATE Y DELETE
		$grilla->VentanaAuto		= 'false';			//SI LA VENTANA DE INSERT, UPDATE Y DELETE ES AUTOMATICA O MANUAL
		$grilla->TituloVentana		= 'Ventana Departamento'; //NOMBRE DE LA VENTANA DE INSER, UPDATE Y DELETE
		$grilla->VBarraBotones		= 'false';			//SI HAY O NO BARRA DE BOTONES
		$grilla->VBotonNuevo		= 'false';			//SI LLEVA EL BOTON DE AGREGAR REGISTRO
		$grilla->VBotonNText		= 'Nuevo Departamento'; //TEXTO DEL BOTON DE NUEVO REGISTRO
		$grilla->VBotonNImage		= 'add';			//IMAGEN CSS DEL BOTON
		//$grilla->AddBotton('Regresar','regresar','Win_Ventana_Contactos.close();Actualiza_Div_Inventario_grupos('.$elid.');');
		//$grilla->VAutoResize		= 'true';			//SI LA VENTANA ES AUTORESIZABLE (LIQUIDA) -> "true" SI NO -> "false"
		$grilla->VAncho		 		= 390;				//ANCHO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'false'
		$grilla->VAlto		 		= 190;				//ALTO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'false'
		$grilla->VQuitarAncho		= 70;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ANCHO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'true'
		$grilla->VQuitarAlto		= 150;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ALTO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'true'
		$grilla->VAutoScroll		= 'false';			//SI LA VENTANA TIENE O NO AUTOSCROLL
		$grilla->VBotonEliminar		= 'true';			//SI MUESTRA BOTON DE ELIMINAR EN LA VENTANA DE EDICION
		$grilla->VComporEliminar	= 'true';			//COMPORTAMIENTO DEL BOTON DE ELIMINAR ("true" ES CAMPO ACTIVO DE 1 A 0) ("false" -> ELIMINA EL REGISTRO DE LA BASE DE DATOS)

	/**//////////////////////////////////////////////////////////////**/
	/**///				INICIALIZACION DE LA GRILLA	  			  ///**/
	/**/															/**/
	/**/	$grilla->Link = $link;  	//Conexion a la BD			/**/
	/**/	$grilla->inicializa($_POST);//variables POST			/**/
	/**/	$grilla->GeneraGrilla(); 	// Inicializa la Grilla		/**/
	/**/															/**/
	/**//////////////////////////////////////////////////////////////**/

	$permiso_agregar   = (user_permisos(190,'false') <> 'true')? 'false' : 'true';
	$permiso_actualiza = (user_permisos(191,'false') <> 'true')? 'false' : 'true';

if(!isset($opcion)){
?>

<script>

function Editar_Empleados(id){
	if('<?php echo $permiso_actualiza; ?>' == 'true'){
		Agregar_Empleado(id); //EJECUTA LA MISMA FUNCION DE AGREGAR PERO ENVIA EL ID INDICANDO QUE ES UNA EDICION
	}else{
		alert('No posee privilegios para editar Usuarios');
	}
}

function Agregar_Empleado(id){

	var titulo="";

	if(!id){var id = false;}
	if (id==false){
		if ('<?php echo $permiso_agregar; ?>' == 'false') { alert('No posee privilegios para agregar Usuarios'); return; }
		titulo="Agregar Empleado";
		cuerpo_grilla(id,titulo);
	}
	else{
		Ext.Ajax.request({
			url		: "../panel_de_control/bd/bd.php",
			success	: function(req){
						var respuesta=req.responseText;
						titulo="Informacion del Empleado - "+respuesta;
						cuerpo_grilla(id,titulo);
					},
			params: { op: 'TituloVentana', tabla: 'empleados', titulo_nueva_ventana: 'nombre', id: id}
		});

	}

	function cuerpo_grilla(id,titulo){
		var empresa = document.getElementById('filtro_empresa').value;
		var sucursal = document.getElementById('filtro_sucursal').value;

		var myalto2  = Ext.getBody().getHeight();
		var myancho2  = Ext.getBody().getWidth();
		//if(myalto2 < 550 || myancho2 < 980){alert('Pantalla demasiado pequeña para ejecutar el subModulo de Creacion de Empleados');return false;}
		//alert(id);
		Win_Agregar_Empleado = new Ext.Window
		(
			{
				width		: myancho2 - 30,
				id			: 'Win_Agregar_Empleado',
				height		: myalto2 - 30,
				title		: titulo,
				modal		: true,
				autoScroll	: false,
				autoDestroy : true,
				autoLoad	:
				{
					url		:'agregar_empleado.php',
					scripts	:true,
					nocache	:true,
					params	:
							{
								ID 		: 	id,
								empresa	:	empresa,
								sucursal:	sucursal
							}
				},
				tbar		:
				[
					{
							xtype		: 'button',
							text		: 'Guardar<br />Datos',
							scale		: 'large',
							iconCls		: 'guardaruser',
							iconAlign	: 'left',
							handler 	: function(){BloqBtn(this); agregarEmpleado();}
					},
					{
							xtype		: 'button',
							id			: 'BtnEliminaEmpleado',
							text		: 'Eliminar<br />Empleado',
							scale		: 'large',
							iconCls		: 'eliminaruser',
							iconAlign	: 'left',
							disabled	: true,
							handler 	: function(){BloqBtn(this); EliminarEmpleado();}
					},'-',{
							xtype		: 'button',
							id			: 'btnTrasladarEmpleado',
							text		: 'Trasladar<br/>de Sucursal',
							scale		: 'large',
							iconCls		: 'trasladar',
							iconAlign	: 'left',
							disabled	: true,
							handler 	: function(){BloqBtn(this); ventana_traslado();}
					},'-',{
							xtype		: 'button',
							id			: 'btnHVEmpleado',
							text		: 'Perfil del <br/>Empleado',
							scale		: 'large',
							iconCls		: 'perfil_empleado',
							iconAlign	: 'left',
							disabled	: true,
							handler 	: function(){BloqBtn(this); ventanaPerfilEmpleado(id,titulo);}
					},' ',' ',' ',' ',' ',' ',' ',' ',

					// ,

					// {
					// 		//xtype		: 'splitbutton',
					// 		id			: 'BtnEmpleados4',
					// 		text		: 'Opciones',
					// 		scale		: 'large',
					// 		iconCls		: 'opciones',
					// 		iconAlign	: 'left',
					// 		disabled	: true,
					// 		menu		:
					// 		[
					// 			{
					// 					text		: 'Consulta Fechas de Ingreso y Retiro',
					// 					iconCls		: 'calendario16',
					// 					handler 	: function(){BloqBtn(this);}
					// 			},
					// 			{
					// 					text		: 'Generar Contrato',
					// 					iconCls		: 'doc16',
					// 					handler 	: function(){BloqBtn(this);Wizard('contrato');}
					// 			},
					// 			{
					// 					text		: 'Impresion de Carnet',
					// 					iconCls		: 'doc16',
					// 					handler 	: function(){BloqBtn(this);Wizard('carnet');}
					// 			},
					// 			{
					// 					text		: 'Informe Horas Extras',
					// 					iconCls		: 'doc16',
					// 					handler 	: function(){BloqBtn(this);horasExtras();}
					// 			}
					// 		]
					// }



				]
			}
		).show();

	}
}

function EliminarEmpleado(){
	function termina(btn){
		if(btn == 'yes'){
			// var empresa = document.getElementById('id_empresa1').value;
			// var sucursal= document.getElementById('id_sucursal1').value;
			Ext.Ajax.request
			(
				{
				url		: 'bd/bd.php',
				method	: 'post',
				timeout : 180000,
				params	:
					{
						op			:	'EliminarEmpleado',
						id			:	opcion_guardar,
						// empresa 	:	empresa,
						// sucursal	:	sucursal
					},
				success: function (result, request)
					{
						var resultado  =  result.responseText.split("{.}");
						var respuesta = resultado[0];
						var id = resultado[1];
						if(respuesta == 'true'){
							Elimina_Div_Empleados(id);
							MyLoading();
							setTimeout(function(){Win_Agregar_Empleado.close(); },500);

						}else{
							alert('Error Eliminado Empleado!');
						}
					}
				}
			);
		}
	}
	Ext.MessageBox.buttonText.yes = "Si";
	Ext.MessageBox.buttonText.no = "No";
	Ext.MessageBox.confirm('Eliminar Empleado', 'Seguro que desea Eliminar este Empleado', termina);
}

function Wizard(cual){
	Win_Wizard = new Ext.Window
	(
		{
			width		: 600,
			id			: 'Win_Wizard',
			height		: 400,
			title		: 'Agregar Empleado',
			bodyStyle	: 'background-color:#FFF',
			modal		: true,
			autoScroll	: true,
			autoDestroy : true,
			autoLoad	:
			{
				url		:'wizard_'+cual+'/wizard.php',
				scripts	:true,
				nocache	:true,
				params	:
						{
							id 		  	: IDPERSONA,
							id_empresa	: IDEMPRESA,
							id_sucursal	: IDSUCURSAL
						}
			},
			bbar		:
			[
				'->',
				{
						xtype		: 'button',
						id			: 'BtnWizardPrev',
						width		: 75,
						text		: 'Anterior',
						scale		: 'large',
						iconCls		: 'anterior',
						disabled	: true,
						iconAlign	: 'top',//left',
						handler 	: function(){BloqBtn(this); FncPrev();}
				},'-',
				{
						xtype		: 'button',
						id			: 'BtnWizardNext',
						width		: 75,
						text		: 'Siguiente',
						scale		: 'large',
						iconCls		: 'siguiente',
						disabled	: true,
						iconAlign	: 'top', //right',
						handler 	: function(){BloqBtn(this); FncNext();}
				}
			]
		}
	).show();

}

function ventanaPerfilEmpleado(id,titulo){
	var myalto  = Ext.getBody().getHeight();
	var myancho = Ext.getBody().getWidth();

	var title = titulo.split('-')[1];

	Win_Ventana_perfil_empleado = new Ext.Window({
	    width       : myancho-100,
	    height      : myalto-50,
	    id          : 'Win_Ventana_perfil_empleado',
	    // title       : 'PERFIL DE '+title,
	    modal       : true,
	    autoScroll  : false,
	    closable    : false,
	    autoDestroy : true,
	    autoLoad    :
	    {
	        url     : 'perfil_empleado/panel_perfil_empleado.php',
	        scripts : true,
	        nocache : true,
	        params  :
	        {
				id_empleado : id,
				nombre      : title,
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
	    //                 handler     : function(){ BloqBtn(this); Win_Ventana_perfil_empleado.close(id) }
	    //             }
	    //         ]
	    //     }
	    // ]
	}).show();
    // window.open("hoja_de_vida/imprimir_hv_empleado.php?id="+id+"&IMPRIME_PDF=true");
}

Agregar_Autosize_Ext("Win_Agregar_Empleado",30,30,"true","true");
Agregar_Autosize_Ext("PanelDatosDeEmpleados",45,144,"true","true");

</script>
<?php } ?>