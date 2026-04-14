<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>@yield('title')</title>
  <link rel="icon" href="{{ asset('assets/img/tikettt.png') }}" />

  {{-- Vite build: loads Tailwind CSS & app JS --}}
  @vite(['resources/css/app.css', 'resources/js/app.js'])

  {{-- Icon libraries --}}
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="{{ asset('css/notifications.css') }}">
  <link rel="stylesheet" href="{{ asset('css/carousel.css') }}">
</head>

<body class="min-h-screen flex flex-col bg-gray-100">
  {{-- Navbar --}}
  <header class="bg-white shadow fixed top-0 w-full z-30">
    <div class="container mx-auto px-6 lg:px-8">
      <div class="flex items-center justify-between h-16">
        {{-- Logo & Brand --}}
        <a href="{{ route('catalogue.index') }}" class="flex items-center space-x-2">
          <img src="{{ asset('assets/img/vip.png') }}" alt="Tiket Aja" class="h-10 w-auto" />
          <span class="text-2xl font-bold text-gray-800">Tiket Aja</span>
        </a>

        {{-- Desktop Nav Links --}}
        <nav class="hidden lg:flex items-center space-x-8">
          <a href="{{ route('catalogue.index') }}" class="text-gray-700 hover:text-blue-600">Beranda</a>

          {{-- Dropdown Event --}}
          <div class="relative">
            <button id="eventDropdownToggle" type="button" class="flex items-center text-gray-700 hover:text-blue-600 focus:outline-none">
              <span>Event</span>
              <i class="bx bx-chevron-down ml-1"></i>
            </button>
            <div id="eventDropdown" class="absolute left-0 mt-2 w-40 bg-white rounded-md shadow-lg hidden transition-opacity">
              <a href="{{ route('user.catalogue.showAllEvents', ['category'=>'Music','search'=>request('search')]) }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-50">Acara Musik</a>
              <a href="{{ route('user.catalogue.showAllEvents', ['category'=>'Sport','search'=>request('search')]) }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-50">Acara Olahraga</a>
              <a href="{{ route('user.catalogue.showAllEvents', ['category'=>'Seminar,Workshop','search'=>request('search')]) }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-50">Acara Pendidikan</a>
              <hr class="my-1" />
              <a href="#" class="block px-4 py-2 text-gray-700 hover:bg-gray-50">Lainnya</a>
            </div>
          </div>


          <a href="https://wa.me/6285245464758" class="text-gray-700 hover:text-blue-600">Hubungi Kami</a>

          {{-- User/Auth Links --}}
          @if(Auth::check())
          <div class="relative group">
              <!-- Tombol untuk User Profile -->
              <button id="profileToggle" class="flex items-center text-gray-700 hover:text-blue-600 focus:outline-none">
                  <i class="bi bi-person-circle text-2xl"></i>
                </button>
                <div id="profileDropdown" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg hidden z-50">
                  <div class="px-4 py-2 border-b text-gray-700 font-semibold">
                      Hi, {{ Auth::user()->name_user }}
                  </div>
                <a href="{{ route('user.orders.index') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-50">Tiketku</a>
                    <a href="{{ route('user.settings') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-50">Settings</a>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="block w-full text-left px-4 py-2 text-gray-700 hover:bg-gray-50">Logout</button>
                    </form>
                </div>
            </div>
            @else
            <a href="{{ route('login') }}" class="px-4 py-2 border border-blue-600 text-blue-600 rounded-full hover:bg-blue-600 hover:text-white transition">LOGIN</a>
            @endif
            {{-- Notifikasi untuk Desktop --}}
            <div class="relative group">
              <button id="desktop-notification-toggle" class="flex items-center text-gray-700 hover:text-blue-600 focus:outline-none">
                <i class="bi bi-bell text-2xl"></i>
                @if(Auth::check() && Auth::user()->unreadNotifications->count() > 0)
                  <span class="bg-red-500 text-white text-xs rounded-full px-2 py-0.5 ml-1">
                    {{ Auth::user()->unreadNotifications->count() }}
                  </span>
                @endif
              </button>
              <div id="desktop-notification-dropdown" class="absolute right-0 mt-2 w-80 bg-white rounded-md shadow-lg hidden z-50">
                @if(Auth::check())
                    <div class="px-4 py-2 text-gray-700 font-bold border-b border-gray-200 flex justify-between items-center">
                        <span>Notifikasi</span>
                        <button id="delete-all-notifications" class="text-red-600 hover:underline text-sm">Hapus Semua</button>
                    </div>
                    <ul class="divide-y divide-gray-200 max-h-64 overflow-y-auto">
                        @forelse(Auth::user()->unreadNotifications as $notification)
                            <li class="px-4 py-2">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <p class="text-sm">{{ $notification->data['message'] ?? 'No message available' }}</p>
                                        <small class="text-gray-500">{{ $notification->created_at->diffForHumans() }}</small>
                                    </div>
                                    <button class="text-blue-600 hover:underline mark-as-read" data-id="{{ $notification->id }}">
                                        Tandai
                                    </button>
                                </div>
                            </li>
                        @empty
                            <li class="px-4 py-2 text-gray-500 text-center">Tidak ada notifikasi baru</li>
                        @endforelse
                    </ul>
                @else
                    <div class="px-4 py-2 text-gray-500 text-center">Silakan login untuk melihat notifikasi</div>
                @endif
              </div>
            </div>
        </nav>

        {{-- Mobile Menu Toggle --}}
        <button id="mobile-toggle" class="lg:hidden focus:outline-none">
          <i class="bi bi-list text-3xl text-gray-800"></i>
        </button>
      </div>
    </div>


        <!-- Mobile Nav -->
        <div id="mobile-menu" class="hidden lg:hidden bg-white border-t border-gray-200">
          <nav class="flex flex-col px-6 py-4 space-y-2">
              @if(Auth::check())
              <div class="px-4 py-2 border-b text-gray-700 font-semibold">
                  Hi, {{ Auth::user()->name_user }}
              </div>
              @endif
            <a href="{{ route('catalogue.index') }}" class="text-gray-700 hover:text-blue-600">Beranda</a>
            <a href="#" id="mobile-notification-toggle" class="text-gray-700 hover:text-blue-600 flex items-center">
              <i class="bi bi-bell text-lg mr-2"></i> Notifikasi
              @if(Auth::check() && Auth::user()->unreadNotifications->count() > 0)
                <span class="bg-red-500 text-white text-xs rounded-full px-2 py-0.5 ml-2">
                  {{ Auth::user()->unreadNotifications->count() }}
                </span>
              @endif
            </a>
            <div id="mobile-notification-dropdown" class="hidden mt-2 w-full bg-white rounded-md shadow-lg border border-gray-200">
              @if(Auth::check())
                <div class="px-4 py-2 text-gray-700 font-bold border-b border-gray-200 flex justify-between items-center">
                  <span>Notifikasi</span>
                  <button id="delete-all-notifications-mobile" class="text-red-600 hover:underline text-sm">Hapus Semua</button>
                </div>
                <ul class="divide-y divide-gray-200 max-h-64 overflow-y-auto">
                  @forelse(Auth::user()->unreadNotifications as $notification)
                    <li class="px-4 py-2">
                      <div class="flex justify-between items-start">
                        <div>
                          <p class="text-sm">{{ $notification->data['message'] ?? 'No message available' }}</p>
                          <small class="text-gray-500">{{ $notification->created_at->diffForHumans() }}</small>
                        </div>
                        <button class="text-blue-600 hover:underline mark-as-read" data-id="{{ $notification->id }}">
                          Tandai
                        </button>
                      </div>
                    </li>
                  @empty
                    <li class="px-4 py-2 text-gray-500 text-center">Tidak ada notifikasi baru</li>
                  @endforelse
                </ul>
              @else
                <div class="px-4 py-2 text-gray-500 text-center">Silakan login untuk melihat notifikasi</div>
              @endif
            </div>
            <div class="relative">
              <button id="mobileEventDropdownToggle" type="button" class="flex items-center justify-between w-full text-gray-700 hover:text-blue-600 focus:outline-none">
                <span>Event</span>
                <svg id="mobileEventChevron" class="ml-2 h-5 w-5 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                </svg>
              </button>
              <div id="mobileEventDropdown" class="hidden z-50 mt-2 w-full bg-white rounded-md shadow-lg border border-gray-200">
                <a href="{{ route('user.catalogue.showAllEvents', ['category'=>'Music','search'=>request('search')]) }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-50">Acara Musik</a>
                <a href="{{ route('user.catalogue.showAllEvents', ['category'=>'Sport','search'=>request('search')]) }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-50">Acara Olahraga</a>
                <a href="{{ route('user.catalogue.showAllEvents', ['category'=>'Seminar,Workshop','search'=>request('search')]) }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-50">Acara Pendidikan</a>
                <hr class="my-1" />
                <a href="#" class="block px-4 py-2 text-gray-700 hover:bg-gray-50">Lainnya</a>
              </div>
            </div>
            <div class="border-t border-gray-200"></div>
            <a href="https://wa.me/6285245464758" class="text-gray-700 hover:text-blue-600">Hubungi Kami</a>
            @if(Auth::check())
            <a href="{{ route('order.index') }}" class="text-gray-700 hover:text-blue-600">Tiketku</a>
            <a href="{{ route('user.settings') }}" class="text-gray-700 hover:text-blue-600">Settings</a>
            <form action="{{ route('logout') }}" method="POST">
              @csrf
              <button type="submit" class="text-left text-gray-700 hover:text-blue-600">Logout</button>
            </form>
            @else
            <a href="{{ route('login') }}" class="text-gray-700 hover:text-blue-600">LOGIN</a>
            @endif
          </nav>
        </div>
      </nav>
    </div>
  </header>

  {{-- Notifikasi --}}
  @if (session('success'))
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            showNotification('success', "{{ session('success') }}");
        });
    </script>
  @endif

  @if (session('error'))
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            showNotification('danger', "{{ session('error') }}");
        });
    </script>
  @endif

  @if (session('status') && session('message'))
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            showNotification(
                "{{ session('status') === 'error' ? 'danger' : session('status') }}",
                "{{ session('message') }}"
            );
        });
    </script>
  @endif

  <div id="global-notifications" class="fixed top-5 right-5 z-50 space-y-2"></div>

  {{-- Main Content --}}
  <main class="flex-grow pt-20">
    @yield('content')
  </main>

  {{-- Footer --}}
  <footer class="bg-gray-800 text-gray-200">
    <div class="container mx-auto px-6 lg:px-8 py-8 grid grid-cols-1 md:grid-cols-4 gap-6">
      <div>
        <h5 class="font-bold mb-3">Tiket Aja</h5>
        <p>Platform penjualan tiket terpercaya untuk berbagai acara seperti konser, olahraga, seminar, dan lainnya.</p>
      </div>
      <div>
        <h5 class="font-bold mb-3">Hubungi Kami</h5>
        <p class="flex items-center"><i class="bi bi-whatsapp mr-2"></i> +6285179787955</p>
        <p class="flex items-center"><i class="bi bi-instagram mr-2"></i> tiketaja.id</p>
        <p class="flex items-center"><i class="bi bi-envelope mr-2"></i> contact@tiketaja.id</p>
      </div>
      <div>
        <h5 class="font-bold mb-3">Metode Pembayaran</h5>
        <div class="flex flex-wrap gap-2">
          <img src="{{ asset('assets/img/14.png') }}" alt="Bank BCA" class="h-8" />
        </div>
      </div>
      <div>
        <h5 class="font-bold mb-3">Tautan</h5>
        <ul class="space-y-1">
          <li><a href="#" class="hover:text-white">Tentang Kami</a></li>
          <li><a href="#" class="hover:text-white">Syarat dan Ketentuan</a></li>
          <li><a href="#" class="hover:text-white">Kebijakan Privasi</a></li>
        </ul>
      </div>
    </div>
    <div class="text-center py-4 text-sm">&copy; 2024 Tiket Aja Corp - All Rights Reserved.</div>
  </footer>

  {{-- Mobile Toggle Script --}}
  <script>
    document.getElementById('mobile-toggle').addEventListener('click', () => {
      document.getElementById('mobile-menu').classList.toggle('hidden');
    });

    document.addEventListener('DOMContentLoaded', () => {
      const notificationToggle = document.getElementById('notificationToggle');
      const notificationDropdown = document.getElementById('notificationDropdown');

      if (notificationToggle && notificationDropdown) {
        // Toggle visibility of the notification dropdown
        notificationToggle.addEventListener('click', () => {
          notificationDropdown.classList.toggle('hidden');
        });

        // Close the dropdown if clicked outside
        document.addEventListener('click', (event) => {
          if (!notificationToggle.contains(event.target) && !notificationDropdown.contains(event.target)) {
            notificationDropdown.classList.add('hidden');
          }
        });
      }

      const profileToggle = document.getElementById('profileToggle');
      const profileDropdown = document.getElementById('profileDropdown');

      if (profileToggle && profileDropdown) {
        // Toggle visibility of the profile dropdown
        profileToggle.addEventListener('click', () => {
          profileDropdown.classList.toggle('hidden');
        });

        // Close the dropdown if clicked outside
        document.addEventListener('click', (event) => {
          if (!profileToggle.contains(event.target) && !profileDropdown.contains(event.target)) {
            profileDropdown.classList.add('hidden');
          }
        });
      }

      const desktopNotificationToggle = document.getElementById('desktop-notification-toggle');
      const desktopNotificationDropdown = document.getElementById('desktop-notification-dropdown');

      if (desktopNotificationToggle && desktopNotificationDropdown) {
        // Toggle visibility of the notification dropdown
        desktopNotificationToggle.addEventListener('click', (e) => {
          e.stopPropagation(); // Prevent event bubbling
          desktopNotificationDropdown.classList.toggle('hidden');
        });

        // Close the dropdown if clicked outside
        document.addEventListener('click', (event) => {
          if (!desktopNotificationToggle.contains(event.target) && !desktopNotificationDropdown.contains(event.target)) {
            desktopNotificationDropdown.classList.add('hidden');
          }
        });
      }
    });

    const mobileNotificationToggle = document.getElementById('mobile-notification-toggle');
    const mobileNotificationDropdown = document.getElementById('mobile-notification-dropdown');

    if (mobileNotificationToggle && mobileNotificationDropdown) {
      mobileNotificationToggle.addEventListener('click', () => {
        mobileNotificationDropdown.classList.toggle('hidden');
      });

      document.addEventListener('click', (event) => {
        if (!mobileNotificationToggle.contains(event.target) && !mobileNotificationDropdown.contains(event.target)) {
          mobileNotificationDropdown.classList.add('hidden');
        }
      });
    }

    document.addEventListener('DOMContentLoaded', function () {
      const notifications = document.querySelectorAll('.notification');
      notifications.forEach(notification => {
        setTimeout(() => {
          notification.classList.add('hide');
        }, 5000); // Hilang setelah 2 detik
      });
    });

    document.addEventListener('DOMContentLoaded', function () {
        // Tandai notifikasi sebagai sudah dibaca
        document.querySelectorAll('.mark-as-read').forEach(button => {
            button.addEventListener('click', function () {
                const notificationId = this.dataset.id;

                fetch('{{ route('notifications.markAsRead') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ id: notificationId })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        this.closest('li').remove();
                    } else {
                        alert(data.message);
                    }
                })
                .catch(error => console.error('Error:', error));
            });
        });

        // Hapus semua notifikasi untuk desktop
        const deleteAllButtonDesktop = document.getElementById('delete-all-notifications');
        if (deleteAllButtonDesktop) { // Pastikan elemen ada
            deleteAllButtonDesktop.addEventListener('click', function () {
                if (confirm('Apakah Anda yakin ingin menghapus semua notifikasi?')) {
                    fetch('{{ route('notifications.deleteAll') }}', {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            document.querySelectorAll('#desktop-notification-dropdown ul li').forEach(li => li.remove());
                            alert(data.message);
                        } else {
                            alert('Gagal menghapus notifikasi.');
                        }
                    })
                    .catch(error => console.error('Error:', error));
                }
            });
        }

        // Hapus semua notifikasi untuk mobile
        const deleteAllButtonMobile = document.getElementById('delete-all-notifications-mobile');
        if (deleteAllButtonMobile) { // Pastikan elemen ada
            deleteAllButtonMobile.addEventListener('click', function () {
                if (confirm('Apakah Anda yakin ingin menghapus semua notifikasi?')) {
                    fetch('{{ route('notifications.deleteAll') }}', {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            document.querySelectorAll('#mobile-notification-dropdown ul li').forEach(li => li.remove());
                            alert(data.message);
                        } else {
                            alert('Gagal menghapus notifikasi.');
                        }
                    })
                    .catch(error => console.error('Error:', error));
                }
            });
        }
    });

    function showNotification(type, message) {
        const notificationContainer = document.getElementById('global-notifications');
        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        notification.innerHTML = `<span>${message}</span>`;
        notificationContainer.appendChild(notification);

        setTimeout(() => {
            notification.classList.add('fade-out');
            setTimeout(() => notification.remove(), 500); // Hapus setelah transisi selesai
        }, 3000); // Tampilkan selama 3 detik
    }

    document.addEventListener('DOMContentLoaded', function () {
        const notifications = document.querySelectorAll('.notification');
        notifications.forEach(notification => {
            setTimeout(() => {
                notification.classList.add('fade-out');
                setTimeout(() => notification.remove(), 500); // Hapus setelah transisi selesai
            }, 3000); // Tampilkan selama 3 detik
        });
    });

    document.addEventListener('DOMContentLoaded', function () {
    // Dropdown Event Desktop
    const eventToggle = document.getElementById('eventDropdownToggle');
    const eventDropdown = document.getElementById('eventDropdown');
    if (eventToggle && eventDropdown) {
      eventToggle.addEventListener('click', function (e) {
        e.stopPropagation();
        eventDropdown.classList.toggle('hidden');
      });
      // Tutup dropdown jika klik di luar
      document.addEventListener('click', function (e) {
        if (!eventDropdown.contains(e.target) && !eventToggle.contains(e.target)) {
          eventDropdown.classList.add('hidden');
        }
      });
    }
  });

  document.addEventListener('DOMContentLoaded', function () {
    // Dropdown Event Mobile
    const mobileEventToggle = document.getElementById('mobileEventDropdownToggle');
    const mobileEventDropdown = document.getElementById('mobileEventDropdown');
    const mobileEventChevron = document.getElementById('mobileEventChevron');
    if (mobileEventToggle && mobileEventDropdown) {
        mobileEventToggle.addEventListener('click', function (e) {
            e.stopPropagation();
            mobileEventDropdown.classList.toggle('hidden');
            // Putar chevron
            if (mobileEventChevron) {
                mobileEventChevron.classList.toggle('rotate-180', !mobileEventDropdown.classList.contains('hidden'));
            }
        });
        document.addEventListener('click', function (e) {
            if (!mobileEventDropdown.contains(e.target) && !mobileEventToggle.contains(e.target)) {
                mobileEventDropdown.classList.add('hidden');
                if (mobileEventChevron) {
                    mobileEventChevron.classList.remove('rotate-180');
                }
            }
        });
    }
});
  </script>
   @stack('scripts')
</body>

</html>
