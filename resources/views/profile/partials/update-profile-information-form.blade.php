<section class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
    <header class="mb-6 text-center sm:text-left">
        <div class="flex flex-col sm:flex-row items-center sm:items-start space-y-4 sm:space-y-0 sm:space-x-6">
            @php
                $url = Auth::user()->profile_picture;
                $fileId = null;

                // Try to extract id from any format
                if (str_contains($url, 'id=')) {
                    parse_str(parse_url($url, PHP_URL_QUERY), $query);
                    $fileId = $query['id'] ?? null;
                } elseif (preg_match('/\/d\/(.*?)\//', $url, $matches)) {
                    $fileId = $matches[1];
                }

                $finalImageUrl = $fileId ? "https://drive.google.com/thumbnail?id={$fileId}" : null;
            @endphp

            <div class="h-24 w-24 relative rounded-full overflow-hidden border border-black shadow-lg">
                @if($finalImageUrl)
                    <img src="{{ $finalImageUrl }}"
                         class="w-full h-full object-cover"
                         alt="{{ Auth::user()->first_name }}'s Profile Picture"
                         onerror="this.onerror=null;this.src='{{ asset('storage/profile_pictures/default.png') }}';">
                @else
                    <img src="{{ asset('storage/profile_pictures/default.png') }}"
                         class="w-full h-full object-cover"
                         alt="{{ Auth::user()->first_name }}'s Profile Picture">
                @endif
            </div>

            <div>
                <h2 class="text-lg sm:text-xl font-medium text-black">
                    {{ __('Profile Information') }}
                </h2>

                <p class="mt-1 text-sm text-gray-600">
                    {{ __("Update your account's profile information, email address, and profile picture.") }}
                </p>
            </div>
        </div>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div class="flex flex-col sm:flex-row items-center sm:items-start space-y-4 sm:space-y-0 sm:space-x-6">
            <div class="w-full sm:w-auto">
                <label for="profile_picture" class="block text-black font-medium">
                    {{ __('Change Profile Picture') }}
                </label>
                <input id="profile_picture" name="profile_picture" type="file" class="mt-1 block w-full text-sm" accept="image/*">
                <x-input-error class="mt-2" :messages="$errors->get('profile_picture')" />
            </div>
        </div>

        <!-- Name Fields (Stack on small screens, grid on larger screens) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            <div>
                <label for="first_name" class="block text-black font-medium">
                    {{ __('First Name') }}
                </label>
                <x-text-input id="first_name" name="first_name" type="text" class="mt-1 block w-full" 
                              :value="old('first_name', $user->first_name)" required autofocus autocomplete="given-name" />
                <x-input-error class="mt-2" :messages="$errors->get('first_name')" />
            </div>

            <div>
                <label for="middle_name" class="block text-black font-medium">
                    {{ __('Middle Name') }}
                </label>
                <x-text-input id="middle_name" name="middle_name" type="text" class="mt-1 block w-full" 
                              :value="old('middle_name', $user->middle_name)" required autocomplete="additional-name" />
                <x-input-error class="mt-2" :messages="$errors->get('middle_name')" />
            </div>

            <div>
                <label for="last_name" class="block text-black font-medium">
                    {{ __('Last Name') }}
                </label>
                <x-text-input id="last_name" name="last_name" type="text" class="mt-1 block w-full" 
                              :value="old('last_name', $user->last_name)" required autocomplete="family-name" />
                <x-input-error class="mt-2" :messages="$errors->get('last_name')" />
            </div>
        </div>

        <!-- Email Field -->
        <div>
            <x-input-label class="text-black" for="email" :value="__('Email')" /> 
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" 
                          :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />
        </div>

        <!-- Buttons and Status Message -->
        <div class="flex flex-col sm:flex-row items-center gap-4">
            <x-primary-button class="w-full sm:w-auto flex justify-center items-center">
                {{ __('Save') }}
            </x-primary-button>
        
            @if (session('status') === 'profile-updated')
                <p x-data="{ show: true }" x-show="show" x-transition 
                   x-init="setTimeout(() => show = false, 2000)" class="text-sm text-black">
                    {{ __('Saved.') }}
                </p>
            @endif
        </div>        
    </form>
</section>
