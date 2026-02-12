@extends('layouts.auth.layout')
@section('title', 'Login')
@section('content')
    <div class="text-black h-screen flex items-center justify-between pl-36 overflow-hidden">

        <div class="relative">
            <div class="font-bebas flex gap-0 flex-col items-start">
                <div class="flex flex-col">
                    <h1 class="text-[208px] leading-[0.8]">SHELLY</h1>
                    <h1 class="text-8xl tracking-[0.3em] leading-none">SERAGAM</h1>
                </div>
                <p class="text-5xl tracking-[0.2em] mt-8">ADMIN PANEL</p>
            </div>

            <div class="absolute -z-10 w-[16rem] h-[16rem] rounded-full bg-brand -top-40 -left-[5.5rem]"></div>

            <div class="absolute -z-10 w-28 h-28 rounded-full bg-primary -bottom-20 right-8"></div>
        </div>

        <form action="{{ route('login') }}" method="POST"
            class="bg-brand h-full w-[40%] border-r-primary border-r-[1.2rem] border-l-[1.2rem] border-l-black flex flex-col items-center justify-center px-10">
            @csrf
            <div class="space-y-6 w-full max-w-xs">
                <div class="flex items-start flex-col">
                    <label class="font-bold font-inter" for="username">Username :</label>

                    <div class="flex items-center w-full gap-5">
                        <input type="text" placeholder="Username"
                            class="flex-1 placeholder:text-[#D0D0D0] p-2 rounded-lg border-black border  outline-none w-full">
                        <svg width="19" height="19" viewBox="0 0 19 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M9.21457 9.21457C7.94756 9.21457 6.86293 8.76344 5.96067 7.86118C5.05841 6.95892 4.60728 5.87429 4.60728 4.60728C4.60728 3.34028 5.05841 2.25565 5.96067 1.35339C6.86293 0.45113 7.94756 0 9.21457 0C10.4816 0 11.5662 0.45113 12.4685 1.35339C13.3707 2.25565 13.8218 3.34028 13.8218 4.60728C13.8218 5.87429 13.3707 6.95892 12.4685 7.86118C11.5662 8.76344 10.4816 9.21457 9.21457 9.21457ZM0 18.4291V15.204C0 14.5513 0.168166 13.9516 0.504498 13.4049C0.840829 12.8582 1.28697 12.4404 1.84291 12.1517C3.03313 11.5566 4.24254 11.1105 5.47115 10.8133C6.69976 10.5161 7.94756 10.3672 9.21457 10.3664C10.4816 10.3656 11.7294 10.5146 12.958 10.8133C14.1866 11.112 15.396 11.5581 16.5862 12.1517C17.1429 12.4397 17.5895 12.8574 17.9258 13.4049C18.2621 13.9524 18.4299 14.5521 18.4291 15.204V18.4291H0Z"
                                fill="black" />
                        </svg>
                    </div>
                </div>

                <div class="flex items-start flex-col">
                    <label class="font-bold font-inter" for="password">Password :</label>

                    <div class="flex items-center w-full gap-5">
                        <input id="passwordInput" placeholder="Password" type="password"
                            class="flex-1 placeholder:text-[#D0D0D0] p-2 rounded-lg border-black border  outline-none w-full">
                        <button type="button" onclick="togglePassword()" class="focus:outline-none">

                            <svg id="eyeOpen" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                class="size-6">
                                <path d="M12 15a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z" />
                                <path fill-rule="evenodd"
                                    d="M1.323 11.447C2.811 6.976 7.028 3.75 12.001 3.75c4.97 0 9.185 3.223 10.675 7.69.12.362.12.752 0 1.113-1.487 4.471-5.705 7.697-10.677 7.697-4.97 0-9.186-3.223-10.675-7.69a1.762 1.762 0 0 1 0-1.113ZM17.25 12a5.25 5.25 0 1 1-10.5 0 5.25 5.25 0 0 1 10.5 0Z"
                                    clip-rule="evenodd" />
                            </svg>

                            <svg id="eyeClosed" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                class="size-6 hidden">
                                <path
                                    d="M3.53 2.47a.75.75 0 0 0-1.06 1.06l18 18a.75.75 0 1 0 1.06-1.06l-18-18ZM22.676 12.553a11.249 11.249 0 0 1-2.631 4.31l-3.099-3.099a5.25 5.25 0 0 0-6.71-6.71L7.759 4.577a11.217 11.217 0 0 1 4.242-.827c4.97 0 9.185 3.223 10.675 7.69.12.362.12.752 0 1.113Z" />
                                <path
                                    d="M15.75 12c0 .18-.013.357-.037.53l-4.244-4.243A3.75 3.75 0 0 1 15.75 12ZM12.53 15.713l-4.243-4.244a3.75 3.75 0 0 0 4.244 4.243Z" />
                                <path
                                    d="M6.75 12c0-.619.107-1.213.304-1.764l-3.1-3.1a11.25 11.25 0 0 0-2.63 4.31c-.12.362-.12.752 0 1.114 1.489 4.467 5.704 7.69 10.675 7.69 1.5 0 2.933-.294 4.242-.827l-2.477-2.477A5.25 5.25 0 0 1 6.75 12Z" />
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="flex justify-center pt-4">
                    <button type="submit"
                        class="bg-neutral w-full text-white px-12 py-4 rounded-lg uppercase tracking-widest font-semibold">
                        Login
                    </button>
                </div>
            </div>
        </form>
    </div>


    <script>
        const togglePassword = () => {
            const passwordInput = document.getElementById('passwordInput');
            const eyeOpen = document.getElementById('eyeOpen');
            const eyeClosed = document.getElementById('eyeClosed');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeOpen.classList.add('hidden');
                eyeClosed.classList.remove('hidden');
            } else {
                passwordInput.type = 'password';
                eyeOpen.classList.remove('hidden');
                eyeClosed.classList.add('hidden');
            }
        }
    </script>
@endsection