@extends('layouts.adminLayout')

@section('content')
<div class="container mx-auto mt-8 px-4">
    <div class="max-w-3xl mx-auto bg-white shadow-lg rounded-lg p-6">
        <h1 class="text-2xl font-bold text-gray-800 mb-6 text-center">Buat Tiket Baru</h1>


        <form action="{{ route('tickets.store') }}" method="POST">
            @csrf
            <input type="hidden" name="id_event" value="{{ $event->id_event }}">

            <!-- Pilihan Jenis Tiket -->
            <div class="mb-6">
                <label class="block text-gray-700 font-medium mb-2">Pilih Jenis Tiket</label>

                <!-- Regular Ticket -->
                <div class="flex items-center mb-2">
                    <input type="checkbox" name="types[]" value="Regular" id="type_regular" class="form-checkbox h-5 w-5 text-blue-600">
                    <label for="type_regular" class="ml-2 text-gray-700">Regular</label>
                </div>
                <div class="mb-4 hidden" id="regular_inputs">
                    <div class="border border-blue-300 rounded-lg p-4 bg-blue-50 flex flex-col sm:flex-row gap-4">
                        <input type="number" name="prices[Regular]" id="price_regular" class="form-input w-full border-gray-300 rounded-md" placeholder="Masukkan harga" min="0" disabled>
                        <input type="number" name="quantity[Regular]" id="quantity_regular" class="form-input w-full border-gray-300 rounded-md" placeholder="Masukkan kuantitas" min="1" disabled>
                    </div>
                </div>

                <!-- VIP Ticket -->
                <div class="flex items-center mb-2">
                    <input type="checkbox" name="types[]" value="VIP" id="type_vip" class="form-checkbox h-5 w-5 text-blue-600">
                    <label for="type_vip" class="ml-2 text-gray-700">VIP</label>
                </div>
                <div class="mb-4 hidden" id="vip_inputs">
                    <div class="border border-yellow-400 rounded-lg p-4 bg-yellow-50 flex flex-col sm:flex-row gap-4">
                        <input type="number" name="prices[VIP]" id="price_vip" class="form-input w-full border-gray-300 rounded-md" placeholder="Masukkan harga" min="0" disabled>
                        <input type="number" name="quantity[VIP]" id="quantity_vip" class="form-input w-full border-gray-300 rounded-md" placeholder="Masukkan kuantitas" min="1" disabled>
                    </div>
                </div>

                <!-- VVIP Ticket -->
                <div class="flex items-center mb-2">
                    <input type="checkbox" name="types[]" value="VVIP" id="type_vvip" class="form-checkbox h-5 w-5 text-blue-600">
                    <label for="type_vvip" class="ml-2 text-gray-700">VVIP</label>
                </div>
                <div class="mb-4 hidden" id="vvip_inputs">
                    <div class="border border-purple-400 rounded-lg p-4 bg-purple-50 flex flex-col sm:flex-row gap-4">
                        <input type="number" name="prices[VVIP]" id="price_vvip" class="form-input w-full border-gray-300 rounded-md" placeholder="Masukkan harga" min="0" disabled>
                        <input type="number" name="quantity[VVIP]" id="quantity_vvip" class="form-input w-full border-gray-300 rounded-md" placeholder="Masukkan kuantitas" min="1" disabled>
                    </div>
                </div>
            </div>

            <!-- Tombol Submit -->
            <div class="flex justify-between">
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 transition">Buat Tiket</button>
                <a href="{{ route('events.index') }}" class="bg-gray-600 text-white px-6 py-2 rounded-md hover:bg-gray-700 transition">Kembali</a>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const checkboxes = document.querySelectorAll('.form-checkbox');

        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const ticketType = this.value.toLowerCase();
                const inputsContainer = document.getElementById(`${ticketType}_inputs`);
                const priceInput = document.getElementById(`price_${ticketType}`);
                const quantityInput = document.getElementById(`quantity_${ticketType}`);

                if (this.checked) {
                    inputsContainer.classList.remove('hidden');
                    priceInput.disabled = false;
                    quantityInput.disabled = false;
                } else {
                    inputsContainer.classList.add('hidden');
                    priceInput.disabled = true;
                    quantityInput.disabled = true;
                    priceInput.value = '';
                    quantityInput.value = '';
                }
            });
        });
    });
</script>
@endsection
