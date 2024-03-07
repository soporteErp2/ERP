<?php
	include("../../../configuracion/conectar.php");
	include("../../../configuracion/define_variables.php");

	$id_empresa=$_SESSION['EMPRESA'];


	//CONSULTAR TODOS LOS CONCEPTOS
	$sql="SELECT id_grupo,id,codigo,descripcion FROM nomina_conceptos WHERE activo=1 AND id_empresa=$id_empresa AND tipo_concepto='General'";
	$query=mysql_query($sql,$link);
	while ($row=mysql_fetch_array($query)) {
		//CREAR ARRAY CON LOS CONCEPTOS
		$arrayDefinicionTributaria[$row['id_grupo']][$row['id']] = array(	'descripcion' => $row['descripcion'],
																			'codigo'=>$row['codigo']
																		);
	}

	//CONCULTAR LOS GRUPOS DE LOS CONCEPTOS
	$sql="SELECT  id,descripcion FROM nomina_grupos_conceptos WHERE activo=1 AND id_empresa=$id_empresa";
	$query=mysql_query($sql,$link);
	while ($row=mysql_fetch_array($query)) {
		//ARRAY CON LOS GRUPOS
		$arrayGrupos[$row['id']]=$row['descripcion'];
	}

	//CONSULTAR SI EL CARGO TIENE VALORES YA CONFIGURADOS
	$sql="SELECT id_concepto,id_cargo,valor_concepto FROM nomina_conceptos_cargo WHERE activo=1 AND id_empresa=$id_empresa AND id_cargo=$id_cargo";
	$query=mysql_query($sql,$link);
	while ($row=mysql_fetch_array($query)) {
		//ARRAY CON LOS DATOS YA GUARDADOS
		$arrayValorConceptos[$row['id_concepto']]= $row['valor_concepto'];
	}

	$body='';
	//RECORRER LOS ARRAY PARA ARMAR EL BODY
	foreach ($arrayGrupos as $id_grupo => $grupo) {
		$body.='<div class="divPadreFieldSet">';
		$body.='<fieldset class="fieldSetContenedor">';
		$body.='<legend><b>'.$grupo.'</b></legend>';
		foreach ($arrayDefinicionTributaria[$id_grupo] as $id => $resul){

			$valor=(isset($arrayValorConceptos[$id]))? $arrayValorConceptos[$id] : 0 ;

			$body.='<div class="filaCargoConceptos">';
			$body.='<div class="labelCargoConceptos">'.$resul['descripcion'].'</div>';
			$body.='<div class="campoCargoConceptos"><input type="text" value="'.$valor.'" id="'.$id.'" onkeyup="validaNumero(this)" class="select_input"></div>';
			//SALTO ENTRE DIV
			$body.='</div>';

			// $acumScript.='arrayInputs['.$id.']={codigo:"'.$resul['codigo'].'",id:"'.$id.'"};';

		}

		$body.='</fieldset>';
		$body.='</div>';

	}

 ?>

<style>

	.divPadreFieldSet{
		width     : 365px;
		float     : left;
		font-size : 9px;
	}

	.fieldSetContenedor{
		margin  : 10 20 0 10;
		padding : 5px 0 10px 15px;
		border  : none;
		background-color: #FFF;
	}

	.fieldSetContenedor>legend{
		height           : 25px;
		background-color : #999;
		color            : #FFF;
		font-weight      : bold;
		font-family      : sans-serif;
		width            : 100%;
		font-size        : 12px;
		line-height      : 24px;
		padding-left     : 15px;
		margin-left: -28px;
	}

	.filaCargoConceptos{
		float  : left;
		margin : 0 0 0 0;
		padding-left: 10px;
		height : 25px;
	}

	.labelCargoConceptos{
		width         : 150px;
		float         : left;
		overflow      : hidden;
		white-space   : nowrap;
		text-overflow : ellipsis;
		font-size     : 11px;
		font-family   : sans-serif;
	}
	.campoCargoConceptos{
		width  :150px;
		float  :left;
		overflow: hidden;
	}
	.campoCargoConceptos>input{
		border:none;
		height: 22px;
		text-align: right;
		background: #EEE;
		width: 100%;
	}

	.campoCargoConceptos>input:focus{border:1px solid #999;}

	#divContenedor_barra_conceptos{
		/*width       : 100%;*/
		font-size   : 20px;
		font-weight : bold;
		margin-top  : 15px;
		font-weight : bold;
		color       : #727272;
		font-family : Oswald, sans-serif;
	}

</style>

<div style="width:95%;height:100%;margin-left:20px;">
	<div id="tbarDefinicionTributaria" style="width:100%;height:60px;">
	</div>
	<div id="divLoad"></div>


	<div style="width:100%;height:calc(100% - 60px - 40px);">


		<div style="width:100%;height:calc(100% - 40px - 25px);overflow:auto;" id="contenedorInputsConceptos">
			<?php echo $body; ?>
		</div>

	</div>

</div>

 <script>
 	var arrayInputs={};

 	// <?php echo $acumScript; ?>

	var renderTolbar =document.getElementById('tbarDefinicionTributaria');
	var widthTolbar  =document.getElementById('tbarDefinicionTributaria').offsetWidth;

 	var tb = new Ext.Toolbar({
		renderTo : renderTolbar,
		width    : widthTolbar,
		height   : 60,
		style    : 'background-color:#CDDBF0;border-bottom:1px solid #999;',
		items    : [
    	    {
    	        xtype       : 'button',
    	        width		: 60,
				height		: 56,
				id 			: '',
    	        text        : 'Guardar',
    	        tooltip		: 'Guardar definicion tributaria',
    	        scale       : 'large',
    	        iconCls     : 'guardar',
    	        iconAlign   : 'top',
    	        handler     : function(){ BloqBtn(this); guardarConceptosCargos() }
    	    },
    	    '->',
    	    {
    	        xtype       : "tbtext",
    	        text        : '<div id="divContenedor_barra_conceptos"><?php echo $nombre_cargo; ?><div>',
    	        scale       : "large",
    	    }
    	]
	});

	function guardarConceptosCargos() {

		var arrayDatos={};

		var inputs = document.getElementById('contenedorInputsConceptos').querySelectorAll('.select_input');

		var cont = inputs.length;
		for (i = 0 ; i < cont; i++){

			indice=inputs[i].id;
			arrayDatos[i]={
				id : indice,
				valor : document.getElementById(inputs[i].id).value
			}

		};

		arrayDatos=JSON.stringify(arrayDatos);

		Ext.get('divLoad').load({
			url     : 'tributario/bd/bd.php',
			scripts : true,
			nocache : true,
			params  :
			{
				id_cargo   : '<?php echo $id_cargo ?>',
				arrayDatos : arrayDatos,
			}
		});

		// MyLoading();

	}

	function validaNumero(Input) {
		patron = /[^\d.]/g;
        if(patron.test(Input.value)){ Input.value = (Input.value).replace(/[^0-9]/g,''); }
	}

 </script>