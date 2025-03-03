@extends('layouts.app')
@include('components.header')
@section('content')
    <div class="py-12 max-h-screen overflow-y-auto bg-blue-100">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 mb-20">
            <div class="p-4 sm:p-8 bg-white border border-gray-200 shadow-lg shadow-black sm:rounded-lg text-black mb-10">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white border border-gray-200 shadow-lg shadow-black sm:rounded-lg text-black mb-10">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>
        </div>
    </div>
@endsection




