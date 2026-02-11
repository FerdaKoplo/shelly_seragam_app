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

        <form action=""
            class="bg-brand h-full w-[40%] border-r-primary border-r-[1.2rem] border-l-[1.2rem] border-l-black flex flex-col items-center justify-center px-10">
            <div class="space-y-6 w-full max-w-xs">
                <div class="flex items-start flex-col">
                    <label class="font-bold font-inter" for="username">Username :</label>
                    <input type="text" placeholder="Username"
                        class="flex-1 placeholder:text-[#D0D0D0] p-2 rounded-lg border-black border  outline-none w-full">
                </div>

                <div class="flex items-start flex-col">
                    <label class="font-bold font-inter" for="password">Password :</label>
                    <input placeholder="Password" type="password"
                        class="flex-1 placeholder:text-[#D0D0D0] p-2 rounded-lg border-black border  outline-none w-full">
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
@endsection