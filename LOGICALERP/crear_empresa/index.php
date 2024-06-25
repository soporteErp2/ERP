<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>
    <div class="flex flex-col w-full py-10">
        <div class="sticky top-0" >
            <ol class=" text-gray-500 border-s border-gray-200 dark:border-gray-700 dark:text-gray-400 flex justify-center  ">                  
                <li class="mb-5 ms-6 flex justify-between cursor-pointer">            
                    <span class="flex items-center justify-center w-8 h-8 bg-gray-100 rounded-full -start-4 ring-4 ring-white dark:ring-gray-900 dark:bg-gray-700">
                        <svg class="w-3.5 h-3.5 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 18 20">
                            <path d="M16 1h-3.278A1.992 1.992 0 0 0 11 0H7a1.993 1.993 0 0 0-1.722 1H2a2 2 0 0 0-2 2v15a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V3a2 2 0 0 0-2-2Zm-3 14H5a1 1 0 0 1 0-2h8a1 1 0 0 1 0 2Zm0-4H5a1 1 0 0 1 0-2h8a1 1 0 1 1 0 2Zm0-5H5a1 1 0 0 1 0-2h2V2h4v2h2a1 1 0 1 1 0 2Z"/>
                        </svg>
                    </span>
                    <div>
                        <h3 class="font-medium leading-tight">Empresa</h3>
                        <p class="text-sm">Detalles de la empresa</p>
                    </div>
                </li>
                <li class="mb-5 ms-6  flex justify-between cursor-pointer">
                    <span class=" flex items-center justify-center w-8 h-8 bg-gray-100 rounded-full -start-4 ring-4 ring-white dark:ring-gray-900 dark:bg-gray-700">
                        <svg class="w-3.5 h-3.5 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 16">
                            <path d="M18 0H2a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2ZM6.5 3a2.5 2.5 0 1 1 0 5 2.5 2.5 0 0 1 0-5ZM3.014 13.021l.157-.625A3.427 3.427 0 0 1 6.5 9.571a3.426 3.426 0 0 1 3.322 2.805l.159.622-6.967.023ZM16 12h-3a1 1 0 0 1 0-2h3a1 1 0 0 1 0 2Zm0-3h-3a1 1 0 1 1 0-2h3a1 1 0 1 1 0 2Zm0-3h-3a1 1 0 1 1 0-2h3a1 1 0 1 1 0 2Z"/>
                        </svg>
                    </span>
                    <div>
                        <h3 class="font-medium leading-tight">Usuario</h3>
                        <p class="text-sm">Datos del superusuario</p>
                    </div>
                </li>
            </ol>
        </div>
        <div class="border-t border-gray-100 w-full flex justify-center py-5">
            <div id="empresa-info" class="grid grid-cols-2 gap-4">
                <div class="mb-5 col-span-2">
                    <label for="documento" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Tipo documento</label>
                    <select id="documento" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                        <option value="C.C">C.C</option>
                        <option value="T.I">T.I</option>
                        <option value="PASAPORTE">PASAPORTE</option>
                        <option value="C.E">C.E</option>
                        <option value="NIT" selected="">NIT</option>
                        <option value="RUC">RUC</option>
                        <option value="C.I">C.I</option>
                    </select>
                </div>
                <div class="mb-5">
                    <label for="documento" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Numero documento</label>
                    <input type="text" id="documento" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 dark:shadow-sm-light" required />
                </div>
                <div class="mb-5">
                    <label for="dv" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Digito verificacion</label>
                    <input type="text" id="dv" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 dark:shadow-sm-light" required />
                </div>
                <div class="mb-5 col-span-2">
                    <label for="nombre_empres" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nombre de la empresa</label>
                    <input type="text" id="nombre_empres" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 dark:shadow-sm-light" required />
                </div>
                <div class="mb-5 col-span-2">
                    <label for="razon_social" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Razon Social</label>
                    <input type="text" id="razon_social" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 dark:shadow-sm-light" required />
                </div>
                <div class="mb-5 col-span-2">
                    <label for="actividad_economica" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Actividad Economica</label>
                    <input type="text" id="actividad_economica" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 dark:shadow-sm-light" required />
                </div>
            </div>
            <div id="usuario-info"></div>
        </div>
    </div>
    <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Register new account</button>
</body>
</html>