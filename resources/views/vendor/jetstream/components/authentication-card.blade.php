<div
    class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100 bg-right-bottom bg-no-repeat">
    <div>
        {{ $logo }}
    </div>

    <div class="w-full sm:max-w-md mt-6 p-10 bg-white shadow-shadow3 overflow-hidden rounded-2xl">
        {{ $slot }}
    </div>
    <div class="text-center py-10">
            <a target="_blank" class="underline text-lg mr-4 text-blue hover:text-gray-900"
            href="{{url('/advisor-subscriber-terms-and-conditions')}}">
                {{ __('Advisor Terms & Conditions') }}
            </a>
            <a target="_blank"  class="underline text-lg text-blue hover:text-gray-900"
            href="{{url('/privacy-policy')}}">
                {{ __('Website Privacy policy') }}
            </a>
            <a target="_blank" class="underline text-lg ml-4 text-blue hover:text-gray-900"
            href="{{url('/client-subscriber-terms-and-conditions')}}">
                {{ __('Client Terms & Conditions') }}
            </a>
        </div>
</div>
