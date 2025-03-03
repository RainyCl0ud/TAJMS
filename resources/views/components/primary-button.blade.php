<button {{ $attributes->merge([
    'type' => 'submit',
    'class' => 'inline-flex justify-center items-center 
                px-4 py-2 bg-blue-600 text-white 
                hover:bg-gray-800 active:bg-gray-700
                border border-transparent rounded-md 
                font-medium text-sm uppercase tracking-wide 
                focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 
                transition-all ease-in-out duration-200 
                shadow-sm hover:shadow-md active:shadow-inner 
                active:scale-95 transform'
]) }}>
    {{ $slot }}
</button>
