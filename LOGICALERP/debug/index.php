<!DOCTYPE html>
<html lang="es">
<head>
	<title>Sistema de depuracion Logicalsoft-erp</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" crossorigin="anonymous">
	<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js" integrity="sha384-q2kxQ16AaE6UbzuKqyBE9/u/KzioAlnx2maXQHiDX9d4/zp8Ok3f+M7DPm+Ib6IU" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.min.js" integrity="sha384-pQQkAEnwaBkjpqZ8RU1fF1AKtTcHJwFl3pblpTlHXybJjHpMYo79HY3hIi4NKxyj" crossorigin="anonymous"></script>
</head>
<body>
	<div class="container-fluid mt-3">
	<h5>Sistema depurador logicalsoft-erp</h5>		
		<div class="row">
		  <div class="col-4">
		    <div class="list-group" id="list-tab" role="tablist">
		      <a class="list-group-item list-group-item-action active" id="list-home-list" data-bs-toggle="list" href="#list-end-year-payroll-report" role="tab" aria-controls="home">Informe de fin de año</a>
		      <a class="list-group-item list-group-item-action" id="list-profile-list" data-bs-toggle="list" href="#list-profile" role="tab" aria-controls="profile">comming soon...</a>
		      <a class="list-group-item list-group-item-action" id="list-messages-list" data-bs-toggle="list" href="#list-messages" role="tab" aria-controls="messages">comming soon...</a>
		      <a class="list-group-item list-group-item-action" id="list-settings-list" data-bs-toggle="list" href="#list-settings" role="tab" aria-controls="settings">comming soon...</a>
		    </div>
		  </div>
		  <div class="col-8">
		    <div class="tab-content" id="nav-tabContent">
		      <div class="tab-pane fade show active" id="list-end-year-payroll-report" role="tabpanel" aria-labelledby="list-home-list">
		      	<form class="row g-3">				  
				  <div class="col-md-12">
				    <label for="empresa_id" class="form-label">Empresa</label>
				    <select id="empresa_id" class="form-select">
				      <option selected>seleccione...</option>
				      <option value="1">Plataforma Colombia</option>
				      <option value="47">Plataforma Comunicaciones</option>
				    </select>
				  </div>
				  <div class="col-md-6">
				    <label for="start" class="form-label">Fecha Inicio</label>
				    <input type="date" class="form-control" id="start">
				  </div>
				  <div class="col-md-6">
				    <label for="end" class="form-label">Fecha Final</label>
				    <input type="date" class="form-control" id="end">
				  </div>
				  <div class="col-12">
				    <button type="button" onclick="loadFunction({endPoint:'informe_fin_anio_liquidacion.php',inputs : ['empresa_id','start','end'] })" class="btn btn-primary">Generar</button>
				  </div>
				</form>
		      </div>
		      <div class="tab-pane fade" id="list-profile" role="tabpanel" aria-labelledby="list-profile-list">...</div>
		      <div class="tab-pane fade" id="list-messages" role="tabpanel" aria-labelledby="list-messages-list">...</div>
		      <div class="tab-pane fade" id="list-settings" role="tabpanel" aria-labelledby="list-settings-list">...</div>
		    </div>
		  </div>
		</div>
	</div>
</body>
</html>
<script>
	const loadFunction = params =>{
		let urlParams = ''
		params.inputs.map((element)=>{
			urlParams += `${element}=${document.getElementById(element).value}&`;
		})
		// console.log(`${window.location.origin}/LOGICALERP/debug/${params.endPoint}?${urlParams}`); return;
		window.open(`${window.location.origin}/LOGICALERP/debug/${params.endPoint}?${urlParams}`)
	}
</script>