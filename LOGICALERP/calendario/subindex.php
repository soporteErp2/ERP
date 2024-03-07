<?php
	$permiso_ver_funcionarios = (user_permisos(196,'false') == 'true')? 'true' : 'false';

	$displayFuncionarios = '';
	if($permiso_ver_funcionarios == 'false'){
		$displayFuncionarios = 'display:none';
	}

?>

<script>
//////////////////////////////////   VARIABLES PARA LOS INFORMES  /////////////////////////////////////////
var my_fecha_desde = '<?php $fecha = date("Y-m-d"); echo date("Y-m-d", strtotime("$fecha -5 day")); ?>';
var my_fecha_hasta = '<?php $fecha = date("Y-m-d"); echo $fecha; ?>';
var Tam            = parent.TamVentana();
var myancho        = Tam[0];
var myalto         = Tam[1];
var apuntador_este_gridraro = 2;
var arrayContGlobals = new Array();

Ext.QuickTips.init();
/////////////////////////////////////////////////////////////////////////////////////////////////////////////

var id_activo
,	randomico_maestro
,	id_intercambio
,	id_intercambio_cotizacion
,	id_intercambio_pedido;

Ext.onReady
(function()
	{
		new Ext.Viewport //TAB PRINCIPAL
		(
			{
			layout		: 'border',
			style 		: 'font-family:Tahoma, Geneva, sans-serif; font-size:12px;',
			items:
				[
					{
						region		: 'north',
						xtype		: 'panel',
						height		: 33,
						border		: false,
						margins		: '0 0 0 0',
						html		: '<div class="DivNorth" style="float:left;<?php echo $displayFuncionarios; ?>">'
										  +'<div style="float:left">'
											  +'<input id="nom_empl_cal" class="myfield" type="text" style="width:250px;font-weight:bold" placeholder="seleccione funcionario..." onclick="BuscarFuncionario(\'id_empl_cal\',\'nom_empl_cal\',\'false\')"/>'
										  +'</div>'
										  +'<div style="float:left; width:25px; text-align:center; cursor:pointer;margin-left:2px" onclick="recargaCalendarioFuncionario(\'\',\'\')" class="buscar16">'
				  	 						 +'&nbsp;'
    		  	 						  +'</div>'
    		  	 						  +'<input id="id_empl_cal" type="hidden">'
    		  	 					  +'</div>'
    		  	 					  +'<div class="DivNorth" style="float:right; text-align:right;"><?php echo $_SESSION["NOMBREFUNCIONARIO"] ?></div>',
						bodyStyle 	: 'background-color:rgba(<?php echo $_SESSION["COLOR_MD_CALENDARIO"] ?>,1);'
					},
					{
						region			: 'center',
						xtype			: 'panel',
						closable	: false,
						autoScroll	: false,
						border		: false,
						//title		: 'Mi Dashboard',
						bodyStyle 	: 'background-color:<?php echo $_SESSION["COLOR_MD_CALENDARIO"] ?>',
						iconCls 	: 'dashboard',
						items		:
						[
							{
								xtype			: 'panel',
								id				: 'divContenedorRecibidorVisorCalendario',
								border			: false,
								bodyStyle 		: 'background-color:#FFF',
								autoLoad		:
								{
									url		: 'calendarioMes.php',//'logistico.php',
									scripts	: true,
									nocache	: true,
									params	:
										{
											ano			: '<?php echo date('Y')?>',
											mes			: '<?php echo date('n')?>',
											id_empleado : '<?php echo $_SESSION['IDUSUARIO'] ?>'
										}
								}

							}
						]

					}
				]
			}
		);
	}
);

function informe(cual){

	Ext.getCmp('contenedor_informes').load(
		{
			url 	:	'../informes/'+cual,
			scripts	:	true,
			nocache	:	true,
			params	:	{modulo:'comercial'}
		}
	);

}

/*---------------------------FUNCION EJECUTA VENTANA GLOBAL CLIENTES PARA EXTRAER DATOS------------------------*/

/*
variables funcion VentanaGlobalClientes
nombre_modulo 	--> evitar que esta grilla se llame con el mismo nombre en varios modulos
titulo 			--> titulo de la ventana de terceros-clientes
campos 			--> Array con los campos a los cuales devolver informacion cargada en otros input
					NOTA nombre_campo_grilla_extraer_dato_ DEBE SIEMPRE TERMINAR CON "_" PARA QUE SE LE CONCATENE EL ID	EXCEPTO EL PRIMERO QUE SOLO TRAE EL ID
					'id_campo:id, nombre_campo_mostrar_dato:nombre_campo_grilla_extraer_dato_,...todos los campos que se quieran devolver'
condicional 	--> aumentar condicional al where de la grilla
javascript 		--> javascript que se desea ejecutar se pueden hacer llamados de funcion tambien
*/

function VentanaGlobalClientes(nombre_modulo,titulo,campos,condicional,javascript){
	var myalto  = Ext.getBody().getHeight();
	var myancho  = Ext.getBody().getWidth();

    campos =  Base64.encode(campos);  //FUNCION DE ENCODE EN MyFunction.js codifica en 64 desde javascript y ya en la funcion se descodifica con PHP

	if (nombre_modulo=="Win_Ventana_Terceros_Global") {

		Win_Ventana_Terceros_Global = new Ext.Window({
			id			: 'Win_Ventana_Terceros_Global',
			width		: 900,// myancho-100,
			height		: myalto - 100,
			title		: titulo,
			modal		: true,
			autoScroll	: false,
			closable	: true,
			autoDestroy : true,
			autoLoad	:
			{
				url		:'BusquedaTerceros.php',
				scripts	:true,
				nocache	:true,
				params	:
						{
							nombreVentana	: nombre_modulo,
							campos			: campos,
							condicional		: condicional,
							javascript 		: javascript
						}
			}
		}).show();
	};
}

function CRM(cual,id){

	if(cual=='maestro'){var Elid_intercambio = randomico_maestro; var id='false'; var opcion_objetivo = 'documento'}
	if(cual=='cotizacion'){var Elid_intercambio = id_intercambio_cotizacion ; var id='false'; var opcion_objetivo = 'documento'}
	if(cual=='personalizado'){var Elid_intercambio = 'false'; var opcion_objetivo = 'personalizado'}

	var myalto  = Ext.getBody().getHeight();
	var myancho  = Ext.getBody().getWidth();

	Win_Ventana_CRM = new Ext.Window({
		id			: 'Win_Ventana_CRM',
		width		: myancho - 80,// myancho-100,
		height		: myalto - 80,
		title		: 'CRM Gestion de la Relacion con el Cliente - Actividades' ,
		modal		: true,
		autoScroll	: false,
		closable	: true,
		autoDestroy : true,
		//iconCls		: 'actividades16',
		autoLoad	:
		{
			url		:'../crm/actividades.php',
			scripts	:true,
			nocache	:true,
			params	:
					{
						id_intercambio 	: 	Elid_intercambio,
						opcion_objetivo	: 	opcion_objetivo,
						id              : 	id
					}
		}
	}).show();
}

function CRMobjetivos(cual){

	var myalto  = Ext.getBody().getHeight();
	var myancho  = Ext.getBody().getWidth();

	Win_Ventana_CRMObjetivos = new Ext.Window({
		id			: 'Win_Ventana_CRMObjetivos',
		width		: myancho - 30,// myancho-100,
		height		: myalto - 30,
		title		: 'CRM Gestion de la Relacion con el Cliente - Objetivos o Proyectos' ,
		modal		: true,
		autoScroll	: false,
		closable	: true,
		autoDestroy : true,
		iconCls		: 'proyecto16',
		autoLoad	:
		{
			url		:'../crm/objetivos.php',
			scripts	:true,
			nocache	:true,
			params	:
					{
						id_cliente 	: 	cual
					}
		}
	}).show();
}


//////////////////////////// AUTOSIZES DEL MODULO /////////////////////////////////
//Agregar_Autosize_Ext("DivDelTabMaestroAlquileres",1,158,"true","true");
//Agregar_Autosize_Ext("PanelDeRequerimientos",1,1,"true","true");
Agregar_Autosize_Ext('Win_Ventana_CRM',30,30,'true','true');
Agregar_Autosize_Ext('Win_Ventana_CRMObjetivos',30,30,'true','true');


//Agregar_Autosize("ContenedorPrincipalReque",1,1,"true","true");
Agregar_Autosize("contenedorIzq",220,185,"true","true");
Agregar_Autosize("contenedorDer",210,190,"false","true");
///////////////////////////////////////////////////////////////////////////////////

/*-------------- FUNCION PARA COLOCAR LOS ICONOS EN PESTAÑA PEDIDO ---------------*/
/**********************************************************************************/
// Deshabilitado por mejoras en rendimiento
// function iconoEstadoEvento(div,estado_pedido,valor){
//     if(estado_pedido >= valor && valor==2){ div.setAttribute("src","images/add3.png"); }
//     else if(estado_pedido >= valor && valor==3){ div.setAttribute("src","images/add3.png"); }
//     else if(estado_pedido >= valor && valor==4){ div.setAttribute("src","images/add3.png"); }
//     else if(estado_pedido >= valor && valor==5){ div.setAttribute("src","images/add3.png"); }
//     else if(estado_pedido >= valor && valor==7){ div.setAttribute("src","images/add3.png"); }
//     else if(estado_pedido >= valor && valor==8){ div.setAttribute("src","images/add3.png"); }
// }


function recargaCalendarioFuncionario(){

	var id_user  = document.getElementById('id_empl_cal').value
	,	nom_user = document.getElementById('nom_empl_cal').value;


	if(nom_user == ''){
		id_user = '<?php echo $_SESSION['IDUSUARIO'] ?>';
	}

	Ext.get('divContenedorRecibidorVisorCalendario').load({
		url     : 'calendarioMes.php',
		scripts : true,
		nocache : true,
		params  :
		{
			ano			: '<?php echo date('Y')?>',
			mes			: '<?php echo date('n')?>',
			id_empleado : id_user
		}
	});

	//console.log(id_user);
}
</script>