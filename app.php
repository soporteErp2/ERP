<?php
    session_start();
    if (!isset($_SESSION['IDUSUARIO'])) {
        header('location: login.php');
    }
	$fileRoot = ($_SERVER['SERVER_NAME'] == 'localhost')? $_SERVER['DOCUMENT_ROOT']."/ERP":$_SERVER['DOCUMENT_ROOT'];
    $fileRoute = $fileRoot."/ARCHIVOS_PROPIOS/empresa_".$_SESSION['ID_HOST']."/assets/logo.png";
	$serverRoot = ($_SERVER['SERVER_NAME'] == 'localhost')? "http://localhost/ERP":"https://".$_SERVER['SERVER_NAME'];
    $imgRoute = '"'.$serverRoot.'/ARCHIVOS_PROPIOS/empresa_'.$_SESSION['ID_HOST'].'/assets/logo.png"';

    $logo_or_name = (file_exists($fileRoute))? "<img src=$imgRoute alt='Logo Empresa' class='h-12 ml-2 w-auto' />" : 
    "<span class='font-montserrat font-bold text-gray-700'>".$_SESSION['NOMBREEMPRESA']."</span>";

    // var_dump($_SESSION);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ERP</title>
    <link rel="stylesheet" href="assets/css/tailwind.css">
    <link rel="stylesheet" href="LOGICALERP/app/app_style.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap');
        .font-montserrat {
            font-family: 'Montserrat', sans-serif;
        }
    </style>
    <script src="LOGICALERP/app/app_js.js"></script>
</head>
<body class="h-screen">
    <!-- <div class="absolute top-0 left-0 h-screen w-screen bg-white z-20 flex items-center justify-center" id="app-loader">
        <span class="animate-ping absolute inline-flex h-20 w-20 rounded-full bg-gray-400 opacity-75"></span>
        <span class="relative inline-flex rounded-full h-20 w-20 bg-gray-400"></span>    
    </div> -->

    <div id="main" class="font-montserrat h-screen flex flex-col">
        <div id="header" class="bg-white border-b border-gray-dark w-full h-15 flex justify-between items-center content-center p-2">
            <div class="flex items-center gap-2">
                <svg id="nav-btn" class="fill-gray-icon h-8 w-8 cursor-pointer" fill="none" viewBox="0 0 40 40" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" clip-rule="evenodd" fill="text-gray-icon" d="M6.28571 20C6.28571 12.4258 12.4258 6.28571 20 6.28571C27.5742 6.28571 33.7143 12.4258 33.7143 20C33.7143 27.5742 27.5742 33.7143 20 33.7143C12.4258 33.7143 6.28571 27.5742 6.28571 20ZM20 4C11.1634 4 4 11.1634 4 20C4 28.8366 11.1634 36 20 36C28.8366 36 36 28.8366 36 20C36 11.1634 28.8366 4 20 4ZM14.2857 13.1429C13.6545 13.1429 13.1429 13.6545 13.1429 14.2857C13.1429 14.9169 13.6545 15.4286 14.2857 15.4286H25.7143C26.3455 15.4286 26.8571 14.9169 26.8571 14.2857C26.8571 13.6545 26.3455 13.1429 25.7143 13.1429H14.2857ZM13.1429 20C13.1429 19.3688 13.6545 18.8571 14.2857 18.8571H25.7143C26.3455 18.8571 26.8571 19.3688 26.8571 20C26.8571 20.6312 26.3455 21.1429 25.7143 21.1429H14.2857C13.6545 21.1429 13.1429 20.6312 13.1429 20ZM14.2857 24.5714C13.6545 24.5714 13.1429 25.0831 13.1429 25.7143C13.1429 26.3455 13.6545 26.8571 14.2857 26.8571H25.7143C26.3455 26.8571 26.8571 26.3455 26.8571 25.7143C26.8571 25.0831 26.3455 24.5714 25.7143 24.5714H14.2857Z" />
                </svg>
              <?= $logo_or_name ?>
            </div>
            <div>
                <div id="profile-btn" class="bg-gray-dropdown w-28 p-1 h-8 rounded-full text-gray-text flex justify-around content-center font-medium cursor-pointer">
                    <svg class="h-6 w-5" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M6.78571 19.5C6.78571 11.9258 12.9258 5.78571 20.5 5.78571C28.0742 5.78571 34.2143 11.9258 34.2143 19.5C34.2143 23.1623 32.7788 26.4893 30.4397 28.9491C29.4127 27.6229 28.1277 26.5118 26.6571 25.6857C24.7769 24.6295 22.6566 24.0748 20.5001 24.0748C18.3436 24.0748 16.2233 24.6295 14.3432 25.6857C12.8725 26.5118 11.5874 27.623 10.5604 28.9492C8.2213 26.4895 6.78571 23.1624 6.78571 19.5ZM12.2726 30.4733C14.5645 32.1945 17.4132 33.2143 20.5 33.2143C23.5869 33.2143 26.4356 32.1944 28.7275 30.4732C27.8721 29.3331 26.7867 28.3801 25.5376 27.6785C23.9993 26.8144 22.2645 26.3605 20.5001 26.3605C18.7357 26.3605 17.0009 26.8144 15.4626 27.6785C14.2135 28.3802 13.1281 29.3331 12.2726 30.4733ZM20.5 3.5C11.6634 3.5 4.5 10.6634 4.5 19.5C4.5 28.3366 11.6634 35.5 20.5 35.5C29.3366 35.5 36.5 28.3366 36.5 19.5C36.5 10.6634 29.3366 3.5 20.5 3.5ZM20.5 11.5C17.9753 11.5 15.9286 13.5467 15.9286 16.0714C15.9286 18.5962 17.9753 20.6429 20.5 20.6429C23.0247 20.6429 25.0714 18.5962 25.0714 16.0714C25.0714 13.5467 23.0247 11.5 20.5 11.5ZM13.6429 16.0714C13.6429 12.2843 16.7129 9.21429 20.5 9.21429C24.2871 9.21429 27.3571 12.2843 27.3571 16.0714C27.3571 19.8585 24.2871 22.9286 20.5 22.9286C16.7129 22.9286 13.6429 19.8585 13.6429 16.0714Z" fill="#3C3C3B"/>
                    </svg>                   
                    <span>Perfil</span>
                    <svg class="h-8 w-5" viewBox="10 5 60 90" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M24.3347 32.3347C24.781 31.8884 25.5047 31.8884 25.951 32.3347L40 46.3838L54.049 32.3347C54.4953 31.8884 55.219 31.8884 55.6653 32.3347C56.1116 32.781 56.1116 33.5047 55.6653 33.951L41.6187 47.9976C41.4121 48.2135 41.1643 48.386 40.8899 48.5048C40.609 48.6264 40.3061 48.6892 40 48.6892C39.6939 48.6892 39.391 48.6264 39.1101 48.5048C38.8357 48.386 38.5879 48.2135 38.3813 47.9976L24.3347 33.951C23.8884 33.5047 23.8884 32.781 24.3347 32.3347Z" fill="#3C3C3B"/>
                    </svg>
                        
                </div>
                <div id="profile-content" class="opacity-0 absolute right-0 m-3 w-80 origin-top-right divide-y divide-gray-100 rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none  transition-all ease-in-out duration-200" role="menu" aria-orientation="vertical" aria-labelledby="menu-button" tabindex="-1">
                    <div class="py-1 hover:bg-gray-light" role="none">
                      <a href="#" class="block px-4 pt-2 text-sm text-gray-text font-bold"><?= $_SESSION['NOMBREEMPRESA']?></a>
                      <a href="#" class="block px-4 text-sm text-gray-text"><?= $_SESSION['NOMBRESUCURSAL'] ?></a>
                    </div>
                    <div class="py-1 hover:bg-gray-light" role="none">
                      <a href="#" class="block px-4 pt-2 text-sm text-gray-text font-bold"><?= $_SESSION['NOMBREFUNCIONARIO']? $_SESSION['NOMBREFUNCIONARIO'] : $_SESSION['NOMBREUSUARIO'] ?></a>
                      <a href="#" class="block px-4 text-sm text-gray-text"><?= $_SESSION['NOMBREROL'] ?></a>
                    </div>
                    <div class="py-1 text-red-500 flex items-center justify-start pl-2 hover:bg-red-50 cursor-pointer font-medium" role="none">
                        <svg class="h-5 w-5" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path fill="currentColor" fill-rule="evenodd" clip-rule="evenodd" d="M16.8835 9.27598C19.4225 8.25496 22.209 8.0169 24.8845 8.59243C27.56 9.16795 30.0021 10.5307 31.8967 12.5055C32.3337 12.961 33.0572 12.976 33.5126 12.539C33.9681 12.102 33.9831 11.3785 33.5461 10.9231C31.3357 8.6192 28.4866 7.02928 25.3652 6.35783C22.2438 5.68638 18.9929 5.96412 16.0307 7.15531C13.0684 8.34649 10.5303 10.3967 8.74275 13.0421C6.9552 15.6875 6 18.8073 6 22C6 25.1927 6.9552 28.3125 8.74275 30.9579C10.5303 33.6033 13.0684 35.6535 16.0307 36.8447C18.9929 38.0359 22.2438 38.3136 25.3652 37.6422C28.4866 36.9707 31.3357 35.3808 33.5461 33.0769C33.9831 32.6215 33.9681 31.898 33.5126 31.461C33.0572 31.024 32.3337 31.039 31.8967 31.4945C30.0021 33.4693 27.56 34.832 24.8845 35.4076C22.209 35.9831 19.4225 35.745 16.8835 34.724C14.3444 33.703 12.1689 31.9457 10.6367 29.6782C9.1045 27.4107 8.28575 24.7366 8.28575 22C8.28575 19.2634 9.1045 16.5893 10.6367 14.3218C12.1689 12.0543 14.3444 10.297 16.8835 9.27598ZM23.3347 16.3347C23.7811 15.8884 24.5047 15.8884 24.951 16.3347L29.5225 20.9062C29.9688 21.3525 29.9688 22.0761 29.5225 22.5224L24.951 27.0938C24.5047 27.5401 23.7811 27.5401 23.3347 27.0938C22.8884 26.6475 22.8884 25.9239 23.3347 25.4776L25.5267 23.2857H12.1429C11.5117 23.2857 11 22.774 11 22.1429C11 21.5117 11.5117 21 12.1429 21H26.3838L23.3347 17.951C22.8884 17.5047 22.8884 16.781 23.3347 16.3347Z"/>
                        </svg>
                        <a href="logout.php" class="block px-4 py-2 text-sm" role="menuitem" tabindex="-1" id="menu-item-6">Cerrar sesión</a>
                    </div>
                </div>
            </div>
            
        </div>
        <div class="overflow-hidden flex">
            <div id="nav" class="overflow-y-auto overflow-x-hidden scroll-smooth border-r border-gray-dark transition-all ease-in-out duration-500 bg-white w-64 h-full flex flex-col justify-between ">
                <ul class=" w-60 ml-2 list-none p-0 text-gray-text pt-4" id="modules-nav">
                    <li class="rounded-lg h-10 py-2 px-4 flex content-center items-center gap-2 cursor-pointer  transition-all ease-in-out duration-100 animate-pulse">
                        <div class="rounded-full bg-gray-300 w-6 h-6"></div>                     
                        <div class="rounded-lg w-full h-2 bg-gray-300"></div>                     
                    </li>
                    <li class="rounded-lg h-10 py-2 px-4 flex content-center items-center gap-2 cursor-pointer  transition-all ease-in-out duration-100 animate-pulse">
                        <div class="rounded-full bg-gray-300 w-6 h-6"></div>                     
                        <div class="rounded-lg w-full h-2 bg-gray-300"></div>                     
                    </li>
                    <li class="rounded-lg h-10 py-2 px-4 flex content-center items-center gap-2 cursor-pointer  transition-all ease-in-out duration-100 animate-pulse">
                        <div class="rounded-full bg-gray-300 w-6 h-6"></div>                     
                        <div class="rounded-lg w-full h-2 bg-gray-300"></div>                     
                    </li>
                </ul>
                <div class=" p-2 text-red-500 flex items-center justify-start pl-2 hover:bg-red-50 cursor-pointer font-medium" role="none">
                    <svg class="h-5 w-5" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path fill="currentColor" fill-rule="evenodd" clip-rule="evenodd" d="M16.8835 9.27598C19.4225 8.25496 22.209 8.0169 24.8845 8.59243C27.56 9.16795 30.0021 10.5307 31.8967 12.5055C32.3337 12.961 33.0572 12.976 33.5126 12.539C33.9681 12.102 33.9831 11.3785 33.5461 10.9231C31.3357 8.6192 28.4866 7.02928 25.3652 6.35783C22.2438 5.68638 18.9929 5.96412 16.0307 7.15531C13.0684 8.34649 10.5303 10.3967 8.74275 13.0421C6.9552 15.6875 6 18.8073 6 22C6 25.1927 6.9552 28.3125 8.74275 30.9579C10.5303 33.6033 13.0684 35.6535 16.0307 36.8447C18.9929 38.0359 22.2438 38.3136 25.3652 37.6422C28.4866 36.9707 31.3357 35.3808 33.5461 33.0769C33.9831 32.6215 33.9681 31.898 33.5126 31.461C33.0572 31.024 32.3337 31.039 31.8967 31.4945C30.0021 33.4693 27.56 34.832 24.8845 35.4076C22.209 35.9831 19.4225 35.745 16.8835 34.724C14.3444 33.703 12.1689 31.9457 10.6367 29.6782C9.1045 27.4107 8.28575 24.7366 8.28575 22C8.28575 19.2634 9.1045 16.5893 10.6367 14.3218C12.1689 12.0543 14.3444 10.297 16.8835 9.27598ZM23.3347 16.3347C23.7811 15.8884 24.5047 15.8884 24.951 16.3347L29.5225 20.9062C29.9688 21.3525 29.9688 22.0761 29.5225 22.5224L24.951 27.0938C24.5047 27.5401 23.7811 27.5401 23.3347 27.0938C22.8884 26.6475 22.8884 25.9239 23.3347 25.4776L25.5267 23.2857H12.1429C11.5117 23.2857 11 22.774 11 22.1429C11 21.5117 11.5117 21 12.1429 21H26.3838L23.3347 17.951C22.8884 17.5047 22.8884 16.781 23.3347 16.3347Z"/>
                    </svg>
                    <a href="logout.php" class="block px-4 py-2 text-sm" role="menuitem" tabindex="-1" id="menu-item-6">Cerrar sesión</a>
                </div>
            </div>
            <div id="modules-content" class="z-10 flex-grow bg-gray-light h-screen overflow-auto transition-all ease-in-out overflow-hidden">
                <div class="h-full w-full bg-white z-10 flex items-center justify-center hidden" id="iframe-loading">
                    <span class="animate-ping absolute inline-flex h-10 w-10 rounded-full bg-gray-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-10 w-10 bg-gray-400"></span>    
                </div>
            </div>
        </div>
    </div>
</body>
</html>
