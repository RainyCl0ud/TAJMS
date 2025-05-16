<x-app-layout>
    <x-slot name="pageTitle">
        Requests
    </x-slot>

    <div class="py-6 px-4 sm:px-6 lg:px-8">
        <div class="space-y-4">
            <x-request-list :requests="$requests" />
        </div>
    </div>

    <x-request-modal />
</x-app-layout> 