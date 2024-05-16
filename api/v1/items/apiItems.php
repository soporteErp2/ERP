<?php

	include '../../../misc/ConnectDb/class.ConnectDb.php';
	/**
	 * @apiDefine Ventas Se requieren permisos de ventas
	 * Para crear, actualizar o eliminar documentos, se requieren los respectivos permisos del modulo de ventas
	 *
	 */
	class ApiItems
	{
		private $objConectDB;
		private $mysql;
		private $nit_empresa;
		private $id_ciente;
		private $id_sucursal;
		private $nombre_sucursal;
		private $id_bodega;
		private $nombre_bodega;
		private $id_empresa;
		private $nombre_empresa;
		private $decimales_moneda;
		private $id_usuario;
		private $tipo_doc_usuario;
		private $documento_usuario;
		private $nombre_usuario;
		private $usuarioPermisos;
		private $UsuarioDb    = 'root';
		private $PasswordDb   = 'serverchkdsk';
		private $actionUpdate = false;

		// CONEXION DESARROLLO
		private $ServidorDb = 'localhost';
		//private $NameDb     = 'erp_bd';

		// CONEXION PRODUCCION
		// private $ServidorDb = 'localhost';
		private $NameDb     = 'erp_acceso';
		// private $UsuarioDb  = 'root';
		// private $PasswordDb = 'serverchkdsk';

		function __construct(){
			$this->conexion();
			$this->authentication();
		}

		public function conexion(){
			$this->objConectDB = new ConnectDb(
			                       'MySql',
			                       $this->ServidorDb,
			                       $this->UsuarioDb,
			                       $this->PasswordDb,
			                       $this->NameDb
			                   );
			$this->mysql = $this->objConectDB->getApi();
			$this->link  = $this->mysql->conectar();
		}

		public function authentication(){
			if (!isset($_SERVER['PHP_AUTH_USER']) || !isset($_SERVER['PHP_AUTH_PW']) || $_SERVER['PHP_AUTH_USER']=='' || $_SERVER['PHP_AUTH_PW']=='') {
				$this->apiResponse(array('status' => 401,'data'=> 'Datos de autenticacion incompletos'));
			}
			else{
				$arrayExplode      = explode(":", $_SERVER['PHP_AUTH_PW']);
				$token             = $arrayExplode[0];
				$this->nit_empresa = $arrayExplode[1];
				$sql   = "SELECT id,servidor,bd FROM host WHERE activo=1 AND nit=$this->nit_empresa ";
				$query = $this->mysql->query($sql,$this->mysql->link);
				$rows  = $this->mysql->num_rows($query);
				if ($rows==0) {
					$this->apiResponse(array('status' => 401,'data'=> 'La empresa no existe en el sistema'));
				}
				$this->ServidorDb = $this->mysql->result($query,0,'servidor');
				$this->NameDb     = $this->mysql->result($query,0,'bd');

				$this->conexion();

				$sql="SELECT id,nombre,decimales_moneda FROM empresas WHERE activo=1 AND documento=$this->nit_empresa";
				$query=$this->mysql->query($sql,$this->mysql->link);
				$this->id_empresa       = $this->mysql->result($query,0,'id');
				$this->nombre_empresa   = $this->mysql->result($query,0,'nombre');
				$this->decimales_moneda = $this->mysql->result($query,0,'decimales_moneda');

				$sql="SELECT id,tipo_documento_nombre,documento,nombre,token,id_rol FROM empleados WHERE activo=1 AND id_empresa=$this->id_empresa AND username='$_SERVER[PHP_AUTH_USER]'";
				$query=$this->mysql->query($sql,$this->mysql->link);
				$rows  = $this->mysql->num_rows($query);
				if ($rows==0) {
					$this->apiResponse(array('status' => 401,'data'=> 'El usuario no existe en el sistema'));
				}
				$this->id_usuario        = $this->mysql->result($query,0,'id');
				$this->tipo_doc_usuario  = $this->mysql->result($query,0,'tipo_documento_nombre');
				$this->documento_usuario = $this->mysql->result($query,0,'documento');
				$this->nombre_usuario    = $this->mysql->result($query,0,'nombre');
				$id_rol                  = $this->mysql->result($query,0,'id_rol');

				if ($token<>$this->mysql->result($query,0,'token')){
					$this->apiResponse(array('status' => 401,'data'=> "Error, token invalido $token - ".$this->mysql->result($query,0,'token')));
				}

				$sql="SELECT id_permiso FROM empleados_roles_permisos WHERE id_rol=$id_rol";
				$query=$this->mysql->query($sql,$this->mysql->link);
				while ($row=$this->mysql->fetch_array($query)) { $this->usuarioPermisos[$row['id_permiso']] = true;  }

			}
		}

		/**
		 * @api {get} /items/:codigo/:cod_familia/:cod_grupo/:cod_subgrupo/:disponible_pos/:disponible_asiste/:modulo/:id_sucursal/:id_bodega/:id_seccion Consultar Items
		 * @apiVersion 1.0.0
		 * @apiDescription Consultar items del sistema.
		 * @apiName get_items
		 * @apiGroup Items
		 *
		 * @apiParam {String} [codigo] Codigo del item para hacer consulta unica
		 * @apiParam {String} [nombre] Buscar items que contengan ese nombre o parte de el
		 * @apiParam {String} [cod_familia] Codigo de la familia para consultar todos los items de una familia
		 * @apiParam {String} [cod_grupo] Codigo del grupo para consultar todos los items de un grupo
		 * @apiParam {String} [cod_subgrupo] Codigo del subgrupo para consultar todos los items de un subgrupo
		 * @apiParam {String="true","false"} [disponible_pos] Filtrar si los items son del pos
		 * @apiParam {Int} [id_seccion] Id de la seccion a la que pertenecen los items que se quieren consultar (Consultar en el panel de control del sistema)
		 * @apiParam {String="true","false"} [minibar] Filtrar si los items estan disponibles en para minibar (Hoteles)
		 * @apiParam {String="true","false"} [disponible_asiste] Filtrar si los items estan disponibles en asiste
		 * @apiParam {String="venta","compra"} [modulo] Filtrar si los items estan disponibles en el modulo compra o venta
		 * @apiParam {Int} [id_sucursal] Id de la sucursal de la bodega (Consultar en el panel de control del sistema)
		 * @apiParam {Int} [id_bodega] Id de la sucursal de la bodega (Consultar en el panel de control del sistema)
		 * @apiSuccess {Int} id Id interno del item
		 * @apiSuccess {String} codigo Codigo unico que identifica el item
		 * @apiSuccess {String} code_bar Codigo de barras del item
		 * @apiSuccess String{} unidad_medida Unidad de medida
		 * @apiSuccess {Docuble} cantidad_unidades Cantidad de la unidad de medida
		 * @apiSuccess {String} codigo_familia Codigo de la familia a la que pertenece el item
		 * @apiSuccess {String} familia Familia a la que pertenece el item
		 * @apiSuccess {String} codigo_grupo Codigo del grupo al que pertenece el item
		 * @apiSuccess {String} grupo Grupo al que pertenece el item
		 * @apiSuccess {String} codigo_subgrupo Codigo del subgrupo al que pertenece el item
		 * @apiSuccess {String} subgrupo Subgrupo al que pertenece el item
		 * @apiSuccess {String} codigo_centro_costos Codigo del centro de costos del item
		 * @apiSuccess {String} centro_costos Centro de costos del item
		 * @apiSuccess {String} nombre Nombre del item
		 * @apiSuccess {String} marca Marca del item
		 * @apiSuccess {String} modelo Modelo del item
		 * @apiSuccess {String} color Color del item
		 * @apiSuccess {String} numero_piezas Numero de piezas del item
		 * @apiSuccess {String} descripcion1 Descripcion 1 del item
		 * @apiSuccess {String} descripcion2 Descripcion 2 del item
		 * @apiSuccess {String} impuesto Nombre del impuesto del ite
		 * @apiSuccess {Double} valor_impuesto Valor del impuesto del item
		 * @apiSuccess {String} inventariable Si es o no inventariable el item
		 * @apiSuccess {String} disponible_compra Disponible para comprarlo
		 * @apiSuccess {String} disponible_venta Dsiponible para venderlo
		 * @apiSuccess {String} item_costo Si es un item de costo
		 * @apiSuccess {String} item_gasto Si es un item de gasto
		 * @apiSuccess {String} activo_fijo Si es un activo fijo
		 * @apiSuccess {Double} costo Costo de inventario del item, para que se muestre se debe enviar el id sucursal y el id_bodega
		 * @apiSuccess {Double} precio_venta Precio de venta del item, para que se muestre se debe enviar el id sucursal y el id_bodega
		 * @apiSuccess {Double} cantidad Cantida del item en inventario, para que se muestre se debe enviar el id sucursal y el id_bodega
		 * @apiSuccess {String} codigo_transaccion Codigo de transaccion para cargos en SIHO
		 * @apiSuccess {Int} id_bodega_produccion id de la bodega de donde se descontara el inventario
		 * @apiSuccess {String} activo_pos Si esta o no disponible para el POS
		 * @apiSuccess {String} termino Termino del producto terminado (POS)
		 * @apiSuccess {Double} precio_venta_1 Precio de venta 1
		 * @apiSuccess {Double} precio_venta_2 Precio de venta 2
		 * @apiSuccess {Double} precio_venta_3 Precio de venta 3
		 * @apiSuccess {Double} precio_venta_4 Precio de venta 4
		 * @apiSuccess {Double} precio_venta_5 Precio de venta 5
		 * @apiSuccess {Object[]} receta Si el item tiene una receta configurada, entonces se detallan los ingredientes
		 * @apiSuccess {String} logo Nombre del logo del intem en el sitema
		 * @apiSuccess {Int} id_categoria_asiste Id de la categoria configurada para asiste
		 * @apiSuccess {Int} receta.id Id interno del item
		 * @apiSuccess {String} receta.codigo Codigo unico que identifica el item
		 * @apiSuccess {String} receta.nombre Nombre del item
		 * @apiSuccess {Double} receta.costo Costo de inventario del item, para que se muestre se debe enviar el id sucursal y el id_bodega
		 * @apiSuccess {Double} receta.precio_venta Precio de venta del item, para que se muestre se debe enviar el id sucursal y el id_bodega
		 * @apiSuccess {Double} receta.cantidad Cantida del item necesario para la receta
		 * @apiSuccess {Double} receta.stock Cantida del item en inventario, para que se muestre se debe enviar el id sucursal y el id_bodega
		 *
		 * @apiSuccessExample Success-Response:
		 *     HTTP/1.1 200 OK
		 *     [
		 *     	 {
	     *  	    "id": "1",
	     * 	    "codigo": "01010101",
	     * 	    "code_bar": "",
	     * 	    "unidad_medida": "Mililitros",
	     * 	    "cantidad_unidades": "1",
	     * 	    "codigo_familia": "01",
	     * 	    "familia": "INGREDIENTES",
	     * 	    "codigo_grupo": "01",
	     * 	    "grupo": "MATERIA PRIMA ",
	     * 	    "codigo_subgrupo": "01",
	     * 	    "subgrupo": "ABARROTES",
	     * 	    "codigo_centro_costos": "",
	     * 	    "centro_costos": "",
	     * 	    "nombre": "ACEITE CANOLA",
	     * 	    "marca": "",
	     * 	    "modelo": "",
	     * 	    "color": "",
	     * 	    "numero_piezas": "0",
	     * 	    "descripcion1": "",
	     * 	    "descripcion2": "",
	     * 	    "impuesto": "IVA GENERADO SERVICIOS 19% (19%)",
	     * 	    "valor_impuesto": "19.00",
	     * 	    "inventariable": "Si",
	     * 	    "disponible_compra": "Si",
	     * 	    "disponible_venta": "Si",
	     * 	    "item_costo": "No",
	     * 	    "item_gasto": "No",
	     * 	    "activo_fijo": "No",
	     * 	    "costo": "0.00",
	     * 	    "precio_venta": "0.00",
	     * 	    "cantidad": "0.00",
	     * 	    "logo": "logo.png",
	     * 	    "id_categoria_asiste": "1",
	     * 	    "receta": ""
	     * 	},
	     * 	{
	     * 	    "id": "405",
	     * 	    "codigo": "1",
	     * 	    "code_bar": "",
	     * 	    "unidad_medida": "Unidad",
	     * 	    "cantidad_unidades": "1",
	     * 	    "codigo_familia": "02",
	     * 	    "familia": "PRODUCTO TERMINADO",
	     * 	    "codigo_grupo": "01",
	     * 	    "grupo": "ENTRADAS",
	     * 	    "codigo_subgrupo": "01",
	     * 	    "subgrupo": "ENTRADAS FRIAS",
	     * 	    "codigo_centro_costos": "",
	     * 	    "centro_costos": "",
	     * 	    "nombre": "CEVICHE PERUANO",
	     * 	    "marca": "",
	     * 	    "modelo": "",
	     * 	    "color": "",
	     * 	    "numero_piezas": "0",
	     * 	    "descripcion1": "",
	     * 	    "descripcion2": "",
	     * 	    "impuesto": "",
	     * 	    "valor_impuesto": "",
	     * 	    "inventariable": "No",
	     * 	    "disponible_compra": "No",
	     * 	    "disponible_venta": "Si",
	     * 	    "item_costo": "No",
	     * 	    "item_gasto": "No",
	     * 	    "activo_fijo": "No",
	     * 	    "costo": "0.00",
	     * 	    "precio_venta": "0.00",
	     * 	    "cantidad": "0.00",
	     * 	    "logo": "",
	     * 	    "id_categoria_asiste": "",
	     * 	    "receta": [
	     * 	        {
	     * 	            "id": "200",
	     * 	            "codigo": "01010504",
	     * 	            "nombre": "CAMARON CRUDO ",
	     * 	            "cantidad": "300.00",
	     * 	            "costo": "0.00",
	     * 	            "precio_venta": "0.00",
	     * 	            "stock": "0.00"
	     * 	        },
	     * 	        {
	     * 	            "id": "329",
	     * 	            "codigo": "01010871",
	     * 	            "nombre": "TOMATE CHONTO",
	     * 	            "cantidad": "200.00",
	     * 	            "costo": "0.00",
	     * 	            "precio_venta": "0.00",
	     * 	            "stock": "0.00"
	     * 	        },
	     * 	        {
	     * 	            "id": "274",
	     * 	            "codigo": "01010816",
	     * 	            "nombre": "CEBOLLA ROJA",
	     * 	            "cantidad": "100.00",
	     * 	            "costo": "0.00",
	     * 	            "precio_venta": "0.00",
	     * 	            "stock": "0.00"
	     * 	        }
	     * 	    ]
	     * 	}
		 *    ]
		 *
		 */
		public function show($data=NULL){

			$sql="SELECT id_familia,cod_familia,id_grupo,cod_grupo,id AS id_subgrupo,codigo AS cod_subgrupo
					FROM items_familia_grupo_subgrupo WHERE activo=1 AND id_empresa=$this->id_empresa";
			$query=$this->mysql->query($sql,$this->mysql->link);
			while ($row=$this->mysql->fetch_array($query)) {
				$arrayFamilia['codigo'][$row['cod_familia']]   = $row['id_familia'];
				$arrayGrupo['codigo'][$row['cod_grupo']]       = $row['id_grupo'];
				$arraySubgrupo['codigo'][$row['cod_subgrupo']] = $row['id_subgrupo'];

				$arrayFamilia['id'][$row['id_familia']]   = $row['cod_familia'];
				$arrayGrupo['id'][$row['id_grupo']]       = $row['cod_grupo'];
				$arraySubgrupo['id'][$row['id_subgrupo']] = $row['cod_subgrupo'];
			}

			$sql="SELECT id,impuesto,valor FROM impuestos WHERE activo=1 AND id_empresa=$this->id_empresa";
			$query=$this->mysql->query($sql,$this->mysql->link);
			while ($row=$this->mysql->fetch_array($query)) {
				$arrayImpuestos[$row['id']] = $row['valor'];
			}

			$sql="SELECT id,codigo,nombre FROM centro_costos WHERE activo=1 AND id_empresa=$this->id_empresa";
			$query=$this->mysql->query($sql,$this->mysql->link);
			while ($row=$this->mysql->fetch_array($query)) {
				$arrayCentroCostos[$row['id']] = array(
													'codigo' => $row['codigo'],
													'nombre' => $row['nombre'],
 													);
			}

			if($data['codigo'] <> ''){ $whereItems       .= " AND I.codigo='$data[codigo]' "; }
			if($data['nombre'] <> ''){ $whereItems       .= " AND I.nombre_equipo LIKE '%$data[nombre]%' "; }
			if($data['cod_familia'] <> ''){ $whereItems  .= " AND I.id_familia='".$arrayFamilia['codigo'][$data['cod_familia']]."'"; }
			if($data['cod_grupo'] <> ''){ $whereItems    .= " AND I.id_familia='".$arrayGrupo['codigo'][$data['cod_grupo']]."'"; }
			if($data['cod_subgrupo'] <> ''){ $whereItems .= " AND I.id_familia='".$arraySubgrupo['codigo'][$data['cod_subgrupo']]."'"; }
			if($data['disponible_pos'] <> ''){
				if ($data['disponible_pos'] <> 'true' && $data['disponible_pos'] <> 'false') {
					return array('status'=>false,'detalle'=>'Para el campo disponible_pos solo se permite true o false ');
				}
				else{
					$whereItems .= " AND I.modulo_pos='$data[disponible_pos]'";
				}
			}
			if($data['minibar'] <> ''){
				if ($data['minibar'] <> 'true' && $data['minibar'] <> 'false') {
					return array('status'=>false,'detalle'=>'Para el campo minibar solo se permite true o false ');
				}
				else{
					$whereItems .= " AND I.minibar='$data[minibar]'";
				}
			}
			if($data['disponible_asiste'] <> ''){
				if ($data['disponible_asiste'] <> 'true' && $data['disponible_asiste'] <> 'false') {
					return array('status'=>false,'detalle'=>'Para el campo disponible_asiste solo se permite true o false ');
				}
				else{
					$whereItems .= " AND I.disponible_asiste='$data[disponible_asiste]'";
				}
			}

			switch ($data['modulo']) {
				case 'venta':
					$whereItems   .= " I.estado_venta='true' ";
					break;
				case 'compra':
					$whereItems   .= " I.estado_compra='true' ";
					break;
				default:
					$whereItems   .= " ";
					break;
			}

			if($data['id_sucursal'] <> ''){ $whereItemsTotales  .= " AND id_sucursal=$data[id_sucursal]"; }
			if($data['id_bodega'] <> ''){ $whereItemsTotales    .= " AND id_ubicacion=$data[id_bodega]"; }
			$sql="SELECT
						IR.id AS id_grupo_termino,
						IR.nombre AS grupo_termino,
						IRD.id AS id_termino,
						IRD.nombre AS termino
					FROM
						items_terminos AS IR
					INNER JOIN items_terminos_detalle AS IRD ON IRD.id_termino = IR.id
					WHERE
						IR.activo = 1
					AND IR.id_empresa = $this->id_empresa";
			$query=$this->mysql->query($sql);
			while ($row=$this->mysql->fetch_array($query)) {
				if (isset($arrayTermino[$row['id_grupo_termino']])) {
					$arrayTermino[$row['id_grupo_termino']]['terminos'][] = array(
																					'id_termino' => $row['id_termino'],
																					'termino'    => $row['termino'],
																				);
				}
				else{
					$arrayTermino[$row['id_grupo_termino']] = array(
																	'id_grupo_termino' => $row['id_grupo_termino'],
																	'grupo_termino'    => $row['grupo_termino'],
																	'terminos'         => array( '0' =>
																									array(
																											'id_termino' => $row['id_termino'],
																											'termino'    => $row['termino'],
																										)
																							)
																	);
				}
			}
			// LEFT JOIN inventario_totales AS IT ON IT.id_item=I.id
			
			$sqlJoin = ($data['id_seccion']<>'')? " INNER JOIN seccion_items as SI ON SI.id_item=I.id WHERE SI.id_seccion =$data[id_seccion] AND " : " WHERE ";
			$sqlItems="SELECT
					I.id,
					I.codigo,
					I.code_bar AS codigo_barras,
					I.unidad_medida,
					I.cantidad_unidades,
					I.id_familia,
					I.familia,
					I.id_grupo,
					I.grupo,
					I.id_subgrupo,
					I.subgrupo,
					I.id_centro_costos,
					I.centro_costos,
					I.nombre_equipo,
					I.marca,
					I.modelo,
					I.color,
					I.numero_piezas,
					I.descripcion1,
					I.descripcion2,
					I.id_impuesto,
					I.impuesto,
					IF(I.inventariable='true','Si','No') AS inventariable,
					IF(I.estado_compra='true','Si','No') AS estado_compra,
					IF(I.estado_venta='true','Si','No') AS estado_venta,
					IF(I.opcion_costo='true','Si','No') AS opcion_costo,
					IF(I.opcion_gasto='true','Si','No') AS opcion_gasto,
					IF(I.opcion_activo_fijo='true','Si','No') AS opcion_activo_fijo,
					I.codigo_transaccion,
					I.id_bodega_produccion,
					I.activo_pos,
					I.id_termino,
					I.precio_venta_1,
					I.precio_venta_2,
					I.precio_venta_3,
					I.precio_venta_4,
					I.precio_venta_5,
					I.id_categoria_asiste
				FROM items AS I
				$sqlJoin
				I.activo=1
				AND I.id_empresa=$this->id_empresa
				AND I.opcion_activo_fijo <> 'true'
				$whereItems
				LIMIT 0,50";
			$query=$this->mysql->query($sqlItems,$this->mysql->link);
			while ($row=$this->mysql->fetch_array($query)) {
				$result[] = array(
									'id'                   => $row['id'],
									'codigo'               => $row['codigo'],
									'code_bar'             => $row['code_bar'],
									'unidad_medida'        => $row['unidad_medida'],
									'cantidad_unidades'    => $row['cantidad_unidades'],
									'codigo_familia'       => $arrayFamilia['id'][$row['id_familia']],
									'familia'              => $row['familia'],
									'codigo_grupo'         => $arrayGrupo['id'][$row['id_grupo']],
									'grupo'                => $row['grupo'],
									'codigo_subgrupo'      => $arraySubgrupo['id'][$row['id_subgrupo']],
									'subgrupo'             => $row['subgrupo'],
									'codigo_centro_costos' => $arrayCentroCostos[$row['id_centro_costos']]['codigo'],
									'centro_costos'        => $row['centro_costos'],
									'nombre'               => utf8_encode($row['nombre_equipo']),
									'marca'                => $row['marca'],
									'modelo'               => $row['modelo'],
									'color'                => $row['color'],
									'numero_piezas'        => $row['numero_piezas'],
									'descripcion1'         => $row['descripcion1'],
									'descripcion2'         => $row['descripcion2'],
									'id_impuesto'          => $row['id_impuesto'],
									'impuesto'             => $row['impuesto'],
									'valor_impuesto'       => $arrayImpuestos[$row['id_impuesto']],
									'inventariable'        => $row['inventariable'],
									'disponible_compra'    => $row['estado_compra'],
									'disponible_venta'     => $row['estado_venta'],
									'item_costo'           => $row['opcion_costo'],
									'item_gasto'           => $row['opcion_gasto'],
									'activo_fijo'          => $row['opcion_activo_fijo'],
									'codigo_transaccion'   => $row['codigo_transaccion'],
									'id_bodega_produccion' => $row['id_bodega_produccion'],
									'activo_pos'           => $row['activo_pos'],
									'termino'              => $arrayTermino[$row['id_termino']],
									'precio_venta_1'       => $row['precio_venta_1'],
									'precio_venta_2'       => $row['precio_venta_2'],
									'precio_venta_3'       => $row['precio_venta_3'],
									'precio_venta_4'       => $row['precio_venta_4'],
									'precio_venta_5'       => $row['precio_venta_5'],
									'id_categoria_asiste'  => $row['id_categoria_asiste']

								);
				$whereIdItem .= ($whereIdItem=='')? " id_item='$row[id]' " : " OR id_item='$row[id]' " ;
			}

			$sqlRecipie = "SELECT
						id,
						id_item,
						codigo_item,
						code_bar_item,
						nombre_item,
						id_item_materia_prima,
						codigo_item_materia_prima,
						code_bar_item_materia_prima,
						nombre_item_materia_prima,
						cantidad_item_materia_prima,
						id_unidad_medida,
						unidad_medida
					FROM items_recetas WHERE activo=1 AND id_empresa=$this->id_empresa AND ($whereIdItem) ";
			$query=$this->mysql->query($sqlRecipie,$this->mysql->link);
			while ($row=$this->mysql->fetch_array($query)) {
				$arrayRecetas[$row['id_item']][] = array(
														'id'       => $row['id_item_materia_prima'],
														'codigo'   => $row['codigo_item_materia_prima'],
														'nombre'   => utf8_encode($row['nombre_item_materia_prima']),
														'cantidad' => $row['cantidad_item_materia_prima'],
													);
				$whereIdItem .= ($whereIdItem=='')? " id_item='$row[id_item_materia_prima]' " : " OR id_item='$row[id_item_materia_prima]' " ;
			}
			// return array('status' => true,'data'=> $row);



			if ($data['id_sucursal'] <> '' && $data['id_bodega']) {
				$sql="SELECT
						id_item,
						costos,
						precio_venta,
						cantidad
					FROM inventario_totales
					WHERE activo=1 AND id_empresa=$this->id_empresa AND ($whereIdItem) $whereItemsTotales";
				$query=$this->mysql->query($sql,$this->mysql->link);
				while ($row=$this->mysql->fetch_array($query)) {
					$arrayValoresItem[$row['id_item']] = array(
																'costo'        => $row['costos'],
																'precio_venta' => $row['precio_venta'],
																'cantidad'     => $row['cantidad'],
															);
				}
			}

			$whereIdItem = str_replace("id_item", "id_inventario", $whereIdItem);
			$sql="SELECT id,id_inventario,randomico_documento,ext FROM items_documentos WHERE tipo_documento_nombre = 'Imagen Logo' AND ($whereIdItem) ";
			$query=$this->mysql->query($sql);
			while ($row=$this->mysql->fetch_array($query)) {
				$arrayLogo[$row['id_inventario']] = "$row[randomico_documento]_$row[id].$row[ext]";
			}

			foreach ($arrayRecetas as $id_item => $arrayRecetas1) {
				foreach ($arrayRecetas1 as $key => $arrayResult) {
					$arrayRecetas[$id_item][$key]['costo']        = $arrayValoresItem[$arrayResult['id']]['costo'];
					$arrayRecetas[$id_item][$key]['precio_venta'] = $arrayValoresItem[$arrayResult['id']]['precio_venta'];
					$arrayRecetas[$id_item][$key]['stock']        = $arrayValoresItem[$arrayResult['id']]['cantidad'];
				}
			}
			// return array('status' => true,'data'=> $result);
			foreach ($result as $key => $arrayResult) {
				$result[$key]['costo']        = $arrayValoresItem[$arrayResult['id']]['costo'];
				$result[$key]['precio_venta'] = $arrayValoresItem[$arrayResult['id']]['precio_venta'];
				$result[$key]['cantidad']     = $arrayValoresItem[$arrayResult['id']]['cantidad'];
				$result[$key]['receta']       = $arrayRecetas[$arrayResult['id']];
				// continue;
				$result[$key]['logo']         = $arrayLogo[$arrayResult['id']];
			}
			// var_dump($result);
			// return array('status' => false,'detalle'=> var_dump($result));
			return array('status' => true,'data'=> $result);
		}

		public function apiResponse($response){
		    $http_response_code = array(
		        100 => 'Continue',
		        101 => 'Switching Protocols',
		        200 => 'OK',
		        201 => 'Created',
		        202 => 'Accepted',
		        203 => 'Non-Authoritative Information',
		        204 => 'No Content',
		        205 => 'Reset Content',
		        206 => 'Partial Content',
		        300 => 'Multiple Choices',
		        301 => 'Moved Permanently',
		        302 => 'Found',
		        303 => 'See Other',
		        304 => 'Not Modified',
		        305 => 'Use Proxy',
		        306 => '(Unused)',
		        307 => 'Temporary Redirect',
		        400 => 'Bad Request',
		        401 => 'Unauthorized',
		        402 => 'Payment Required',
		        403 => 'Forbidden',
		        404 => 'Not Found',
		        405 => 'Method Not Allowed',
		        406 => 'Not Acceptable',
		        407 => 'Proxy Authentication Required',
		        408 => 'Request Timeout',
		        409 => 'Conflict',
		        410 => 'Gone',
		        411 => 'Length Required',
		        412 => 'Precondition Failed',
		        413 => 'Request Entity Too Large',
		        414 => 'Request-URI Too Long',
		        415 => 'Unsupported Media Type',
		        416 => 'Requested Range Not Satisfiable',
		        417 => 'Expectation Failed',
		        500 => 'Internal Server Error',
		        501 => 'Not Implemented',
		        502 => 'Bad Gateway',
		        503 => 'Service Unavailable',
		        504 => 'Gateway Timeout',
		        505 => 'HTTP Version Not Supported',
		    );
		    // header('HTTP/1.1 ' . $response['status'] . ' ' . $http_response_code[$response['status']]);
	    	header('Content-Type: application/json; charset=utf-8');
			if (is_array($response['data'])) {
				foreach ($response['data'] as $key => $arrayResult) {
					if (is_array($arrayResult)) {
						foreach ($arrayResult as $campo => $valor) {
							if (!is_array($valor)) {
								$response['data'][$key][$campo]=utf8_encode($valor);
							}

						}
					}
				}
			}
			// print_r($response);
		    $json_response = json_encode($response['data']);
		    echo $json_response;
		    exit;
		}

		/**
		 * rollBack deshacer los cambios realizados
		 * @param  Int $id_factura Id de la factura a realizar rollback
		 * @param  Int $nivel      Nivel del rollback a realizar
		 */
		public function rollBack($id_remision,$nivel, $sentencia = NULL){
			if ($this->actionUpdate==true) {
				$sentencia = " estado=0 ";
			}
			else if($sentencia==NULL){
				$sentencia = " estado=0,consecutivo=0 " ;
			}

			if ($nivel>=1){
				$sql="UPDATE ventas_remisiones SET $sentencia WHERE id_empresa=$this->id_empresa AND id=$id_remision; ";
				$query=$this->mysql->query($sql,$this->mysql->link);

				$sql="DELETE FROM asientos_colgaap WHERE id_empresa=$this->id_empresa AND id_sucursal=$this->id_sucursal AND id_documento=$id_remision AND tipo_documento='RV'; ";
				$query=$this->mysql->query($sql,$this->mysql->link);

				$sql="DELETE FROM asientos_niif WHERE id_empresa=$this->id_empresa AND id_sucursal=$this->id_sucursal AND id_documento=$id_remision AND tipo_documento='RV'; ";
				$query=$this->mysql->query($sql,$this->mysql->link);
			}
			if ($nivel>=2){
				$sql   = "UPDATE inventario_totales AS IT,
								(
									SELECT SUM(cantidad) AS total_remision_venta, id_inventario AS id_item
									FROM ventas_remisiones_inventario
									WHERE id_remision_venta='$id_remision'
										AND activo=1
										AND inventariable='true'
									GROUP BY id_inventario
								) AS VFI
						SET IT.cantidad=IT.cantidad+VFI.total_remision_venta
						WHERE IT.id_item=VFI.id_item
	 						AND IT.activo = 1
	 						AND IT.id_ubicacion = '$this->id_bodega'; ";
				$query = $this->mysql->query($sql,$this->mysql->link);
			}
		}


	}