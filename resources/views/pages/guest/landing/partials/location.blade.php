<section class="max-w-7xl mx-auto px-4 py-20">
    <h2 class="text-4xl font-bold uppercase mb-12">Temukan Kami</h2>

    <div class="relative flex flex-col md:flex-row items-center gap-12 bg-white">
        <div class="relative p-2 border-2 border-[#F3C344] rounded-2xl w-full md:w-1/2">
            <div class="rounded-xl overflow-hidden h-[300px] bg-gray-100">
                {{-- Replace with Actual Google Maps Embed --}}
                <iframe
                    class="w-full h-full border-0"
                    allowfullscreen="" loading="lazy"
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d5040.910207810498!2d111.85567689999999!3d-8.0577917!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e791de24e5325fd%3A0x5f44d96cba1eb7ec!2sToko%20Shelly%20Seragam!5e1!3m2!1sen!2sid!4v1770983615296!5m2!1sen!2sid"  referrerpolicy="no-referrer-when-downgrade">
                </iframe>
            </div>
        </div>

        <div class="w-full md:w-1/2 space-y-6">
            <div>
                <h3 class="text-3xl font-bold">Shelly Seragam Tulungagung</h3>
                <p class="text-gray-600 mt-4 leading-relaxed">
                    Ruko Bolo Raya No. 16-17, Marangan, Bolorejo, Kec. Kauman, Kabupaten Tulungagung, Jawa Timur 66261
                </p>
            </div>

            <div class="flex flex-col gap-3">
                <x-shared.button variant="outline" class="justify-between text-sm">
                    Lihat di Google Maps <span>📍</span>
                </x-shared.button>
                <x-shared.button variant="outline" class="justify-between text-sm" href="https://maps.app.goo.gl/SM9DLWgfabvBqfbx8">
                    Hubungi Kami <span>💬</span>
                </x-shared.button>
            </div>
        </div>

        <div class="absolute top-1/2 -left-4 md:-left-8 transform -translate-y-1/2 cursor-pointer text-3xl opacity-50">❮</div>
        <div class="absolute top-1/2 -right-4 md:-right-8 transform -translate-y-1/2 cursor-pointer text-3xl opacity-50">❯</div>
    </div>
</section>