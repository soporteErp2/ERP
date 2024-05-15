<?php
	include("../../../configuracion/conectar.php");
	include("../../../configuracion/define_variables.php");
	include("../../../misc/MyGrilla/class.MyGrilla.php");
?>

<div class="w-full h-full bg-white  ">
	<div class="p-2 flex justify-end ">
		<!-- <label for="search" class="mb-2 text-sm font-medium text-gray-900 sr-only">Buscar</label> -->
		<div class="relative w-96">
			<div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
				<svg class="w-4 h-4 text-gray-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
					<path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
				</svg>
			</div>
			<input type="search" id="search_input" class="block w-full p-4 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 " placeholder="codigo, nombre, etc..." required />
			<button type="button" id="search_button" class="text-white absolute end-2.5 bottom-2.5 bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-4 py-2 ">Buscar</button>
		</div>
		<!-- <input type="text" id="search_input" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-1/4 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Buscar"  /> -->
	</div>
	<div class="w-full h-4/5 p-3 pt-0 overflow-x-hidden overflow-y-auto" id="table-content">
		<table class="w-full text-sm text-left rtl:text-right text-gray-500 table-auto">
			<thead class="text-xs text-gray-900 uppercase bg-gray-300 sticky top-0 cursor-pointer">
				<tr>
					<th scope="col" class="px-6 py-3 text-center">
						<span>Asignar</span>	
					</th>
					<th scope="col" class="px-6 py-3">
						<span>Codigo</span>	
					</th>
					<th scope="col" class="px-6 py-3 flex justify-between items-center">
						<span>Nombre</span>
						<!-- <svg class="w-3 h-3 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 14">
							<path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13V1m0 0L1 5m4-4 4 4"></path>
						</svg>
						<svg class="w-3 h-3 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 14">
							<path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 1v12m0 0 4-4m-4 4L1 9"></path>
						</svg> -->
					</th>
					<th scope="col" class="px-6 py-3">
						<span>Familia</span>
					</th>
					<th scope="col" class="px-6 py-3">
						<span>Grupo</span>
					</th>
					<th scope="col" class="px-6 py-3">
						<span>Subgrupo</span>
					</th>
				</tr>
			</thead>
			<tbody id="tbody-items">
				<!-- <tr class="" >
					<td colspan="6">
						<div class='flex items-center justify-center'>
							<div class="px-2" >cargando</div>  
							<div class="animate-bounce w-6 text-5xl">.</div>
							<div class="animate-bounce w-6 text-5xl" style="animation-delay: 0.2s">.</div>
							<div class="animate-bounce w-6 text-5xl"  style="animation-delay: 0.4s">.</div>
						</div>
					</td>
				</tr> -->
			</tbody>
		</table>
	</div>

</div>
<script>
	var page      = 1
	  , q         = ""
	  ,	order_by  = ""
	  , data_list = []
	  ,	is_fetching = false
	  ,	end_fetching = false

	// consultar los items
	async function get_items(page=1){
		if (is_fetching) return;
    	is_fetching = true;
		loading_records(true)

		let url = `configuracion_secciones_pos/bd/backend.php`
		,	data = 	{
						page,
						q,
						option:'get_items',
						id_seccion: '<?= $id_seccion ?>',
						id_empresa : '<?= $_SESSION["EMPRESA"] ?>'
					}

		let requestOptions = {
			method: 'POST',
			headers: {
			'Content-Type': 'application/json'
			},
			body: JSON.stringify(data)
		};

		try {
			let response = await fetch(url, requestOptions);
			let data = await response.json()
			let content = render_list(data)
			let tbody = document.getElementById("tbody-items")
			// console.log(data.length)
			if (data.length > 0) {
				tbody.innerHTML = (data_list.length == 0 || page == 1) ? content : tbody.innerHTML + content;
				data_list=[...data]
				loading_records(false)
				end_fetching = false;
			}
			else{
				end_fetching = true
			}
			// console.log(data_list);
			
		} catch (error) {
			console.log(error)
		}

		loading_records(false)
		is_fetching = false;
		
	}

	// mostrar loading de los datos
	function loading_records(state){
		// console.log(state,end_fetching,q)
		if (state && !end_fetching) {
			let tbody = document.getElementById("tbody-items")
			let new_tr = document.createElement("tr")
			new_tr.setAttribute("id","loading-tr");
			new_tr.innerHTML = `<td colspan="6">
									<div class='flex items-center justify-center'>
										<div class="px-2" >cargando</div>  
										<div class="animate-bounce w-6 text-5xl">.</div>
										<div class="animate-bounce w-6 text-5xl" style="animation-delay: 0.2s">.</div>
										<div class="animate-bounce w-6 text-5xl"  style="animation-delay: 0.4s">.</div>
									</div>
								</td>`
			tbody.appendChild(new_tr);

			if (q=="") {
				let content = document.getElementById("table-content")
				content.scrollTop = content.scrollHeight;
			}
		}
		else{
			try {
				document.getElementById("loading-tr").remove()
			} catch (error) {
				// console.warn(error)				
			}
		}

	}

	// renderizar la lista de la tabla
	function render_list (data){
		let content = data.map(element=>(
			`<tr class="bg-white odd:bg-white even:bg-slate-50 hover:bg-gray-200 cursor-default">
				<td class="px-6 py-1 text-center">
					<input type="checkbox" ${(element.id_seccion)? "checked" : "" } onclick="check_uncheck(${element.id})" id="check_${element.id}" class="cursor-pointer h-4 w-4">
					<div role="status" class="hidden" id="load_check_${element.id}">
						<svg aria-hidden="true" class="inline w-4 h-4 text-gray-200 animate-spin dark:text-gray-600 fill-gray-600 dark:fill-gray-300" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
							<path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="currentColor"/>
							<path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="currentFill"/>
						</svg>
						<span class="sr-only">Loading...</span>
					</div>
				</td>	
				<td class="px-6 py-1">
					${element.codigo}
				</td>
				<th scope="row" class="px-6 py-1 font-medium whitespace-nowrap">
					${element.nombre}
				</th>
				<td class="px-6 py-1">
					${element.familia}
				</td>
				<td class="px-6 py-1">
					${element.grupo}
				</td>
				<td class="px-6 py-1">
					${element.subgrupo}
				</td>
			</tr>`
		)).join('')
		return content;
	}

	get_items(1);

	// eventos
	document.getElementById("table-content").addEventListener("scroll", function (e) {
		function handleScroll(event){
			let table = event.target;
			if (table.scrollHeight - table.scrollTop === table.clientHeight) {
				page++;
				get_items(page)
			}
		};
		handleScroll(e)
	});

	
	document.getElementById("search_input").addEventListener('keyup', function(event) {
		q = event.target.value;
		data_list = [];
		
		// Verificar si se presionó la tecla "Enter" o el campo de búsqueda está vacío
		if (event.keyCode === 13 || q === "") {
			document.getElementById("tbody-items").innerHTML = "";
			// Realizar la consulta si se presionó "Enter" o el campo está vacío y no hay una solicitud en curso
			if (!is_fetching) {
				get_items(1);
			}
		} 
	});

	document.getElementById("search_button").addEventListener('click', function(event) {
		q = document.getElementById("search_input").value;
		data_list = [];
			// Realizar la consulta si se presionó "Enter" o el campo está vacío y no hay una solicitud en curso
		if (!is_fetching) {
			document.getElementById("tbody-items").innerHTML = "";
			get_items(1);
		}
	});

	async function check_uncheck(id_item){
		let check = document.getElementById(`check_${id_item}`)
		,	load = document.getElementById(`load_check_${id_item}`)

		check.classList.add("hidden");
		load.classList.remove("hidden");

		let url = `configuracion_secciones_pos/bd/backend.php`
		,	data = 	{
						option:'set_item',
						id_item,
						set : check.checked,
						id_seccion: '<?= $id_seccion ?>',
						id_empresa : '<?= $_SESSION["EMPRESA"] ?>'
					}

		let requestOptions = {
			method: 'POST',
			headers: {
			'Content-Type': 'application/json'
			},
			body: JSON.stringify(data)
		};

		try {
			let response = await fetch(url, requestOptions);
			let data = await response.json()
			if (!data.success) {
				alert("Se presento un error, intentelo de nuevo, si persiste contacte a soporte");
				check.checked= !check.checked				
			}
		} catch (error) {
			alert("Se presento un error, intentelo de nuevo, si persiste contacte a soporte");
			console.log(error)
			check.checked= !check.checked				
		}

		check.classList.remove("hidden");
		load.classList.add("hidden");
	}

</script>