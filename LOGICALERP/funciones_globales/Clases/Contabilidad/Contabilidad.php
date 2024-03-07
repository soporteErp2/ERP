<?php 
	/**
	 * namespace Contabilidad Espacio de trabajo para generar la contabilidad de un documento
	 */
	namespace Contabilidad;


	/**
	 * Contabilidad Gestionar la contabilizacion de un documento
	 * @var protected documentStructure 	Corresponde la estructura (nombres de tablas, nombres de campos, etc) 
	 *										del documento a contabilizar
	 *
	 */
	class Contabilidad
	{
		protected $mysql;
		protected $params;
		protected $documentStructure;
		
		/**
		 * Description
		 * @param  $mysql 							Conexion mysql a la base de datos
		 * @param  $params 							Parametros requeridos
		 *         $params[id_documento]			id del documento que realiza el movimiento
		 *         $params[tipo]					Tipo del documento a contabilizar (FC,FV,RC, etc.)
		 *         $params[Content-Type]			Forma en que se requiere la respuesta, si se necesita en formato JSON
		 *											entonces este campo debe ser igual a application/json, de lo contrario
		 *											si se deja vacio la clase retorna un array php de forma automatica
		 */
		function __construct($mysql,$params)
		{
			$this->mysql  = $mysql;
			$this->params = $params;
		}

		/**
		 * getDocumentStructure Consultar la estructura del documento a contabilizar
		 *
		 */
		public function getDocumentStructure(){
			echo $sql   = "SELECT
							D.id,
							D.tipo,
							D.detalle,
							D.nombre_tabla,
							D.campo_id_tercero,
							D.aplica_cuenta_pago,
							D.campo_id_cuenta_pago,
							D.aplica_retenciones,
							D.aplica_anticipos,
							DI.nombre_tabla,
							DI.campo_id_item,
							DI.campo_cantidad,
							DI.campo_costo,
							DI.campo_precio,
							DI.campo_tipo_descuento,
							DI.campo_descuento,
							DI.campo_id_item
						FROM contabilizacion_documentos AS D
						LEFT JOIN contabilizacion_documentos_items AS DI ON DI.id_documento = D.id
						WHERE D.activo=1 AND D.tipo='".$this->params[tipo]."' ";
			$query = $this->mysql->query($sql);
			$this->documentStructure = $this->mysql->fetch_assoc($query);
		}

		/**
		 * getItems Consultar los items del documento
		 * @return Array Listado de los items del documento y de inventario
		 */
		public function getItems()
		{
			$query = $this->mysql->query($this->params['sqlItems']);
			// var_dump($this->params);
			// var_dump($query);
			while ($row=$this->mysql->fetch_array($query)) {
				// echo $row['id_item']."<br>";
				$arrayItems['documento'][$row['id_item']] = array(
															'cantidad_total' => $row['cantidad_total'],
															'costo_unitario' => $row['costo_unitario'],
															'costo_total'    => $row['costo_total'],
														);
			}
			$whereItems = " id_item='".implode("' OR id_item='", array_keys($arrayItems['documento']))."' ";
			$sql   = "SELECT id_item,costos,cantidad
						FROM inventario_totales
						WHERE activo=1
						AND id_ubicacion=".$this->params['id_bodega']."
						AND ($whereItems) ";
			$query = $this->mysql->query($sql);
			while ($row=$this->mysql->fetch_array($query)) {
				$arrayItems['inventario'][$row['id_item']] = array(
																'cantidad_total' => $row['cantidad'],
																'costo_unitario' => $row['costos'],
																'costo_total'    => $row['cantidad']*$row['costos'],
															);
			}
			// echo $sql;
			// var_dump( $arrayItems);
			return $arrayItems;
		}

		public function generate(){
			$this->getDocumentStructure();
			var_dump($this->documentStructure);
		}

		/**
		 * response Dar respuesta de la clase
		 * @return Array/Json Listado con el detalle del resultado de la clase, sea error o no
		 */
		public function response(array $params){
			
			return ($this->params['Content-Type'] == 'application/json' )? json_encode($params) : $params ;
		}
	}

	include_once "../../../../configuracion/conectar.php";
	include_once "../../../../configuracion/define_variables.php";

	$params = array(
						'id_documento' => 17039, 
						'tipo'         => 'FC', 
					);

	$accounts = new Contabilidad($mysql,$params);
	$accounts->generate();

?>