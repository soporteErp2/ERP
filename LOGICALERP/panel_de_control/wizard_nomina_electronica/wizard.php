
<link rel="stylesheet" type="text/css" href="wizard_nomina_electronica/wizard.css">
<script type="text/javascript" src="wizard_nomina_electronica/wizard.js" ></script>

<div id="WizCapaPrincipal">
    <div id="WizCapaIzquierda">
        <div class="bot">
            <div class="head">
                <div class="face">
                    <div class="loader" id="loader">Loading...</div>
                    <div class="eye"></div>
                    <div class="eye"></div>
                </div>
            </div>
            <div class="body">
                <div class="left-arm"></div>
                <div class="right-arm"></div>
            </div>
        </div>
    </div>
    <div id="WizCapaDerecha">
        <div class="WizTitulo">
            Asistente Para configurar la nomina electronica <br><br>
        </div>
        <div  class="WizContenido" id="WizContenido">
            <div id="content-1">
                <span>
                    Bienvenido al asistente de configuracion de nomina electronica, el sistema le guiara paso a paso 
                    en la configuracion necesaria y puesta en marcha del sistema de nomina electronica, por favor
                    lea atentamente cada indicacion y sigala cumplidamente para una correcta implementacion del sistema, 
                    para iniciar haga click en el boton iniciar, luego siga las indicaciones.
                </span>
                    <br>
                    <br>
                <span style="font-weight:bold;color: #dc3545;">
                    ¡Advertencia!<br> los cambios realizados no se pueden reversar, si no va a utilizar nomina electronica, no continue
                    con este asistente y cierre la ventana, pues en este proceso se modifica la base de datos y los registros almacenados
                </span>
                <br>
                <br>
                <br>
                <button onclick="this.disabled=true; payRollObj.tablesConfiguration()">Iniciar</button>    
            </div>
            <div id="content-2">
                <table class="table-form">
                    <tr>
                        <td id="main-text-content-2">Validando la existencia de las tablas en la base de datos </td>
                        <td><img src='../../temas/clasico/images/loading.gif' ></td>
                    </tr>
                    <tr class="thead">
                        <td>Tabla</td>
                        <td>Estado</td>
                        <td>Proceso</td>
                    </tr>
                    <tr>
                        <td>Consecutivos</td>
                        <td id="consecutivesText">pendiente</td>
                        <td id="consecutivesIcon"><img src='../../temas/clasico/images/BotonesTabs/Prospecto.png' ></td>
                    </tr>
                    <tr>
                        <td>Tipos de nomina electronica</td>
                        <td id="typeDocText">pendiente</td>
                        <td id="typeDocIcon"><img src='../../temas/clasico/images/BotonesTabs/Prospecto.png' ></td>
                    </tr>
                    <tr>
                        <td>Tipos de ajuste nomina electronica</td>
                        <td id="typeDocEditText">pendiente</td>
                        <td id="typeDocEditIcon"><img src='../../temas/clasico/images/BotonesTabs/Prospecto.png' ></td>
                    </tr>
                    <!-- <tr>
                        <td>Tipos de contrato</td>
                        <td id="contractTypeText" >pendiente</td>
                        <td id="contractTypeIcon"><img src='../../temas/clasico/images/BotonesTabs/Prospecto.png' ></td>
                    </tr> -->
                    <tr>
                        <td>Tipo de trabajador</td>
                        <td id="employeeTypeText">pendiente</td>
                        <td id="employeeTypeIcon"><img src='../../temas/clasico/images/BotonesTabs/Prospecto.png' ></td>
                    </tr>
                    <tr>
                        <td>Subtipo de trabajador</td>
                        <td id="subEmployeeTypeText">pendiente</td>
                        <td id="subEmployeeTypeIcon"><img src='../../temas/clasico/images/BotonesTabs/Prospecto.png' ></td>
                    </tr>
                    <tr>
                        <td>Formas de pago</td>
                        <td id="wayToPayTypeText">pendiente</td>
                        <td id="wayToPayTypeIcon"><img src='../../temas/clasico/images/BotonesTabs/Prospecto.png' ></td>
                    </tr>
                    <tr>
                        <td>Medios de pago</td>
                        <td id="payMethodTypeText">pendiente</td>
                        <td id="payMethodTypeIcon"><img src='../../temas/clasico/images/BotonesTabs/Prospecto.png' ></td>
                    </tr>
                    <tr>
                        <td>Tipo hora extra o recargo</td>
                        <td id="overTimeTypeText">pendiente</td>
                        <td id="overTimeTypeIcon"><img src='../../temas/clasico/images/BotonesTabs/Prospecto.png' ></td>
                    </tr>
                    <tr>
                        <td>Tipos de moneda</td>
                        <td id="coinTypeText">pendiente</td>
                        <td id="coinTypeIcon"><img src='../../temas/clasico/images/BotonesTabs/Prospecto.png' ></td>
                    </tr>
                    <tr>
                        <td>Idiomas</td>
                        <td id="languageTypeText">pendiente</td>
                        <td id="languageTypeIcon"><img src='../../temas/clasico/images/BotonesTabs/Prospecto.png' ></td>
                    </tr>
                    <tr>
                        <td>Tabla de configuracion</td>
                        <td id="configuratioStatusText">pendiente</td>
                        <td id="configuratioStatusIcon"><img src='../../temas/clasico/images/BotonesTabs/Prospecto.png' ></td>
                    </tr>
                    <tr>
                        <td>Tabla de la planilla</td>
                        <td id="documentStatusText">pendiente</td>
                        <td id="documentStatusIcon"><img src='../../temas/clasico/images/BotonesTabs/Prospecto.png' ></td>
                    </tr>
                    <tr>
                        <td>Tabla de la planilla (empleados)</td>
                        <td id="documentEmployesStatusText">pendiente</td>
                        <td id="documentEmployesStatusIcon"><img src='../../temas/clasico/images/BotonesTabs/Prospecto.png' ></td>
                    </tr>   
                    <tr>
                        <td>Tabla de la planilla (listado conceptos)</td>
                        <td id="documentEmployesConceptsText">pendiente</td>
                        <td id="documentEmployesConceptsIcon"><img src='../../temas/clasico/images/BotonesTabs/Prospecto.png' ></td>
                    </tr>                 
                    <tr>
                        <td>Tabla de la planilla (configuracion detalle conceptos)</td>
                        <td id="detailConfigText">pendiente</td>
                        <td id="detailConfigIcon"><img src='../../temas/clasico/images/BotonesTabs/Prospecto.png' ></td>
                    </tr>
                    <tr>
                        <td>Tabla de la planilla (detalle conceptos)</td>
                        <td id="configText">pendiente</td>
                        <td id="configIcon"><img src='../../temas/clasico/images/BotonesTabs/Prospecto.png' ></td>
                    </tr>
                    <tr>
                        <td>Tabla de la planilla (fechas de pago)</td>
                        <td id="payDateText">pendiente</td>
                        <td id="payDateIcon"><img src='../../temas/clasico/images/BotonesTabs/Prospecto.png' ></td>
                    </tr>

                </table>
            </div>
            <div id="content-3">
                <table class="table-form">
                    <tr>
                        <td id="main-text-content-3">Actualizando estructura las tablas en la base de datos </td>
                        <td><img src='../../temas/clasico/images/loading.gif' ></td>
                    </tr>
                    <tr class="thead">
                        <td>Tabla</td>
                        <td>Estado</td>
                        <td>Proceso</td>
                    </tr>
                    <tr>
                        <td>Tipos de liquidacion de nomina</td>
                        <td id="payrollText">pendiente</td>
                        <td id="payrollIcon"><img src='../../temas/clasico/images/BotonesTabs/Prospecto.png' ></td>
                    </tr>
                    <tr>
                        <td>Contrato del empleado</td>
                        <td id="employeeContractText">pendiente</td>
                        <td id="employeeContractIcon"><img src='../../temas/clasico/images/BotonesTabs/Prospecto.png' ></td>
                    </tr>
                    <tr>
                        <td>Informacion de la empresa</td>
                        <td id="companyText">pendiente</td>
                        <td id="companyIcon"><img src='../../temas/clasico/images/BotonesTabs/Prospecto.png' ></td>
                    </tr>
                    <tr>
                        <td>Conceptos de nomina</td>
                        <td id="payRollConceptsText">pendiente</td>
                        <td id="payRollConceptsIcon"><img src='../../temas/clasico/images/BotonesTabs/Prospecto.png' ></td>
                    </tr>
                </table>
            </div>

            <div id="content-4">
                <table>
                    <tr>
                        <td colspan="2">
                            A contininuacion diligencie los prefijos y consecutivos (a libre eleccion del usuario) que tendran cada tipo de documento 
                            de nomina electronica, todos los campos son obligatorios 
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">                                
                            <b>Configuracion prefijos y consecutivos Nomina individual
                        </td>
                    </tr>
                    <tr>
                        <td>Prefijo</td>
                        <td><input type="text" id="nomina_individual_prefijo" value="NETX" readonly></td>
                    </tr>
                    <tr>
                        <td>Consecutivo</td>
                        <td><input type="text" id="nomina_individual_consecutivo"></td>
                    </tr>
                    <tr>
                        <td colspan="2">                                
                            <b>Configuracion prefijos y consecutivos ajuste modificacion
                        </td>
                    </tr>
                    <tr>
                        <td>Prefijo</td>
                        <td><input type="text" id="ajuste_modificacion_prefijo" value="NEAJ" readonly></td>
                    </tr>
                    <tr>
                        <td>Consecutivo</td>
                        <td><input type="text" id="ajuste_modificacion_consecutivo"></td>
                    </tr>
                    <tr>
                        <td>
                            <button onclick="this.disabled=true; payRollObj.saveConsecutives(this)">Guardar</button>
                        </td>
                        <td id="content-2-btn-next" style="display:none;">
                            <button onclick="document.getElementById('content-4').classList.add('forward');">Saltar</button>
                        </td>
                    </tr>
                </table>
            </div>
            <div id="content-5">
                <table>
                    <tr>
                        <td>
                            El proceso a continuacion insertara informacion en las tablas de la base de datos para despues
                            ser utilizadas en el proceso de la nomina electronica, si ya realizo este paso le recomendamos
                            no lo repita, debido a que los registros de las tablas se eliminan y se insertan unos nuevos
                            asi que si ya genero nominas electronicas puede presentar errores futuros por registros
                            que se sobre escribieron, el sistema detecta si ya se inserto la informacion y habilitara el boton saltar para que no repita el paso, si no ha realizado el proceso aun haga click en insertar para insertar la informacion necesaria en la base de datos 
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <button onclick="this.disabled=true; payRollObj.insertDataBaseData(this)">Insertar</button>
                        </td>
                        <td id="content-5-btn-next" style="display:none;">
                            <button onclick="document.getElementById('content-5').classList.add('forward');">Saltar</button>
                        </td>
                    </tr>
                    <tr>
                        <td>Tipos documentos electronicos</td>
                        <td id="documentTypeText" data-table="nomina_configuracion_tipo_documentos" data-type="text">pendiente</td>
                        <td id="documentTypeIcon" data-table="nomina_configuracion_tipo_documentos" data-type="icon"><img src='../../temas/clasico/images/BotonesTabs/Prospecto.png' ></td>
                    </tr>
                    <tr>
                        <td>Tipos de documentos de ajuste  </td>
                        <td id="documentPatchText" data-table="nomina_configuracion_tipo_documentos_ajuste">pendiente</td>
                        <td id="documentPatchIcon" data-table="nomina_configuracion_tipo_documentos_ajuste" ><img src='../../temas/clasico/images/BotonesTabs/Prospecto.png' ></td>
                    </tr>
                    <tr>
                        <td>Tipos de liquidacion</td>
                        <td id="liquidationTypeText" data-table="nomina_tipos_liquidacion" >pendiente</td>
                        <td id="liquidationTypeIcon" data-table="nomina_tipos_liquidacion" ><img src='../../temas/clasico/images/BotonesTabs/Prospecto.png' ></td>
                    </tr>
                    <tr>
                        <td>Tipos de contratos</td>
                        <td id="contractText" data-table="nomina_tipo_contrato">pendiente</td>
                        <td id="contractIcon" data-table="nomina_tipo_contrato" ><img src='../../temas/clasico/images/BotonesTabs/Prospecto.png' ></td>
                    </tr>
                    <tr>
                        <td>Tipos de trabajador</td>
                        <td id="workerTypeText" data-table="nomina_configuracion_tipo_trabajador">pendiente</td>
                        <td id="workerTypeIcon" data-table="nomina_configuracion_tipo_trabajador" ><img src='../../temas/clasico/images/BotonesTabs/Prospecto.png' ></td>
                    </tr>
                    <tr>
                        <td>Subtipos de trabajador</td>
                        <td id="subWorkerTypeText" data-table="nomina_configuracion_subtipo_trabajador">pendiente</td>
                        <td id="subWorkerTypeIcon" data-table="nomina_configuracion_subtipo_trabajador" ><img src='../../temas/clasico/images/BotonesTabs/Prospecto.png' ></td>
                    </tr>
                    <tr>
                        <td>Idiomas</td>
                        <td id="languageText" data-table="nomina_configuracion_idiomas">pendiente</td>
                        <td id="languageIcon" data-table="nomina_configuracion_idiomas" ><img src='../../temas/clasico/images/BotonesTabs/Prospecto.png' ></td>
                    </tr>
                    <tr>
                        <td>Monedas</td>
                        <td id="coinText" data-table="nomina_configuracion_monedas">pendiente</td>
                        <td id="coinIcon" data-table="nomina_configuracion_monedas" ><img src='../../temas/clasico/images/BotonesTabs/Prospecto.png' ></td>
                    </tr>
                    <tr>
                        <td>Estructura Conceptos</td>
                        <td id="concepTypesText" data-table="nomina_electronica_estructura_conceptos">pendiente</td>
                        <td id="concepTypesIcon" data-table="nomina_electronica_estructura_conceptos" ><img src='../../temas/clasico/images/BotonesTabs/Prospecto.png' ></td>
                    </tr>
                    <tr>
                        <td>Formas de pago</td>
                        <td id="payTypeText" data-table="nomina_configuracion_formas_pago">pendiente</td>
                        <td id="payTypeIcon" data-table="nomina_configuracion_formas_pago" ><img src='../../temas/clasico/images/BotonesTabs/Prospecto.png' ></td>
                    </tr>
                    <tr>
                        <td>Metodos de pago</td>
                        <td id="payMethodText" data-table="nomina_configuracion_medios_pago">pendiente</td>
                        <td id="payMethodIcon" data-table="nomina_configuracion_medios_pago" ><img src='../../temas/clasico/images/BotonesTabs/Prospecto.png' ></td>
                    </tr>
                    
                    <tr>
                        <td>&nbsp;</td>
                    </tr>
                    <tr id="content-3-load" style="display:none">
                        <td>Actualizando datos en la base de datos</td>
                        <td id="step3PayrollPeriods"><img src='../../temas/clasico/images/loading.gif' ></td>
                    </tr>
                </table>
            </div>
            <div id="content-6">
                <span>
                    Proceso finalizado, ya puede cerrar esta ventana y continuar con las demas
                    configuracion de nomina electronica
                </span>
            </div>
        </div>
        <div class="WizFooter">
            <!-- <button onclick="processStart()">Anterior</button> -->
            <!-- <button onclick="processStart()">Siguiente</button> -->
        </div>
    </div>
</div>
<script>
    var processStart = async ()=>{
        let response = await fetch('configuracion_nomina_electronica/bd/bd.php?opc=processStart',{
            method: 'POST',
            body: JSON.stringify(data)
        })
        // .then(response => response.text())
        // .then(data => console.log(data));
        response.json();

    }
</script>