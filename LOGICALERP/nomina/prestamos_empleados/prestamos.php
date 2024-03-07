<?php
	include("../../../configuracion/conectar.php");
	include("../../../configuracion/define_variables.php");
	include("../../../misc/MyGrilla/class.MyGrilla.php");

      $id_empresa      = $_SESSION['EMPRESA'];
      $filtro_sucursal = $_SESSION['SUCURSAL'];

      // VALIDAR SI SE VA A ACTUALIZAR LA PARA CONSULTAR SI EL PRESTAMOS ESTA RELACIONADO EN OTROS DOCUMENTOS, SI ESTA ENTONCES NO SE DEBE EDITAR
      if ($opcion=='Vupdate') {
            $sql="SELECT consecutivo FROM nomina_prestamos_empleados WHERE activo=1 AND id_empresa=$id_empresa AND id=$id";
            $query=mysql_query($sql,$link);
            $consecutivo=mysql_result($query,0,'consecutivo');
            echo '<script>

                        var tbar=Ext.getCmp("Win_Editar_Prestamo").getTopToolbar();
                        if (tbar) {
                          tbar.add(

                              "->",
                              {
                                  xtype : "tbtext",
                                  text  : \'<div id="titleDocumento" style="text-align:center; font-size:14px; font-weight:bold;">Consecutivo No.<br>'.$consecutivo.'</div>\',
                                  scale : "large",
                              }

                          );
                          tbar.doLayout();
                        }



                  </script>';

            // CONSULTAR LAS PLANILLAS DE NOMINA
            $sql="SELECT
                        id_planilla,
                        valor_concepto
                  FROM
                        nomina_planillas_empleados_conceptos AS NPEC
                  INNER JOIN nomina_planillas AS NP ON NPEC.id_planilla=NP.id
                  WHERE
                        NP.activo = 1
                  AND NP.id_empresa = $id_empresa
                  AND NP.estado=1
                  AND NPEC.id_prestamo=$id
                  GROUP BY
                        NPEC.id_planilla";
            $query=mysql_query($sql,$link);
            $id_planilla=mysql_result($query,0,'id_planilla');
            if ($id_planilla>0) {
                  echo '<script>
                              Ext.getCmp("BtnV_nomina_prestamos_empleados").disable();
                              Ext.getCmp("BtnV_eliminar_nomina_prestamos_empleados").disable();
                        </script>';
            }
      }


	/**//////////////////////////////////////////////**/
	/**		      INICIALIZACION DE LA CLASE  	      /**/
	/**/								                            /**/
	/**/	        $grilla = new MyGrilla();			    /**/
	/**/							                              /**/
	/**//////////////////////////////////////////////**/



	//CONFIGURACION//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

		//NOMBRE DE LA GRILLA
      $grilla->GrillaName = 'nomina_prestamos_empleados';  	//NOMBRE DE LA GRILLA (DEBE SER UNICO POR CADA GRILLA DE LA APLICACION)
		//QUERY
      $grilla->TableName  = 'nomina_prestamos_empleados';		//NOMBRE DE LA TABLA DE CONSULTA EN LA BASE DE DATOS DE
      $grilla->MyWhere    = 'activo = 1 AND id_empresa='.$id_empresa.' AND id_empleado='.$id_empleado;		//WHERE DE LA CONSULTA A LA TABLA "$TableName"
      $grilla->OrderBy    = '';
      $grilla->MySqlLimit = '0,100';			//LIMITE DE LA CONSULTA
		//TAMANO DE LA GRILLA
      $grilla->AutoResize  = 'true';			//SI LA GRILLA ES AUTORESIZABLE (LIQUIDA) -> "true" SI NO -> "false"
      //$grilla->Ancho     = 800;				//ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'
      //$grilla->Alto      = 220;				//ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'
      $grilla->QuitarAncho = 150;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'true'
      $grilla->QuitarAlto  = 180;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'true'
		//TOOLBAR Y CAMPO DE BUSQUEDA
      $grilla->Gtoolbar           = 'true';			//SI LA GRILLA LLEVA EL TOOLBAR DE BUSQUEDA
      $grilla->CamposBusqueda     = 'fecha_inicial,fecha_final,consecutivo,usuario';		//VARIABLE QUE DEFINE LOS CAMPOS DE LA BD DONDE SE BUSCARA
      $grilla->DivActualiBusqueda = '' ;				//VARIABLE QUE DEFINE LA CAPA DONDE SE ACTUALIZA LA GRILLA DESPUES DE UNA BUSQUEDA

		//CONFIGURACION DE CAMPOS EN LA GRILLA
      $grilla->AddRow('Fecha','fecha_inicio',65);
      $grilla->AddRow('Fecha inicio Pago','fecha_inicio_pago',100);
			$grilla->AddRow('Consecutivo','consecutivo',70);
      $grilla->AddRow('Concepto','concepto',150);
      $grilla->AddRow('Valor cuota','valor_cuota',100);
			$grilla->AddRow('Cuotas Restantes','cuotas_restantes',110);
      $grilla->AddRow('Valor Restante','valor_prestamo_restante',100);
      $grilla->AddRow('Valor Prestamo','valor_prestamo',100);
      $grilla->AddRow('Cuotas','cuotas',50);
      $grilla->AddRowImage('Historico','<center><img src="img/doc16.png" style="cursor:pointer" width="16" height="16" onclick="ventanaDocumentosCruzados([id])" title="Ver documentos cruzados a este prestamo" /></center>','55');

      $grilla->FContenedorAncho     = 300;
      $grilla->FColumnaGeneralAncho = 300;
      $grilla->FColumnaGeneralAlto  = 25;
      $grilla->FColumnaLabelAncho   = 120;
      $grilla->FColumnaFieldAncho   = 160;

		//CONFIGURACION DE LA VENTANA DE INSERT, UPDATE Y DELETE
			$grilla->VentanaAuto            = 'true';			//SI LA VENTANA DE INSERT, UPDATE Y DELETE ES AUTOMATICA O MANUAL
			$grilla->TituloVentana          = 'Prestamo'; //NOMBRE DE LA VENTANA DE INSER, UPDATE Y DELETE
			$grilla->CerrarDespuesDeAgregar = 'true';
			$grilla->VBarraBotones          = 'true';			//SI HAY O NO BARRA DE BOTONES
			$grilla->VBotonNuevo            = 'true';			//SI LLEVA EL BOTON DE AGREGAR REGISTRO
			$grilla->VBotonNText            = 'Nuevo'; 	//TEXTO DEL BOTON DE NUEVO REGISTRO
      $grilla->AddBotton('Regresar','regresar','Win_Ventana_prestamos_empleado.close();');
			$grilla->VBotonNImage           = 'add_new';	    //IMAGEN CSS DEL BOTON
			//$grilla->VAutoResize          = 'true';			//SI LA VENTANA ES AUTORESIZABLE (LIQUIDA) -> "true" SI NO -> "false"
			$grilla->VAncho                 = 360;				//ANCHO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'false'
			$grilla->VAlto                  = 460;				//ALTO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'false'
			//$grilla->VQuitarAncho         = 540;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ANCHO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'true'
			// $grilla->VQuitarAlto            = 20;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ALTO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'true'
			$grilla->VAutoScroll            = 'true';			//SI LA VENTANA TIENE O NO AUTOSCROLL
			$grilla->VBotonEliminar         = 'true';			//SI MUESTRA BOTON DE ELIMINAR EN LA VENTANA DE EDICION
			$grilla->VComporEliminar        = 'true';			//COMPORTAMIENTO DEL BOTON DE ELIMINAR ("true" ES CAMPO ACTIVO DE 1 A 0) ("false" -> ELIMINA EL REGISTRO DE LA BASE DE DATOS)

      //CONFIGURACION DE LO FORMULARIOS DE CAPTURA Y EDICION
      $grilla->AddTextField('Fecha','fecha_inicio',150,'true','false');
      $grilla->AddTextField('Fecha Inicio Pago','fecha_inicio_pago',150,'true','false');
      $grilla->AddTextField('','id_tercero',150,'true','true');
      $grilla->AddTextField('Tercero','tercero',150,'true','false');
      // $grilla->AddTextField('Tipo Documento Cruce','tipo_documento_cruce',150,'false','false');

      $grilla->AddTextField('','id_documento_cruce',150,'false','true');
      $grilla->AddComboBox('Tipo Documento Cruce','tipo_documento_cruce',150,'true','false','CE:CE - Comprobante de Egreso,false:Sin Documento');

      $grilla->AddTextField('Documento Cruce','numero_documento_cruce',150,'false','false');
      $grilla->AddTextField('','id_concepto',150,'true','true');
      $grilla->AddTextField('Concepto','concepto',130,'true','false');
      $grilla->AddTextField('Valor Prestamo','valor_prestamo',150,'true','false');
      $grilla->AddTextField('Cuotas','cuotas',150,'true','false');
      $grilla->AddTextField('Valor Cuota','valor_cuota',150,'true','false');
      // $grilla->AddTextField('Observacion','observacion',150,'true','false');
      $grilla->AddTextArea('Observacion','observacion',150,100,'false');
      $grilla->AddTextField('','id_empleado',150,'true','true',$id_empleado);
      $grilla->AddTextField('','nombre_empleado',150,'true','true',$nombre_empleado);
      $grilla->AddTextField('','id_sucursal',150,'true','true',$filtro_sucursal);
      $grilla->AddTextField('','id_empresa',150,'true','true',$id_empresa);

        // $grilla->AddTextField('Descripcion','descripcion',200,'true','false');
        // $grilla->AddValidation('descripcion','mayuscula');


      $grilla->MenuContext    = 'true';   //MENU CONTEXTUAL
      $grilla->MenuContextEliminar = 'false';
      $grilla->AddMenuContext('Abonar / Pagar','ventas16','ventanaAbonar([id])');


	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


	/**//////////////////////////////////////////////////////////////**/
	/**///	    INICIALIZACION DE LA GRILLA	  		                ///**/
	/**/										                                        /**/
	/**/	$grilla->Link = $link;  	//Conexion a la BD	            /**/
	/**/	$grilla->inicializa($_POST);//variables POST		          /**/
	/**/	$grilla->GeneraGrilla(); 	// Inicializa la Grilla	        /**/
	/**/										                                        /**/
	/**//////////////////////////////////////////////////////////////**/



if(!isset($opcion)){ ?>

<script>

      // VENTANA PARA VER LOS DOCUMENTOS CRUZADOS
      function ventanaDocumentosCruzados(id) {

            Win_Ventana_historico_prestamo = new Ext.Window({
                width       : 500,
                height      : 520,
                id          : 'Win_Ventana_historico_prestamo',
                title       : 'Pagos del prestamo',
                modal       : true,
                autoScroll  : false,
                closable    : false,
                autoDestroy : true,
                autoLoad    :
                {
                    url     : 'prestamos_empleados/historico.php',
                    scripts : true,
                    nocache : true,
                    params  :
                    {
                        id : id,
                        var2 : 'var2',
                    }
                },
                tbar        :
                [
                    {
                        xtype   : 'buttongroup',
                        columns : 3,
                        title   : 'Opciones',
                        style   : 'border-right:none;',
                        items   :
                        [
                            {
                                xtype       : 'button',
                                width       : 60,
                                height      : 56,
                                text        : 'Regresar',
                                scale       : 'large',
                                iconCls     : 'regresar',
                                iconAlign   : 'left',
                                hidden      : false,
                                handler     : function(){ BloqBtn(this); Win_Ventana_historico_prestamo.close(id) }
                            }
                        ]
                    }
                ]
            }).show();
      }

      // FUNCION PARA ABONAR,PAGAR O CONDONAR LOS PRESTAMOS
      function ventanaAbonar(id_prestamo) {

        Win_Ventana_ventana_abono = new Ext.Window({
            width       : 500,
            height      : 460,
            id          : 'Win_Ventana_ventana_abono',
            title       : 'Pagar Prestamo',
            modal       : true,
            autoScroll  : false,
            closable    : false,
            autoDestroy : true,
            autoLoad    :
            {
                url     : 'prestamos_empleados/pagar_prestamo.php',
                scripts : true,
                nocache : true,
                params  :
                {
                    id_prestamo : id_prestamo,
                    id_empleado : '<?php echo $id_empleado; ?>',
                }
            }
        }).show();
      }

</script>

<?php
}
if ($opcion=='Vupdate' || $opcion=='Vagregar') {
?>
<script>

      // PONER LOS CAMPOS DE LA GRILLA COMO FECHA
      new Ext.form.DateField({
          emptyText  : 'Inserte una fecha...',    //PLACEHOLDER
          fieldLabel : 'Date from today',     //SI TIENE LABEL
          format     : 'Y-m-d',               //FORMATO
          width      : 150,                   //ANCHO
          allowBlank : false,
          showToday  : false,
          applyTo    : 'nomina_prestamos_empleados_fecha_inicio',
          editable   : false,                 //EDITABLE
          listeners  : { select: function() {  } }
      });

      new Ext.form.DateField({
          emptyText  : 'Inserte una fecha...',    //PLACEHOLDER
          fieldLabel : 'Date from today',     //SI TIENE LABEL
          format     : 'Y-m-d',               //FORMATO
          width      : 150,                   //ANCHO
          allowBlank : false,
          showToday  : false,
          applyTo    : 'nomina_prestamos_empleados_fecha_inicio_pago',
          editable   : false,                 //EDITABLE
          listeners  : { select: function() {  } }
      });

      // ESTILOS DE LOS CAMPOS
      var inputTercero=document.getElementById('nomina_prestamos_empleados_tercero');
      inputTercero.setAttribute('style','float:left;width: 128px;');
      var inputConcepto=document.getElementById('nomina_prestamos_empleados_concepto');
      inputConcepto.setAttribute('style','float:left;width: 128px;');
      var inputDocCruce=document.getElementById('nomina_prestamos_empleados_numero_documento_cruce');
      inputDocCruce.setAttribute('style','float:left;width: 128px;');
      var selectTipoDoc =document.getElementById('nomina_prestamos_empleados_tipo_documento_cruce');
      selectTipoDoc.setAttribute('onchange','verifica_doc_cruce()');

      document.getElementById('nomina_prestamos_empleados_valor_cuota').readOnly=true;
      inputConcepto.readOnly = true;
      inputDocCruce.readOnly = true;
      inputTercero.readOnly  = true;

      // EVENTOS PARA LOS CALCULOS DEL VALOR DE LA CUOTA
      document.getElementById('nomina_prestamos_empleados_valor_prestamo').setAttribute('onkeyup','calculaCuota()');
      document.getElementById('nomina_prestamos_empleados_cuotas').setAttribute('onkeyup','calculaCuota()');

      // AGREGAR LOS BOTONES DE BUSQUEDA DE LOS CAMPOS
      var divBtn = document.createElement("div");
      divBtn.setAttribute("class","divBtnBuscarPuc");
      divBtn.setAttribute("onclick","ventanaBuscarTerceroPrestamo()");
      divBtn.setAttribute('title','Buscar Cuenta Colgaap');
      divBtn.innerHTML = '<img src="img/buscar20.png" />';
      document.getElementById("DIV_nomina_prestamos_empleados_tercero").appendChild(divBtn);

      var divBtn = document.createElement("div");
      divBtn.setAttribute("class","divBtnBuscarPuc");
      divBtn.setAttribute("onclick","ventanaBuscarConcepto()");
      divBtn.setAttribute('title','Buscar Cuenta Colgaap');
      divBtn.innerHTML = '<img src="img/buscar20.png" />';
      document.getElementById("DIV_nomina_prestamos_empleados_concepto").appendChild(divBtn);

      var divBtn = document.createElement("div");
      divBtn.setAttribute("class","divBtnBuscarPuc");
      divBtn.setAttribute("onclick","ventanaBuscarDocumentoCruce()");
      divBtn.setAttribute('title','Buscar Documento Cruce');
      divBtn.innerHTML = '<img src="img/buscar20.png" />';
      document.getElementById("DIV_nomina_prestamos_empleados_numero_documento_cruce").appendChild(divBtn);

      // CALCULAR EL VALOR DE LA CUOTA
      function calculaCuota() {
            var valor_prestamo = document.getElementById('nomina_prestamos_empleados_valor_prestamo').value;
            var cuotas = document.getElementById('nomina_prestamos_empleados_cuotas').value;

            // document.getElementById('nomina_prestamos_empleados_valor_prestamo').value=formato_numero(valor_prestamo,"<?php echo $_SESSION['DECIMALESMONEDA']; ?>", '.', ',');
            if (valor_prestamo<1 || cuotas<1) { return; }

            var total =  valor_prestamo/cuotas ;
            document.getElementById('nomina_prestamos_empleados_valor_cuota').value=total;
            // document.getElementById('nomina_prestamos_empleados_valor_cuota').value=formato_numero(total,"2", '.', ',');
      }

      //====================== VENTANA PARA BUSCAR LOS CONCEPTOS ========================================//
      function ventanaBuscarConcepto() {

         Win_Ventana_bucar_concepto = new Ext.Window({
             width       : 500,
             height      : 450,
             id          : 'Win_Ventana_bucar_concepto',
             title       : 'Seleccione un Concepto',
             modal       : true,
             autoScroll  : false,
             closable    : false,
             autoDestroy : true,
             autoLoad    :
             {
                 url     : 'prestamos_empleados/bd/grillaBuscarConceptos.php',
                 scripts : true,
                 nocache : true,
                 params  :
                 {
                     cargaFuncion : 'rederizaResultadoVentanaConcepto(id)',
                     id_empleado  : '<?php echo $id_empleado; ?>',
                 }
             }
         }).show();
      }

      //============ RENDERIZAR EL CONCEPTO BUSCADO ============================//
      function rederizaResultadoVentanaConcepto(id) {
        var concepto = document.getElementById('div_buscarConceptos_descripcion_'+id).innerHTML;

        document.getElementById('nomina_prestamos_empleados_id_concepto').value=id;
        document.getElementById('nomina_prestamos_empleados_concepto').value=concepto;

        Win_Ventana_bucar_concepto.close();
      }


      // VENTANA PARA BUSCAR EL CENTRO DE COSTOS
      function ventanaBuscarDocumentoCruce() {

        var type_doc     = document.getElementById('nomina_prestamos_empleados_tipo_documento_cruce').value;
        var title        = '';
        var url          = '';
        var cargaFuncion = '';

        if (type_doc=='CE') {
          title        = 'Comprobantes de Egreso';
          url          = 'prestamos_empleados/bd/comprobante_egreso.php';
          cargaFuncion = 'rederizaResultadoVentanaCentroCostos(id)';
        }

         Win_Ventana_bucar_centro_costos = new Ext.Window({
             width       : 600,
             height      : 600,
             id          : 'Win_Ventana_bucar_centro_costos',
             title       : title,
             modal       : true,
             autoScroll  : false,
             closable    : false,
             autoDestroy : true,
             autoLoad    :
             {
                 url     : url,
                 scripts : true,
                 nocache : true,
                 params  :
                 {
                     cargaFuncion : cargaFuncion,
                     id_empleado  : '<?php echo $id_empleado; ?>',
                 }
             }
         }).show();
      }

      //============ RENDERIZAR EL CONCEPTO BUSCADO ============================//
      function rederizaResultadoVentanaCentroCostos(id) {
        var consecutivo = document.getElementById('div_comprobante_egreso_consecutivo_'+id).innerHTML;

        document.getElementById('nomina_prestamos_empleados_id_documento_cruce').value=id;
        document.getElementById('nomina_prestamos_empleados_numero_documento_cruce').value=consecutivo;

        Win_Ventana_bucar_centro_costos.close();
      }

      function formato_numero(numero, decimales, separador_decimal, separador_miles){ // v2007-08-06

        numero=parseFloat(numero);
        if(isNaN(numero)){ return ''; }
        if(decimales!==undefined){ numero=numero.toFixed(decimales); }  // Redondeamos

        // Convertimos el punto en separador_decimal
        numero=numero.toString().replace('.', separador_decimal!==undefined ? separador_decimal : ',');

        if(separador_miles){
            // Añadimos los separadores de miles
            var miles=new RegExp('(-?[0-9]+)([0-9]{3})');
            while(miles.test(numero)) { numero=numero.replace(miles, '$1' + separador_miles + '$2'); }
        }

        return numero;
      }

      //VENTANA PARA BUSCAR LA ENTIDAD
      function ventanaBuscarTerceroPrestamo(){
          var myalto  = Ext.getBody().getHeight();
          var myancho = Ext.getBody().getWidth();

          Win_Ventana_ventana_buscar_tercero_prestamo = new Ext.Window({
              width       : myancho-100,
              height      : myalto-50,
              id          : 'Win_Ventana_ventana_buscar_tercero_prestamo',
              title       : 'Terceros',
              modal       : true,
              autoScroll  : false,
              closable    : false,
              autoDestroy : true,
              autoLoad    :
              {
                  url     : '../funciones_globales/grillas/BusquedaTerceros.php',
                  scripts : true,
                  nocache : true,
                  params  :
                  {
                      cargaFuncion : 'responseVentanaBuscarTerceroPrestamo(id)',
                      nombre_grilla : 'terceros',
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
                              xtype       : 'button',
                              width       : 60,
                              height      : 56,
                              text        : 'Regresar',
                              scale       : 'large',
                              iconCls     : 'regresar',
                              iconAlign   : 'left',
                              handler     : function(){ Win_Ventana_ventana_buscar_tercero_prestamo.close(id) }
                          }
                      ]
                  }
              ]
          }).show();
      }

      function responseVentanaBuscarTerceroPrestamo(id) {
          var nombre = document.getElementById('div_terceros_nombre_'+id).innerHTML;
          document.getElementById('nomina_prestamos_empleados_id_tercero').value=id;
          document.getElementById('nomina_prestamos_empleados_tercero').value=nombre;
          Win_Ventana_ventana_buscar_tercero_prestamo.close();
      }

      verifica_doc_cruce();

      function verifica_doc_cruce() {
        var type_doc=document.getElementById('nomina_prestamos_empleados_tipo_documento_cruce').value;
        // console.log(type_doc);
        if ((type_doc=='false' || type_doc=='') && '<?php echo $opcion; ?>'=='Vagregar' ) {
          document.getElementById('EmpConte_nomina_prestamos_empleados_numero_documento_cruce').style.display='none';
          document.getElementById('nomina_prestamos_empleados_id_documento_cruce').value='0';
          document.getElementById('nomina_prestamos_empleados_numero_documento_cruce').value=' ';
        }
        else if( '<?php echo $opcion; ?>'=='Vagregar' ){
          document.getElementById('EmpConte_nomina_prestamos_empleados_numero_documento_cruce').style.display='block';
          document.getElementById('nomina_prestamos_empleados_id_documento_cruce').value='0';
          document.getElementById('nomina_prestamos_empleados_numero_documento_cruce').value='';
        }

      }

</script>
<?php
}
?>


