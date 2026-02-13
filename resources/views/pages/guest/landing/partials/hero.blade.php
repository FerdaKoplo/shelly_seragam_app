<section class="relative bg-secondary min-h-[600px] lg:min-h-[500px] lg:h-[600px] flex items-center overflow-hidden">
    {{-- Decorative Background Circle --}}
    <div class="absolute bottom-5 lg:left-[60%] sm:left-[40%] -translate-x-1/2 lg:left-50 bg-black rounded-full opacity-45 w-[15rem] h-[15rem] lg:w-[20rem] lg:h-[20rem]"></div>

    <div class="max-w-7xl mx-auto px-4 grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-8 py-12 items-center">
        {{-- Left Content --}}
        <div class="flex flex-col justify-center z-10 text-center lg:text-left">
            <h1 class="text-[80px] md:text-[120px] lg:text-[180px] font-black uppercase leading-[0.8] tracking-[-0.05em] text-black">
                SHELLY
            </h1>
            <h2 class="text-[34px] md:text-[50px] lg:text-[76px] font-medium uppercase tracking-[0.2em] lg:tracking-[0.35em] mt-2 text-black leading-none">
                SERAGAM
            </h2>

            <div class="mt-8 lg:mt-12 space-y-2 text-lg lg:text-xl">
                <p class="font-medium text-black/80">20 Tahun+ memberi kualitas</p>
                <p class="text-black/80">Cari seragam? <span class="font-bold text-black underline decoration-2 underline-offset-4">Di sini aja!</span></p>
            </div>
        </div>

        {{-- Right Content: The Stacked "Carousel" Effect --}}
        <div class="relative h-[450px] md:h-[550px] flex items-center justify-end lg:justify-end"
            x-data="{ 
                active: 0, 
                images: [
                    'https://images.unsplash.com/photo-1523240795612-9a054b0db644?q=80&w=1000',
                    'https://images.unsplash.com/photo-1509062522246-3755977927d7?q=80&w=1000',
                    'https://images.unsplash.com/photo-1522202176988-66273c2fd55f?q=80&w=1000'
                ],
                next() {
                    this.active = (this.active + 1) % this.images.length;
                },
                init() {
                    setInterval(() => this.next(), 5000);
                }
            }" 
            @mouseenter="paused = true"
            @mouseleave="paused = false">

            {{-- Main Image Card (The Rolling Loop) --}}
            <template x-for="(img, index) in images" :key="index">
                <div
                    class="absolute w-[85%] lg:w-3/4 sm:w-[85%] h-[95%] transition-all duration-700 ease-in-out cursor-pointer"
                    @click="next()"

                    {{-- Dynamic Styling for Depth --}}
                    :style="{
                    'z-index': active === index ? 30 : (active + 1) % images.length === index ? 20 : 10,
                    'transform': active === index 
                    ? 'rotate(0deg) translate(0, 0) scale(1)' 
                    : (active + 1) % images.length === index 
                        ? 'rotate(-2deg) translate(-20px, 10px) scale(0.98)' 
                        : 'rotate(4deg) translate(20px, 20px) scale(0.95)',
                    'opacity': active === index ? 1 : (active + 1) % images.length === index ? 0.8 : 0.4,
                    'filter': active === index ? 'none' : 'blur(1px) grayscale(0.5)'
                    }"
                    :class="active === index ? 'shadow-2xl' : 'shadow-lg'">
                    <div class="w-full h-full bg-white p-0 border-4 border-white overflow-hidden shadow-inner">
                        <img :src="img"
                            class="w-full h-full object-cover transition-all duration-700"
                            :class="active === index ? 'contrast-100' : 'contrast-75'">
                    </div>
                </div>

            </template>
            <!-- <div class="absolute top-5 -right-20 bg-[#E8A317] rounded-full" style="width: 12rem; height: 12rem;"></div> -->
            <div class="absolute -top-5 -right-5 lg:top-5 lg:-right-20 bg-[#E8A317] rounded-full w-24 h-24 lg:w-38 lg:h-38 z-40"></div>
        </div>


</section>