<button {{ $attributes->merge(['type' => 'submit', 'class' => 'text-center select-none border font-normal whitespace-no-wrap
           rounded-lg py-2 px-6 leading-normal no-underline bg-blue text-white hover:bg-dark_gray2']) }}>
    {{ $slot }}
</button>
