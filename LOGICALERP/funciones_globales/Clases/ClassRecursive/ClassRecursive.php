<?php
	// error_reporting(E_ALL);
	/**
	 * ClassRecursive funciones recursivas para informes u otros
	 */
	class ClassRecursive
	{

		public $theme;
		public $listState;

		function __construct($listState='collapse',$theme=NULL){
			$this->theme = $theme;
			if ($theme==NULL) {
				echo '<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">';
				echo "<style>";
				include 'theme-basic.css';
				echo "</style>";
			}
			$this->listState = ($listState=='collapse')? '' : "checked" ;
		}

		/**
		 * createTreeView Visualizar array en estructura de arbol
		 * @param  Int $parent    Id o Codigo del padre principal del array
		 * @param  Array $arrayData Array con los datos a visualizar
		 * @return String            Retornar el cntenido procesado
		 */
		public function createTreeView($parent,$arrayData){
			$html = "";
			if (isset($arrayData['parents'][$parent])) {
				$html .= "
				<ol class='tree'>";
				foreach ($arrayData['parents'][$parent] as $itemId) {
					if(!isset($arrayData['parents'][$itemId])) {
						$html .= "<li ".$arrayData['items'][$itemId]['css']." ><ul><label >".$arrayData['items'][$itemId]['nombre']."</label> ".$arrayData['items'][$itemId]['html']." <input type='checkbox' ".$this->listState." />  </ul></li>";
					}
					if(isset($arrayData['parents'][$itemId])) {
						$html .= "
						<li ".$arrayData['items'][$itemId]['css']." ></ul><label for='title' >".$arrayData['items'][$itemId]['nombre']."  </label>".$arrayData['items'][$itemId]['html']."</ul> <input type='checkbox' ".$this->listState." />";
						$html .= $this->createTreeView($itemId, $arrayData);
						$html .= "</li>";
					}
				}
				$html .= "</ol>";
			}
			return $html;
		}

	}

?>