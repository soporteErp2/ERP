<?php
include("../../../../configuracion/conectar.php");
include('../../../../configuracion/define_variables.php');

switch ($op) {

///////		FORMATOS DESCARGABLES		//////////////////////////////////////////////////

	  case "ventanaConfiguracionActividadCRM": 
      ventanaConfiguracionActividadCRM($id,$mysql,$link);
      break; 
  
    case "guardarConfiguracionActividadCRM":
      guardarConfiguracionActividadCRM($id,$departamento_act_crm,$nombre_act_crm,$fecha_completa_act_crm,$fecha_vencimiento_act_crm,$icono_act_crm,$genera_visita_act_crm,$genera_llamada_act_crm,$copia_act_crm,$mysql,$link);
      break;  

    case 'deleteActivity':
      deleteActivity($id,$mysql);
      break;

}

function ventanaConfiguracionActividadCRM($id,$mysql,$link){

    $comboDepartamentos = '';

    if ($id>0) {
      $sql   = "SELECT * FROM crm_configuracion_actividades WHERE id = $id";
      $query = $mysql->query($sql);
      $row   = $mysql->fetch_array($query); 
    }

    $sql_0   = "SELECT * FROM crm_configuracion_actividades_departamentos WHERE activo = 1";
    $query_0 = $mysql->query($sql_0);
    
    while($row_0  = $mysql->fetch_array($query_0)){
        $selected = '';
        if($row_0['id'] == $row['id_departamento']){ $selected = 'selected'; } 
        $comboDepartamentos .= '<option value="'.$row_0['id'].'" '.$selected.'>'.$row_0['nombre'].'</option>';
    } 

    ?>

    <style>
        .divIconos{
          background-repeat: no-repeat;
          width:18px;
          height:18px;
          float:left;
          margin: 4px 4px 4px 4px;
        }
        .contenedorIconos{
          width:28px;
          height:28px;
          float:left;
          margin-right: 2px;
          cursor:pointer;
          margin-right: 6px;
          /*background-color: green;    */
        }
    </style>

    <div id="render_actividades_crm"></div>
    <div style="width:100%;height:30px;padding-top:7px">
      <div style="float:left;padding-left:10px;width:90px">Departamento:</div>
      <div style="float:left;padding-left:5px;">             
           <select id="departamento_act_crm" type="text" class="myfield" style="width:215px">
              <?php echo $comboDepartamentos ?>
           </select>
      </div>
    </div>
    <div style="width:100%;height:30px;padding-top:7px">
      <div style="float:left;padding-left:10px;width:90px">Nombre:</div>
      <div style="float:left;padding-left:5px;">                 
           <input id="nombre_act_crm" type="text" class="myfield" style="width:215" value="<?php echo $row['nombre'] ?>"/>
      </div>
    </div>
    <div style="width:100%;height:30px;padding-top:7px">
      <div style="float:left;padding-left:10px;width:90px">Fecha Completa:</div>
      <div style="float:left;padding-left:5px;">                 
           <select id="fecha_completa_act_crm" type="number" class="myfield" style="width:60px">
               <option value="inline">Si</option>
               <option value="none">No</option>
           </select>
      </div>
      <div style="float:left;padding-left:10px;width:80px">Vencimiento:</div>
      <div style="float:left;padding-left:5px;">                 
           <select id="fecha_vencimiento_act_crm" type="text" class="myfield" style="width:60px">
               <option value="inline">Si</option>
               <option value="none">No</option>
           </select>
      </div>
    </div>          
    <div style="width:100%;height:30px;padding-top:7px;padding-bottom:6px">
      <div style="float:left;padding-left:10px;width:90px">Icono:</div>
      <div style="float:left;padding-left:5px;">                 
           <input id="icono_act_crm" type="hidden" class="myfield" style="width:60px" value="<?php echo $row['icono'] ?>"/>
           <div id="divIconos" style="float:left;width:220px">
               <div class ="contenedorIconos" id="icon1" onclick="selectIcon(this)" style="border:1px solid">
                   <div class = "divIconos" style="background-image:url(../calendario/images/t1B.png);"></div>
               </div>
               <div class ="contenedorIconos" id="icon2" onclick="selectIcon(this)" style="border:1px solid">
                 <div class = "divIconos" style="background-image:url(../calendario/images/t2B.png);"></div>
               </div>
               <div class ="contenedorIconos" id="icon3" onclick="selectIcon(this)" style="border:1px solid">
                 <div class = "divIconos" style="background-image:url(../calendario/images/t3B.png);"></div>
               </div>
               <div class ="contenedorIconos" id="icon4" onclick="selectIcon(this)" style="border:1px solid">
                 <div class = "divIconos" style="background-image:url(../calendario/images/t4B.png);"></div>
               </div>
               <div class ="contenedorIconos" id="icon5" onclick="selectIcon(this)" style="border:1px solid">
                 <div class = "divIconos" style="background-image:url(../calendario/images/t5B.png);"></div>
               </div>
               <div class ="contenedorIconos" id="icon6" onclick="selectIcon(this)" style="border:1px solid">
                 <div class = "divIconos" style="background-image:url(../calendario/images/t6B.png);"></div>
               </div>
           </div>
      </div>      
    </div>           
    <div style="width:100%;height:30px;padding-top:10px">
      <div style="float:left;padding-left:10px;width:90px">Genera Visita:</div>
      <div style="float:left;padding-left:5px;">                 
           <select id="genera_visita_act_crm" type="number" class="myfield" style="width:60px">
               <option value="true">Si</option>
               <option value="false">No</option>
           </select>
      </div>
      <div style="float:left;padding-left:10px;width:80px">Llamada:</div>
      <div style="float:left;padding-left:5px;">                 
           <select id="genera_llamada_act_crm" type="number" class="myfield" style="width:60px">
               <option value="true">Si</option>
               <option value="false">No</option>
           </select>
      </div>
    </div>          
    <div style="width:100%;height:30px;padding-top:7px">            
      <div style="float:left;padding-left:10px;width:90px">CRM Obligatorio:</div>
      <div style="float:left;padding-left:5px;">                 
           <select id="copia_act_crm" type="text" class="myfield" style="width:60px">
               <option value="true">Si</option>
               <option value="false">No</option>
           </select>
      </div>            
    </div> 
    <script>
        document.getElementById("fecha_completa_act_crm").value    = "<?php echo $row['fecha_completa'] ?>"
        document.getElementById("fecha_vencimiento_act_crm").value = "<?php echo $row['fecha_vencimiento'] ?>"  
        document.getElementById("genera_visita_act_crm").value     = "<?php echo $row['genera_visita'] ?>"         
        document.getElementById("genera_llamada_act_crm").value    = "<?php echo $row['genera_llamada'] ?>"         
        document.getElementById("copia_act_crm").value             = "<?php echo $row['copiar_crm_obligatorio'] ?>"

        var icono = document.getElementById('icono_act_crm').value;        

        if(icono > 0){           
            document.getElementById('icon'+icono).style.border = "3px solid blue";   
        }

        function selectIcon(div){
            var icon = div.id.replace('icon','');
            if(div.style.border == '1px solid'){
      
              var x = document.getElementsByClassName("contenedorIconos"); 
              
              [].forEach.call(x,function(div){
                  div.style.border = "1px solid";                 
              });
      
              div.style.border = '3px solid blue';
              document.getElementById('icono_act_crm').value = icon;
            }
            else{ div.style.border = '1px solid'; }
        }       
    </script>         
  <?php          

}  

function guardarConfiguracionActividadCRM($id,$departamento_act_crm,$nombre_act_crm,$fecha_completa_act_crm,$fecha_vencimiento_act_crm,$icono_act_crm,$genera_visita_act_crm,$genera_llamada_act_crm,$copia_act_crm,$mysql,$link){
    if($id > 0){
        $sql   = "UPDATE crm_configuracion_actividades
                  SET 
                      nombre                 = '$nombre_act_crm',
                      fecha_completa         = '$fecha_completa_act_crm',
                      fecha_vencimiento      = '$fecha_vencimiento_act_crm',
                      copiar_crm_obligatorio = '$copia_act_crm',
                      icono                  = '$icono_act_crm',
                      genera_visita          = '$genera_visita_act_crm',
                      genera_llamada         = '$genera_llamada_act_crm',
                      id_departamento        = '$departamento_act_crm'
                  WHERE id = $id";
        $query = $mysql->query($sql,$link);
        echo '<script>
                 MyLoading2("off");
                 Actualiza_Div_configuracion_actividades_crm('.$id.');
                 Win_Ventana_ConfigurarActividadCRM.close();
              </script>';

        

    }else{
        echo$sql   = "INSERT INTO crm_configuracion_actividades
                          (nombre,
                           fecha_completa,
                           fecha_vencimiento,
                           copiar_crm_obligatorio,
                           icono,
                           genera_visita,
                           genera_llamada,
                           id_departamento) 
                  VALUES ('$nombre_act_crm',
                          '$fecha_completa_act_crm',
                          '$fecha_vencimiento_act_crm',
                          '$copia_act_crm',
                          '$icono_act_crm',
                          '$genera_visita_act_crm',
                          '$genera_llamada_act_crm',
                          '$departamento_act_crm')";

        $query = $mysql->query($sql,$link);
        $id = $mysql->insert_id();

        echo '<script>
                  MyLoading2("off");
                  Inserta_Div_configuracion_actividades_crm('.$id.');
                  Win_Ventana_ConfigurarActividadCRM.close();
              </script>';
    }
    
}

function deleteActivity($id,$mysql)
{
  $sql   = "UPDATE crm_configuracion_actividades SET activo = 0 WHERE id = $id";
  $query = $mysql->query($sql,$link);
  echo '<script>
           MyLoading2("off");
           Elimina_Div_configuracion_actividades_crm('.$id.');
           Win_Ventana_ConfigurarActividadCRM.close();
        </script>';
}

?>

