<?php
	include("../../configuracion/conectar.php");
	include("../../configuracion/define_variables.php");
	include("../../misc/MyGrilla/class.MyGrilla.php");

	$GrillaName			 = 'Documentos';
	$grilla      		 = new MyGrilla();
	$grilla->MySql		 = 'SELECT * FROM empleados_tipo_documento WHERE	activo = 1 AND id_empresa='.$_SESSION['EMPRESA'];//CONSULTA DE LA GRILLA
	$grilla->MySqlLimit  = '0,50'; //POR DEFECTO EL LIMITE DE LA CONSULTA SIEMPRES ON 100 REGISTROS
	$grilla->MySqlInUpDe = 'SELECT * FROM empleados_tipo_documento WHERE id='.$elid." AND id_empresa=".$_SESSION['EMPRESA']; //MYSQL DE LA CONSULTA DE INSERT - UPDATE Y DELETE
	$grilla->GrillaName	 = $GrillaName;// NOMBRE DE LA GRILLA
	$grilla->Ancho		 = 465;
	$grilla->Alto		 = 205;
	$grilla->AutoResize	 = 'false';
	// $grilla->QuitarAncho = 25;// PIXELES A RESTAR A LO ANCHO
	// $grilla->QuitarAlto	 = 170;// PIXELES A RESTAR A LO ALTO
	$grilla->AddRow('Codigo','id',60,'codigo'); //(Nombre Columna, Nombre del Campo, Largo de la Celda, Funcion PHP definida dentro de la clase para dar formato)
	$grilla->AddRow('Tipo de Documento','nombre',270);
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

function Agregar_Documentos(id){
	if(!id){var id = false;}

	Win_Agregar_Documento = new Ext.Window({
		width		: 300,
		//id			: 'Win_Agregar_Cargo',
		height		: 160,
		title		: 'Agregar Documento',
		modal		: true,
		autoScroll	: true,
		autoDestroy : true,
		autoLoad	:
		{
			url		: 'agregar_documento.php',
			scripts	: true,
			nocache	: true,
			params	: { id	: id }
		},
		tbar		:
		[
			{
				xtype		: 'button',
				text		: 'Guardar',
				scale		: 'large',
				iconCls		: 'guardar',
				iconAlign	: 'left',
				handler 	: function(){ guardaDocumento(); }
			},'-'
		]
	}).show();
}

function Editar_Documentos(id){
	Agregar_Documentos(id); //EJECUTA LA MISMA FUNCION DE AGREGAR PERO ENVIA EL ID INDICANDO QUE ES UNA EDICION
}

</script>
<?php } ?>