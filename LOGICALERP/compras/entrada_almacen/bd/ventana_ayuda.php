<style>
    /*ESTILOS DEL WIZARD Y DE LA GRILLA ESTAN EN INDEX.CSS, ESTE ESTILO ES PARA PERSONALIZACION DE CONTENIDO*/
    .sub-content[data-position="right"]{width: 100%; }
    /*.sub-content[data-position="left"]{width: 40%; overflow:auto;}*/
    /*.content-grilla-filtro { height: calc(50% - 45px);}
    .content-grilla-filtro .cell[data-col="1"]{width: 22px;}
    .content-grilla-filtro .cell[data-col="2"]{width: 89px;}
    .content-grilla-filtro .cell[data-col="3"]{width: 190px;}
    .sub-content [data-width="input"]{width: 120px;}*/

</style>

<div class="main-content">
    <div class="sub-content" data-position="right">
        <div class="title">ENTRADA DE ALMACEN</div>
            <br>
            La entrada de almacen de este tipo, genera el movimiento de ingreso de inventario,
            y contabiliza las cuentas de transito configuradas en el panel de control.
            <br>
            <br>
        <div class="title">AJUSTE DE INVENTARIO</div>
            <br>
            Este tipo de entrada de almacen, genera el movimiendo de ingreso al inventario,
            pero se contabiliza de manera inversa al movimiento de la remision de venta,
            es decir incrementa la cuenta de inventario y contabiliza como contrapartida la cuenta que tenga cada item configurada
            <br>
    </div>
</div>