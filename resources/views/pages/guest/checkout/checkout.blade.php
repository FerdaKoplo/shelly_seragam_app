@extends('layouts.guest.layout')
@section('title', 'Checkout')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-7xl"
    x-cloak
    x-init="init()"
    x-data="checkoutPage({{ Js::from([
        'type' => $type,
        'items' => $items,
        'customData' => $customData,
        'shippingOptions' => $shippingOptions,
        'shippingMethod' => old('shipping_id'),
        'notes' => old('notes', $checkoutNotes),
        'customer' => [
            'full_name' => old('full_name', ''),
            'email' => old('email', ''),
            'phone' => old('phone', ''),
        ],
        'address' => [
            'address' => old('address', ''),
            'city' => old('city', ''),
            'province' => old('province', ''),
            'postal_code' => old('postal_code', ''),
        ],
        'errors' => $errors->toArray(),
    ]) }})">

    <form
        x-ref="checkoutForm"
        method="POST"
        action="{{ route('checkout') }}"
        @submit.prevent="submitOrder()">
        @csrf
        <input type="hidden" name="type" value="{{ $type }}">

    {{-- Header with Back Button --}}
    <div class="flex items-center gap-4 mb-12">
        <a href="javascript:history.back()" class="hover:opacity-50 transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
            </svg>
        </a>
        <h1 class="text-6xl font-bebas tracking-widest uppercase">Checkout</h1>
    </div>

    {{-- Main Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-16 items-start">

        {{-- LEFT COLUMN: Forms --}}
        <div class="lg:col-span-6 space-y-12">
            @include('pages.guest.checkout.partials.customer-info')
            @include('pages.guest.checkout.partials.shipping-address')

            {{-- Only show Xendit box for Katalog/Standard checkout --}}
            <div x-show="type === 'katalog'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform -translate-y-2">
                @include('pages.guest.checkout.partials.payment-method')
            </div>
        </div>

        {{-- RIGHT COLUMN: Order Content & Summary --}}
        <div class="lg:col-span-6 space-y-10">

            {{-- Product Items (For Katalog) --}}
            <template x-if="type === 'katalog'">
                <div class="space-y-6 max-h-[60vh] overflow-y-auto pr-2 no-scrollbar">
                    <template x-for="(item, index) in items" :key="item.id">
                        <x-cards.product-card.cart-item />
                    </template>
                </div>
            </template>

            {{-- Custom Card (For Kustom) --}}
            <template x-if="type === 'kustom'">
                @include('pages.guest.checkout.partials.custom-summary')
            </template>

            {{-- Summary & Totals --}}
            <div class="pt-6 border-t border-gray-100">
                @include('pages.guest.checkout.partials.order-footer')
            </div>
        </div>
    </div>
</div>

    <x-guest.katalog.modals.panduan-ukuran />

<script>
    function checkoutPage(initial) {
        return {
            type: initial.type,
            items: initial.items || [],
            customData: initial.customData || null,
            shippingOptions: initial.shippingOptions || [],
            isSubmitting: false,
            isConfirming: false,
            isLoadingDestinations: false,
            destinationQuery: "",
            destinationId: null,
            destinationResults: [],
            shippingMethod: initial.shippingMethod || null,
            shippingCost: 0,
            notes: initial.notes || "",
            customer: initial.customer || { full_name: "", email: "", phone: "" },
            address: initial.address || { address: "", city: "", province: "", postal_code: "" },
            errors: initial.errors || {},

            init() {
                this.flattenErrors();
                if (this.shippingMethod) {
                    const option = this.shippingOptions.find((o) => o.id === this.shippingMethod);
                    if (option) this.selectShipping(option);
                }
            },

            flattenErrors() {
                for (const key in this.errors) {
                    if (Array.isArray(this.errors[key])) {
                        this.errors[key] = this.errors[key][0];
                    }
                }
            },

            selectShipping(option) {
                this.shippingMethod = option.id;
                this.shippingCost = option.price;
            },

            get subtotal() {
                if (this.type === "katalog") {
                    return this.items.reduce((sum, item) => sum + (item.price * item.quantity), 0);
                }
                return this.customData ? this.customData.price : 0;
            },

            get total() {
                return this.subtotal + this.shippingCost;
            },

            formatCurrency(num) {
                return "Rp" + num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
            },

            validateCheckout() {
                const errors = {};

                const fullName = (this.customer.full_name || "").trim();
                const email = (this.customer.email || "").trim();
                const phone = (this.customer.phone || "").trim();

                const address = (this.address.address || "").trim();
                const province = (this.address.province || "").trim();
                const postal = (this.address.postal_code || "").trim();

                if (!fullName) errors.full_name = "Nama lengkap wajib diisi.";
                if (!email) errors.email = "Email wajib diisi.";
                else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) errors.email = "Format email tidak valid.";

                if (!phone) errors.phone = "Nomor telepon wajib diisi.";
                else if (!/^[0-9+\-\s]{8,20}$/.test(phone)) errors.phone = "Nomor telepon tidak valid.";

                if (!address) errors.address = "Alamat jalan wajib diisi.";
                if (!province) errors.province = "Provinsi wajib diisi.";
                if (!postal) errors.postal_code = "Kode pos wajib diisi.";
                else if (!/^[0-9]{4,6}$/.test(postal)) errors.postal_code = "Kode pos tidak valid.";
                if (!this.destinationId) errors.city = "Pilih kota/kecamatan dari pencarian (RajaOngkir).";

                if (!this.shippingMethod) errors.shipping_id = "Pilih opsi pengiriman.";

                this.errors = errors;
                return Object.keys(errors).length === 0;
            },

            get isAddressValid() {
                const address = (this.address.address || "").trim();
                const postal = (this.address.postal_code || "").trim();

                if (!address || address.length < 5) return false;
                if (!postal || !/^[0-9]{4,6}$/.test(postal)) return false;
                if (!this.destinationId) return false;

                return true;
            },

            async searchDestinations() {
                const q = (this.destinationQuery || "").trim();
                this.destinationId = null;
                this.destinationResults = [];
                if (q.length < 2) return;

                this.isLoadingDestinations = true;
                try {
                    const resp = await fetch(`{{ route('shipping.destinations') }}?search=${encodeURIComponent(q)}`, {
                        headers: { "Accept": "application/json" },
                    });
                    const json = await resp.json().catch(() => ({}));
                    this.destinationResults = Array.isArray(json.data) ? json.data : [];
                } catch (e) {
                    this.destinationResults = [];
                } finally {
                    this.isLoadingDestinations = false;
                }
            },

            selectDestination(dest) {
                const id = dest?.id ?? dest?.destination_id ?? null;
                const label = dest?.label ?? dest?.name ?? dest?.city_name ?? dest?.subdistrict_name ?? "";
                const province = dest?.province_name ?? dest?.province ?? "";
                const postal =
                    dest?.postal_code ??
                    dest?.postalCode ??
                    dest?.zip ??
                    dest?.zip_code ??
                    dest?.zipCode ??
                    "";

                this.destinationId = id ? parseInt(id, 10) : null;
                this.destinationQuery = label || this.destinationQuery;
                this.address.city = label || this.address.city;
                this.address.province = province || this.address.province;
                if (postal) this.address.postal_code = String(postal);
                this.destinationResults = [];

                if (this.destinationId) {
                    this.loadShippingOptions();
                }
            },

            get totalWeight() {
                if (this.type !== "katalog") return 1000;
                const qty = this.items.reduce((sum, item) => sum + (item.quantity || 0), 0);
                return Math.max(1, qty) * 1000;
            },

            async loadShippingOptions() {
                if (!this.destinationId) return;
                try {
                    const resp = await fetch(`{{ route('shipping.cost') }}`, {
                        method: "POST",
                        headers: {
                            "Accept": "application/json",
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": "{{ csrf_token() }}",
                        },
                        body: JSON.stringify({
                            destination: this.destinationId,
                            weight: this.totalWeight,
                            courier: "jne",
                        }),
                    });

                    const json = await resp.json().catch(() => ({}));
                    const raw = Array.isArray(json.data) ? json.data : [];

                    const mapped = [];
                    for (const courier of raw) {
                        const courierName = courier?.name || courier?.courier || "Kurir";
                        const costs = courier?.costs || courier?.cost || courier?.services || [];
                        if (!Array.isArray(costs)) continue;

                        for (const c of costs) {
                            const service = c?.service || c?.name || c?.code || "Service";
                            const costArr = c?.cost || c?.costs || [];
                            const firstCost = Array.isArray(costArr) ? costArr[0] : null;
                            const value = firstCost?.value ?? c?.value ?? null;
                            const etd = firstCost?.etd ?? c?.etd ?? "";
                            if (typeof value !== "number" && typeof value !== "string") continue;

                            mapped.push({
                                id: `${courierName}-${service}`.toLowerCase().replace(/\s+/g, "-"),
                                label: `${courierName} ${service}`.trim(),
                                duration: etd ? `${etd}` : null,
                                price: parseInt(value, 10) || 0,
                            });
                        }
                    }

                    if (mapped.length > 0) {
                        this.shippingOptions = mapped;
                        this.shippingMethod = null;
                        this.shippingCost = 0;
                    }
                } catch (e) {
                    // keep existing options
                }
            },

            async submitOrder() {
                if (!this.validateCheckout()) return;
                if (this.isSubmitting || this.isConfirming) return;

                this.isConfirming = true;
                const confirmed = window.confirm("Apakah data yang dimasukkan sudah benar?");
                this.isConfirming = false;
                if (!confirmed) return;

                this.isSubmitting = true;
                this.$refs.checkoutForm.submit();
            }
        };
    }
</script>
    </form>
@endsection
