<?php
/*=========================================// FUNCION PARA CALCULAR LOS TOTALES DE LA FACTURA //==============================================//
    ->subtotal      = (suma de (cantidad * costo) de cada uno de los articulos)
    ->descuento     =  {si es en porcentaje = (subtotal * descuento) /100
                       {si es en pesos = (subtotal-descuento)

    ->iva           = ((suma de ivas de todos los articulos)*(subtotal-descuento))/100
    ->retefuente    = ((suma de las retenciones de la factura)*(subtotal-descuento))/100
    ->total         = (subtotal-descuento) + iva + retefuente 
*/

echo"<script type='text/javascript'>
   
        function calcTotalDocCompraVenta".$opcGrillaContable."(cantidad,costo,accion,cont){
            if(!document.getElementById('contenedor_totales_".$opcGrillaContable."')){ return; }

        	var descuentoNeto    = 0
        	,   subtotal         = 0
        	,   subtotalNeto     = 0
        	,   ivaNeto          = 0
        	,   descuentoTotal   = 0
        	,   descuentoMostrar = 0;

        	
            subtotal=(cantidad*costo);
                
            subtotalNeto = parseFloat(subtotal);
             
            //ACCIONES    
            if (accion=='agregar') {
        		subtotalAcumulado".$opcGrillaContable."= parseFloat(subtotalAcumulado".$opcGrillaContable.") + parseFloat(subtotalNeto);		     //SUBTOTAL ACUMULADO
            }
            else if (accion=='eliminar') {
                subtotalAcumulado".$opcGrillaContable." = parseFloat(subtotalAcumulado".$opcGrillaContable.") - parseFloat(subtotalNeto);
            }          
            
            total".$opcGrillaContable." = (parseFloat(subtotalAcumulado".$opcGrillaContable."));
                        
            //RENDERIZA VALORES EN LA VENTANA
            document.getElementById('subtotalAcumulado".$opcGrillaContable."').innerHTML= formato_numero(subtotalAcumulado".$opcGrillaContable.", ".$_SESSION['DECIMALESMONEDA'].", '.', ',');
            document.getElementById('totalAcumulado".$opcGrillaContable."').innerHTML   = formato_numero(total".$opcGrillaContable.", ".$_SESSION['DECIMALESMONEDA'].", '.', ',');
        }
        
        function formato_numero(numero, decimales, separador_decimal, separador_miles){ // v2007-08-06
            numero=parseFloat(numero);
            if(isNaN(numero)){
                return '';
            }
        
            if(decimales!==undefined){
                // Redondeamos
                numero=numero.toFixed(decimales);
            }
        
            // Convertimos el punto en separador_decimal
            numero=numero.toString().replace('.', separador_decimal!==undefined ? separador_decimal : ',');
        
            if(separador_miles){
                // Añadimos los separadores de miles
                var miles=new RegExp('(-?[0-9]+)([0-9]{3})');
                while(miles.test(numero)) {
                    numero=numero.replace(miles, '$1' + separador_miles + '$2');
                }
            }
    
            return numero;
        }

    </script>";

?>