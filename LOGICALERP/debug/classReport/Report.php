<?php

/**
* @class Report funciona solo con metodo POST
*/
class Report
{

	private $mysql         = '';
	public $arrayTables    = '';
	public $arrayTablesIni = '';

	function __construct($mysql,$arrayTablesIni)
	{
		$this->mysql          = $mysql;
		$this->arrayTablesIni = $arrayTablesIni;
		$this->TableRelations();
	}

	/**
	* @method TableRelations crear la relacion de las tablas que pertenecen al informe
	* @param arr array con las tablas y sus respectivas relaciones
	*/
	public function TableRelations()
	{

		foreach ($this->arrayTablesIni as $keyTable => $arrayResulTable) {
			$sql="SHOW COLUMNS FROM ".$arrayResulTable['table'];
			$query=$this->mysql->query($sql,$this->mysql->link);
			if ($query) {
				while ($row = $this->mysql->fetch_array($query)) {

					// $body .= '<tr>
					// 			<td>'.$arrayResulTable['table'].'</td>
					// 			<td>'.$row['Field'].'</td>
					// 			<td>'.$row['Type'].'</td>
					// 			<td>'.$row['Key'].'</td>
					// 		</tr>';

					$arrayTemp[$arrayResulTable['alias']][] = array(
																	'field' => $row['Field'],
																	'type'  => $row['Type'],
																	);
				}

				$arrayTemp[$arrayResulTable['alias']]['dependencies'] = $arrayResulTable['dependencies'];
				$arrayTemp[$arrayResulTable['alias']]['alias_fields'] = $arrayResulTable['alias_fields'];

			}
			else{
				echo '<script>alert("No se logro consultar la tabla '.$arrayResulTable['table'].' ");</script>';
			}

		}

		$this->arrayTables = $arrayTemp;

	}

	public function windowAddColumn($id,$col_name,$col_data)
	{

		foreach ($this->arrayTables as $tabla => $arrayTablesResul) {

			$body.='<table class="tables-report">
					<thead>
						<tr>
							<td >'.$tabla.'</td>
						</tr>
					</thead>';
			$style='';
			foreach ($arrayTablesResul as $key => $arrayResul) {
				if ($key == 'dependencies' || $key == 'alias_fields') { continue; }
				if ($this->arrayTables[$tabla]['alias_fields'][ $arrayResul['field'] ]=='') { continue; }
				$style = ($style=='')? 'style="background-color:#EEE;"' : '';
				$body .= '<tr>
							<td '.$style.' ondblclick="addRowField(\''.$tabla.'.'.$this->arrayTables[$tabla]['alias_fields'][ $arrayResul['field'] ].'\')" >
							'.$this->arrayTables[$tabla]['alias_fields'][ $arrayResul['field'] ].'</td>
						</tr>';
			}

			$body .= '</table>';

		}

		echo '<div class="content-field-add-column">
				<table>
					<tr>
						<td>Nombre de la columna</td>
						<td><input type="text" id="alias_columna" value="'.$col_name.'"></td>
						<td rowspan="2">
							<input type="button" onclick="addCol(\''.$id.'\')" value="Agregar" >
							<!--<button onclick="addCol(\''.$id.'\')">Agregar</button>-->
							</td>
					</tr>
					<tr>
						<td>Campos de la columna</td>
						<td><input type="text" id="campos_columna" ></td>
					</tr>

				</table>
			</div>
			'.$body.'
			<script>
				var col_data ="'.$col_data.'";
				var newchar = "+"
				col_data = col_data.split("<mas>").join(newchar);
				document.getElementById("campos_columna").value=col_data;
			</script>
			';

	}

	public function showEditableFormat()
	{
		foreach ($this->arrayTables as $tabla => $arrayTablesResul) {

			$colspan  = 1;
			$columnas = '';
			foreach ($arrayTablesResul as $key => $arrayResul) {
				if ($key == 'dependencies' || $key == 'alias_fields') { continue; }
				if ($this->arrayTables[$tabla]['alias_fields'][ $arrayResul['field'] ]=='') { continue; }

				$columnas .= '<li id="'.$arrayResul['field'].'" ondblclick="ventana_agregar_columna(id)">
									<span id="'.$arrayResul['field'].'_col_name">'.$tabla.'.'.$this->arrayTables[$tabla]['alias_fields'][ $arrayResul['field'] ].'</span>
									<i class="js-remove" onclick="deleteCol(\''.$arrayResul['field'].'\')">X</i>
									<input id="'.$arrayResul['field'].'_col_data" type="hidden" value="['.$tabla.'.'.$this->arrayTables[$tabla]['alias_fields'][ $arrayResul['field'] ].']">
							</li>';
				$colspan++;
			}

			// $estructure.='<div class="content-table"><div class="table-name">'.$tabla.'</div> '.$columnas.' </div>';
			$estructure.=$columnas;

		}

		echo'
			<div class="container" style="padding-top: 20px">
				<div id="filter" style="margin-left: 20px">
					<div class="content-layer"><div data-force="5" class="layer title title_xl">Columnas del Informe</div></div>

					<div style="margin-top: -8px; margin-left: 10px" class="block__list block__list_words">
						<ul id="cols">
							'.$estructure.'
						</ul>

						<button id="addUser" onclick="ventana_agregar_columna()">Agregar Columna</button>
					</div>

					<div class="content-layer"><div data-force="5" class="layer title title_xl" style="margin-left:10px;">Vista Previa</div></div>
					<div class="preview">
					</div>

				</div>
			</div>



			<script>
				var el = document.getElementById("cols");
				var sortable = Sortable.create(el);
			</script>

			';
		$this->setJSFunctions();
	}

	public function setJSFunctions()
	{
		echo '<script>
				var cont_cols_Report = 0;
				function deleteCol(id){
					document.getElementById(id).parentNode.removeChild(document.getElementById(id));
				}

				function ventana_agregar_columna(id){
					var col_name = ""
					,	col_data = "";
					if (typeof(id)!="undefined") {
						if (!document.getElementById(id+"_col_name")) { return; }
						col_name = document.getElementById(id+"_col_name").innerHTML;
						col_data = document.getElementById(id+"_col_data").value;

						var newchar = "<mas>"
						col_data = col_data.split("+").join(newchar);

					}


					Win_ventana_add_column = new Win.Window({
						apply       : "prueba",
						bodyStyle   : "",
						width       : 450,
						height      : 450,
						id          : "Win_ventana_add_column",
						title       : "VENTANA 2",
						modal       : true,
						autoScroll  : true,
						closable    : true,
						autoDestroy : true,
						autoLoad    :
						{
					        url     : "'.$_SERVER['SCRIPT_NAME'].'",
					        params  :
						        {
									opc_report : "add_column",
									col_name   : col_name,
									col_data   : col_data,
									id 		   : ""+id+"",
						        }
				    	},
					});
				}

				function addRowField(row){
					var element = document.getElementById("campos_columna");
					element.value = element.value+" ["+row+"]";

				}

				function addCol(id){
					var alias_columna  = document.getElementById("alias_columna").value;
					var campos_columna = document.getElementById("campos_columna").value;

					if (alias_columna == "" || campos_columna == "") { return; }

					if (typeof(id)!="undefined" && id!="undefined" && id!="") {
						document.getElementById(id+"_col_name").innerHTML=alias_columna;
						document.getElementById(id+"_col_data").value=campos_columna;
						Win_ventana_add_column.close()
					}
					else{

						var id = "col_"+cont_cols_Report;

					    var col = document.createElement("li");
					    col.setAttribute("id","col_"+cont_cols_Report);
					    col.setAttribute("ondblclick","ventana_agregar_columna(\'col_"+cont_cols_Report+"\')" );
					    col.innerHTML = \'<span id="\'+id+\'_col_name">\'+alias_columna+\'</span>\'+
										\'<i class="js-remove" onclick="deleteCol(\'+id+\')">X</i>\'+
										\'<input id="\'+id+\'_col_data" type="hidden" value="\'+campos_columna+\'">\';
					    document.getElementById("cols").appendChild(col);
					    cont_cols_Report++;

					    Win_ventana_add_column.close()
					}
				}

			</script>';
	}


	public function inicializa()
	{
		if ($_POST['opc_report']=='add_column' || $_GET['opc_report']=='add_column') {
			$id       = $_GET['id'];
			$col_name = $_GET['col_name'];
			$col_data = $_GET['col_data'];
			$this->windowAddColumn($id,$col_name,$col_data);
		}
		else{
			$this->showEditableFormat();
		}
	}

}

 ?>