<style>
  /*ESTILOS DEL WIZARD Y DE LA GRILLA ESTAN EN INDEX.CSS, ESTE ESTILO ES PARA PERSONALIZACION DE CONTENIDO*/
  .sub-content[data-position="right"]{width: calc(60% - 3px); }
  .sub-content[data-position="left"]{width: 40%; overflow:hidden;}
  .content-grilla-filtro { height: calc(50% - 45px);}
  .content-grilla-filtro .cell[data-col="1"]{width: 22px;}
  .content-grilla-filtro .cell[data-col="2"]{width: 89px;}
  .content-grilla-filtro .cell[data-col="3"]{width: 190px;}
  .sub-content [data-width="input"]{width: 150px;}
</style>
<div class="main-content">
  <div class="sub-content" data-position="right">
    <div class="title">FILTRAR POR CLIENTE</div>
    <div class="content-grilla-filtro">
      <div class="head">
        <div class="cell" data-col="1"></div>
        <div class="cell" data-col="2">Documento</div>
        <div class="cell" data-col="3">Nombre</div>
        <div class="cell" data-col="1" data-icon="search" title="Buscar Proveedor" onclick="ventanaBusquedaTercero();"></div>
      </div>
      <div class="body" id="body_grilla_filtro">
      </div>
    </div>
    <div class="title">FILTRAR POR CENTROS DE COSTOS</div>
    <div class="content-grilla-filtro">
      <div class="head">
        <div class="cell" data-col="2">Codigo</div>
        <div class="cell" data-col="2">Nombre</div>
        <div class="cell" data-col="1" data-icon="search" title="Buscar Centro de costos" onclick="ventanaBusquedaCentroCostos();"></div>
      </div>
      <div class="body" id="body_grilla_filtro_ccos">
      </div>
    </div>
  </div>
  <div class="sub-content" data-position="left">
    <div class="title" style="margin-bottom:15px;">FILTRO PRINCIPAL</div>
    <table style="margin:auto;">
      <tr>
        <td style="width:30%">Detallar Por</td>
        <td style="width:70%">
          <select id="detallado_principal">
            <option value="year">Año</option>
            <option value="monthYear">Mes Y Año</option>
          </select>
        </td>
      </tr>
    </table>
    <div class="title" style="margin-bottom:15px;margin-top:15px;">FILTRO POR AÑOS</div>
    <table style="margin:auto;">
      <tr>
        <td style="width:30%">Año Inicial</td>
        <td style="width:70%"><select id="beginYear"></select></td>
      </tr>
      <tr>
        <td style="width:30%">Año Final</td>
        <td style="width:70%"><select id="endYear"></select></td>
      </tr>
    </table>
  </div>
</div>
<script>
  Ext.onReady(function(){

    carga_sucursal_guardada();

    //CARGAR SUCURSAL GUARDADA
    function carga_sucursal_guardada(){
      try{
        if(typeof(localStorage.sucursalFP) != "undefined" && localStorage.sucursalFP != ""){
          setTimeout(function(){
            document.getElementById("filtro_sucursal_facturas_por_periodo").value = localStorage.sucursalFP;
          },200);
        }
      } catch{
        setTimeout('carga_sucursal_guardada()',500);
      }
    }

    //CREAR LAS OPCIONES CON AÑOS DINAMICAMENTE
    beginYear = document.getElementById("beginYear");
    endYear   = document.getElementById("endYear");
    for(i = 2000; i <= 2050; i++){
      optionInicio = document.createElement("option");
      optionInicio.value = i;
      optionInicio.text = i;
      optionFin = document.createElement("option");
      optionFin.value = i;
      optionFin.text = i;
      beginYear.appendChild(optionInicio);
      endYear.appendChild(optionFin);
    }

    //CARGAR FILTRO PRINCIPAL
    if(typeof(localStorage.detallado_principalFP) != "undefined"){
      if(localStorage.detallado_principalFP != ""){
        document.getElementById("detallado_principal").value = localStorage.detallado_principalFP;
      }
    }

    //CARGAR AÑO INICIAL GUARDADO
    if(typeof(localStorage.MyInformeFiltroFechaInicioFP) != "undefined"){
      if(localStorage.MyInformeFiltroFechaInicioFP != ""){
        document.getElementById("beginYear").value = localStorage.MyInformeFiltroFechaInicioFP;
      }
    }

    //CARGAR AÑO FINAL GUARDADO
    if(typeof(localStorage.MyInformeFiltroFechaFinalFP) != "undefined"){
      if(localStorage.MyInformeFiltroFechaFinalFP != ""){
        document.getElementById("endYear").value = localStorage.MyInformeFiltroFechaFinalFP;
      }
    }

    //RECORRER EL ARRAY PARA RENDERIZAR LOS PROVEEDORES DEL FILTRO
    var rows = '';
    tercerosConfiguradosFP.forEach(function(elemento) {rows += elemento;});
    document.getElementById('body_grilla_filtro').innerHTML=rows;

    //RECORRER EL ARRAY PARA RENDERIZAR LOS TERCEROS DEL FILTRO
    var rowsCentroCostos  = '';
    centroCostosConfiguradosFP.forEach(function(elemento) {rowsCentroCostos += elemento;});
    document.getElementById('body_grilla_filtro_ccos').innerHTML = rowsCentroCostos;
  });
</script>
