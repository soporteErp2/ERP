/**
 * Class electronicPayroll
 * Clase para configurar la nomina electronica
 */
var electronicPayroll = class electronicPayroll{

	urlEndPoint = 'wizard_nomina_electronica/bd/bd.php';
	constructor(){
		this.icons = new Array();
		this.icons.loading = "<img src='../../temas/clasico/images/loading.gif' >";
		this.icons.checked = "<img src='../../temas/clasico/images/BotonesTabs/saved.png' >";
		this.icons.alert = "<img src='../../temas/clasico/images/BotonesTabs/alert.png' >";

		this.getConsecutives();
		this.getWizardProcess();
		// this.urlEndPoint = 'wizard_nomina_electronica/bd/bd.php';
	}

	/**
	 * consultar los consecutivos de la resolucion si ya fueron creados
	 */
	getConsecutives = async ()=>{
		let response = await this.getData(`${this.urlEndPoint}?opc=getConsecutives`);
		if (response.status != 'error'){
			document.getElementById("nomina_individual_prefijo").value       = response["NominaIndividual"]["prefijo"];
			document.getElementById("nomina_individual_consecutivo").value   = response["NominaIndividual"]["consecutivo"];
			document.getElementById("ajuste_modificacion_prefijo").value     = response["NominaIndividualDeAjuste"]["prefijo"];
			document.getElementById("ajuste_modificacion_consecutivo").value = response["NominaIndividualDeAjuste"]["consecutivo"];
			document.getElementById("content-2-btn-next").style.display      = "block";
		}
	}

	/**
	 * consultar los registros de las tablas que ya se insertaron
	 */
	getWizardProcess = async ()=>{
		let response = await this.getData(`${this.urlEndPoint}?opc=getWizardProcess`);
		if (response.status != 'error'){
			let icons = this.icons
			response.map(element=>{
				// console.table(element)
				let domElement = [...document.querySelectorAll(`[data-table="${element.table}"]`)]
				domElement[0].innerHTML = "Insertada"
				domElement[1].innerHTML = icons.checked;
			})

			document.getElementById("content-5-btn-next").style.display      = "block";
		}
	}

	/**
	 *  validateTable
	 *  validar que una tabla especifica exista
	 */
	validateTable = async (tableName)=>{
		let response = await fetch(`${this.urlEndPoint}?opc=tableValidate&tableName=${tableName}`)
        return response.json();
	}

	/**
	 * createTable
	 * crear una tabla en la bd que no existe
	 */
	// createTable = async (index)=>{
	// 	let response = await fetch(`${this.urlEndPoint}?opc=createTable&index=${index}`)
 //        return response.json();
	// }

	getRequest = async(index,opc)=>{
		let response = await fetch(`${this.urlEndPoint}?opc=${opc}&index=${index}`)
        return response.json();
	}

	/**
	 * updateTable
	 * Actualizar las colunmas de tablas existentes
	 */
	updateTable = async (index)=>{
		let response = await fetch(`${this.urlEndPoint}?opc=updateTable&index=${index}`)
        return response.json();
	}

	/**
	 * enviar peticiones post al server
	 * @param  {string} url url del endpoint
	 * @param  {obj} data objeto con los parametros a enviar via post
	 * @return {json}      respuesta de la peticion
	 */
	postData = async (url,data) =>{
		let response = await fetch(url,
		{
			method: 'POST',
			headers: {
		      'Content-Type': 'application/json'
		    },
			body: JSON.stringify(data)
		})
        return response.json();
	}

	/**
	 * consultar peticiones get al servidor
	 * @return {json} respuesta de la peticion
	 */
	getData = async (url)=>{
		let response = await fetch(url)
        return response.json();
	}



	/**
	 * validar las tablas en la db y crearlas 
	 * @return {json} respuesta de la peticion
	 */
	tablesConfiguration = async ()=> {
		// saltar el primer contenedor para iniciar el proceso
		document.getElementById("content-1").classList.add("forward");
		// esperar un segundo a la animacion del conenedor para iniciar el asistente
		await new Promise((resolve,reject)=>setTimeout(()=>resolve(),1500) )

		// validar tablas en la bd
		await this.processTable('nomina_configuracion_consecutivos','consecutivesText','consecutivesIcon')
		await this.processTable('nomina_configuracion_tipo_documentos','typeDocText','typeDocIcon')		
		await this.processTable('nomina_configuracion_tipo_documentos_ajuste','typeDocEditText','typeDocEditIcon')		
		// await this.processTable('nomina_tipo_contrato','contractTypeText','contractTypeIcon')
		await this.processTable('nomina_configuracion_tipo_trabajador','employeeTypeText','employeeTypeIcon')
		await this.processTable('nomina_configuracion_subtipo_trabajador','subEmployeeTypeText','subEmployeeTypeIcon')
		await this.processTable('nomina_configuracion_formas_pago','wayToPayTypeText','wayToPayTypeIcon')
		await this.processTable('nomina_configuracion_medios_pago','payMethodTypeText','payMethodTypeIcon')
		await this.processTable('nomina_configuracion_hora_extra_recargo','overTimeTypeText','overTimeTypeIcon')
		await this.processTable('nomina_configuracion_monedas','coinTypeText','coinTypeIcon')
		await this.processTable('nomina_configuracion_idiomas','languageTypeText','languageTypeIcon')
		await this.processTable('nomina_wizard_process','configuratioStatusText','configuratioStatusIcon')
		await this.processTable('nomina_planillas_electronica','documentStatusText','documentStatusIcon')
		await this.processTable('nomina_planillas_electronica_empleados','documentEmployesStatusText','documentEmployesStatusIcon')
		await this.processTable('nomina_planillas_electronica_empleados_conceptos','documentEmployesConceptsText','documentEmployesConceptsIcon')
		await this.processTable('nomina_electronica_estructura_conceptos','detailConfigText','detailConfigIcon')
		await this.processTable('nomina_planillas_empleados_conceptos_datos_nomina_electronica','configText','configIcon')
		await this.processTable('nomina_planillas_electronica_empleados_fechas_pago','payDateText','payDateIcon')
		
		await new Promise((resolve,reject)=>setTimeout(()=>resolve(),1500) )

		// saltar al siguiente contenedor la validacion de la estructura de las tablas main-text-content-2
		document.getElementById("content-2").classList.add("forward");
		// esperar un segundo a la animacion del conenedor para iniciar el asistente
		await this.tablesStructure();
	}

	/**
	 * validar la estructura de las tablas en la db y crea las columnas que no existen
	 * @return {json} respuesta de la peticion
	 */
	tablesStructure = async () =>{
		console.info("updating database structure");

		await new Promise((resolve,reject)=>setTimeout(()=>resolve(),1500) )

		await this.updateStructure('nomina_tipos_liquidacion','payrollText','payrollIcon','updateStructure')
		await this.updateStructure('empleados_contratos','employeeContractText','employeeContractIcon','updateStructure')
		await this.updateStructure('empresas','companyText','companyIcon','updateStructure')
		await this.updateStructure('nomina_conceptos','payRollConceptsText','payRollConceptsIcon','updateStructure')
		await this.updateStructure('nomina_tipo_contrato','employeeContractText','employeeContractIcon','updateStructure')

		await new Promise((resolve,reject)=>setTimeout(()=>resolve(),1500) )
		document.getElementById("content-3").classList.add("forward");

	}

	/**
	 * consecutivesConfiguration validar la la tabla en bd de los consecutivos
	 * @return {json} respuesta de la peticion
	 */
	processTable = async (table,idText,idIcon) =>{
		
		document.getElementById(idText).innerHTML = 'validando'; 
		document.getElementById(idIcon).innerHTML = this.icons.loading; 


		let tableProccess = await this.validateTable(table);


		// si no existe la tabla se crea
		if (tableProccess.status == 'error'){
			document.getElementById(idText).innerHTML = 'Creando'; 

			let tableProccess = await this.getRequest(table,'createTable');

			if (tableProccess.status == 'error'){
				alert(`Error: \n ${tableProccess.message}`);
				document.getElementById(idText).innerHTML = 'error'; 
				document.getElementById(idIcon).innerHTML = this.icons.alert; 	
			}
			else{
				document.getElementById(idText).innerHTML = 'creada'; 
				document.getElementById(idIcon).innerHTML = this.icons.checked; 	
			}
		}
		else{
			document.getElementById(idText).innerHTML = 'verificada'; 			
			document.getElementById(idIcon).innerHTML = this.icons.checked;
		}

	}

	/**
	 * updateStructure actualizar la estructura de una tabla en bd agregando columnas
	 * @return {json} respuesta de la peticion
	 */
	updateStructure = async(table,idText,idIcon)=>{
		console.table({table,idText,idIcon})
		document.getElementById(idText).innerHTML = 'validando'; 
		document.getElementById(idIcon).innerHTML = this.icons.loading; 

		let tableProccess = await this.updateTable(table);
		if (tableProccess.status == 'error'){
			alert(`Error: \n ${tableProccess.message}`);
			document.getElementById(idText).innerHTML = 'error'; 
			document.getElementById(idIcon).innerHTML = this.icons.alert; 	
		}
		else{
			document.getElementById(idText).innerHTML = 'Actualizada'; 
			document.getElementById(idIcon).innerHTML = this.icons.checked; 	
		}

	}

	/**
	 * insertar la informacion necesaria para el envio de documentos a la dian
	 */
	insertDataBaseData = async()=>{

		await this.queryTable('nomina_configuracion_tipo_documentos','documentTypeText','documentTypeIcon')
		await this.queryTable('nomina_configuracion_tipo_documentos_ajuste','documentPatchText','documentPatchIcon')
		await this.queryTable('nomina_tipos_liquidacion','liquidationTypeText','liquidationTypeIcon')
		await this.queryTable('nomina_tipo_contrato','contractText','contractIcon')
		await this.queryTable('nomina_configuracion_tipo_trabajador','workerTypeText','workerTypeIcon')
		await this.queryTable('nomina_configuracion_subtipo_trabajador','subWorkerTypeText','subWorkerTypeIcon')
		await this.queryTable('nomina_configuracion_idiomas','languageText','languageIcon')
		await this.queryTable('nomina_configuracion_monedas','coinText','coinIcon')
		await this.queryTable('nomina_electronica_estructura_conceptos','concepTypesText','concepTypesIcon')
		await this.queryTable('nomina_configuracion_formas_pago','payTypeText','payTypeIcon')
		await this.queryTable('nomina_configuracion_medios_pago','payMethodText','payMethodIcon')
	}

	queryTable = async(table,idText,idIcon)=>{
		document.getElementById(idText).innerHTML = 'Insertando'; 
		document.getElementById(idIcon).innerHTML = this.icons.loading; 

		let tableProccess = await this.getRequest(table,'insertDataTable');

		if (tableProccess.status == 'error'){
			alert(`Error: \n ${tableProccess.message}`);
			document.getElementById(idText).innerHTML = 'error'; 
			document.getElementById(idIcon).innerHTML = this.icons.alert; 	
		}
		else{
			document.getElementById(idText).innerHTML = 'finalizado'; 
			document.getElementById(idIcon).innerHTML = this.icons.checked; 	
		}
	}


	/**
	 * saveConsecutives guardar la configuracion de consecutivos de nominas electronicas
	 * @return {json} respuesta de la peticion
	 */
	saveConsecutives = async (btn)=>{
		
		let error   = false;
		let message = '';
		let data    = {};

		data.nomina_individual_prefijo       = document.getElementById("nomina_individual_prefijo").value
		data.nomina_individual_consecutivo   = document.getElementById("nomina_individual_consecutivo").value
		data.ajuste_modificacion_prefijo     = document.getElementById("ajuste_modificacion_prefijo").value
		data.ajuste_modificacion_consecutivo = document.getElementById("ajuste_modificacion_consecutivo").value

		if (
			data.nomina_individual_prefijo       == '' ||
			data.nomina_individual_consecutivo   == '' ||
			data.ajuste_modificacion_prefijo     == '' ||
			data.ajuste_modificacion_consecutivo == '' 
			) { 
				alert("todos los campos son obligatorios"); 
				btn.disabled=false;
				return;
			}
		let response = await this.postData(`${this.urlEndPoint}?opc=saveConsecutives`,data);

		console.log(response)

		response.map(element => {if(element.status=='error') {message += `${element.message} \n`; error=true;}})

		if (error){
			alert(`Se presentaron los siguientes errores:\n${message}`);
			btn.disabled=false;
		}
		else{			
			document.getElementById("content-4").classList.add("forward");
		}
	}

	/**
	 * configurar los periodos de nomina que exige la dian
	 */
	payRollPeriods = async(btn)=>{
		let error =false;
		document.getElementById("content-3-load").style.display = "block";
		let response = await this.getData(`${this.urlEndPoint}?opc=payRollPeriods`);
		document.getElementById("content-3-load").style.display = "none";

		response.map(element => {if(element.status=='error') {message += `${element.message} \n`; error=true;}})

		if (error){
			alert(`Se presentaron los siguientes errores:\n${message}`);
			btn.disabled=false;
		}
		else{			
			document.getElementById("content-3").classList.add("forward");
		}
		
	}


}

var payRollObj = new electronicPayroll();

