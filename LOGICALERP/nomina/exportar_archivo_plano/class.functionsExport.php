<?php
	// include("../../../configuracion/conectar.php");
	// include("../../../configuracion/define_variables.php");

    /**
    *
    */
    class functionsExport
    {

        protected function get_tipo_documento($tipo_doc){
            $tipo_doc = str_replace('.', '', $tipo_doc);
            $arrayConvert[0]=array('tipo'=>'NI', 'descripcion' => 'Número de identificación tributaria');
            $arrayConvert[1]=array('tipo'=>'CC', 'descripcion' => 'Cédula de ciudadanía');
            $arrayConvert[2]=array('tipo'=>'CE', 'descripcion' => 'Cédula de extranjería');
            $arrayConvert[3]=array('tipo'=>'TI', 'descripcion' => 'Tarjeta de identidad');
            $arrayConvert[4]=array('tipo'=>'RC', 'descripcion' => 'Registro civil');
            $arrayConvert[5]=array('tipo'=>'PA', 'descripcion' => 'Pasaporte');

            $search0 = strpos($tipo_doc, 'NIT');
            $search1 = strpos($tipo_doc, 'Pasaporte');
            if ($search0!==false) {
                $tipo_doc = $arrayConvert[0]['tipo'];
            }
            elseif ($search1!==false) {
                $tipo_doc = $arrayConvert[5]['tipo'];
            }

            return $tipo_doc;

        }

    }

?>