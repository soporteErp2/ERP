<?php

    include("../../../configuracion/conectar.php");
    include("../../../configuracion/define_variables.php");
    include("../../../misc/MyGrilla/class.MyGrilla.php");

    /**//////////////////////////////////////////////**/
    /**///       INICIALIZACION DE LA CLASE       ///**/
    /**/                                            /**/
    /**/    $grilla = new MyGrilla();              /**/
    /**/                                            /**/
    /**//////////////////////////////////////////////**/

            $id_empresa  = $_SESSION['EMPRESA'];


    //CONFIGURACION//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

        //NOMBRE DE LA GRILLA
            $grilla->GrillaName         = 'costosDocumentos';      //NOMBRE DE LA GRILLA (DEBE SER UNICO POR CADA GRILLA DE LA APLICACION)
        //QUERY
            $grilla->TableName          = 'costo_documento';      //NOMBRE DE LA TABLA EN LA BASE DE DATOS
            $grilla->MyWhere            = "activo=1 AND id_empresa = '$id_empresa'";     //WHERE DE LA CONSULTA A LA TABLA ""
            $grilla->OrderBy            = 'id ASC';           //LIMITE DE LA CONSULTA
            $grilla->MySqlLimit         = '0,50';           //LIMITE DE LA CONSULTA
        //TAMANO DE LA GRILLA
            //$grilla->AutoResize         = 'false';           //SI LA GRILLA ES AUTORESIZABLE (LIQUIDA) -> "true" SI NO -> "false"
            $grilla->Ancho              = 330;              //ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'
            $grilla->Alto               = 230;              //ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'
            //$grilla->QuitarAncho        = 70;               //AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'true'
            //$grilla->QuitarAlto         = 220;              //AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'true'
        //TOOLBAR Y CAMPO DE BUSQUEDA
            $grilla->Gtoolbar           = 'true';           //SI LA GRILLA LLEVA EL TOOLBAR DE BUSQUEDA
            $grilla->CamposBusqueda     = 'nombre';       //VARIABLE QUE DEFINE LOS CAMPOS DE LA BD DONDE SE BUSCARA
            $grilla->DivActualiBusqueda = '' ;              //VARIABLE QUE DEFINE LA CAPA DONDE SE ACTUALIZA LA GRILLA DESPUES DE UNA BUSQUEDA
        //CONFIGURACION DE CAMPOS EN LA GRILLA
            $grilla->AddRow('tipo','nombre',190);
            //$grilla->AddRow('Modulo','modulo',80);
            //$grilla->AddRowImage('','<center><div style="float:left"></div></center>',18);

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
            $grilla->VBotonNuevo        = 'true';          //SI LLEVA EL BOTON DE AGREGAR REGISTRO
            $grilla->VBotonNText        = 'Nuevo'; //TEXTO DEL BOTON DE NUEVO REGISTRO
            $grilla->VBotonNImage       = 'documentadd';            //IMAGEN CSS DEL BOTON
            $grilla->AddBotton('Regresar','regresar',' Win_Panel_Global.close();');

            $grilla->VAutoResize        = 'true';           //SI LA VENTANA ES AUTORESIZABLE (LIQUIDA) -> "true" SI NO -> "false"
            $grilla->VAncho             = 290;              //ANCHO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'false'
            $grilla->VAlto              = 170;              //ALTO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'false'
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

            $grilla->AddTextField('Nombre','nombre',170,'true');
            $grilla->AddComboBox('Modulo','modulo',170,'true','false','produccion:Produccion,general:General');//estatico
            $grilla->AddTextField('empresa','id_empresa',170,'true','true',$id_empresa);
            //$grilla->valida
            //$grilla->AddComboBox('label','campoBd',160,'true','boleanoSiesBd(false)','Si:Si,No:No');//estatico
            //$grilla->AddTextArea('label','campoBd',160,50,'true');


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





    </script>

<?php
}

if(!isset($opcion)){ ?>

    <script>



        function Agregar_costosDocumentos(){



                var myalto  = Ext.getBody().getHeight();
                var myancho = Ext.getBody().getWidth();

                Win_Ventana_Costos_Documento_insert = new Ext.Window({
                    width       : 390,
                    height      : 450,
                    id          : 'Win_Ventana_Costos_Documento_insert',
                    title       : 'Nuevo documento',
                    modal       : true,
                    autoScroll  : true,
                    closable    : true,
                    autoDestroy : true,
                    autoLoad    :
                    {
                        url     : 'costos/bd/ventana_costo_documento_insert.php',
                        scripts : true,
                        nocache : true,
                        params  :
                        {
                            var1 : 'var1',
                            var2 : 'var2',
                        }
                    },
                    tbar        :
                    [
                        {
                            xtype   : 'buttongroup',
                            columns : 3,
                            title   : '',
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
                                    handler     : function(){ BloqBtn(this); guardarDocumentoCosto() }
                                }

                            ]
                        }
                    ]
                }).show();
        }

        function Editar_costosDocumentos(id){

                //alert(id);

                var myalto  = Ext.getBody().getHeight();
                var myancho = Ext.getBody().getWidth();

                Win_Ventana_Costos_Documento_update = new Ext.Window({
                    width       : 390,
                    height      : 450,
                    id          : 'Win_Ventana_Costos_Documento_update',
                    title       : 'Actualizar documento',
                    modal       : true,
                    autoScroll  : true,
                    closable    : true,
                    autoDestroy : true,
                    autoLoad    :
                    {
                        url     : 'costos/bd/ventana_costo_documento_update.php',
                        scripts : true,
                        nocache : true,
                        params  :
                        {
                            id   : id,
                            var2 : 'var2',
                        }
                    },
                    tbar        :
                    [
                        {
                            xtype   : 'buttongroup',
                            columns : 3,
                            title   : '',
                            style   : 'border-right:none;',
                            items   :
                            [
                                {
                                    xtype       : 'button',
                                    width       : 60,
                                    height      : 56,
                                    text        : 'Actualizar',
                                    scale       : 'large',
                                    iconCls     : 'guardar',
                                    iconAlign   : 'top',
                                    hidden      : false,
                                    handler     : function(){ BloqBtn(this); updateDocumentoCosto(id) }
                                },
                                  {
                                    xtype       : 'button',
                                    width       : 60,
                                    height      : 56,
                                    text        : 'Eliminar',
                                    scale       : 'large',
                                    iconCls     : 'eliminar',
                                    iconAlign   : 'top',
                                    hidden      : false,
                                    handler     : function(){ BloqBtn(this); eliminarDocumentoCosto(id) }
                                }
                            ]
                        }
                    ]
                }).show();
        }

        var arrayCamposCcos = []; //DECLARO arrayCamposCcos
        function guardarDocumentoCosto(){
            //MyLoading2('on');
            var contCcos  = arrayCamposCcos.length;
            //console.log(contCcos);

            var nombre_documento  = document.getElementById('nombre_costo_documento').value;

            if(nombre_documento == ''){

                   alert('Nombre no puede quedar vacio!');
                   return;
            }

            var objCostos = {};
            var sumaValor = 0;

            for(var idCcos=0; idCcos < contCcos; idCcos++){

                campoValor     = document.getElementById('valor_'+arrayCamposCcos[idCcos]).value;

                id_costo_tipo  = arrayCamposCcos[idCcos];

                valor          = (campoValor == '')? 0 : parseInt(campoValor);
                ccos           = document.getElementById(arrayCamposCcos[idCcos]+'_id_centro_costos').value;
                cuenta_colgaap = document.getElementById(arrayCamposCcos[idCcos]+'_id_cuenta_colgaap').value;
                cuenta_niif    = document.getElementById(arrayCamposCcos[idCcos]+'_id_cuenta_niif').value;

                if(valor > 0 && (ccos == '' || cuenta_colgaap == '' || cuenta_niif == '')
                    || valor > 0 && (ccos == 0 || cuenta_colgaap == 0 || cuenta_niif == 0)){

                        alert("Al ingresar valor los demas campos no pueden quedar vacios");
                        return;
                }



                sumaValor += valor;

                //console.log(sumaValor);

                objCostos[idCcos] = {
                   id_costo_tipo  : id_costo_tipo,
                   valor          : valor,
                   ccos           : ccos,
                   cuenta_colgaap : cuenta_colgaap,
                   cuenta_niif    : cuenta_niif
                };
            }

            //console.log(arrayCamposCcos.length);

            //console.log(sumaValor);

            if(sumaValor != 100){
                   alert("La suma de los valores debe ser 100%");
                   return;
            }



            objCostos = JSON.stringify(objCostos);

            //arrayCamposCcos.length = 0;
            //console.log(objCostos);

            Ext.get('contenedor_costos_documento').load({
                url     : 'costos/bd/bd.php',
                scripts : true,
                nocache : true,
                params  :
                {
                    jsonCostos       : objCostos,
                    op               : 'guardarCostosDocumento',
                    nombre_documento : nombre_documento
                }
            });
        }

        function updateDocumentoCosto(id){

            //alert(id);
            var contCcos  = arrayCamposCcos.length;

            var nombre_documento  = document.getElementById('nombre_costo_documento').value;

            if(nombre_documento == ''){

                   alert('Nombre no puede quedar vacio!');
                   return;
            }

            var objCostos = {};
            var sumaValor = 0;

            for(var idCcos=0; idCcos< contCcos; idCcos++){

                campoValor     = document.getElementById('valor_'+arrayCamposCcos[idCcos]).value;

                //console.log(arrayCamposCcos[idCcos]);

                valor          = (campoValor == '')? 0 : parseInt(campoValor);
                ccos           = document.getElementById(arrayCamposCcos[idCcos]+'_id_centro_costos').value;
                cuenta_colgaap = document.getElementById(arrayCamposCcos[idCcos]+'_id_cuenta_colgaap').value;
                cuenta_niif    = document.getElementById(arrayCamposCcos[idCcos]+'_id_cuenta_niif').value;

                if(valor > 0 && (ccos == '' || cuenta_colgaap == '' || cuenta_niif == '')
                    || valor > 0 && (ccos == 0 || cuenta_colgaap == 0 || cuenta_niif == 0)){

                        alert("Al ingresar valor los demas campos no pueden quedar vacios");
                        return;
                }

                sumaValor += valor;

                objCostos[arrayCamposCcos[idCcos]] = {
                   valor          : valor,
                   ccos           : ccos,
                   cuenta_colgaap : cuenta_colgaap,
                   cuenta_niif    : cuenta_niif
                };
            }

            //arrayCamposCcos.length = 0;

            //console.log(arrayCamposCcos.length);

            //console.log(sumaValor);

            if(sumaValor != 100){
                   alert("La suma de los valores debe ser 100%");
                   return;
            }

            objCostos = JSON.stringify(objCostos);
            //console.log(objCostos);

            Ext.get('contenedor_costos_documento').load({
                url     : 'costos/bd/bd.php',
                scripts : true,
                nocache : true,
                params  :
                {
                    jsonCostos       : objCostos,
                    op               : 'updateCostosDocumento',
                    id_documento     : id,
                    nombre_documento : nombre_documento
                }
            });
        }

        function eliminarDocumentoCosto(id){

            var r = confirm("Seguro que desea eliminar este Documento?");
            if (r == true) {

               Ext.get('contenedor_costos_documento').load({
                    url     : 'costos/bd/bd.php',
                    scripts : true,
                    nocache : true,
                    params  :
                    {
                        op : 'deleteCostosDocumento',
                        id : id
                    }
                });

            }
            else{return;}

        }

    </script>

<?php
}

?>