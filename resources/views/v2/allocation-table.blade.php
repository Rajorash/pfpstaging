<div class="rounded-xl">
    <table id="allocationTable" cellpadding="0" cellspacing="0" class="border-collapse rounded-xl bg-white w-full">
        <thead>
        <tr>
            <th class="border border-gray-300"></th>
            {{--            today --}}
            <th class="border border-gray-300 text-blue p-4">
                <span class="block text-xs font-normal">Mar 2021</span>
                <span class="block text-xl">31</span>
                <span class="block text-xs font-normal">Web</span>
            </th>
            {{--            regular days--}}
            @for($i = 0; $i < 6; $i++)
                <th class="border border-gray-300 p-4">
                    <span class="block text-xs font-normal">Mar 2021</span>
                    <span class="block text-xl">31</span>
                    <span class="block text-xs font-normal">Web</span>
                </th>
            @endfor
        </tr>
        </thead>
        <tbody>
        <tr class="bg-blue text-white uppercase">
            <td class="py-1 pr-2 pl-4" colspan="8">Revenue accounts</td>
        </tr>
        <tr>
            <td class="border border-gray-300 whitespace-nowrap p-1 pr-2 pl-4 bg-indigo-100">Core</td>
            @for($i = 0; $i < 7; $i++)
                <td class="border border-gray-300 text-right p-1 bg-indigo-100"><input
                        class="px-2 py-0 w-20 text-right bg-transparent border-none"
                        type="text" value="0" disabled/></td>
            @endfor
        </tr>
        <tr>
            <td class="border border-gray-300 whitespace-nowrap p-1 pr-2 pl-6">Accounts Receivable</td>
            @for($i = 0; $i < 7; $i++)
                <td class="border border-gray-300 text-right p-1"><input
                        class="px-2 py-0 w-20 text-right bg-transparent border-0 border-b border-transparent outline-none
                        focus:border-yellow-700 focus:outline-none focus:shadow-none focus:ring-0"
                        id="someOwnKey_1_{{$i}}"
                        type="text" value="0"/></td>
            @endfor
        </tr>
        <tr>
            <td class="border border-gray-300 whitespace-nowrap p-1 pr-2 pl-6">Estimated Activity</td>
            @for($i = 0; $i < 7; $i++)
                <td class="border border-gray-300 text-right p-1"><input
                        class="px-2 py-0 w-20 text-right bg-transparent border-0 border-b border-transparent outline-none
                        focus:border-yellow-700 focus:outline-none focus:shadow-none focus:ring-0"
                        id="someOwnKey_2_{{$i}}"
                        type="text" value="0"/></td>
            @endfor
        </tr>
        <tr>
            <td colspan="8" class="h-0.5 bg-light_blue"></td>
        </tr>
        <tr>
            <td class="border border-gray-300 whitespace-nowrap p-1 pr-2 pl-6">Estimated Activity</td>
            @for($i = 0; $i < 7; $i++)
                <td class="border border-gray-300 text-right p-1"><input
                        class="px-2 py-0 w-20 text-right bg-transparent border-0 border-b border-transparent outline-none
                        focus:border-yellow-700 focus:outline-none focus:shadow-none focus:ring-0"
                        id="someOwnKey_3_{{$i}}"
                        type="text" value="0"/></td>
            @endfor
        </tr>
        </tbody>
    </table>
</div>
