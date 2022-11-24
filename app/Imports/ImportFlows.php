<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use App\Models\AccountFlow;
use App\Models\BankAccount;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithHeadingRow;


class ImportFlows implements ToCollection,WithHeadingRow
{
    use Importable ;
    public int $flowId = 0;
    public int $accountId = 0;

    public function __construct( $flowId,$accountId)
    {
        $this->flowId = $flowId ;
        $this->accountId = $accountId ;
        // dd($this->accountId);
    }


       

    /**
    * @param Collection $collection
    */
    public function collection(Collection $collection)
    {
        // dd($collection->toArray());

        Validator::make($collection->toArray(), [
            '*.label' => ['required', 'min:3'],
            '*.certainty'  => ['required', 'integer', 'min:0', 'max:500'],
            '*.negative_flow' => ['required']
        ])->validate();

        $flow = $this->flowId ? AccountFlow::find($this->flowId) : new AccountFlow();
        $time_created = date('Y-m-d h:i:s', time());
            foreach($collection as $collec){
                $account = null;
                $account = BankAccount::findOrFail($this->accountId);

             $totalflow[] = [  'label' => trim($collec['label']),
                                'certainty' => trim($collec['certainty']),
                                'negative_flow' => trim($collec['negative_flow']) == "N"  ? true : false,
                                'account_id' => $account->id,
                                'created_at' => $time_created,
                                'updated_at' => $time_created
                            ];

            }


        AccountFlow::insert($totalflow);
    }
}
