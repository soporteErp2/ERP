<?php

	include_once '../../Clases/ApiFunctions.php';
	/**
	 * @apiDefine adjuntar documentos en factura de compra
	 *
	 */
	class ApiArchivosAdjuntos extends ApiFunctions
	{	
		public $valid_extensions = ["doc","docx","xls","xlsx","pdf","txt","jpg","jpeg","png"];
		/**
		 * @api {post} /facturas_compras/archivos_adjuntos Adjuntar archivo
		 * @apiVersion 1.0.0
		 * @apiDescription Adjuntar archivo a una factura de compra (form-data)
		 * @apiName attach_facturas
		 * @apiPermission Compras
		 * @apiGroup Facturas_compras
		 *
		 *
		 * @apiParam {int} consecutivo Numero de consecutivo de la factura de compra
		 * @apiParam {int} id_sucursal Id de la sucursal de a factura
		 * @apiParam {file} adjunto Archivo a adjuntar enviado como form-data
		 * 
		 * @apiSuccess {200} success  Archivo adjunto almacenado 
		 *
		 * @apiSuccessExample Success-Response:
		 *     HTTP/1.1 200 OK
		 *     {
		 *        "success": "Archivo adjunto almacenado "
		 *     }
		 * @apiErrorExample Error-Response:
		 * HTTP/1.1 400 Bad Response
		 * {
		 *  "failure": "Ha ocurrido un error",
		 *   "detalle": "detalle del o los errores"
		 * }
		 *
		 * @apiError failure Ha ocurrido un error
		 * @apiError detalle
		 *     HTTP/1.1 400 Bad Response
		 *     {
		 *     	"failure":"Ha ocurrido un error",
		 *     "detalle": "detalle del error"
		 *     }
		 */
		public function store($data=NULL){

			$name = explode(".",$_FILES["adjunto"]["name"]);
			if(!in_array(strtolower(end($name)),$this->valid_extensions)){
				return array(
					'status'  => false,
					'detalle' => "La extension ".end($name)." no es permitida, solo se permite: ".implode(",",$this->valid_extensions),
				);
			}		

			if (!isset($_POST['consecutivo']) || $_POST['consecutivo']=='' || $_POST['consecutivo']==0 ){ return array('status'=>false,'detalle'=>'el campo consecutivo es obligatorio'); }
			if (!isset($_POST['id_sucursal']) || $_POST['id_sucursal']=='' || $_POST['id_sucursal']==0 ){ return array('status'=>false,'detalle'=>'el campo id_sucursal es obligatorio'); }

			$consecutivo = $_POST['consecutivo'];
			$id_sucursal = $_POST['id_sucursal'];

			$sql="SELECT id,estado,id_proveedor FROM compras_facturas WHERE activo=1 AND consecutivo=$consecutivo AND id_sucursal=$id_sucursal";
			$query=$this->mysql->query($sql);
			$id_documento = $this->mysql->result($query,0,"id");
			$estado_documento = $this->mysql->result($query,0,"estado");
			$id_proveedor = $this->mysql->result($query,0,"id_proveedor");

			if ( $id_documento==0 || $id_documento=='' ){ return array('status'=>false,'detalle'=>'No se encontro una factura con esos datos'); }
			if ( in_array($estado_documento,[3]) ){ return array('status'=>false,'detalle'=>'se encontro la factura pero esta cancelada y no se puede adjuntar archivos'); }
			

			
			// compras_facturas_archivos_adjuntos

			$rutaServer = $_SERVER['DOCUMENT_ROOT'];

			$serv  = $rutaServer."/";
			$ruta1 = $serv.'ARCHIVOS_PROPIOS/empresa_'.$this->idHost;
			if(!file_exists($ruta1)){ mkdir ($ruta1); }

			$ruta2 = $serv.'ARCHIVOS_PROPIOS/empresa_'.$this->idHost.'/compras';
			$url   = $ruta2.'/';
			if(!file_exists($ruta2)){ mkdir ($ruta2); }

			$ruta3 = $serv.'ARCHIVOS_PROPIOS/empresa_'.$this->idHost.'/compras/facturas';
			$url   = $ruta3.'/';
			if(!file_exists($ruta3)){ mkdir ($ruta3); }

			if (!is_writable($url)){
				return array('status'=>false,'detalle'=>'el directorio no tiene permisos de escritura contacte con soporte'); 
            }

			// id_factura_compra
			// nombre_archivo
			// ext
			// id_tercero
			// id_usuario
			// id_empresa

			if(!move_uploaded_file($_FILES["adjunto"]["tmp_name"], "$url".$_FILES["adjunto"]["name"])){
				return array('status'=>false,'detalle'=>"error al cargar ".$_FILES["adjunto"]["name"]." en la ruta $url/ del archivo temporal ".$_FILES["adjunto"]["tmp_name"]); 
			}
			
			

			$sql = "INSERT INTO compras_facturas_archivos_adjuntos
					(
						id_factura_compra,
						nombre_archivo,
						ext,
						id_tercero,
						id_usuario,
						id_empresa
					)
					VALUES
					(
						'$id_documento',
						'".$name[0]."',
						'".$name[1]."',
						'$id_proveedor',
						'".$this->id_usuario."',
						'".$this->id_empresa."'
					)";
			$query=$this->mysql->query($sql);
			if(!$query){
				return array('status'=>false,'detalle'=>"error al guardar en la bd el archivo ".$_FILES["adjunto"]["name"]); 
			}
			
			return array(
				'status'  => true,
				'detalle' => "",
			);
            
		}

		

	}