<div class="p-6 sm:px-20">
    <div class="grid gap-4 grid-cols-2">
        <div id="left_column">
            <div class="text-lg text-dark_gray2 leading-7 mb-4">Select and run a command</div>
            <ul class="list-style-cli text-3xl">
                @foreach ($artisanCommands as $id => $row)
                    <li><a class="text-base hover:text-green hover:underline active:text-blue
                            @if($previousCommandId == $id)
                            text-blue
                            @endif
                            " href="javascript:;"
                           wire:click="run('{{$id}}')">{{$row['title']}}</a></li>
                @endforeach
            </ul>
        </div>
        <div id="right_column">
            <div class="text-lg text-dark_gray2 leading-7 mb-4">Result</div>
            <div class="font-mono bg-black px-4 py-2 rounded
                @if ($artisanResult['type'] == 'error')
                text-red-700
                @else
                text-green
                @endif
                ">
                @if($artisanResult['title'])
                    {!! $artisanResult['title'] !!}
                @else
                    <p>Select and run the command from the left listing. Waiting and enjoy after!</p>
                @endif
            </div>
        </div>
    </div>
</div>
