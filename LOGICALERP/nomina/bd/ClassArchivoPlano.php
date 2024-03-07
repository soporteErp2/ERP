<?php
  include("../../../configuracion/conectar.php");
  include("../../../configuracion/define_variables.php");

  /**
   * @class ClassFacturaJSON
   */

  class ClassArchivoPlano{
    public $mysql;
    public $id_empresa;
    public $id_planilla;
    public $tabla_planilla;
    public $campos_consulta;

  	function __construct($mysql){
  		$this->mysql = $mysql;
  	}

    public function consultarDatos(){
      //aqui iran los sql que consultaran las planillas
    }

    public function generarArchivoPlano(){
      $fecha_archivo  = date('Y-m-d');
      $nombre_archivo = "Bancolombia_pago_nomina_$fecha_archivo.txt";
      $contenido      = "Aqui iran los pagos de empleados";

      header("Content-Type: text/txt");
      header("Content-Disposition: attachment; filename=$nombre_archivo");
      echo $contenido;
    }
  }
  $archivoPlano = new ClassArchivoPlano($mysql);
  $archivoPlano->generarArchivoPlano();
?>
