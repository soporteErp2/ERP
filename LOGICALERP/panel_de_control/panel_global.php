<?php
  include('../../configuracion/conectar.php');
  include '../erp_paises_global/config_paises.php';
?>
<div style="float:left; padding:15px; width:100%">
    <!-- -------------------------------------------------------------------------------------- -->
    <?php
    if(user_permisos(64,'false') == 'true'){
    ?>
    <!-- -------------------------------------------------------------------------------------- -->
    <div class="ContenedorGrupoPanelControl">
        <div class="TituloPanelControl">
            Configuraciones de Empresa
        </div>
        <div style="width:100%; float:left">
            <div class="IconoPanelControl" onClick="AbreVentanaPanelGlobal('<?php echo $url_datos_empresa ?>','Datos de la Empresa',420,500,0);">
                <div class="IconoPanelControlimg"><img src="../../temas/clasico/images/PanelControl/empresa44.png" width="44" height="44"></div>
                <div class="IconoPanelControltxt">Datos Empresa</div>
            </div>

            <div class="IconoPanelControl" onClick="AbreVentanaPanelGlobal('<?php echo $url_sucursales_empresa ?>','Sucursales',650,500,0);">
                <div class="IconoPanelControlimg"><img src="../../temas/clasico/images/PanelControl/sucursales44.png" width="44" height="44"></div>
                <div class="IconoPanelControltxt">Sucursales</div>
            </div>

            <div class="IconoPanelControl" onClick="AbreVentanaPanelGlobal('moneda/moneda','Configuracion de moneda',250,200,0);">
                <div class="IconoPanelControlimg"><img src="../../temas/clasico/images/PanelControl/moneda44.png" width="44" height="44"></div>
                <div class="IconoPanelControltxt">Configuracion Moneda</div>
            </div>

            <div class="IconoPanelControl" onClick="AbreVentanaPanelGlobal('ventas/resolucion_dian','Configuracion Resolucion Facturacion',700,400,0);">
                <div class="IconoPanelControlimg"><img src="../../temas/clasico/images/PanelControl/documentos44.png" width="44" height="44"></div>
                <div class="IconoPanelControltxt">Configuracion Resolucion Facturacion</div>
            </div>
            <div class="IconoPanelControl" onClick="AbreVentanaPanelGlobal('resolucion_documento_soporte/resolucion_dian','Resolucion documento soporte',700,400,0);">
                <div class="IconoPanelControlimg"><img src="../../temas/clasico/images/PanelControl/documentos44.png" width="44" height="44"></div>
                <div class="IconoPanelControltxt">Resolucion documento soporte</div>
            </div>

        </div>
    </div>
    <?php }
    if(user_permisos(65,'false') == 'true'){
    ?>
    <!-- -------------------------------------------------------------------------------------- -->
    <div class="ContenedorGrupoPanelControl">
        <div class="TituloPanelControl">
            Configuraciones del Sistema
        </div>

        <div style="width:100%; float:left">
            <?php if($_SESSION['ROLVALOR'] == 0){ ?>
                <div class="IconoPanelControl" onClick="AbreVentanaPanelGlobal('variables/grupo_variable','Grupos de Variables',650,500,0);">
                    <div class="IconoPanelControlimg"><img src="../../temas/clasico/images/PanelControl/variables44.png" width="44" height="44"></div>
                    <div class="IconoPanelControltxt">Configuracion de Variables</div>
                </div>
            <?php } ?>

            <div class="IconoPanelControl" onClick="AbreVentanaPanelGlobal('configuracion_correo_SMTP/correo','Configuracion de Documentos',550,350,0,true);">
                <div class="IconoPanelControlimg"><img src="../../temas/clasico/images/PanelControl/email44.png" width="44" height="44"></div>
                <div class="IconoPanelControltxt">Configuracion SMTP</div>
            </div>

        </div>
    </div>
    <?php }
    if(user_permisos(66,'false') == 'true'){
    ?>
    <!-- -------------------------------------------------------------------------------------- -->
    <div class="ContenedorGrupoPanelControl">
        <div class="TituloPanelControl">
            Configuracion PUC - NIFF
        </div>
        <div style="width:100%; float:left">

            <div class="IconoPanelControl" onClick="AbreVentanaPanelGlobal('puc/puc','Plan unico de cuentas',0,0,0);">
                <div class="IconoPanelControlimg"><img src="../../temas/clasico/images/PanelControl/libro44.png" width="44" height="44"></div>
                <div class="IconoPanelControltxt">PUC</div>
            </div>

             <div class="IconoPanelControl" onClick="AbreVentanaPanelGlobal('puc_niif/puc','Plan unico de cuentas NIIF',0,0,0);">
                <div class="IconoPanelControlimg"><img src="../../temas/clasico/images/PanelControl/libroNiff44.png" width="44" height="44"></div>
                <div class="IconoPanelControltxt">Cuentas NIIF</div>
            </div>

            <div class="IconoPanelControl" onClick="AbreVentanaPanelGlobal('configuracion_puc/configuracion_puc','Configuracion Digitos Puc',400,300,0);">
                <div class="IconoPanelControlimg"><img src="../../temas/clasico/images/PanelControl/config_puc44.png" width="44" height="44"></div>
                <div class="IconoPanelControltxt">Configuracion Digitos</div>
            </div>

            <div class="IconoPanelControl" onClick="AbreVentanaPanelGlobal('configuracion_puc/configuracion_puc_niif','Configuracion Digitos Puc',400,300,0);">
                <div class="IconoPanelControlimg"><img src="../../temas/clasico/images/PanelControl/config_puc44.png" width="44" height="44"></div>
                <div class="IconoPanelControltxt">Configuracion Digitos Niif</div>
            </div>

            <div class="IconoPanelControl" onClick="AbreVentanaPanelGlobal('configuracion_informes_niif/formatos','Configuracion Informes en Niif',600,500,0);">
                <div class="IconoPanelControlimg"><img src="../../temas/clasico/images/PanelControl/informes44.png" width="44" height="44"></div>
                <div class="IconoPanelControltxt">Configuracion Informes Niif</div>
            </div>

            <div class="IconoPanelControl" onClick="AbreVentanaPanelGlobal('bancos/bancos','Bancos',600,500,0);">
                <div class="IconoPanelControlimg"><img src="../../temas/clasico/images/PanelControl/banco44.png" width="44" height="44"></div>
                <div class="IconoPanelControltxt">Bancos</div>
            </div>
        </div>
    </div>
    <?php }
    if(user_permisos(67,'false') == 'true'){
    ?>
    <!-- -------------------------------------------------------------------------------------- -->
    <div class="ContenedorGrupoPanelControl">
        <div class="TituloPanelControl">
            Parametrizaciones Modulo Empleados
        </div>
        <div style="width:100%; float:left">

            <div class="IconoPanelControl" onClick="AbreVentanaPanelGlobal('roles','Roles y Permisos',0,0,0);">
                <div class="IconoPanelControlimg"><img src="../../temas/clasico/images/PanelControl/roles44.png" width="44" height="44"></div>
                <div class="IconoPanelControltxt">Roles y Permisos</div>
            </div>

            <!-- <div class="IconoPanelControl" onClick="AbreVentanaPanelGlobal('tipo_contrato/tipo_contrato','Tipos de contrato de nomina',450,400,0,0);">
                <div class="IconoPanelControlimg"><img src="../../temas/clasico/images/BotonesTabs/contrato1.png" width="44" height="44"></div>
                <div class="IconoPanelControltxt">Tipo de Contratos</div>
            </div> -->

            <div class="IconoPanelControl" onClick="AbreVentanaPanelGlobal('motivo_finalizacion_contrato/motivo_finalizacion_contrato','Motivos Finalizacion contratos',450,400,0,0);">
                <div class="IconoPanelControlimg"><img src="../../temas/clasico/images/BotonesTabs/contrato1.png" width="44" height="44"></div>
                <div class="IconoPanelControltxt">Motivos Finalizacion contratos</div>
            </div>

            <div class="IconoPanelControl" onClick="AbreVentanaPanelGlobal('grupos_trabajo/grupos_trabajo','Grupos de Trabajo',450,400,0,0);">
                <div class="IconoPanelControlimg"><img src="../../temas/clasico/images/PanelControl/reunion44.png" width="44" height="44"></div>
                <div class="IconoPanelControltxt">Grupos de Trabajo</div>
            </div>

            <div class="IconoPanelControl" onClick="AbreVentanaPanelGlobal('configuracion_tipos_contacto/configuracion_tipos_contacto','Tipos de Contacto',400,400);">
                <div class="IconoPanelControlimg"><img src="../../temas/clasico/images/PanelControl/contactos_44.png" width="44" height="44"></div>
                <div class="IconoPanelControltxt">Configuracion Tipos de Contacto</div>
            </div>

        </div>

    </div>
    <?php }
    if(user_permisos(69,'false') == 'true'){
    ?>
    <!-- -------------------------------------------------------------------------------------- -->
    <div class="ContenedorGrupoPanelControl">
        <div class="TituloPanelControl">
            Parametrizaciones Contables
        </div>
        <div style="width:100%; float:left">
            <div class="IconoPanelControl" onClick="AbreVentanaPanelGlobal('impuestos/impuestos','Impuestos',490,360,0);">
                <div class="IconoPanelControlimg"><img src="../../temas/clasico/images/PanelControl/billetes44.png" width="44" height="44"></div>
                <div class="IconoPanelControltxt">Impuestos</div>
            </div>
            <div class="IconoPanelControl" onClick="AbreVentanaPanelGlobal('retenciones/retenciones','Retenciones',670,360,0);">
                <div class="IconoPanelControlimg"><img src="../../temas/clasico/images/PanelControl/billetes44.png" width="44" height="44"></div>
                <div class="IconoPanelControltxt">Retenciones</div>
            </div>
            <div class="IconoPanelControl" onClick="AbreVentanaPanelGlobal('centro_costos/centro_costos','Centros de Costos',650,600,0);">
                <div class="IconoPanelControlimg"><img src="../../temas/clasico/images/PanelControl/centro_costos44.png" width="44" height="44"></div>
                <div class="IconoPanelControltxt">Centros de Costos</div>
            </div>
            <div class="IconoPanelControl" onClick="AbreVentanaPanelGlobal('cuentas_pago/cuentas_pago','Cuentas de Pago',800,460,0);">
                <div class="IconoPanelControlimg"><img src="../../temas/clasico/images/PanelControl/pagos.png" width="44" height="44"></div>
                <div class="IconoPanelControltxt">Cuentas de Pago y Cobro</div>
            </div>
            <div class="IconoPanelControl" onClick="AbreVentanaPanelGlobal('formas_pago/formas_pago','Formas de Pago',600,460,0);">
                <div class="IconoPanelControlimg"><img src="../../temas/clasico/images/PanelControl/forma_pago.png" width="44" height="44"></div>
                <div class="IconoPanelControltxt">Formas de Pago</div>
            </div>
            <div class="IconoPanelControl" onClick="AbreVentanaPanelGlobal('metodos_pago_dian/formas_pago','Metodo de Pago Dian',600,460,0);">
                <div class="IconoPanelControlimg"><img src="../../temas/clasico/images/PanelControl/forma_pago.png" width="44" height="44"></div>
                <div class="IconoPanelControltxt">Metodos de Pago Dian</div>
            </div>
            <div class="IconoPanelControl" onClick="AbreVentanaPanelGlobal('filtro_nota/filtro_nota','Configuracion Filtro Nota',600,500,0);">
                <div class="IconoPanelControlimg"><img src="../../temas/clasico/images/PanelControl/configuracion_notas.png" width="44" height="44"></div>
                <div class="IconoPanelControltxt">Configuracion Filtro Nota</div>
            </div>
            <div class="IconoPanelControl" onClick="AbreVentanaPanelGlobal('medios_magneticos_formatos/formatos','Configuracion formatos medios magneticos',600,500,0);">
                <div class="IconoPanelControlimg"><img src="../../temas/clasico/images/PanelControl/cuentas_predefinidas44.png" width="44" height="44"></div>
                <div class="IconoPanelControltxt">Formatos Medios Magneticos</div>
            </div>
            <div class="IconoPanelControl" onClick="AbreVentanaPanelGlobal('informes_formatos/formatos','Configuracion formatos Informes',600,500,0);">
                <div class="IconoPanelControlimg"><img src="../../temas/clasico/images/PanelControl/cuentas_predefinidas44.png" width="44" height="44"></div>
                <div class="IconoPanelControltxt">Formatos de Informes</div>
            </div>
            <div class="IconoPanelControl" onClick="AbreVentanaPanelGlobal('certificado_ingreso_retenciones_empleados/secciones','Configuracion formato',600,500,0);">
                <div class="IconoPanelControlimg"><img src="../../temas/clasico/images/PanelControl/cuentas_predefinidas44.png" width="44" height="44"></div>
                <div class="IconoPanelControltxt">Certificado Ingresos y Retenciones</div>
            </div>
            <div class="IconoPanelControl" onClick="AbreVentanaPanelGlobal('cuentas_deterioro_cartera_niif/panel','Configuracion formato',680,350,0);">
                <div class="IconoPanelControlimg"><img src="../../temas/clasico/images/PanelControl/cuentas_predefinidas44.png" width="44" height="44"></div>
                <div class="IconoPanelControltxt">Cuentas deterioro cartera NIIF</div>
            </div>
            <div class="IconoPanelControl" onClick="AbreVentanaPanelGlobal('cuentas_deterioro_cartera_niif/panel','Configuracion formato',680,350,0);">
                <div class="IconoPanelControlimg"><img src="../../temas/clasico/images/PanelControl/cuentas_predefinidas44.png" width="44" height="44"></div>
                <div class="IconoPanelControltxt">Cuentas NIIF a cerrar </div>
            </div>
        </div>
    </div>
    <?php }
    if(user_permisos(68,'false') == 'true'){
    ?>
    <!-- -------------------------------------------------------------------------------------- -->
    <div class="ContenedorGrupoPanelControl">
        <div class="TituloPanelControl">
            Configuracion Items
        </div>
        <div style="width:100%; float:left">

            <div class="IconoPanelControl" onClick="AbreVentanaPanelGlobal('unidades_inventario/unidades_inventario','Unidades de Inventario',550,450,0);">
                <div class="IconoPanelControlimg"><img src="../../temas/clasico/images/iconos/inventario44.png" width="44" height="44"></div>
                <div class="IconoPanelControltxt">Unidades de Inventario</div>
            </div>

            <div class="IconoPanelControl" onClick="AbreVentanaPanelGlobal('cuentas_default/panel_cuentas_default','Cuentas Predefinidas',650,513,0);">
                <div class="IconoPanelControlimg"><img src="../../temas/clasico/images/PanelControl/cuentas_predefinidas44.png" width="44" height="44"></div>
                <div class="IconoPanelControltxt">Configuracion Cuentas Predefinidas</div>
            </div>

             <div class="IconoPanelControl" onClick="AbreVentanaPanelGlobal('items/panel_item','Items',0,0,0);">
                <div class="IconoPanelControlimg"><img src="../../temas/clasico/images/PanelControl/cubos44.png" width="44" height="44"></div>
                <div class="IconoPanelControltxt">Items</div>
            </div>


        </div>
    </div>
    <?php }
    if(user_permisos(70,'false') == 'true'){
    ?>
    <!-- -------------------------------------------------------------------------------------- -->
    <div class="ContenedorGrupoPanelControl">
        <div class="TituloPanelControl">
            Parametrizaciones Modulo Terceros
        </div>

        <div style="width:100%; float:left">
            <div class="IconoPanelControl" onClick="AbreVentanaPanelGlobal('terceros_tratamiento','Tratamiento a Terceros',290,350,0);">
                <div class="IconoPanelControlimg"><img src="../../temas/clasico/images/PanelControl/tratamiento_terceros.png" width="44" height="44"></div>
                <div class="IconoPanelControltxt">Tratamiento a Terceros</div>
            </div>

            <div class="IconoPanelControl" onClick="AbreVentanaPanelGlobal('tipos_identificacion/tipo_identificacion','Tipos de Identificacion',625,330,0);">
                    <div class="IconoPanelControlimg"><img src="../../temas/clasico/images/PanelControl/tipo_identificacion.png" width="44" height="44"></div>
                    <div class="IconoPanelControltxt">Tipos de Identificacion</div>
                </div>

            <div class="IconoPanelControl" onClick="AbreVentanaPanelGlobal('tipos_documentos_tercero/tipos_documentos_tercero','Configuracion Tipos de Documentos Terceros',500,410,0);">
                <div class="IconoPanelControlimg"><img src="../../temas/clasico/images/PanelControl/documentos44.png" width="44" height="44"></div>
                <div class="IconoPanelControltxt">Configuracion Documentos Terceros</div>
            </div>

            <div class="IconoPanelControl" onClick="AbreVentanaPanelGlobal('paises/paises','Paises y Ciudades',0,0,0);">
                <div class="IconoPanelControlimg"><img src="../../temas/clasico/images/PanelControl/paises.png" width="44" height="44"></div>
                <div class="IconoPanelControltxt">Paises y Ciudades</div>
            </div>

            <div class="IconoPanelControl" onClick="AbreVentanaPanelGlobal('sector_empresarial/sector_empresarial','Configuracion sector empresarial',500,410,0);">
                <div class="IconoPanelControlimg"><img src="../../temas/clasico/images/PanelControl/torres44.png" width="44" height="44"></div>
                <div class="IconoPanelControltxt">Configuracion sector empresarial</div>
            </div>

        </div>
    </div>
    <?php }
    if(user_permisos(71,'false') == 'true'){
    ?>
    <!-- -------------------------------------------------------------------------------------- -->
    <!-- <div class="ContenedorGrupoPanelControl">
      <div class="TituloPanelControl">
        Parametrizaciones Modulo Activos Fijos
      </div>
      <div style="width:100%; float:left">
        <div class="IconoPanelControl" onClick="AbreVentanaPanelGlobal('grupo_inventario/inventario_grupos','Grupos de Activos Fijos',0,0,0);">
          <div class="IconoPanelControlimg"><img src="../../temas/clasico/images/iconos/inventario44.png" width="44" height="44"></div>
          <div class="IconoPanelControltxt">Grupos</div>
        </div>
      </div>
    </div> -->
    <?php }
    if(user_permisos(72,'false') == 'true'){
    ?>
    <!-- -------------------------------------------------------------------------------------- -->
    <div class="ContenedorGrupoPanelControl">
        <div class="TituloPanelControl">
            Parametrizaciones Modulo Compras y Ventas
        </div>
        <div style="width:100%; float:left">

            <div class="IconoPanelControl" onClick="AbreVentanaPanelGlobal('compras_requisicion_tipo/tipos_requisicion_compra','Tipos Requicisiones de Compras',350,350,0);">
                <div class="IconoPanelControlimg"><img src="../../temas/clasico/images/PanelControl/cuentas_predefinidas44.png" width="44" height="44"></div>
                <div class="IconoPanelControltxt">Tipos Requicision</div>
            </div>

            <div class="IconoPanelControl" onClick="AbreVentanaPanelGlobal('compras/tipos_ordenes_compra','Tipos Ordenes de Compras',350,350,0);">
                <div class="IconoPanelControlimg"><img src="../../temas/clasico/images/PanelControl/calendario44.png" width="44" height="44"></div>
                <div class="IconoPanelControltxt">Tipos Ordenes de Compra</div>
            </div>

            <div class="IconoPanelControl" onClick="AbreVentanaPanelGlobal('compras/dias_vencimiento','Vencimiento Ordenes de Compras',250,170,0);">
                <div class="IconoPanelControlimg"><img src="../../temas/clasico/images/PanelControl/calendario44.png" width="44" height="44"></div>
                <div class="IconoPanelControltxt">Vencimiento Ordenes de Compras</div>
            </div>

            <div class="IconoPanelControl" onClick="AbreVentanaPanelGlobal('ventas/dias_vencimiento','Vencimiento de Documentos',220,280,0,0);">
                <div class="IconoPanelControlimg"><img src="../../temas/clasico/images/PanelControl/calendario44.png" width="44" height="44"></div>
                <div class="IconoPanelControltxt">Vencimiento Documentos de Ventas</div>
            </div>

            <div class="IconoPanelControl" onClick="AbreVentanaPanelGlobal('compras_facturas_tipo/tipos','Tipos de Facturas',350,350,0,0);">
                <div class="IconoPanelControlimg"><img src="../../temas/clasico/images/PanelControl/cuentas_predefinidas44.png" width="44" height="44"></div>
                <div class="IconoPanelControltxt">Tipos de facturas de compra</div>
            </div>
            <div class="IconoPanelControl" onClick="AbreVentanaPanelGlobal('envio_facturas/Form','Envio automatico',350,250,0,0);">
                <div class="IconoPanelControlimg"><img src="../../temas/clasico/images/BotonesTabs/envia_doc.png" width="44" height="44"></div>
                <div class="IconoPanelControltxt">Envio automatico de facturas</div>
            </div>

            <!--<div class="IconoPanelControl" onClick="AbreVentanaPanelGlobal('configuracion_comprobante_egreso/configuracion_comprobante_egreso','Cuentas documentos cruce permitidos',600,500,0,0);">
                <div class="IconoPanelControlimg"><img src="../../temas/clasico/images/PanelControl/documentos44.png" width="44" height="44"></div>
                <div class="IconoPanelControltxt">Configuracion Comprobante de egreso</div>
            </div>-->

        </div>
    </div>
    <?php }
    // if(user_permisos(72,'false') == 'true'){
    ?>
    <!-- -------------------------------------------------------------------------------------- -->
    <div class="ContenedorGrupoPanelControl">
        <div class="TituloPanelControl">
            Parametrizaciones Modulo POS
        </div>

        <div style="width:100%; float:left">
            <div class="IconoPanelControl" onClick="AbreVentanaPanelGlobal('configuracion_pos/configuracion_pos','Configuraciones POS',700,400,0);">
                <div class="IconoPanelControlimg"><img src="../../temas/clasico/images/PanelControl/documentos44.png" width="44" height="44"></div>
                <div class="IconoPanelControltxt">Configuracion POS</div>
            </div>
            <div class="IconoPanelControl" onClick="AbreVentanaPanelGlobal('pos_tope_facturacion/tope','Configuracion Tope POS',300,300,0);">
                <div class="IconoPanelControlimg"><img src="../../temas/clasico/images/PanelControl/billetes44.png" width="44" height="44"></div>
                <div class="IconoPanelControltxt">Topes facturacion</div>
            </div>
            <div class="IconoPanelControl" onClick="AbreVentanaPanelGlobal('items_terminos/terminos','Grupo Termino',400,350,0);">
                <div class="IconoPanelControlimg"><img src="../../temas/clasico/images/PanelControl/termino44.png" width="44" height="44"></div>
                <div class="IconoPanelControltxt">Terminos de coccion</div>
            </div>

            <div class="IconoPanelControl" onClick="AbreVentanaPanelGlobal('configuracion_secciones_pos/secciones','',500,500,0);">
                <div class="IconoPanelControlimg"><img src="../../temas/clasico/images/PanelControl/sucursales44.png" width="44" height="44"></div>
                <div class="IconoPanelControltxt">Secciones</div>
            </div>

            <div class="IconoPanelControl" onClick="AbreVentanaPanelGlobal('configuracion_cajas_pos/grillaCajas','Configuracion Cajas del POS',450,420,0);">
                <div class="IconoPanelControlimg"><img src="../../temas/clasico/images/PanelControl/pos.png" width="44" height="44"></div>
                <div class="IconoPanelControltxt">Configurar Cajas</div>
            </div>

            <div class="IconoPanelControl" onClick="AbreVentanaPanelGlobal('configuracion_mesas_pos/configuracion_mesas','Configuracion mesas del POS',450,410,0);">
                <div class="IconoPanelControlimg"><img src="../../temas/clasico/images/PanelControl/mesa44.png" width="44" height="44"></div>
                <div class="IconoPanelControltxt">Configurar Mesas</div>
            </div>
            <div class="IconoPanelControl" onClick="AbreVentanaPanelGlobal('formas_pago_pos/cuentas_pago','Formas de Pago POS',600,460,0);">
                <div class="IconoPanelControlimg"><img src="../../temas/clasico/images/PanelControl/forma_pago.png" width="44" height="44"></div>
                <div class="IconoPanelControltxt">Formas de Pago</div>
            </div>
            <div class="IconoPanelControl" onClick="AbreVentanaPanelGlobal('descuentos_pos/descuentos_pos','Descuento de Pago POS',600,460,0);">
                <div class="IconoPanelControlimg"><img src="../../temas/clasico/images/PanelControl/descuento44.png" width="44" height="44"></div>
                <div class="IconoPanelControltxt">Descuentos</div>
            </div>
            <div class="IconoPanelControl" onClick="AbreVentanaPanelGlobal('propinas_pos/propinas_pos','Configuracion Propinas POS',600,460,0);">
                <div class="IconoPanelControlimg"><img src="../../temas/clasico/images/PanelControl/pagos.png" width="44" height="44"></div>
                <div class="IconoPanelControltxt">Propinas</div>
            </div>
            

        </div>
    </div>
    <?php
    // }
    if(user_permisos(105,'false') == 'true'){
    ?>
    <!-- -------------------------------------------------------------------------------------- -->
    <div class="ContenedorGrupoPanelControl">
        <div class="TituloPanelControl">
            Parametrizaciones Modulo de Nomina
        </div>
        <div style="width:100%; float:left">

            <div class="IconoPanelControl" onClick="AbreVentanaPanelGlobal('nomina_grupos_definicion_tributaria/nomina_grupos_definicion_tributaria','Grupos de Conceptos',650,600,0,0);">
                <div class="IconoPanelControlimg"><img src="../../temas/clasico/images/PanelControl/documentos44.png" width="44" height="44"></div>
                <div class="IconoPanelControltxt">Grupos de Conceptos y Conceptos</div>
            </div>

            <div class="IconoPanelControl" onClick="AbreVentanaPanelGlobal('tipo_liquidacion/tipos_liquidacion','Tipos de pago de nomina',450,400,0,0);">
                <div class="IconoPanelControlimg"><img src="../../temas/clasico/images/PanelControl/tipo_pago_nomina.png" width="44" height="44"></div>
                <div class="IconoPanelControltxt">Tipo de Pago</div>
            </div>

            <div class="IconoPanelControl" onClick="AbreVentanaPanelGlobal('niveles_riesgos_laborales/nivel_riesgo','Niveles de Riesgos Laborales',450,400,0,0);">
                <div class="IconoPanelControlimg"><img src="../../temas/clasico/images/PanelControl/reunion44.png" width="44" height="44"></div>
                <div class="IconoPanelControltxt">Niveles de Riesgo Laboral</div>
            </div>

            <div class="IconoPanelControl" onClick="AbreVentanaPanelGlobal('configuracion_ARL/form','Configuracion ARL',400,250,0,0);">
                <div class="IconoPanelControlimg"><img src="../../temas/clasico/images/PanelControl/tipo_identificacion.png" width="44" height="44"></div>
                <div class="IconoPanelControltxt">Codigo ARL</div>
            </div>

            <div class="IconoPanelControl" onClick="AbreVentanaPanelGlobal('configuracion_archivos_planos/archivo_plano','Configuracion Archivos Planos',440,390,0,0);">
                <div class="IconoPanelControlimg"><img src="../../temas/clasico/images/PanelControl/file-text44.png" width="44" height="44"></div>
                <div class="IconoPanelControltxt">Configuracion Archivos Planos</div>
            </div>
            <div class="IconoPanelControl" onClick="AbreVentanaPanelGlobal('wizard_nomina_electronica/wizard','Asistente de configuracion',600,500,0,0);">
                <div class="IconoPanelControlimg"><img src="../../temas/clasico/images/PanelControl/documentos44.png" width="44" height="44"></div>
                <div class="IconoPanelControltxt">Configuracion Nomina Electronica</div>
            </div>
            <div class="IconoPanelControl" onClick="AbreVentanaPanelGlobal('nomina_electronica_he/horas_extras','Configuracion Horas extras',300,300,0);">
                <div class="IconoPanelControlimg"><img src="../../temas/clasico/images/PanelControl/festivos44.png" width="44" height="44"></div>
                <div class="IconoPanelControltxt">Configuracion Hora extra NE</div>
            </div>

        </div>
    </div>
    <?php }
    if(user_permisos(105,'false') == 'true'){
    ?>
    <!-- -------------------------------------------------------------------------------------- -->
    <div class="ContenedorGrupoPanelControl">
        <div class="TituloPanelControl">
            Parametrizaciones Modulo de Costos
        </div>
        <div style="width:100%; float:left">
            <div class="IconoPanelControl" onClick="AbreVentanaPanelGlobal('autorizaciones_requisiciones/seleccion_departamentos','Autorizacion Requicisiones Por Areas',600,450,0,0);">
                <div class="IconoPanelControlimg"><img src="../../temas/clasico/images/PanelControl/documento_check44.png" width="44" height="44"></div>
                <div class="IconoPanelControltxt">Autorizacion Requisiciones Por Areas</div>
            </div>

            <div class="IconoPanelControl" onClick="AbreVentanaPanelGlobal('autorizaciones_ordenes_compra/panel_autorizaciones','Tipo Autorizacion',300,150,0,0);">
                <div class="IconoPanelControlimg"><img src="../../temas/clasico/images/PanelControl/documento_check44.png" width="44" height="44"></div>
                <div class="IconoPanelControltxt">Autorizacion Ordenes de Compra</div>
            </div>

            <div class="IconoPanelControl" onClick="AbreVentanaPanelGlobal('costos/ventana_costos_tipos','Departamentos de la Compa&ntilde;ia',450,450,0);">
                <div class="IconoPanelControlimg"><img src="../../temas/clasico/images/PanelControl/documentos44.png" width="44" height="44"></div>
                <div class="IconoPanelControltxt">Configuraci&oacute;n Departamentos</div>
            </div>

            <div class="IconoPanelControl" onClick="AbreVentanaPanelGlobal('costos/ventana_costos_documentos','Costos por Documento',370,370,0,0);">
                <div class="IconoPanelControlimg"><img src="../../temas/clasico/images/PanelControl/tipo_pago_nomina.png" width="44" height="44"></div>
                <div class="IconoPanelControltxt">Costos por Documento</div>
            </div>

            <div class="IconoPanelControl" onClick="AbreVentanaPanelGlobal('costos_cuentas_transito/cuentas_transito','Cuentas de transito',500,390,0);">
                <div class="IconoPanelControlimg"><img src="../../temas/clasico/images/PanelControl/cuentas_predefinidas44.png" width="44" height="44"></div>
                <div class="IconoPanelControltxt">Cuentas de transito</div>
            </div>

        </div>
    </div>
    <?php }
    if(user_permisos(203,'false') == 'true'){
    ?>
    <!-- -------------------------------------------------------------------------------------- -->
    <div class="ContenedorGrupoPanelControl">
        <div class="TituloPanelControl">
            Parametrizaciones Modulo CRM
        </div>
        <div style="width:100%; float:left">
            <div class="IconoPanelControl" onClick="AbreVentanaPanelGlobal('lineas_negocio/lineas_negocio','Lineas de Negocio',400,400,0,0);">
                <div class="IconoPanelControlimg"><img src="../../temas/clasico/images/PanelControl/negocios44.png" width="44" height="44"></div>
                <div class="IconoPanelControltxt">Lineas de Negocio</div>
            </div>
            <div class="IconoPanelControl" onClick="AbreVentanaPanelGlobal('estado_proyectos/estado_proyectos','Estados de Proyectos',400,400,0,0);">
                <div class="IconoPanelControlimg"><img src="../../temas/clasico/images/PanelControl/estados_proyectos44.png" width="44" height="44"></div>
                <div class="IconoPanelControltxt">Estados de Proyectos</div>
            </div>
            <div class="IconoPanelControl" onClick="AbreVentanaPanelGlobal('tipos_proyecto/tipos_proyecto','Tipos de Proyectos',400,400,0,0);">
                <div class="IconoPanelControlimg"><img src="../../temas/clasico/images/PanelControl/negocios44.png" width="44" height="44"></div>
                <div class="IconoPanelControltxt">Tipos de Proyectos</div>
            </div>
            <div class="IconoPanelControl" onClick="AbreVentanaPanelGlobal('configuracion_actividades_crm/configuracion_actividades_crm','Configuracion Actividades CRM',0,0,0,0);">
                <div class="IconoPanelControlimg"><img src="../../temas/clasico/images/PanelControl/crm44.png" width="44" height="44"></div>
                <div class="IconoPanelControltxt">Configuracion Tipos de Actividad</div>
            </div>
            <div class="IconoPanelControl" onClick="AbreVentanaPanelGlobal('configuracion_departamentos_crm/configuracion_departamentos_crm','Configuracion Departamentos',400,400,0,0);">
                <div class="IconoPanelControlimg"><img src="../../temas/clasico/images/PanelControl/empresa44.png" width="44" height="44"></div>
                <div class="IconoPanelControltxt">Departamentos</div>
            </div>
        </div>
    </div>
    <?php }
        include('../configuracion/conectar.php');
        // CONFIGURACION SIHO
        $sqlModulos   = "SELECT COUNT(id) AS cont FROM web_service_software WHERE id_empresa='$_SESSION[EMPRESA]' AND activo=1";
        $queryModulos = $mysql->query($sqlModulos,$link);
        $confSiho     = $mysql->result($queryModulos,0,'cont');

        if ($confSiho>0) {
        ?>
             <div class="ContenedorGrupoPanelControl">
                <div class="TituloPanelControl">
                    Parametrizaciones SIHO
                </div>
                <div style="width:100%; float:left">
                    <div class="IconoPanelControl" onClick="AbreVentanaPanelGlobal('configuracion_SIHO/configuracion_SIHO','Configuracion',500,250,0,0);">
                        <div class="IconoPanelControlimg"><img src="../../temas/clasico/images/PanelControl/cuentas_predefinidas44.png" width="44" height="44"></div>
                        <div class="IconoPanelControltxt">Configuracion Tercero Ingresos y Reversiones</div>
                    </div>
                    <div class="IconoPanelControl" onClick="AbreVentanaPanelGlobal('configuracion_SIHO/configuracion_pedidos','Configuracion',250,150,0,0);">
                        <div class="IconoPanelControlimg"><img src="../../temas/clasico/images/PanelControl/cuentas_predefinidas44.png" width="44" height="44"></div>
                        <div class="IconoPanelControltxt">Configuracion valor items en Pedidos</div>
                    </div>
                    <div class="IconoPanelControl" onClick="AbreVentanaPanelGlobal('configuracion_SIHO/configuracion_remisiones','Configuracion',250,150,0,0);">
                        <div class="IconoPanelControlimg"><img src="../../temas/clasico/images/PanelControl/cuentas_predefinidas44.png" width="44" height="44"></div>
                        <div class="IconoPanelControltxt">Configuracion valor items en Remisiones</div>
                    </div>
                </div>
            </div>
        <?php
        }
    ?>
</div>
<script>
    function AbreVentanaPanelGlobal(archivo,titulo,ancho,alto,color,resize){

        if(typeof(resize)=='undefined'){ resize = true; }
        if (color == 0) { color = 'background-color:<?php echo $_SESSION['COLOR_FONDO'] ?>;'; }

        var myalto2  = Ext.getBody().getHeight();
        var myancho2 = Ext.getBody().getWidth();

    	if(alto == 0){ WinAlto = myalto2-20; }
        else{ WinAlto = alto; }

    	if(ancho == 0){ WinAncho = myancho2-30 }
        else{ WinAncho = ancho }

    	Win_Panel_Global = new Ext.Window({
            width       : WinAncho,
            height      : WinAlto,
            title       : titulo,
            modal       : true,
            autoScroll  : false,
            autoDestroy : true,
            resizable   : resize,
            bodyStyle   : color,
            items       :
			[
				{
					xtype		: 'panel',
					id			: 'contenedor_Win_Panel_Global',
					border		: false,
					bodyStyle 	: color,
					autoLoad	:
					{
						url		: archivo+'.php',
						scripts	: true,
						nocache	: true,
						params	: {	}
					}
				}
			]
		}).show();
    }

</script>
