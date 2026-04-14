@extends('layouts.adminLayout')

@section('title', 'Create Events Tiketku')

@section('content')
<div class="flex h-screen bg-gray-100">
    <div class="m-auto w-full max-w-4xl">
        <div class="bg-white rounded-lg shadow p-6">
            <h1 class="text-2xl font-bold text-center text-gray-800 mb-6">Pembuatan Event</h1>

            <!-- Form Pembuatan Event -->
            <form action="{{ route('events.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <!-- Nama Event -->
                <div class="mb-4">
                    <label for="name" class="block text-sm font-medium text-gray-700">Nama Event</label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" placeholder="Enter event name"
                        class="w-full px-4 py-2.5 mt-2 text-base border border-gray-300 rounded-lg bg-gray-200 focus:border-blue-500 focus:bg-white focus:outline-none focus:ring-2 ring-offset-current ring-offset-2 ring-gray-400 @error('name') @enderror" required>
                    @error('name')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Tanggal Event -->
                <div class="mb-4">
                    <label for="date" class="block text-sm font-medium text-gray-700">Tanggal Event</label>
                    <input type="date" id="date" name="date" value="{{ old('date') }}" min="{{ date('Y-m-d') }}"
                        class="w-full px-4 py-2.5 mt-2 text-base border border-gray-300 rounded-lg bg-gray-200 focus:border-blue-500 focus:bg-white focus:outline-none focus:ring-2 ring-offset-current ring-offset-2 ring-gray-400 @error('date') @enderror" required>
                    @error('date')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Waktu Mulai dan Berakhir -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="start_time" class="block text-sm font-medium text-gray-700">Waktu Mulai</label>
                        <input type="time" id="start_time" name="start_time" value="{{ old('start_time') }}"
                            class="w-full px-4 py-2.5 mt-2 text-base border border-gray-300 rounded-lg bg-gray-200 focus:border-blue-500 focus:bg-white focus:outline-none focus:ring-2 ring-offset-current ring-offset-2 ring-gray-400 @error('start_time') @enderror" required>
                        @error('start_time')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="end_time" class="block text-sm font-medium text-gray-700">Waktu Selesai/label>
                        <input type="time" id="end_time" name="end_time" value="{{ old('end_time') }}"
                            class="w-full px-4 py-2.5 mt-2 text-base border border-gray-300 rounded-lg bg-gray-200 focus:border-blue-500 focus:bg-white focus:outline-none focus:ring-2 ring-offset-current ring-offset-2 ring-gray-400 @error('end_time') @enderror" required>
                        @error('end_time')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Gambar Event -->
                <div class="mt-4">
                    <label for="image" class="block text-sm font-medium text-gray-700">Poster</label>
                    <input type="file" id="image" name="image"
                        class="w-full px-4 py-2.5 mt-2 text-base border border-gray-300 rounded-lg bg-gray-200 focus:border-blue-500 focus:bg-white focus:outline-none focus:ring-2 ring-offset-current ring-offset-2 ring-gray-400 @error('image') @enderror" required>
                    @error('image')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Lokasi dan Venue -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                    <div>
                        <label for="location" class="block text-sm font-medium text-gray-700">Kota</label>
                        <select id="location" name="location"
                            class="w-full px-4 py-2.5 mt-2 text-base border border-gray-300 rounded-lg bg-gray-200 focus:border-blue-500 focus:bg-white focus:outline-none focus:ring-2 ring-offset-current ring-offset-2 ring-gray-400 @error('location') @enderror" required>
                            <option value="" selected disabled>-- Select City --</option>
                            @foreach($cities as $city)
                                <option value="{{ $city->name }}">{{ $city->name }}</option>
                            @endforeach
                        </select>
                        @error('location')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="venue" class="block text-sm font-medium text-gray-700">Tempat</label>
                        <input type="text" id="venue" name="venue" value="{{ old('venue') }}" placeholder="Enter event venue"
                            class="w-full px-4 py-2.5 mt-2 text-base border border-gray-300 rounded-lg bg-gray-200 focus:border-blue-500 focus:bg-white focus:outline-none focus:ring-2 ring-offset-current ring-offset-2 ring-gray-400 @error('venue') @enderror" required>
                        @error('venue')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Deskripsi -->
                <div class="mt-4">
                    <label for="description" class="block text-sm font-medium text-gray-700">Deskripsi</label>
                    <textarea id="description" name="description" rows="3" placeholder="Enter event description (optional)"
                        class="w-full px-4 py-2.5 mt-2 text-base border border-gray-300 rounded-lg bg-gray-200 focus:border-blue-500 focus:bg-white focus:outline-none focus:ring-2 ring-offset-current ring-offset-2 ring-gray-400 @error('description') @enderror">{{ old('description') }}</textarea>
                    @error('description')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- WhatsApp Group Link -->
                <div class="mt-4">
                    <label for="whatsapp_group_link" class="block text-sm font-medium text-gray-700">WhatsApp Group Link</label>
                    <input type="url" id="whatsapp_group_link" name="whatsapp_group_link" value="{{ old('whatsapp_group_link') }}" placeholder="Enter WhatsApp group link"
                        class="w-full px-4 py-2.5 mt-2 text-base border border-gray-300 rounded-lg bg-gray-200 focus:border-blue-500 focus:bg-white focus:outline-none focus:ring-2 ring-offset-current ring-offset-2 ring-gray-400 @error('whatsapp_group_link') @enderror">
                    @error('whatsapp_group_link')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Kategori dan Kapasitas -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                    <div>
                        <label for="category" class="block text-sm font-medium text-gray-700">Kategori</label>
                        <select id="category" name="category"
                            class="w-full px-4 py-2.5 mt-2 text-base border border-gray-300 rounded-lg bg-gray-200 focus:border-blue-500 focus:bg-white focus:outline-none focus:ring-2 ring-offset-current ring-offset-2 ring-gray-400 @error('category') @enderror" required>
                            <option value="" selected disabled>Select category</option>
                            <option value="Music" {{ old('category') == 'Music' ? 'selected' : '' }}>Music</option>
                            <option value="Sport" {{ old('category') == 'Sport' ? 'selected' : '' }}>Sport</option>
                            <option value="Seminar" {{ old('category') == 'Seminar' ? 'selected' : '' }}>Seminar</option>
                            <option value="Workshop" {{ old('category') == 'Workshop' ? 'selected' : '' }}>Workshop</option>
                        </select>
                        @error('category')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="capacity" class="block text-sm font-medium text-gray-700">Kapasitas</label>
                        <input type="number" id="capacity" name="capacity" value="{{ old('capacity') }}" placeholder="Enter event capacity" min="1"
                            class="w-full px-4 py-2.5 mt-2 text-base border border-gray-300 rounded-lg bg-gray-200 focus:border-blue-500 focus:bg-white focus:outline-none focus:ring-2 ring-offset-current ring-offset-2 ring-gray-400 @error('capacity') @enderror" required>
                        @error('capacity')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Tombol Submit -->
                <div class="mt-6 text-center">
                    <button type="submit"
                        class="flex items-center justify-center px-5 py-2.5 font-medium tracking-wide text-white capitalize bg-black rounded-md hover:bg-gray-900 focus:outline-none transition duration-300 transform active:scale-95 ease-in-out">
                        <svg xmlns="http://www.w3.org/2000/svg" enable-background="new 0 0 24 24" height="24px" viewBox="0 0 24 24" width="24px" fill="#FFFFFF">
                            <g>
                                <rect fill="none" height="24" width="24"></rect>
                            </g>
                            <g>
                                <g>
                                    <path d="M19,13h-6v6h-2v-6H5v-2h6V5h2v6h6V13z"></path>
                                </g>
                            </g>
                        </svg>
                        <span class="pl-2 mx-1">Buat Event</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
