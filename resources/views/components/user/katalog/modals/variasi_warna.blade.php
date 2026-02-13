<x-shared.modal_base name="modal-color-variation" maxWidth="md">
    <div x-data="colorPicker()" x-init="init()" class="p-1">
        
        <div class="mb-5">
            <h3 class="font-bold text-lg mb-3">Warna</h3>
            <div class="grid grid-cols-4 gap-3 mb-3">
                <template x-for="color in presets" :key="color">
                    <button 
                        type="button"
                        @click="setColor(color)"
                        class="w-full aspect-[2/1] rounded-md transition hover:scale-105 hover:ring-2 ring-offset-1 ring-gray-300"
                        :style="`background-color: ${color}`">
                    </button>
                </template>
                
                <button type="button" class="w-full aspect-[2/1] border-2 border-dashed border-gray-400 rounded-md flex items-center justify-center hover:bg-gray-50 text-gray-500 font-bold text-xl">
                    +
                </button>
            </div>
        </div>

        <div class="bg-white p-3 shadow-[0_5px_5px_rgba(0,0,0,0.2)] border border-gray-100">
            <div class="flex gap-4 h-64">
                
                <div 
                    x-ref="sbBox"
                    @mousedown="startDrag($event, 'sb')"
                    @touchstart.prevent="startDrag($event, 'sb')"
                    class="relative flex-1 rounded-md cursor-crosshair overflow-hidden"
                    :style="`background-color: hsl(${hue}, 100%, 50%)`"
                >
                    <div class="absolute inset-0 bg-gradient-to-r from-white to-transparent"></div>
                    <div class="absolute inset-0 bg-gradient-to-t from-black to-transparent"></div>

                    <div 
                        class="absolute w-4 h-4 rounded-full border-2 border-white shadow-sm -translate-x-1/2 -translate-y-1/2 pointer-events-none"
                        :style="`left: ${saturation}%; top: ${100 - brightness}%; background-color: ${hex};`"
                    ></div>
                </div>

                <div class="relative w-4 h-full">
                    <div 
                        x-ref="hueSlider"
                        @mousedown="startDrag($event, 'hue')"
                        @touchstart.prevent="startDrag($event, 'hue')"
                        class="w-full h-full rounded-full cursor-pointer relative"
                        style="background: linear-gradient(to bottom, #ff0000 0%, #ffff00 17%, #00ff00 33%, #00ffff 50%, #0000ff 67%, #ff00ff 83%, #ff0000 100%);"
                    >
                        <div 
                            class="absolute left-1/2 -translate-x-1/2 w-6 h-6 rounded-full border-4 border-white bg-blue-600 shadow-md pointer-events-none"
                            :style="`top: ${(hue / 360) * 100}%;`"
                        ></div>
                    </div>
                </div>
            </div>
            
            <div class="mt-5 flex items-center justify-between gap-3">
                <div class="flex items-center gap-2">
                    <span class="text-sm font-bold text-gray-700">Hex</span>
                    <svg width="10" height="6" viewBox="0 0 10 6" fill="none" class="stroke-gray-500"><path d="M1 1L5 5L9 1" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </div>

                <div class="flex items-center gap-2 text-white bg-[#323232]  rounded-md p-1 flex-1">
                    <div class="w-6 h-6 rounded bg-blue-600 " :style="`background-color: ${hex}`"></div>
                    
                    <input 
                        type="text" 
                        x-model="hex" 
                        @input="updateFromHex()" class="bg-transparent border-none text-sm font-medium  px-2 py-1 rounded w-full focus:ring-0 uppercase tracking-wide"
                    >
                </div>
                
                <button 
                    @click="copyToClipboard()"
                    type="button" 
                    class="p-2 border border-gray-300 rounded-md hover:bg-gray-50 transition"
                >
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-gray-600">
                        <rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect>
                        <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path>
                    </svg>
                </button>
            </div>
        </div>

        <div class="mt-6">
            <button
                @click="submitColor()"
                type="button"
                class="w-full rounded-md bg-[#323232] py-3 text-sm font-medium text-white shadow-sm hover:opacity-90 focus:outline-none"
            >
                Simpan
            </button>
        </div>
    </div>

    <script>
        function colorPicker() {
            return {
                hue: 240,        // 0-360
                saturation: 100, // 0-100
                brightness: 50, // 0-100
                hex: '#1708FF',
                presets: ['#E65F5C', '#E2A056', '#261C1A', '#6B5BDE', '#5D9CEC', '#63D667'],
                isDragging: false,
                activeDrag: null,

                init() {
                    window.addEventListener('mouseup', () => this.stopDrag());
                    window.addEventListener('touchend', () => this.stopDrag());
                    window.addEventListener('mousemove', (e) => this.handleDrag(e));
                    window.addEventListener('touchmove', (e) => this.handleDrag(e));
                    
                    // Set initial color state
                    this.updateHex();
                },

                setColor(hexColor) {
                    this.hex = hexColor;
                    this.updateFromHex();
                },

                // ---- DRAG HANDLERS ----
                startDrag(event, type) {
                    this.isDragging = true;
                    this.activeDrag = type;
                    this.handleDrag(event); // Update immediately on click
                },

                stopDrag() {
                    this.isDragging = false;
                    this.activeDrag = null;
                },

                handleDrag(event) {
                    if (!this.isDragging) return;

                    // Support both mouse and touch events
                    const clientX = event.touches ? event.touches[0].clientX : event.clientX;
                    const clientY = event.touches ? event.touches[0].clientY : event.clientY;

                    if (this.activeDrag === 'sb') {
                        this.updateSB(clientX, clientY);
                    } else if (this.activeDrag === 'hue') {
                        this.updateHue(clientX, clientY);
                    }
                },

                // ---- LOGIC: Saturation & Brightness Box ----
                updateSB(x, y) {
                    const rect = this.$refs.sbBox.getBoundingClientRect();
                    let left = x - rect.left;
                    let top = y - rect.top;

                    // Clamp values to box dimensions
                    left = Math.max(0, Math.min(left, rect.width));
                    top = Math.max(0, Math.min(top, rect.height));

                    this.saturation = (left / rect.width) * 100;
                    this.brightness = 100 - ((top / rect.height) * 100);
                    
                    this.updateHex();
                },

                // ---- LOGIC: Hue Slider ----
                updateHue(x, y) {
                    const rect = this.$refs.hueSlider.getBoundingClientRect();
                    let top = y - rect.top;

                    // Clamp values
                    top = Math.max(0, Math.min(top, rect.height));

                    // 360 degrees
                    this.hue = (top / rect.height) * 360;
                    this.updateHex();
                },

                // ---- COLOR MATH (HSV to HEX) ----
                updateHex() {
                    const h = this.hue;
                    const s = this.saturation / 100;
                    const v = this.brightness / 100;

                    let r, g, b, i, f, p, q, t;
                    
                    // HSV to RGB conversion
                    i = Math.floor(h / 60); 
                    f = h / 60 - i;
                    p = v * (1 - s);
                    q = v * (1 - f * s);
                    t = v * (1 - (1 - f) * s);
                    
                    switch (i % 6) {
                        case 0: r = v, g = t, b = p; break;
                        case 1: r = q, g = v, b = p; break;
                        case 2: r = p, g = v, b = t; break;
                        case 3: r = p, g = q, b = v; break;
                        case 4: r = t, g = p, b = v; break;
                        case 5: r = v, g = p, b = q; break;
                    }

                    const toHex = x => {
                        const hex = Math.round(x * 255).toString(16);
                        return hex.length === 1 ? '0' + hex : hex;
                    };

                    this.hex = `#${toHex(r)}${toHex(g)}${toHex(b)}`.toUpperCase();
                },

                // ---- INPUT HANDLER (HEX to HSV) ----
                updateFromHex() {
                    let hex = this.hex.replace('#', '');
                    if (hex.length === 3) hex = hex.split('').map(c => c + c).join('');
                    if (hex.length !== 6) return;

                    const r = parseInt(hex.substring(0, 2), 16) / 255;
                    const g = parseInt(hex.substring(2, 4), 16) / 255;
                    const b = parseInt(hex.substring(4, 6), 16) / 255;

                    const max = Math.max(r, g, b);
                    const min = Math.min(r, g, b);
                    const d = max - min;
                    
                    let h, s, v = max;
                    s = max === 0 ? 0 : d / max;

                    if (max === min) h = 0;
                    else {
                        switch (max) {
                            case r: h = (g - b) / d + (g < b ? 6 : 0); break;
                            case g: h = (b - r) / d + 2; break;
                            case b: h = (r - g) / d + 4; break;
                        }
                        h *= 60;
                    }

                    this.hue = h;
                    this.saturation = s * 100;
                    this.brightness = v * 100;
                },

                copyToClipboard() {
                    navigator.clipboard.writeText(this.hex);
                },

                submitColor() {
                    console.log('Selected Color:', this.hex);
                    this.$dispatch('color-selected', this.hex);
                    this.closeModal();
                }
            };
        }
    </script>
</x-modal.base>