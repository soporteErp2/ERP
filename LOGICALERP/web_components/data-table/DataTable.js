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

    // Insertar template en el Shadow DOM
    this.render()

    // // Agregar listeners a los elementos
    // this.addListeners();
    
    // // Cargar los ítems iniciales
    // this.get_records(1);
  }

  static get observedAttributes() {
    return ['endpoint','columns'];
  }

  attributeChangedCallback(name, oldValue, newValue) {
    this[name] = newValue;
    this.render()
  }

  connectedCallback() {
    // // Llamar funciones cuando el shadowRoot ya está en el DOM
    this.render();
    this.addListeners();
    this.get_records(1);
  }

  render(){
    this.shadowRoot.innerHTML = '';
    const templateContent = this.template().content.cloneNode(true);
    this.shadowRoot.appendChild(templateContent);
  }

  template() {
    
    const template = document.createElement('template');

    if (!this.columns) {
      template.innerHTML = "where is columns var?";
      return template;
    }
    let cols = JSON.parse(this.columns)
    template.innerHTML = /*html*/`
        <head>
          <link rel="stylesheet" href="../../assets/css/tailwind.css">
        </head>

        <div class="w-full bg-white  ">
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
                  ${cols.map(element => /*html*/`
                      <th scope="col" class="${element.css ? element.css : "py-2" }">
                        <span>${element.field}</span>
                      </th>
                    `).join('')}
                </tr>
              </thead>
              <tbody id="tbody-data-table">
              </tbody>
            </table>
          </div>
        
        </div>
    `;
    return template;
  }

  addListeners() {
    this.shadowRoot.getElementById("table-content").addEventListener("scroll", this.handleScroll.bind(this));
    this.shadowRoot.getElementById("search_input").addEventListener('keyup', this.handleSearch.bind(this));
    this.shadowRoot.getElementById("search_button").addEventListener('click', this.handleSearchClick.bind(this));
  }

  async get_records(page = 1) {
    if (this.is_fetching) return;
    this.is_fetching = true;
    this.loading_records(true);

    let endpoint = JSON.parse(this.endpoint);

    let url = endpoint.url;
    let requestOptions = {
      method: endpoint.method,
      headers: {
        'Content-Type': 'application/json'
      },
      body: null
    };

    // Si el método es GET, construimos la URL con los parámetros
    if (endpoint.method === "GET" && Array.isArray(endpoint.params)) {
        let queryString = endpoint.params.map(param => {
            let key = Object.keys(param)[0];
            let value = encodeURIComponent(param[key]);
            return `${key}=${value}`;
        }).join('&');
        
        url += `?${queryString}&page=${this.page}&q=${this.q}&order_by=${this.order_by}`;
    }

    // Si el método es POST, enviamos los parámetros en el cuerpo de la solicitud
    if (endpoint.method === "POST" && Array.isArray(endpoint.params)) {
        let data = {};
        endpoint.params.forEach(param => {
            let key = Object.keys(param)[0];
            data[key] = param[key];
        });

        requestOptions.body = JSON.stringify(data);
    }

    try {
      let response = await fetch(url, requestOptions);
      let data = await response.json();
      let content = this.render_list(data);
      let tbody = this.shadowRoot.getElementById("tbody-data-table");
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
    let tbody = this.shadowRoot.getElementById("tbody-data-table");
    if (!tbody) {
      return;
    }
    // console.log(tbody)
    if (state && !this.end_fetching) {
      let new_tr = document.createElement("tr");
      new_tr.setAttribute("id", "loading-tr");
      new_tr.innerHTML = /*html*/`
                          <td colspan="6">
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
    let cols = JSON.parse(this.columns)
    
    

    return data.map(element => (
      /*html*/`<tr class="bg-white odd:bg-white even:bg-slate-50 hover:bg-gray-200 cursor-default">
                  ${cols.map(col => /*html*/`
                        <td scope="col" class="py-2" }">
                          <span>${element[col.field]}</span>
                        </td>
                      `).join('')}
              </tr>`
    )).join('');
  }

  handleScroll(event) {
    let table = event.target;
    if (table.scrollHeight - table.scrollTop === table.clientHeight) {
      this.page++;
      this.get_records(this.page);
    }
  }

  handleSearch(event) {
    this.q = event.target.value;
    this.data_list = [];

    if (event.keyCode === 13 || this.q === "") {
      this.shadowRoot.getElementById("tbody-data-table").innerHTML = "";
      if (!this.is_fetching) {
      this.page=1;
      this.get_records(1);
      }
    }
  }

  handleSearchClick() {
    this.q = this.shadowRoot.getElementById("search_input").value;
    this.data_list = [];
    if (!this.is_fetching) {
      this.shadowRoot.getElementById("tbody-data-table").innerHTML = "";
      this.page=1;
      this.get_records(1);
    }
  }
}

customElements.define('data-table', DataTable);
