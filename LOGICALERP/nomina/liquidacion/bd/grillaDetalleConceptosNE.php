<?php
	include("../../../../configuracion/conectar.php");
	include("../../../../configuracion/define_variables.php");
?>
			<link rel="stylesheet" type="text/css" href="planilla/bd/detalleConceptosNE.css">
<?php
	$configTypes = array("cesantias",
						"incapacidad",
						"licencia_maternidad_paternidad",
						"licencia_remunerada",
						"licencia_no_remunerada",
						"licencia_maternidad_paternidad",
						"licencia_remunerada",
						"licencia_no_remunerada",
						"fondo_solidaridad_pensional"
					);
	if (!in_array($tipo_concepto,$configTypes)) {
		?>
			<link rel="stylesheet" type="text/css" href="planilla/bd/detalleConceptosNE.css">
			<div class="content-error">
				Este concepto no requiere esta configuracion adicional
			</div>
		<?php
	}

	/* CONSULTAR EL TIPO DE CONCEPTO */
	$sql = "SELECT id,estructura FROM nomina_electronica_estructura_conceptos WHERE activo=1 AND nombre='$tipo_concepto' AND id_empresa=$_SESSION[EMPRESA]";
	$query = $mysql->query($sql);
	$id_estructura = $mysql->result($query,0,'id');
	$structure = $mysql->result($query,0,'estructura');
	if ($structure=="") {
		?>
			<div class="content-error">
				Algo anda mal!
				El concepto a registrar no tiene estructura configurable, comuniquese con soporte
			</div>
		<?php
	}
	/*armar el array con los datos de la estructura*/
	$decodeStructure = json_decode($structure,true);	

	/*lista de registros guardados*/
	$list = "<table id='concepto_data'>
				<thead>";
	/*armar fomurlario para agregar un nuevo registro*/
	$form .="<table class='table-form'>
				<tbody>";
	/*recorrer el array de la estructura para armar la lista y el formulario*/
	foreach ($decodeStructure as $key => $value) {
		/*titulos lista de datos guardados*/
		$list .= "<td>$value[label]</td>";

		/*campos del formulario*/
		switch ($value[type]) {
			case 'date':
				$input = "<input type='date' id='$value[name]'>";
				break;
			case 'time':
				$input = "<input type='time' id='$value[name]'>";
				break;	
			case 'select':
				$options='';
				foreach ($value['options'] as $key => $option) {
					$options .= "<option  value='$key'>$option</option>"; 
				};
				$input = "<select id='$value[name]'>$options</select>";
				break;			
			default:
				$input = "<input type='text' id='$value[name]'>";
				break;
		}
		/* si tiene comentario, mostrar el icono para que pueda ver la ayuda*/
		$iconHelp = ($value['comment']<>"")? "<img src='img/help.png' onclick='alert(\"$value[comment]\")' >" : "&nbsp;" ;

		$form .= "<tr>
					<td>$value[label]</td>	
					<td>$input</td>	
					<td>$iconHelp</td>	
				  </tr>";
	}
	$form .="</tbody>
			</table>";

	$list .= " <td></td>
				</thead>
				<tbody>";

	/* consultar los demas conceptos almacenados*/
	$sql = "SELECT id,data FROM nomina_planillas_empleados_conceptos_datos_nomina_electronica 
			WHERE activo=1 
			AND tipo_planilla='LE'
			AND id_planilla='$id_planilla' 
			AND id_empleado=$id_empleado 
			AND id_concepto=$id_concepto 
			AND id_empresa=$_SESSION[EMPRESA]";
	$query = $mysql->query($sql);
	while($row=$mysql->fetch_array($query)){
		$arrayData=json_decode($row["data"],true);
		$list .= "<tr id='json_data_$row[id]'>";
		foreach ($arrayData as $key => $value) {
			$text = ($value['type']=='select')? $value['options'][$value[value]] : $value[value] ;
			$list .= "<td>$text</td>";
		}
		$list .= '	
					<td>
						<img 
							src     = "img/edit.png" 
							title   = "editar registro" 
							style   = "cursor:hand" 
							onclick = \'windowEditDataLE('.$row[id].','.$row["data"].')\' 
						/>	
					</td>
					<td>
						<img 
							src     = "img/delete.png" 
							title   = "eliminar registro" 
							style   = "cursor:hand" 
							onclick = "deleteDataLE('.$row[id].')"
						/>	
					</td>
					</tr>';
	}



	$list .= " </tbody>
			  </table>";
?>
 <div>
 	<div id="tbar_concepto_nomina_electronica"></div>
 	<div class="content-default-table" style="height: calc(100% - 80px);overflow: auto;" id="contentConceptoNominaElectronica"><?= $list ?></div>
 </div>
<script>
	// crear la barra superior de botones
	new Ext.Panel
		(
			{
				renderTo :'tbar_concepto_nomina_electronica',
				id       : "tbar_cne",
				frame    :false,
				border   :false,
				tbar     :
				[
					{
						xtype   : 'buttongroup',
						columns : 3,
						title   : 'Opciones',
						items   :
						[
							{
								xtype     : 'button',
								text      : 'Nuevo',
								scale     : 'large',
								iconCls   : 'add_new',
								iconAlign : 'top',
								handler   : function(){BloqBtn(this); windowAddDataLE();}
							},
							{
								xtype     : 'button',
								text      : 'Cerrar',
								scale     : 'large',
								iconCls   : 'regresar',
								iconAlign : 'top',
								handler   : function(){BloqBtn(this); Win_Ventana_configurar_datos_ne.close();}
							},
						]
					},
				]
			}
		);

	function windowAddDataLE(title='agregar',id=0){

        Win_Ventana_add_data = new Ext.Window({
            height      : 350,
            width       : 400,
            id          : 'Win_Ventana_add_data',
            title       : `${title} Registro`,
            modal       : true,
            autoScroll  : false,
            closable    : true,
            autoDestroy : true,
            bodyStyle   : 'background-color:#FFF;',
            items       :
            [
                {
                    closable    : false,
                    border      : false,
                    autoScroll  : true,
                    iconCls     : '',
                    bodyStyle   : 'background-color:#FFF;',                    
                    tbar        :
                    [
                        {
                            xtype       : 'button',
                            width       : 60,
                            height      : 56,
                            text        : 'Guadar',
                            scale       : 'large',
                            iconCls     : 'guardar',
                            iconAlign   : 'top',
                            handler     : function(){ saveData(id) }
                        },
                        {
                            xtype       : 'button',
                            width       : 60,
                            height      : 56,
                            text        : 'Regresar',
                            scale       : 'large',
                            iconCls     : 'regresar',
                            iconAlign   : 'top',
                            handler     : function(){ Win_Ventana_add_data.close() }
                        }
                    ],
                    html : `<?= $form ?>`
                }
            ]

        }).show();
    }

    async function saveData(id){
    	let structure = JSON.parse('<?= $structure ?>');
    	data = structure.map(element=>{
	    	element.value = document.getElementById(element.name).value;
	    	return element;
    	})
    	MyLoading2('on');
    	
    	if (id>0) {
    		await fetch(`planilla/bd/bd.php?opc=deleteDataNE&id=${id}`, {
								method  : 'GET', // or 'PUT'
						})
    	}

    	Ext.Ajax.request({
            url     : 'liquidacion/bd/bd.php',
            params  :
            {
				opc           : 'saveDataNE',
				id_planilla   : '<?php echo $id_planilla; ?>',
				id_empleado   : '<?php echo $id_empleado; ?>',
				id_concepto   : '<?php echo $id_concepto; ?>',
				id_estructura : '<?php echo $id_estructura; ?>',
				data          : JSON.stringify(data),
            },
            success :function (result, request){
    					let response = JSON.parse(result.responseText);
    					if (response.status!='sucess') { MyLoading2('off',{icono:'fail',texto:'Se presento un error intentelo de nuevo'}); return;}
    					if (id>0) {
							document.getElementById(`json_data_${id}`).remove()
						} 
    					let row = `<tr id="json_data_${response.lastId}">`;
    					data.map(element=>{
    						row += `<td>${element.value}</td>`
    					});
    					row += `<td>
									<img 
										src     = "img/edit.png" 
										title   = "editar registro" 
										style   = "cursor:hand" 
										onclick = 'windowEditDataLE(${response.lastId},${JSON.stringify(data)})' 
									/>	
								</td>
								<td>
									<img 
										src     = "img/delete.png" 
										title   = "eliminar registro" 
										style   = "cursor:hand" 
										onclick = "deleteDataLE(${response.lastId})"
									/>	
								</td>	
								<tr>`;
    					$("#concepto_data>tbody").append(row);
    					Win_Ventana_add_data.close()
    					MyLoading2('off');
                        
                    },
            failure : function(){
    					MyLoading2('off',{icono:'fail',texto:'Se presento un error de conexion con el servidor'});
                        // alert("Error\nSe presento un error al guardar el registro, por favor contacte a soporte");
                    }
        });
    }

    function deleteDataLE(id){
    	if (!confirm("Eliminar el registro?")) {return}
    	MyLoading2('on');
    	Ext.Ajax.request({
            url     : 'liquidacion/bd/bd.php',
            params  :
            {
				opc : 'deleteDataNE',
				id  : id
            },
            success :function (result, request){
    					let response = JSON.parse(result.responseText);
    					if (response.status!='sucess') { MyLoading2('off',{icono:'fail',texto:'Se presento un error intentelo de nuevo'}); return;}
    					$(`#json_data_${id}`).remove();
    					MyLoading2('off',{icono:'sucess',texto:'registro eliminado'});
                        
                    },
            failure : function(){
    					MyLoading2('off',{icono:'fail',texto:'Se presento un error de conexion con el servidor'});
                        // alert("Error\nSe presento un error al guardar el registro, por favor contacte a soporte");
                    }
        });
    }

    function windowEditDataLE(id,json){
    	windowAddDataLE("Editar",id);
    	json.map(element=> document.getElementById(element.name).value=element.value );

    }

</script>