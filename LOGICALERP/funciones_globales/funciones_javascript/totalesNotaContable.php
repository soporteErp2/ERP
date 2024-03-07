<?php


echo"<script type='text/javascript'>

        function calcTotal".$opcGrillaContable."(debito,credito,accion){


            debito  = (isNaN(debito) || debito=='')? 0: debito;
            credito = (isNaN(credito) || credito=='')? 0: credito;

            if(!document.getElementById('contenedor_totales_".$opcGrillaContable."')){ return; }

            if (accion=='agregar') {
                debitoAcumulado".$opcGrillaContable."  = parseFloat(debitoAcumulado".$opcGrillaContable.")+parseFloat(debito);
                creditoAcumulado".$opcGrillaContable." = parseFloat(creditoAcumulado".$opcGrillaContable.")+parseFloat(credito);
            }
            else if (accion=='eliminar') {
               debitoAcumulado".$opcGrillaContable."  = parseFloat(debitoAcumulado".$opcGrillaContable.")-parseFloat(debito);
                creditoAcumulado".$opcGrillaContable." = parseFloat(creditoAcumulado".$opcGrillaContable.")-parseFloat(credito);
            }

            total".$opcGrillaContable." = (parseFloat(debitoAcumulado".$opcGrillaContable.")-parseFloat(creditoAcumulado".$opcGrillaContable."))*1;

            //RENDERIZA VALORES EN LA VENTANA
            document.getElementById('debitoAcumulado".$opcGrillaContable."').innerHTML  = formato_numero(debitoAcumulado".$opcGrillaContable.", 2, '.', ',');
            document.getElementById('debitoAcumulado".$opcGrillaContable."').setAttribute('title', formato_numero(debitoAcumulado".$opcGrillaContable.", 2, '.', ','));
            document.getElementById('creditoAcumulado".$opcGrillaContable."').innerHTML = formato_numero(creditoAcumulado".$opcGrillaContable.", 2, '.', ',');
            document.getElementById('creditoAcumulado".$opcGrillaContable."').setAttribute('title', formato_numero(creditoAcumulado".$opcGrillaContable.", 2, '.', ','));
            document.getElementById('totalAcumulado".$opcGrillaContable."').innerHTML   = formato_numero(total".$opcGrillaContable.", 2, '.', ',');
            document.getElementById('totalAcumulado".$opcGrillaContable."').setAttribute('title', formato_numero(total".$opcGrillaContable.", 2, '.', ','));
        }

        function formato_numero(numero, decimales, separador_decimal, separador_miles){ // v2007-08-06
            numero=parseFloat(numero);
            if(isNaN(numero)){
                return 'NaN';
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