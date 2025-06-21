<?php

	include_once '../../Clases/ApiFunctions.php';


	/**
	 * @apiDefine Items No se requieren permisos especiales
	 */
	class apiCategoriaItems extends ApiFunctions
	{
		/**
		 * @api {get} /items/categoria_items/:id_empresa Consultar Items
		 * @apiVersion 1.0.0
		 * @apiDescription Consultar categorias de items del sistema.
		 * @apiName get_categorias_items
		 * @apiGroup Items
		 *
		 * @apiSuccess {Int} id Id interno de la categoria
		 * @apiSuccess {String} codigo Codigo de la categoria
		 * @apiSuccess {String} nombre Nombre de la categoria
         * 
		 * @apiSuccessExample Success-Response:
		 *     HTTP/1.1 200 OK
		 *     [
	     * 	        {
	     * 	            "id": "200",
	     * 	            "codigo": "01010504",
	     * 	            "nombre": "PANTALLAS "
	     * 	        },
	     * 	        {
	     * 	            "id": "329",
	     * 	            "codigo": "01010871",
	     * 	            "nombre": "CORE DE NEGOCIOS"
	     * 	        },
	     * 	        {
	     * 	            "id": "274",
	     * 	            "codigo": "01010816",
	     * 	            "nombre": "MICROFONOS"
	     * 	        }
	     * 	    ]
		 *
		 */
		public function show(){
            $result = [];

			$sql="SELECT id,codigo,nombre
				 FROM categorias_items 
				 WHERE activo=1 AND id_empresa=$this->id_empresa ORDER BY id";
			$query=$this->mysql->query($sql,$this->mysql->link);

            if(!$query){
    			$response = array('status' => false,'detalle'=> "No se pudieron consultar la categoria de los items");
                return $response;
            }

			while ($row=$this->mysql->fetch_assoc($query)){
                $result[] = $row;
			}

			$response = array('status' => true,'data'=> $result);
			return $response;
		}

		

	}