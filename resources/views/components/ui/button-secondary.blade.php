<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center px-6 py-2 bg-white border border-gray-300 rounded-lg font-normal text-gray-700 leading-normal hover:bg-gray-300 focus:outline-none focus:border-blue-300 focus:ring focus:ring-blue-200 active:text-gray-800 active:bg-gray-50 disabled:opacity-25 transition']) }}>
    {{ $slot }}
</button>
