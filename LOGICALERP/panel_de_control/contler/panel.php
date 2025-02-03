<style>
    #contler-content select{
        appearance: none;
        -webkit-appearance: none;
        -moz-appearance: none;
        /* background: none;
        border: none;
        padding: 0;
        margin: 0;
        font: inherit;
        color: inherit;
        outline: none; */
    }
</style>
<body onload="console.log('El body ha cargado completamente.')">    
    <div id="contler-content" class="!p-4 w-full h-full bg-white flex flex-col items-center gap-3">
        <h2  class="text-lg font-bold">Configuracion CONTLER</h2>
        <section>
            <span class="text-sm">Seleccione donde se sincronizaran los pedidos desde contler al POS</span>
            <fieldset class="py-2 w-64">
                <legend class="text-sm font-semibold">Seccion</legend>
                <div id="section-pulse" class="w-full h-8 border-4 border-gray-500 rounded-lg flex items-center justify-center px-6 animate-pulse">
                    <span class="border-4 border-gray-400 rounded-lg w-full"></span>
                </div>
                <select onchange="get_cash_register(this.value);get_table(this.value)" id="section" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg h-12 hidden">
                    <option value="">Seleccione...</option>
                </select>
            </fieldset>
            <fieldset class="py-2 w-64">
                <legend class="text-sm font-semibold">Caja</legend>
                <div id="cash_register-pulse" class="hidden w-full h-8 border-4 border-gray-500 rounded-lg flex items-center justify-center px-6 animate-pulse ">
                    <span class="border-4 border-gray-400 rounded-lg w-full"></span>
                </div>
                <select name="" id="cash_register" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg h-12">
                    <option value="">Seleccione...</option>
                </select>
            </fieldset>
            <fieldset class="py-2 w-64">
                <legend class="text-sm font-semibold">Mesa</legend>
                <div id="table-pulse" class="hidden w-full h-8 border-4 border-gray-500 rounded-lg flex items-center justify-center px-6 animate-pulse ">
                    <span class="border-4 border-gray-400 rounded-lg w-full"></span>
                </div>
                <select name="" id="table" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg h-12">
                    <option value="">Seleccione...</option>
                </select>
            </fieldset>
        </section>
        <section>
            <button type="button" onclick="save_config()" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">Aceptar</button>
            <button type="button" onclick="btn_close()" class="text-white bg-gray-800 hover:bg-gray-900 focus:outline-none focus:ring-4 focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-gray-800 dark:hover:bg-gray-700 dark:focus:ring-gray-700 dark:border-gray-700">Cancelar</button>
        </section>
    </div>
</body>
<script>
    function btn_close(){
        Win_Panel_Global.close();
    }

    async function get_configuration(){
        try {
            const response = await fetch("contler/bd/Controller.php?opt=get_configuration");
            if (!response.ok) {
                throw new Error("Error al obtener los datos");
            }
            let data = await response.json();
            let form_data = JSON.parse(data)

            console.log(form_data)

            if (Object.keys(form_data).length > 0) {
                let section = document.getElementById("section")
                section.value = form_data.section

                await get_cash_register(form_data.section)
                let cash_register = document.getElementById("cash_register")
                cash_register.value = form_data.cash_register
                await get_table(form_data.section)
                let table = document.getElementById("table")
                table.value = form_data.table
            } 



        } catch (error) {
            console.error("Error:", error);
        } 
    }

    async function get_sections(){
        try {
            const response = await fetch("contler/bd/Controller.php?opt=get_sections");
            if (!response.ok) {
                throw new Error("Error al obtener los datos");
            }
            const data = await response.json();
            let section = document.getElementById("section")

            section.innerHTML = "<option value=''>Seleccione...</option>";

            data.forEach(item => {
                section.innerHTML += `<option value="${item.id}">${item.nombre}</option>`;
            });

            document.getElementById("section-pulse").classList.toggle("hidden")
            section.classList.toggle("hidden")

            await get_configuration();
        } catch (error) {
            console.error("Error:", error);
        }
    }

    async function get_cash_register(section_id){
        if (!section_id) {
            return;
        }

        try {
            let cash_register = document.getElementById("cash_register")
            let pulse = document.getElementById('cash_register-pulse')

            cash_register.classList.toggle('hidden')
            pulse.classList.toggle('hidden')

            const response = await fetch(`contler/bd/Controller.php?opt=get_cash_register&section_id=${section_id}`);
            if (!response.ok) {
                throw new Error("Error al obtener los datos");
            }
            const data = await response.json();
            

            cash_register.innerHTML = "<option value=''>Seleccione...</option>";

            data.forEach(item => {
                cash_register.innerHTML += `<option value="${item.id}">${item.nombre}</option>`;
            });

            pulse.classList.toggle("hidden")
            cash_register.classList.toggle("hidden")
        } catch (error) {
            console.error("Error:", error);
        }
    }

    async function get_table(section_id){
        if (!section_id) {
            return;
        }

        try {
            let table = document.getElementById("table")
            let pulse = document.getElementById('table-pulse')

            table.classList.toggle('hidden')
            pulse.classList.toggle('hidden')

            const response = await fetch(`contler/bd/Controller.php?opt=get_table&section_id=${section_id}`);
            if (!response.ok) {
                throw new Error("Error al obtener los datos");
            }
            const data = await response.json();
            

            table.innerHTML = "<option value=''>Seleccione...</option>";

            data.forEach(item => {
                table.innerHTML += `<option value="${item.id}">${item.nombre}</option>`;
            });

            pulse.classList.toggle("hidden")
            table.classList.toggle("hidden")
        } catch (error) {
            console.error("Error:", error);
        }
    }

    get_sections();

    async function save_config(){
        let section       = document.getElementById("section").value
          , cash_register = document.getElementById("cash_register").value
          , table         = document.getElementById("table").value
        
        let form_data = {
          section,
          cash_register,
          table
        }
          
        try {
            let data = {
                opt: 'save',  // Acción que se enviará al servidor
                form_data
            };

            const response = await fetch("contler/bd/Controller.php?opt=save", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",  // Tipo de contenido para enviar JSON
                },
                body: JSON.stringify(data)  // Convertir el objeto a JSON
            });

            if (!response.ok) {
                throw new Error("Error al guardar la configuración");
            }

            const result = await response.json();
            console.log(result);  // Mostrar la respuesta del servidor

            alert("Configuración guardada con éxito");
            Win_Panel_Global.close();

        } catch (error) {
            console.error("Error:", error);
            alert("Hubo un error al guardar la configuración");
        }
        
    }

</script>