<x-guest-layout :showLogo="true">
    <div class="bg-white rounded-lg p-7 border border-gray-400 shadow-lg sm:w-[35rem] w-full h-[30rem] mx-auto flex flex-col justify-center">
        <div class="flex justify-center">    
             <x-application-logo class="w-28 h-28 fill-current text-black mb-4" />
        </div>
        <x-auth-session-status class="mb-4" :status="session('status')"/>

        <form method="POST" action="{{ route('login') }}" class="w-full">
            @csrf
            <!-- Email Address -->
            <div>
                <x-input-label for="email" :value="__('Email')" />
                <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <!-- Password -->
            <div class="mt-4">
                <x-input-label for="password" :value="__('Password')" />
                <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="current-password" />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <!-- Remember Me -->
            <div class="block mt-4">
                <label for="remember_me" class="inline-flex items-center">
                    <input id="remember_me" type="checkbox" class="rounded bg-white border-gray-300 text-black shadow-sm focus:ring-indigo-500" name="remember">
                    <span class="ms-2 text-sm text-black">{{ __('Remember me') }}</span>
                </label>
            </div>

            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mt-4">
                @if (Route::has('password.request'))
                    <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('password.request') }}">
                        {{ __('Forgot your password?') }}
                    </a>
                @endif

                <x-primary-button class="mt-3 sm:mt-0 sm:ms-3 w-full sm:w-auto">
                    {{ __('Log in') }}
                </x-primary-button>
            </div>
            <div class="mt-3">
               <a>Don't have an Account? <a href="{{route('register')}}" class="text-blue-500 hover:text-black">Sign up here!</a></a>
            </div>
        </form>
    </div>
    {{-- ✨ 🎨 --}}
</x-guest-layout>
