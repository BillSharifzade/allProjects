<header class="header-area header-two-area light-area">
    <div class="container header__container">
        <div class="header__main">
            <a href="/" class="logo">
               <img src="{{asset('assets/logo.png')}}" alt="logo">
                {{-- <h2 style="color: #fff">

                </h2> --}}
            </a>
            <div class="main-menu">
                <nav>
                    <ul>
                        <li class="has-megamenu">
                            <a href="{{route('home')}}">Главная</a>
                        </li>
                        <li><a href="{{route('abouts')}}">О нас</a></li>
                        {{-- <li><a href="/qwasar">Qwasar</a></li> --}}
                        <li>
                            <a href="{{route('blog')}}">Блог</a>
                        </li>
                        <li><a href="{{route('contacts')}}">Контакты</a></li>
                    </ul>
                </nav>
            </div>
            <div class="d-none d-xl-flex gap-4">
                <a href="{{route('contacts')}}" class="btn-one">Связаться с нами <i
                        class="fa-regular fa-arrow-right-long"></i></a>
                <div class="about-three__left-item d-flex flex-wrap gap-2 align-items-center">
                    <div class="about-call-icon">
                            <span>
                                <svg width="26" height="26" viewBox="0 0 26 26" fill="none"
                                     xmlns="http://www.w3.org/2000/svg">
                                    <g clip-path="url(#clip0_918_1337)">
                                        <path
                                            d="M5.41667 4.33337H9.75L11.9167 9.75004L9.20833 11.375C10.3685 13.7275 12.2725 15.6315 14.625 16.7917L16.25 14.0834L21.6667 16.25V20.5834C21.6667 21.158 21.4384 21.7091 21.0321 22.1154C20.6257 22.5218 20.0746 22.75 19.5 22.75C15.2742 22.4932 11.2885 20.6987 8.2949 17.7051C5.3013 14.7115 3.5068 10.7258 3.25 6.50004C3.25 5.9254 3.47827 5.3743 3.8846 4.96798C4.29093 4.56165 4.84203 4.33337 5.41667 4.33337Z"
                                            stroke="white" stroke-width="2" stroke-linecap="round"
                                            stroke-linejoin="round">
                                        </path>
                                        <path
                                            d="M16.25 7.58337C16.8246 7.58337 17.3757 7.81165 17.7821 8.21798C18.1884 8.6243 18.4167 9.1754 18.4167 9.75004"
                                            stroke="white" stroke-width="2" stroke-linecap="round"
                                            stroke-linejoin="round">
                                        </path>
                                        <path
                                            d="M16.25 3.25C17.9739 3.25 19.6272 3.93482 20.8462 5.15381C22.0652 6.37279 22.75 8.02609 22.75 9.75"
                                            stroke="white" stroke-width="2" stroke-linecap="round"
                                            stroke-linejoin="round">
                                        </path>
                                    </g>
                                    <defs>
                                        <clipPath>
                                            <rect width="26" height="26" fill="white"></rect>
                                        </clipPath>
                                    </defs>
                                </svg>
                            </span>
                    </div>
                    <div class="info">
                        <span class="sm-font fw-600 text-white">Позвоните нам</span>
                        <h5 class="text-white">+992 710 44 9999</h5>
                    </div>
                </div>
            </div>
            <div class="bars d-block d-lg-none">
                <i id="openButton" class="fa-solid fa-bars"></i>
            </div>
        </div>
    </div>
</header>
