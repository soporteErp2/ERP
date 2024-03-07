<?php

    include("../../../../configuracion/conectar.php");
    include("../../../../configuracion/define_variables.php");
    include("../../../../misc/MyGrilla/class.MyGrilla.php");

    /**//////////////////////////////////////////////**/
    /**///       INICIALIZACION DE LA CLASE       ///**/
    /**/                                            /**/
    /**/    $form = new MyGrilla();                 /**/
    /**/                                            /**/
    /**//////////////////////////////////////////////**/

    //CONFIGURACION//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    //NOMBRE del FORMULARIO
        $form->GrillaName   = 'FichaProveedor';      //NOMBRE DE LA GRILLA (DEBE SER UNICO POR CADA GRILLA DE LA APLICACION)
        $form->Formulario   = 'true';      //NO MUESTRA LA GRILLA, SOLO FORMULARIO DE INSERT Y UPDATE
        $form->TableName    = 'terceros_ficha_tecnica';      //NOMBRE DE LA TABLA EN LA BASE DE DATOS
        $form->MyWhere      = "activo=1 AND id_tercero = '$id_tercero'";     //WHERE DE LA CONSULTA A LA TABLA ""

    //CONFIGURACION FORMULARIO
        $form->FContenedorAncho       = 700;
        $form->FColumnaGeneralAncho   = 350;
        $form->FColumnaGeneralAlto    = 25;
        $form->FColumnaLabelAncho     = 150;
        $form->FColumnaFieldAncho     = 200;

    //CAMPOS DEL FORMULARIO
        $form->AddTextField('Nombre','id_tercero',160,'true','true');
        $form->AddSeparator('Informacion Tributaria');

        $form->AddComboBox('Gran Contribuyente','gran_contribuyente',160,'true','false','Si:Si,No:No');//estatico
        $form->AddComboBox('Responsable de IVA','responsable_iva',160,'true','false','Si:Si,No:No');
        $form->AddComboBox('Autoretenedor','autoretenedor',160,'true','false','Si:Si,No:No');
        $form->AddComboBox('Responsable de ICA','responsable_ica',160,'true','false','Si:Si,No:No');
        $form->AddTextField('Codigo Actividad economica(ICA)','cod_actividad_economica',160,'true');
        $form->AddTextField('Codigo CIIU','cod_ciiu',160,'true');
        $form->AddComboBox('Autoretenedor ICA','autoretenedor_ica',160,'true','false','Si:Si,No:No');
        $form->AddTextField('Tarifa X 1000','tarifa_por_mil',160,'true');
        $form->AddValidation('tarifa_por_mil','numero-real');

        $form->AddSeparator('Informacion para Pagos');

        $form->AddTextField('Pago a la orden de','pago_orden',160,'true');
        $form->AddTextField('Cuenta No.','numero_cuenta',160,'true');
        $form->AddComboBox('Tipo de Cuenta','tipo_cuenta',160,'true','false','ahorros:Ahorros,corriente:Corriente');
        $form->AddTextField('Entidad','entidad',160,'true');
        $form->AddTextField('Persona Contacto para Pagos','contacto_pago',160,'true');

        $form->AddSeparator('Informacion Compa&ntilde;ia');

        $form->AddComboBox('Tipo de Proveedor','tipo_proveedor',160,'true','false','talento:Talento,renta:Renta,servicios:Servicios');
        $form->AddComboBox('Forma de Pago','id_forma_pago',160,'true','true','configuracion_formas_pago,id,nombre,true');

        $form->AddComboBox('Descuento por Pronto Pago','desc_pronto_pago',160,'true','false','Si:Si,No:No');

        $form->AddTextField('Porcentaje Descuento','porc_desc_pronto_pago',160,'true');
        $form->AddValidation('porc_desc_pronto_pago','numero-real');

        $form->AddSeparator('Informacion Funcionario Contacto');

        $form->AddTextField('Nombre','nombre_contacto_cartera',160,'true');
        $form->AddTextField('Cargo','cargo_contacto_cartera',160,'true');
        $form->AddTextField('Telefono','telefono_contacto_cartera',160,'true');
        // $form->AddValidation('telefono_contacto_cartera','numero');
        $form->AddTextField('Extension','extension_contacto_cartera',160,'true');
        // $form->AddValidation('extension_contacto_cartera','numero');
        $form->AddTextField('Correo Electronico','email_contacto_cartera',160,'true');
        $form->AddValidation('email_contacto_cartera','email');
        $form->AddTextField('Fax','fax_contacto_cartera',160,'true');

        $form->AddSeparator('Observaciones');
        $form->AddTextArea('Observaciones','observaciones',160,50,'false');



    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


    /**//////////////////////////////////////////////////////////////**/
    /**///              INICIALIZACION DE LA GRILLA               ///**/
    /**/                                                            /**/
    /**/    $form->Link = $link;      //Conexion a la BD            /**/
    /**/    $form->inicializa($_POST);//variables POST              /**/
    /**/    $form->GeneraGrilla();    // Inicializa la Grilla       /**/
    /**/                                                            /**/
    /**//////////////////////////////////////////////////////////////**/

if($opcion == 'Vupdate' || $opcion == 'Vagregar'){ ?>

    <script></script>

<?php
}

if(!isset($opcion)){ ?>

    <script>

        mostrarInputDescuento();

        document.getElementById("FichaProveedor_desc_pronto_pago").addEventListener("change", function(){
            mostrarInputDescuento();
        });

        document.getElementById("FichaProveedor_id_tercero").value = "<?php echo $id_tercero; ?>";//EL CAMPO OCULTO DEL ID TERCERO

        //DESAPARECEMOS EL LABEL DEL TEXTAREA PARA QUE ESTE OCUPE TODO EL ESPACIO
        document.getElementById("EmpLabel_FichaProveedor_observaciones").style.display = "none";

        //COLOCARLE ESTILOS AL TEXT AREA DE OBSERVACIONES
		document.getElementById("DIV_FichaProveedor_observaciones").style.width   = "660px";
		document.getElementById("DIV_FichaProveedor_observaciones").style.padding = "5px 0px 0px 0px";
		document.getElementById("FichaProveedor_observaciones").style.width       = "660px";
		document.getElementById("FichaProveedor_observaciones").style.height      = "100px";
		document.getElementById("FichaProveedor_observaciones").placeholder       = "escriba sus observaciones...";

        function mostrarInputDescuento(){
             descuento = document.getElementById("FichaProveedor_desc_pronto_pago").value;

             if(descuento == 'Si'){
                  document.getElementById("EmpConte_FichaProveedor_porc_desc_pronto_pago").style.display='block';
                  //document.getElementById("FichaProveedor_porc_desc_pronto_pago").value= '';
             }
             else{
                  document.getElementById("EmpConte_FichaProveedor_porc_desc_pronto_pago").style.display='none';
                  document.getElementById("FichaProveedor_porc_desc_pronto_pago").value= 0;
             }
        }

    </script>

<?php
} ?>