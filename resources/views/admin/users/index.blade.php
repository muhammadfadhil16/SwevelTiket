@extends('layouts.adminLayout')

@section('title', 'User Management')

@section('content')
<div class="container mx-auto mt-8 px-4">
    <h1 class="text-2xl font-bold text-center text-gray-800 mb-6">Manajemen User</h1>


    <!-- Search and Filter -->
    <div class="flex flex-wrap justify-between items-center mb-6 gap-4">
        <!-- Search Form -->
        <form action="{{ route('users.index') }}" method="GET" class="flex flex-wrap gap-2 w-full md:w-auto">
            <input type="text" name="search" class="w-full md:w-64 px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500" placeholder="Search users..." value="{{ request()->get('search') }}">

            <!-- Filter by Role -->
            <select name="role" class="w-full md:w-48 px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                <option value="">All Roles</option>
                <option value="Admin" {{ request()->get('role') == 'Admin' ? 'selected' : '' }}>Admin</option>
                <option value="User" {{ request()->get('role') == 'User' ? 'selected' : '' }}>User</option>
            </select>

            <button type="submit" class="px-4 py-2  bg-slate-800 text-white rounded-md hover:bg-slate-700">
                <i class="bi bi-search"></i> Cari
            </button>
        </form>
    </div>

    <!-- Users Table -->
    <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-black uppercase bg-gray-200 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th scope="col" class="px-6 py-3 text-center">No</th>
                    <th scope="col" class="px-6 py-3">Username</th>
                    <th scope="col" class="px-6 py-3">Email</th>
                    <th scope="col" class="px-6 py-3">Role</th>
                    <th scope="col" class="px-6 py-3 text-center">Waktu dibuat</th>
                    <th scope="col" class="px-6 py-3 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @if($users->count() > 0)
                    @foreach ($users as $key => $user)
                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 border-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600">
                        <td class="px-6 py-4 text-center font-medium text-gray-900 whitespace-nowrap dark:text-white">
                            {{ $users->firstItem() + $key }}
                        </td>
                        <td class="px-6 py-4">{{ $user->name_user }}</td>
                        <td class="px-6 py-4">{{ $user->email_user }}</td>
                        <td class="px-6 py-4">{{ $user->role }}</td>
                        <td class="px-6 py-4 text-center">{{ $user->created_at->format('Y-m-d H:i:s') }}</td>
                        <td class="px-6 py-4 text-center">
                            <div class="flex flex-wrap justify-center gap-2">
                                <!-- Edit Button -->
                                <button class="px-2 py-1 bg-yellow-500 text-white rounded-md hover:bg-yellow-600" onclick="openModal('editModal{{ $user->id }}')">Ubah</button>

                                <!-- Delete Button -->
                                <button class="px-2 py-1 bg-red-600 text-white rounded-md hover:bg-red-700" onclick="openModal('deleteModal{{ $user->id }}')">Hapus</button>
                            </div>
                        </td>
                    </tr>

                    <!-- Edit Modal -->
                    <div id="editModal{{ $user->id }}" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50 hidden">
                        <div class="bg-white rounded-lg shadow-lg w-full max-w-md mx-4 sm:mx-auto transform scale-95 transition-transform duration-300 ease-in-out">
                            <!-- Modal Header -->
                            <div class="bg-blue-600 px-6 py-4 flex justify-between items-center">
                                <h5 class="text-lg font-bold text-white">Edit User</h5>
                                <button type="button" class="text-white hover:text-gray-200 focus:outline-none" onclick="closeModal('editModal{{ $user->id }}')">
                                    <i class="bi bi-x-lg text-2xl"></i>
                                </button>
                            </div>

                            <!-- Modal Body -->
                            <div class="p-6">
                                <form action="{{ route('users.update', $user->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="mb-4">
                                        <label for="name_user" class="block text-sm font-medium text-gray-700">Name</label>
                                        <input type="text" class="block w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500" id="name_user" name="name_user" value="{{ $user->name_user }}" required>
                                    </div>
                                    <div class="mb-4">
                                        <label for="email_user" class="block text-sm font-medium text-gray-700">Email</label>
                                        <input type="email" class="block w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500" id="email_user" name="email_user" value="{{ $user->email_user }}" required>
                                    </div>
                                    <div class="mb-4">
                                        <label for="role" class="block text-sm font-medium text-gray-700">Role</label>
                                        <select class="block w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500" id="role" name="role" required>
                                            <option value="Admin" {{ $user->role == 'Admin' ? 'selected' : '' }}>Admin</option>
                                            <option value="User" {{ $user->role == 'User' ? 'selected' : '' }}>User</option>
                                        </select>
                                    </div>
                                    <div class="flex justify-end gap-2">
                                        <button type="button" class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700" onclick="closeModal('editModal{{ $user->id }}')">Tutup</button>
                                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Simpan Perubahan</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Delete Modal -->
                    <div id="deleteModal{{ $user->id }}" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50 hidden">
                        <div class="bg-white rounded-lg shadow-lg w-full max-w-sm mx-4 sm:mx-auto transform scale-95 transition-transform duration-300 ease-in-out">
                            <!-- Modal Header -->
                            <div class="bg-red-600 px-6 py-4 flex justify-between items-center">
                                <h5 class="text-lg font-bold text-white">Delete User</h5>
                                <button type="button" class="text-white hover:text-gray-200 focus:outline-none" onclick="closeModal('deleteModal{{ $user->id }}')">
                                    <i class="bi bi-x-lg text-2xl"></i>
                                </button>
                            </div>

                            <!-- Modal Body -->
                            <div class="p-6">
                                <p class="text-gray-700">Apakah kamu yakin untuk menghapus user?</p>
                                <div class="flex justify-end gap-2 mt-4">
                                    <button type="button" class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700" onclick="closeModal('deleteModal{{ $user->id }}')">Cancel</button>
                                    <form action="{{ route('users.destroy', $user->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">Hapus</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                @else
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">No users found matching your search.</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $users->links('pagination::tailwind') }}
    </div>
</div>

<script>
    function openModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.remove('hidden');
            modal.classList.add('opacity-100', 'scale-100');
        }
    }

    function closeModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.add('hidden');
            modal.classList.remove('opacity-100', 'scale-100');
        }
    }

    // Close modal when clicking outside
    document.querySelectorAll('.fixed').forEach(modal => {
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                closeModal(modal.id);
            }
        });
    });
</script>
@endsection
