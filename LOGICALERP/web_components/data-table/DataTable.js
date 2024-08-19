class DataTable extends HTMLElement {
  constructor() {
    super();
    this.attachShadow({ mode: 'open' });

    // Variables del estado
    this.page = 1;
    this.q = "";
    this.order_by = "";
    this.data_list = [];
    this.is_fetching = false;
    this.end_fetching = false;
    console.log('in')

    // Insertar template en el Shadow DOM
    // const templateContent = this.template().content.cloneNode(true);
    // this.shadowRoot.appendChild(templateContent);

    // // Agregar listeners a los elementos
    // this.addListeners();
    
    // // Cargar los ítems iniciales
    // this.get_items(1);
  }

  async connectedCallback() {
    console.log('connectedCallback')

    const templateUrl = '../web_components/data-table/template.html'; // Ruta del archivo HTML que contiene el template
    const templateHtml = await this.loadTemplate(templateUrl);
    this.shadowRoot.innerHTML = ''; // Limpia el Shadow DOM

    const templateElement = this.extractTemplate(templateHtml);
    this.shadowRoot.appendChild(templateElement);
    console.log(templateElement)

    // this.addListeners();
    // this.get_items(1);
  }

  // Método para cargar el template HTML
  async loadTemplate(url) {
    console.log('loadTemplate')
    try {
      const response = await fetch(url);
      if (!response.ok) {
        throw new Error(`Error al cargar el template: ${response.statusText}`);
      }
      return await response.text();
    } catch (error) {
      console.error('Error:', error);
      return `<div>Error al cargar el template</div>`;
    }
  }

  extractTemplate(htmlString) {
    const parser = new DOMParser();
    const doc = parser.parseFromString(htmlString, 'text/html');
    const template = doc.querySelector('template#data-table');
    if (template) {
      return template.content.cloneNode(true);
    } else {
      console.error('Template no encontrado en el HTML cargado');
      return document.createElement('div');
    }
  }

  template() {
    const template = document.createElement('template');
    template.innerHTML = `
      <style>
        /* Aquí van los estilos, incluyendo Tailwind CSS */
        .bg-blue-100 { background-color: #ebf8ff; }
        .rounded { border-radius: 0.375rem; }
        .p-4 { padding: 1rem; }
        .text-blue-700 { color: #2c5282; }
        .btn { background-color: #2c5282; color: white; padding: 0.5rem; cursor: pointer; border: none; border-radius: 0.375rem; }
      </style>
      <div id="table-content" class="p-4 bg-blue-100 rounded">
        <input type="text" id="search_input" placeholder="Buscar..." class="p-2 rounded border">
        <button id="search_button" class="btn">Buscar</button>
        <table>
          <thead>
            <!-- Aquí pueden ir las cabeceras de la tabla -->
          </thead>
          <tbody id="tbody-items">
            <!-- Aquí se renderizarán las filas -->
          </tbody>
        </table>
      </div>
    `;
    return template;
  }

  addListeners() {
    this.shadowRoot.getElementById("table-content").addEventListener("scroll", this.handleScroll.bind(this));
    this.shadowRoot.getElementById("search_input").addEventListener('keyup', this.handleSearch.bind(this));
    this.shadowRoot.getElementById("search_button").addEventListener('click', this.handleSearchClick.bind(this));
  }

  async get_items(page = 1) {
    if (this.is_fetching) return;
    this.is_fetching = true;
    this.loading_records(true);

    let url = `configuracion_secciones_pos/bd/backend.php`;
    let data = {
      page,
      q: this.q,
      option: 'get_items',
      id_seccion: '<?= $id_seccion ?>',
      id_empresa: '<?= $_SESSION["EMPRESA"] ?>'
    };

    let requestOptions = {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify(data)
    };

    try {
      let response = await fetch(url, requestOptions);
      let data = await response.json();
      let content = this.render_list(data);
      let tbody = this.shadowRoot.getElementById("tbody-items");
      if (data.length > 0) {
        tbody.innerHTML = (this.data_list.length == 0 || page == 1) ? content : tbody.innerHTML + content;
        this.data_list = [...data];
        this.loading_records(false);
        this.end_fetching = false;
      } else {
        this.end_fetching = true;
      }
    } catch (error) {
      console.log(error);
    }

    this.loading_records(false);
    this.is_fetching = false;
  }

  loading_records(state) {
    let tbody = this.shadowRoot.getElementById("tbody-items");
    if (state && !this.end_fetching) {
      let new_tr = document.createElement("tr");
      new_tr.setAttribute("id", "loading-tr");
      new_tr.innerHTML = `<td colspan="6">
                            <div class='flex items-center justify-center'>
                              <div class="px-2">cargando</div>  
                              <div class="animate-bounce w-6 text-5xl">.</div>
                              <div class="animate-bounce w-6 text-5xl" style="animation-delay: 0.2s">.</div>
                              <div class="animate-bounce w-6 text-5xl" style="animation-delay: 0.4s">.</div>
                            </div>
                          </td>`;
      tbody.appendChild(new_tr);
    } else {
      try {
        this.shadowRoot.getElementById("loading-tr").remove();
      } catch (error) {
        // console.warn(error)				
      }
    }
  }

  render_list(data) {
    return data.map(element => (
      `<tr class="bg-white odd:bg-white even:bg-slate-50 hover:bg-gray-200 cursor-default">
        <td class="px-6 py-1 text-center">
          <input type="checkbox" ${(element.id_seccion) ? "checked" : "" } onclick="check_uncheck(${element.id})" id="check_${element.id}" class="cursor-pointer h-4 w-4">
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
    )).join('');
  }

  handleScroll(event) {
    let table = event.target;
    if (table.scrollHeight - table.scrollTop === table.clientHeight) {
      this.page++;
      this.get_items(this.page);
    }
  }

  handleSearch(event) {
    this.q = event.target.value;
    this.data_list = [];

    if (event.keyCode === 13 || this.q === "") {
      this.shadowRoot.getElementById("tbody-items").innerHTML = "";
      if (!this.is_fetching) {
        this.get_items(1);
      }
    }
  }

  handleSearchClick() {
    this.q = this.shadowRoot.getElementById("search_input").value;
    this.data_list = [];
    if (!this.is_fetching) {
      this.shadowRoot.getElementById("tbody-items").innerHTML = "";
      this.get_items(1);
    }
  }
}

customElements.define('data-table', DataTable);
