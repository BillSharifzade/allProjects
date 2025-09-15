@extends('layouts.site.theme')
@section('content')
    <section class="banner__inner-page bg-image pt-180 pb-180 bg-image"
    data-background="/assets/1.JPG" style="background-position: 0 25%;">
    <div class="shape2 wow slideInLeft" data-wow-delay="00ms" data-wow-duration="1500ms">
        <img src="/assets/images/banner/inner-banner-shape2.png" alt="shape">
    </div>
    <div class="shape1 wow slideInLeft" data-wow-delay="200ms" data-wow-duration="1500ms">
        <img src="/assets/images/banner/inner-banner-shape1.png" alt="shape">
    </div>
    <div class="shape3 wow slideInRight" data-wow-delay="200ms" data-wow-duration="1500ms">
        <img class="sway__animationX" src="/assets/images/banner/inner-banner-shape3.png" alt="shape">
    </div>
    <div class="container">
        <h2 class="wow fadeInUp" data-wow-delay="00ms" data-wow-duration="1500ms">{{$blogs->title}}</h2>
        <div class="breadcrumb-list wow fadeInUp" data-wow-delay="200ms" data-wow-duration="1500ms">
            <a href="/blog">Блог</a><span><i class="fa-regular fa-angles-right mx-2"></i>{{$blogs->title}}</span>
        </div>
    </div>
    </section>
    <section class="blog-single-area pt-120 pb-120">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-8 order-2 order-lg-1">
                    <div class="blog__item blog-single__left-item shadow-none">
                        <div class="image">
                            <img src="/storage/{{ $blogs->img }}" alt="image">
                        </div>
                        <div class="blog__content p-0">
                            
                            <h3 class="blog-single__title mt-20">{{$blogs->title}}</h3>
                            <p class="mb-20 mt-20">
                                {!! $blogs->description !!}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection