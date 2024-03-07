<?php
    include("../../../../configuracion/conectar.php");
    include("../../../../configuracion/define_variables.php");
    include("../../../../misc/MyGrilla/class.MyGrilla.php");

    $id_empresa=$_SESSION['EMPRESA'];

    /**//////////////////////////////////////////////**/
    /**///       INICIALIZACION DE LA CLASE       ///**/
    /**/                                            /**/
    /**/    $grilla = new MyGrilla();              /**/
    /**/                                            /**/
    /**//////////////////////////////////////////////**/
    // echo $elid;

    //CONFIGURACION//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

        //NOMBRE DE LA GRILLA
            $grilla->GrillaName         = 'items_recetas';      //NOMBRE DE LA GRILLA (DEBE SER UNICO POR CADA GRILLA DE LA APLICACION)
        //QUERY
            $grilla->TableName          = 'items_recetas';      //NOMBRE DE LA TABLA EN LA BASE DE DATOS
            $grilla->MyWhere            = "activo=1 and id_empresa=$id_empresa AND id_item=$elid";     //WHERE DE LA CONSULTA A LA TABLA ""
            $grilla->OrderBy            = 'nombre_item_materia_prima ASC';           //LIMITE DE LA CONSULTA
            $grilla->MySqlLimit         = '0,50';           //LIMITE DE LA CONSULTA
        //TAMANO DE LA GRILLA
            $grilla->AutoResize         = 'false';           //SI LA GRILLA ES AUTORESIZABLE (LIQUIDA) -> "true" SI NO -> "false"
            $grilla->Ancho              = 560;              //ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'
            $grilla->Alto               = 260;              //ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'
            // $grilla->QuitarAncho        = 570;               //AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'true'
            // $grilla->QuitarAlto         = 290;              //AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'true'
        //TOOLBAR Y CAMPO DE BUSQUEDA
            $grilla->Gtoolbar           = 'true';           //SI LA GRILLA LLEVA EL TOOLBAR DE BUSQUEDA
            $grilla->CamposBusqueda     = 'campobd1,campobd2';       //VARIABLE QUE DEFINE LOS CAMPOS DE LA BD DONDE SE BUSCARA
            $grilla->DivActualiBusqueda = '' ;              //VARIABLE QUE DEFINE LA CAPA DONDE SE ACTUALIZA LA GRILLA DESPUES DE UNA BUSQUEDA
        //CONFIGURACION DE CAMPOS EN LA GRILLA
            $grilla->AddRow('Codigo','codigo_item_materia_prima',70);
            $grilla->AddRow('Cod. Barras','code_bar_item_materia_prima',70);
            $grilla->AddRow('Nombre Item','nombre_item_materia_prima',200);
            $grilla->AddRow('Cantidad','cantidad_item_materia_prima',60);
            $grilla->AddRow('Unidad Medida','unidad_medida',90);
            // $grilla->AddRowImage('','<center><div style="float:left"></div></center>',18);

            // $grilla->AddColStyle('campoBd','text-align:right; width:95px !important; margin-right:5px;');   //ALINEAR NUMEROS LA DERECHA

        //CONFIGURACION FORMULARIO
            $grilla->FContenedorAncho       = 360;
            $grilla->FColumnaGeneralAncho   = 350;
            $grilla->FColumnaGeneralAlto    = 25;
            $grilla->FColumnaLabelAncho     = 150;
            $grilla->FColumnaFieldAncho     = 200;

        //CONFIGURACION DE LA VENTANA DE INSERT, UPDATE Y DELETE
            $grilla->VentanaAuto        = 'true';          //SI LA VENTANA DE INSERT, UPDATE Y DELETE ES AUTOMATICA O MANUAL
            $grilla->TituloVentana      = 'Nuevo Ingrediente'; //NOMBRE DE LA VENTANA DE INSER, UPDATE Y DELETE
            $grilla->VBarraBotones      = 'true';          //SI HAY O NO BARRA DE BOTONES
            $grilla->VBotonNuevo        = 'true';          //SI LLEVA EL BOTON DE AGREGAR REGISTRO
            $grilla->VBotonNText        = 'Nuevo Ingrediente'; //TEXTO DEL BOTON DE NUEVO REGISTRO
            $grilla->VBotonNImage       = 'ingrediente';            //IMAGEN CSS DEL BOTON
            // $grilla->AddBotton('Regresar','regresar',' Win_Panel_Global.close();');

            $grilla->VAutoResize        = 'true';           //SI LA VENTANA ES AUTORESIZABLE (LIQUIDA) -> "true" SI NO -> "false"
            $grilla->VAncho             = 390;              //ANCHO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'false'
            $grilla->VAlto              = 190;              //ALTO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'false'
            $grilla->VQuitarAncho       = 200;               //AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ANCHO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'true'
            $grilla->VQuitarAlto        = 150;              //AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ALTO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'true'
            $grilla->VAutoScroll        = 'false';          //SI LA VENTANA TIENE O NO AUTOSCROLL
            $grilla->VBotonEliminar     = 'true';           //SI MUESTRA BOTON DE ELIMINAR EN LA VENTANA DE EDICION
            $grilla->VComporEliminar    = 'true';           //COMPORTAMIENTO DEL BOTON DE ELIMINAR ("true" ES CAMPO ACTIVO DE 1 A 0) ("false" -> ELIMINA EL REGISTRO DE LA BASE DE DATOS)

        //CONFIGURACION DEL MENU CONTEXTUAL
            $grilla->MenuContext        = 'true';       //MENU CONTEXTUAL
            $grilla->MenuContextEliminar= 'false';

        //OPCIONES ADICIONALES EN EL MENU CONTEXTUAL
            $grilla->AddMenuContext('label','calendario16','javascript');

            $grilla->AddTextField('Item materia Prima','id_item_materia_prima',180,'true','true');
            $grilla->AddTextField('Item materia Prima','nombre_item_materia_prima',170,'true');
            $grilla->AddTextField('Cantidad','cantidad_item_materia_prima',170,'true');
            // $grilla->AddComboBox ('Unidad de medida','id_unidad_medida',170,'true','true','inventario_unidades,id,nombre','activo = 1 AND id_empresa='.$id_empresa);
            $grilla->AddTextField('','id_item',180,'true','true', $elid);
            $grilla->AddTextField('','id_empresa',180,'true','true', $id_empresa);
            $grilla->AddValidation('cantidad_item_materia_prima','numero-real');



    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


    /**//////////////////////////////////////////////////////////////**/
    /**///              INICIALIZACION DE LA GRILLA               ///**/
    /**/                                                            /**/
    /**/    $grilla->Link = $link;      //Conexion a la BD        /**/
    /**/    $grilla->inicializa($_POST);//variables POST          /**/
    /**/    $grilla->GeneraGrilla();    // Inicializa la Grilla    /**/
    /**/                                                            /**/
    /**//////////////////////////////////////////////////////////////**/

 if($opcion == 'Vupdate' || $opcion == 'Vagregar'){ ?>

    <script>

    	var nombre_item_materia_prima = document.getElementById('items_recetas_nombre_item_materia_prima');
    	nombre_item_materia_prima.readOnly=true;
		nombre_item_materia_prima.style.width='149px';
		nombre_item_materia_prima.style.float='left';

    	var divBtn = document.createElement("div");
		divBtn.setAttribute("class","btnBuscar2");
		divBtn.setAttribute("onclick","buscarItemReceta()");
		divBtn.setAttribute('title','Buscar Cuenta Item a transformar');
		divBtn.innerHTML = '<img src="img/buscar20.png" />';
		document.getElementById("DIV_items_recetas_nombre_item_materia_prima").appendChild(divBtn);

		function buscarItemReceta() {
			Win_Ventana_buscarItemTransformacion = new Ext.Window({
			    width       : 650,
			    height      : 500,
			    id          : 'Win_Ventana_buscarItemTransformacion',
			    title       : 'Buscar Item como Ingrediente',
			    modal       : true,
			    autoScroll  : false,
			    closable    : false,
			    autoDestroy : true,
			    autoLoad    :
			    {
			        url     : '../funciones_globales/grillas/BusquedaInventarios.php',
			        scripts : true,
			        nocache : true,
			        params  :
			        {
						nombre_grilla : 'items_receta',
						nombreTabla   : 'items',
						sql           : ' AND id<><?php echo $elid; ?>',
						QuitarAncho   : 330,
						QuitarAlto    : 180,
						cargaFuncion  : 'cargaItemReceta(id)',
			        }
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
			                    text        : 'Regresar',
			                    scale       : 'large',
			                    iconCls     : 'regresar',
			                    iconAlign   : 'top',
			                    hidden      : false,
			                    handler     : function(){ BloqBtn(this); Win_Ventana_buscarItemTransformacion.close(id) }
			                }
			            ]
			        }
			    ]
			}).show();
		}

		function cargaItemReceta(id){
			var nombre = document.getElementById('div_items_receta_nombre_equipo_'+id).innerHTML;
			document.getElementById('items_recetas_id_item_materia_prima').value     = id;
			document.getElementById('items_recetas_nombre_item_materia_prima').value = nombre;
			Win_Ventana_buscarItemTransformacion.close()
		}

    </script>

<?php
}

if(!isset($opcion)){ ?>

    <script></script>

<?php
}
 ?>