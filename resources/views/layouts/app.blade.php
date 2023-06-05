<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Mints CD Consulting') }} :: {!! isset($titleHeader) ? $titleHeader : (isset($header) ? strip_tags($header) : '') !!}</title>

    <link rel="apple-touch-icon" sizes="180x180" href="{{url('/favicons/apple-touch-icon.png')}}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{url('/favicons/favicon-32x32.png')}}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{url('/favicons/favicon-16x16.png')}}">
    <link rel="manifest" href="{{url('/favicons/site.webmanifest')}}">
    <link rel="mask-icon" href="{{url('/favicons/safari-pinned-tab.svg')}}" color="#3e04e2">
    <link rel="shortcut icon" href="{{url('/favicons/favicon.ico')}}">
    <meta name="msapplication-TileColor" content="#3e04e2">
    <meta name="msapplication-config" content="{{url('/favicons/browserconfig.xml')}}">
    <meta name="theme-color" content="#ffffff">

    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Questrial&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{ mix('css/app.css') }}">
    <link rel="stylesheet" href="{{ mix('css/all.css') }}">

@livewireStyles
<style>
    #livewire-error {
        display:none;
    }
</style>

    <script src="{{ mix('js/app.js') }}" defer></script>
    <script src="https://cdn.tailwindcss.com"></script>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>

</head>
<body class="relative font-sans antialiased text-light_gray">

<div class="min-h-screen bg-right-bottom bg-no-repeat bg-light_purple2"
     >
    <div class="bg-white shadow-shadow4">
        <livewire:navigation-menu/>
    </div>

    <!-- Page Heading -->
    @if(isset($header))
        <header class="px-4 py-2 mx-auto max-w-7xl sm:px-6 lg:px-8 relative">
            <div class="flex content-between">
                <h2 class="mt-8 text-4xl font-normal leading-tight text-dark_gray2">
                    {{ $header }}
                </h2>
                @if(isset($subMenu))
                    {{$subMenu}}
                @endif
            </div>
            @if(isset($subHeader))
                <div class="py-2">
                    {{$subHeader}}
                </div>
            @endif
        </header>
    @endif

    {{ $slot }}

</div>

<div id="delay_progress" class="fixed z-50 h-1 rounded bg-blue bottom-2 right-4 sm:right-6 lg:right-8"></div>

@stack('modals')
@livewire('livewire-ui-modal')
@livewireScripts
{{-- commented below line, removes small box at bottom of layout. --}}
{{-- <div id="ddd" class="absolute z-50 w-2 h-2 bg-red-600"></div> --}}
</body>
<script type="text/javascript" src="{{ url('js/table-to-excel/dist/tableToExcel.js') }}"></script>
    <script>
         function exportTableToExcel(){
            var inputval = [];
            $('tbody td').each(function() {
                if($(this).find('input').val() !== undefined){
                     $(this).append('<div style="display:none;">'+$(this).find('input').val()+'</div>');
                }
            })

            // console.log(inputval);
            TableToExcel.convert(document.getElementById("projectionsTablePlace"), {
            name: "projection.xlsx",
            sheet: {
                name: "Sheet 1"
            }
            });
         }

         function exportTableExpenseExcel(){
            var inputval = [];
            $('tbody td').each(function() {
                if($(this).find('input').val() !== undefined){
                     $(this).append('<div style="display:none;">'+$(this).find('input').val()+'</div>');
                }
            })

            // console.log(inputval);
            TableToExcel.convert(document.getElementById("allocationsNewTablePlace"), {
            name: "expense.xlsx",
            sheet: {
                name: "Sheet 1"
            }
            });
         }
</script>
<script type="text/javascript" >
        
    $(document).on("keydown",'body', function (e) {
      if(e.altKey){
         $('.pfp_copy_move_element').removeClass('pfp_copy_move_element bg-yellow-300');
      }
    });

</script>
<script>
// var isdraggable = true;
// document.querySelectorAll('[drag-input]').forEach(el => {
// });

function mouseDown(){
    document.querySelectorAll('[drag-root]').forEach(el => {
        el.addEventListener('dragstart', e => {
                e.target.setAttribute('dragging', true);
                e.target.classList.add('dropclass');
        })
        el.addEventListener('drop', e => {
            e.target.classList.remove('bg-yellow-100');
            let draggingEl = document.querySelector('[dragging]');
            e.target.closest("tr").after(draggingEl);

            // let data_accountId = Array.from(document.querySelectorAll('[data-account_id]')).map(itemEl =>
            //         itemEl.getAttribute('data-account_id')
            // );

            var drop_accountId = $(".dropclass").attr('data-account_id');
            var drop_flowId = $(".dropclass").attr('flowId');
            var current_accountId = e.target.closest("tr").getAttribute('data-account_id');
            var current_flowId = e.target.closest("tr").getAttribute('flowId');

            var getAllFlowId = [];

            $('tbody tr').each(function() {
                if($(this).attr('data-account_id') == current_accountId && $(this).attr('flowid') !== undefined|| $(this).attr('flowid') == drop_flowId && $(this).attr('data-account_id') == drop_accountId && $(this).attr('flowid') !== undefined){
                    getAllFlowId.push($(this).attr('flowid'));
                }
            })

            // console.log(getAllFlowId , "flow if chal ");

            updateAccount(drop_accountId, drop_flowId, current_accountId, current_flowId,getAllFlowId);
        
        })
        el.addEventListener('dragenter', e => {
            e.target.classList.add('bg-yellow-100');
            e.preventDefault();
        })

        el.addEventListener('dragover', e => { e.preventDefault(); })

        el.addEventListener('dragleave', e => {
            e.target.classList.remove('bg-yellow-100');
            e.target.classList.remove('dropclass');
        })
        el.addEventListener('dragend', e => {
            e.target.removeAttribute('dragging');
            e.target.classList.remove('dropclass');
        })
    })
}


function updateAccount(drop_accountId, drop_flowId, current_accountId, current_flowId, getAllFlowId){

    if(drop_accountId !== undefined){
        // console.log(drop_accountId , "drop_accountId");
        // console.log(drop_flowId , "drop_flowId");
        // console.log(current_accountId , "current_accountId");
        // console.log(current_flowId , "current_flowId");
    
        $.ajax({
            url: "{{ url('/business/allocations/ajax/updatedrag') }}", 
            type: "POST",
            dataType: "json",
            contentType: "application/json; charset=utf-8",
            data: JSON.stringify({ drop_accountId: drop_accountId, drop_flowId: drop_flowId, current_accountId: current_accountId, current_flowId: current_flowId, getAllFlowId : getAllFlowId, returnType: "json" , businessId: "{{ session('businessId') }}" }),
            success: function (result) {
                    if(result.return){
                        window.location.reload();
                    }
                },
                error: function (err) {
                    console.log(err,"error");
                }
        }); 
    }
   
}
</script>

</html>
