<!DOCTYPE html>
<html lang="en">
    
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap');
        .font-montserrat {
            font-family: 'Montserrat', sans-serif;
        }
    </style>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'blue':{
                            'dark' :  '#000089'
                        },
                        'gray' : {
                            'light' : '#DADADA'
                        },
                        'orange' : {
                            'dark' : '#FF6010',
                            'light' : '#ffb088'
                        },
                        screens: {
                            'h-sm': { 'raw': '(max-height: 640px)' }, // Custom media query for max height
                        }
                    }
                }
            }
        }
        // icons 
    </script>
    <script src="LOGICALERP/login/index.js"></script>
    <title>Login</title>
</head>

<body class="font-montserrat overflow-hidden">
    <div class="absolute top-0 left-0 h-screen w-screen bg-white z-20 flex items-center justify-center hidden" id="login-loader">
        <span class="animate-ping absolute inline-flex h-20 w-20 rounded-full bg-gray-400 opacity-75"></span>
        <span class="relative inline-flex rounded-full h-20 w-20 bg-gray-400"></span>    
    </div>

    <div id="modal-content" class="absolute w-full h-full left-0 top-0 bg-gray-200/50 flex justify-center items-center">
        <div id="content" class="bg-white opacity-1 p-4 min-w-96 max-h-full">
            <div class="flex items-center justify-between p-4 md:p-5 rounded-t ">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                    Seleccione la empresa
                </h3>
                <button id="close_modal" type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm h-8 w-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-toggle="select-modal">
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                    </svg>
                    <span class="sr-only" >Close modal</span>
                </button>
            </div>
            <div class="overflow-auto max-h-96">
                <ul class="space-y-4 mb-4" id="companies">

                    <li class="group animate-pulse">
                        <label class="inline-flex items-center justify-between w-full p-5 text-gray-900 bg-gray-200 border border-gray-200 rounded-lg cursor-pointer peer-checked:border-blue-600 peer-checked:text-blue-600 ">                           
                            <div class="flex gap-4 w-full flex-col">
                                <div class="w-24 text-base font-bold">
                                    <div class="h-2 bg-slate-400 rounded"></div>
                                </div>
                                <div class="w-40 text-sm text-gray-500 dark:text-gray-400">
                                    <div class="h-2 bg-slate-400 rounded"></div>
                                </div>
                            </div>
                            <svg class="w-4 h-4 ms-3 rtl:rotate-180 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 10"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 5h12m0 0L9 1m4 4L9 9"/></svg>
                        </label>
                    </li>

                </ul>
            </div>
        </div>
    </div>
    <div class="bg-[#EFF4FD] w-screen h-screen overflow-hidden flex relative">
        <div class="bg-white xl:w-[536px] md:w-[536px] w-full z-10 flex justify-center">
            <div class="flex flex-col gap-8 h-sm:py-0 py-16 px-20 md:px-[85px] xl:px-[85px] h-screen overflow-y-auto">
                <img src="assets/img/plataforma_software.png" alt="logo" width="65" class="mb-3">
                <h1 class="text-title-10 text-brand-blue600 text-blue-dark text-4xl font-semibold">Welcome</h1>
                <p class="text-body-30-regular text-neutral-black leading-[17.5px] max-w-[367px]">Ingrese todos los campos para iniciar sesion en el software contable</p>
                <div>
                    <form  class="grid gap-5 max-w-[500px]" onsubmit="return false;">
                        <div class="grid w-full">
                            <div class="h-[48px] relative">
                                <label class="translate-y-3 text-body-30-regular absolute left-[45px] flex items-center text-neutral-mediumGray transition-all" for="nit">
                                </label>
                            <div class="text-[20px] flex items-center absolute top-0 bottom-0 left-[16px] m-auto text-neutral-mediumGray">
                                <svg width=".8em" height=".8em" viewBox="0 0 32 32" fill="currentColor" xmlns="http://www.w3.org/2000/svg">                                    
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M9.53314 0.28277C9.96402 -0.0942565 10.6074 -0.0942565 11.0383 0.28277L20.1811 8.28277C20.4292 8.49978 20.5714 8.8133 20.5714 9.14286V13.7143H30.8571C31.4883 13.7143 32 14.226 32 14.8571V30.8571C32 31.4883 31.4883 32 30.8571 32H19.4286H10.2857H1.14286C0.511675 32 0 31.4883 0 30.8571V9.14286C0 8.8133 0.142263 8.49978 0.39028 8.28277L9.53314 0.28277ZM11.4286 29.7143V26.2857C11.4286 25.6545 10.9169 25.1429 10.2857 25.1429C9.65453 25.1429 9.14286 25.6545 9.14286 26.2857V29.7143H2.28571V9.66145L10.2857 2.66145L18.2857 9.66145L18.2857 14.8571L18.2857 29.7143H11.4286ZM29.7143 16H20.5714V29.7143H29.7143V16ZM5.71429 19.4286C5.71429 18.7974 6.22596 18.2857 6.85714 18.2857H13.7143C14.3455 18.2857 14.8571 18.7974 14.8571 19.4286C14.8571 20.0598 14.3455 20.5714 13.7143 20.5714H6.85714C6.22596 20.5714 5.71429 20.0598 5.71429 19.4286ZM6.85714 11.4286C6.22596 11.4286 5.71429 11.9402 5.71429 12.5714C5.71429 13.2026 6.22596 13.7143 6.85714 13.7143H13.7143C14.3455 13.7143 14.8571 13.2026 14.8571 12.5714C14.8571 11.9402 14.3455 11.4286 13.7143 11.4286H6.85714Z" fill="currentColor"></path>
                                </svg>
                            </div>
                                <input
                                    class="border-neutral-pureGray ps-11 absolute bg-transparent w-full h-full border rounded-[12px] pe-3 text-[16px] text-body-30-regular focus:text-brand-blue600 focus:border-brand-blue400 outline-none"
                                    type="text" 
                                    name="nit" 
                                    placeholder="Nit / CODE"
                                    id="n_documento"
                                    value="900542975"
                                    required 
                                >
                            </div>
                        </div>
                        <div class="grid">
                            <div class="max-w-[367px] h-[48px] relative">
                                <label class="translate-y-3 text-[16px] absolute left-[45px] flex items-center text-neutral-mediumGray transition-all" >
                                </label>
                                <div class="text-[20px] flex items-center absolute top-0 bottom-0 left-[16px] m-auto text-neutral-mediumGray">
                                    <svg width=".8em" height=".8em" viewBox="0 0 32 32" fill="currentColor"  xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" clip-rule="evenodd"  d="M9.53314 0.28277C9.96402 -0.0942565 10.6074 -0.0942565 11.0383 0.28277L20.1811 8.28277C20.4292 8.49978 20.5714 8.8133 20.5714 9.14286V13.7143H30.8571C31.4883 13.7143 32 14.226 32 14.8571V30.8571C32 31.4883 31.4883 32 30.8571 32H19.4286H10.2857H1.14286C0.511675 32 0 31.4883 0 30.8571V9.14286C0 8.8133 0.142263 8.49978 0.39028 8.28277L9.53314 0.28277ZM11.4286 29.7143V26.2857C11.4286 25.6545 10.9169 25.1429 10.2857 25.1429C9.65453 25.1429 9.14286 25.6545 9.14286 26.2857V29.7143H2.28571V9.66145L10.2857 2.66145L18.2857 9.66145L18.2857 14.8571L18.2857 29.7143H11.4286ZM29.7143 16H20.5714V29.7143H29.7143V16ZM5.71429 19.4286C5.71429 18.7974 6.22596 18.2857 6.85714 18.2857H13.7143C14.3455 18.2857 14.8571 18.7974 14.8571 19.4286C14.8571 20.0598 14.3455 20.5714 13.7143 20.5714H6.85714C6.22596 20.5714 5.71429 20.0598 5.71429 19.4286ZM6.85714 11.4286C6.22596 11.4286 5.71429 11.9402 5.71429 12.5714C5.71429 13.2026 6.22596 13.7143 6.85714 13.7143H13.7143C14.3455 13.7143 14.8571 13.2026 14.8571 12.5714C14.8571 11.9402 14.3455 11.4286 13.7143 11.4286H6.85714Z" fill="currentColor"></path>
                                    </svg>
                                </div>
                                <select required  id="sucursal" class=" absolute bg-transparent w-full h-full border border-neutral-pureGray  rounded-[12px] ps-10 pe-3 text-[16px] focus:text-brand-blue600 focus:border-brand-blue400 outline-none">
                                    <option value="">Sucursal...</option>
                                </select>
                                <div id="branch-skeleton" class="hidden animate-pulse rounded-lg w-full h-full flex items-center justify-center">
                                    <div class="w-full bg-gray-400 h-5 rounded-lg ml-10"></div>
                                </div>
                            </div>
                        </div>
                        <div class="grid w-full">
                            <div class="h-[48px] relative">
                                <label class="translate-y-3 text-body-30-regular absolute left-[45px] flex items-center text-neutral-mediumGray transition-all" for="email"></label>
                                <div class="text-[20px] flex items-center absolute top-0 bottom-0 left-[16px] m-auto text-neutral-mediumGray">
                                    <svg width="1em" height="1em" viewBox="0 0 40 40" fill="currentColor"xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M6.78571 19.5C6.78571 11.9258 12.9258 5.78571 20.5 5.78571C28.0742 5.78571 34.2143 11.9258 34.2143 19.5C34.2143 23.1623 32.7788 26.4893 30.4397 28.9491C29.4127 27.6229 28.1277 26.5118 26.6571 25.6857C24.7769 24.6295 22.6566 24.0748 20.5001 24.0748C18.3436 24.0748 16.2233 24.6295 14.3432 25.6857C12.8725 26.5118 11.5874 27.623 10.5604 28.9492C8.2213 26.4895 6.78571 23.1624 6.78571 19.5ZM12.2726 30.4733C14.5645 32.1945 17.4132 33.2143 20.5 33.2143C23.5869 33.2143 26.4356 32.1944 28.7275 30.4732C27.8721 29.3331 26.7867 28.3801 25.5376 27.6785C23.9993 26.8144 22.2645 26.3605 20.5001 26.3605C18.7357 26.3605 17.0009 26.8144 15.4626 27.6785C14.2135 28.3802 13.1281 29.3331 12.2726 30.4733ZM20.5 3.5C11.6634 3.5 4.5 10.6634 4.5 19.5C4.5 28.3366 11.6634 35.5 20.5 35.5C29.3366 35.5 36.5 28.3366 36.5 19.5C36.5 10.6634 29.3366 3.5 20.5 3.5ZM20.5 11.5C17.9753 11.5 15.9286 13.5467 15.9286 16.0714C15.9286 18.5962 17.9753 20.6429 20.5 20.6429C23.0247 20.6429 25.0714 18.5962 25.0714 16.0714C25.0714 13.5467 23.0247 11.5 20.5 11.5ZM13.6429 16.0714C13.6429 12.2843 16.7129 9.21429 20.5 9.21429C24.2871 9.21429 27.3571 12.2843 27.3571 16.0714C27.3571 19.8585 24.2871 22.9286 20.5 22.9286C16.7129 22.9286 13.6429 19.8585 13.6429 16.0714Z" fill="currentColor"></path>
                                    </svg>
                                </div>
                                <input
                                    class="border-neutral-pureGray ps-11 absolute bg-transparent w-full h-full border rounded-[12px] pe-3 text-[16px] text-body-30-regular focus:text-brand-blue600 focus:border-brand-blue400 outline-none"
                                    type="text" 
                                    value=""
                                    placeholder="Usuario"
                                    id="usuario"
                                    required 
                                >
                            </div>
                        </div>
                        <div class="grid w-full">
                            <div class="h-[48px] relative">
                                <label class="translate-y-3 text-body-30-regular absolute left-[45px] flex items-center text-neutral-mediumGray transition-all" for="password"></label>
                                <div class="text-[20px] flex items-center absolute top-0 bottom-0 left-[16px] m-auto text-neutral-mediumGray">
                                    <svg width="1em" height="1em" viewBox="0 0 40 40" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M13.1065 6.67788C14.8211 4.96326 17.1466 4 19.5714 4C21.9963 4 24.3218 4.96326 26.0364 6.67788C27.751 8.3925 28.7143 10.718 28.7143 13.1429L28.7143 15.4286C30.6078 15.4286 32.1429 16.9636 32.1429 18.8571V32.5714C32.1429 34.465 30.6078 36 28.7143 36H10.4286C8.53502 36 7 34.465 7 32.5714V18.8571C7 16.9636 8.53502 15.4286 10.4286 15.4286V13.1429C10.4286 10.718 11.3918 8.3925 13.1065 6.67788ZM27.5714 17.7143L27.5693 17.7143H11.5735L11.5714 17.7143L11.5693 17.7143H10.4286C9.79739 17.7143 9.28571 18.226 9.28571 18.8571V32.5714C9.28571 33.2026 9.79739 33.7143 10.4286 33.7143H28.7143C29.3455 33.7143 29.8571 33.2026 29.8571 32.5714V18.8571C29.8571 18.226 29.3455 17.7143 28.7143 17.7143H27.5735L27.5714 17.7143ZM26.4286 13.1429V15.4286H12.7143V13.1429C12.7143 11.3242 13.4367 9.58009 14.7227 8.29413C16.0087 7.00816 17.7528 6.28571 19.5714 6.28571C21.3901 6.28571 23.1342 7.00816 24.4202 8.29413C25.7061 9.58009 26.4286 11.3242 26.4286 13.1429ZM19.5714 23.4286C18.3091 23.4286 17.2857 24.4519 17.2857 25.7143C17.2857 26.9767 18.3091 28 19.5714 28C20.8338 28 21.8571 26.9767 21.8571 25.7143C21.8571 24.4519 20.8338 23.4286 19.5714 23.4286Z" fill="currentColor"></path>
                                    </svg>
                                </div>
                                <button type="button" class="text-[20px] flex items-center absolute top-0 bottom-0 right-[16px] m-auto text-neutral-mediumGray z-10 outline-none">
                                    <svg width="1em" height="1em" viewBox="0 0 40 40" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M6.28976 20.3039C6.28976 20.0228 6.39314 19.7517 6.58008 19.542C7.74603 18.288 9.63885 16.46 11.9915 14.9507C14.3546 13.4347 17.104 12.2898 20 12.2898C22.896 12.2898 25.6454 13.4347 28.0085 14.9507C30.3612 16.46 32.254 18.288 33.4199 19.542C33.6069 19.7517 33.7102 20.0229 33.7102 20.3039C33.7102 20.585 33.6069 20.8561 33.4199 21.0658C32.254 22.3198 30.3611 24.1478 28.0085 25.6572C25.6454 27.1731 22.896 28.3181 20 28.3181C17.104 28.3181 14.3546 27.1731 11.9915 25.6572C9.63886 24.1478 7.74604 22.3198 6.58009 21.0658C6.39315 20.8561 6.28976 20.585 6.28976 20.3039ZM20 10C16.5075 10 13.3265 11.3738 10.7551 13.0234C8.17782 14.6768 6.13496 16.6572 4.89611 17.9905L4.89603 17.9904L4.8849 18.0027C4.3153 18.6338 4 19.4538 4 20.3039C4 21.1541 4.3153 21.974 4.8849 22.6051L4.88481 22.6052L4.89611 22.6174C6.13496 23.9506 8.17782 25.931 10.7551 27.5844C13.3265 29.234 16.5075 30.6078 20 30.6078C23.4925 30.6078 26.6735 29.234 29.2449 27.5844C31.8222 25.931 33.865 23.9506 35.1039 22.6174L35.104 22.6174L35.1151 22.6051C35.6847 21.974 36 21.1541 36 20.3039C36 19.4538 35.6847 18.6338 35.1151 18.0027L35.1152 18.0026L35.1039 17.9905C33.865 16.6572 31.8222 14.6768 29.2449 13.0234C26.6735 11.3738 23.4925 10 20 10ZM16.2898 20.7244C16.2898 18.8275 17.8275 17.2898 19.7244 17.2898C21.6213 17.2898 23.159 18.8275 23.159 20.7244C23.159 22.6213 21.6213 24.159 19.7244 24.159C17.8275 24.159 16.2898 22.6213 16.2898 20.7244ZM19.7244 15C16.5629 15 14 17.5629 14 20.7244C14 23.8859 16.5629 26.4488 19.7244 26.4488C22.8859 26.4488 25.4488 23.8859 25.4488 20.7244C25.4488 17.5629 22.8859 15 19.7244 15Z" fill="currentColor"></path>
                                    </svg>
                                </button>
                                <input
                                    class="border-neutral-pureGray ps-11 absolute bg-transparent w-full h-full border rounded-[12px] pe-3 text-[16px] text-body-30-regular focus:text-brand-blue600 focus:border-brand-blue400 outline-none"
                                    type="password" 
                                    value=""
                                    placeholder="Contraseña"
                                    id="password"
                                    required 
                                >
                            </div>
                        </div>
                            <a
                                class="text-body-20-semibold text-brand-blue100 underline mt-[-10px] w-fit place-self-end"
                                href="#">Forgot your Password?</a>
                            <label class="text-body-20-regular text-neutral-darkGray flex items-center gap-3 w-fit">
                                <input type="checkbox" name="stayLogged" class="my-5" value="false">
                                Stay Logged
                            </label>
                            <!-- <button class="max-w-[367px] px-[16px] py-2 h-[48px] text-subtitle-10 rounded-[12px] grid place-content-center bg-brand-orange600 transition-all text-white bg-neutral-lightGray pointer-events-none" type="submit"> -->
                           <button id="btn_submit" class=" cursor-pointer p-2 rounded-lg flex items-center gap-5 place-content-center text-subtitle-10 bg-orange-dark hover:bg-orange-light text-white text-xl">
                                Continue 
                                <svg width="1em" height="1em" viewBox="0 0 40 40" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M36.3428 20C36.3428 20.3637 36.1983 20.7126 35.9411 20.9698L24.9697 31.9412C24.4341 32.4768 23.5657 32.4768 23.0302 31.9412C22.4946 31.4056 22.4946 30.5373 23.0302 30.0017L31.6604 21.3715L5.7142 21.3715C4.95678 21.3715 4.34277 20.7574 4.34277 20C4.34277 19.2426 4.95678 18.6286 5.7142 18.6286L31.6604 18.6286L23.0302 9.99834C22.4946 9.46276 22.4946 8.59442 23.0302 8.05885C23.5657 7.52327 24.4341 7.52327 24.9697 8.05885L35.9411 19.0303C36.1983 19.2875 36.3428 19.6363 36.3428 20Z" fill="currentColor"></path>
                                </svg>
                            </button>
                        <!-- </button> -->
                    </form>
                </div>
                <img class="self-center mt-6" src="assets/img/plataforma_software.png" width="120"  alt="logo_plataforma">
            </div>
        </div> 
    </div>

    

</body>

</html>