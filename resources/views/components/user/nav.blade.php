<nav class="fixed top-0 z-50 w-full h-24 flex items-center justify-between px-14 bg-secondary">
    <h1 class="font-bebas text-5xl">
        SHELLY ADMIN PANEL
    </h1>

    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" data-cy="logout-button" class="font-bold text-warningPrimary">
            Logout
        </button>
    </form>
</nav>