<?php

    include("../../../../configuracion/conectar.php");
    include("../../../../configuracion/define_variables.php");
    include("../../../../configuracion/mimetype.php");

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
        function getName() { return $_GET['qqfile']; }
        function getSize() {
            if (isset($_SERVER["CONTENT_LENGTH"])){ return (int)$_SERVER["CONTENT_LENGTH"]; }
            else { throw new Exception('Getting content length is not supported.'); }
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
            if(!move_uploaded_file($_FILES['qqfile']['tmp_name'], $path)){ return false; }
            return true;
        }
        function getName() { return $_FILES['qqfile']['name']; }
        function getSize() { return $_FILES['qqfile']['size']; }
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

            if (isset($_GET['qqfile'])) { $this->file = new qqUploadedFileXhr(); }
            elseif (isset($_FILES['qqfile'])) {  $this->file = new qqUploadedFileForm(); }
            else { $this->file = false; }
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

            //RANDOMICO 1
            $random1 = mktime();             //GENERA PRIMERA PARTE DEL ID UNICO

            //RANDOMICO 2
            $chars = array(
                    'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H',
                    'I', 'J', 'K', 'L', 'M', 'N', 'O',
                    'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W',
                    'X', 'Y', 'Z', '1', '2', '3', '4', '5',
                    '6', '7', '8', '9', '0'
                    );
            $max_chars = count($chars) - 1;
            srand((double) microtime()*1000000);
            $random2 = '';
            for($i=0; $i < 6; $i++){ $random2 = $random2 . $chars[rand(0, $max_chars)]; }

            return $random1.''.$random2;;
        }

        function handleUpload($uploadDirectory,$consecutivo,$id_empleado){
            $id_usuario = $_SESSION['IDUSUARIO'];
            $id_empresa = $_SESSION["EMPRESA"];

            $pathinfo = pathinfo($this->file->getName());
            $filename = $pathinfo['filename'];
            $ext      = $pathinfo['extension'];

            $filename = str_replace(' ', '_', $filename);

            $sql_select_id         = "SELECT id FROM compras_requisicion WHERE consecutivo = '$consecutivo' AND id_empresa = '$id_empresa'";
            $query_id              = mysql_query($sql_select_id);
            $id_requisicion_compra = mysql_result($query_id,0,'id');


            $sqlInsertDocumento = "INSERT INTO compras_requisicion_documentos (id_requisicion_compra, nombre, ext, id_usuario,fecha)
                                    VALUES ('$id_requisicion_compra','$filename','$ext','$id_usuario',NOW())";

            $queryInsertDocumento = mysql_query($sqlInsertDocumento);

            if (!is_writable($uploadDirectory)){ return array('error' => "Server error. El directorio no tiene permisos de escritura "); }
            if (!$this->file){ return array('error' => 'No files were uploaded.'); }

            $size = $this->file->getSize();
            if ($size == 0) { return array('error' => 'Archivo vacio'); }
            if ($size > $this->sizeLimit) { return array('error' => 'Archivo muy Grande'); }

            // $pathinfo = pathinfo($this->file->getName());
            // $filename = $pathinfo['filename'];
            // $ext      = $pathinfo['extension'];

            // $filename = str_replace(' ', '_', $filename);

            if($this->allowedExtensions && !in_array(strtolower($ext), $this->allowedExtensions)){
                $these = implode(', ', $this->allowedExtensions);
                return array('error' => 'File has an invalid extension, it should be one of '. $these . '.');
            }

            $sqlInsertId = "SELECT LAST_INSERT_ID()";
            $idInsert    = mysql_result(mysql_query($sqlInsertId),0,0);
            $filenameMd5 = md5($filename);
            $filenameMd5 = $filenameMd5.'_'.$idInsert.'.'.$ext;

            if ($this->file->save($uploadDirectory.$filenameMd5)){ return array('success'=>true, 'idInsert'=>$idInsert,'filenameMd5'=>$filenameMd5, 'filename'=>$filename); }
            else{ return array('error'=> 'No se guardo el Documento en el servidor'); }
        }
    }

    $allowedExtensions = array();               // list of valid extensions, ex. array("jpeg", "xml", "bmp")
    $sizeLimit         = 10 * 1024 * 1024;      // max file size in bytes
    $id_host           = $_SESSION['ID_HOST'];

    //CONDICION PARA QUE FUNCIONE EN LOCAL Y EN DESARROLLO
    // $findme     = 'LOGICALERP';
    // $pos        = strpos($rutaServer, $findme);
    //if (!$pos && $_SERVER['HTTP_HOST'] != 'erp.plataforma.co') { $rutaServer=$_SERVER['DOCUMENT_ROOT'].'/LOGICALERP'; }

    $rutaServer = $_SERVER['DOCUMENT_ROOT'];
    $serv  = $rutaServer."/";
    $ruta1 = $serv.'ARCHIVOS_PROPIOS/empresa_'.$id_host;
    if(!file_exists($ruta1)){ mkdir ($ruta1); }

    $ruta2 = $serv.'ARCHIVOS_PROPIOS/empresa_'.$id_host.'/compras';
    $url   = $ruta2.'/';
    if(!file_exists($ruta2)){ mkdir ($ruta2); }

    $ruta3 = $serv.'ARCHIVOS_PROPIOS/empresa_'.$id_host.'/compras/requisicion';
    $url   = $ruta3.'/';
    if(!file_exists($ruta3)){ mkdir ($ruta3); }

    ////////////////////////////
    // $serv = $_SERVER['DOCUMENT_ROOT']."/";
    // $url  = $serv.'ARCHIVOS_PROPIOS/documentos_ordenes_compra/';
    // if(!file_exists($url)){ mkdir ($url); }

    // $url = $url.'empresa_'.$id_host.'/';
    // if(!file_exists($url)){ mkdir ($url); }
    ////////////////////////////

    $uploader = new qqFileUploader($allowedExtensions, $sizeLimit);
    $result   = $uploader->handleUpload($url,$consecutivo,$id_empleado);

    echo htmlspecialchars(json_encode($result), ENT_NOQUOTES);

?>
