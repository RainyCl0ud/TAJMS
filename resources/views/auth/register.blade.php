<x-guest-layout :showLogo="false">
    <div class="max-h-screen overflow-auto">
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex flex-col md:flex-row items-center justify-center p-4 md:p-6 space-y-6 md:space-y-0 md:space-x-8 overflow-auto">
            <!-- Left Side: Logo & Description -->
            <div class="bg-blue-100 flex flex-col justify-center items-center text-center p-6 md:p-8 w-full md:w-1/2">
                <x-application-logo class="w-20 h-20 sm:w-24 sm:h-24 md:w-28 md:h-28 fill-current text-black mb-4" />
                <h2 class="text-2xl sm:text-3xl font-bold text-gray-800 mb-2 sm:mb-4">Welcome to TAJMS</h2>
                <h3 class="text-base sm:text-lg font-bold text-gray-800 mb-2 sm:mb-4">Trainees Attendance Journal Monitoring System</h3>
                <p class="text-gray-600 leading-relaxed text-xs sm:text-sm md:text-base">
                    Create an account to access all features.  
                    Stay connected, manage your activities, and have a wonderful On-The-Job-training experience.
                </p>
            </div>

            <!-- Right Side: Registration Form -->
            <div class="bg-white rounded-lg shadow-lg border border-gray-400 p-6 mb-10 sm:p-8 w-full md:w-1/2">
                <form method="POST" action="{{ route('register') }}">
                    @csrf
                    <h2 class="text-xl sm:text-2xl font-bold text-gray-800 text-center mb-4 sm:mb-6">Register an Account</h2>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                        <!-- First Name -->
                        <div>
                            <x-input-label for="first_name" :value="__('First Name')" />
                            <x-text-input id="first_name" class="block mt-1 w-full" type="text" name="first_name" :value="old('first_name')" required autofocus autocomplete="given-name" />
                            <x-input-error :messages="$errors->get('first_name')" class="mt-2" />
                        </div>

                        <!-- Middle Name -->
                        <div>
                            <x-input-label for="middle_name" :value="__('Middle Name')" />
                            <x-text-input id="middle_name" class="block mt-1 w-full" type="text" name="middle_name" :value="old('middle_name')" autocomplete="additional-name" />
                            <x-input-error :messages="$errors->get('middle_name')" class="mt-2" />
                        </div>

                        <!-- Last Name -->
                        <div>
                            <x-input-label for="last_name" :value="__('Last Name')" />
                            <x-text-input id="last_name" class="block mt-1 w-full" type="text" name="last_name" :value="old('last_name')" required autocomplete="family-name" />
                            <x-input-error :messages="$errors->get('last_name')" class="mt-2" />
                        </div>

                        <!-- Student ID -->
                        <div>
                            <x-input-label for="student_id" :value="__('Student ID')" />
                            <x-text-input id="student_id" class="block mt-1 w-full" type="text" name="student_id" :value="old('student_id')" required autocomplete="student_id" />
                            <x-input-error :messages="$errors->get('student_id')" class="mt-2" />
                        </div>

                        <!-- Email -->
                        <div>
                            <x-input-label for="email" :value="__('Email')" />
                            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        <!-- Contact Number -->
                        <div>
                            <x-input-label for="contact_number" :value="__('Contact Number')" />
                            <x-text-input id="contact_number" class="block mt-1 w-full" type="text" name="contact_number" :value="old('contact_number')" required />
                            <x-input-error :messages="$errors->get('contact_number')" class="mt-2" />
                        </div>

                        <!-- Course -->
                        <div>
                            <x-input-label for="course" :value="__('Course')" />
                            <x-text-input id="course" class="block mt-1 w-full" type="text" name="course" :value="old('course')" required />
                            <x-input-error :messages="$errors->get('course')" class="mt-2" />
                        </div>

                        <!-- Password -->
                        <div>
                            <x-input-label for="password" :value="__('Password')" />
                            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
                            <x-input-error :messages="$errors->get('password')" class="mt-2" />
                        </div>

                        <!-- Confirm Password -->
                        <div>
                            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
                            <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
                            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row items-center justify-between mt-6">
                        <a class="underline font-bold text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('login') }}">
                            {{ __('Already registered?') }}
                        </a>

                        <x-primary-button class="mt-4 sm:mt-0 sm:ml-4 w-full sm:w-auto">
                            {{ __('Register') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </body>
</div>
</x-guest-layout>
