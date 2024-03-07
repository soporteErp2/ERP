<?php
	include("../../../configuracion/conectar.php");
	include("../../../configuracion/define_variables.php");
	include("../../../configuracion/mimetype.php");

    $id_empresa = $_SESSION['EMPRESA'];
    $id_host    = $_SESSION['ID_HOST'];

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

        function randomico_maestro(){
            $chars = array(
                    'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H',
                    'I', 'J', 'K', 'L', 'M', 'N', 'O',
                    'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W',
                    'X', 'Y', 'Z', '1', '2', '3', '4', '5',
                    '6', '7', '8', '9', '0'
                    );
            $max_chars = count($chars) - 1;
            srand((double) microtime()*1000000);
            $rand_str = '';
            for($i=0; $i < 6; $i++){ $rand_str = $rand_str . $chars[rand(0, $max_chars)]; }
            return $rand_str;
        }

        /**
         * Returns array('success'=>true) or array('error'=>'error message')
         */
        function handleUpload($uploadDirectory, $link, $MyId, $MyTd, $replaceOldFile = TRUE){
            if (!is_writable($uploadDirectory)){
                return array('error' => "Server error. El directorio no tiene permisos de escritura");
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

            // VERIFICAR EL ESPACIO DISPONIBLE DE ALMACENAMIENTO
            $folderSize = getFolderSize($_SESSION['ID_HOST'],'../../../');
            // KB
            $size /= 1024;
            // MB
            $size /= 1024;
            if (($size+$folderSize)>$_SESSION['ALMACENAMIENTO']) {
                return array('error' => 'No hay espacio de almacenamiento suficiente');
            }

    	    $pathinfo = pathinfo($this->file->getName());
    		$filename = $pathinfo['filename'];
            //$filename = md5(uniqid());
            $ext = $pathinfo['extension'];

    	    if($this->allowedExtensions && !in_array(strtolower($ext), $this->allowedExtensions)){
                $these = implode(', ', $this->allowedExtensions);
                return array('error' => 'File has an invalid extension, it should be one of '. $these . '.');
            }

            // if(!$replaceOldFile){
            //     /// don't overwrite previous files that were uploaded
            //     while (file_exists($uploadDirectory . $filename . '.' . $ext)) {
            //         $filename .= rand(10, 99);
            //     }
            // }

            $random1 = mktime();             //GENERA PRIMERA PARTE DEL ID UNICO
            $random2 = $this->randomico_maestro();
            $idUnico = $random1.''.$random2; // ID UNICO

            $sqlInsert="INSERT INTO empleados_documentos (id_empleado,tipo_documento,ext,fecha_creacion,randomico_documento,nombre_documento)
                        VALUES($MyId,$MyTd,'$ext',now(),'$idUnico','$filename')";

            if(mysql_query($sqlInsert,$link)){
                $idRow = mysql_insert_id($link);

                if ($this->file->save($uploadDirectory.$idUnico.'_'.$idRow.'.'.$ext)){

                    //Abriendo el Archivo imagen.
                    // $fp = fopen($uploadDirectory . $filename . '.' . $ext, "rb");
                    // $tfoto = fread($fp, filesize($uploadDirectory . $filename . '.' . $ext));
                    // $tfoto = addslashes($tfoto);
                    // fclose($fp);

                    $filetype  = mime_content_type($uploadDirectory.$idUnico.'_'.$idRow.'.'.$ext);
                    $sqlUpdate = "UPDATE empleados_documentos SET document_type='$filetype' WHERE id=$idRow";
                    mysql_query($sqlUpdate,$link);

                    $result = array('success'=>true, 'idRow'=>$idRow);
                    return $result;

                    // unlink($uploadDirectory . $filename . '.' . $ext);
                    //Guardando los datos binarios de la imagen
                    // $SQL = "INSERT INTO empleados_documentos (id_empleado,tipo_documento,documento,ext,document_type,fecha_creacion) VALUES ()";
                    // if(mysql_query($SQL,$link)){
                    //     $idRow = mysql_insert_id($link);
                    //     return array('success'=>true, 'idRow'=>$idRow);
                    // }

                }
                else{ return array('error'=> "No se pudo guardar el Documento en la base de datos"); }
            }
            else{ return array('error'=> 'No se puedo guardar el Documento en el servidor'); }
        }
    }

    // list of valid extensions, ex. array("jpeg", "xml", "bmp")
    $allowedExtensions = array();
    // max file size in bytes
    $sizeLimit = 10 * 1024 * 1024;

    //CONDICION PARA QUE FUNCIONE EN LOCAL Y EN DESARROLLO
    $rutaServer = $_SERVER['DOCUMENT_ROOT'];
    // $findme     = 'LOGICALERP';
    // $pos        = strpos($rutaServer, $findme);
    // if (!$pos) { $rutaServer=$_SERVER['DOCUMENT_ROOT'].'/LOGICALERP'; }

    $serv  = $rutaServer."/";
    $ruta1 = $serv.'ARCHIVOS_PROPIOS/empresa_'.$id_host;
    if(!file_exists($ruta1)){ mkdir ($ruta1); }


    $ruta2 = $serv.'ARCHIVOS_PROPIOS/empresa_'.$id_host.'/empleados';
    $url   = $ruta2.'/';
    if(!file_exists($ruta2)){ mkdir ($ruta2); }

    $uploader = new qqFileUploader($allowedExtensions, $sizeLimit);
    $result   = $uploader->handleUpload($url, $link, $id, $td);
    // to pass data through iframe you will need to encode all html tags

    echo htmlspecialchars(json_encode($result), ENT_NOQUOTES);

?>

