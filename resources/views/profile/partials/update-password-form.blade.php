<section class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
    <header class="mb-6 text-center sm:text-left">
        <h2 class="text-lg sm:text-xl font-medium text-black"> 
            {{ __('Update Password') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600"> 
            {{ __('Ensure your account is using a long, random password to stay secure.') }}
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('put')

        <div>
            <x-input-label class="text-black" for="update_password_current_password" :value="__('Current Password')" /> 
            <x-text-input id="update_password_current_password" name="current_password" type="password" class="mt-1 block w-full" autocomplete="current-password" />
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
        </div>

        <div>
            <x-input-label class="text-black" for="update_password_password" :value="__('New Password')" /> 
            <x-text-input id="update_password_password" name="password" type="password" class="mt-1 block w-full" autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
        </div>

        <div>
            <x-input-label class="text-black" for="update_password_password_confirmation" :value="__('Confirm Password')" /> 
            <x-text-input id="update_password_password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full" autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
        </div>

        <!-- Button and Success Message -->
        <div class="flex flex-col sm:flex-row items-center gap-4">
            <x-primary-button class="w-full sm:w-auto flex justify-center items-center">
                {{ __('Save') }}
            </x-primary-button>
        
            @if (session('status') === 'password-updated')
                <p x-data="{ show: true }" x-show="show" x-transition 
                   x-init="setTimeout(() => show = false, 2000)" class="text-sm text-black">
                    {{ __('Saved.') }}
                </p>
            @endif
        </div>
        
    </form>
</section>
