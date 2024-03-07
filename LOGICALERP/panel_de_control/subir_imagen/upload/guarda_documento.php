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


            /*----------------------- RANDOMICO --------------------------*/
            /**************************************************************/
            $random1 = mktime();             //GENERA PRIMERA PARTE DEL ID UNICO
            $random2 = $this->randomico_maestro();
            $idUnico = $random1.''.$random2; // ID UNICO

            //primero verificamos si ya se inerto un registro y esta activo, para saber si ya hay una imagen cargada
            $sql    = "SELECT nombre FROM configuracion_imagenes_documentos WHERE activo=1 AND id_empresa=".$_SESSION['EMPRESA'];
            $query  = mysql_query($sql,$link);
            $nombre = mysql_result($query,0,'nombre');


            //subir al archivo a la ruta
            if ($this->file->save($uploadDirectory.$idUnico.'.'.$ext)){
                //al subir el archivo se verifica el tamaño del mismo
                $rutaImg=$uploadDirectory.$idUnico.'.'.$ext;
                $dimensiones = GetImageSize($rutaImg);

                //si tiene una imagen ya almacenada
                if ($nombre!='') {
                    //si la imagen subida cumnple con las medidas minimas
                    if ($dimensiones[0]>=500 && $dimensiones[1]>=200) { $sqlInsert = "UPDATE configuracion_imagenes_documentos SET nombre='$idUnico',ext='$ext' WHERE activo=1 AND id_empresa=".$_SESSION['EMPRESA']; }
                    //sino cumple con las mediddas minimas no se altera el registro
                    else{ $sqlInsert = "UPDATE configuracion_imagenes_documentos SET activo=1 WHERE activo=1 AND id_empresa=".$_SESSION['EMPRESA']; }
                }
                else{
                    //sino, se va a ingresar una nueva
                    if ($dimensiones[0]>=500 && $dimensiones[1]>=200) { $sqlInsert = "INSERT INTO configuracion_imagenes_documentos (nombre,ext,id_empresa) VALUES ('$idUnico','$ext',".$_SESSION['EMPRESA'].")"; }
                    //sino cumple con las mediddas minimas no se altera el registro
                    else{ $sqlInsert = "UPDATE configuracion_imagenes_documentos SET activo=1 WHERE activo=1 AND id_empresa=".$_SESSION['EMPRESA']; }
                }

                $filetype  = mime_content_type($uploadDirectory.$idUnico.'.'.$ext);

                 //ejecutar el query de insert o update
                if(mysql_query($sqlInsert,$link)){

                    $idRow = mysql_insert_id($link);

                    $result=array('success'=>true, 'idRow'=>$idRow,'imagen'=>$idUnico.'.'.$ext,'ruta'=>$uploadDirectory,'ancho'=>$dimensiones[0],'alto'=>$dimensiones[1],'sql'=>$sqlInsert);
                    return $result;

                }
                else{ return array('error'=> 'No se pudo guardar el Documento en la base de datos'); }

                // unlink($uploadDirectory . $filename . '.' . $ext);
            }
            else{ return array('error'=> 'No se puedo guardar el Documento en el servidor'); }
        }
    }

    // list of valid extensions, ex. array("jpeg", "xml", "bmp")
    $allowedExtensions = array();
    // max file size in bytes
    $sizeLimit = 10 * 1024 * 1024;


        $ruta = '../../../../ARCHIVOS_PROPIOS/imagenes_empresas/empresa_'.$_SESSION['ID_HOST'];
        if(!is_dir($ruta))
        {
            mkdir ($ruta);
            $ruta2 = $ruta.'/logos';
            if(!is_dir($ruta2)){ mkdir ($ruta2); }

        }
        else{
            $ruta2 = $ruta.'/logos';
            if(!is_dir($ruta2)){  mkdir ($ruta2); }
        }

        $url = $ruta2.'/';


    $uploader = new qqFileUploader($allowedExtensions, $sizeLimit);
    $result   = $uploader->handleUpload($url, $link, $id, $td);
    // to pass data through iframe you will need to encode all html tags

    echo htmlspecialchars(json_encode($result), ENT_NOQUOTES);

?>