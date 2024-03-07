<?php
	include("../../../configuracion/conectar.php");
	include("../../../configuracion/define_variables.php");

    /**
     * Handle file uploads via XMLHttpRequest
     */
    class qqUploadedFileXhr {
        /**
         * Save the file to the specified path
         * @return boolean TRUE on success
         */
        function save($path) {
            $input = fopen("php://input", "wb");
            $temp = tmpfile();
            $realSize = stream_copy_to_stream($input, $temp);
            fclose($input);

            if ($realSize != $this->getSize()){
                return false;
            }

            $target = fopen($path, "w");
            fseek($temp, 0, SEEK_SET);
            stream_copy_to_stream($temp, $target);
            fclose($target);

            return true;
        }
        function getName() {
            return $_GET['qqfile'];
        }
        function getSize() {
            if (isset($_SERVER["CONTENT_LENGTH"])){
                return (int)$_SERVER["CONTENT_LENGTH"];
            } else {
                throw new Exception('Getting content length is not supported.');
            }
        }
    }
    /**
     * Handle file uploads via regular form post (uses the $_FILES array)
     */
    class qqUploadedFileForm {
        /**
         * Save the file to the specified path
         * @return boolean TRUE on success
         */
        function save($path) {
            if(!move_uploaded_file($_FILES['qqfile']['tmp_name'], $path)){
                return false;
            }
            return true;
        }
        function getName() {
            return $_FILES['qqfile']['name'];
        }
        function getSize() {
            return $_FILES['qqfile']['size'];
        }
    }

    class qqFileUploader {
        private $allowedExtensions = array();
        private $sizeLimit = 10485760;
        private $file;

        function __construct(array $allowedExtensions = array(), $sizeLimit = 10485760){
            $allowedExtensions = array_map("strtolower", $allowedExtensions);

            $this->allowedExtensions = $allowedExtensions;
            $this->sizeLimit = $sizeLimit;

            $this->checkServerSettings();

            if (isset($_GET['qqfile'])) {
                $this->file = new qqUploadedFileXhr();
            } elseif (isset($_FILES['qqfile'])) {
                $this->file = new qqUploadedFileForm();
            } else {
                $this->file = false;
            }
        }

        private function checkServerSettings(){
            $postSize = $this->toBytes(ini_get('post_max_size'));
            $uploadSize = $this->toBytes(ini_get('upload_max_filesize'));

            if ($postSize < $this->sizeLimit || $uploadSize < $this->sizeLimit){
                $size = max(1, $this->sizeLimit / 1024 / 1024) . 'M';
                die("{'error':' Incremente los siguientes valores en su php.ini -> post_max_size y upload_max_filesize a $size'}");
            }
        }

        private function toBytes($str){
            $val = trim($str);
            $last = strtolower($str[strlen($str)-1]);
            switch($last) {
                case 'g': $val *= 1024;
                case 'm': $val *= 1024;
                case 'k': $val *= 1024;
            }
            return $val;
        }

        /**
         * Returns array('success'=>true) or array('error'=>'error message')
         */
        function handleUpload($uploadDirectory, $link, $MyId, $replaceOldFile = TRUE){
            if (!is_writable($uploadDirectory)){
                return array('error' => "Server error. El directorio \"".$uploadDirectory."\" no tiene permisos de escritura");
            }
            if (!$this->file){
                return array('error' => 'No files were uploaded.');
            }
            $size = $this->file->getSize();
            if ($size == 0) {
                return array('error' => 'Archivo vacio');
            }
            if ($size > $this->sizeLimit) {
                return array('error' => 'Archivo muy Grande');
            }

    	    $pathinfo = pathinfo($this->file->getName());
            $filename = $pathinfo['filename'];
            //$filename = md5(uniqid());
            $ext = $pathinfo['extension'];

    	    if($this->allowedExtensions && !in_array(strtolower($ext), $this->allowedExtensions)){
                $these = implode(', ', $this->allowedExtensions);
                return array('error' => 'File has an invalid extension, it should be one of '. $these . '.');
            }

            if(!$replaceOldFile){
                /// don't overwrite previous files that were uploaded
                while (file_exists($uploadDirectory . $filename . '.' . $ext)) {
                    $filename .= rand(10, 99);
                }
            }
            if ($this->file->save($uploadDirectory . $filename . '.' . $ext)){

    			function redim($ruta1,$ruta2,$ancho,$alto){

    				$datos=getimagesize ($ruta1); # se obtene la dimension y tipo de imagen
    				$ancho_orig = $datos[0]; # Anchura de la imagen original
    				$alto_orig = $datos[1];    # Altura de la imagen original
    				$tipo = $datos[2];

    				if($tipo==1){if(function_exists("imagecreatefromgif")){$img = imagecreatefromgif($ruta1);}else{return false;}} # GIF
    				else if ($tipo==2){if(function_exists("imagecreatefromjpeg")){$img = imagecreatefromjpeg($ruta1);}else{return false;}} # JPG
    				else if ($tipo==3){if(function_exists("imagecreatefrompng")){$img = imagecreatefrompng($ruta1);}else{return false;}} # PNG

    				if ($ancho_orig>$alto_orig) { # Se calculan las nuevas dimensiones de la imagen
    					$ancho_dest=$ancho;
    					$alto_dest=($ancho_dest/$ancho_orig)*$alto_orig;
    				}else{
    					$alto_dest=$alto;
    					$ancho_dest=($alto_dest/$alto_orig)*$ancho_orig;
    				}
    				$img2=@imagecreatetruecolor($ancho_dest,$alto_dest) or $img2=imagecreate($ancho_dest,$alto_dest); // imagecreatetruecolor, solo estan en G.D. 2.0.1 con PHP 4.0.6+
    				@imagecopyresampled($img2,$img,0,0,0,0,$ancho_dest,$alto_dest,$ancho_orig,$alto_orig) or imagecopyresized($img2,$img,0,0,0,0,$ancho_dest,$alto_dest,$ancho_orig,$alto_orig);// Redimensionar // imagecopyresampled, solo estan en G.D. 2.0.1 con PHP 4.0.6+
    				// Crear fichero nuevo, según extensión.
    				if($tipo==1){if(function_exists("imagegif")){imagegif($img2, $ruta2);}else{return false;}} // GIF
    				if($tipo==2){if(function_exists("imagejpeg")){imagejpeg($img2, $ruta2);}else{return false;}} // JPG
    				if($tipo==3){if(function_exists("imagepng")){imagepng($img2, $ruta2);}else{return false;}} // PNG

    				return true;
    			}

    			$imagen = $uploadDirectory . $filename . '.' . $ext;
    			# ruta de la imagen final, si se pone el mismo nombre que la imagen, esta se sobreescribe
    			$imagen_final = $uploadDirectory . $filename . '.' . $ext;
    			$ancho_nuevo = 103;
    			$alto_nuevo = 133;

    			redim ($imagen,$imagen_final,$ancho_nuevo,$alto_nuevo);

    			//Abriendo el Archivo imagen.
    			$fp = fopen($uploadDirectory . $filename . '.' . $ext, "rb");
    			$tfoto = fread($fp, filesize($uploadDirectory . $filename . '.' . $ext));
    			$tfoto = addslashes($tfoto);
    			fclose($fp);
    			unlink($imagen);
    			//Guardando los datos binarios de la imagen
    			if(mysql_query("UPDATE empleados SET foto='$tfoto' WHERE id=$MyId",$link)){

    				return array('success'=>true);
    			}else{
    				return array('error'=> 'No se pudo guardar la imagen en la base de datos');
    			}
            }else{
                return array('error'=> 'No se puedo guardar la imagen en el servidor');
            }
        }
    }

    // list of valid extensions, ex. array("jpeg", "xml", "bmp")
    $allowedExtensions = array();
    // max file size in bytes
    $sizeLimit = 10 * 1024 * 1024;

    $url = '../../../ARCHIVOS_PROPIOS/temp/';
    $uploader = new qqFileUploader($allowedExtensions, $sizeLimit);
    $result = $uploader->handleUpload($url, $link, $id);
    // to pass data through iframe you will need to encode all html tags

    echo htmlspecialchars(json_encode($result), ENT_NOQUOTES);

?>

