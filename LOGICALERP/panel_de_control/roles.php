<?php
	include("../../configuracion/conectar.php");
	include("../../configuracion/define_variables.php");
	include("../../misc/MyGrilla/class.MyGrilla.php");

	if(!isset($opcion)){
	  echo '<div id="toolbar_roles" style="height:85px"></div>';
	}

	$id_empresa          = $_SESSION['EMPRESA'];
	$GrillaName			     = 'Rol';
	$grilla      		     = new MyGrilla();
	$grilla->MySql		   = "SELECT * FROM empleados_roles WHERE	activo = 1 AND id_empresa='$id_empresa' AND codigo>0 ORDER BY valor";//CONSULTA DE LA GRILLA
	$grilla->MySqlLimit  = '0,50'; //POR DEFECTO EL LIMITE DE LA CONSULTA SIEMPRES ON 100 REGISTROS
	$grilla->MySqlInUpDe = 'SELECT * FROM empleados_roles WHERE id='.$elid; //MYSQL DE LA CONSULTA DE INSERT - UPDATE Y DELETE
	$grilla->GrillaName	 = $GrillaName;// NOMBRE DE LA GRILLA
	$grilla->AutoResize	 = 'true';
	if(isset($modulo) && $modulo == 'Usuarios'){//SI SE ESTA LLAMANDO DESDE EL MODULO DE USUARIOS
		$grilla->QuitarAncho = 25;// PIXELES A RESTAR A LO ANCHO
		$grilla->QuitarAlto	 = 170;// PIXELES A RESTAR A LO ALTO
	} else{ //SI SE ESTA LLAMANDO DESDE EL MODULO DE PANEL DE CONTROL
		$grilla->QuitarAncho = 70;// PIXELES A RESTAR A LO ANCHO
		$grilla->QuitarAlto	 = 160;// PIXELES A RESTAR A LO ALTO
	}
	$grilla->AddRow('Codigo','codigo',60,'codigo'); //(Nombre Columna, Nombre del Campo, Largo de la Celda, Funcion PHP definida dentro de la clase para dar formato)
	$grilla->AddRow('Rol de Usuario','nombre',300);
	$grilla->AddRow('Nivel','valor',50);
	//$grilla->AddRowImage('','<img src="../temas/clasico/images/BotonesTabs/historial.png" style="cursor:pointer" width="16" height="16" onclick="alert(\'el id es [id]\');">',50);
	//$grilla->AddTooltipGeneral = "<b>Esta es una prueba de Tooltip</b><br /><br /> la categoria es [id] y el usuario es [usuario]";

	if(isset($contador)){$grilla->ElContador = $contador;}
	if(isset($VarFiltros)){$grilla->VarFiltros = $VarFiltros;}
	if(isset($pagina)){$grilla->PaginaActual = $pagina;} // ES IMPORTATNTE QUE EST METODO ESTE ACTIVADO SI HAY PAGINACION (MAS DE 100 REGISTROS O LOS DEFINIDOS EN EL PARAMETRO ENTERIOR)
	if(isset($opcion)){$grilla->LaOpcion = $opcion;}
	$grilla->GeneraGrilla();

	// SE DEBE DEFINIR LA FUNCION JAVASCRIPT "Editar_< ?php echo $GrillaName ? >(elid)" ESTA FUNCION ES LA QUE SE EJECUTA CUANDO SE DA DOBLECLICK
	// DESPUES DE AGREGRA UN REGISTRO SE DEBE INVOCAR LA FUNCION Inserta_Div_< ?php echo $GrillaName ? >(elid)
	// DESPUES DE ACTUALIZAR UN REGISTRO SE DEBE INVOCAR LA FUNCION Actualiza_Div_< ?php echo $GrillaName ? >(elid)
	// DESPUES DE BORRAR UN REGISTRO SE DEBE INVOCAR LA FUNCION Elimina_Div_< ?php echo $GrillaName ? >(elid)

if(!isset($opcion)){ ?>
<script>
	new Ext.Panel(
									{
										renderTo  : 'toolbar_roles',
										frame		  : false,
										border	  : false,
										tbar		  : [
																	{
																		xtype	  : 'buttongroup',
																		columns	: 3,
																		title	  : 'Opciones',
																		items	  :	[
																								{
																									xtype		  : 'button',
																									text		  : 'Nuevo Rol',
																									scale		  : 'large',
																									iconCls		: 'add',
																									iconAlign	: 'top',
																									handler 	: function(){
																																BloqBtn(this);
																																Agregar_Rol();
																															}
																								},
																								{
																									xtype		  : 'button',
																									text		  : 'Exportar<br>Roles Y Permisos',
																									scale		  : 'large',
																									iconCls		: 'excel32',
																									iconAlign	: 'top',
																									handler 	: function(){
																																BloqBtn(this);
																																exportar_roles_permisos();
																															}
																								}
																							]
																	}
															  ]
									}
	);

	function Editar_Rol(elid){
		var myalto2  = Ext.getBody().getHeight();
		var myancho2  = Ext.getBody().getWidth();
		Win_Editar_Rol = new Ext.Window
		(
			{
				width		: 600,
				id			: 'Win_Editar_Rol',
				height		: myalto2 - 20,
				title		: 'Edicion y Permisos de Rol',
				modal		: true,
				autoScroll	: true,
				autoDestroy : true,
				autoLoad	:
				{
					url		: '../personal/permisos.php',
					scripts	: true,
					nocache	: true,
					params	:
							{
								elid 	: 	elid
							}
				},
				tbar		:
				[
					{
							xtype		: 'button',
							id			: 'BtnGuardaPermi',
							text		: 'Guardar',
							scale		: 'large',
							iconCls		: 'guardar',
							iconAlign	: 'left',
							handler 	: function(){Guardar_Permisos(elid)}
					},
					{
							xtype		: 'button',
							id			: 'BtnEliminaPermi',
							text		: 'Eliminar',
							scale		: 'large',
							iconCls		: 'eliminar',
							iconAlign	: 'left',
							handler 	: function(){Eliminar_Rol(elid)}
					}
				]
			}
		).show();
	}

	function Guardar_Permisos(elid){
		var nombre         = document.getElementById('rol').value;
		var rolnivel       = document.getElementById('rolnivel').value;
		var cuantos_checks = document.formPERMISOS.checks_PERMISOS;
		var ArrayPermisos  = new Array();

		for (i=0; i<cuantos_checks.length; i++){
			if(cuantos_checks[i].checked == true){
				ArrayPermisos[i] = cuantos_checks[i].value;
			}
		}

		var Permisos = ArrayPermisos.join(',');
		//alert(Permisos);

		Ext.Ajax.request(
			{
				url: 'bd/actualiza_permisos.php',
				success	: function (result, request)
						  {
								var resultado   =  result.responseText.split("{.}");
								var respuesta   = resultado[0];
								var observacion = resultado[1];
								//var opcion = resultado[2];
								if(respuesta == 'false'){
									alert('Error Enviando la Solicitud!\n\n'+observacion);
								}else{
									MyLoading();
									Actualiza_Div_Rol(observacion);
									Win_Editar_Rol.close();
								}
						  },
				failure : function()
						  {
								alert('Error Actualizando Permisos');
						  },
				params	: {
								id       : elid,
								nombre   : nombre,
								permisos : Permisos,
								rolnivel : rolnivel
						  }
			}
		);
	}

	function Agregar_Rol(){
		Win_Agregar_Rol1 = new Ext.Window
		(
			{
				width		: 300,
				id			: 'Win_Agregar_Rol1',
				height		: 170,
				title		: 'Agregar Rol',
				modal		: true,
				autoScroll	: true,
				autoDestroy : true,
				autoLoad	:
				{
					url		: '../panel_de_control/agregar_rol.php',
					scripts	: true,
					nocache	: true,
					params	:
							{

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
							handler 	: function(){guardaRol();}
					},'-'
				]
			}
		).show();
	}

	function exportar_roles_permisos(){
		window.open("roles/exportar_roles_permisos.php");
	}

	function Eliminar_Rol(elid){

		Ext.MessageBox.confirm('Eliminar Rol', 'Esta seguro que desaa eliminar el rol?',function(boton){
			if(boton=="yes"){
				Ext.Ajax.request(
					{
						url: 'bd/elimina_rol.php',
						success	: function (result, request)
								  {
										var resultado   =  result.responseText.split("{.}");
										var respuesta   = resultado[0];
										var observacion = resultado[1];
										//var opcion = resultado[2];
										if(respuesta == 'false'){
											Ext.MessageBox.alert('Error','Este rol no se puede eliminar por que esta asignado a un(os) empleado(s)\n\n');
										}else{
											MyLoading();
											Elimina_Div_Rol(observacion);
											Win_Editar_Rol.close();
										}
								  },
						failure : function()
								  {
										Ext.MessageBox.alert('Error','Error Eliminando Rol');
								  },
						params	: {
										id       : elid
								  }
					}
				);
			}
		});


		}
</script>
<?php } ?>
