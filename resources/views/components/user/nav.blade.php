<nav class="fixed top-0 z-50 w-full h-24 flex items-center justify-between px-6 md:px-14 bg-secondary">
    <div class="flex items-center gap-4">
        <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden p-2 rounded-md hover:bg-black/10 transition">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
            </svg>
        </button>

        <h1 class="font-bebas text-3xl md:text-5xl whitespace-nowrap">
            SHELLY <span class="hidden sm:inline">ADMIN PANEL</span>
        </h1>
    </div>

    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" data-cy="logout-button" class="font-bold text-warningPrimary text-sm md:text-base">
            Logout
        </button>
    </form>
</nav>