@extends('layouts.site.theme')
@section('content')
    <section class="banner-two-area">
        <div class="swiper banner__slider">
            <div class="swiper-wrapper">
                <div class="swiper-slide">
                    <div class="banner-two__line-left" data-animation="slideInLeft" data-duration="3s"
                         data-delay=".3s">
                        <img src="{{asset('assets/images/banner/banner-two-left-line.png')}}" alt="shape">
                    </div>
                    <div class="banner-two__shape2" data-animation="slideInRight" data-duration="2s"
                         data-delay=".3s">
                        <img src="{{asset('assets/images/banner/banner-two-solid-right-down.png')}}" alt="shape">
                    </div>
                    <div class="banner-two__shape1" data-animation="slideInRight" data-duration="2s"
                         data-delay=".5s">
                        <img src="{{asset('assets/images/banner/banner-two-solid-right-up.png')}}" alt="shape">
                    </div>
                    <div class="banner-two__right-shape wow slideInRight" data-wow-delay="200ms"
                         data-wow-duration="1500ms">
                        <img class="sway_Y__animation" src="{{asset('assets/images/banner/banner-two-right-shape.png')}}"
                             alt="shape">
                    </div>
                    <div class="banner-two__circle-solid">
                        <img class="animation__rotate" src="{{asset('assets/images/banner/banner-two-circle-solid.png')}}"
                             alt="shape">
                    </div>
                    <div class="banner-two__circle-regular">
                        <img class="animation__rotateY" src="{{asset('assets/images/banner/banner-two-circle-regular.png')}}"
                             alt="shape">
                    </div>
                    <div class="slide-bg" data-background="{{asset('assets/3.png')}}"></div>
                    <div class="container">
                        <div class="banner-two__content text-center">
                            <h4 data-animation="fadeInUp" data-delay=".3s" class="text-white mb-20">
                                Быстрый старт в высокооплачиваемую сферу IT.
                            </h4>
                            <div class="banner__content-title">
                                <h1>
                                    Кодируй, Обучайся, Работай
                                </h1>
                            </div>
                            <p data-animation="fadeInUp" data-delay=".7s" class="mt-20">
                                Освой программирование на практике, получи помощь с резюме и собеседованиями, и стань частью 95% выпускников, которые находят работу в IT!
                            </p>
                            <a data-animation="fadeInUp" data-delay="1s" href="{{route('abouts')}}"
                               class="btn-one mt-50">Подробнее <i class="fa-regular fa-arrow-right-long"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="banner__dot-wrp banner-two__dot-wrp">
            <div class="dot-light banner__dot"></div>
        </div>
    </section>
    <!-- Banner area end here -->
    <!-- About area start here -->
    @if(isset($abouts))
        <section class="about-two-area pt-120">
            <div class="about-two__shape">
                <img src="{{asset('assets/images/shape/about-two-shape.png')}}" alt="shape">
            </div>
            <div class="container">
                <div class="row g-4">
                    <div class="col-xl-6 wow fadeInRight" data-wow-delay="200ms" data-wow-duration="1500ms">
                        <div class="about-two__left-item">
                            <div class="dots">
                                <img class="sway_Y__animation" src="{{asset('assets/images/shape/about-two-dot.png')}}" alt="shape">
                            </div>
                            <div class="shape-halper">
                                <img class="sway__animation" src="{{asset('assets/images/shape/about-circle-helper.png')}}"
                                     alt="shape">
                            </div>
                            <div class="image big-image">
                                <img src="/storage/{{$abouts->img_1}}" alt="image">
                            </div>
                            <div class="image sm-image">
                                <img src="/storage/{{$abouts->img_2}}" alt="image">
                            </div>
                            <div class="circle-shape">
                                <img class="animation__rotate" src="{{asset('assets/images/shape/about-circle-helper.png')}}"
                                     alt="shape">
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-6">
                        <div class="section-header mb-40">
                            <h5 class="wow fadeInUp" data-wow-delay="00ms" data-wow-duration="1500ms">
                                <svg class="me-1" width="20" height="12" viewBox="0 0 20 12" fill="none"
                                     xmlns="http://www.w3.org/2000/svg">
                                    <rect x="0.75" y="0.75" width="18.5" height="10.5" rx="5.25" stroke="#3C72FC"
                                          stroke-width="1.5"
                                    />
                                    <mask id="path-2-inside-1_668_146" fill="white">
                                        <path
                                            d="M3 6C3 3.79086 4.79086 2 7 2H13C15.2091 2 17 3.79086 17 6C17 8.20914 15.2091 10 13 10H7C4.79086 10 3 8.20914 3 6Z"
                                        />
                                    </mask>
                                    <path
                                        d="M3 6C3 2.96243 5.46243 0.5 8.5 0.5H11.5C14.5376 0.5 17 2.96243 17 6C17 4.61929 15.2091 3.5 13 3.5H7C4.79086 3.5 3 4.61929 3 6ZM17 6C17 9.03757 14.5376 11.5 11.5 11.5H8.5C5.46243 11.5 3 9.03757 3 6C3 7.38071 4.79086 8.5 7 8.5H13C15.2091 8.5 17 7.38071 17 6ZM3 10V2V10ZM17 2V10V2Z"
                                        fill="#3C72FC" mask="url(#path-2-inside-1_668_146)"
                                    />
                                </svg>
                                О НАС
                            </h5>
                            <h2 class="wow fadeInUp" data-wow-delay="200ms" data-wow-duration="1500ms">
                                {{$abouts->title}}
                            </h2>
                            <p class="wow fadeInUp" data-wow-delay="400ms" data-wow-duration="1500ms">
                                {!! $abouts->description !!}
                            </p>
                        </div>
                        <div class="about-two__right-item wow fadeInDown" data-wow-delay="200ms"
                             data-wow-duration="1500ms">
                            <ul>
                                <li><i class="fa-solid fa-check"></i>Учись на реальных задачах</li>
                                <li><i class="fa-solid fa-check"></i>Практика с первого дня</li>
                            </ul>
                            <ul>
                                <li><i class="fa-solid fa-check"></i>Менторы – эксперты с 10+ лет опыта</li>
                                <li><i class="fa-solid fa-check"></i>Помощь в трудоустройстве</li>
                            </ul>
                        </div>
                        <div class="about__info mt-50 wow fadeInDown" data-wow-delay="400ms" data-wow-duration="1500ms">
                            <a href="{{route('abouts')}}" class="btn-one">Подробнее
                                <i class="fa-regular fa-arrow-right-long"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    @endif
    <!-- About area end here -->
    <!-- Case area start here -->
    @if(isset($courses))
        <section class="case-two-area secondary-bg pt-120">
            <div class="case-two__bg">
                <img src="{{asset('assets/images/bg/case-two-bg.png')}}" alt="image">
            </div>
            <div class="container">
                <div class="d-flex gap-4 flex-wrap align-items-center justify-content-between mb-60">
                    <div class="section-header">
                        <h5 class="wow fadeInLeft" data-wow-delay="00ms" data-wow-duration="1500ms">
                            <svg class="me-1" width="20" height="12" viewBox="0 0 20 12" fill="none"
                                 xmlns="http://www.w3.org/2000/svg">
                                <rect x="0.75" y="0.747803" width="18.5" height="10.5" rx="5.25" stroke="#3C72FC"
                                      stroke-width="1.5" />
                                <mask id="path-2-inside-1_1120_297" fill="white">
                                    <path
                                        d="M3 5.9978C3 3.78866 4.79086 1.9978 7 1.9978H13C15.2091 1.9978 17 3.78866 17 5.9978C17 8.20694 15.2091 9.9978 13 9.9978H7C4.79086 9.9978 3 8.20694 3 5.9978Z" />
                                </mask>
                                <path
                                    d="M3 5.9978C3 2.96024 5.46243 0.497803 8.5 0.497803H11.5C14.5376 0.497803 17 2.96024 17 5.9978C17 4.61709 15.2091 3.4978 13 3.4978H7C4.79086 3.4978 3 4.61709 3 5.9978ZM17 5.9978C17 9.03537 14.5376 11.4978 11.5 11.4978H8.5C5.46243 11.4978 3 9.03537 3 5.9978C3 7.37851 4.79086 8.4978 7 8.4978H13C15.2091 8.4978 17 7.37851 17 5.9978ZM3 9.9978V1.9978V9.9978ZM17 1.9978V9.9978V1.9978Z"
                                    fill="#3C72FC" mask="url(#path-2-inside-1_1120_297)" />
                            </svg>
                            Наши курсы
                        </h5>
                        <h2 class="text-white wow fadeInLeft" data-wow-delay="200ms" data-wow-duration="1500ms">
                            Востребованные направления на рынке</h2>
                    </div>
                    <div class="arry-btn  d-flex gap-3 wow fadeInUp" data-wow-delay="200ms" data-wow-duration="1500ms">
                        <button class="arry-prev case__arry-prev"><i class="fa-light fa-chevron-left"></i></button>
                        <button class="arry-next case__arry-next active"><i
                                class="fa-light fa-chevron-right"></i></button>
                    </div>
                </div>
            </div>
            <div class="case-two__container">
                <div class="swiper case-two__slider">
                    <div class="swiper-wrapper">
                        @foreach($courses as $course)
                            <div class="swiper-slide">
                                <div class="case-two__item">
                                    <div class="image case-two__image">
                                        <img src="/storage/{{$course->img}}" alt="image">
                                    </div>
                                    <div class="case-two__content">
                                        <span></span>
                                        <h4><a href="{{route('courses.details', ['id' => $course->id])}}" class="text-white">{{$course->title}}</a></h4>
                                    </div>
                                    <a href="{{route('courses.details', ['id' => $course->id])}}" class="case-two__btn">
                                        <i class="fa-regular fa-arrow-right"></i>
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>
    @endif
    <!-- Case area end here -->
    <!-- Choose area start here -->
    <section class="choose-area sub-bg pt-120 pb-120">
        <div class="choose__video-btn">
            <div class="video-btn video-pulse wow fadeIn" data-wow-delay="200ms" data-wow-duration="1500ms">
                <a class="video-popup" href="https://www.youtube.com/watch?v=iVqz_4M5mA0"><i
                        class="fa-solid fa-play"></i></a>
            </div>
        </div>
        <div class="choose__shape-right1 wow slideInRight d-none d-lg-block" data-wow-delay="200ms"
             data-wow-duration="1500ms">
            <img src="assets/images/shape/choose-shape-right.png" alt="shape">
        </div>
        <div class="choose__shape-right2 wow slideInRight d-none d-lg-block" data-wow-delay="200ms"
             data-wow-duration="1000ms">
            <img src="assets/images/shape/choose-shape-right2.png" alt="shape">
        </div>
        <div class="choose__shape-left sway__animation">
            <img src="assets/images/shape/choose-shape-left.png" alt="shape">
        </div>
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-6 d-block d-lg-none">
                    <div class="image">
                        <img src="assets/images/choose/choose-image1.png" alt="image">
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="section-header mb-40">
                        <h5 class="wow fadeInUp" data-wow-delay="00ms" data-wow-duration="1500ms">
                            <svg class="me-1" width="20" height="12" viewBox="0 0 20 12" fill="none"
                                 xmlns="http://www.w3.org/2000/svg">
                                <rect x="0.75" y="0.75" width="18.5" height="10.5" rx="5.25" stroke="#3C72FC"
                                      stroke-width="1.5" />
                                <mask id="path-2-inside-1_682_455" fill="white">
                                    <path
                                        d="M3 6C3 3.79086 4.79086 2 7 2H13C15.2091 2 17 3.79086 17 6C17 8.20914 15.2091 10 13 10H7C4.79086 10 3 8.20914 3 6Z" />
                                </mask>
                                <path
                                    d="M3 6C3 2.96243 5.46243 0.5 8.5 0.5H11.5C14.5376 0.5 17 2.96243 17 6C17 4.61929 15.2091 3.5 13 3.5H7C4.79086 3.5 3 4.61929 3 6ZM17 6C17 9.03757 14.5376 11.5 11.5 11.5H8.5C5.46243 11.5 3 9.03757 3 6C3 7.38071 4.79086 8.5 7 8.5H13C15.2091 8.5 17 7.38071 17 6ZM3 10V2V10ZM17 2V10V2Z"
                                    fill="#3C72FC" mask="url(#path-2-inside-1_682_455)" />
                            </svg>
                            Qwasar
                        </h5>
                        <h2 class="wow fadeInUp" data-wow-delay="200ms" data-wow-duration="1500ms">{{$aboutQwasar->title}}</h2>
                    </div>
                    <div class="row g-4 wow fadeInUp" data-wow-delay="400ms" data-wow-duration="1500ms">
                        <div class="col-md-6">
                            <div class="about__right-item">
                                <div class="icon">
                                    <img src="assets/images/icon/about-icon1.png" alt="icon">
                                </div>
                                <div class="content">
                                    <h4 class="mb-1">Реальные проекты</h4>
                                    <p>Практика, опыт, успех в IT</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="about__right-item">
                                <div class="icon">
                                    <img src="assets/images/icon/about-icon2.png" alt="icon">
                                </div>
                                <div class="content">
                                    <h4 class="mb-1">Доступность 24/7</h4>
                                    <p>Обучайся в удобное для себя время</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <p class="wow fadeInUp mt-5" data-wow-delay="400ms" data-wow-duration="1500ms">
                        {!! $aboutQwasar->description !!}
                    </p>
                </div>
                <div class="col-lg-6 d-none d-lg-block">
                    <div class="choose__image image">
                        <img src="assets/images/choose/choose-image1.png" alt="image">
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Choose area end here -->

    <section class="contact-area pt-120 pb-120">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-5">
                    <div class="contact__left-item primary-bg">
                        <h3 class="text-white mb-30">Контактная информация</h3>
                        <ul class="mt-40 mb-40">
                            <li>
                                <i>
                                    <svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M23.7891 1.81641H16.7578C13.3658 1.81641 10.6055 4.5767 10.6055 7.96875C10.6055 11.063 12.9015 13.631 15.8789 14.0585V16.7578C15.8788 16.9317 15.9303 17.1016 16.0268 17.2462C16.1234 17.3907 16.2607 17.5033 16.4214 17.5697C16.7456 17.705 17.1258 17.6325 17.3793 17.3792L20.6374 14.1211H23.7891C27.1811 14.1211 30 11.3608 30 7.96875C30 4.5767 27.1811 1.81641 23.7891 1.81641ZM16.7578 8.84754C16.2723 8.84754 15.8789 8.45402 15.8789 7.96863C15.8789 7.48324 16.2723 7.08973 16.7578 7.08973C17.2432 7.08973 17.6367 7.48324 17.6367 7.96863C17.6367 8.45402 17.2432 8.84754 16.7578 8.84754ZM20.2734 8.84754C19.7879 8.84754 19.3945 8.45402 19.3945 7.96863C19.3945 7.48324 19.7879 7.08973 20.2734 7.08973C20.7588 7.08973 21.1523 7.48324 21.1523 7.96863C21.1523 8.45402 20.7588 8.84754 20.2734 8.84754ZM23.7891 8.84754C23.3036 8.84754 22.9102 8.45402 22.9102 7.96863C22.9102 7.48324 23.3036 7.08973 23.7891 7.08973C24.2745 7.08973 24.668 7.48324 24.668 7.96863C24.668 8.45402 24.2745 8.84754 23.7891 8.84754Z" fill="#3C72FC"></path>
                                        <path d="M19.7461 28.1836C21.2 28.1836 22.3828 27.0008 22.3828 25.5469V22.0312C22.3828 21.6527 22.1408 21.3171 21.782 21.1978L16.5209 19.44C16.2634 19.3533 15.9819 19.3928 15.7553 19.5421L13.5186 21.033C11.1496 19.9035 8.33871 17.0925 7.20914 14.7236L8.7 12.4868C8.77415 12.3754 8.82189 12.2485 8.83958 12.1158C8.85728 11.9831 8.84447 11.8482 8.80213 11.7212L7.04432 6.46014C6.98611 6.28516 6.87428 6.13295 6.72469 6.02512C6.5751 5.91728 6.39534 5.85929 6.21094 5.85938H2.63672C1.18277 5.85938 0 7.02979 0 8.48373C0 18.61 9.6198 28.1836 19.7461 28.1836Z" fill="#3C72FC"></path>
                                    </svg>
                                </i>
                                <div>
                                    <span class="text-white">Телефон</span>
                                    <h3 class="mt-1"><a class="text-white" href="tel:+992710449999">+992 710 44 9999</a>
                                    </h3>
                                </div>
                            </li>
                            <li>
                                <i>
                                    <svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M12.6503 10.8272C12.8028 10.914 12.9765 10.9569 13.1519 10.9509C13.3316 10.9344 13.5058 10.8798 13.6628 10.7906L24.9222 4.22062C24.6775 3.79416 24.3249 3.43955 23.8999 3.19239C23.4749 2.94523 22.9923 2.81422 22.5006 2.8125H3.75063C3.25879 2.81406 2.77603 2.94499 2.35081 3.19216C1.92559 3.43932 1.57289 3.79402 1.32812 4.22062L12.6503 10.8272Z" fill="#3C72FC"></path>
                                        <path d="M25.3125 6.15938V12.675C24.4104 12.3503 23.4587 12.1854 22.5 12.1875C20.2633 12.191 18.1192 13.081 16.5376 14.6626C14.956 16.2442 14.066 18.3883 14.0625 20.625C14.0623 20.9384 14.0811 21.2514 14.1188 21.5625H3.75C3.00476 21.5603 2.29069 21.2632 1.76372 20.7363C1.23676 20.2093 0.939726 19.4952 0.9375 18.75V6.15938L11.7094 12.45C12.1434 12.6874 12.6303 12.8118 13.125 12.8118C13.6197 12.8118 14.1066 12.6874 14.5406 12.45L25.3125 6.15938Z" fill="#3C72FC"></path>
                                        <path d="M22.5 14.0625C20.7595 14.0625 19.0903 14.7539 17.8596 15.9846C16.6289 17.2153 15.9375 18.8845 15.9375 20.625C15.9375 22.3655 16.6289 24.0347 17.8596 25.2654C19.0903 26.4961 20.7595 27.1875 22.5 27.1875C22.7486 27.1875 22.9871 27.0887 23.1629 26.9129C23.3387 26.7371 23.4375 26.4986 23.4375 26.25C23.4375 26.0014 23.3387 25.7629 23.1629 25.5871C22.9871 25.4113 22.7486 25.3125 22.5 25.3125C21.5729 25.3125 20.6666 25.0376 19.8958 24.5225C19.1249 24.0074 18.5241 23.2754 18.1693 22.4188C17.8145 21.5623 17.7217 20.6198 17.9026 19.7105C18.0834 18.8012 18.5299 17.966 19.1854 17.3104C19.841 16.6549 20.6762 16.2084 21.5855 16.0276C22.4948 15.8467 23.4373 15.9395 24.2938 16.2943C25.1504 16.6491 25.8824 17.2499 26.3975 18.0208C26.9126 18.7916 27.1875 19.6979 27.1875 20.625V21.5625C27.1875 21.8111 27.0887 22.0496 26.9129 22.2254C26.7371 22.4012 26.4986 22.5 26.25 22.5C26.0014 22.5 25.7629 22.4012 25.5871 22.2254C25.4113 22.0496 25.3125 21.8111 25.3125 21.5625V20.625C25.3125 20.0687 25.1476 19.525 24.8385 19.0625C24.5295 18.5999 24.0902 18.2395 23.5763 18.0266C23.0624 17.8137 22.4969 17.758 21.9513 17.8665C21.4057 17.9751 20.9046 18.2429 20.5113 18.6363C20.1179 19.0296 19.8501 19.5307 19.7415 20.0763C19.633 20.6219 19.6887 21.1874 19.9016 21.7013C20.1145 22.2152 20.4749 22.6545 20.9375 22.9635C21.4 23.2726 21.9437 23.4375 22.5 23.4375C22.9843 23.4344 23.4594 23.3048 23.8781 23.0616C24.2022 23.578 24.6856 23.9748 25.2552 24.1921C25.8248 24.4094 26.4496 24.4353 27.0353 24.266C27.621 24.0967 28.1356 23.7412 28.5013 23.2535C28.867 22.7657 29.064 22.1721 29.0625 21.5625V20.625C29.0605 18.8851 28.3685 17.2171 27.1382 15.9868C25.9079 14.7565 24.2399 14.0645 22.5 14.0625ZM22.5 21.5625C22.3146 21.5625 22.1333 21.5075 21.9792 21.4045C21.825 21.3015 21.7048 21.1551 21.6339 20.9838C21.5629 20.8125 21.5443 20.624 21.5805 20.4421C21.6167 20.2602 21.706 20.0932 21.8371 19.9621C21.9682 19.831 22.1352 19.7417 22.3171 19.7055C22.499 19.6693 22.6875 19.6879 22.8588 19.7589C23.0301 19.8298 23.1765 19.95 23.2795 20.1042C23.3825 20.2583 23.4375 20.4396 23.4375 20.625C23.4375 20.8736 23.3387 21.1121 23.1629 21.2879C22.9871 21.4637 22.7486 21.5625 22.5 21.5625Z" fill="#3C72FC"></path>
                                    </svg>
                                </i>
                                <div>
                                    <span class="text-white">Оставь заявку</span>
                                    <h3 class="mt-1"><a class="text-white" href="#0">info@technohub.tj</a>
                                    </h3>
                                </div>
                            </li>
                            <li>
                                <i>
                                    <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M16 1.66667C11.036 1.66667 7 5.73867 7 10.7613C7 12.4627 7.74933 14.5733 8.84 16.6787C11.2413 21.3147 15.2413 25.984 15.2413 25.984C15.3352 26.0933 15.4516 26.1811 15.5826 26.2412C15.7135 26.3014 15.8559 26.3325 16 26.3325C16.1441 26.3325 16.2865 26.3014 16.4174 26.2412C16.5484 26.1811 16.6648 26.0933 16.7587 25.984C16.7587 25.984 20.7587 21.3147 23.16 16.6787C24.2507 14.5733 25 12.4627 25 10.7613C25 5.73867 20.964 1.66667 16 1.66667ZM16 7.00001C15.0447 7.02577 14.1371 7.42338 13.4705 8.10821C12.8039 8.79303 12.4309 9.71098 12.4309 10.6667C12.4309 11.6224 12.8039 12.5403 13.4705 13.2251C14.1371 13.91 15.0447 14.3076 16 14.3333C16.9553 14.3076 17.8629 13.91 18.5295 13.2251C19.1961 12.5403 19.5691 11.6224 19.5691 10.6667C19.5691 9.71098 19.1961 8.79303 18.5295 8.10821C17.8629 7.42338 16.9553 7.02577 16 7.00001Z" fill="#3C72FC"></path>
                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M22.3774 23.1693C23.4614 23.4947 24.3547 23.8973 24.972 24.3693C25.372 24.6733 25.6654 24.9707 25.6654 25.3333C25.6654 25.5467 25.544 25.74 25.3734 25.9333C25.0907 26.252 24.6707 26.5387 24.1507 26.8053C22.3134 27.7453 19.3427 28.3333 15.9987 28.3333C12.6547 28.3333 9.68403 27.7453 7.8467 26.8053C7.3267 26.5387 6.9067 26.252 6.62403 25.9333C6.45336 25.74 6.33203 25.5467 6.33203 25.3333C6.33203 24.9707 6.62536 24.6733 7.02536 24.3693C7.6427 23.8973 8.53603 23.4947 9.62003 23.1693C9.87411 23.093 10.0874 22.9188 10.2131 22.6851C10.3387 22.4514 10.3664 22.1774 10.29 21.9233C10.2136 21.6693 10.0395 21.4559 9.80579 21.3303C9.57212 21.2046 9.29811 21.177 9.04403 21.2533C7.39336 21.7507 6.1107 22.432 5.34003 23.1853C4.6627 23.8453 4.33203 24.584 4.33203 25.3333C4.33203 26.2693 4.86136 27.2027 5.93736 27.9813C7.82536 29.3467 11.6174 30.3333 15.9987 30.3333C20.38 30.3333 24.172 29.3467 26.06 27.9813C27.136 27.2027 27.6654 26.2693 27.6654 25.3333C27.6654 24.584 27.3347 23.8453 26.6574 23.1853C25.8867 22.432 24.604 21.7507 22.9534 21.2533C22.8276 21.2155 22.6955 21.2028 22.5648 21.2161C22.4341 21.2293 22.3073 21.2681 22.1916 21.3303C22.0759 21.3925 21.9736 21.4769 21.8905 21.5787C21.8074 21.6804 21.7452 21.7975 21.7074 21.9233C21.6695 22.0491 21.6569 22.1812 21.6701 22.3119C21.6833 22.4426 21.7221 22.5694 21.7843 22.6851C21.8465 22.8008 21.9309 22.9031 22.0327 22.9862C22.1344 23.0693 22.2516 23.1315 22.3774 23.1693Z" fill="#3C72FC"></path>
                                    </svg>
                                </i>
                                <div>
                                    <span class="text-white">Адрес</span>
                                    <h3 class="mt-1"><a class="text-white" href="tel:+2085550112">Душанбе, ул. Aйни 55</a>
                                    </h3>
                                </div>
                            </li>
                        </ul>
                        <h4 class="text-white mb-20">
                            Follow Social:
                        </h4>
                        <div class="social">
                            <a href="#0"><i class="fa-brands fa-facebook-f" style="color: #3c72fc"></i></a>
                            <a href="#0"><i class="fa-brands fa-twitter" style="color: #3c72fc"></i></a>
                            <a href="#0"><i class="fa-brands fa-linkedin-in" style="color: #3c72fc"></i></a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="contact__right-item">
                        <div class="section-header mb-20">
                            <h5 class="wow fadeInUp pb-2" data-wow-delay="00ms" data-wow-duration="1500ms" style="visibility: visible; animation-duration: 1500ms; animation-delay: 0ms; animation-name: fadeInUp;">
                                <img class="me-1" src="assets/images/icon/section-title.png" alt="icon">
                                Форма заявки
                            </h5>
                            <h2 class="wow fadeInUp" data-wow-delay="200ms" data-wow-duration="1500ms" style="visibility: visible; animation-duration: 1500ms; animation-delay: 200ms; animation-name: fadeInUp;">Оставьте свою заявку</h2>
                        </div>
                        <div class="contact__form">                            
                            <form id="contact-form" action="/request" method="post">@csrf
                                <div class="row">
                                    <div class="col-6">
                                        <input name="name" class="bg-transparent bor" type="text" placeholder="Имя*" required="" style="color: #fff">
                                    </div>
                                    <div class="col-6">
                                        <input class="bg-transparent bor" name="email" type="email" placeholder="Email*" required="" style="color: #fff">
                                    </div>
                                    <div class="col-12">
                                        <input class="bg-transparent bor" name="subject" type="text" placeholder="Тема обращения*" required="" style="color: #fff">
                                    </div>
                                </div>
                                <div class="text-area">
                                    <textarea class="bg-transparent bor" name="message" placeholder="Сообщение" style="color: #fff"></textarea>
                                </div>
                                <div class="btn-two">
                                    <span class="btn-circle">
                                    </span>
                                    <button class="btn-one" type="submit">Оставить заявку <i class="fa-regular fa-arrow-right-long"></i></button>
                                </div>
                            </form>
                            <div id="form-messages"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="quote-area">
        <div class="container">
            <div class="quote__wrp gradient-bg">
                <div class="counter__shape wow slideInRight" data-wow-delay="200ms" data-wow-duration="1500ms">
                    <img src="assets/images/shape/quote-shape.png" alt="shape">
                </div>
                <div class="quote__shape bobble__animation">
                    <img src="assets/images/shape/quote-shape2.png" alt="shape">
                </div>
                <div class="d-flex flex-wrap gap-4 align-items-center justify-content-between">
                    <div class="section-header">
                        <h5 class="wow fadeInLeft text-white" data-wow-delay="00ms" data-wow-duration="1500ms">
                            <svg class="me-1" width="28" height="12" viewBox="0 0 28 12" fill="none"
                                 xmlns="http://www.w3.org/2000/svg">
                                <rect x="0.75" y="0.75" width="18.5" height="10.5" rx="5.25" stroke="white"
                                      stroke-width="1.5" />
                                <rect x="8.75" y="0.75" width="18.5" height="10.5" rx="5.25" fill="white"
                                      stroke="white" stroke-width="1.5" />
                            </svg>
                            Свяжитесь с нами
                        </h5>
                        <h2 class="wow fadeInLeft text-white" data-wow-delay="200ms" data-wow-duration="1500ms">
                            Мы позаботимся
                            О Вашей <br> Карьере</h2>
                    </div>
                    <a href="/contacts" class="btn-one wow fadeInUp" data-wow-delay="200ms"
                       data-wow-duration="1500ms">Оставить заявку <i class="fa-regular fa-arrow-right-long"></i></a>
                </div>
            </div>
        </div>
    </section>

@endsection
