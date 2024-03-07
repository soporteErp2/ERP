<?php


    echo"<script type='text/javascript'>
            function calcTotal".$opcGrillaContable."(debito,credito,accion){

                if(!document.getElementById('contenedor_totales_".$opcGrillaContable."')){ return; }

                if (accion=='agregar') {
                    subtotalDebito".$opcGrillaContable."  += parseFloat(debito);
                    subtotalCredito".$opcGrillaContable." += parseFloat(credito);
                }
                else if (accion=='eliminar') {
                    subtotalDebito".$opcGrillaContable."  -= parseFloat(debito);
                    subtotalCredito".$opcGrillaContable." -= parseFloat(credito);
                }

                if('$opcGrillaContable' == 'ReciboCaja'){ total".$opcGrillaContable." = (parseFloat(subtotalCredito".$opcGrillaContable.") - parseFloat(subtotalDebito".$opcGrillaContable."))*1; }
                else{ total".$opcGrillaContable." = (parseFloat(subtotalCredito".$opcGrillaContable.") - parseFloat(subtotalDebito".$opcGrillaContable."))*1; }

                //RENDERIZA VALORES EN LA VENTANA
                document.getElementById('subtotalDebito".$opcGrillaContable."').innerHTML = formato_numero(subtotalDebito".$opcGrillaContable.", ".$_SESSION['DECIMALESMONEDA'].", '.', ',');
                document.getElementById('subtotalDebito".$opcGrillaContable."').setAttribute('title', formato_numero(subtotalDebito".$opcGrillaContable.", ".$_SESSION['DECIMALESMONEDA'].", '.', ','));

                document.getElementById('subtotalCredito".$opcGrillaContable."').innerHTML = formato_numero(subtotalCredito".$opcGrillaContable.", ".$_SESSION['DECIMALESMONEDA'].", '.', ',');
                document.getElementById('subtotalCredito".$opcGrillaContable."').setAttribute('title', formato_numero(subtotalCredito".$opcGrillaContable.", ".$_SESSION['DECIMALESMONEDA'].", '.', ','));

                document.getElementById('totalAcumulado".$opcGrillaContable."').innerHTML = formato_numero(total".$opcGrillaContable.", ".$_SESSION['DECIMALESMONEDA'].", '.', ',');
                document.getElementById('totalAcumulado".$opcGrillaContable."').setAttribute('title', formato_numero(total".$opcGrillaContable.", ".$_SESSION['DECIMALESMONEDA'].", '.', ','));
            }

            function formato_numero(numero, decimales, separador_decimal, separador_miles){ // v2007-08-06
                numero=parseFloat(numero);
                if(isNaN(numero)){ return ''; }

                if(decimales!==undefined){ numero=numero.toFixed(decimales); }                // Redondeamos

                // Convertimos el punto en separador_decimal
                numero=numero.toString().replace('.', separador_decimal!==undefined ? separador_decimal : ',');

                if(separador_miles){
                    var miles=new RegExp('(-?[0-9]+)([0-9]{3})');                // Añadimos los separadores de miles
                    while(miles.test(numero)) {
                        numero=numero.replace(miles, '$1' + separador_miles + '$2');
                    }
                }
                return numero;
            }

        </script>";

?>