<?php
    include('../../../../configuracion/conectar.php');
    include('../../../../configuracion/define_variables.php');

    $id_empresa = $_SESSION['EMPRESA'];

    switch ($opc) {
    	case 'filtro_informe':
    		filtro_informe($id_empresa,$mysql);
    		break;
    }

    function filtro_informe($id_empresa,$mysql){
        $sql="SELECT id,codigo,nombre FROM informes_formatos WHERE activo=1 AND id_empresa=$id_empresa";
        $query=$mysql->query($sql,$mysql->link);
        while ($row=$mysql->fetch_array($query)) {
            $reports .= "<option value='$row[id]'>$row[codigo] - $row[nombre]</option>";
        }

        ?>
            <select id="id_formato" style="margin-top: 9px;" onchange="carga_wizard(this.value)">
                <option value="">Seleccione...</option>
                <?php echo $reports; ?>
            </select>

            <script>
                function carga_wizard(id_formato) {
                    Ext.get('content-wizard').load({
                        url     : 'informes/report/wizard_report.php',
                        scripts : true,
                        nocache : true,
                        params  :
                        {
                            id_formato : id_formato,
                        }
                    });
                }
            </script>
        <?php

    }

?>