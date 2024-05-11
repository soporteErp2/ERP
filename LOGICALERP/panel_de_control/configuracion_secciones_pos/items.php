<?php
	include("../../../configuracion/conectar.php");
	include("../../../configuracion/define_variables.php");
	include("../../../misc/MyGrilla/class.MyGrilla.php");
?>

<div class="w-full h-full bg-white  ">
	<div class="p-2 flex justify-end">
		<input type="text" id="first_name" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-1/4 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Buscar"  />
	</div>
	<div class="w-full h-4/5 p-3 pt-0 overflow-x-hidden overflow-y-auto" id="table-content">
		<table class="w-full text-sm text-left rtl:text-right text-gray-500">
			<thead class="text-xs text-gray-900 uppercase bg-gray-300 sticky top-0">
				<tr>
					<th scope="col" class="px-6 py-3">
						Asignar
					</th>
					<th scope="col" class="px-6 py-3">
						Codigo
					</th>
					<th scope="col" class="px-6 py-3">
						Nombre
					</th>
					<th scope="col" class="px-6 py-3">
						Familia
					</th>
					<th scope="col" class="px-6 py-3">
						Grupo
					</th>
					<th scope="col" class="px-6 py-3">
						Subgrupo
					</th>
				</tr>
			</thead>
			<tbody id="tbody-items">
			</tbody>
		</table>
	</div>

</div>
<script>
	var page      = 1
	  , q         = ""
	  , data_list = []
	  ,	is_fetching = false

	async function get_items(page=1){
		if (is_fetching) return;
    	is_fetching = true;

		let url = `configuracion_secciones_pos/bd/backend.php`
		,	data = 	{
						page,
						option:'get_items'
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
			
			if (data.length > 0) {
				tbody.innerHTML = (data_list.length == 0 || page == 1) ? content : tbody.innerHTML + content;
				data_list=[...data]
			}
			console.log(data_list);
			
		} catch (error) {
			console.log(error)
		}
		is_fetching = false;
	}

	function render_list (data){
		let content = data.map(element=>(
			`<tr class="bg-white odd:bg-white even:bg-slate-50 hover:bg-gray-200 cursor-default">
				<td class="px-6 py-1">
					<input type="checkbox">
				</td>	
				<td class="px-6 py-1">
					${element.codigo}
				</td>
				<th scope="row" class="px-6 py-1 font-medium text-gray-900 whitespace-nowrap dark:text-white">
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

	document.getElementById("table-content").addEventListener("scroll", function (e) {
		function handleScroll(event){
			let table = event.target;
			if (table.scrollHeight - table.scrollTop === table.clientHeight) {
				console.log("scrolling");
				page++;
				get_items(page)
			}
		};
		handleScroll(e)
	});

	get_items(1);

</script>