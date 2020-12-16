@props([
    'business'
])

<x-forms.form
    method="PATCH"
    action=""
>
    <input name="date" type="date">
    <input name="amount[{{$acc->id}}]" type="text"
        class="account-value bg-warning text-right allocation-value form-control form-control-sm border-info"
        disabled>
</x-forms.form>
