@extends('layouts.authLayout')

@section('title', 'Register')

@section('content')

<div class="py-20">
    <div class="flex h-full items-center justify-center">
        <div
            class="rounded-lg border border-gray-200 bg-white shadow-md dark:border-gray-700 dark:bg-gray-900 flex-col flex h-full items-center justify-center sm:px-4">
            <div class="flex h-full flex-col justify-center gap-4 p-6">
                <div class="left-0 right-0 inline-block border-gray-200 px-2 py-2.5 sm:px-4">
                    <form method="POST" action="{{ route('register') }}" class="flex flex-col gap-4 pb-4">
                        @csrf
                        <h1 class="mb-4 text-2xl font-bold dark:text-white">Register</h1>

                        <!-- Input Nama -->
                        <div>
                            <div class="mb-2">
                                <label class="text-sm font-medium text-gray-900 dark:text-gray-300" for="name_user">Name:</label>
                            </div>
                            <div class="flex w-full rounded-lg pt-1">
                                <div class="relative w-full">
                                    <input
                                        class="block w-full border disabled:cursor-not-allowed disabled:opacity-50 bg-gray-50 border-gray-300 text-gray-900 focus:border-cyan-500 focus:ring-cyan-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400 dark:focus:border-cyan-500 dark:focus:ring-cyan-500 p-2.5 text-sm rounded-lg"
                                        id="name_user" type="text" name="name_user" placeholder="Your Name" value="{{ old('name_user') }}" required autofocus />
                                </div>
                            </div>
                            @error('name_user')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Input Email -->
                        <div>
                            <div class="mb-2">
                                <label class="text-sm font-medium text-gray-900 dark:text-gray-300" for="email_user">Email:</label>
                            </div>
                            <div class="flex w-full rounded-lg pt-1">
                                <div class="relative w-full">
                                    <input
                                        class="block w-full border disabled:cursor-not-allowed disabled:opacity-50 bg-gray-50 border-gray-300 text-gray-900 focus:border-cyan-500 focus:ring-cyan-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400 dark:focus:border-cyan-500 dark:focus:ring-cyan-500 p-2.5 text-sm rounded-lg"
                                        id="email_user" type="email" name="email_user" placeholder="email@example.com" value="{{ old('email_user') }}" required />
                                </div>
                            </div>
                            @error('email_user')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Input Password -->
                        <div>
                            <div class="mb-2">
                                <label class="text-sm font-medium text-gray-900 dark:text-gray-300" for="password">Password:</label>
                            </div>
                            <div class="flex w-full rounded-lg pt-1">
                                <div class="relative w-full">
                                    <input
                                        class="block w-full border disabled:cursor-not-allowed disabled:opacity-50 bg-gray-50 border-gray-300 text-gray-900 focus:border-cyan-500 focus:ring-cyan-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400 dark:focus:border-cyan-500 dark:focus:ring-cyan-500 p-2.5 text-sm rounded-lg"
                                        id="password" type="password" name="password" placeholder="Password" required />
                                </div>
                            </div>
                            @error('password')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Input Konfirmasi Password -->
                        <div>
                            <div class="mb-2">
                                <label class="text-sm font-medium text-gray-900 dark:text-gray-300" for="password_confirmation">Confirm Password:</label>
                            </div>
                            <div class="flex w-full rounded-lg pt-1">
                                <div class="relative w-full">
                                    <input
                                        class="block w-full border disabled:cursor-not-allowed disabled:opacity-50 bg-gray-50 border-gray-300 text-gray-900 focus:border-cyan-500 focus:ring-cyan-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400 dark:focus:border-cyan-500 dark:focus:ring-cyan-500 p-2.5 text-sm rounded-lg"
                                        id="password_confirmation" type="password" name="password_confirmation" placeholder="Confirm Password" required />
                                </div>
                            </div>
                            @error('password_confirmation')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Tombol Register -->
                        <div class="flex flex-col gap-2">
                            <button
                                class="mt-2 tracking-wide font-semibold bg-green-500 text-gray-100 w-full py-4 rounded-lg hover:bg-green-700 transition-all duration-300 ease-in-out flex items-center justify-center focus:shadow-outline focus:outline-none">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                                </svg>
                                <span class="ml-3">
                                    Sign Up
                                </span>
                            </button>
                        </div>
                    </form>

                    <!-- Link ke Login -->
                    <div class="min-w-[270px]">
                        <div class="mt-4 text-center dark:text-gray-200">Already have an account?
                            <a class="text-blue-500 underline hover:text-blue-600" href="{{ route('login') }}">Login here</a>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

@endsection
