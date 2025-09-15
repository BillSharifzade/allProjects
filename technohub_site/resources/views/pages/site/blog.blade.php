@extends('layouts.site.theme')
@section('content')
    <section class="banner__inner-page bg-image pt-180 pb-180 bg-image"
        data-background="/assets/1.JPG" style="background-position: 0 25%;">
        <div class="shape2 wow slideInLeft" data-wow-delay="00ms" data-wow-duration="1500ms">
            <img src="assets/images/banner/inner-banner-shape2.png" alt="shape">
        </div>
        <div class="shape1 wow slideInLeft" data-wow-delay="200ms" data-wow-duration="1500ms">
            <img src="assets/images/banner/inner-banner-shape1.png" alt="shape">
        </div>
        <div class="shape3 wow slideInRight" data-wow-delay="200ms" data-wow-duration="1500ms">
            <img class="sway__animationX" src="assets/images/banner/inner-banner-shape3.png" alt="shape">
        </div>
        <div class="container">
            <h2 class="wow fadeInUp" data-wow-delay="00ms" data-wow-duration="1500ms">Блог</h2>
            <div class="breadcrumb-list wow fadeInUp" data-wow-delay="200ms" data-wow-duration="1500ms">
                <a href="/">Главная</a><span><i class="fa-regular fa-angles-right mx-2"></i>Блог</span>
            </div>
        </div>
    </section>
    @if(isset($blogs))
        @foreach ($blogs as $blog)
            <section class="blog-area pt-120 pb-120">
                <div class="container">
                    <div class="row g-4">
                        <div class="col-xl-4 col-lg-6 col-md-6 wow fadeInUp" data-wow-delay="00ms" data-wow-duration="1500ms" style="visibility: visible; animation-duration: 1500ms; animation-delay: 0ms; animation-name: fadeInUp;">
                            <div class="blog__item">
                                <a href="/blog-details/{{ $blog->id }}" class="blog__image d-block image">
                                    <img src="/storage/{{ $blog->img }}" alt="image">
                                </a>
                                <div class="blog__content">
                                    <h3>
                                        <a href="/blog-details/{{ $blog->id }}" class="primary-hover">
                                            {{ $blog->title }}
                                        </a>
                                    </h3>
                                    <a class="mt-25 read-more-btn" href="blog-details.html">Подробнее <i class="fa-regular fa-arrow-right-long"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        @endforeach
    @endif
@endsection