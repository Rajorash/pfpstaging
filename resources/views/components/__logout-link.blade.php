<a class="block w-full py-1 px-6 font-normal text-gray-900 whitespace-no-wrap border-0" href="{{ route('logout') }}"
onclick="event.preventDefault();
              document.getElementById('logout-form').submit();">
 {{ $slot }}
</a>

<form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
 @csrf
</form>
