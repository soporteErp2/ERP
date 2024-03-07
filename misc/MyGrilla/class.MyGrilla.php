<?php

//
//PARA QUE ESTA CLASE FUNCIONES EN NECESARIO TENER LAS SIGUIENTES LIBRERIAS:
//ExtJS 3
//MyFunction.js
//fileuploader.js


class MyGrilla {

	/*----------------------------------------------------------------------------------------------------------------------------------------------------------------------------*/
	//VARIABLES PUBLICAS
	//GRILLA
		public  $MySql      			= "";		//Consulta de datos de la grilla
		public  $MySqlLimit      		= "0,100";	//Consulta de datos de la grilla
		public	$ConsulCustom			= 'false';
		public  $MysqlHaving            = 'false';
		public  $MySqlInUpDe			= "";
		public  $GrillaName 			= "";		//Nombre de la Grilla
		public  $AutoResize				= 'true';	//si el tamano cambia automaticamente en funcion del tamano del navegador
		public  $LlevaPaginacion		= 'true';  //Define si lleva o no paginacion, esta variable se define sola
		public  $Ancho					= 0;		//Ancho de la grilla si la opcion Autoresize es 'false'
		public  $Alto					= 0;		//Alto de la grilla si la opcion Autoresize es 'false'
		public  $QuitarAncho			= 0;		//Pixeles que seran restados de el ancho en caso que la opcion AutoResize sea 'true'
		public  $QuitarAlto				= 0;		//Pixeles que seran restados de el alto en caso que la opcion AutoResize sea 'true'
		public  $AddTooltipGeneral  	= '';		//Estring que sera mostardo en un tooltip
		public  $LaOpcion				= 'false'; 	//[false] [insert] [update] [delete]
		public  $PaginaActual       	= 1;		//pagina en la que se encuentra actualmente
		//public  $VarFiltros				= 'false';  //variable que guarda las variables post cuando la pagina tiene variables de filtro
		public  $ElContador				= 0;
		public	$TableName				= '';		//NOMBRE DE LA TABLA EN LA BASE DE DATOS
		public	$TableName2				= '';		//NOMBRE DE LA TABLA EN LA BASE DE DATOS
		public	$MyWhere				= '';		//WHERE DE LA CONSULTA A LA TABLA "$TableName"
		public	$OrderBy				= '';		//ORDER BY DE LA CONSULTA A LA TABLA "$TableName"
		public	$GroupBy				= '';		//GROUP BY DE LA CONSULTA A LA TABLA "$TableName"
		public 	$VariableInUpDe			= '';		//ID PARA INSER, UPDATE Y DELETE
		public	$VarPost				= '';
		public	$VarPostSql				= '';
		public  $VariablesPost			= '';
		public 	$Link					= '';
		public  $FiltroEmpresa			= ''; 		//VARIABLE QUE GUARDA EL FILTRO DE LA EMPRESA
		public  $FiltroSucursal			= ''; 		//VARIABLE QUE GUARDA EL FILTRO DE LA EMPRESA
		public  $CerrarDespuesDeAgregar = 'true';   //VARIABLE QUE CIERRA LA VENTANA DE AGREGAR DESPUES DE GAURDAR EN REGISTRO NUEVO
		public  $CerrarDespuesDeEditar  = 'true';   //VARIABLE QUE CIERRA LA VENTANA DE AGREGAR DESPUES DE GAURDAR EN REGISTRO NUEVO
		public	$ElPathDeLaClase		= "/misc/MyGrilla";

	//BARRA DE BUSQUEDA
		public  $Gtoolbar				= 'false'; 	//VARIABLE QUE DEFINE SI LA GRILLA LLEVA EL TOOLBAR DE BUSQUEDAS
		public	$CamposBusqueda			= '';		//VARIABLE QUE DEFINE LOS CAMPOS DE LA BD DONDE SE BUSCARA
		public  $DivActualiBusqueda 	= '';		//VARIABLE QUE DEFINE LA CAPA DONDE SE ACTUALIZA LA GRILLA DESPUES DE UNA BUSQUEDA
		public	$MyFiltroBusqueda		= '';		//VARIABLE QUE GUARDA LOS DATOS DEL FILTRO DE BUSQUEDA

	//BARRA DE FILTROS
		public  $Gfilters				= 'false';
		private	$MyFiltersActivado		= 'false';
		public  $GfiltersAutoOpen		= 'false';
		public  $MySqlFilter      		= array();
		private $CuantosFilters 		= 0;
		private	$FilterNombre			= array();
		private	$FilterIdCampo			= array();
		private	$FilterNombreCampo		= array();
        private $condicional            = "";
        private $condicional2           = "";


	//VENTANAS
		public 	$VentanaAuto			= 'false';	//SI LA VENTANA DE INSERT, UPDATE Y DELETE ES AUTOMATICA O MANUAL
		public 	$VBarraBotones			= 'false';	//SI HAY O NO BARRA DE BOTONES
		public 	$VBotonNuevo			= 'true';	//SI HAY O NO BARRA DE BOTONES
		public 	$VBotonNText			= 'Agregar Registro'; //TEXTO DEL BOTON DE NUEVO REGISTRO
		public	$VBotonNImage			= 'add';	//IMAGEN DEL BOTON DE NUEVO REGISTRO
		public 	$VAutoResize			= 'false';	//SI LA VENTANA ES AUTORESIZABLE (LIQUIDA) -> "true" SI NO -> "false"
		public 	$VAncho		 			= 0;		//ANCHO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'false'
		public 	$VAlto		 			= 0;		//ALTO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'false'
		public 	$VQuitarAncho			= 0;		//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ANCHO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'true'
		public 	$VQuitarAlto			= 0;		//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ALTO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'true'
		public 	$VAutoScroll			= 'false';	//SI LA VENTANA TIENE O NO AUTOSCROLL
		public 	$VBotonEliminar			= 'true';	//SI MUESTRA BOTON DE ELIMINAR EN LA VENTANA DE EDICION
		public 	$VComporEliminar		= 'true';	//COMPORTAMIENTO DEL BOTON DE ELIMINAR ("true" CAMBIA EL CAMPO ACTIVO DE 1 A 0) ("false" -> ELIMINA EL REGISTRO DE LA BASE DE DATOS)
		public 	$VSqlBtnEliminar		= 'false';	//VALIDA SI EJECUTA BTN ELIMINAR CON UNA CONSULTA SQL

	//MENU CONTEXTUAL
		public 	$MenuContext			= 'false';	//MENU CONTEXTUAL
		public 	$MenuContextEliminar	= 'false';	//ELIMINAR EN EL MENU CONTEXTUAL

	//FORMULARIOS
		public	$FContenedorAncho		= 350;
		public	$FColumnaGeneralAncho	= 340;
		public  $FColumnaGeneralAlto	= 22;
		public	$FColumnaLabelAncho		= 150;
		public  $FColumnaFieldAncho		= 150;

	//FUNCIONES DESPUES DE SAVE O UPDATE
		public $LastInsert 				= '';
		public $LastUpdate 				= '';
		public $StopEventLastInsert 	= 'false';
		public $StopEventLastUpdate 	= 'false';
		public $Formulario 				= 'false';


	/*----------------------------------------------------------------------------------------------------------------------------------------------------------------------------*/
	//VARIABLES PRIVADAS
	//GRILLA
		private $ElTitulo				= array();	//Array con los titulos de columna
		private $ElDato					= array();	//Array con los nombres de campos en la base de datos
		private $ElLargo				= array();	//Array con los largos de columna
		private $LaImagen				= array();	//Array con los nombres de campos en la base de datos
		private $CuantasRows 			= 0;		//Variable de la clase (nunca se debe definir)
		private $LargTotal				= 0;		//Variable de la clase (nunca se debe definir)
		private $LabelPaginacion		= '';		//variable que guarda el label que sale en la paginacion
		private $MaxPage				= 1;		//variable que guarda el total de las paginas
		private $NewAlto				= 0;        //variable que guarda el alto del listado cuando la grilla no es Autoresize
		private $colStyle 				= '';		//Array de estilos por indice nombre de columna mysql

	//VENTANAS
		private	$CuantosFields			= 0;		//Contador del Numero de Fields
		private $FieldLabel 			= array();	//Array con los labels
		private $FieldField				= array();	//Array con los nombres de los fields
		private $FieldLargo				= array();	//Array con el largo de los fields
		private $FieldAlto				= array();	//Array con el largo de los fields
		private $FieldObligatorio		= array();	//Array con los datos de obligatoriedad
		private	$FieldHidden			= array();	//Array con los datos de visibilidad
		private	$FieldHiddenValue		= array();	//Array con los datos del valor cuando el campo es oculto
		private $FieldTipo				= array();	//Array con el tipo de campo (combo - field)
		private $FieldBd				= array();	//Array con datos de la generacion del combo (BD o Manual)
		private $FieldArray				= array();	//Array con los datos en array de los combos
		private $FieldWhere				= array();  //Array con los datos del where de la consulta de combobox
		private $FieldDigitoVerificacion= array();  //Array con los datos si el fiel es nit y tiene campo de digito de verificacion

	//BOTONES
		private	$CuantosBottons			= 0;		//Contador del Numero de Botones
		private $BottonText 			= array();	//Array con los labels de los botones
		private $BottonIcon				= array();	//Array con los iciones de los botones
		private $BottonFunction			= array();	//Array con las funciones de los botones

	//CONTEXTMENU
		private	$CuantosContextMenu		= 0;		//Contador del Numero de Botones
		private $ContextMenuText 		= array();	//Array con los labels de los botones
		private $ContextMenuIcon		= array();	//Array con los iciones de los botones
		private $ContextMenuFunction	= array();	//Array con las funciones de los botones

	//BOTONES DE LA VENTANA IN-UP-DE
		private	$CuantosBottonsVentana	= 0;		//Contador del Numero de Botones
		private $BottonTextVentana 		= array();	//Array con los labels de los botones
		private $BottonIconVentana		= array();	//Array con los iciones de los botones
		private $BottonFunctionVentana	= array();	//Array con las funciones de los botones
		private $BottonVentanaInsert	= array();	//Array con las funciones de los botones
		private $BottonVentanaUpdate	= array();	//Array con las funciones de los botones

	//VALIDACIONES
		private	$CuantosValidations		= 0;		//Contador del array de validaciones
		private	$ValidacionesCampos		= array();	//array con los nombres de los campos a validad
		//private	$TextValidacionesCampos		= array();	//array con los nombres de los campos a validad
		private $Validaciones			= array();	//array con las validaciones
        private $ValidacionGlobal       = 'false';  //Si hay o no validacion Global al guardar formulario
        private $ValidacionGlobalField  = ''; 		//Campo que se valida al guardar el formulario
        private $ValidacionGlobalSql  	= ''; 		//Campo que se valida al guardar el formulario
        private $ValidacionEmail        = 'false';  //Si hay o no validacion Global al guardar formulario
        private $ValidacionEmailField   = ''; 		//Campo que se valida al guardar el formulario
        private $arrayDigitoVerificacion= array(49 /*colombia*/); 	//array Paises con funcion digito de verificacion

		private $ElQuitarAltoOriginal	= 0;
		private $ElAltoOriginal			= 0;

	//SQL
		private	$ArrayEditLike			= array();	//EDITAR EL LIKE DE LA CONSULTA A LA TABLA "$TableName"


	/*----------------------------------------------------------------------------------------------------------------------------------------------------------------------------*/
	//FUNCION QUE INICIALIZA LAS VARIABLES QUE VIENE POR POST
	public function inicializa($MyPost){
		if($this->Formulario=='true' && !isset($MyPost['opcion'])){
			$sqlFormulario   = "SELECT COUNT(id) AS cont, id FROM $this->TableName WHERE $this->MyWhere LIMIT 0,1";
			$queryFormulario = mysql_query($sqlFormulario);
			$idFormulario    = mysql_result($queryFormulario, 0, 'id');
			$contFormulario  = mysql_result($queryFormulario, 0, 'cont');

			if($contFormulario > 0){
				$MyPost['opcion'] = 'Vupdate';
				$MyPost['id']     = $idFormulario;
			}
			else{
				$MyPost['opcion'] ='Vagregar';
				$MyPost['id']     =false;
			}
		}

		if(isset($MyPost['contador'])){$this->ElContador = $MyPost['contador'];}
		if(isset($MyPost['VarFiltros'])){$this->VarFiltros = $MyPost['VarFiltros'];}
		if(isset($MyPost['pagina'])){$this->PaginaActual = $MyPost['pagina'];} // ES IMPORTANTE QUE ESTE METODO ESTE ACTIVADO SI HAY PAGINACION (MAS DE 100 REGISTROS O LOS DEFINIDOS EN EL PARAMETRO ENTERIOR)
		if(isset($MyPost['opcion'])){$this->LaOpcion = $MyPost['opcion'];}
		if(isset($MyPost['id'])){$this->VariableInUpDe = $MyPost['id'];}
		if(isset($MyPost['elid'])){$this->VariableInUpDe = $MyPost['elid'];}
		if(isset($MyPost['filtro_empresa'])){$this->FiltroEmpresa = $MyPost['filtro_empresa'];}
		if(isset($MyPost['filtro_sucursal'])){$this->FiltroSucursal = $MyPost['filtro_sucursal'];}
		if(isset($MyPost['VBarraBotones'])){$this->VBarraBotones = $MyPost['VBarraBotones'];}
		if(isset($MyPost['MyFiltroBusqueda'])){$this->MyFiltroBusqueda = addslashes($MyPost['MyFiltroBusqueda']);}
		if(isset($MyPost['MyFiltersActivado'])){$this->MyFiltersActivado = $MyPost['MyFiltersActivado'];}
		if(isset($MyPost['condicional'])){$this->condicional = $MyPost['condicional'];}
		if(isset($MyPost['condicional2'])){$this->condicional2 = $MyPost['condicional2'];}
		if(isset($MyPost['ValidacionGlobalSql'])){$this->ValidacionGlobalSql = $MyPost['ValidacionGlobalSql'];}


		$VarPost    = '';
		$VarPostSql = '';
		foreach($MyPost as $nombre_campo => $valor){
			if(
				$nombre_campo!='contador'
				&& $nombre_campo!='VarFiltros'
				&& $nombre_campo!='pagina'
				&& $nombre_campo!='opcion'
				&& $nombre_campo!='id'
				&& $nombre_campo!='elid'
				&& $nombre_campo!='filtro_empresa'
				&& $nombre_campo!='filtro_sucursal'
				&& $nombre_campo!='VBarraBotones'
				&& $nombre_campo!='MyFiltroBusqueda'
				&& $nombre_campo!='MyFiltersActivado'
				){
				$VarPost    .= $nombre_campo.':'.utf8_decode($valor).'{.}';
				$VarPostSql .= $nombre_campo.'{:}'.utf8_decode($valor).'{.}';
			}
		}
		$this->VarPost    = $VarPost;
		$this->VarPostSql = $VarPostSql;
		//echo '<script>console.log("'.$VarPost.'")< /script>';
	}

	//========================// METODO EDITAR LIKE CAMPO BUSQUEDA //========================//
	/*
		@campo  = Campo de filtro
	 	@tipo 	= Tipo de LIKE a apicar "RIGHT", "LEFT", "NONE".
	*/
	public function EditLike($campo,$tipo){
		$this->ArrayEditLike[$campo] = $tipo;
	}

	//*----------------------------------------------------------------------------------------------------------------------------------------------------------------------------*/
	/*Function 	AddFilter(
							"Nombre del Filtro",
							"Campo id para el filtro"
							"Campo nombre del filtro"
					   );
	*/
	public function AddFilter($nombre,$idCampo,$nombreCampo){
		$this->FilterNombre[$this->CuantosFilters] = $nombre;
		$this->FilterIdCampo[$this->CuantosFilters] = $idCampo;
		$this->FilterNombreCampo[$this->CuantosFilters] = $nombreCampo;
		$this->CuantosFilters++;
	}


	//*----------------------------------------------------------------------------------------------------------------------------------------------------------------------------*/
	/*Function 	AddValidation(
							"Campo que se valida",
							"validacion a aplicar",
							"Opcional Personaliza unico_global en una condicion sql" ->  sucursal=$id_sucursal AND ...
					    );
	*/
	//unico_global
	//email
	//numero
	//numero-real
	//numero-texto
	//fecha
	//minuscula
	//mayuscula

	public function AddValidation($campo,$validate,$sql=''){
		//if(is_array($validate)){

		//}else{
			$this->Validaciones[$this->CuantosValidations]           = $validate;
			$this->ValidacionesCampos[$this->CuantosValidations]     = $campo;
			//$this->TextValidacionesCampos[$this->CuantosValidations] = $textValidate;
			$this->CuantosValidations++;

            if($validate == 'unico_global'){
				$this->ValidacionGlobal      = 'true';
				$this->ValidacionGlobalField = $campo;

				if($sql!=''){ $sql = ' AND '.$sql; }
				$this->ValidacionGlobalSql   = $sql;
            }
            elseif($validate == 'email'){
				$this->ValidacionEmail      = 'true';
				$this->ValidacionEmailField = $campo;
            }

		//}
	}

	/*----------------------------------------------------------------------------------------------------------------------------------------------------------------------------*/
	/*Function 	AddBotton(
							"Texto del Boton",
							"Icono del Boton (css)"
							"Funcion del Boton"
					   );
	*/
	public function AddBotton($text,$icon,$function){
		$this->BottonText[$this->CuantosBottons] = $text;
		$this->BottonIcon[$this->CuantosBottons] = $icon;
		$this->BottonFunction[$this->CuantosBottons] = $function;
		$this->CuantosBottons++;
	}


	/*----------------------------------------------------------------------------------------------------------------------------------------------------------------------------*/
	/*Function 	AddMenuContext(
							"Texto del Boton",
							"Icono del Boton (css)"
							"Funcion del Boton"
					   );
	*/
	public function AddMenuContext($text,$icon,$function){
		$this->ContextMenuText[$this->CuantosContextMenu] = $text;
		$this->ContextMenuIcon[$this->CuantosContextMenu] = $icon;
		$this->ContextMenuFunction[$this->CuantosContextMenu] = $function;
		$this->CuantosContextMenu++;
	}


	/*----------------------------------------------------------------------------------------------------------------------------------------------------------------------------*/
	/*Function 	AddBottonVentana(
							"Texto del Boton",
							"Icono del Boton (css)"
							"Funcion del Boton"
					   );
	*/
	public function AddBottonVentana($text,$icon,$function,$insert='true',$update='true'){
		$this->BottonTextVentana[$this->CuantosBottonsVentana] = $text;
		$this->BottonIconVentana[$this->CuantosBottonsVentana] = $icon;
		$this->BottonFunctionVentana[$this->CuantosBottonsVentana] = $function;
		$this->BottonVentanaInsert[$this->CuantosBottonsVentana] = $insert;
		$this->BottonVentanaUpdate[$this->CuantosBottonsVentana] = $update;
		$this->CuantosBottonsVentana++;
	}

	/*----------------------------------------------------------------------------------------------------------------------------------------------------------------------------*/
	/*Function 	AddRow(
							"Titulo de la Celda",
							"Dato que se mostrara de la base de datos"
							"largo de la celda -> si se pone como valor 0 -> no muestra la celda"
							"invoca una funcion previamente creada en la clase"
					   );
	*/
	public function AddRow($titulo,$dato,$largo,$funcion=''){
		$this->ElTitulo[$this->CuantasRows] = $titulo;
		$this->ElDato[$this->CuantasRows] = $dato;
		$this->ElLargo[$this->CuantasRows] = $largo;
		$this->LaFuncion[$this->CuantasRows] = $funcion;
		//$this->EsVisible[$this->CuantasRows] = $esvisible;
		$this->CuantasRows++;
	}

	/*----------------------------------------------------------------------------------------------------------------------------------------------------------------------------*/
	/*Function 	AddSeparator(
							"Texto del Separador",
							"Icono del Separador (css)"
					   );
	*/
	public function AddSeparator($text,$icon=''){
		$this->FieldLabel[$this->CuantosFields] = $text;
		$this->FieldField[$this->CuantosFields] = $icon;
		$this->FieldLargo[$this->CuantosFields] = '';
		$this->FieldAlto[$this->CuantosFields] = '';
		$this->FieldObligatorio[$this->CuantosFields] = '';
		$this->FieldHidden[$this->CuantosFields] = '';
		$this->FieldHiddenValue[$this->CuantosFields] = '';
		$this->FieldTipo[$this->CuantosFields] = 'Separador';
		$this->FieldBd[$this->CuantosFields] = '';
		$this->FieldArray[$this->CuantosFields] = '';
		$this->FieldWhere[$this->CuantosFields] = '';
		$this->FieldDigitoVerificacion[$this->CuantosFields] = '';
		$this->CuantosFields++;
	}

	/*----------------------------------------------------------------------------------------------------------------------------------------------------------------------------*/
	/*Function AddTextField(
								"label del TextField",
								"Nombre del TextField en la Base de Datos"
								"Largo del TextField"
								"Si es Obligatorio (booleano)"
								"Si es Oculto (booleano)"
						  );
	*/
	public function AddTextField($label,$field,$largo,$obligatorio,$hidden='false',$hiddenValue='',$DigitoVerificacion='false'){
		$this->FieldLabel[$this->CuantosFields] = $label;
		$this->FieldField[$this->CuantosFields] = $field;
		$this->FieldLargo[$this->CuantosFields] = $largo;
		$this->FieldAlto[$this->CuantosFields] = '';
		$this->FieldObligatorio[$this->CuantosFields] = $obligatorio;
		$this->FieldHidden[$this->CuantosFields] = $hidden;
		$this->FieldHiddenValue[$this->CuantosFields] = $hiddenValue;
		$this->FieldTipo[$this->CuantosFields] = 'TextField';
		$this->FieldBd[$this->CuantosFields] = '';
		$this->FieldArray[$this->CuantosFields] = '';
		$this->FieldWhere[$this->CuantosFields] = '';
		$this->FieldDigitoVerificacion[$this->CuantosFields] = $DigitoVerificacion;
		$this->CuantosFields++;
	}

	/*----------------------------------------------------------------------------------------------------------------------------------------------------------------------------*/
	/*Function AddComboBox(
								"label del ComboBox",
								"Nombre del ComboBox en la Base de Datos"
								"Largo del ComboBox"
								"Si es Obligatorio (booleano)"
								"Si los datos del combo son de una Base de datos (booleano)"
								"array con los datos del combo"
						  );

	ARRAY EN MODO BD -> 'NOMBRE DE LA TABLA, NOMBRE DEL CAMPO INDEX, NOMBRE DEL CAMPO TEXTO ,SI MUESTRA SOLO LOS ACTIVOS(boolenao)'
	ARRAY EN MODO MANUAL ' INDEX : TEXTO , INDEX : TEXTO, INDEX : TEXTO, INDEX : TEXTO '
	*/
	public function AddComboBox($label,$field,$largo,$obligatorio,$bd='true',$array='',$where=''){
		$this->FieldLabel[$this->CuantosFields] = $label;
		$this->FieldField[$this->CuantosFields] = $field;
		$this->FieldLargo[$this->CuantosFields] = $largo;
		$this->FieldAlto[$this->CuantosFields] = '';
		$this->FieldObligatorio[$this->CuantosFields] = $obligatorio;
		$this->FieldHidden[$this->CuantosFields] = 'false';
		$this->FieldHiddenValue[$this->CuantosFields] = '';
		$this->FieldTipo[$this->CuantosFields] = 'ComboBox';
		$this->FieldBd[$this->CuantosFields] = $bd;
		$this->FieldArray[$this->CuantosFields] = $array;
		$this->FieldWhere[$this->CuantosFields] = $where;
		$this->FieldDigitoVerificacion[$this->CuantosFields] = '';
		$this->CuantosFields++;
	}

	/*----------------------------------------------------------------------------------------------------------------------------------------------------------------------------*/
	/*Function AddFields(
								"label del TextArea",
								"Nombre del TextArea en la Base de Datos"
								"Largo del TextArea"
								"Alto del TextArea
								"Si es Obligatorio (booleano)"
						  );
	*/
	public function AddTextArea($label,$field,$largo,$alto,$obligatorio){
		$this->FieldLabel[$this->CuantosFields] = $label;
		$this->FieldField[$this->CuantosFields] = $field;
		$this->FieldLargo[$this->CuantosFields] = $largo;
		$this->FieldAlto[$this->CuantosFields] = $alto;
		$this->FieldObligatorio[$this->CuantosFields] = $obligatorio;
		$this->FieldHidden[$this->CuantosFields] = 'false';
		$this->FieldHiddenValue[$this->CuantosFields] = '';
		$this->FieldTipo[$this->CuantosFields] = 'TextArea';
		$this->FieldBd[$this->CuantosFields] = '';
		$this->FieldArray[$this->CuantosFields] = '';
		$this->FieldWhere[$this->CuantosFields] = '';
		$this->FieldDigitoVerificacion[$this->CuantosFields] = '';
		$this->CuantosFields++;
	}

	/*----------------------------------------------------------------------------------------------------------------------------------------------------------------------------*/
	/*Function AddRowImage(
								"Titulo de la Celda",
								"codigo HTML de la imagen -> acepta funciones de javascript definidas fuera o dentro de la clase y variables de php definidas con AddRow()"
								"largo de la celda -> si se pone como valor 0 -> no muestra la celda"
								"invoca una funcion previamente creada en la clase"
						  );
	*/
	public function AddRowImage($titulo,$scriptImagen,$largo,$funcion=''){
		$this->ElTitulo[$this->CuantasRows] = $titulo;
		$this->ElDato[$this->CuantasRows] = false;
		$this->ElLargo[$this->CuantasRows] = $largo;
		$this->LaFuncion[$this->CuantasRows] = $funcion;
		$this->LaImagen[$this->CuantasRows] = $scriptImagen;
		$this->EsVisible[$this->CuantasRows] = $esvisible;
		$this->CuantasRows++;
	}

	/*----------------------------------------------------------------------------------------------------------------------------------------------------------------------------*/
	/*Function AddColStyle("campoMysql","css");*/

	public function AddColStyle($campoMysql,$styleCss){
		$this->colStyle[$campoMysql] = $styleCss;
	}

	/*----------------------------------------------------------------------------------------------------------------------------------------------------------------------------*/
	// FUNCION QUE LE DA FORMATOS A LOS CODIGOS RELLENADO CON CERO POR EJEMPLO  23 -> 0023
	public function Codigo($cadena){
		return str_pad($cadena, 4, "0", STR_PAD_LEFT);
	}

	/*----------------------------------------------------------------------------------------------------------------------------------------------------------------------------*/
	// FUNCION QUE LE DA FORMATO DE MONEDA
	public function Moneda($cadena){
		$sql   = "SELECT simbolo,decimales FROM configuracion_moneda WHERE predeterminado = 'true'";
		$query = mysql_query($sql,$this->Link);
		$row   = mysql_fetch_array($query);
		return $row['simbolo'].' '.number_format($cadena,$row['decimales']);
	}

	/*----------------------------------------------------------------------------------------------------------------------------------------------------------------------------*/
	// FUNCION QUE LE DA FORMATO DE MONEDA EN ERP
	public function MonedaErp($cadena){
		// $sql   = "SELECT simbolo,decimales FROM configuracion_moneda WHERE predeterminado = 'true'";
		// $query = mysql_query($sql,$this->Link);
		// $row   = mysql_fetch_array($query);
		return number_format($cadena,$_SESSION['DECIMALESMONEDA']);
	}

	public function MonedaAsientos($cadena){
		return number_format($cadena,2);
	}

	/*----------------------------------------------------------------------------------------------------------------------------------------------------------------------------*/
	// FUNCION QUE LE DA FORMATO DE FECHA
	public function Fecha($cadena){
		if($cadena == ''){ return ''; }

		list($date,$time) = split(" ",$cadena);
		list($aano,$mmes,$ddia) = split("-",$date);

		$ww    = date('w', mktime(0,0,0,date($mmes)  ,date($ddia) ,date($aano)));
		$dias  = array("Domingo","Lunes","Martes","Miercoles","Jueves","Viernes","Sabado");
		$meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");

		$resultado = $dias[$ww]." ".$ddia." ".$meses[$mmes-1]." de ".$aano;
		if($time != ''){ $resultado .= " - ".$time; }

		return $resultado;
	}

	/*----------------------------------------------------------------------------------------------------------------------------------------------------------------------------*/
	// FUNCION QUE ENCUENTRA VARIABLES EN UN STRING Y DEVUELVE UN ARRAY CON ESAS VARIABLES (LAS VARIABLES DEEN ESTAR DEFINIDAS EN EL STRING DE LA SIGUIENTE FORMA "[VARIABLE]")
	public function EncuentraVariablesCadena($mensaje){
		$resultado = array();
		$esta = stripos($mensaje,"[");
		if($esta !== false){
			$primera = explode("[",$mensaje);
			for($i=0;$i<count($primera);$i++){
				$esta2 = stripos($primera[$i],"]");
				if($esta2 !== false){
					$r = count($resultado);
					$segunda = explode("]",$primera[$i]);
					$resultado[$r] = $segunda[0];
				}
			}
		}
		return $resultado;
	}

	/*----------------------------------------------------------------------------------------------------------------------------------------------------------------------------*/
	// FUNCION QUE GENERA EL TOOLTIP $mensaje = MENSAJE QUE MOSTRARA EN EL TOOLTIP; $id = ID DE LA CAPA QUE RENDERIZA EL TOOLTIP
	public function CreateTooltipGeneral($mensaje,$id){

		$lafuncion = 	'
							<script>
								new Ext.ToolTip(
									{
										target		: "MuestraToltip_'.$id.'",
										anchor		: "left",
										dismissDelay: 60000,
										trackMouse	: true,
										minWidth	: 250,
										html		: "'.$mensaje.'",
									}
								);
							</script>
						';
		return $lafuncion;
	}

	/*----------------------------------------------------------------------------------------------------------------------------------------------------------------------------*/
	// FUNCION PRIVADA QUE CONVIERTE EL ARRAY DE LOS COMBOS MANUALES EN HTML
	private function GeneraComboTX($array){
		$campos = explode(',',$array);
		$contenido = array();
		$MySalida = '';
		for($i=0;$i<count($campos);$i++){
			$contenido[$i] = explode(':',$campos[$i]);
			$MySalida .= '<option value="'.$contenido[$i][0].'">'.$contenido[$i][1].'</option>';
		}
		return $MySalida;
	}

	/*----------------------------------------------------------------------------------------------------------------------------------------------------------------------------*/
	// FUNCION PRIVADA QUE CONVIERTE EL ARRAY DE LOS COMBOS DE BASE DE DATOS EN HTML
	private function GeneraComboBD($array,$where){

		$campos = explode(',',$array);

		if($where != ''){
			if($row[$campos[3]]=='true'){
				$mywhere = 'WHERE activo = 1 AND '.$where;
			}else{
				$mywhere = 'WHERE '.$where;
			}
		}else{
			if($row[$campos[3]]=='true'){
				$mywhere = 'WHERE activo = 1';
			}else{
				$mywhere = '';
			}
		}

		$SQL = "SELECT * FROM $campos[0] ".$mywhere;
		$consul = mysql_query($SQL,$this->Link);
		while($row = mysql_fetch_array($consul)){
			$MySalida .= '<option value="'.$row[$campos[1]].'">'.$row[$campos[2]].'</option>';
		}

		return $MySalida;

	}

	/*----------------------------------------------------------------------------------------------------------------------------------------------------------------------------*/
	//FUNCION QUE DETECTA LA CANTIDAD DE REGISTROS Y CREA LA BARRA DE PAGINACION
	public function CreatePaginacion($limite,$total){
		$limi = explode(',',$limite);
				$paginas = ceil($total/$limi[1]);
		if($paginas>1){
			$this->LlevaPaginacion = 'true';
			$this->LabelPaginacion = '<div style="float:right; margin:0 20px 0 0;">
										<div style="float:left; margin:2px 5px 0 5px">Pagina '.$this->PaginaActual.' de '.$paginas.'</div>
										<div class="my_first" onClick="pag_'.$this->GrillaName.'(\'first\')"></div>
										<div class="my_prev" onClick="pag_'.$this->GrillaName.'(\'prev\')"></div>
										<div class="my_next" onClick="pag_'.$this->GrillaName.'(\'next\')"></div>
										<div class="my_last" onClick="pag_'.$this->GrillaName.'(\'last\')"></div>
									  </div>';
			$this->MaxPage = $paginas;
			$newInicio = $limi[1] * ($this->PaginaActual - 1);
			$this->MySqlLimit= $newInicio.','.$limi[1];
			$this->NewAlto = $this->Alto-42;
		}else{
			$this->NewAlto = $this->Alto-23;
		}
	}

	/*----------------------------------------------------------------------------------------------------------------------------------------------------------------------------*/
	//FUNCION QUE GENERA MyGrilla
	public function GeneraGrilla(){
		$this->MyFiltroBusqueda = utf8_decode($this->MyFiltroBusqueda);
        if($this->condicional2 != ""){
            $ElCondicional = "";
			//echo base64_decode($this->condicional2);
            $NEWcondicional2 = explode("#",base64_decode($this->condicional2));
            for($i=0;$i<count($NEWcondicional2);$i++){
                $NEWcondi =  explode(",",$NEWcondicional2[$i]);
                if($NEWcondi[0]!='false'){$ElCondicional .= "AND ".$NEWcondi[0]."='".$NEWcondi[1]."' ";}
            }
            $this->condicional = $ElCondicional;
        }

		if($this->TableName2 == ''){$this->TableName2 = $this->TableName;}

		///////////////////////////////////////////////////////////////////////////////////////////////
		//FUNCION QUE COJE LAS VARIABLES DE POST Y LAS CONCATENA PARA ENVIARLAS POR PARAMS EN EL AJAX/
		$VariPost = explode("{.}",$this->VarPost);
		for($v=0;$v<count($VariPost);$v++){
			if($VariPost[$v]!=""){
				$VarsMyPost[$v]= explode(":",$VariPost[$v]);
			}
		}
		$VariablesPost = "";
		for($s=0;$s<count($VarsMyPost);$s++){
			$VariablesPost .= ','.$VarsMyPost[$s][0].':\''.$VarsMyPost[$s][1].'\'';
		}
		$this->VariablesPost = $VariablesPost;
		////////////////////////////////////////////////////////////////////////////////////////////////

		$ElFiltro = '';
		if($this->MyFiltroBusqueda != ''){
			$ElFiltro .= ' AND ( ';
			$CamposDeBusqueda = explode(',',$this->CamposBusqueda);
			for($cb=0;$cb<count($CamposDeBusqueda);$cb++){

				if($this->ArrayEditLike[$CamposDeBusqueda[$cb]]=='LEFT'){ $ElFiltro .= $CamposDeBusqueda[$cb].' LIKE \'%'.$this->MyFiltroBusqueda.'\''; }
				else if($this->ArrayEditLike[$CamposDeBusqueda[$cb]]=='RIGHT'){ $ElFiltro .= $CamposDeBusqueda[$cb].' LIKE \''.$this->MyFiltroBusqueda.'%\''; }
				else if($this->ArrayEditLike[$CamposDeBusqueda[$cb]]=='NONE'){ $ElFiltro .= $CamposDeBusqueda[$cb].' = \''.$this->MyFiltroBusqueda.'\''; }
				else{ $ElFiltro .= $CamposDeBusqueda[$cb].' LIKE \'%'.$this->MyFiltroBusqueda.'%\''; }

				if($cb<(count($CamposDeBusqueda)-1)){$ElFiltro .= ' OR ';}
			}

			$ElFiltro .= ' )';
		}

		if($this->GroupBy!=""){ $GroupBy=" GROUP BY ".$this->GroupBy; }else{ $GroupBy=""; }
		if($this->OrderBy!=""){ $OrderBy=" ORDER BY ".$this->OrderBy; }else{ $OrderBy=""; }

        $CondicionalFiltros = " ".$this->condicional;

		if($this->ConsulCustom == 'false'){
			$ConsultaCustom = "SELECT * FROM";
		}else{
			$ConsultaCustom = $this->ConsulCustom;
		}

		//AGREGARLE HAVING AL QUERY
		if($this->MysqlHaving == 'false'){
			$sqlHaving = "";
		}else{
			$sqlHaving = " HAVING ".$this->MysqlHaving;
		}

		if($this->TableName != ''){
			if($this->MyWhere != ''){$MyWhere = " WHERE ".$this->MyWhere;}else{$MyWhere = '';}
			$this->MySql = $ConsultaCustom." ".$this->TableName.$MyWhere.$CondicionalFiltros.$ElFiltro.$GroupBy.$sqlHaving.$OrderBy;
			$this->MySqlInUpDe =  "SELECT * FROM ".$this->TableName." WHERE id=".$this->VariableInUpDe;
			for($i=0;$i<$this->CuantosFilters;$i++){
				$siFilterGroupBy = ($this->GroupBy == "")? "" : $this->GroupBy.",";
				$this->MySqlFilter[$i] = "SELECT ".$this->FilterIdCampo[$i].",".$this->FilterNombreCampo[$i].",COUNT(".$this->FilterIdCampo[$i].") AS cuantos FROM ".$this->TableName.$MyWhere.$ElFiltro." GROUP BY ".$siFilterGroupBy.$this->FilterIdCampo[$i];
			}
		}

		////////////////////////////////////////////////////////////////////////////
		if($this->LaOpcion == 'false'){

				$consulT = mysql_query($this->MySql);
				$this->CreatePaginacion($this->MySqlLimit,mysql_num_rows($consulT));

				$consul  = mysql_query($this->MySql.' LIMIT '.$this->MySqlLimit);

				$count = 0;

				if($this->AddTooltipGeneral != ''){
					$array_variables = $this->EncuentraVariablesCadena($this->AddTooltipGeneral);
				}

				if($this->VBarraBotones == 'true'){
					echo '<div id="toolbar_'.$this->GrillaName.'" style="height:85px"></div>';

					echo'
						<script>
							new Ext.Panel
							(
								{
									renderTo	:\'toolbar_'.$this->GrillaName.'\',
									id			: \'ToolBar_'.$this->GrillaName.'\',
									frame		:false,
									border		:false,
									tbar		:
									[
										{
											xtype	: \'buttongroup\',
											columns	: 5,
											title	: \'Opciones\',
											items	:
											[
					';
					if($this->VBotonNuevo == 'true'){
						echo '					{
													xtype		: \'button\',
													id			: \'Btn_'.$this->GrillaName.'\',
													//id			: \'btn2\',
													text		: \''.$this->VBotonNText.'\',
													scale		: \'large\',
													iconCls		: \''.$this->VBotonNImage.'\',
													iconAlign	: \'top\',
													handler 	: function(){BloqBtn(this); Agregar_'.$this->GrillaName.'();}
												}
						';
					}
					for($b=0;$b<count($this->BottonText);$b++){
						if($this->VBotonNuevo == 'true' && count($this->BottonText)>0 && $b<1){echo ',';}
						echo '
								{
									xtype		: \'button\',
									minWidth	: 90,
									text		: \''.$this->BottonText[$b].'\',
									scale		: \'large\',
									iconCls		: \''.$this->BottonIcon[$b].'\',
									iconAlign	: \'top\',
									handler 	: function(){BloqBtn(this); '.$this->BottonFunction[$b].'}
								}
						';
						if($b<(count($this->BottonText)-1)){echo ',';}
					}


					echo'						]
										}
									]
								}
							);
						</script>
					';
				}

				$this->LargTotal = (COUNT($this->ElLargo)*3)+array_sum($this->ElLargo)+95;

				/////////////////////////////////////////////////////
				$this->ElAltoOriginal = $this->Alto;
				$this->ElQuitarAltoOriginal = $this->QuitarAlto;

				$cierraDiv='';
				//echo  $this->MyFiltersActivado;
				if ($this->MyFiltersActivado == 'false'){
					echo '<div id="ContenedorPrincipal_'.$this->GrillaName.'" style="float:left">';
					$cierraDiv='</div>';
				}
					if($this->Gfilters == 'true' && $this->Gtoolbar == 'true'  && $this->MyFiltersActivado == 'false'){
						echo '
							<div id="ContenedorFilters_'.$this->GrillaName.'" class="my_grilla_filters" style="width:20px; height:'.$this->Alto.'px">

								<div onclick="BarraFilters_'.$this->GrillaName.'()" class="TituloBarraFilters" style="">
									<div style="float:left; width:18px; height:18px; margin:0 0 0 2px" class="IconoFiltro"></div>
									<div style="float:left; width:160px; margin:0 0 0 0; text-align:center;"><b>Filtros de Busqueda</b></div>
									<div style="float:left; width:20px; height:18px" class="IconoAtras"></div>
								</div>
								<div id="BarraFiltersDespliegue_'.$this->GrillaName.'" class="BarraFiltersDespliegue" onclick="BarraFilters_'.$this->GrillaName.'()">
									<div class="BarraFiltersVerticalText"></div>
								</div>
								<div class="ListadoFilters">
							';

                        echo '<script>var ElFiltroBarraBusqueda_'.$this->GrillaName.' = new Array();</script>';

						for($i=0;$i<$this->CuantosFilters;$i++){
							$consul_filter = mysql_query($this->MySqlFilter[$i]);
							if(mysql_num_rows($consul_filter)>1){
                                echo '<script>ElFiltroBarraBusqueda_'.$this->GrillaName.'["'.$i.'"] = Array("false","false");</script>';
								echo '<div class="LineaFiltroTitulo">'.$this->FilterNombre[$i].'</div>';
								$countFil = 0;
								while($row_filter = mysql_fetch_array($consul_filter)){
									echo '<div id="ElFilter_'.str_replace(' ', '',$this->FilterNombre[$i]).$countFil.'_'.$this->GrillaName.'" class="LineaFiltro" name="ElFilter_'.str_replace(' ', '',$this->FilterNombre[$i]).'_'.$this->GrillaName.'" onclick="SeleccionaFiltro_'.$this->GrillaName.'(\''.str_replace(' ', '',$this->FilterNombre[$i]).'\',this.id,\''.$this->FilterIdCampo[$i].'\',\''.$row_filter[$this->FilterIdCampo[$i]].'\','.$i.')">'.$row_filter[$this->FilterNombreCampo[$i]].'&nbsp;<span style="color:#69C; font-weight:normal; font-size:10px">('.$row_filter['cuantos'].')</span></div>';
									$countFil++;
								}
							}
						}


						echo $cierraDiv.'

							</div>

							<script>


								function SeleccionaFiltro_'.$this->GrillaName.'(cual,id,cualId,cualValor,IdFiltro){
									var este = "ElFilter_"+cual+"_'.$this->GrillaName.'";
									var cuales = document.getElementsByName(este);

									for(r=0;r<cuales.length;r++){

										if(cuales[r].id == id){
											document.getElementById(cuales[r].id).className = "LineaFiltro LineaFiltroEnabled";
											document.getElementById(cuales[r].id).onclick   = function(){CierraFiltro_'.$this->GrillaName.'(cual,id,cualId,cualValor,IdFiltro);};
											ElFiltroBarraBusqueda_'.$this->GrillaName.'[IdFiltro] =  Array(cualId,cualValor);
                                            MyFilters'.$this->GrillaName.'();
										}else{
											document.getElementById(cuales[r].id).className = "LineaFiltro LineaFiltroDisabled";
										}
									}
								}

								function CierraFiltro_'.$this->GrillaName.'(cual,id,cualId,cualValor,IdFiltro){
                                    var este2 = "ElFilter_"+cual+"_'.$this->GrillaName.'";
									var cuales2 = document.getElementsByName(este2);

                                    var habilito = "true";

                                    for(p=0;p<cuales2.length;p++){

                                        if(cuales2[p].id == id){
                                            if(document.getElementById(cuales2[p].id).className == "LineaFiltro LineaFiltroDisabled" || document.getElementById(cuales2[p].id).className == "LineaFiltro"){
                                                SeleccionaFiltro_'.$this->GrillaName.'(cual,id,cualId,cualValor,IdFiltro)
                                                habilito = "false";
                                            }else{
										        document.getElementById(cuales2[p].id).className = "LineaFiltro";
										        document.getElementById(cuales2[p].id).onclick   = function(){SeleccionaFiltro_'.$this->GrillaName.'(cual,id,cualId,cualValor,IdFiltro);};
										        ElFiltroBarraBusqueda_'.$this->GrillaName.'[IdFiltro] =  Array("false","false");
                                                //console.log(cuales2[p].id);
                                                MyFilters'.$this->GrillaName.'();
                                            }
                                        }
									}

                                    if(habilito == "true"){
                                        for(p=0;p<cuales2.length;p++){
                                            if(cuales2[p].id != id){
                                             document.getElementById(cuales2[p].id).className = "LineaFiltro";
                                            }
                                        }
                                    }

								}

								function MyFilters'.$this->GrillaName.'(){

                                    var ElFiltro = "";
                                    for(s=0;s<ElFiltroBarraBusqueda_'.$this->GrillaName.'.length;s++){
                                    	if(typeof(ElFiltroBarraBusqueda_'.$this->GrillaName.'[s]) == "undefined") continue;

                                        ElFiltro += ElFiltroBarraBusqueda_'.$this->GrillaName.'[s][0]+","+ElFiltroBarraBusqueda_'.$this->GrillaName.'[s][1];
                                        if(s<(ElFiltroBarraBusqueda_'.$this->GrillaName.'.length-1)){ElFiltro += "#";}
                                    }
                                    ElFiltro = Base64.encode(ElFiltro);

                                    var MyFiltroBusqueda	= document.getElementById(\'FieldBusqueda'.$this->GrillaName.'\').value;
									var MyFiltersActivado	= \'true\';
									var filtro_empresa 		= \''.$this->FiltroEmpresa.'\';
									var filtro_sucursal 	= \''.$this->FiltroSucursal.'\';
									var elid 				= \''.$this->VariableInUpDe.'\';
									Ext.get("DIV_contenedor_'.$this->GrillaName.'").load(
										{
											url		:\''.$_SERVER['SCRIPT_NAME'].'\',
											scripts	:true,
											nocache	:true,
											params	:
												{
													VBarraBotones 		: 	\'false\',
													MyFiltersActivado	:	MyFiltersActivado,
													MyFiltroBusqueda	:	MyFiltroBusqueda,
													filtro_empresa		:	filtro_empresa,
													filtro_sucursal		:	filtro_sucursal,
													elid 				:   elid,
                                                    condicional2        :   ElFiltro
													'.$this->VariablesPost.'
												}
										}
									);
									//MyLoading2(\'on\');
								}

								function BarraFilters_'.$this->GrillaName.'(){

									var TamanoFilters = document.getElementById("ContenedorFilters_'.$this->GrillaName.'").style.width;
									TamanoFilters = parseInt(TamanoFilters.replace("px",""));

									var TamanoGrilla = document.getElementById("DIV_contenedor_'.$this->GrillaName.'").style.width;
									TamanoGrilla = parseInt(TamanoGrilla.replace("px",""));


									if(TamanoFilters == 20){
										setTimeout(function(){document.getElementById("DIV_toolbar_'.$this->GrillaName.'").style.width = TamanoGrilla - 180;},100);
										setTimeout(function(){document.getElementById("DIV_contenedor_'.$this->GrillaName.'").style.width = TamanoGrilla - 180;},100);
										document.getElementById("ContenedorFilters_'.$this->GrillaName.'").style.width = 200;
										document.getElementById("BarraFiltersDespliegue_'.$this->GrillaName.'").className = "BarraFiltersDespliegueDisabled";
									    document.getElementById("DIV_listado_'.$this->GrillaName.'").style.width = "100%";
                                        document.getElementById("DIV_titulo_'.$this->GrillaName.'").style.width = "100%";
										//console.log("abierta");
                                    }

									if(TamanoFilters == 200){
										setTimeout(function(){document.getElementById("DIV_toolbar_'.$this->GrillaName.'").style.width = TamanoGrilla + 180;},100);
										setTimeout(function(){document.getElementById("DIV_contenedor_'.$this->GrillaName.'").style.width = TamanoGrilla + 180;},100);
										document.getElementById("ContenedorFilters_'.$this->GrillaName.'").style.width = 20;
										document.getElementById("BarraFiltersDespliegue_'.$this->GrillaName.'").className = "BarraFiltersDespliegue";
                                        document.getElementById("DIV_listado_'.$this->GrillaName.'").style.width = "100%";
                                        document.getElementById("DIV_titulo_'.$this->GrillaName.'").style.width = "100%";
										//console.log("cerrada");
									}
								}

							</script>
						';
						//////////////////////////////////////////////
						$this->Ancho = $this->Ancho-30;
						if($this->QuitarAncho > 0){$this->QuitarAncho = $this->QuitarAncho + 25;}
						/////////////////////////////////////////////
					}

					if($this->Gfilters == 'true' && $this->Gtoolbar == 'true'  && $this->MyFiltersActivado == 'true'){
						//////////////////////////////////////////////
						$this->Ancho = $this->Ancho-30;
						if($this->QuitarAncho > 0){$this->QuitarAncho = $this->QuitarAncho + 25;}
						/////////////////////////////////////////////

						//////////////////////////////////////////////
						$this->NewAlto = $this->NewAlto-35;
						$this->Alto = $this->Alto-35;
						$this->QuitarAlto = $this->QuitarAlto+30;
						/////////////////////////////////////////////
					}

					if($this->Gtoolbar == 'true' && $this->MyFiltersActivado == 'false' ){
						echo '
								<div id="DIV_toolbar_'.$this->GrillaName.'" class="my_grilla_toolbar" style="float:left; overflow:hidden;">
									<div style="float:left; margin:9px 0 0 9px">
										<input class="myfieldBusqueda" name="FieldBusqueda'.$this->GrillaName.'" type="text" id="FieldBusqueda'.$this->GrillaName.'" style="width:209px" onKeyUp="ValEnterBusq'.$this->GrillaName.'(event)"  />
									</div>
									<div style="float:left; margin:10px 0 0 13px; width:20px; height:20px; cursor:pointer;" class="reload" onClick="MyBusqueda'.$this->GrillaName.'()"></div>
								</div>
							';

						//////////////////////////////////////////////
						$this->NewAlto = $this->NewAlto-35;
						$this->Alto = $this->Alto-35;
						$this->QuitarAlto = $this->QuitarAlto+30;
						/////////////////////////////////////////////

						echo '
							<script>
								function ValEnterBusq'.$this->GrillaName.'(e) {//FUNCIONES QUE VALIDAN EL ENTER E INVOCA LA BUSQUEDA DE EMPLEADOS
									tecla = (document.all)?e.keyCode:e.which;
									if (tecla==13){MyBusqueda'.$this->GrillaName.'();}
									return true;
								}

								function MyBusqueda'.$this->GrillaName.'(){

									var MyParent 			= document.getElementById(\'DIV_contenedor_'.$this->GrillaName.'\').parentNode;
									var MyFiltroBusqueda	= document.getElementById(\'FieldBusqueda'.$this->GrillaName.'\').value;
									//var MyFiltersActivado	= \'true\';
									var filtro_empresa 		= \''.$this->FiltroEmpresa.'\';
									var filtro_sucursal 	= \''.$this->FiltroSucursal.'\';
									var elid 				= \''.$this->VariableInUpDe.'\';
									Ext.get(MyParent).load(
									//Ext.get("DIV_contenedor_'.$this->GrillaName.'").load(

										{
											url		: \''.$_SERVER['SCRIPT_NAME'].'\',
											scripts	:true,
											nocache	:true,
											params	:
												{
													VBarraBotones 		: 	\'false\',
													//MyFiltersActivado	:	MyFiltersActivado,
													MyFiltroBusqueda	:	MyFiltroBusqueda,
													filtro_empresa		:	filtro_empresa,
													filtro_sucursal		:	filtro_sucursal,
													elid 				:   elid
													'.$this->VariablesPost.'
												}
										}
									);
									//MyLoading2(\'on\');
								}
								var MyFiltroBusqueda2 = \''.$this->MyFiltroBusqueda.'\';
								if(MyFiltroBusqueda2 != \'\'){
									document.getElementById(\'FieldBusqueda'.$this->GrillaName.'\').focus();
									document.getElementById(\'FieldBusqueda'.$this->GrillaName.'\').value = MyFiltroBusqueda2;

								}

							</script>
						';
					}
					///////////////////////////////////////////////////////////////////

				if($this->MyFiltersActivado == 'false'){
					echo '<div id="DIV_contenedor_'.$this->GrillaName.'" class="my_grilla_contenedor" style="width:'.$this->Ancho.'px; height:'.$this->Alto.'px" >';
				}
					echo '	<div id="DIV_titulo_'.$this->GrillaName.'" style="float:left; background-color:#EEEEEE; overflow:hidden;">';
					echo '		<div style="float:left; min-width:'.$this->LargTotal.'px; width:100%">';
					echo '			<div class="my_grilla_cabezera" style="float:left; width:30px;  ">No.</div>';
										for($i=0;$i<count($this->ElTitulo);$i++){
											if($this->ElLargo[$i] > 0){
												echo '<div class="my_grilla_cabezera" style="float:left; width:'.$this->ElLargo[$i].'px; ">'.$this->ElTitulo[$i].'</div>';
											}
										}
					echo '		</div>';
					echo '	</div>';
					echo '	<div id="DIV_listado_'.$this->GrillaName.'" style="float:left; overflow:auto; width:100%; background-color:#FFFFFF; height:'.$this->NewAlto.'px">';


					while($row = mysql_fetch_array($consul)){

							//SI EL PARAMETRO DEL TOOLTIP ES DIFERENTE A "" ENTONCES VERIFICA EL CAMBIO DE VARIABLES [VARIABLE]
							if($this->AddTooltipGeneral != ''){
								$mensaje = $this->AddTooltipGeneral;
								//if(in_array($this->ElDato[$i],$array_variables,true)){
								for($h=0;$h<count($array_variables);$h++){
									//$mensaje = str_replace("[".$this->ElDato[$i]."]",$Dato,$mensaje);
									$mensaje = str_replace("[".$array_variables[$h]."]",$row[$array_variables[$h]],$mensaje);
								}
							}



							$count = $count + 1;
							echo '<div class="my_grilla_celdas2" id="item_'.$this->GrillaName.'_'.$row['id'].'" divid="'.$row['id'].'" style="float:left; min-width:'.$this->LargTotal.'px; width:100%" >';
							echo '	<div id="MuestraToltip_General_'.$this->GrillaName.'_'.$row['id'].'" ondblclick="MyEditar_'.$this->GrillaName.'(\''.$row['id'].'\',\''.$count.'\')">';
							echo '	<div id="MuestraToltip_'.$this->GrillaName.'_'.$row['id'].'" class="my_grilla_columna" style="float:left; width:30px;">'.$count.'</div>';

										for($i=0;$i<count($this->ElTitulo);$i++){

											//VERIFICA SI HAY FUNCIONES DE FORMATOS DE DATOS.
											if($this->LaFuncion[$i]!=""){

												switch($this->LaFuncion[$i]){
													case "codigo":
													$Dato = $this->Codigo($row[$this->ElDato[$i]]);
													break;

													case "moneda":
													$Dato = $this->Moneda($row[$this->ElDato[$i]]);
													break;

													case "MonedaErp":
													$Dato = $this->MonedaErp($row[$this->ElDato[$i]]);
													break;

													case "MonedaAsientos":
													$Dato = $this->MonedaAsientos($row[$this->ElDato[$i]]);
													break;

													case "fecha":
													$Dato = $this->Fecha($row[$this->ElDato[$i]]);
													break;
												}

											}else{

												if($this->ElDato[$i]!=false){
													$Dato = $row[$this->ElDato[$i]];
												}else{
													$array_variables_imagen = $this->EncuentraVariablesCadena($this->LaImagen[$i]);
													$laimagen = $this->LaImagen[$i];
													for($h=0;$h<count($array_variables_imagen);$h++){
														$laimagen = str_replace("[".$array_variables_imagen[$h]."]",$row[$array_variables_imagen[$h]],$laimagen);
													}
													$Dato = $laimagen;
												}
											}
											//SI EL LARGO ES DIFERENTE A "0" ENTONCES IMPRIME LA CELDA
											if($this->ElLargo[$i] > 0){
												$MyLarge = $this->ElLargo[$i]+1;
												echo '<div id="div_'.$this->GrillaName.'_'.$this->ElDato[$i].'_'.$row['id'].'" class="my_grilla_celdas" style="float:left; width:'.$MyLarge.'px;'.$this->colStyle[$this->ElDato[$i]].'">'.$Dato.'</div>';
											}
										}

							echo '	</div>';
							echo '</div>';

							if($this->AddTooltipGeneral != ''){
								echo $this->CreateTooltipGeneral($mensaje,'General_'.$this->GrillaName.'_'.$row['id']);
							}

							if($this->MenuContextEliminar == 'true'){$MCEliminar = '';}else{$MCEliminar = '//';}
							if($this->MenuContext == 'true'){
								echo 	"<script>
											Ext.get('item_".$this->GrillaName."_".$row['id']."').on('contextmenu', function(eventObj, elRef_".$this->GrillaName."_".$row['id'].")
												{
													eventObj.stopEvent();

													var divid = document.getElementById(\"item_".$this->GrillaName."_".$row['id']."\").getAttribute('divid');

													if (!this.ctxMenu) {
														this.ctxMenu = new Ext.menu.Menu(
															{
																items :
																[
																	".$MCEliminar."{
																	".$MCEliminar."	text 	: 'Eliminar',
																	".$MCEliminar."	iconCls : 'delete',
																	".$MCEliminar."	handler : function(){
																	".$MCEliminar."				elimina_desde_contextmenu_".$this->GrillaName."(divid);
																	".$MCEliminar."			}
																	".$MCEliminar."},
																	'-'";

																	if($this->CuantosContextMenu > 0){
																		for($i=0;$i<$this->CuantosContextMenu;$i++){
																			$array_variables_context_menu = $this->EncuentraVariablesCadena($this->ContextMenuFunction[$i]);
																			$lafuncion_context = $this->ContextMenuFunction[$i];
																			for($h=0;$h<count($array_variables_context_menu);$h++){
																				$lafuncion_context = str_replace("[".$array_variables_context_menu[$h]."]",addslashes($row[$array_variables_context_menu[$h]]),$lafuncion_context);
																			}


																			echo '
																				,{
																					text 	: \''.$this->ContextMenuText[$i].'\',
																					iconCls : \''.$this->ContextMenuIcon[$i].'\',
																					handler : function(){
																								'.$lafuncion_context.'
																							}
																				}
																			';
																		}
																	}

								echo "							]
															}
														);
													}
													this.ctxMenu.show(elRef_".$this->GrillaName."_".$row['id'].");
												}
											);
										</script>
										";
							}


					}

					echo '		<div id="Recibidor_Celda_'.$this->GrillaName.$count,'"></div>';
					echo '	</div>';
					echo '	<div class="my_grilla_cabezera" style="float:left; width:100%;  ">'.$this->LabelPaginacion.'</div>';
				if($this->MyFiltersActivado == 'false'){
					echo '</div>';
				}
				echo '</div>';

				echo '<script>';

				echo '	var Contador_'.$this->GrillaName.' = '.$count.';';

				//if($this->AutoResize=='true'){

				if($this->QuitarAncho == 0){
					$MyGrillaAncho = $this->Ancho;
					$AnchoAuto = 'false';
				}else{
					$MyGrillaAncho = $this->QuitarAncho;
					$AnchoAuto = 'true';
				}
				if($this->ElQuitarAltoOriginal  == 0){
					$MyGrillaAlto  = $this->Alto;
					$AltoAuto = 'false';
				}else{
					$MyGrillaAlto  = $this->QuitarAlto;
					$AltoAuto = 'true';
				}

					echo '	MyResizeGrilla(\''.$this->GrillaName.'\','.$MyGrillaAncho.','.$MyGrillaAlto; if($this->LlevaPaginacion=='true'){echo ',\'true\'';}else{echo ',\'false\'';} /*if($this->Gtoolbar == 'true'){echo ',\'true\'';}else{echo ',\'false\''; }*/echo ','.$AnchoAuto.','.$AltoAuto.');';
					echo '  if(VerificaArray(\''.$this->GrillaName.'\')==\'false\'){// FUNCION QUE AGREGA AL WINDOWS.ONLOAD EL AUTORESIZE DE LAS GRILLA
								OnResizeList[OnResizeList.length] = \''.$this->GrillaName.'\';
								OnResizeAncho[OnResizeAncho.length] = \''.$MyGrillaAncho.'\';
								OnResizeAlto[OnResizeAlto.length] = \''.$MyGrillaAlto.'\';
								OnResizeAnchoAuto[OnResizeAnchoAuto.length] = \''.$AnchoAuto.'\';
								OnResizeAltoAuto[OnResizeAltoAuto.length] = \''.$AltoAuto.'\';
							}


                            // FUNCION PARA SCROLL
                            function calculaScroll_'.$this->GrillaName.'(){
                                var hscroll = document.getElementById("DIV_listado_'.$this->GrillaName.'").scrollLeft;
                                document.getElementById("DIV_titulo_'.$this->GrillaName.'").scrollLeft = hscroll;
                            }
                            document.getElementById("DIV_listado_'.$this->GrillaName.'").onscroll = calculaScroll_'.$this->GrillaName.';


						  ';
				//}

				echo '	var No_Divs_'.$this->GrillaName.' = '.$count.';
						function Inserta_Div_'.$this->GrillaName.'(elid){
							Ext.get(\'Recibidor_Celda_'.$this->GrillaName.'\'+No_Divs_'.$this->GrillaName.').load(
								{
									url		: "'.$_SERVER['SCRIPT_NAME'].'",
									timeout : 180000,
									scripts	: true,
									nocache	: true	,
									params	:
										{
											elid	:	elid,
											opcion	:	"insert",
											contador:	Contador_'.$this->GrillaName.'
											'.$this->VariablesPost.'
										}
								}
							);
							No_Divs_'.$this->GrillaName.' ++;
						}';

				echo "
						function elimina_desde_contextmenu_".$this->GrillaName."(divid){

							function EliminaRegistroSeleccionado(btn){
								if(btn == 'yes'){
									var opcion = 'EliminaBD';

									Ext.Ajax.request({
										url     : '".$_SERVER['SCRIPT_NAME']."',
										method  : 'post',
										params  :
										{
											opcion          :   opcion,
											id              :   divid
										},
										success : function (result, request)
										{
											var resultado =  result.responseText.split('{.}');
											var respuesta = resultado[1];
											var resp      = resultado[2];
											var resp2     = resultado[3];

											if(respuesta == 'false'){
												alert(resp+'\\n\\n'+resp2);
											}
											else if(respuesta == 'trueSQL'){
												alert('Error, Existe informacion asociadas a este registro');
											}
											else{
												MyLoading();
												Elimina_Div_".$this->GrillaName."(resp);
											}
										}
									});
								}
							}


							Ext.MessageBox.buttonText.yes = 'SI';
							Ext.MessageBox.buttonText.no  = 'NO';
							Ext.Msg.show
							(
								{
									title       :   'Eliminar Registro',
									msg         :   '<br />Seguro que desea eliminar este Registro?<br />',
									buttons     :   Ext.Msg.YESNO,
									icon        :   Ext.MessageBox.WARNING,
									fn          :   EliminaRegistroSeleccionado
								}
							);
						}

					 ";

				echo '	function Actualiza_Div_'.$this->GrillaName.'(elid,contador){
							var NameDiv = "item_'.$this->GrillaName.'_"+elid;

							if(document.getElementById("MuestraToltip_'.$this->GrillaName.'_"+elid) && (isNaN(contador) || contador==0)){
								contador = document.getElementById("MuestraToltip_'.$this->GrillaName.'_"+elid).innerHTML;
							}

							if(document.getElementById(NameDiv)){
								Ext.get(NameDiv).load(
									{
										url		: "'.$_SERVER['SCRIPT_NAME'].'",
										timeout : 180000,
										scripts	: true,
										nocache	: true,
										params	:
											{
												elid	:	elid,
												contador:	contador,
												opcion	:	"update"
												'.$this->VariablesPost.'
											}
									}
								);
							}
							else{ console.log("Error "+NameDiv+" No Encontrado!"); }
						}';

				echo '	function Elimina_Div_'.$this->GrillaName.'(elid){
							Ext.get(\'item_'.$this->GrillaName.'_\'+elid).load(
								{
									url		: "'.$_SERVER['SCRIPT_NAME'].'",
									timeout : 180000,
									scripts	: true,
									nocache	: true,
									params	:
										{
											elid	:	elid,
											opcion	:	"delete"
										}
								}
							);
						}';

				echo ' function MyEditar_'.$this->GrillaName.'(id,contador){
							if(typeof (Editar_'.$this->GrillaName.') == \'function\') {
								Editar_'.$this->GrillaName.'(id,contador);
							}else{
								alert(\'la funcion javascript "Editar_'.$this->GrillaName.'()" no esta definida!\');
							}

					   }';

				/*if($this->VarFiltros != 'false'){
					$array_filters = explode(',',$this->VarFiltros);
					$varfiltros = '';
					for($n=0;$n<count($array_filters);$n++){
						$varfiltros .= ','.$array_filters[$n];
					}
				}*/

				echo ' function pag_'.$this->GrillaName.'(accion){
					   		//MyLoading2(\'on\');
							var PaginaActual = '.$this->PaginaActual .';
							var MaxPage      = '.$this->MaxPage.';
							var filtro_empresa 		= \''.$this->FiltroEmpresa.'\';
							var filtro_sucursal 	= \''.$this->FiltroSucursal.'\';
							var MyFiltroBusqueda	= \''.$this->MyFiltroBusqueda.'\';
							var elid 				= \''.$this->VariableInUpDe.'\';
							var MyParent 	 = document.getElementById(\'DIV_contenedor_'.$this->GrillaName.'\').parentNode;

							if(accion==\'first\'){
								var pagina = 1;
								if(PaginaActual!=1){
										Ext.get(MyParent).load(
											{
												url		: "'.$_SERVER['SCRIPT_NAME'].'",
												scripts	: true,
												nocache	: true,
												params	:
													{
														VBarraBotones 		: 	\'false\',
														filtro_empresa		:	filtro_empresa,
														filtro_sucursal		:	filtro_sucursal,
														MyFiltroBusqueda	:	MyFiltroBusqueda,
														elid 				:   elid,
														pagina 				: 	pagina
														'.$this->VariablesPost.'
													}
											}
										);
								}
							}

							if(accion==\'prev\'){
								var pagina = PaginaActual-1;
								if(PaginaActual!=1){
										Ext.get(MyParent).load(
											{
												url		: "'.$_SERVER['SCRIPT_NAME'].'",
												scripts	: true,
												nocache	: true,
												params	:
													{
														VBarraBotones 		: 	\'false\',
														filtro_empresa		:	filtro_empresa,
														filtro_sucursal		:	filtro_sucursal,
														MyFiltroBusqueda	:	MyFiltroBusqueda,
														elid 				:   elid,
														pagina 				: 	pagina
														'.$this->VariablesPost.'
													}
											}
										);
								}
							}
							if(accion==\'next\'){
								var pagina = PaginaActual+1;
								if(PaginaActual!=MaxPage){
									Ext.get(MyParent).load(
										{
											url		: "'.$_SERVER['SCRIPT_NAME'].'",
											scripts	: true,
											nocache	: true,
											params	:
												{
														VBarraBotones 		: 	\'false\',
														filtro_empresa		:	filtro_empresa,
														filtro_sucursal		:	filtro_sucursal,
														MyFiltroBusqueda	:	MyFiltroBusqueda,
														elid 				:   elid,
														pagina 				: 	pagina
														'.$this->VariablesPost.'
												}
										}
									);
								}
							}

							if(accion==\'last\'){
								var pagina = MaxPage;
								if(PaginaActual!=MaxPage){
									Ext.get(MyParent).load(
										{
											url		: "'.$_SERVER['SCRIPT_NAME'].'",
											scripts	: true,
											nocache	: true,
											params	:
												{
														VBarraBotones 		: 	\'false\',
														filtro_empresa		:	filtro_empresa,
														filtro_sucursal		:	filtro_sucursal,
														MyFiltroBusqueda	:	MyFiltroBusqueda,
														elid 				:   elid,
														pagina 				: 	pagina
														'.$this->VariablesPost.'
												}
										}
									);
								}
							}
					   }';


				if($this->VentanaAuto == 'true'){
					/*----------------------------------------------------------------------------------*/
					echo '
					function Editar_'.$this->GrillaName.'(elid,contador){
						var myalto  = Ext.getBody().getHeight();
						var myancho  = Ext.getBody().getWidth();
						Win_Editar_'.$this->GrillaName.' = new Ext.Window
						(
							{
					';
					//if($this->VAutoResize == 'true'){
						if($this->VAncho == 0){
							echo 'width	    : myancho - '.$this->VQuitarAncho.', ';
						}else{
							echo 'width	    : '.$this->VAncho.', ';
						}
						if($this->VAlto ==0 ){
							echo 'height	: myalto - '.$this->VQuitarAlto.',';
						}else{
							echo 'height	: '.$this->VAlto.',';
						}
					//}else{
					//	echo 'width	: '.$this->VAncho.', height	: '.$this->VAlto.',';
					//}

					echo'
								id			: \'Win_Editar_'.$this->TituloVentana.'\',
								title		: \''.$this->TituloVentana.'\',
								modal		: true,
								autoScroll	: '.$this->VAutoScroll.',
								autoDestroy : true,
								autoLoad	:
								{
									url		:\''.$_SERVER['SCRIPT_NAME'].'\',
									scripts	:true,
									nocache	:true,
									params	:
											{
												id				: 	elid,
												opcion			:	\'Vupdate\',
												contador		:	contador,
												filtro_empresa	:	\''.$this->FiltroEmpresa.'\',
												filtro_sucursal	:	\''.$this->FiltroSucursal.'\'
												'.$this->VariablesPost.'
											}
								},

								tbar		:
								[
									{
										xtype		: \'button\',
										id			: \'BtnV_'.$this->GrillaName.'\',
										//id			: \'btn2\',
										text		: \'Actualizar\',
										scale		: \'large\',
										iconCls		: \'guardar\',
										iconAlign	: \'left\',
										handler 	: function(){guarda'.$this->GrillaName.'();}
									}
							';

							if($this->VBotonEliminar == 'true'){
								echo '			,
												{
													xtype		: \'button\',
													id			: \'BtnV_eliminar_'.$this->GrillaName.'\',
													//id			: \'btn2\',
													text		: \'Eliminar\',
													scale		: \'large\',
													iconCls		: \'eliminar\',
													iconAlign	: \'left\',
													handler 	: function(){BloqBtn(this); elimina'.$this->GrillaName.'();}
												}
								';
							}

							if($this->CuantosBottonsVentana > 0){
								for($i=0;$i<$this->CuantosBottonsVentana;$i++){
									if($this->BottonVentanaUpdate[$i] == 'true'){
										echo '			,
														{
															xtype		: \'button\',
															id			: \'BtnV'.$i.'_'.$this->GrillaName.'\',
															text		: \''.$this->BottonTextVentana[$i].'\',
															scale		: \'large\',
															iconCls		: \''.$this->BottonIconVentana[$i].'\',
															iconAlign	: \'left\',
															handler 	: function(){BloqBtn(this); '.$this->BottonFunctionVentana[$i].'}
														}
										';
									}
								}
							}

							echo'
								]
							}
						).show();
					}
					';


					/*----------------------------------------------------------------------------------*/
					echo '
					function Agregar_'.$this->GrillaName.'(elid){
						var myalto  = Ext.getBody().getHeight();
						var myancho  = Ext.getBody().getWidth();
						Win_Agregar_'.$this->GrillaName.' = new Ext.Window
						(
							{
					';

                    //if($this->VAutoResize == 'true'){
					if($this->VAncho == 0){
						echo 'width     : myancho - '.$this->VQuitarAncho.', ';
					}else{
						echo 'width     : '.$this->VAncho.', ';
					}
					if($this->VAlto ==0 ){
						echo 'height    : myalto - '.$this->VQuitarAlto.',';
					}else{
						echo 'height    : '.$this->VAlto.',';
					}
                    //}else{
                   //     echo 'width : '.$this->VAncho.', height : '.$this->VAlto.',';
                   // }

					echo'
								id			: \'Win_Agregar_'.$this->TituloVentana.'\',
								title		: \''.$this->TituloVentana.'\',
								modal		: true,
								autoScroll	: '.$this->VAutoScroll.',
								autoDestroy : true,
								autoLoad	:
								{
									url		:\''.$_SERVER['SCRIPT_NAME'].'\',
									scripts	:true,
									nocache	:true,
									params	:
											{
												elid			: 	\''.$this->VariableInUpDe.'\',
												opcion			:	\'Vagregar\',
												filtro_empresa	:	\''.$this->FiltroEmpresa.'\',
												filtro_sucursal	:	\''.$this->FiltroSucursal.'\'
												'.$this->VariablesPost.'
											}
								},
								tbar		:
								[
									{
										xtype		: \'button\',
										id			: \'BtnV_'.$this->GrillaName.'\',
										//id			: \'btn2\',
										text		: \'Guardar\',
										scale		: \'large\',
										iconCls		: \'guardar\',
										iconAlign	: \'left\',
										handler 	: function(){BloqBtn(this); guarda'.$this->GrillaName.'();}
									}
								';
								if($this->CuantosBottonsVentana > 0){
									for($i=0;$i<$this->CuantosBottonsVentana;$i++){
										if($this->BottonVentanaInsert[$i] == 'true'){
											echo '			,
															{
																xtype		: \'button\',
																id			: \'BtnV'.$i.'_'.$this->GrillaName.'\',
																text		: \''.$this->BottonTextVentana[$i].'\',
																scale		: \'large\',
																iconCls		: \''.$this->BottonIconVentana[$i].'\',
																iconAlign	: \'left\',
																handler 	: function(){BloqBtn(this); '.$this->BottonFunctionVentana[$i].'}
															}
											';
										}
									}
								}

						echo   ']
							}
						).show();
					}
					';

				}
				echo 'var modf = document.getElementsByClassName("x-window-body");for(var i=0;i<modf.length;i++){var WInicial=modf[i].style.width.split("px")[0]-1;modf[i].style.width=WInicial+"px";}';

				echo '</script>';

				if($this->Gfilters=='true' && $this->GfiltersAutoOpen=='true' && $this->Gtoolbar == 'true'  && $this->MyFiltersActivado == 'false'){
					echo '<script>BarraFilters_'.$this->GrillaName.'();</script>';
				}
				if($this->Gfilters=='true' && $this->Gtoolbar == 'true'  && $this->MyFiltersActivado == 'true'){
					echo '<script>BarraFilters_'.$this->GrillaName.'();</script>';
				}
		}else{

			include("class.MyGrilla.VentanaInsert.php");
			include("class.MyGrilla.VentanaUpdate.php");
			include("class.MyGrilla.Datos.php");
			include("class.MyGrilla.Operaciones.php");
		}
	}
}
?>
