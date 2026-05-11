<div>
    <h2 class="text-xl font-bold mb-4">Informasi Pelanggan</h2>
    <div class="space-y-3">
        <div>
            <input
                type="text"
                data-cy="input-full-name"
                name="full_name"
                placeholder="Nama Lengkap"
                x-model="customer.full_name"
                @input="delete errors.full_name"
                class="w-full bg-gray-100 border-none rounded-sm p-3 focus:ring-1 focus:ring-black">
            <p x-show="errors.full_name" data-cy="error-full-name" x-text="errors.full_name" class="mt-1 text-sm text-red-600"></p>
        </div>

        <div>
            <input
                type="email"
                name="email"
                placeholder="Alamat Email"
                data-cy="input-email"
                x-model="customer.email"
                @input="delete errors.email"
                class="w-full bg-gray-100 border-none rounded-sm p-3 focus:ring-1 focus:ring-black">
            <p x-show="errors.email" x-text="errors.email" data-cy="error-email" class="mt-1 text-sm text-red-600"></p>
        </div>

        <div>
            <input
                type="tel"
                name="phone"
                data-cy="input-phone"
                placeholder="Nomor Telepon"
                x-model="customer.phone"
                @input="delete errors.phone"
                class="w-full bg-gray-100 border-none rounded-sm p-3 focus:ring-1 focus:ring-black">
            <p x-show="errors.phone" data-cy="error-phone" x-text="errors.phone" class="mt-1 text-sm text-red-600"></p>
        </div>
    </div>
</div>