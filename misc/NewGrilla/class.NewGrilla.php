<?php

	/*	ExtJS 3
	*	MyFunction.js
	*	fileuploader.js
	*/
	class newGrilla{

		public $GrillaName  = '';
		public $TableName   = '';
		public $MyWhere     = '';
		public $MySqlLimit  = '';
		public $GroupBy     = '';
		public $OrderBy     = '';

		private $headGrilla = '';
		private $bodyGrilla = '';
		private $colGrilla  = '';

		private $conexion   = '';
		private $camposSql  = '';




		function __construct($link){ $this->conexion = $link; }


		/** ADD COLUMNA TEXT GRILLA
		*	Function AddRow(
		*		"Titulo de la Columna",
		*		"Campo en la BD"
		*		"largo de la celda -> si se pone como valor 0 -> no muestra la celda"
		*		"Style columna"
		*	);
		*/
		public function AddRow($title,$campo,$width,$style=''){
			$this->colGrilla[$campo] = array("type"=>"text",
											"title"=>$title,
											"width"=>$width,
											"style"=>$style);
			$this->camposSql .= $campo.',';
			$this->headGrilla .= '<div class="colGrilla" style="width:'.$width.'; '.$style.'">'.$title.'</div>';
		}

		/** ADD COLUMNA CHECK GRILLA
		*	Function AddRow(
		*		"Titulo de la Columna",
		*		"Campo en la BD"
		*		"largo de la celda -> si se pone como valor 0 -> no muestra la celda"
		*		"invoca una funcion previamente creada en la clase"
		*	);
		*/
		public function addCheck($title,$campo,$width,$style=''){
			$this->colGrilla[$campo] = array("type"=>"check",
											"title"=>$title,
											"width"=>$width,
											"style"=>$style);
			$this->headGrilla .= '<div class="colGrilla" style="width:'.$width.'; '.$style.'">'.$title.'</div>';
		}

		/** ADD COLUMNA CHECK GRILLA
		*	Function AddRow(
		*		"Titulo de la Columna",
		*		"Id div Fila"
		*		"largo de la celda -> si se pone como valor 0 -> no muestra la celda"
		*		"invoca una funcion previamente creada en la clase"
		*	);
		*/
		public function addHtml($title,$campo,$width,$style='',$html=''){
			$this->colGrilla[$campo] = array("type"=>"html",
											"title"=>$title,
											"width"=>$width,
											"style"=>$style,
											"html"=>$html);
			$this->headGrilla .= '<div class="colGrilla" style="width:'.$width.'; '.$style.'">'.$title.'</div>';
		}



		public function GeneraGrilla(){
			$contFila = 0;

			if($this->MyWhere != ''){ $this->MyWhere = 'WHERE '.$this->MyWhere; }
			if($this->GroupBy != ''){ $this->GroupBy = 'GROUP BY '.$this->GroupBy; }
			if($this->OrderBy != ''){ $this->OrderBy = 'ORDER BY '.$this->OrderBy; }

			$this->camposSql = substr($this->camposSql, 0, -1);

			$this->sql   = 'SELECT id,'.$this->camposSql.' FROM '.$this->TableName.' '.$this->MyWhere.' '.$this->GroupBy.' '.$this->OrderBy;
			$queryGenera = mysql_query($this->sql);

			if(!$queryGenera){ echo '<script>console.log("'.mysql_errno().' => '.mysql_error().'")</script>'; }
			while ($rowFila = mysql_fetch_assoc($queryGenera)) {
				$contFila ++;

				//DIV FILA DE LA GRILLA
				$this->bodyGrilla .= '<div class="rowGrilla" id="fila1_grilla_'.$this->GrillaName.'_'.$rowFila['id'].'">
										<div class="rowGrilla" id="fila2_grilla_'.$this->GrillaName.'_'.$rowFila['id'].'">
											<div class="colCont" id="campo_grilla_'.$this->GrillaName.'_contFila_'.$rowFila['id'].'">'.$contFila.'</div>';

				//DIV CAMPOS DE LA FILA
				foreach ($this->colGrilla as $campo => $columna) {
					if($columna['type'] == "text"){
						$this->bodyGrilla .= '<div id="campo_grilla_'.$this->GrillaName.'_'.$campo.'_'.$rowFila['id'].'" class="colGrilla" style="width:'.$columna['width'].'; '.$columna['style'].'">'.$rowFila[$campo].'</div>';
					}
					else if($columna['type'] == "html"){
						$this->bodyGrilla .= '<div id="campo_grilla_'.$this->GrillaName.'_'.$campo.'_'.$rowFila['id'].'" class="colGrilla" style="width:'.$columna['width'].'; '.$columna['style'].'">'.$columna['html'].'</div>';
					}
				}

				$this->bodyGrilla .= '	</div>
									</div>';
			}

			echo'<div class="newGrilla" id="grilla_'.$this->GrillaName.'">
					<div class="toolbar">
						<div class="div_input_busqueda">
							<input type="text" id="inputBuscarGrillaManual_'.$this->GrillaName.'" onkeyup="inputBuscarGrillaManual_'.$this->GrillaName.'(event,this);">
						</div>
						<div class="div_img_actualizar_datos">
							<div style=""></div>
							<img src="images/reload_grilla.png" onclick="buscarDatosGrillaManual(document.getElementById(\'inputBuscarGrillaManual_'.$this->GrillaName.'\').value);">
						</div>

					</div>
					<div class="contenedorNewGrilla">
						<div class="headNewGrilla">
							<div class="colCont">&nbsp;</div>
							'.$this->headGrilla.'
						</div>
						<div id="bodyNewGrilla_'.$this->GrillaName.'" class="bodyNewGrilla">'.$this->bodyGrilla.'</div>
						<div style="float:right; padding:5px 20px 0 0;">
							<div style="float:left; margin:2px 5px 0 5px;font-weight:bold;" id="labelPaginacion">Pagina 1 de <?php echo $paginas; ?></div>
							<div class="grilla_first" onclick="paginacionGrilla(\'first\')"></div>
							<div class="grilla_prev" onclick="paginacionGrilla(\'prev\')"></div>
							<div class="grilla_next" onclick="paginacionGrilla(\'next\')"></div>
							<div class="grilla_last" onclick="paginacionGrilla(\'last\')"></div>
						</div>
					</div>
				</div>
				<script>
					// FUNCION PARA SCROLL
                    function calculaScroll_'.$this->GrillaName.'(){
                        var hscroll = document.getElementById("bodyNewGrilla_'.$this->GrillaName.'").scrollLeft;
                        document.getElementById("headNewGrilla'.$this->GrillaName.'").scrollLeft = hscroll;
                    }
                    document.getElementById("bodyNewGrilla_'.$this->GrillaName.'").onscroll = calculaScroll_'.$this->GrillaName.';
				</script>';

		}
	}

?>