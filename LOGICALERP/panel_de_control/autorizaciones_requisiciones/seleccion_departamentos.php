<?php
	include("../../../configuracion/conectar.php");
	include("../../../configuracion/define_variables.php");
	include("../../../misc/MyGrilla/class.MyGrilla.php");

	/**//////////////////////////////////////////////**/
	/**///		 INICIALIZACION DE LA CLASE  	  ///**/
	/**/											/**/
	/**/	$grilla = new MyGrilla();				/**/
	/**/											/**/
	/**//////////////////////////////////////////////**/

	$id_empresa    = $_SESSION['EMPRESA'];
	$grupo_empresa = $_SESSION['GRUPOEMPRESARIAL'];
	//CONFIGURACION//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

		//NOMBRE DE LA GRILLA
            $grilla->GrillaName         = 'costo_departamentos';      //NOMBRE DE LA GRILLA (DEBE SER UNICO POR CADA GRILLA DE LA APLICACION)
        //QUERY
            $grilla->TableName          = 'costo_departamentos';      //NOMBRE DE LA TABLA EN LA BASE DE DATOS
            $grilla->MyWhere            = "activo=1 AND id_empresa = '$id_empresa'";     //WHERE DE LA CONSULTA A LA TABLA ""
            $grilla->OrderBy            = 'id ASC';           //LIMITE DE LA CONSULTA
            $grilla->MySqlLimit         = '0,50';           //LIMITE DE LA CONSULTA
        //TAMANO DE LA GRILLA
            //$grilla->AutoResize         = 'false';           //SI LA GRILLA ES AUTORESIZABLE (LIQUIDA) -> "true" SI NO -> "false"
            $grilla->Ancho              = 570;              //ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'
            $grilla->Alto               = 320;              //ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'
            //$grilla->QuitarAncho        = 70;               //AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'true'
            //$grilla->QuitarAlto         = 220;              //AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'true'
        //TOOLBAR Y CAMPO DE BUSQUEDA
            $grilla->Gtoolbar           = 'true';           //SI LA GRILLA LLEVA EL TOOLBAR DE BUSQUEDA
            $grilla->CamposBusqueda     = 'nombre';       //VARIABLE QUE DEFINE LOS CAMPOS DE LA BD DONDE SE BUSCARA
            $grilla->DivActualiBusqueda = '' ;              //VARIABLE QUE DEFINE LA CAPA DONDE SE ACTUALIZA LA GRILLA DESPUES DE UNA BUSQUEDA
        //CONFIGURACION DE CAMPOS EN LA GRILLA
            $grilla->AddRow('Codigo','codigo',40);
            $grilla->AddRow('Nombre','nombre',320);
            $grilla->AddRow('Modulo','modulo',80);
            $grilla->AddRowImage('Autorizadores','<center><img src="../../temas/clasico/images/BotonesTabs/user_check.png" onclick="ventana_autorizadores([id])" style="width:15px;height:15px;cursor:pointer;"></center>',80);

            //$grilla->AddColStyle('campoBd','text-align:right; width:95px !important; margin-right:5px;');   //ALINEAR NUMEROS LA DERECHA

        //CONFIGURACION FORMULARIO
            $grilla->FContenedorAncho       = 350;
            $grilla->FColumnaGeneralAncho   = 330;
            $grilla->FColumnaGeneralAlto    = 25;
            $grilla->FColumnaLabelAncho     = 50;
            $grilla->FColumnaFieldAncho     = 200;

        //CONFIGURACION DE LA VENTANA DE INSERT, UPDATE Y DELETE
            $grilla->VentanaAuto        = 'false';          //SI LA VENTANA DE INSERT, UPDATE Y DELETE ES AUTOMATICA O MANUAL
            $grilla->TituloVentana      = 'Nuevo'; //NOMBRE DE LA VENTANA DE INSER, UPDATE Y DELETE
            $grilla->VBarraBotones      = 'true';          //SI HAY O NO BARRA DE BOTONES
            $grilla->VBotonNuevo        = 'false';          //SI LLEVA EL BOTON DE AGREGAR REGISTRO
            $grilla->VBotonNText        = 'Nuevo'; //TEXTO DEL BOTON DE NUEVO REGISTRO
            $grilla->VBotonNImage       = 'documentadd';            //IMAGEN CSS DEL BOTON
            $grilla->AddBotton('Regresar','regresar','Win_Panel_Global.close();');

            $grilla->VAutoResize        = 'true';           //SI LA VENTANA ES AUTORESIZABLE (LIQUIDA) -> "true" SI NO -> "false"
            $grilla->VAncho             = 290;              //ANCHO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'false'
            $grilla->VAlto              = 180;              //ALTO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'false'
            $grilla->VQuitarAncho       = 200;               //AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ANCHO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'true'
            $grilla->VQuitarAlto        = 150;              //AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ALTO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'true'
            $grilla->VAutoScroll        = 'false';          //SI LA VENTANA TIENE O NO AUTOSCROLL
            $grilla->VBotonEliminar     = 'true';           //SI MUESTRA BOTON DE ELIMINAR EN LA VENTANA DE EDICION
            $grilla->VComporEliminar    = 'true';           //COMPORTAMIENTO DEL BOTON DE ELIMINAR ("true" ES CAMPO ACTIVO DE 1 A 0) ("false" -> ELIMINA EL REGISTRO DE LA BASE DE DATOS)

        //CONFIGURACION DEL MENU CONTEXTUAL
        //     $grilla->MenuContext        = 'true';       //MENU CONTEXTUAL
        //     $grilla->MenuContextEliminar= 'false';

        // //OPCIONES ADICIONALES EN EL MENU CONTEXTUAL
        //     $grilla->AddMenuContext('label','calendario16','javascript');

            $grilla->AddTextField('Codigo','codigo',170,'true');
            $grilla->AddTextField('Nombre','nombre',170,'true');
            $grilla->AddComboBox('Modulo','modulo',170,'true','false','produccion:Produccion,general:General');//estatico
            $grilla->AddTextField('empresa','id_empresa',170,'true','true',$id_empresa);

            $grilla->AddValidation('codigo','unico_global');
            $grilla->AddValidation('codigo','mayuscula');
            $grilla->AddValidation('nombre','mayuscula');

	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


	/**//////////////////////////////////////////////////////////////**/
	/**///				INICIALIZACION DE LA GRILLA	  			  ///**/
	/**/															/**/
	/**/	$grilla->Link = $link;  	 //Conexion a la BD			/**/
	/**/	$grilla->inicializa($_POST); //variables POST			/**/
	/**/	$grilla->GeneraGrilla(); 	 // Inicializa la Grilla	/**/
	/**/															/**/
	/**//////////////////////////////////////////////////////////////**/


if(!isset($opcion)){  ?>
	<script>

		function Editar_costo_departamentos(id){
			// body...
		}

		function ventana_autorizadores(id_area){

			Win_Ventana_autirzadores = new Ext.Window({
			    width       : 500,
			    height      : 450,
			    id          : 'Win_Ventana_autirzadores',
			    title       : 'Personas Autorizadoras',
			    modal       : true,
			    autoScroll  : false,
			    closable    : false,
			    autoDestroy : true,
			    autoLoad    :
			    {
			        url     : 'autorizaciones_requisiciones/autorizaciones_requisiciones.php',
			        scripts : true,
			        nocache : true,
			        params  :
			        {
			            id_area : id_area,
			        }
			    }
			}).show();
		}

	</script>
<?php }

if ($opcion=='Vupdate' || $opcion=='Vagregar') {
?>
	<script>



		// var inputDocEmpleado   = document.getElementById('costo_autorizadores_requisicion_documento_empleado');
		// var inputEmpleado      = document.getElementById('costo_autorizadores_requisicion_nombre_empleado');
		// var inputEmpleadoEmail = document.getElementById('costo_autorizadores_requisicion_email');
		// var inputCargo         = document.getElementById('costo_autorizadores_requisicion_cargo');

		// inputDocEmpleado.readOnly   = true;
		// inputEmpleado.readOnly      = true;
		// inputEmpleadoEmail.readOnly = true;
		// inputCargo.readOnly         = true;

		// inputDocEmpleado.setAttribute("style","float:left; width:135px;");

		// var divBtnPlantilla = document.createElement("div");
		// divBtnPlantilla.setAttribute("class","divBtnBuscarPuc");
		// divBtnPlantilla.setAttribute("onclick","ventanaBuscarEmpleado()");
		// divBtnPlantilla.setAttribute('title','Buscar Empleado');
		// divBtnPlantilla.innerHTML = '<img src="img/buscar20.png" />';
		// document.getElementById("DIV_costo_autorizadores_requisicion_documento_empleado").appendChild(divBtnPlantilla);

		// function ventanaBuscarEmpleado(){
		// 	var myalto  = Ext.getBody().getHeight();
		// 	var myancho = Ext.getBody().getWidth();

		// 	Win_Ventana_buscar_empleado = new Ext.Window({
		// 	    width       : myancho-100,
		// 	    height      : myalto-50,
		// 	    id          : 'Win_Ventana_buscar_empleado',
		// 	    title       : '',
		// 	    modal       : true,
		// 	    autoScroll  : false,
		// 	    closable    : false,
		// 	    autoDestroy : true,
		// 	    autoLoad    :
		// 	    {
		// 	        url     : '../funciones_globales/grillas/BusquedaVendedor.php',
		// 	        scripts : true,
		// 	        nocache : true,
		// 	        params  :
		// 	        {
		// 				cargaFuncion  : 'renderizaEmpleado(id)',
		// 				nombre_grilla : 'empleados',
		// 	        }
		// 	    },
		// 	    tbar        :
		// 	    [
		// 	        {
		// 	            xtype   : 'buttongroup',
		// 	            columns : 3,
		// 	            title   : 'Opciones',
		// 	            style   : 'border-right:none;',
		// 	            items   :
		// 	            [
		// 	                {
		// 	                    xtype       : 'button',
		// 	                    width       : 60,
		// 	                    height      : 56,
		// 	                    text        : 'Regresar',
		// 	                    scale       : 'large',
		// 	                    iconCls     : 'regresar',
		// 	                    iconAlign   : 'top',
		// 	                    hidden      : false,
		// 	                    handler     : function(){ BloqBtn(this); Win_Ventana_buscar_empleado.close(id) }
		// 	                }
		// 	            ]
		// 	        }
		// 	    ]
		// 	}).show();
		// }

		// function renderizaEmpleado(id){

		// 	var documento   = document.getElementById('div_empleados_documento_'+id).innerHTML
		// 	,	nombre      = document.getElementById('div_empleados_nombre_'+id).innerHTML
		// 	,	email      = document.getElementById('email_'+id).innerHTML
		// 	,	id_cargo    = document.getElementById('id_cargo_'+id).innerHTML
		// 	,	cargo       = document.getElementById('cargo_'+id).innerHTML;

		// 	document.getElementById('costo_autorizadores_requisicion_id_empleado').value        = id;
		// 	document.getElementById('costo_autorizadores_requisicion_documento_empleado').value = documento;
		// 	document.getElementById('costo_autorizadores_requisicion_nombre_empleado').value    = nombre;
		// 	document.getElementById('costo_autorizadores_requisicion_email').value    = email;
		// 	document.getElementById('costo_autorizadores_requisicion_id_cargo').value           = id_cargo;
		// 	document.getElementById('costo_autorizadores_requisicion_cargo').value              = cargo;

		// 	Win_Ventana_buscar_empleado.close(id);

		// }

	</script>

<?php
}
?>