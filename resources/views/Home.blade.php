<!DOCTYPE html>
@php($lang = session('lang', 'en'))
@php($isAr = $lang === 'ar')
<html lang="{{ $lang }}" dir="{{ $isAr ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trady Shop</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Swiper.js for the image slider -->
    <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css" />
    <script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <!-- Custom Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- <script src="js/shopApi.js"></script> -->

    <style>
        /* A simple style to make the body look like a mobile app screen */
        body {
            background-color: #f0f2f5;
            font-family: 'Inter', sans-serif;
        }

        .dark body {
            background-color: #111827;
        }

        .mobile-container {
            margin: 0 auto;
            background-color: #ffffff;
            min-height: 100vh;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .swiper-pagination-bullet-active {
            background-color: #f34611b9 !important;
        }

        .hero-swiper .swiper-pagination {
            bottom: 10px !important;
        }

        .text-xxs {
            font-size: 0.65rem;
            /* 10.4px */
            line-height: 1rem;
            /* 16px */
        }

        /* Class to prevent body scrolling when a modal is open */
        .no-scroll {
            overflow: hidden;
        }

        /* Hide scrollbar for clean look */
        .hide-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .hide-scrollbar {
            -ms-overflow-style: none;
            /* IE and Edge */
            scrollbar-width: none;
            /* Firefox */
        }

        .expanding-clone {
            position: fixed;
            z-index: 99999;
            border-radius: 16px;
            overflow: hidden;
            transition: all 320ms cubic-bezier(.2, .8, .2, 1);
            will-change: top, left, width, height, opacity;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.25);
            background: inherit;
        }

        @media (min-width: 768px) {
            .expanding-clone {
                border-radius: 24px;
            }
        }



        :root {
            --marquee-duration: 18s;
        }

        @keyframes marquee {
            0% {
                transform: translateX(0);
            }

            100% {
                transform: translateX(-100%);
            }
        }

        @keyframes marquee2 {
            0% {
                transform: translateX(100%);
            }

            100% {
                transform: translateX(0);
            }
        }

        .marquee,
        .marquee2 {
            position: absolute;
            top: 0;
            left: 0;
            height: 100%;
            display: flex;
            will-change: transform;
        }

        .marquee {
            animation: marquee var(--marquee-duration) linear infinite;
        }

        .marquee2 {
            animation: marquee2 var(--marquee-duration) linear infinite;
        }
    </style>
</head>

<body class="flex justify-center">

    <!--desktop navbar-->
    <nav id="side-menuDesktop"
        class="hidden md:flex md:fixed top-0 {{ $isAr ? 'right-0' : 'left-0' }} w-3/4 max-w-xs md:w-64 bg-white shadow-l z-50 transform {{ $isAr ? 'translate-x-full' : '-translate-x-full' }} md:translate-x-0 transition-transform duration-300 ease-in-out dark:bg-gray-800 {{ $isAr ? 'dark:border-l' : 'dark:border-r' }} dark:border-gray-700 flex-col flex-shrink-0">
        <div class="flex justify-between items-center p-4 border-b dark:border-gray-700">
            <h1
                class="text-3xl pl-2 font-bold bg-gradient-to-tr from-[#FFF687] to-[#F6416C] bg-clip-text text-transparent mb-1 pt-1">
                Trady
            </h1>
        </div>
        <div class="p-4 overflow-y-auto flex-grow custom-scrollbar">
            <!-- Desktop Nav Links -->
            <div class="hidden md:block mb-6">
                <h3 class="px-2 mb-2 text-xs font-semibold text-gray-400 uppercase tracking-wider">Navigation</h3>
                <ul class="space-y-1">
                    <li><a href="{{ route('customer.home') }}"
                            class="flex items-center p-2 text-rose-500 bg-rose-100 dark:bg-rose-500/20 dark:text-rose-300 rounded-lg"><i
                                data-lucide="home" class="w-5 h-5 mr-3"></i>Home</a></li>
                    <li><a href="notifications.html"
                            class="flex items-center p-2 text-gray-700 hover:bg-gray-100 rounded-lg dark:text-gray-200 dark:hover:bg-gray-700"><i
                                data-lucide="bell"
                                class="w-5 h-5 mr-3 text-gray-500 dark:text-gray-400"></i>Notifications</a></li>
                    <li><a href="orders-page.html"
                            class="flex items-center p-2 text-gray-700 hover:bg-gray-100 rounded-lg dark:text-gray-200 dark:hover:bg-gray-700"><i
                                data-lucide="clipboard-list"
                                class="w-5 h-5 mr-3 text-gray-500 dark:text-gray-400"></i>Orders</a></li>
                    <li><a href="messages.html"
                            class="flex items-center p-2 text-gray-700 hover:bg-gray-100 rounded-lg dark:text-gray-200 dark:hover:bg-gray-700"><i
                                data-lucide="message-square"
                                class="w-5 h-5 mr-3 text-gray-500 dark:text-gray-400"></i>Messages</a></li>
                    <li><a href="{{ route('shop.account') }}"
                            class="flex items-center p-2 text-gray-700 hover:bg-gray-100 rounded-lg dark:text-gray-200 dark:hover:bg-gray-700"><i
                                data-lucide="user" class="w-5 h-5 mr-3 text-gray-500 dark:text-gray-400"></i>Account</a>
                    </li>
                </ul>
            </div>
            <!-- All Screens Menu Items -->
            <div>
                <h3 class="px-2 mb-2 text-xs font-semibold text-gray-400 uppercase tracking-wider">Settings</h3>
                <ul class="space-y-1">
                    <li><a href="#"
                            class="flex items-center p-2 text-gray-700 hover:bg-gray-100 rounded-lg dark:text-gray-200 dark:hover:bg-gray-700"><i
                                data-lucide="languages"
                                class="w-5 h-5 mr-3 text-gray-500 dark:text-gray-400"></i>Language</a></li>
                    <li><a href="#"
                            class="flex items-center p-2 text-gray-700 hover:bg-gray-100 rounded-lg dark:text-gray-200 dark:hover:bg-gray-700"><i
                                data-lucide="wrench" class="w-5 h-5 mr-3 text-gray-500 dark:text-gray-400"></i>Technical
                            Support</a></li>
                    <li><a href="#"
                            class="flex items-center p-2 text-gray-700 hover:bg-gray-100 rounded-lg dark:text-gray-200 dark:hover:bg-gray-700"><i
                                data-lucide="shield" class="w-5 h-5 mr-3 text-gray-500 dark:text-gray-400"></i>Privacy
                            and
                            Policies</a>
                    </li>
                    <li class="flex items-center justify-between p-2 text-gray-700 dark:text-gray-200">
                        <div class="flex items-center"><i data-lucide="moon"
                                class="w-5 h-5 mr-3 text-gray-500 dark:text-gray-400"></i>Dark Mode</div>
                        <label for="dark-mode-toggle-desktop"
                            class="inline-flex relative items-center cursor-pointer"><input type="checkbox" value=""
                                id="dark-mode-toggle-desktop" class="dark-mode-toggle sr-only peer">
                            <div
                                class="w-11 h-6 bg-gray-200 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-rose-400">
                            </div>
                        </label>
                    </li>
                    <li><a href="#"
                            class="flex items-center p-2 text-gray-700 hover:bg-gray-100 rounded-lg dark:text-gray-200 dark:hover:bg-gray-700"><i
                                data-lucide="undo-2" class="w-5 h-5 mr-3 text-gray-500 dark:text-gray-400"></i>Return an
                            Order</a></li>
                </ul>
                <div class="mt-4 pt-4 border-t dark:border-gray-700">
                    <a href="messages.html?openChat=true"
                        class="flex items-center p-2 rounded-lg bg-gradient-to-r from-yellow-300 to-orange-400 text-white shadow-md">
                        <i data-lucide="sparkles" class="w-5 h-5 mr-3"></i>
                        <span class="font-bold">Special
                            Order</span>
                    </a>
                </div>
            </div>
        </div>
    </nav>
    <div class=" absolute md:relative top-0 {{ $isAr ? 'right-0' : 'left-0' }} w-3/4 max-w-xs md:w-64 bg-white shadow-l z-30 transform
        {{ $isAr ? 'translate-x-full' : '-translate-x-full' }} md:translate-x-0 transition-transform duration-300 ease-in-out dark:bg-gray-800 {{ $isAr ? 'dark:border-l' : 'dark:border-r' }}
        dark:border-gray-700 flex flex-col flex-shrink-0 hidden md:block"></div>


    <div class=" mobile-container w-full relative pb-24 overflow-hidden dark:bg-gray-900">

        <!-- Side Menu mobile-->
        <div id="side-menu"
            class="absolute top-0 {{ $isAr ? 'right-0 translate-x-full' : 'left-0 -translate-x-full' }} h-full w-3/4 max-w-xs bg-white shadow-xl z-50 transform transition-transform duration-300 ease-in-out dark:bg-gray-800 {{ $isAr ? 'dark:border-l' : 'dark:border-r' }} dark:border-gray-700">
            <div class="flex justify-between items-center p-4 border-b dark:border-gray-700">
                <h2 class="text-xl font-bold text-gray-800 dark:text-white">Menu</h2>
                <button id="close-menu-btn"><i data-lucide="x"
                        class="w-6 h-6 text-gray-600 dark:text-gray-300"></i></button>
            </div>
            <div class="p-4">
                <div class="mb-4">
                    <h3 class="px-2 mb-2 text-xs font-semibold text-gray-400 uppercase tracking-wider">Navigation</h3>
                    <ul class="space-y-1">
                        <li><a href="{{ route('customer.home') }}"
                                class="flex items-center p-2 text-gray-700 hover:bg-gray-100 rounded-lg dark:text-gray-200 dark:hover:bg-gray-700"><i
                                    data-lucide="home" class="w-5 h-5 mr-3 text-gray-500 dark:text-gray-400"></i>Home</a></li>
                        <li><a href="{{ route('shop.account') }}"
                                class="flex items-center p-2 text-gray-700 hover:bg-gray-100 rounded-lg dark:text-gray-200 dark:hover:bg-gray-700"><i
                                    data-lucide="user" class="w-5 h-5 mr-3 text-gray-500 dark:text-gray-400"></i>Account</a></li>
                    </ul>
                </div>

                <ul class="space-y-2">
                    <li><a href="#"
                            class="flex items-center p-2 text-gray-700 hover:bg-gray-100 rounded-lg dark:text-gray-200 dark:hover:bg-gray-700"><i
                                data-lucide="languages"
                                class="w-5 h-5 mr-3 text-gray-500 dark:text-gray-400"></i>Language</a></li>
                    <li><a href="#"
                            class="flex items-center p-2 text-gray-700 hover:bg-gray-100 rounded-lg dark:text-gray-200 dark:hover:bg-gray-700"><i
                                data-lucide="wrench" class="w-5 h-5 mr-3 text-gray-500 dark:text-gray-400"></i>Technical
                            Support</a></li>
                    <li><a href="#"
                            class="flex items-center p-2 text-gray-700 hover:bg-gray-100 rounded-lg dark:text-gray-200 dark:hover:bg-gray-700"><i
                                data-lucide="shield" class="w-5 h-5 mr-3 text-gray-500 dark:text-gray-400"></i>Privacy
                            and Policies</a></li>
                    <li class="flex items-center justify-between p-2 text-gray-700 dark:text-gray-200">
                        <div class="flex items-center">
                            <i data-lucide="moon" class="w-5 h-5 mr-3 text-gray-500 dark:text-gray-400"></i>Dark Mode
                        </div>
                        <label for="dark-mode-toggle-mobile" class="inline-flex relative items-center cursor-pointer">
                            <input type="checkbox" value="" id="dark-mode-toggle-mobile"
                                class="dark-mode-toggle sr-only peer">
                            <div
                                class="w-11 h-6 bg-gray-200 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-rose-400">
                            </div>
                        </label>
                    </li>
                    <li><a href="#"
                            class="flex items-center p-2 text-gray-700 hover:bg-gray-100 rounded-lg dark:text-gray-200 dark:hover:bg-gray-700"><i
                                data-lucide="undo-2" class="w-5 h-5 mr-3 text-gray-500 dark:text-gray-400"></i>Return an
                            Order</a></li>
                </ul>
                <a href="messages.html?openChat=true"
                    class="flex items-center p-3 mb-4 rounded-lg bg-gradient-to-r from-yellow-300 to-orange-400 text-white shadow-md">
                    <i data-lucide="sparkles" class="w-5 h-5 mr-3"></i>
                    <span class="font-bold">Special Order</span>
                </a>
            </div>
        </div>

        <!-- Overlay -->
        <div id="overlay" class="absolute inset-0 bg-black bg-opacity-40 z-40 hidden"></div>

        <!-- Country Selector Modal -->
        <div id="country-modal"
            class="absolute bottom-0 left-0 right-0 h-3/4 bg-white md:mx-[20%] rounded-t-2xl shadow-xl z-50 transform translate-y-full transition-transform duration-300 ease-in-out flex flex-col dark:bg-gray-800">
            <div class="p-4 border-b dark:border-gray-700 sticky top-0 bg-white dark:bg-gray-800 rounded-t-2xl">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-bold text-gray-800 dark:text-white">Select Country</h2>
                    <button id="close-country-modal-btn"><i data-lucide="x"
                            class="w-6 h-6 text-gray-600 dark:text-gray-300"></i></button>
                </div>
                <!-- Country Search Input -->
                <div class="relative">
                    <input type="text" id="country-search-input" placeholder="Search for a country..."
                        class="w-full py-2 pl-10 pr-4 text-gray-700 bg-gray-100 border-2 border-transparent rounded-lg focus:outline-none focus:border-red-500 focus:bg-white dark:bg-gray-700 dark:text-gray-200 dark:placeholder-gray-400">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3">
                        <i data-lucide="search" class="w-5 h-5 text-gray-400"></i>
                    </div>
                </div>
            </div>
            <div class="p-4 flex-grow overflow-y-auto">
                <ul id="country-list" class="space-y-1">
                    <li><a href="#"
                            class="country-item flex items-center p-3 text-gray-700 hover:bg-gray-100 rounded-lg dark:text-gray-300 dark:hover:bg-gray-700"><span
                                class="text-2xl mr-4"><img class="h-8 w-8"
                                    src="https://img.icons8.com/color/48/egypt-circular.png"
                                    alt="egypt-circular" /></span> Egypt</a></li>
                    <li><a href="#"
                            class="country-item flex items-center p-3 text-gray-700 hover:bg-gray-100 rounded-lg dark:text-gray-300 dark:hover:bg-gray-700"><span
                                class="text-2xl mr-4"><img class="h-8 w-8"
                                    src="https://img.icons8.com/color/48/saudi-arabia-circular.png"
                                    alt="saudi-arabia-circular" /></span> Saudi Arabia</a></li>
                    <li><a href="#"
                            class="country-item flex items-center p-3 text-gray-700 hover:bg-gray-100 rounded-lg dark:text-gray-300 dark:hover:bg-gray-700"><span
                                class="text-2xl mr-4"><img class="h-8 w-8"
                                    src="https://img.icons8.com/color/48/china-circular.png"
                                    alt="china-circular" /></span> China</a></li>
                    <li><a href="#"
                            class="country-item flex items-center p-3 text-gray-700 hover:bg-gray-100 rounded-lg dark:text-gray-300 dark:hover:bg-gray-700"><span
                                class="text-2xl mr-4"><img class="h-8 w-8"
                                    src="https://img.icons8.com/color/48/usa-circular.png"
                                    alt="china-circular" /></span> United States</a></li>
                    <li><a href="#"
                            class="country-item flex items-center p-3 text-gray-700 hover:bg-gray-100 rounded-lg dark:text-gray-300 dark:hover:bg-gray-700"><span
                                class="text-2xl mr-4"><img class="h-8 w-8"
                                    src="https://img.icons8.com/color/48/united-arab-emirates-circular.png"
                                    alt="china-circular" /></span> United Arab Emirates</a></li>
                </ul>
            </div>
        </div>

        <!-- Header Section -->
        <header
            class="flex items-center justify-between p-4 bg-white border-b border-gray-200 top-0 z-30 dark:bg-gray-800 dark:border-gray-700">
            <div class="flex items-center gap-4 md:hidden">
                <button id="menu-btn"><i data-lucide="menu"
                        class="w-6 h-6 text-gray-700 dark:text-gray-300"></i></button>
                <h1
                    class="text-2xl font-bold bg-gradient-to-tr from-[#FFF687] to-[#F6416C] bg-clip-text text-transparent">
                    Trady
                </h1>
            </div>


            <!-- Search Bar Section for desktop -->
            <div class="relative flex items-center w-[90%] hidden md:flex">
                <input type="text" placeholder="Search..."
                    class="w-full py-2 pl-10 pr-4 text-gray-700 bg-gray-100 border-2 border-transparent rounded-lg focus:outline-none focus:border-rose-300 focus:bg-white dark:bg-gray-700 dark:text-gray-200 dark:placeholder-gray-400">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3">
                    <i data-lucide="search" class="w-5 h-5 text-gray-400"></i>
                </div>
                <button id="desktop-country-btn" class="p-2 ml-2 bg-gray-100 rounded-lg dark:bg-gray-700">
                    <i data-lucide="globe-2" class="w-7 h-7 text-gray-600 dark:text-white"></i>
                </button>
            </div>


            <div class="flex items-center gap-4 md:mr-4">
                <details class="relative">
                    <summary class="list-none cursor-pointer text-gray-600 dark:text-gray-200 hover:text-rose-500">
                        <i data-lucide="languages" class="w-6 h-6"></i>
                    </summary>
                    <div class="absolute {{ $isAr ? 'left-0' : 'right-0' }} mt-2 w-40 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-lg p-2 z-50">
                        <form method="POST" action="{{ route('shop.lang.set') }}">
                            @csrf
                            <input type="hidden" name="lang" value="en" />
                            <button type="submit" class="w-full text-left px-3 py-2 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/40 {{ !$isAr ? 'font-semibold text-rose-500' : 'text-gray-700 dark:text-gray-200' }}">English</button>
                        </form>
                        <form method="POST" action="{{ route('shop.lang.set') }}">
                            @csrf
                            <input type="hidden" name="lang" value="ar" />
                            <button type="submit" class="w-full text-left px-3 py-2 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/40 {{ $isAr ? 'font-semibold text-rose-500' : 'text-gray-700 dark:text-gray-200' }}">العربية</button>
                        </form>
                    </div>
                </details>
                <i data-lucide="shopping-cart" class="w-6 h-6 text-rose-500 dark:text-rose-400 md:hover:cursor-pointer"
                    onclick="window.location.href='cart-page.html'"></i>
                <span id="header-flag" class="text-2xl"><img width="30" height="30"
                        src="https://img.icons8.com/color/48/egypt-circular.png" alt="egypt-circular" /></span>
            </div>
        </header>

        <main class="p-4 md:p-8">
            <!-- Search Bar Section Mobile -->
            <div class="relative flex items-center mb-2 md:hidden">
                <input type="text" placeholder="Search..."
                    class="w-full py-2 pl-10 pr-4 text-gray-700 bg-gray-100 border-2 border-transparent rounded-lg focus:outline-none focus:border-rose-300 focus:bg-white dark:bg-gray-700 dark:text-gray-200 dark:placeholder-gray-400">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3">
                    <i data-lucide="search" class="w-5 h-5 text-gray-400"></i>
                </div>
                <button id="mobile-country-btn"
                    class="p-2 w-16 ml-2 text-center bg-gray-100 rounded-lg dark:bg-gray-700">
                    <i data-lucide="globe-2" class="w-7 h-7 ml-1 text-gray-600 dark:text-gray-300"></i>
                </button>
            </div>

            <!-- Offer Bar -->
            <div
                class="relative w-full overflow-hidden rounded-lg bg-rose-300 dark:bg-rose-500/50 dark:text-white h-8 mb-4 mt-4">
                <!-- Gradient fade left -->
                <div
                    class="pointer-events-none absolute left-0 top-0 h-full w-12 bg-gradient-to-r from-orange-200 dark:from-orange-400/50 to-transparent z-10">
                </div>
                <!-- Gradient fade right -->
                <div
                    class="pointer-events-none absolute right-0 top-0 h-full w-12 bg-gradient-to-l from-orange-200 dark:from-orange-400/50 to-transparent z-10">
                </div>

                <!-- First track -->
                <ul
                    class="marquee flex min-w-max items-center gap-8 md:gap-32 px-3 text-sm font-semibold text-white dark:text-rose-200">
                    <li> Buy 2 for 50% off now</li>
                    <li> Get 10 points free with every order</li>
                    <li> Free delivery this weekend</li>
                    <li> New: Daily recharge card available</li>
                </ul>

                <!-- Second track -->
                <ul aria-hidden="true"
                    class="marquee2 flex min-w-max items-center gap-8 md:gap-32 px-3 text-sm font-semibold text-white dark:text-rose-200">
                    <li> Buy 2 for 50% off now</li>
                    <li> Get 10 points free with every order</li>
                    <li> Free delivery this weekend</li>
                    <li> New: Daily recharge card available</li>
                </ul>
            </div>

            <!-- Hero Image Swiper -->
            <div class="relative h-50 mb-6 md:h-[5rem]">
                <div class="swiper-container hero-swiper h-full overflow-hidden rounded-xl">
                    <div class="swiper-wrapper">
                        <!-- Slides -->
                        <div
                            class="swiper-slide flex items-center justify-center text-3xl font-bold text-gray-500 bg-gray-300 dark:bg-gray-700 dark:text-gray-400">
                            AD 1</div>
                        <div
                            class="swiper-slide flex items-center justify-center text-3xl font-bold text-gray-500 bg-gray-300 dark:bg-gray-700 dark:text-gray-400">
                            AD 2</div>
                        <div
                            class="swiper-slide flex items-center justify-center text-3xl font-bold text-gray-500 bg-gray-300 dark:bg-gray-700 dark:text-gray-400">
                            AD 3</div>
                    </div>
                    <!-- Add Pagination -->
                    <div class="swiper-pagination"></div>
                </div>
            </div>

            <!-- Categories Section -->
            <div class="mb-6 overflow-hidden">
                <div class="swiper-container categories-swiper">
                    <div class="swiper-wrapper">
                        @foreach(($categories ?? []) as $cat)
                            <div class="swiper-slide flex flex-col items-center gap-2 text-center w-20">
                                <a href="{{ route('shop.categories.show', $cat->slug) }}">
                                    <div
                                        class="flex items-center justify-center w-16 h-16 md:w-20 md:h-20 rounded-full dark:bg-gray-800 overflow-hidden"
                                        style="background-color: {{ $cat->bg_color ?? '#f3f4f6' }};">
                                        @if(!empty($cat->image))
                                            <img src="{{ asset('storage/' . $cat->image) }}" alt="category" class="w-full h-full object-cover" />
                                        @else
                                            <i data-lucide="{{ $cat->icon ?? 'grid-2x2' }}" class="w-8 h-8 text-gray-700 dark:text-gray-200"></i>
                                        @endif
                                    </div>
                                    <span class="text-xs md:text-sm font-medium text-gray-700 dark:text-gray-300">{{ $isAr ? ($cat->name_ar ?? $cat->name_en) : ($cat->name_en ?? $cat->name_ar) }}</span>
                                </a>
                            </div>
                        @endforeach

                        <!-- repeat for Screens, Keyboards, Laptops, Tablets -->
                    </div>
                </div>
            </div>


            <!-- Best Sellers Section -->
            <div class="mb-8 overflow-hidden">
                <h2
                    class="mb-4 text-2xl font-bold bg-gradient-to-tr from-[#FFF687] to-[#F6416C] to-[60%] bg-clip-text text-transparent">
                    Best sellers</h2>
                <div class="swiper-container best-sellers-swiper">
                    <div class="swiper-wrapper">
                        @foreach(($bestSellers ?? []) as $product)
                            @php($factory = ($product->suppliers ?? collect())->firstWhere('type', 'factory'))
                            @php($supplier = ($product->suppliers ?? collect())->firstWhere('type', 'supplier'))
                            @php($colorsStr = collect((array) ($product->colors ?? []))->filter(fn($v) => trim((string) $v) !== '')->implode(','))
                            @php($sizesStr = collect((array) ($product->sizes ?? []))->filter(fn($v) => trim((string) $v) !== '')->implode(','))
                            <div class="swiper-slide">
                                <div class="p-3 md:p-5 bg-gray-50 rounded-xl dark:bg-gray-800 md:hover:cursor-pointer" data-shop-product
                                    data-product-id="{{ $product->id }}"
                                    data-name-en="{{ $product->name_en ?? $product->name }}"
                                    data-name-ar="{{ $product->name_ar ?? $product->name }}"
                                    data-image="{{ !empty($product->image) ? asset('storage/' . $product->image) : asset('apple-touch-icon.png') }}"
                                    data-description="{{ $product->description ?? '' }}"
                                    data-description-ar="{{ $product->description_ar ?? '' }}"
                                    data-description-en="{{ $product->description_en ?? '' }}"
                                    data-color="{{ $product->color ?? '' }}"
                                    data-size="{{ $product->size ?? '' }}"
                                    data-colors="{{ $colorsStr }}"
                                    data-sizes="{{ $sizesStr }}"
                                    data-factory-name="{{ $factory->name ?? '' }}"
                                    data-factory-price="{{ (string) ($factory->pivot->price ?? '') }}"
                                    data-supplier-name="{{ $supplier->name ?? '' }}"
                                    data-supplier-price="{{ (string) ($supplier->pivot->price ?? '') }}">
                                        <div class="flex items-center justify-center h-24 md:h-32 mb-2 font-semibold text-gray-500 bg-gray-200 rounded-lg dark:bg-gray-700 dark:text-gray-400 overflow-hidden">
                                            @if(!empty($product->image))
                                                <img src="{{ asset('storage/' . $product->image) }}" alt="product" class="w-full h-full object-cover" />
                                            @else
                                                <img src="{{ asset('apple-touch-icon.png') }}" alt="default" class="w-full h-full object-cover" />
                                            @endif
                                        </div>
                                        <h3 class="mb-1 font-semibold text-gray-800 truncate dark:text-white text-sm md:text-lg">
                                            {{ $isAr ? ($product->name_ar ?? $product->name) : ($product->name_en ?? $product->name) }}
                                        </h3>
                                    <div class="flex items-center mb-2">
                                        <i data-lucide="star" class="w-4 h-4 md:w-5 md:h-5 text-yellow-500 fill-current"></i>
                                        <i data-lucide="star" class="w-4 h-4 md:w-5 md:h-5 text-yellow-500 fill-current"></i>
                                        <i data-lucide="star" class="w-4 h-4 md:w-5 md:h-5 text-yellow-500 fill-current"></i>
                                        <i data-lucide="star" class="w-4 h-4 md:w-5 md:h-5 text-gray-300 fill-current dark:text-gray-600"></i>
                                        <i data-lucide="star" class="w-4 h-4 md:w-5 md:h-5 text-gray-300 fill-current dark:text-gray-600"></i>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Featured Products Section -->
            <div class="overflow-hidden">
                <h2
                    class="mb-4 text-2xl font-bold bg-gradient-to-tr from-[#FFF687] to-[#F6416C] to-[60%] bg-clip-text text-transparent">
                    Featured Products</h2>
                <div class="swiper-container featured-products-swiper">
                    <div class="swiper-wrapper">
                        @foreach(($featuredProducts ?? []) as $product)
                            @php($factory = ($product->suppliers ?? collect())->firstWhere('type', 'factory'))
                            @php($supplier = ($product->suppliers ?? collect())->firstWhere('type', 'supplier'))
                            @php($colorsStr = collect((array) ($product->colors ?? []))->filter(fn($v) => trim((string) $v) !== '')->implode(','))
                            @php($sizesStr = collect((array) ($product->sizes ?? []))->filter(fn($v) => trim((string) $v) !== '')->implode(','))
                            <div class="swiper-slide">
                                <div class="p-3 md:p-5 bg-gray-50 rounded-xl dark:bg-gray-800 md:hover:cursor-pointer" data-shop-product
                                    data-product-id="{{ $product->id }}"
                                    data-name-en="{{ $product->name_en ?? $product->name }}"
                                    data-name-ar="{{ $product->name_ar ?? $product->name }}"
                                    data-image="{{ !empty($product->image) ? asset('storage/' . $product->image) : asset('apple-touch-icon.png') }}"
                                    data-description="{{ $product->description ?? '' }}"
                                    data-description-ar="{{ $product->description_ar ?? '' }}"
                                    data-description-en="{{ $product->description_en ?? '' }}"
                                    data-color="{{ $product->color ?? '' }}"
                                    data-size="{{ $product->size ?? '' }}"
                                    data-colors="{{ $colorsStr }}"
                                    data-sizes="{{ $sizesStr }}"
                                    data-factory-name="{{ $factory->name ?? '' }}"
                                    data-factory-price="{{ (string) ($factory->pivot->price ?? '') }}"
                                    data-supplier-name="{{ $supplier->name ?? '' }}"
                                    data-supplier-price="{{ (string) ($supplier->pivot->price ?? '') }}">
                                    <div class="flex items-center justify-center h-24 md:h-32 mb-2 font-semibold text-gray-500 bg-gray-200 rounded-lg dark:bg-gray-700 dark:text-gray-400 overflow-hidden">
                                        @if(!empty($product->image))
                                            <img src="{{ asset('storage/' . $product->image) }}" alt="product" class="w-full h-full object-cover" />
                                        @else
                                            <img src="{{ asset('apple-touch-icon.png') }}" alt="default" class="w-full h-full object-cover" />
                                        @endif
                                    </div>
                                    <h3 class="mb-1 font-semibold text-gray-800 truncate dark:text-white text-sm md:text-lg">
                                        {{ $isAr ? ($product->name_ar ?? $product->name) : ($product->name_en ?? $product->name) }}
                                    </h3>
                                    <div class="flex items-center mb-2">
                                        <i data-lucide="star" class="w-4 h-4 md:w-5 md:h-5 text-yellow-500 fill-current"></i>
                                        <i data-lucide="star" class="w-4 h-4 md:w-5 md:h-5 text-yellow-500 fill-current"></i>
                                        <i data-lucide="star" class="w-4 h-4 md:w-5 md:h-5 text-yellow-500 fill-current"></i>
                                        <i data-lucide="star" class="w-4 h-4 md:w-5 md:h-5 text-gray-300 fill-current dark:text-gray-600"></i>
                                        <i data-lucide="star" class="w-4 h-4 md:w-5 md:h-5 text-gray-300 fill-current dark:text-gray-600"></i>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Ordering Options Modal -->
            <div id="ordering-options-modal"
                class="fixed inset-0 bg-black bg-opacity-20 z-50 hidden flex items-center justify-center p-4">
                <!-- FIX: Added responsive max-width -->
                <div
                    class="w-full max-w-sm md:max-w-md bg-white rounded-2xl shadow-2xl p-6 dark:bg-gray-800 transition-all duration-300">

                    <!-- Modal Header -->
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-xl font-bold text-gray-800 dark:text-white">Choose Ordering Option</h2>
                        <button id="close-ordering-options" class="text-gray-500 hover:text-red-500 transition-colors">
                            <i data-lucide="x" class="w-6 h-6"></i>
                        </button>
                    </div>

                    <!-- Options -->
                    <div class="space-y-4">
                        <!-- Option 1 -->
                        <div
                            class="flex items-center justify-between p-4 bg-gray-100 rounded-xl dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors cursor-pointer">
                            <div class="flex items-center gap-3">
                                <i data-lucide="factory" class="w-6 h-6 text-rose-500"></i>
                                <span class="font-semibold text-gray-800 dark:text-white">Order from Factory</span>
                            </div>
                            <button
                                class="addtocartbtn px-3 py-2 text-sm font-semibold text-white bg-gradient-to-r from-[#F6416C] to-orange-400 rounded-lg shadow hover:opacity-90">Add</button>
                        </div>

                        <!-- Option 2 -->
                        <div
                            class="flex items-center justify-between p-4 bg-gray-100 rounded-xl dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors cursor-pointer">
                            <div class="flex items-center gap-3">
                                <i data-lucide="truck" class="w-6 h-6 text-rose-500"></i>
                                <span class="font-semibold text-gray-800 dark:text-white">Order from Supplier</span>
                            </div>
                            <button
                                class="addtocartbtn px-3 py-2 text-sm font-semibold text-white bg-gradient-to-r from-[#F6416C] to-orange-400 rounded-lg shadow hover:opacity-90">Add</button>
                        </div>

                        <!-- Option 3 -->
                        <div
                            class="flex items-center justify-between p-4 bg-gray-100 rounded-xl dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors cursor-pointer">
                            <div class="flex items-center gap-3">
                                <i data-lucide="sparkles" class="w-6 h-6 text-rose-500"></i>
                                <span class="font-semibold text-gray-800 dark:text-white">Special Order</span>
                            </div>
                            <button
                                class="addtocartbtn px-3 py-2 text-sm font-semibold text-white bg-gradient-to-r from-[#F6416C] to-orange-400 rounded-lg shadow hover:opacity-90">Add</button>
                        </div>
                    </div>
                </div>
            </div>


            <!-- Toast Notification -->
            <div id="toast"
                class="fixed bottom-20 left-1/2 -translate-x-1/2 bg-gradient-to-r from-[#F6416C] to-orange-300 text-white text-sm font-semibold px-4 py-2 rounded-full opacity-0 pointer-events-none transition-opacity duration-300 z-50">
                Added to cart ✅
            </div>

            <div id="home-product-modal-overlay" class="fixed inset-0 bg-black/40 z-50 hidden"></div>
            <div id="home-product-modal" class="fixed inset-0 z-[60] hidden items-center justify-center p-4">
                <div class="w-full max-w-md bg-white dark:bg-gray-800 rounded-2xl shadow-2xl overflow-hidden">
                    <div class="flex items-center justify-between px-4 py-3 border-b border-gray-200 dark:border-gray-700">
                        <div class="text-base font-bold text-gray-900 dark:text-white" id="home-product-modal-title"></div>
                        <button id="home-product-modal-close" class="text-gray-500 hover:text-rose-500">
                            <i data-lucide="x" class="w-6 h-6"></i>
                        </button>
                    </div>

                    <div class="p-4">
                        <div class="w-full h-44 rounded-xl overflow-hidden bg-gray-100 dark:bg-gray-700 border border-gray-200 dark:border-gray-700">
                            <img id="home-product-modal-image" src="" alt="product" class="w-full h-full object-cover" />
                        </div>

                        <div class="mt-4 space-y-2 text-sm text-gray-700 dark:text-gray-200">
                            <div id="home-product-modal-factory" class="hidden"></div>
                            <div id="home-product-modal-supplier" class="hidden"></div>
                            <div id="home-product-modal-colors" class="hidden"></div>
                            <div id="home-product-modal-sizes" class="hidden"></div>
                        </div>

                        <div class="mt-5 flex items-center justify-end gap-3">
                            <a id="home-product-modal-details" href="#" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-gradient-to-r from-[#F6416C] to-orange-400 text-white font-semibold hover:opacity-90">
                                <span>{{ $isAr ? 'المزيد من التفاصيل' : 'More details' }}</span>
                                <i data-lucide="arrow-up-right" class="w-5 h-5"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

        </main>

        <!-- Bottom Navigation -->
        <footer class="hidden"></footer>
    </div>
    <script>
        // --- Dark Mode Logic ---
        const darkModeToggles = document.querySelectorAll('.dark-mode-toggle');
        const html = document.documentElement;

        // Function to update the state of all toggles
        function updateToggles(isChecked) {
            darkModeToggles.forEach(toggle => {
                toggle.checked = isChecked;
            });
        }

        // Check for saved dark mode preference on page load
        if (localStorage.getItem('darkMode') === 'enabled') {
            html.classList.add('dark');
            updateToggles(true);
        } else {
            updateToggles(false);
        }

        // Add event listeners to all toggles
        darkModeToggles.forEach(toggle => {
            toggle.addEventListener('change', () => {
                if (toggle.checked) {
                    html.classList.add('dark');
                    localStorage.setItem('darkMode', 'enabled');
                    updateToggles(true);
                } else {
                    html.classList.remove('dark');
                    localStorage.setItem('darkMode', 'disabled');
                    updateToggles(false);
                }
            });
        });


        // Initialize Lucide Icons
        lucide.createIcons();

        // Initialize Hero Swiper
        var heroSwiper = new Swiper('.hero-swiper', {
            loop: true,
            autoplay: {
                delay: 2000,
                disableOnInteraction: false,
            },
            pagination: {
                el: '.hero-swiper .swiper-pagination',
                clickable: true,
            },
        });

        // Initialize Best Sellers Swiper
        var bestSellersSwiper = new Swiper('.best-sellers-swiper', {
            slidesPerView: 2.2,
            spaceBetween: 16,
            freeMode: true,
            breakpoints: {
                640: {
                    slidesPerView: 2.7, // when ≥640px
                },
                768: {
                    slidesPerView: 3.2,   // when ≥768px
                },
                1024: {
                    slidesPerView: 4.7,   // when ≥1024px
                },
            },
        });

        // Initialize Featured Products Swiper
        var featuredSwiper = new Swiper('.featured-products-swiper', {
            slidesPerView: 2.2,
            spaceBetween: 16,
            freeMode: true,
            breakpoints: {
                640: {
                    slidesPerView: 2.7, // when ≥640px
                },
                768: {
                    slidesPerView: 3.2,   // when ≥768px
                },
                1024: {
                    slidesPerView: 4.7,   // when ≥1024px
                },
            },
        });

        new Swiper('.categories-swiper', {
            slidesPerView: 4.6, // show ~3 and a half on mobile
            spaceBetween: 16,
            freeMode: true,
            breakpoints: {
                640: { slidesPerView: 4.8 },
                768: { slidesPerView: 5 },
                1024: { slidesPerView: 6 },
                1280: { slidesPerView: 12 },
            },
        });


        // --- Popups & Country Selector Logic ---

        // --- Elements ---
        const menuBtn = document.getElementById('menu-btn');
        const sideMenu = document.getElementById('side-menu');
        const overlay = document.getElementById('overlay');
        const closeMenuBtn = document.getElementById('close-menu-btn');

        const homeProductModalOverlay = document.getElementById('home-product-modal-overlay');
        const homeProductModal = document.getElementById('home-product-modal');
        const homeProductModalClose = document.getElementById('home-product-modal-close');
        const homeProductModalTitle = document.getElementById('home-product-modal-title');
        const homeProductModalImage = document.getElementById('home-product-modal-image');
        const homeProductModalFactory = document.getElementById('home-product-modal-factory');
        const homeProductModalSupplier = document.getElementById('home-product-modal-supplier');
        const homeProductModalColors = document.getElementById('home-product-modal-colors');
        const homeProductModalSizes = document.getElementById('home-product-modal-sizes');
        const homeProductModalDetails = document.getElementById('home-product-modal-details');

        // --- Updated Country Selector Elements ---
        // Make sure you've added these IDs to your buttons in the HTML
        const mobileCountryBtn = document.getElementById('mobile-country-btn');
        const desktopCountryBtn = document.getElementById('desktop-country-btn');

        const countryModal = document.getElementById('country-modal');
        const closeCountryModalBtn = document.getElementById('close-country-modal-btn');
        const countryItems = document.querySelectorAll('.country-item');
        const headerFlag = document.getElementById('header-flag');
        const countrySearchInput = document.getElementById('country-search-input');


        // --- Functions for Modals & Overlays ---
        function openMenu() {
            if (document.documentElement.dir === 'rtl') {
                sideMenu.classList.remove('translate-x-full');
            } else {
                sideMenu.classList.remove('-translate-x-full');
            }
            overlay.classList.remove('hidden');
            document.body.classList.add('no-scroll');
        }

        function closeMenu() {
            if (document.documentElement.dir === 'rtl') {
                sideMenu.classList.add('translate-x-full');
            } else {
                sideMenu.classList.add('-translate-x-full');
            }
            // Only hide overlay if the country modal is also closed
            if (countryModal.classList.contains('translate-y-full')) {
                overlay.classList.add('hidden');
                document.body.classList.remove('no-scroll');
            }
        }

        function openCountryModal() {
            countryModal.classList.remove('translate-y-full');
            overlay.classList.remove('hidden');
            document.body.classList.add('no-scroll');
        }

        function closeCountryModal() {
            countryModal.classList.add('translate-y-full');
            // Only hide overlay if the side menu is also closed
            const menuClosed = document.documentElement.dir === 'rtl'
                ? sideMenu.classList.contains('translate-x-full')
                : sideMenu.classList.contains('-translate-x-full');
            if (menuClosed) {
                overlay.classList.add('hidden');
                document.body.classList.remove('no-scroll');
            }
        }

        function openHomeProductModal(el) {
            if (!homeProductModal || !homeProductModalOverlay) return;

            const isRtl = document.documentElement.dir === 'rtl';
            const id = el.dataset.productId;
            const name = isRtl ? (el.dataset.nameAr || el.dataset.nameEn || '') : (el.dataset.nameEn || el.dataset.nameAr || '');
            const image = el.dataset.image || '';

            const factoryName = el.dataset.factoryName || '';
            const factoryPrice = el.dataset.factoryPrice || '';
            const supplierName = el.dataset.supplierName || '';
            const supplierPrice = el.dataset.supplierPrice || '';

            const colorsRaw = el.dataset.colors || el.dataset.color || '';
            const sizesRaw = el.dataset.sizes || el.dataset.size || '';

            if (homeProductModalTitle) homeProductModalTitle.textContent = name;
            if (homeProductModalImage) homeProductModalImage.src = image;

            const factoryLabel = isRtl ? 'مصنع' : 'Factory';
            const supplierLabel = isRtl ? 'مورد' : 'Supplier';

            if (homeProductModalFactory) {
                if (factoryName) {
                    homeProductModalFactory.classList.remove('hidden');
                    homeProductModalFactory.textContent = `${factoryLabel}: ${factoryName}${factoryPrice ? ' - ' + factoryPrice + ' EGP' : ''}`;
                } else {
                    homeProductModalFactory.classList.add('hidden');
                    homeProductModalFactory.textContent = '';
                }
            }

            if (homeProductModalSupplier) {
                if (supplierName) {
                    homeProductModalSupplier.classList.remove('hidden');
                    homeProductModalSupplier.textContent = `${supplierLabel}: ${supplierName}${supplierPrice ? ' - ' + supplierPrice + ' EGP' : ''}`;
                } else {
                    homeProductModalSupplier.classList.add('hidden');
                    homeProductModalSupplier.textContent = '';
                }
            }

            const showInlineList = function (targetEl, raw, labelAr, labelEn) {
                if (!targetEl) return;
                const isRtl = document.documentElement.dir === 'rtl';
                const list = String(raw || '')
                    .split(/[,;|]/)
                    .map(x => x.trim())
                    .filter(Boolean);
                if (!list.length) {
                    targetEl.classList.add('hidden');
                    targetEl.textContent = '';
                    return;
                }
                const label = isRtl ? labelAr : labelEn;
                targetEl.classList.remove('hidden');
                targetEl.textContent = `${label}: ${list.join(' / ')}`;
            };

            showInlineList(homeProductModalColors, colorsRaw, 'الألوان', 'Colors');
            showInlineList(homeProductModalSizes, sizesRaw, 'المقاسات', 'Sizes');

            if (homeProductModalDetails) {
                const base = "{{ url('/products') }}";
                homeProductModalDetails.href = `${base}/${id}`;
            }

            homeProductModalOverlay.classList.remove('hidden');
            homeProductModal.classList.remove('hidden');
            homeProductModal.classList.add('flex');
            document.body.classList.add('no-scroll');
        }

        function closeHomeProductModal() {
            if (!homeProductModal || !homeProductModalOverlay) return;
            homeProductModalOverlay.classList.add('hidden');
            homeProductModal.classList.add('hidden');
            homeProductModal.classList.remove('flex');
            document.body.classList.remove('no-scroll');
        }

        document.querySelectorAll('[data-shop-product]').forEach((el) => {
            el.addEventListener('click', (e) => {
                e.preventDefault();
                openHomeProductModal(el);
            });
        });

        if (homeProductModalOverlay) homeProductModalOverlay.addEventListener('click', closeHomeProductModal);
        if (homeProductModalClose) homeProductModalClose.addEventListener('click', closeHomeProductModal);

        // --- Event Listeners ---

        // Side Menu
        if (menuBtn) menuBtn.addEventListener('click', openMenu);
        if (closeMenuBtn) closeMenuBtn.addEventListener('click', closeMenu);

        // Country Modal Buttons
        if (mobileCountryBtn) mobileCountryBtn.addEventListener('click', openCountryModal);
        if (desktopCountryBtn) desktopCountryBtn.addEventListener('click', openCountryModal);
        if (closeCountryModalBtn) closeCountryModalBtn.addEventListener('click', closeCountryModal);

        // Overlay to close any open modal
        if (overlay) {
            overlay.addEventListener('click', () => {
                closeMenu();
                closeCountryModal();
            });
        }

        // Logic to handle selecting a country
        countryItems.forEach(item => {
            item.addEventListener('click', (e) => {
                e.preventDefault();

                const flagImg = item.querySelector('img');

                // This handles items that have an <img> tag for the flag.
                if (flagImg) {
                    const flagSrc = flagImg.getAttribute('src');
                    const flagAlt = flagImg.getAttribute('alt');

                    // Update the flag icon on both mobile and desktop buttons
                    mobileCountryBtn.innerHTML = `<img class="h-7 w-7 rounded-full ml-1" src="${flagSrc}" alt="${flagAlt}">`;
                    desktopCountryBtn.innerHTML = `<img class="h-7 w-7 rounded-full" src="${flagSrc}" alt="${flagAlt}">`;

                } else {
                    // This part handles the flags that are just text emojis (like 🇺🇸)
                    const flagEmoji = item.querySelector('span').textContent;


                    // Update the buttons with the emoji
                    mobileCountryBtn.innerHTML = `<span class="text-xl">${flagEmoji}</span>`;
                    desktopCountryBtn.innerHTML = `<span class="text-2xl">${flagEmoji}</span>`;
                }

                closeCountryModal();
            });
        });

        // Country Search Filter Logic
        if (countrySearchInput) {
            countrySearchInput.addEventListener('input', (e) => {
                const searchTerm = e.target.value.toLowerCase();
                const allCountries = document.querySelectorAll('#country-list .country-item');

                allCountries.forEach(item => {
                    // .textContent gets all text, including the country name and the emoji/alt text
                    const countryName = item.textContent.toLowerCase();
                    if (countryName.includes(searchTerm)) {
                        item.style.display = 'flex'; // Show matching items
                    } else {
                        item.style.display = 'none'; // Hide non-matching items
                    }
                });
            });
        }

        //ordering options 
        const orderBtns = document.querySelectorAll('.orderbtn');
        const orderingOptionsModal = document.getElementById('ordering-options-modal');
        const closeOrderingOptionsBtn = document.getElementById('close-ordering-options');
        const addToCartBtns = document.querySelectorAll('.addtocartbtn');
        const toast = document.getElementById('toast');
        let lastClickedCard = null;

        function openAnimatedModal(card) {
            lastClickedCard = card;

            const modalContent = orderingOptionsModal.querySelector(':scope > div');

            // 1. Temporarily show modal to measure it
            orderingOptionsModal.classList.remove("hidden");
            orderingOptionsModal.style.visibility = "hidden"; // hide visually
            const modalRect = modalContent.getBoundingClientRect();
            orderingOptionsModal.classList.add("hidden");
            orderingOptionsModal.style.visibility = "";

            // 2. Create clone starting at card rect
            const cardRect = card.getBoundingClientRect();
            const clone = modalContent.cloneNode(true);
            Object.assign(clone.style, {
                position: "fixed",
                left: cardRect.left + "px",
                top: cardRect.top + "px",
                width: cardRect.width + "px",
                height: cardRect.height + "px",
                margin: "0",
                zIndex: "99999",
                borderRadius: "16px",
                overflow: "hidden",
                transition: "all 320ms cubic-bezier(.2,.8,.2,1)"
            });
            document.body.appendChild(clone);

            // 3. Animate to modal’s real position
            requestAnimationFrame(() => {
                clone.style.left = modalRect.left + "px";
                clone.style.top = modalRect.top + "px";
                clone.style.width = modalRect.width + "px";
                clone.style.height = modalRect.height + "px";
            });

            clone.addEventListener("transitionend", () => {
                clone.remove();
                orderingOptionsModal.classList.remove("hidden");
                document.body.classList.add("no-scroll");
            }, { once: true });
        }


        function closeAnimatedModal() {
            orderingOptionsModal.classList.add("hidden");
            document.body.classList.remove("no-scroll");
            lastClickedCard = null;
        }


        orderBtns.forEach(btn => {
            btn.addEventListener('click', () => openAnimatedModal(btn));
        });

        if (closeOrderingOptionsBtn) closeOrderingOptionsBtn.addEventListener('click', closeAnimatedModal);
        if (orderingOptionsModal) {
            orderingOptionsModal.addEventListener('click', (e) => { if (e.target === orderingOptionsModal) closeAnimatedModal(); });
        }

        addToCartBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                orderingOptionsModal.classList.add('hidden');
                document.body.classList.remove('no-scroll');
                if (toast) {
                    toast.classList.remove('opacity-0', 'pointer-events-none');
                    toast.classList.add('opacity-100');
                    setTimeout(() => {
                        toast.classList.remove('opacity-100');
                        toast.classList.add('opacity-0', 'pointer-events-none');
                    }, 2000);
                }
            });
        });


        // Add to cart buttons
        addToCartBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                orderingOptionsModal.classList.add('hidden');
                toast.classList.remove('opacity-0', 'pointer-events-none');
                toast.classList.add('opacity-100');

                setTimeout(() => {
                    toast.classList.remove('opacity-100');
                    toast.classList.add('opacity-0', 'pointer-events-none');
                }, 2000);
            });
        });

    </script>
</body>

</html>
