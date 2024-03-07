<?php
  include("../../../../configuracion/conectar.php");
  include("../../../../configuracion/define_variables.php");
  include("../../../funciones_globales/funciones_php/randomico.php");

  $opcGrillaContable == 'conciliaciones'?$tablaPrincipal='conciliaciones':'';
  $id_empresa        = $_SESSION['EMPRESA'];
  $id_sucursal       = $_SESSION['SUCURSAL'];
  $id_usuario        = $_SESSION['IDUSUARIO'];
  $nombre_sucursal   = $_SESSION['NOMBRESUCURSAL'];
  $documento_usuario = $_SESSION['CEDULAFUNCIONARIO'];
  $nombre_usuario    = $_SESSION['NOMBREFUNCIONARIO'];
  $acumScript        = '';
  $estado            = '';
  $fecha             = date('Y-m-d');
  $cuenta_banco      = 0;
?>
<script>
    Ext.getCmp("Btn_guardar_<?php echo $opcGrillaContable; ?>").disable();
    Ext.getCmp("Btn_editar_<?php echo $opcGrillaContable; ?>").disable();
    Ext.getCmp("Btn_cancelar_<?php echo $opcGrillaContable; ?>").disable();
    Ext.getCmp("Btn_restaurar_<?php echo $opcGrillaContable; ?>").disable();
    Ext.getCmp("BtnGroup_Estado1_<?php echo $opcGrillaContable; ?>").hide();
    Ext.getCmp("BtnGroup_Guardar_<?php echo $opcGrillaContable; ?>").show();
</script>
<?php
  $acumScript .= (user_permisos(6,'false') == 'true')? 'Ext.getCmp("Btn_guardar_'.$opcGrillaContable.'").enable();' : '';    //guardar
  $acumScript .= (user_permisos(8,'false') == 'true')? 'Ext.getCmp("Btn_cancelar_'.$opcGrillaContable.'").enable();' : '';   //cancelar

  //============== SI NO EXISTE CONCILIACION SE CREA EL ID UNICO =============//
  if(!isset($id_documento)){
    $readonly = '';

    // CREACION DEL ID UNICO
    $random_conciliacion = responseUnicoRanomico();

    $sqlInsert = "INSERT INTO $tablaPrincipal (id_empresa,random,fecha_extracto,id_sucursal,id_usuario,documento_usuario,nombre_usuario,sucursal)
                  VALUES('$id_empresa','$random_conciliacion','$fecha','$id_sucursal',$id_usuario,'$documento_usuario','$nombre_usuario','$nombre_sucursal')";
    $queryInsert  = mysql_query($sqlInsert,$link);

    $sqlSelectId  = "SELECT id FROM $tablaPrincipal  WHERE random = '$random_conciliacion' LIMIT 0,1";
    $id_documento = mysql_result(mysql_query($sqlSelectId,$link),0,'id');

    $acumScript .= 'new Ext.form.DateField({
                      format     : "Y-m-d",
                      width      : 145,
                      allowBlank : false,
                      showToday  : false,
                      applyTo    : "fecha_inicio'.$opcGrillaContable.'",
                      editable   : false,
                      value      : "2017-12-12",
                      listeners  : { select: function() { cargaTabla'.$opcGrillaContable.'(); } }
                    });

                    new Ext.form.DateField({
                      format     : "Y-m-d",
                      width      : 145,
                      allowBlank : false,
                      showToday  : false,
                      applyTo    : "fecha_fin'.$opcGrillaContable.'",
                      editable   : false,
                      value      : "2018-01-02",
                      listeners  : { select: function() { cargaTabla'.$opcGrillaContable.'(); } }
                    });';
  }

  //======================== SI EXISTE LA CONCILIACION =======================//
  else{

    $sql = "SELECT * FROM $tablaPrincipal WHERE id = '$id_documento'";

    $query = mysql_query($sql,$link);

    $id_extracto          = mysql_result($query,0,'id_extracto');
    $tipo                 = mysql_result($query,0,'tipo');
    $numero_documento     = mysql_result($query,0,'numero_documento');
    $fecha_inicio            = mysql_result($query,0,'fecha_inicio');
    $fecha_fin            = mysql_result($query,0,'fecha_fin');
    $valor                = mysql_result($query,0,'valor');
    $estado               = mysql_result($query,0,'estado');
    $cuentaconciliaciones = mysql_result($query,0,'cuenta');
    $descripcion_cuenta   = mysql_result($query,0,'descripcion_cuenta');

    if ($readonly =='false') {

        $readonly='';
        $acumScript .= 'new Ext.form.DateField({
                            format     : "Y-m-d",
                            width      : 145,
                           allowBlank : false,
                            showToday  : false,
                            applyTo    : "fecha_inicio'.$opcGrillaContable.'",
                            editable   : false,
                            // value      : new Date(),
                            listeners  : { select: function() { updateFecha_conciliacion'.$opcGrillaContable.'(this.value,"fecha_inicio"); } }
                        });

                        new Ext.form.DateField({
                            format     : "Y-m-d",
                            width      : 145,
                            allowBlank : false,
                            showToday  : false,
                            applyTo    : "fecha_fin'.$opcGrillaContable.'",
                            editable   : false,
                            // value      : new Date(),
                            listeners  : { select: function() { updateFecha_conciliacion'.$opcGrillaContable.'(this.value,"fecha_fin"); } }
                        });';
    }else{$readonly='readonly';}

    $acumScript .= 'document.getElementById("fecha_inicio'.$opcGrillaContable.'").value        = "'.$fecha_inicio.'";
                    document.getElementById("fecha_fin'.$opcGrillaContable.'").value           = "'.$fecha_fin.'";
                    document.getElementById("cuenta'.$opcGrillaContable.'").value              = "'.$cuentaconciliaciones.'";
                    document.getElementById("descripcion_cuenta'.$opcGrillaContable.'").value  = "'.$descripcion_cuenta.'";';

    $bodyizquierda = load_izquierda($id_documento,$fecha_inicio,$fecha_fin,$estado,$opcGrillaContable,$idTablaPrincipal,$id_empresa,$link);
    $bodyderecha   = load_derecha($id_documento,$cuentaconciliaciones,$fecha_inicio,$fecha_fin,$estado,$opcGrillaContable,$idTablaPrincipal,$id_empresa,$link);

  }
?>
<style>
  .fondo_modal_saldos {
    z-index  : 99999;
    top      : 0px;
    width    : 100%;
    height   : 100%;
    display  : table;
    left     : 0px;
    position : absolute !important;
  }
  #modal {
    display        : table-cell;
    vertical-align : middle;
  }
  .bodydivs {
    display :flex ;
    width   : 100%;
    height  : 100%;
  }
  overflow-x: hidd/*en;
  overflow-y: auto;
  min-width: 700px;*/
  .renderFilasconciliacion {
    margin        : 4px;
    padding       : 5px;
    border        : 1px solid #15428b;
    border-radius : 7pt;
    -webkit-flex  : 3 1 60%;
    flex          : 3 1 60%;
    -webkit-order : 2;
    order         : 2;
  }
  .divTable {
    display: table;
    width: 100%;
  }
  .divTableRow {
    display: table-row;
  }
  .divTableHeading {
    background-color: #EEE;
    display: table-header-group;

  }
  .divTableHead {
    border      : 1px solid #999999;
    display     : table-cell;
    text-align  : center;
    font-weight : bold;
    font-size   : 13px;
    height      : 34px;
  }
  .divTableCell {
    border      : 1px solid #999999;
    display     : table-cell;
    font-size   : 12px;
    height      : 20px;
  }
  .divTableHeading {
    background-color: #EEE;
    display: table-header-group;
    font-weight: bold;
  }
  .divTableFoot {
    background-color: #EEE;
    display: table-footer-group;
    font-weight: bold;
  }
  .divTableBody {
    display: table-row-group;
  }
  .tablaConciliacion {
    width: 96%;
    margin-left: 2%;
    height: 420px;
  }
  .titleTable {
    border            : 1px solid #999999;
    caption-side      : top;
    display           : table-caption;
    text-align        : center;
    font-weight       : bold;
    background-color  : #eeeeee;
  }
</style>
<div class="contenedorconciliaciones" id="contenedor<?php echo $opcGrillaContable; ?>">
  <div class="bodyTop">
    <div class="contInfoFact">
    <div id="render_btns_<?php echo $opcGrillaContable; ?>"></div>
      <div id="terminar<?php echo $opcGrillaContable; ?>"></div>
      <div class="contTopFila">

        <div id="cuentaContable" class="renglonTop" style="width:290px;">
          <div class="labelTop" style="float:left; width:100%;">Cuenta Contable</div>
          <div id="renderCuenta<?php echo $opcGrillaContable; ?>" style="float:left; margin-left:-20px; width:20px; height:19px; overflow:hidden;"></div>
          <div class="campoTop">
            <input class="input_extractos" type="text" id="cuenta<?php echo $opcGrillaContable; ?>" style="width:22% !important; float:left;"  onkeyup="buscarCuenta<?php echo $opcGrillaContable; ?>(event,this)" >
            <div style="width:10% !important; float:left;background-color:#F3F3F3; height:100%; text-align:center;">-</div>
            <input class="input_extractos" type="text" id="descripcion_cuenta<?php echo $opcGrillaContable; ?>" style="width:68% !important; float:left;" Readonly>
          </div>
          <div class="iconBuscarProveedor" onclick="ventanaBuscarCuenta<?php echo $opcGrillaContable; ?>()" id="imgBuscarProveedor" title="Buscar Tercero">
            <img src="img/buscar20.png"/>
          </div>
        </div>

        <div id="fechas_<?php echo $opcGrillaContable; ?>">
          <div class="renglonTop" style="width:135px;">
            <div id="cargaFechaInicio<?php echo $opcGrillaContable; ?>"></div>
            <div class="labelTop">Fecha inicio</div>
            <div class="campoTop"><input class="input_conciliacion" type="text" id="fecha_inicio<?php echo $opcGrillaContable; ?>" value="" readonly></div>
          </div>
          <div class="renglonTop" style="width:135px;">
            <div id="cargaFechaFin<?php echo $opcGrillaContable; ?>"></div>
            <div class="labelTop">Fecha final</div>
            <div class="campoTop"><input class="input_conciliacion" type="text" id="fecha_fin<?php echo $opcGrillaContable; ?>" value="" readonly></div>
          </div>
        </div>

        <div class="renglonTop">
          <div class="labelTop">Usuario</div>
          <div class="campoTop"style="width:277px;"><input class="input_conciliacion" type="text" id="usuario<?php echo $opcGrillaContable; ?>" value="<?php echo $_SESSION['NOMBREFUNCIONARIO']; ?>" readonly/></div>
        </div>
      </div>
    </div>
  </div>
  <div class="bodydivs renderFilasconciliacion" id="bodyArticulos<?php echo $opcGrillaContable; ?>">
    <div class="tablaConciliacion" id="renderFilasConciliacion"></div>
  </div>
<script>
  <?php echo $acumScript; ?>

  //=========================== CARGAR CONCILIACION ==========================//
  function cargaTabla<?php echo $opcGrillaContable; ?>(){
    fecha_inicio = document.getElementById("fecha_inicio<?php echo $opcGrillaContable; ?>").value;
    fecha_fin    = document.getElementById("fecha_fin<?php echo $opcGrillaContable; ?>").value;
    cuenta       = document.getElementById("cuenta<?php echo $opcGrillaContable; ?>").value;

    if(fecha_inicio != "" && fecha_fin != "" && cuenta != ""){
      Ext.get("render_btns_<?php echo $opcGrillaContable; ?>").load({
        url     : 'conciliacion_bancaria/conciliacion/bd/bd.php',
        scripts : true,
        nocache : true,
        params  :
        {
          opc               : 'load_conciliacion',
          opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
          id_documento      : '<?php echo $id_documento; ?>',
          fecha_inicio      : fecha_inicio,
          fecha_fin         : fecha_fin,
          cuenta            : cuenta
        }
      });
    }
  }

  //============================ ACTUALIZAR FECHA ============================//
  function UpdateFecha<?php echo $opcGrillaContable; ?>(fecha) {
    Ext.get("render_btns_<?php echo $opcGrillaContable; ?>").load({
      url     : 'conciliacion_bancaria/conciliacion/bd/bd.php',
      scripts : true,
      nocache : true,
      params  :
      {
        opc               : 'UpdateFecha',
        opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
        id_documento      : '<?php echo $id_documento; ?>',
        fecha             : fecha
      }
    });
  }

  //============================= BUSCAR CUENTA ==============================//
  function ventanaBuscarCuenta<?php echo $opcGrillaContable; ?>(){
      var myalto  = Ext.getBody().getHeight();
      var myancho = Ext.getBody().getWidth();

      Win_Ventana_buscar_cuenta_conciliacion = new Ext.Window({
          width       : myancho-100,
          height      : myalto-50,
          id          : 'Win_Ventana_buscar_cuenta_conciliacion',
          title       : 'Seleccionar Cuenta',
          modal       : true,
          autoScroll  : false,
          closable    : false,
          autoDestroy : true,
          autoLoad    :
          {
              url     : '../funciones_globales/grillas/buscar_cuenta_puc.php',
              scripts : true,
              nocache : true,
              params  :
              {
                  opcGrilla    : 'puc',
                  tabla_puc    : 'puc',
                  cargaFuncion : "responseVentanaBuscarCuenta<?php echo $opcGrillaContable; ?>(id)",
              }
          },
          tbar        :
          [
              {
                  xtype       : 'button',
                  text        : 'Regresar',
                  scale       : 'large',
                  iconCls     : 'regresar',
                  iconAlign   : 'left',
                  handler     : function(){ Win_Ventana_buscar_cuenta_conciliacion.close(id) }
              },'-'
          ]
      }).show();
  }

  function responseVentanaBuscarCuenta<?php echo $opcGrillaContable; ?>(id){
    var           cuenta  = document.getElementById('div_puc_cuenta_' + id).innerHTML
    ,  descripcionCuenta  = document.getElementById('div_puc_descripcion_' + id).innerHTML

    document.getElementById('cuenta<?php echo $opcGrillaContable; ?>').value              = cuenta;
    document.getElementById('descripcion_cuenta<?php echo $opcGrillaContable; ?>').value  = descripcionCuenta;

    Ext.get('renderCuenta<?php echo $opcGrillaContable; ?>').load({
      url     : 'conciliacion_bancaria/conciliacion/bd/bd.php',
      scripts : true,
      nocache : true,
      params  :
      {
        opc               : 'buscarCuenta',
        cuenta            : cuenta,
        id_documento      : '<?php echo $id_documento; ?>',
        opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
        tablaPrincipal    : '<?php echo $tablaPrincipal; ?>'
      }
    });

    Win_Ventana_buscar_cuenta_conciliacion.close(id);
  }

  function buscarCuenta<?php echo $opcGrillaContable; ?>(event,input){
    var tecla         = input? event.keyCode : event.which
    ,   value         = input.value;

    if(tecla == 13){
      Ext.get('renderCuenta<?php echo $opcGrillaContable; ?>').load({
        url     : 'conciliacion_bancaria/conciliacion/bd/bd.php',
        scripts : true,
        nocache : true,
        params  :
        {
          opc               : 'buscarCuenta',
          cuenta            : value,
          id_documento      : '<?php echo $id_documento; ?>',
          opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
          tablaPrincipal    : '<?php echo $tablaPrincipal; ?>'
        }
      });
    }
  }

  //=========================== GENERAR DOCUMENTO ============================//
  function guardar<?php echo $opcGrillaContable; ?>(){

    //VALIDAR QUE LOS DATOS DE LA CABECERA ESTEN LLENOS
    inputNull = 0;
    $('.input_extractos').each(function(i, obj){
      if(obj.value == ""){
        inputNull++;
      }
    });
    if(inputNull > 0){
      alert("Faltan datos por completar en la cabecera del documento.")
      generar = 'false';
      return;
    } else{
      generar = 'true';
    }

    //VALIDAR QUE EXISTAN ARTICULOS GUARDADOS
    if($(".bodyDivArticulos").length > 1){
      generar = 'true';
    } else{
      alert("No hay detalles guardados en este extracto.")
      generar = 'false';
    }

    //VALIDAR QUE NO EXISTAN DIFERENCIAS ENTRE EL EXTRACTO Y EL DETALLE
    diferenciaExtracto = parseFloat(document.getElementById("totalAcumulado<?php echo $opcGrillaContable?>").innerHTML);
    if(diferenciaExtracto == 0.00 || diferenciaExtracto == 0){
      generar = 'true';
    } else{
      generar = 'false';
      alert("No se puede generar el documento porque la diferencia del extracto es mayor o menor a cero(0).");
    }

    //GENERAR DOCUMENTO
    if(generar != 'false'){
      var observacion = document.getElementById("observacion<?php echo $opcGrillaContable; ?>").value;
      observacion = observacion.replace(/[\#\<\>\'\"]/g, '');
      Ext.get('render_btns_<?php echo $opcGrillaContable; ?>').load({
        url     : 'conciliacion_bancaria/extracto/bd/bd.php',
        scripts : true,
        nocache : true,
        params  :
        {
          opc               : 'terminarGenerar',
          opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
          id                : '<?php echo $id_documento; ?>',
        }
      });
    }
  }

  //========================= CANCELAR UN DOCUMENTO ==========================//
  function cancelar<?php echo $opcGrillaContable; ?>(){
      var contDetalles = 0;

      if(!document.getElementById('DivArticulos<?php echo $opcGrillaContable; ?>')){ alert('El documento esta en blanco, no hay nada para cancelar'); return; }

      arrayIdsArticulos = document.getElementById('DivArticulos<?php echo $opcGrillaContable; ?>').querySelectorAll('.campoNombreArticulo');
      for(i in arrayIdsArticulos){if(arrayIdsArticulos[i].innerHTML != '' ){ contDetalles++; } }

      if(contDetalles > 0){
          if(confirm('Esta seguro de Eliminar el presente Documento y su contenido relacionado')){

              Ext.get("render_btns_<?php echo $opcGrillaContable; ?>").load({
                  url     : 'conciliacion_bancaria/extracto/bd/bd.php',
                  scripts : true,
                  nocache : true,
                  params  :
                  {
                      opc               : 'cancelarDocumento',
                      id                : '<?php echo $id_documento; ?>',
                      opcGrillaContable : '<?php echo $opcGrillaContable; ?>'

                  }
              });
          };
      }
  }

  //============================ BUSCAR DOCUMENTO ============================//
  function buscar<?php echo $opcGrillaContable; ?>(){
      var myalto  = Ext.getBody().getHeight();
      var myancho = Ext.getBody().getWidth();

      Win_Ventana_buscar_<?php echo $opcGrillaContable; ?> = new Ext.Window({
          width       : myancho-100,
          height      : myalto-50,
          id          : 'Win_Ventana_buscar_<?php echo $opcGrillaContable; ?>',
          title       : 'Seleccionar ',
          modal       : true,
          autoScroll  : false,
          closable    : false,
          autoDestroy : true,
          autoLoad    :
          {
              url     : 'conciliacion_bancaria/extracto/bd/buscarGrillaContable.php',
              scripts : true,
              nocache : true,
              params  :
              {
                  opc               : 'buscar_<?php echo $opcGrillaContable; ?>',
                  opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
                  tablaPrincipal    : '<?php echo $opcGrillaContable == "Extractos" ? "extractos":"conciliaciones"; ?>'

              }
          },
          tbar        :
          [
              {
                  xtype   : 'buttongroup',
                  columns : 3,
                  title   : 'Opciones',
                  items   :
                  [
                      {
                           xtype      : 'button',
                          text        : 'Regresar',
                          scale       : 'large',
                          iconCls     : 'regresar',
                          height      : 56,
                          iconAlign   : 'top',
                          handler     : function(){ Win_Ventana_buscar_<?php echo $opcGrillaContable; ?>.close(id) }
                      }
                  ]
              }
          ]
      }).show();
  }

  //============================ NUEVO DOCUMENTO =============================//
  function nueva<?php echo $opcGrillaContable; ?>(){
    Ext.get("contenedorconciliaciones").load({
      url     : 'conciliacion_bancaria/conciliacion/grillaContable.php',
      scripts : true,
      nocache : true,
      params  :
      {
        id                : '<?php echo $id_documento; ?>',
        opcGrillaContable : '<?php echo $opcGrillaContable; ?>'
      }
    });
  }

</script>
