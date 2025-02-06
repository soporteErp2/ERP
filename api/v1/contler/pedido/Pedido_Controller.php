<?php

require '../../Clases/ApiFunctions.php';

class Pedido_Controller extends ApiFunctions
{
    private $configuration_data = null;
    private $cash_register = null;


    public function show(){
        echo json_encode([1,2,3]);
    }

    public function get_config(){
        $sql = "SELECT data FROM configuracion_general WHERE modulo='panel_de_control/contler' AND activo=1 AND id_empresa=$this->id_empresa";
        $query = $this->mysql->query($sql);
        $response = $this->mysql->result($query,0,'data');
        $this->configuration_data = json_decode($response,true);
    }

    public function is_open_cash_register(){
        $sql="SELECT
						id,
						id_caja,
						nombre_caja,
						id_seccion,
						seccion
					FROM ventas_pos_cajas_secciones
					WHERE activo=1
					AND id_empresa=$this->id_empresa
					AND id_seccion=".$this->configuration_data['section'];
        $query=$this->mysql->query($sql);
        $estadoCaja = 'Cerrada';
        while ($row=$this->mysql->fetch_array($query)) {
            $arrayRest[] = array('id' => $row['id_caja'], 'nombre' => $row['nombre_caja'] , 'id_seccion' => $row['id_seccion'] );
        }
        if (count($arrayRest)>0) {
            if (count($arrayRest)==1) {
                // $jsonEstado = $this->getCashRegisterState($arrayRest[0]['id']);
                // $jsonEstado = json_decode($jsonEstado,true);
                $sql   = "SELECT estado
                            FROM ventas_pos_cajas_movimientos
                            WHERE activo=1
                            AND id_caja = ".$arrayRest[0]['id']."
                            AND id_empresa=$this->id_empresa
                            AND estado='Abierta' ";
                $query=$this->mysql->query($sql);
                $estadoCaja = $this->mysql->result($query,0,'estado');
            }
            // print_r($arrayRest);
            $arrayResult = array('status' => 'success', 'cajas'=> $arrayRest, 'estado_caja_unica'=> $estadoCaja, 'id_caja_unica'=>$arrayRest[0]['id'], 'nombre_caja_unica' =>$arrayRest[0]['nombre'] , 'id_seccion' =>$arrayRest[0]['id_seccion'] );
        }
        else{
            $arrayResult = array('status' => 'failed', 'message'=>'No hay cajas configuradas' );
        }
        $this->cash_register = $arrayResult;
        return $arrayResult['estado_caja_unica'];
    }

    public function store($data){
        $this->get_config();
        if(!$this->is_open_cash_register()){
            return ["status"=>false,"detalle"=>"la caja del restaurante esta cerrada!"];
        }
        
        // echo $this->configuration_data['section'];
        echo json_encode($data);
    }
}