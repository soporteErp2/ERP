<?php

    include_once('../../../configuracion/conectar.php');
    include_once('../../../configuracion/define_variables.php');
    include_once('../../../configuracion/mimetype.php');
    include_once('../../../misc/excel/Classes/PHPExcel.php');

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

            if ($realSize != $this->getSize()){ return false; }

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

        /**
         * Returns array('success'=>true) or array('error'=>'error message')
         */
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

        function handleUpload($uploadDirectory,$id_documento,$id_bodega,$AjusteMensual,$mysql){
            if (!is_writable($uploadDirectory)){ return array('error' => "Server error. El directorio no tiene permisos de escritura : $_SERVER[HTTP_HOST]  - $_SERVER[DOCUMENT_ROOT] <> ".$uploadDirectory); }
            if (!$this->file){ return array('error' => 'No files were uploaded.'); }

            $size = $this->file->getSize();
            if ($size == 0) { return array('error' => 'Archivo vacio'); }
            if ($size > $this->sizeLimit) { return array('error' => 'Archivo muy Grande'); }

            $pathinfo = pathinfo($this->file->getName());
            $filename = $pathinfo['filename'];
            $ext      = $pathinfo['extension'];

            if($this->allowedExtensions && !in_array(strtolower($ext), $this->allowedExtensions)){
                $these = implode(', ', $this->allowedExtensions);
                return array('error' => 'File has an invalid extension, it should be one of '. $these . '.');
            }

            /*----------------------- RANDOMICO --------------------------*/
            /**************************************************************/
            // $filename = $this->randomico_maestro(); // ID UNICO

            if ($this->file->save($uploadDirectory.$filename.'.'.$ext)){

                chmod($uploadDirectory.$filename.'.'.$ext, 0777);

                $debugError     = '';
                $errorLoadFile  = '';
                $idNotaContable = 0;

                include("load_excel.php");

                unlink($uploadDirectory . $filename . '.' . $ext);

                // if($idNotaContable > 0){ return array('success'=>true, 'idNotaContable'=>$idNotaContable, 'contCuentaNoExiste'=>$contCuentaNoExiste, 'debugError'=>$debugError); }
                // else{ return array('error'=> $errorLoadFile, 'debug'=> "$debugError"); }

                if ($errorLoadFile<>'') {
                    return array('error'=>$errorLoadFile, 'nombreArchivo'=>$filename);
                }

                return array('success'=>true, 'nombreArchivo'=>$filename,"ruta"=>$uploadDirectory,"document_root"=>$_SERVER['DOCUMENT_ROOT'],"http_host"=>$_SERVER['HTTP_HOST']);
            }
            else{ return array('error'=> 'No se guardo el Documento en el servidor'); }
        }
    }

    $allowedExtensions = array();               // list of valid extensions, ex. array("jpeg", "xml", "bmp")
    $sizeLimit         = 10 * 1024 * 1024;      // max file size in bytes
    $id_host           = $_SESSION['ID_HOST'];

    //CONDICION PARA QUE FUNCIONE EN LOCAL Y EN DESARROLLO
    $rutaServer = $_SERVER['DOCUMENT_ROOT'];
    $findme     = 'LOGICALERP';
    $pos        = strpos($rutaServer, $findme);

    if (!$pos && $_SERVER['HTTP_HOST'] != 'erp.plataforma.co' && $_SERVER['HTTP_HOST'] != 'logicalsoft-erp.com' && $_SERVER['HTTP_HOST'] != 'repo.logicalsoft-erp.com') { $rutaServer = $_SERVER['DOCUMENT_ROOT'].'/LOGICALERP'; }

    $serv = $rutaServer."/";
    $url  = $serv.'ARCHIVOS_PROPIOS/empresa_'.$id_host.'/';
    if(!file_exists($url)){ mkdir ($url); }

    $url  = $serv.'ARCHIVOS_PROPIOS/empresa_'.$id_host.'/inventario/';
    if(!file_exists($url)){ mkdir ($url); }

    $url = $url.'ajuste_inventario/';
    if(!file_exists($url)){ mkdir ($url); }

    $uploader = new qqFileUploader($allowedExtensions, $sizeLimit);
    $result   = $uploader->handleUpload($url,$id_documento,$id_bodega,$AjusteMensual,$mysql);

    echo htmlspecialchars(json_encode($result), ENT_NOQUOTES);

?>

