<?php
/*=========================================// FUNCION PARA CALCULAR LOS TOTALES DE LA FACTURA //==============================================//
    ->subtotal      = (suma de (cantidad * costo) de cada uno de los articulos)
    ->descuento     =  {si es en porcentaje = (subtotal * descuento) /100
                       {si es en pesos = (subtotal-descuento)

    ->iva           = ((suma de ivas de todos los articulos)*(subtotal-descuento))/100
    ->retefuente    = ((suma de las retenciones de la factura)*(subtotal-descuento))/100
    ->total         = (subtotal-descuento) + iva + retefuente
*/

//arrayIva=[];
//arrayIva[id]={nombre:'',saldo:''}
//arrayIva[id].saldo=arrayIva[id].saldo+nuevo;

echo "<script type='text/javascript'>
        function calcTotalDocCompraVenta".$opcGrillaContable."(cantidad,descuento,costo,accion,tipoDesc,iva,cont){
          if(!document.getElementById('contenedor_totales_".$opcGrillaContable."')){ return; }

          var descuentoNeto    = 0
        	,   subtotal         = 0
        	,   subtotalNeto     = 0
        	,   ivaNeto          = 0
        	,   descuentoTotal   = 0
        	,   descuentoMostrar = 0;

          subtotal = (cantidad * costo);

          if(tipoDesc == 'porcentaje'){
            descuentoNeto = ((subtotal * descuento) / 100); //DESCUENTO NETO
            descuentoNeto = redondeo(descuentoNeto,".$_SESSION['DECIMALESMONEDA'].");
          }
          else if(tipoDesc == 'pesos'){
            descuentoNeto = redondeo(descuento,".$_SESSION['DECIMALESMONEDA'].");
          }

          subtotalNeto = parseFloat(subtotal) - parseFloat(descuentoNeto);      //SUBTOTAL NETO
          subtotalNeto = redondeo(subtotalNeto,".$_SESSION['DECIMALESMONEDA'].");

          if(iva > 0){
            ivaNeto = (parseFloat(arrayIva".$opcGrillaContable."[iva].valor) * parseFloat(subtotalNeto)) / 100; //IVA NETO
            // ivaNeto = redondeo(ivaNeto,".$_SESSION['DECIMALESMONEDA'].");
          }
          else{
            ivaNeto = 0; //IVA NETO
            iva     = 0;
          }

          //ACCIONES
          if(accion == 'agregar'){
        		subtotalAcumulado".$opcGrillaContable." = parseFloat(subtotalAcumulado".$opcGrillaContable.") + parseFloat(subtotalNeto);	 //SUBTOTAL ACUMULADO
        		ivaAcumulado".$opcGrillaContable."      = parseFloat(ivaAcumulado".$opcGrillaContable.") + parseFloat(ivaNeto);			       //IVA ACUMULADO

            //SI EL OBJETO SALDO EN EL ARRAY DEL IVA NO EXISTE, CREAR EL CAMPO SALDO CON EL PRIMER VALOR
            if(typeof(arrayIva".$opcGrillaContable."[iva].saldo) == 'undefined'){
              arrayIva".$opcGrillaContable."[iva].saldo = ivaNeto;
            }
            //SI YA EXISTE EL CAMPO SALDO EN EL OBJETO, ENTONCES ACUMULAR EL VALOR
            else{
              arrayIva".$opcGrillaContable."[iva].saldo = arrayIva".$opcGrillaContable."[iva].saldo + ivaNeto;
            }

            document.getElementById('costoTotalArticulo".$opcGrillaContable."_' + cont).value = subtotalNeto;
          }
          else if(accion == 'eliminar'){
            subtotalAcumulado".$opcGrillaContable." = parseFloat(subtotalAcumulado".$opcGrillaContable.") - parseFloat(subtotalNeto);
            ivaAcumulado".$opcGrillaContable."      = parseFloat(ivaAcumulado".$opcGrillaContable.") - parseFloat(ivaNeto);

            //SI EL OBJETO SALDO EN EL ARRAY DEL IVA EXISTE, RESTAR EL VALOR DEL IVA
            if(typeof(arrayIva".$opcGrillaContable."[iva].saldo) != 'undefined'){
              arrayIva".$opcGrillaContable."[iva].saldo -= ivaNeto;
            }
          }

          //RECORRER EL ARRAY DE LOS IVA Y ARMAR ELEMENTOS PARA EL DOM
          var labelIva = ''
          , simboloIva = ''
          , valoresIva = '';

          for(var id_iva in arrayIva".$opcGrillaContable."){
            if(typeof(arrayIva".$opcGrillaContable."[id_iva].saldo) != 'undefined'){
              if(arrayIva".$opcGrillaContable."[id_iva].saldo > 0){
                labelIva   += '<div style=\"margin-bottom:5px; overflow:hidden; width:100%; padding-left:3px; font-weight:bold; overflow:hidden;margin-bottom:5px;\"><div class=\"labelNombreRetencion\">'+arrayIva".$opcGrillaContable."[id_iva].nombre+'</div><div class=\"labelValorRetencion\">('+(arrayIva".$opcGrillaContable."[id_iva].valor*1)+'%)</div></div>';
                simboloIva += '<div style=\"margin-bottom:5px\">$</div>';
                valoresIva += '<div style=\"margin-bottom:5px\" title=\"'+formato_numero(arrayIva".$opcGrillaContable."[id_iva].saldo, ".$_SESSION['DECIMALESMONEDA'].", '.', ',')+'\" >'+formato_numero(arrayIva".$opcGrillaContable."[id_iva].saldo, ".$_SESSION['DECIMALESMONEDA'].", '.', ',')+'</div>';
              }
            }
          }

          //CALC RETENCIONES
          var contador              = 0
          ,   retenciones           = document.querySelectorAll('.capturarCheckboxAcumulado".$opcGrillaContable."')
          ,   labelRetenciones      = document.querySelectorAll('.capturaLabelAcumulado".$opcGrillaContable."')
          ,   id_retencion          = 0
          ,   valorRetencion        = 0
          ,   retencionTemp         = 0
          ,   listadoRetenciones    = ''
          ,   simboloRetencion      = ''
          ,   valoresRetenciones    = ''
          ,   divValoresRetenciones = '';

          //CICLO QUE RECORRE TODOS LOS CHECK DE RETENCIONES
          for(i in retenciones){
            if(typeof(retenciones[i].id) != 'undefined'){
              id_retencion = (retenciones[i].id).split('_')[1];

              if(objectRetenciones_".$opcGrillaContable."[id_retencion].tipo_retencion == 'ReteIva'){
                //VERIFICAR SI EL VALOR ES MAYOR O IGUAL A LA BASE DE LA RETENCION
                if (objectRetenciones_".$opcGrillaContable."[id_retencion].base>parseFloat(ivaAcumulado".$opcGrillaContable.")) {
                  continue;
                }
      					valorRetencion     += (parseFloat(ivaAcumulado".$opcGrillaContable.")* objectRetenciones_".$opcGrillaContable."[id_retencion].valor)/100;
      					valoresRetenciones  = valoresRetenciones+''+parseFloat((parseFloat(ivaAcumulado".$opcGrillaContable.")* objectRetenciones_".$opcGrillaContable."[id_retencion].valor)/100).toFixed(2);

                listadoRetenciones += '<div style=\"margin-bottom:5px; overflow:hidden; width:100%;\">'+labelRetenciones[i].innerHTML+'</div>';
                simboloRetencion    = '<div style=\"margin-bottom:5px\">$</div>'+simboloRetencion;
              }
              else if (objectRetenciones_".$opcGrillaContable."[id_retencion].tipo_retencion=='AutoRetencion') { continue; }
              else{
                //VERIFICAR SI EL VALOR ES MAYOR O IGUAL A LA BASE DE LA RETENCION
                if(objectRetenciones_".$opcGrillaContable."[id_retencion].base>parseFloat(subtotalAcumulado".$opcGrillaContable.")) {
                  continue;
                }
                valorRetencion     += (parseFloat(subtotalAcumulado".$opcGrillaContable.")* objectRetenciones_".$opcGrillaContable."[id_retencion].valor)/100;
			          valoresRetenciones  = valoresRetenciones+''+parseFloat(((parseFloat(subtotalAcumulado".$opcGrillaContable.") - parseFloat(descuentoTotal))* objectRetenciones_".$opcGrillaContable."[id_retencion].valor)/100).toFixed(2);

                listadoRetenciones+= '<div style=\"margin-bottom:5px; overflow:hidden; width:100%;\">'+labelRetenciones[i].innerHTML+'</div>';
                simboloRetencion   = '<div style=\"margin-bottom:5px\">$</div>'+simboloRetencion;
              }

              titulo_rete=formato_numero(valoresRetenciones, ".$_SESSION['DECIMALESMONEDA'].", '.', ',');

              divValoresRetenciones += '<div style=\"margin-bottom:5px\" title=\"'+titulo_rete+'\" >'+formato_numero(valoresRetenciones, ".$_SESSION['DECIMALESMONEDA'].", '.', ',')+'</div>';
              valoresRetenciones     = '';
            }
          }

          total".$opcGrillaContable." = (parseFloat(subtotalAcumulado".$opcGrillaContable.".toFixed(".$_SESSION['DECIMALESMONEDA']."))-parseFloat(valorRetencion.toFixed(".$_SESSION['DECIMALESMONEDA'].")))+parseFloat(ivaAcumulado".$opcGrillaContable.".toFixed(".$_SESSION['DECIMALESMONEDA']."));

          //RENDERIZA VALORES EN LA VENTANA
          document.getElementById('subtotalAcumulado".$opcGrillaContable."').innerHTML            = formato_numero(subtotalAcumulado".$opcGrillaContable.", ".$_SESSION['DECIMALESMONEDA'].", '.', ',');
          document.getElementById('subtotalAcumulado".$opcGrillaContable."').setAttribute('title', formato_numero(subtotalAcumulado".$opcGrillaContable.", ".$_SESSION['DECIMALESMONEDA'].", '.', ','));

          document.getElementById('divRetencionesAcumulado".$opcGrillaContable."').style.display  = 'inline';
          document.getElementById('idretencionAcumulado".$opcGrillaContable."').innerHTML         = listadoRetenciones;
          document.getElementById('simboloRetencionAcumulado".$opcGrillaContable."').innerHTML    = simboloRetencion;
          document.getElementById('retefuenteAcumulado".$opcGrillaContable."').innerHTML          = divValoresRetenciones;

          document.getElementById('labelIva".$opcGrillaContable."').innerHTML     = labelIva;
          document.getElementById('simboloIva".$opcGrillaContable."').innerHTML   = simboloIva;
          document.getElementById('ivaAcumulado".$opcGrillaContable."').innerHTML = valoresIva;
          //document.getElementById('ivaAcumulado".$opcGrillaContable."').setAttribute('title', formato_numero(ivaAcumulado".$opcGrillaContable.", ".$_SESSION['DECIMALESMONEDA'].", '.', ','));

          document.getElementById('totalAcumulado".$opcGrillaContable."').innerHTML = formato_numero(total".$opcGrillaContable.", ".$_SESSION['DECIMALESMONEDA'].", '.', ',');
          document.getElementById('totalAcumulado".$opcGrillaContable."').setAttribute('title', formato_numero(total".$opcGrillaContable.", ".$_SESSION['DECIMALESMONEDA'].", '.', ','));
        }

        function formato_numero(numero,decimales,separador_decimal,separador_miles){
          numero = parseFloat(numero);
          if(isNaN(numero)){ return ''; }
          if(decimales !== undefined){ numero = numero.toFixed(decimales); }  // Redondeamos

          // Convertimos el punto en separador_decimal
          numero = numero.toString().replace('.', separador_decimal !== undefined ? separador_decimal : ',');

          if(separador_miles){
            // Añadimos los separadores de miles
            var miles=new RegExp('(-?[0-9]+)([0-9]{3})');
            while(miles.test(numero)) { numero=numero.replace(miles, '$1' + separador_miles + '$2'); }
          }

          return numero;
        }

        function redondeo(numero,decimales){
          var flotante = parseFloat(numero);
          var resultado = Math.round(flotante*Math.pow(10,decimales))/Math.pow(10,decimales);
          return resultado;
        }
      </script>";
?>
