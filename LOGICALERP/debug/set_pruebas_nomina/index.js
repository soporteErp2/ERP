
let payRoll = async () => {
	let nit                 = document.getElementById('nit').value
	,	digito_verificacion = document.getElementById('digito_verificacion').value
	,	client_token        = document.getElementById('client_token').value
	,	access_token        = document.getElementById('access_token').value
	
	let data = Object.assign(json);

	data["Credencial"]["ClientToken"]      = client_token;
	data["Credencial"]["AccessToken"]      = access_token;
	data["Empleador"]["Identificacion"]    = nit;
	data["Empleador"]["DigitoVerificador"] = digito_verificacion;

	let list = document.getElementById('accordionPayRoll');
	let consecutivo = parseInt(document.getElementById('consecutivo').value);
	
	list.innerHTML = '';
	for (let i = consecutivo; i <= (consecutivo+20); i++) {
		data["Documento"]["Consecutivo"] = i;
		let response = await api(data);
		console.log(response)
   	   	list.innerHTML = `${list.innerHTML} 
	   	   	<div class="accordion-item">
	          <h2 class="accordion-header" id="heading${i}">
	            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapse${i}" aria-expanded="false" aria-controls="collapse${i}">
	              Planilla #${i}
	            </button>
	          </h2>
	          <div id="collapse${i}" class="accordion-collapse collapse show" aria-labelledby="heading${i}" data-bs-parent="#accordionPayRoll">
	            <div class="accordion-body">
	              detalle: ${response.respuesta}
	            </div>
	          </div>
	        </div>`;
	}
}

let payRollAdjustmentType1 = async ()=>{
	let nit                 = document.getElementById('nit').value
	,	digito_verificacion = document.getElementById('digito_verificacion').value
	,	client_token        = document.getElementById('client_token').value
	,	access_token        = document.getElementById('access_token').value
	,	list                = document.getElementById('accordionPayRollAdjustmentType1')
	,	consecutivoAjuste   = parseInt(document.getElementById('consecutivo_ajuste_1').value)
	,	consecutivoAjustado = parseInt(document.getElementById('consecutivo_planilla_ajustar1').value)
	
	let data = Object.assign(json);

	data["Credencial"]["ClientToken"]      = client_token;
	data["Credencial"]["AccessToken"]      = access_token;
	data["Empleador"]["Identificacion"]    = nit;
	data["Empleador"]["DigitoVerificador"] = digito_verificacion;

	data["Documento"]["TipoDocumento"]    = 103;
	data["Documento"]["Prefijo"]          = "NEAJ";
	data["Documento"]["Consecutivo"] = consecutivoAjuste;
	data["Documento"]["TipoNotaAjuste"]   = 1
	data["Documento"]["NumeroNotaAjuste"] = `NETX${consecutivoAjustado}`

	
	list.innerHTML = '';
	let response = await api(data);

   	list.innerHTML = `${list.innerHTML} 
   	   	<div class="accordion-item">
          <h2 class="accordion-header" id="heading${consecutivoAjuste}">
            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapse${consecutivoAjuste}" aria-expanded="false" aria-controls="collapse${consecutivoAjuste}">
              Planilla #${consecutivoAjuste}
            </button>
          </h2>
          <div id="collapse${consecutivoAjuste}" class="accordion-collapse collapse show" aria-labelledby="heading${consecutivoAjuste}" data-bs-parent="#accordionPayRoll">
            <div class="accordion-body">
              detalle: ${response.respuesta}
            </div>
          </div>
        </div>`;
}

let payRollAdjustmentType2 = async ()=>{
	let nit                 = document.getElementById('nit').value
	,	digito_verificacion = document.getElementById('digito_verificacion').value
	,	client_token        = document.getElementById('client_token').value
	,	access_token        = document.getElementById('access_token').value
	,	list                = document.getElementById('accordionPayRollAdjustmentType2')
	,	consecutivoAjuste   = parseInt(document.getElementById('consecutivo_ajuste_2').value)
	,	consecutivoAjustado = parseInt(document.getElementById('consecutivo_planilla_ajustar2').value)
	
	let data = json;

	data["Credencial"]["ClientToken"]      = client_token;
	data["Credencial"]["AccessToken"]      = access_token;
	data["Empleador"]["Identificacion"]    = nit;
	data["Empleador"]["DigitoVerificador"] = digito_verificacion;

	data["Documento"]["TipoDocumento"]    = 103;
	data["Documento"]["Prefijo"]          = "NEAJ";
	data["Documento"]["Consecutivo"] = consecutivoAjuste;
	data["Documento"]["TipoNotaAjuste"]   = 2
	data["Documento"]["NumeroNotaAjuste"] = `NETX${consecutivoAjustado}`

	
	list.innerHTML = '';
	let response = await api(data);

   	list.innerHTML = `${list.innerHTML} 
   	   	<div class="accordion-item">
          <h2 class="accordion-header" id="heading${consecutivoAjuste}">
            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapse${consecutivoAjuste}" aria-expanded="false" aria-controls="collapse${consecutivoAjuste}">
              Planilla #${consecutivoAjuste}
            </button>
          </h2>
          <div id="collapse${consecutivoAjuste}" class="accordion-collapse collapse show" aria-labelledby="heading${consecutivoAjuste}" data-bs-parent="#accordionPayRoll">
            <div class="accordion-body">
              detalle: ${response.respuesta}
            </div>
          </div>
        </div>`;
}

let api = async (data) => {
	let url = "https://logicalsoft-erp.com/LOGICALERP/proxy.php"; // Cambia esta URL a donde hayas colocado tu proxy
	let response = await fetch(url, {
		method: "POST",
		headers: {
	      'Content-Type': 'application/json',
	    },
		body: JSON.stringify(data)
	});
	return response.json();
}