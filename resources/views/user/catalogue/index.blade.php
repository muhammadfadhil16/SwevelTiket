@extends('layouts.userLayout')

@section('title', 'Tiket Aja - Home Page')

@section('content')
<!-- Carousel -->
<div class="relative w-full overflow-hidden">
    <div id="carousel" class="flex transition-transform duration-500 ease-in-out">
        <div class="carousel-item w-full">
            <img src="{{ asset('images/user/Carousell4.jpeg') }}" alt="Carousel 1"
                class="h-64 md:h-96 w-full object-cover">
        </div>
        <div class="carousel-item w-full">
            <img src="{{ asset('images/user/Carousell3.jpeg') }}" alt="Carousel 2"
                class="h-64 md:h-96 w-full object-cover">
        </div>
        <div class="carousel-item w-full">
            <img src="{{ asset('images/user/Carousell6.jpeg') }}" alt="Carousel 3"
                class="h-64 md:h-96 w-full object-cover">
        </div>
        <div class="carousel-item w-full">
            <img src="{{ asset('images/user/Carousell5.jpeg') }}" alt="Carousel 4"
                class="h-64 md:h-96 w-full object-cover">
        </div>
    </div>

    <!-- Previous Button -->
    <button id="prevBtn"
        class="absolute top-1/2 left-4 transform -translate-y-1/2 bg-white text-gray-600 text-2xl p-2 rounded-full shadow hover:bg-gray-100">
        <i class='bx bxs-chevron-left'></i>
    </button>

    <!-- Next Button -->
    <button id="nextBtn"
        class="absolute top-1/2 right-4 transform -translate-y-1/2 bg-white text-gray-600 text-2xl p-2 rounded-full shadow hover:bg-gray-100">
        <i class='bx bxs-chevron-right'></i>
    </button>

    <!-- Indicators -->
    <div id="carouselIndicators" class="absolute bottom-4 left-1/2 transform -translate-x-1/2 flex gap-2">
        <span data-index="0" class="indicator-dot h-3 w-3 rounded-full bg-gray-400 cursor-pointer"></span>
        <span data-index="1" class="indicator-dot h-3 w-3 rounded-full bg-gray-400 cursor-pointer"></span>
        <span data-index="2" class="indicator-dot h-3 w-3 rounded-full bg-gray-400 cursor-pointer"></span>
        <span data-index="3" class="indicator-dot h-3 w-3 rounded-full bg-gray-400 cursor-pointer"></span>
    </div>
</div>

<!-- Search -->
<div class="mt-4 px-4">
    <form action="{{ route('catalogue.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-6 gap-3">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Event Disini..."
            class="col-span-1 sm:col-span-5 border border-blue-800 rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 placeholder-gray-500" />
        <button type="submit"
            class="flex items-center justify-center gap-2 col-span-1 bg-blue-800 text-white py-2 px-3 rounded-xl hover:opacity-80">
            <i class="bx bx-search text-xl"></i> Cari Event
        </button>
    </form>
</div>

<!-- Categories -->
<div class="mt-4 px-4 flex flex-wrap gap-2">
    @foreach(['Music'=>'bxs-music','Sport'=>'bx-run','Seminar'=>'bxs-calendar','Workshop'=>'bxs-wrench'] as $cat=>$icon)
    <a href="{{ route('catalogue.index', ['category' => $cat, 'search' => request('search')]) }}"
        class="flex items-center gap-2 bg-white text-blue-800 font-semibold px-3 py-2 rounded-md shadow hover:bg-blue-50">
        <i class="bx {{ $icon }} text-blue-500"></i> {{ $cat }}
    </a>
    @endforeach
</div>

<!-- Event Cards -->
<div class="mt-5 px-4">
    <h3 class="text-lg font-semibold text-blue-800 mb-2">Event Terdekat</h3>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        @foreach($events as $event)
        <div class="bg-white rounded-lg shadow p-4 flex flex-col">
            <img src="{{ asset('storage/'.$event->image) }}" alt="{{ $event->name }}"
                class="w-full h-40 object-cover rounded-md mb-2" />
            <h4 class="text-blue-700 font-semibold text-sm mb-1 truncate">{{ $event->name }}</h4>
            <p class="text-xs text-gray-500 flex items-center gap-1">
                <i class="bx bxs-calendar"></i>
                {{ \Carbon\Carbon::parse($event->date)->format('l, d M Y') }}
            </p>
            <p class="text-xs text-gray-500 flex items-center gap-1">
                <i class="bx bx-current-location"></i>
                {{ $event->location }}
            </p>
            <div class="mt-auto flex items-center justify-between pt-3">
                @if($event->tickets->count())
                <span class="text-orange-500 font-bold text-sm">
                    Rp{{ number_format($event->min_price ?? 0, 0, ',', '.') }}
                </span>
                @else
                <span class="text-red-500 font-bold text-sm">Sold Out</span>
                @endif
                <a href="{{ route('user.catalogue.showEvent', ['id_event'=>$event->id_event]) }}"
                    class="bg-blue-800 text-white px-3 py-1 rounded hover:bg-blue-700 text-xs">
                    Lihat Tiket
                </a>
            </div>
        </div>
        @endforeach
    </div>
    <div class="text-center mt-6">
        <a href="{{ route('user.catalogue.showAllEvents') }}"
            class="inline-block bg-blue-800 text-white px-6 py-2 rounded-xl hover:bg-blue-700">
            Lihat Semua Event
        </a>
    </div>
</div>

<!-- Banner CTA -->
<div class="mt-8 mb-5 mx-4 p-6 rounded-lg text-center bg-white shadow-lg relative">
    <!-- Background Image -->
    <div class="absolute inset-0 bg-cover bg-center rounded-lg" style="background-image: url('{{ asset('assets/img/banner7.png') }}'); opacity: 0.9;"></div>

    <!-- Konten -->
    <div class="relative z-10">
        <h2 class="text-2xl font-bold mb-2 text-gray-800">Event yang dipilih khusus untuk Anda!</h2>
        <p class="mb-4 text-black">Dapatkan saran acara yang disesuaikan dengan minat Anda! Jangan sampai acara favorit Anda terlewatkan.</p>
        <a href="#"
            class="inline-block bg-red-600 text-white px-5 py-2 rounded-lg hover:bg-red-700 transition duration-200">
            Get Started
        </a>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const carousel = document.getElementById('carousel');
        const slides = document.querySelectorAll('.carousel-item');
        const dots = document.querySelectorAll('.indicator-dot');
        const prevBtn = document.getElementById('prevBtn');
        const nextBtn = document.getElementById('nextBtn');

        let currentIndex = 0;
        const slideInterval = 5000; // 5 detik
        let autoplay;

        const update = () => {
            carousel.style.transform = `translateX(-${currentIndex * 100}%)`;
            dots.forEach((dot, index) => {
                dot.classList.toggle('bg-blue-500', index === currentIndex);
                dot.classList.toggle('bg-gray-400', index !== currentIndex);
            });
        };

        const nextSlide = () => {
            currentIndex = (currentIndex < slides.length - 1) ? currentIndex + 1 : 0;
            update();
        };

        const prevSlide = () => {
            currentIndex = (currentIndex > 0) ? currentIndex - 1 : slides.length - 1;
            update();
        };

        const startAutoplay = () => {
            autoplay = setInterval(nextSlide, slideInterval);
            console.log('Autoplay started');
        };

        const stopAutoplay = () => {
            clearInterval(autoplay);
        };

        prevBtn.addEventListener('click', () => {
            stopAutoplay();
            prevSlide();
            startAutoplay();
        });

        nextBtn.addEventListener('click', () => {
            stopAutoplay();
            nextSlide();
            startAutoplay();
        });

        dots.forEach((dot, index) => {
            dot.addEventListener('click', () => {
                stopAutoplay();
                currentIndex = index;
                update();
                startAutoplay();
            });
        });

        // Start autoplay on page load
        startAutoplay();
        update();
    });
</script>
@endpush
@endsection
