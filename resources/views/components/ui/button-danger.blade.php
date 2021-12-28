<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center px-6 py-2 bg-red-500 border border-red-300 rounded-lg font-normal text-white leading-normal hover:bg-red-800 focus:outline-none focus:border-red-300 focus:ring focus:ring-red-200 active:text-red-800 active:bg-red-600 disabled:opacity-25 transition']) }}>
    {{ $slot }}
</button>
