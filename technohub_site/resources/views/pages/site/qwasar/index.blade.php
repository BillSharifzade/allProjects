@extends('layouts.site.theme')
@section('content')
<style>
  .service__item h4{
    color: #3060D6
  }
  .active h4{
    color: #ffffff;
  }
  .service__item{
    color: #3060D6
  }

</style>
<section
class="banner__inner-page bg-image pt-180 pb-180 bg-image"
data-background="/assets/images/banner/banner-inner-page.jpg"
>
<div
  class="shape2 wow slideInLeft"
  data-wow-delay="00ms"
  data-wow-duration="1500ms"
>
  <img src="assets/images/banner/inner-banner-shape2.png" alt="shape" />
</div>
<div
  class="shape1 wow slideInLeft"
  data-wow-delay="200ms"
  data-wow-duration="1500ms"
>
  <img src="assets/images/banner/inner-banner-shape1.png" alt="shape" />
</div>
<div
  class="shape3 wow slideInRight"
  data-wow-delay="200ms"
  data-wow-duration="1500ms"
>
  <img
    class="sway__animationX"
    src="assets/images/banner/inner-banner-shape3.png"
    alt="shape"
  />
</div>
<div class="container">
  <h2
    class="wow fadeInUp"
    data-wow-delay="00ms"
    data-wow-duration="1500ms"
  >
    О нас
  </h2>
  <div
    class="breadcrumb-list wow fadeInUp"
    data-wow-delay="200ms"
    data-wow-duration="1500ms"
  >
    <a href="/">Главная</a
    ><span
      ><i class="fa-regular fa-angles-right mx-2"></i>Qwasar</span
    >
  </div>
</div>
</section>

@if(isset($services))
<section class="service-area pt-120 pb-120">
  <div class="service__shape wow slideInRight" style="visibility: visible; animation-name: slideInRight;">
      <img class="sway_Y__animation" src="/assets/images/shape/service-bg-shape.png" alt="shape">
  </div>
  <div class="container">
      <div class="row g-4">
          @foreach($services as $item)
            <div class="col-lg-4 col-md-6 wow bounceInUp" data-wow-delay="00ms" data-wow-duration="1000ms" style="visibility: visible; animation-duration: 1000ms; animation-delay: 0ms; animation-name: bounceInUp;">
              <div class="service__item">
                  <div class="service-shape">
                      <img src="/assets/images/shape/service-item-shape.png" alt="shape">
                  </div>
                  <div class="service__icon">
                      <img src="/storage/{{ $item->img }}" style="width: 100%" alt="icon">
                  </div>
                  <h4>{{$item->title}}</h4>
                  <p>{!! $item->description !!}</p>
              </div>
            </div>
          @endforeach
      </div>
  </div>
</section>
@endif
<!-- Page banner area end here -->

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
                      <img src="/storage/aboutUs/JrX7gJbC4LX1jREVcuo6o3UEjc3JPvsm5RPRIuzU.jpg" alt="image">
                  </div>
                  <div class="image sm-image">
                      <img src="/storage/aboutUs/H046Dhf4Z1HztxJ65QCPBtvtCrqHaNKFoPM7GulB.png" alt="image">
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
                    Ваш путь в IT с мировым именем
                  </h2>
                  <p class="wow fadeInUp" data-wow-delay="400ms" data-wow-duration="1500ms">
                    Наша миссия – предоставить вам доступ к передовым знаниям и практическим навыкам в сфере программирования.
                  </p>
                  <p class="wow fadeInUp" data-wow-delay="400ms" data-wow-duration="1500ms">
                    Обучение проходит на уникальной онлайн-платформе Qwasar, предлагающей более 8 востребованных направлений с возможностью получения международных сертификатов.
                  </p>
                  <p class="wow fadeInUp" data-wow-delay="400ms" data-wow-duration="1500ms">
                    Мы создаем все условия для вашего успеха: комфортное пространство для занятий, постоянная поддержка опытных менторов, сильное комьюнити единомышленников и яркие развлекательные мероприятия.
                  </p>
                  <p class="wow fadeInUp" data-wow-delay="400ms" data-wow-duration="1500ms">
                    Но главное – мы готовим вас к реальной карьере. Наша программа включает практические тренинги, помощь в формировании профессионального портфолио, составлении сильного резюме и подготовке к собеседованиям.
                  </p>
                  <p class="wow fadeInUp" data-wow-delay="400ms" data-wow-duration="1500ms">
                    Начните свой путь в IT с нами и получите образование, признанное в Кремниевой долине!
                  </p>
              </div>
              <div class="about-two__right-item wow fadeInDown" data-wow-delay="200ms"
                    data-wow-duration="1500ms">
                  <ul>
                      <li><i class="fa-solid fa-check"></i>Стандарты Кремниевой долины</li>
                      <li><i class="fa-solid fa-check"></i>Выбирай свое направление</li>
                  </ul>
                  <ul>
                      <li><i class="fa-solid fa-check"></i>Международный Сертификат</li>
                      <li><i class="fa-solid fa-check"></i>Практические Навыки</li>
                  </ul>
                  <ul>
                    <li><i class="fa-solid fa-check"></i>Создай свое портфолио</li>
                    <li><i class="fa-solid fa-check"></i>Помощь в трудоустройстве</li>
                </ul>
              </div>
              {{-- <div class="about__info mt-50 wow fadeInDown" data-wow-delay="400ms" data-wow-duration="1500ms">
                  <a href="{{route('abouts')}}" class="btn-one">Подробнее
                      <i class="fa-regular fa-arrow-right-long"></i>
                  </a>
              </div> --}}
          </div>
      </div>
  </div>
</section>
<!-- About area end here -->
@endsection
