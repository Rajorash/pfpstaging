<?php

namespace App\Http\Livewire;

use App\Models\AccountFlow;
use App\Models\BankAccount;
use Livewire\Component;
use Livewire\WithFileUploads;
use App\Imports\ImportFlows;
use Excel;

class AddEditFlow extends Component
{
    use WithFileUploads;
    
    public int $flowId = 0;
    public int $accountId = 0;
    public bool $defaultNegative = false;
    public string $routeName = '';

    public string $label = '';
    public string $errormessege = '';
    public bool $negative_flow = false;
    public int $certainty = 100;

    public bool $modalMode = false;
    public int $tab1 = 1;
    public int $tab2 = 0;
    public $flowscsv;
    protected $listeners = [
        'checktab1' => 'checktab1',
        'checktab2' => 'checktab2'
    ];
    

    public function mount()
    {
        $account = BankAccount::findOrFail($this->accountId);

        if ($this->flowId) {
            $flow = AccountFlow::find($this->flowId);

            $this->label = $flow->label;
            $this->negative_flow = $flow->negative_flow;
            $this->certainty = $flow->certainty;
        } else {
            $this->negative_flow = $this->defaultNegative;
        }
        $this->errormessege = '';
    }

    protected function rules()
    {
        if($this->tab2){
            return ['flowscsv' =>  'required|file|mimes:xls,xlsx,csv'];
        }else{
            return [
                'label' => ['required', 'min:3'],
                'negative_flow' => ['required'],
                'certainty' => ['required', 'integer', 'min:0', 'max:500']
            ];
        }
        
    }

    public function checktab1()
    {
       $this->tab1 = 1;
       $this->tab2 = 0;
    }


    public function checktab2()
    {
        $this->tab2 = 1;
        $this->tab1 = 0;
    }

    public function store()
    {
        $validateValues = $this->validate();

        $flow = $this->flowId ? AccountFlow::find($this->flowId) : new AccountFlow();
        $flow->label = $validateValues['label'];
        $flow->negative_flow = $validateValues['negative_flow'];
        $flow->certainty = $validateValues['certainty'];

        $account = null;

        if ($this->accountId) {
            $account = BankAccount::findOrFail($this->accountId);
            $flow->account_id = $account->id;
        }

        $flow->save();

        if ($this->modalMode) {
            $this->emit('reloadRevenueTable');
        } else {
            return redirect("business/".$account->business->id."/accounts");
        }
    }

    public function import()
    {
        if($this->flowscsv == null){
            $this->errormessege =  'Please Select xls,xlsx,csv File.';
        }else{
                $this->errormessege = '';
                $filename = $this->flowscsv->getRealPath();

                
                    $checkexcelstatus = new ImportFlows($this->flowId , $this->accountId);
                    $checkexcelstatus->import($filename);
                        
                   
                            if ($this->modalMode) {
                                $this->emit('reloadRevenueTable');
                            } else {
                                 return redirect("business/".$account->business->id."/accounts");
                            }
                        
        }        
        
    }

    public function render()
    {
        return view('livewire.add-edit-flow');
    }
}
