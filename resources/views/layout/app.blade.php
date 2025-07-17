<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('title') | AI Explorer</title>

    <link rel="icon" type="image/png" href="{{ asset('favicon.ico') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/lipis/flag-icons@7.3.2/css/flag-icons.min.css" />

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,200..1000;1,200..1000&display=swap"
        rel="stylesheet">

    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet">

    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js"></script>

    <style type="text/tailwindcss">
        /* @theme {
            --color-clifford: #da373d;
        } */
    </style>

    <style>
        .object-fit-cover {
            object-fit: cover;
        }

        @font-face {
            font-family: 'ocraext';
            src: url('{{ asset('asset/fonts/ocraext.ttf') }}') format('truetype');
            font-weight: normal;
            font-style: normal;
        }

        .ocraext {
            font-family: 'ocraext', sans-serif;
        }

        .outlined-text {
            -webkit-text-stroke: 2px black;
            /* Outline thickness and color */
            color: transparent;
        }

        .nunito {
            font-family: "Nunito", sans-serif;
        }

        .poppins {
            font-family: "Poppins", sans-serif;
        }

        /* From Uiverse.io by jeremy_4982 */
        @keyframes dropAndShift {
            0% {
                transform: translate(0px, 15px);
            }

            16.67% {
                transform: translate(80px, 13px);
            }

            33.34% {
                transform: translate(40px, 10px);
            }

            50.01% {
                transform: translate(40px, -30px);
            }

            66.68% {
                transform: translate(40px, 55px);
            }

            83.35% {
                transform: translate(40px, 10px);
            }

            100% {
                transform: translate(0px, 15px);
            }
        }

        @keyframes bubbleGlint {
            0% {
                top: 3px;
                left: 4px;
                opacity: 0;
            }

            8.335% {
                top: 6px;
                left: 6px;
                opacity: 0.5;
            }

            16.67% {
                top: 3px;
                left: 4px;
                opacity: 0;
            }

            33.34% {
                top: 3px;
                left: 4px;
                opacity: 0.5;
            }

            50.01% {
                top: 3px;
                left: 4px;
                opacity: 0;
            }

            58.345% {
                top: 6px;
                left: 6px;
                opacity: 0.5;
            }

            66.68% {
                top: 3px;
                left: 4px;
                opacity: 0;
            }

            83.35% {
                top: 6px;
                left: 6px;
                opacity: 0.5;
            }

            100% {
                top: 3px;
                left: 4px;
                opacity: 0;
            }
        }

        .Strich1 {
            top: calc(50% - 25px);
            left: calc(50% - 65px);
            position: absolute;
            width: 130px;
            height: 50px;
            background: #000;
            border-radius: 25px;
            transform: rotate(45deg);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.4);
            z-index: 0;
        }

        .Strich2 {
            position: absolute;
            width: 130px;
            height: 50px;
            background: #000;
            border-radius: 25px;
            transform: rotate(-90deg);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.4);
            z-index: 0;
        }

        .bubble {
            position: absolute;
            top: 0;
            left: 15px;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background: radial-gradient(circle at 30% 30%, #ffb3c1, #e64980, #ff8787);
            animation: dropAndShift 5s ease-in-out infinite;
            z-index: 1;
        }

        .bubble1 {
            position: absolute;
            top: 0;
            width: 20px;
            height: 20px;
            background: radial-gradient(circle at 30% 30%, #edb3ff, #ac49e6, #fb87ff);
            border-radius: 50%;
            left: 8px;
            animation: dropAndShift 6s ease-in-out infinite;
            z-index: 2;
        }

        .bubble2 {
            position: absolute;
            top: 0;
            width: 20px;
            height: 20px;
            background: radial-gradient(circle at 30% 30%, #b3d8ff, #4963e6, #87a7ff);
            border-radius: 50%;
            left: 12px;
            animation: dropAndShift 4s ease-in-out infinite;
            z-index: 3;
        }

        .bubble3 {
            position: absolute;
            top: 0;
            width: 20px;
            height: 20px;
            background: radial-gradient(circle at 30% 30%, #b3ffbc, #35a32f, #75ba61);
            border-radius: 50%;
            left: 10px;
            animation: dropAndShift 7s ease-in-out infinite;
            z-index: 4;
        }

        .Strich1 .text {
            transform: rotate(-45deg);
            position: absolute;
            bottom: 0;
        }

        .loading-animation-text {
            position: absolute;
            top: calc(50% + 75px);
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 1rem;
            font-weight: bold;
            color: #000;
            text-shadow: 0 0 10px rgba(255, 255, 255, 0.8);
            z-index: 5;
        }
    </style>
</head>

<body class="">
    <div id="loading-animation">
        <div class="Strich1">
            <div class="Strich2">
                <div class="bubble"></div>
                <div class="bubble1"></div>
                <div class="bubble2"></div>
                <div class="bubble3"></div>
                <div class="bubble4"></div>
            </div>
        </div>
        <div class="loading-animation-text">
            AI Explorer
        </div>


    </div>

    <div class="main-container" style="display: none;">
        @yield('content')
    </div>




    <script>
        window.addEventListener('scroll', function() {
            const navbar = document.getElementById('main-navbar');
            const scrollY = window.scrollY;

            if (scrollY > 50) {
                navbar.classList.add('fixed', 'top-0', 'left-0', 'w-full', 'bg-white', 'shadow-md', 'z-50');
                navbar.classList.remove('bg-transparent');
            } else {
                navbar.classList.remove('fixed', 'top-0', 'left-0', 'w-full', 'bg-white', 'shadow-md', 'z-50');
                navbar.classList.add('bg-transparent');
            }
        });

        //show loading animation on page load
        document.addEventListener("DOMContentLoaded", function() {
            const loadingAnimation = document.querySelector('#loading-animation');
            loadingAnimation.style.display = 'block';

            // Hide the loading animation after 2 seconds with fade out
            setTimeout(() => {
                loadingAnimation.style.transition = 'opacity 0.6s';
                loadingAnimation.style.opacity = '0';
                setTimeout(() => {
                    loadingAnimation.style.display = 'none';
                    document.querySelector('.main-container').style.display = 'block';
                }, 500);
            }, 1000);
        });
    </script>
</body>

</html>
