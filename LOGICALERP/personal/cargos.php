<?php
	include("../../configuracion/conectar.php");
	include("../../configuracion/define_variables.php");
	include("../../misc/MyGrilla/class.MyGrilla.php");

	$GrillaName			 = 'Cargo';
	$grilla      		 = new MyGrilla();
	$grilla->MySql		 = 'SELECT * FROM empleados_cargos WHERE activo = 1 AND id_empresa='.$_SESSION['EMPRESA'];//CONSULTA DE LA GRILLA
	$grilla->MySqlLimit  = '0,50'; //POR DEFECTO EL LIMITE DE LA CONSULTA SIEMPRES ON 100 REGISTROS
	$grilla->MySqlInUpDe = 'SELECT * FROM empleados_cargos WHERE id='.$elid; //MYSQL DE LA CONSULTA DE INSERT - UPDATE Y DELETE
	$grilla->GrillaName	 = $GrillaName;// NOMBRE DE LA GRILLA
	$grilla->Ancho		 = 465;
	$grilla->Alto		 = 205;
	$grilla->AutoResize	 = 'false';
	// $grilla->QuitarAncho = 25;// PIXELES A RESTAR A LO ANCHO
	// $grilla->QuitarAlto	 = 170;// PIXELES A RESTAR A LO ALTO
	$grilla->AddRow('Codigo','id',60,'codigo'); //(Nombre Columna, Nombre del Campo, Largo de la Celda, Funcion PHP definida dentro de la clase para dar formato)
	$grilla->AddRow('Cargo','nombre',260);
	$grilla->AddRow('id','idv',0);
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

if(!isset($opcion)){
?>

<script>
function Agregar_Cargo(id){

	if(!id){var id = false;}

	Win_Agregar_Cargo = new Ext.Window
	(
		{
			width		: 300,
			height		: 200,
			title		: 'Agregar Cargo',
			modal		: true,
			autoScroll	: true,
			autoDestroy : true,
			autoLoad	:
			{
				url		:'agregar_cargo.php',
				scripts	:true,
				nocache	:true,
				params	:
						{
							id	:	id,
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
						handler 	: function(){guardaCargo()}
				},'-',
				{
						xtype		: 'button',
						text		: 'Eliminar',
						scale		: 'large',
						iconCls		: 'eliminar',
						iconAlign	: 'left',
						handler 	: function(){eliminarCargo()}
				}
			]
		}
	).show();
}

function Editar_Cargo(id){
	Agregar_Cargo(id); //EJECUTA LA MISMA FUNCION DE AGREGAR PERO ENVIA EL ID INDICANDO QUE ES UNA EDICION
}

function eliminarCargo(){
	function termina(btn){
		if(btn == 'yes'){
			Ext.Ajax.request
			(
				{
				url		: 'bd/bd.php',
				method	: 'post',
				timeout : 180000,
				params	:
					{
						op			:	'EliminarCargo',
						id			:	opcion_guardar
					},
				success: function (result, request)
					{
						var resultado  =  result.responseText.split("{.}");
						var respuesta = resultado[0];
						var id = resultado[1];
						if(respuesta == 'true'){
							Win_Agregar_Cargo.close();
							MyLoading();
							Elimina_Div_Cargo(id);
						}else{
							alert('Error Eliminado Cargo!');
						}
					}
				}
			);
		}
	}
	Ext.MessageBox.buttonText.yes = "Si";
	Ext.MessageBox.buttonText.no = "No";
	Ext.MessageBox.confirm('Eliminar Cargo', 'Seguro que desea Eliminar este Cargo', termina);
}
</script>
<?php } ?>