<?php
	include("../../../configuracion/conectar.php");
	include("../../../configuracion/define_variables.php");
	include("../../../misc/MyGrilla/class.MyGrilla.php");

	$empresa          = $_SESSION['EMPRESA'];
	$filtro_sucursal  = $_SESSION['SUCURSAL'];
	$filtro_ubicacion = $filtro_bodega;


	// CONSULTAR EL COSTO DEL INVENTARIO
	$sql="SELECT SUM(CAST(cantidad AS DECIMAL(20, 2)) * CAST(costos AS DECIMAL(20, 2))) AS costo
			FROM inventario_totales
			WHERE activo = 1 AND id_sucursal='$filtro_sucursal' AND id_ubicacion = '$filtro_ubicacion'  AND id_empresa='$empresa' AND inventariable='true'";

	$query=$mysql->query($sql,$mysql->link);
	$costo_inventario = number_format( $mysql->result($query,0,'costo'),$_SESSION['DECIMALESMONEDA'] );
	echo "<script>document.getElementById('titleInventario').innerHTML='<b>Costo Total Inventario</b><br>$ $costo_inventario';</script>";

	/**//////////////////////////////////////////////**/
	/**///		 INICIALIZACION DE LA CLASE  	  ///**/
	/**/											/**/
	/**/	$grilla = new MyGrilla();				/**/
	/**/											/**/
	/**//////////////////////////////////////////////**/


	//CONFIGURACION//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

		//NOMBRE DE LA GRILLA
			$grilla->GrillaName	 		= 'InventarioTotales';  	//NOMBRE DE LA GRILLA (DEBE SER UNICO POR CADA GRILLA DE LA APLICACION)
		//QUERY
			$grilla->TableName			= 'inventario_totales';		//NOMBRE DE LA TABLA DE CONSULTA EN LA BASE DE DATOS DE
			$grilla->MyWhere			= 'activo = 1 AND id_sucursal='.$filtro_sucursal.' AND id_ubicacion = '.$filtro_ubicacion.'  AND id_empresa='.$_SESSION['EMPRESA'].' AND inventariable="true"';		//WHERE DE LA CONSULTA A LA TABLA "$TableName"
			$grilla->OrderBy 			= 'codigo ASC';
			$grilla->MySqlLimit			= '0,100';			//LIMITE DE LA CONSULTA
		//TAMANO DE LA GRILLA
			$grilla->AutoResize	 		= 'true';			//SI LA GRILLA ES AUTORESIZABLE (LIQUIDA) -> "true" SI NO -> "false"
			//$grilla->Ancho		 	= 800;				//ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'
			//$grilla->Alto		 		= 220;				//ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'
			$grilla->QuitarAncho		= 25;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'true'
			$grilla->QuitarAlto			= 170;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'true'
		//TOOLBAR Y CAMPO DE BUSQUEDA
			$grilla->Gtoolbar			= 'true';			//SI LA GRILLA LLEVA EL TOOLBAR DE BUSQUEDA
			$grilla->CamposBusqueda		= 'familia,grupo,subgrupo,nombre_equipo,codigo';		//VARIABLE QUE DEFINE LOS CAMPOS DE LA BD DONDE SE BUSCARA
			$grilla->DivActualiBusqueda = '' ;				//VARIABLE QUE DEFINE LA CAPA DONDE SE ACTUALIZA LA GRILLA DESPUES DE UNA BUSQUEDA

		//CONFIGURACION DE CAMPOS EN LA GRILLA
			$grilla->AddRow('id','id',40);
			$grilla->AddRow('Codigo','codigo',80);
			$grilla->AddRow('Codigo de Barras','code_bar',100);
			$grilla->AddRow('Nombre del Item','nombre_equipo',250);
			$grilla->AddRow('Costo','costos',80);
			$grilla->AddRowImage('Cantidad','<div title="[cantidad]" id="div_InventarioTotales_cantidad_[id]" class="InventarioTotales_cantidad_stock">[cantidad]</div>','80');
			$grilla->AddRowImage('Stock.Min','<div title="[cantidad_minima_stock]" class="InventarioTotales_min_stock">[cantidad_minima_stock]</div>','70');
			$grilla->AddRowImage('Stock.Max','<div title="[cantidad_maxima_stock]" class="InventarioTotales_max_stock">[cantidad_maxima_stock]</div>','70');
			$grilla->AddRow('Familia','familia',200);
			$grilla->AddRow('Grupo','grupo',200);
			$grilla->AddRow('Subgrupo','subgrupo',200);

			//$grilla->AddRowImage('Por Ingresar','<div title="[cantidad_pendiente]">[cantidad_pendiente]</div>','80');
			$grilla->AddRowImage('Venta','<center><img src="img/[estado_venta]_inv.png"></center>','40');
			$grilla->AddRowImage('Compra','<center><img src="img/[estado_compra]_inv.png"></center>','45');

			$grilla->AddColStyle('costos','text-align:right; width:75px !important; margin-right:5px;');   //ALINEAR NUMEROS LA DERECHA

			$grilla->FContenedorAncho		= 500;
			$grilla->FColumnaGeneralAncho	= 250;
			$grilla->FColumnaGeneralAlto	= 25;
			$grilla->FColumnaLabelAncho		= 250;
			$grilla->FColumnaFieldAncho		= 25;

		//CONFIGURACION DE LA VENTANA DE INSERT, UPDATE Y DELETE
			$grilla->VentanaAuto            = 'false';			//SI LA VENTANA DE INSERT, UPDATE Y DELETE ES AUTOMATICA O MANUAL
			$grilla->TituloVentana          = 'Ventana Inventario'; //NOMBRE DE LA VENTANA DE INSER, UPDATE Y DELETE
			$grilla->CerrarDespuesDeAgregar = 'false';
			$grilla->VBarraBotones          = 'false';			//SI HAY O NO BARRA DE BOTONES
			$grilla->VBotonNuevo            = 'false';			//SI LLEVA EL BOTON DE AGREGAR REGISTRO
			$grilla->VBotonNText            = 'Nuevo Item'; 	//TEXTO DEL BOTON DE NUEVO REGISTRO
			$grilla->VBotonNImage           = 'addequipo';	//IMAGEN CSS DEL BOTON
			//$grilla->VAutoResize          = 'true';			//SI LA VENTANA ES AUTORESIZABLE (LIQUIDA) -> "true" SI NO -> "false"
			$grilla->VAncho                 = 560;				//ANCHO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'false'
			//$grilla->VAlto                = 570;				//ALTO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'false'
			//$grilla->VQuitarAncho         = 540;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ANCHO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'true'
			$grilla->VQuitarAlto            = 20;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ALTO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'true'
			$grilla->VAutoScroll            = 'true';			//SI LA VENTANA TIENE O NO AUTOSCROLL
			$grilla->VBotonEliminar         = 'false';			//SI MUESTRA BOTON DE ELIMINAR EN LA VENTANA DE EDICION
			$grilla->VComporEliminar        = 'true';			//COMPORTAMIENTO DEL BOTON DE ELIMINAR ("true" ES CAMPO ACTIVO DE 1 A 0) ("false" -> ELIMINA EL REGISTRO DE LA BASE DE DATOS)

		//CONFIGURACION DEL MENU CONTEXTUAL
 			$grilla->MenuContext		= 'true';		//MENU CONTEXTUAL
	 		$grilla->MenuContextEliminar= 'false';

		//OPCIONES ADICIONALES EN EL MENU CONTEXTUAL
 			if (user_permisos(51,'false') == 'true') $grilla->AddMenuContext('Traslado de Inventario','doc','ventana_traslado([id])');
			$grilla->AddMenuContext('Imprimir Codigo de Barras','barcode16','ventana_codigo_barras([id])');
			if (user_permisos(52,'false') == 'true') $grilla->AddMenuContext('Configurar Cantidades Stock','config16','ventanaConfigurarInventario([id])');
			// if (user_permisos(53,'false') == 'true') $grilla->AddMenuContext('Agregar al Inventario','auto_back16','ventana_entrada_salida([id],"entrada",[id_item],"[nombre_equipo]")');
			// if (user_permisos(54,'false') == 'true') $grilla->AddMenuContext('Sacar del inventario','auto_go16','ventana_entrada_salida([id],"salida",[id_item],"[nombre_equipo]")');
			$grilla->AddMenuContext('Kardex','auto_go16','kardex([id],[id_item],"[nombre_equipo]")');

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

		filtro_empresa   = '<?php echo $empresa ?>';
		filtro_sucursal  = '<?php echo $filtro_sucursal ?>';
		filtro_ubicacion = '<?php echo $filtro_ubicacion ?>';

		function Editar_InventarioTotales(id){  };

		//DAR COLOR CUANDO NO SE CUMPLA MINSTOCK O MAXSTOCK
		var arrayMinStock      = document.getElementById('DIV_contenedor_InventarioTotales').querySelectorAll('.InventarioTotales_min_stock');
		var arrayMaxStock      = document.getElementById('DIV_contenedor_InventarioTotales').querySelectorAll('.InventarioTotales_max_stock');
		var arrayCantidadStock = document.getElementById('DIV_contenedor_InventarioTotales').querySelectorAll('.InventarioTotales_cantidad_stock');

		for(divFila in arrayCantidadStock ){
			if(isNaN(divFila)) continue;
			if((arrayCantidadStock[divFila].innerHTML * 1) >= (arrayMaxStock[divFila].innerHTML * 1)){ arrayMaxStock[divFila].style.color='red'; }
			if((arrayCantidadStock[divFila].innerHTML * 1) <= (arrayMinStock[divFila].innerHTML * 1)){ arrayMinStock[divFila].style.color='red'; }
		}

		function kardex(id,id_item,nombre){
			var myalto  = Ext.getBody().getHeight();
			var myancho = Ext.getBody().getWidth();

			Win_Ventana_kardex = new Ext.Window({
			    width       : 800,
			    height      : 500,
			    id          : 'Win_Ventana_kardex',
			    title       : 'Kardex ',
			    modal       : true,
			    autoScroll  : false,
			    closable    : false,
			    autoDestroy : true,
			    items       :
			    [
			        {
			            closable    : false,
			            border      : false,
			            autoScroll  : true,
			            iconCls     : '',
			            bodyStyle   : 'background-color:#fff; border-top:1px solid #5179B3;',
			            items       :
			            [
			                {
			                    xtype       : "panel",
			                    id          : 'contenedor_kardex',
			                    border      : false,
			                    bodyStyle   : 'background-color:<?php echo $_SESSION['COLOR_FONDO'] ?>;',
			                }
			            ]
			        }
			    ],
			    tbar        :
			    [
                    {
                        xtype       : 'panel',
                        border      : false,
                        width       : 160,
                        height      : 56,
                        bodyStyle   : 'background-color:rgba(255,255,255,0);',
                        autoLoad    :
                        {
                            url     : 'inventario_unidades/bd/bd.php',
                            scripts : true,
                            nocache : true,
                            params  :
                            {
								op            : 'filtro_fecha_kardex',
								id            : id,
								id_item       : id_item,
								filtro_bodega : '<?php echo $filtro_ubicacion ?>',
                            }
                        }
                    },
                    {
	                    xtype       : 'button',
	                    width       : 60,
	                    height      : 56,
	                    text        : 'Exportar',
	                    scale       : 'large',
	                    iconCls     : 'excel32',
	                    iconAlign   : 'top',
	                    hidden      : false,
	                    handler     : function(){ BloqBtn(this); descargarExcelKardex(); }
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
	                    handler     : function(){ BloqBtn(this); Win_Ventana_kardex.close(id) }
	                }
			    ]
			}).show();
		}


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
						url		: 'inventario_unidades/bd/bd.php',
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
				window.open("inventario_unidades/imprimir_acta_inventario.php?filtro_empresa=<?php echo $empresa; ?>&filtro_sucursal=<?php echo $filtro_sucursal; ?>&filtro_ubicacion=<?php echo $filtro_ubicacion; ?>&fecha_ini="+fecha_ini+"&fecha_fin="+fecha_fin+"&opc=imprimirActaParcialBetween");
			}
		}

		/*-------------------------------------- funcion para entrar o sacar articulos del inventario --------------------------------------------*/
		function ventana_entrada_salida(id,opc,id_item,nombre){

			var cantidad = document.getElementById('div_InventarioTotales_cantidad_'+id).innerHTML;

			titulo=(opc=='entrada')? 'Entrada al Inventario ' : 'Salida del Inventario ' ;
			var myalto  = Ext.getBody().getHeight();
			var myancho = Ext.getBody().getWidth();
			Win_Ventana_entrada_salida = new Ext.Window
			(
				{
					width		: 400,
					id			: 'Win_Ventana_entrada_salida_inventario',
					height		: 320,
					title		: titulo+' '+nombre,
					modal		: true,
					autoScroll	: false,
					closable	: false,
					autoDestroy : true,
					autoLoad	:
					{
						url		: 'inventario_unidades/movimiento_nota/movimiento_nota.php',
						scripts	: true,
						nocache	: true,
						params	:
						{
							id              : id,
							id_item         : id_item,
							filtro_sucursal : filtro_sucursal,
							filtro_bodega   : filtro_ubicacion,
							opc             : opc,
							nombre          : nombre,
							cantidad        : cantidad
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
							handler 	: function(){Win_Ventana_entrada_salida.close()}
						},'-'
					]
				}
			).show();
		}

		/*--------------------------------------------------------------Funcion Grilla Ventana Traslado----------------------------------------------------*/

		function ventana_traslado(id){
			var cantidad_actual=document.getElementById('div_InventarioTotales_cantidad_'+id).innerHTML;
			var titulo  = (document.getElementById('div_InventarioTotales_codigo_'+id).innerHTML)+' '+(document.getElementById('div_InventarioTotales_nombre_equipo_'+id).innerHTML);
			var myalto  = Ext.getBody().getHeight();
			var myancho = Ext.getBody().getWidth();
			Win_Ventana_Traslado = new Ext.Window
			(
				{
					width		: myancho-100,
					id			: 'Win_Ventana_Agregar_Traslado',
					height		: myalto - 50,
					title		: 'Traslados de Inventario &nbsp;&nbsp;-'+titulo,
					modal		: true,
					autoScroll	: false,
					closable	: false,
					autoDestroy : true,
					autoLoad	:
					{
						url		: 'inventario_unidades/inventario_traslado.php',
						scripts	: true,
						nocache	: true,
						params	:
						{
							elid                    : id,
							cantidad_actual			: cantidad_actual,
							filtro_sucursal_origen  : filtro_sucursal,
							filtro_ubicacion_origen : filtro_ubicacion
						}
					}
				}
			).show();
		}

		function ventana_codigo_barras(id){
			var titulo  = (document.getElementById('div_InventarioTotales_codigo_'+id).innerHTML)+' '+(document.getElementById('div_InventarioTotales_nombre_equipo_'+id).innerHTML);
			Win_Ventana_CodigoBarras = new Ext.Window({
				width		: 400,
				id			: 'Win_Ventana_CodigoBarras',
				height		: 300,
				title		: 'Codigo de Barras Equipo &nbsp;&nbsp;-'+titulo,
				modal		: true,
				autoScroll	: false,
				closable	: true,
				autoDestroy : true,
				autoLoad	:
				{
					url		: 'inventario_unidades/CodigoBarras.php',
					scripts	: true,
					nocache	: true,
					params	:
					{
						elid                    : id,
						filtro_empresa_origen   : filtro_empresa,
						filtro_sucursal_origen  : filtro_sucursal,
						filtro_ubicacion_origen : filtro_ubicacion
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
						url		: 'inventario_unidades/impresion_grupo_codigos_barras.php',
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
				campo="codigo";
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
							url		: 'inventario_unidades/CodigoBarrasRango.php',
							scripts	: true,
							nocache	: true,
							params	:
							{
								campo                   : campo,
								limite_inferior         : limite_inferior,
								limite_superior         : limite_superior,
								elid                    : id,
								filtro_empresa_origen   : filtro_empresa,
								filtro_sucursal_origen  : filtro_sucursal,
								filtro_ubicacion_origen : filtro_ubicacion
							}
						}
					}
				).show();
			}
		}

		////////////////////////// Historico Inventario ////////////////////////////////////
		function historico_inventario(id){
			var myalto  = Ext.getBody().getHeight();
			var myancho = Ext.getBody().getWidth();
			var title   = document.getElementById('div_InventarioTotales_codigo_'+id).innerHTML;
			title += ' '+document.getElementById('div_InventarioTotales_nombre_equipo_'+id).innerHTML;

			Win_inventario_historico = new Ext.Window({
				id			: 'Win_inventario_historico',
				width		: myancho-200,
				height		: myalto-50,
				title		: 'Historico inventario '+title,
				modal		: true,
				autoScroll	: false,
				closable	: false,
				autoDestroy : true,
				autoLoad	:
				{
					url		: 'inventario_unidades/historico/grillaHistoricoInventario.php',
					scripts	: true,
					nocache	: true,
					params	: { elid : id }
				},
				tbar		:
				[
					{
						xtype     : 'button',
						scale     : 'large',
						width     : 80,
						height    : 40,
						iconCls   : 'regresar',
						text      : 'Regresar',
						iconAlign : 'top',
						handler   : function(){ Win_inventario_historico.close(id) }
					}
				]
			}).show();
		}

		function ventanaConfigurarInventario(id){

			var title = document.getElementById('div_InventarioTotales_nombre_equipo_'+id).innerHTML+' -Codigo '+document.getElementById('div_InventarioTotales_codigo_'+id).innerHTML;
			Win_inventario_configuracion = new Ext.Window({
				id			: 'Win_inventario_configuracion',
				width		: 300,
				height		: 200,
				title		: 'Stock&nbsp;&nbsp;'+title,
				modal		: true,
				autoScroll	: false,
				closable	: false,
				autoDestroy : true,
				autoLoad	:
				{
					url		: 'inventario_unidades/configurar_cantidades_stock.php',
					scripts	: true,
					nocache	: true,
					params	: { id : id }
				},
				tbar		:
				[
					{
						xtype     : 'button',
						scale     : 'large',
						width     : 80,
						height    : 40,
						iconCls   : 'guardar',
						text      : 'Guardar',
						iconAlign : 'top',
						handler   : function(){ guardarValoresCantidadStock(); }
					},
					{
						xtype     : 'button',
						scale     : 'large',
						width     : 80,
						height    : 40,
						iconCls   : 'regresar',
						text      : 'Regresar',
						iconAlign : 'top',
						handler   : function(){ Win_inventario_configuracion.close() }
					}
				]
			}).show();
		}

		function ventanaConfigurarInforme(){
			Win_Ventana_configurar = new Ext.Window({
			    width       : 500,
				height      : 280,
			    id          : 'Win_Ventana_configurar',
			    title       : 'Configuracion inventario exportado',
			    modal       : true,
			    autoScroll  : false,
			    closable    : true,
			    autoDestroy : true,
			    autoLoad    :
			    {
			        url     : 'inventario_unidades/wizard_exportar_inventario.php',
			        scripts : true,
			        nocache : true,
			        params  :
			        {
			            opc : 'informe_kardex',
			        }
			    },
			}).show();
		}

		function generarInventarioExcel(){
			var id_bodega   		 = document.getElementById('filtro_ubicacion_inventario').value
			,	separadorDecimales   = document.getElementById('separadorDecimales').value
			,	separadorMiles   	 = document.getElementById('separadorMiles').value

			const url = "inventario_unidades/excel_inventario.php?filtro_empresa="+'<?php echo $empresa; ?>'+"&filtro_sucursal="+'<?php echo $filtro_sucursal ?>'+"&filtro_ubicacion="+id_bodega+"&separador_decimales="+separadorDecimales+"&separador_miles="+separadorMiles;
			
			//console.log(url);
			window.open(url);
		}

		function validarSelect(separadorId){

			const selectDecimales	  = document.getElementById('separadorDecimales'),
				  selectMiles 		  = document.getElementById('separadorMiles')

			if(selectDecimales.value === selectMiles.value && separadorId === 'decimales'){
				selectMiles.value = (selectMiles.value === ',')? "." : ",";
			}
			else if(selectDecimales.value === selectMiles.value){
				selectDecimales.value = (selectDecimales.value === ',')? "." : ",";
			}
		}

</script>
<?php
} ?>
