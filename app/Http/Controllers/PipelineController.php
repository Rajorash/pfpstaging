<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use App\Models\Pipeline;
use Illuminate\Database\Eloquent\Collection;

class PipelineController extends Controller
{
    protected RecurringPipelineController $recurringPipelineController;

    public function __construct()
    {
        $this->recurringPipelineController = new RecurringPipelineController();
    }

    /**
     * @param  int  $bankAccountId
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function list(int $bankAccountId)
    {
        $bankAccount = BankAccount::findOrfail($bankAccountId);

        $pipelines = Pipeline::where('business_id', $bankAccount->id)
            ->orderBy('title')
            ->get();

        return view(
            'pipelines.list',
            [
                'bankAccount' => $bankAccount,
                'pipelines' => $pipelines
            ]
        );
    }

    public function create(int $bankAccountId)
    {
        $bankAccount = BankAccount::findOrfail($bankAccountId);

        $pipeline = null;

        return view(
            'pipelines.create',
            [
                'bankAccount' => $bankAccount,
                'pipeline' => $pipeline
            ]
        );
    }

    /** Get compiled forecast for pipeline
     * @param  Collection  $recurringTransactionsArray
     * @param  string|null  $dateStart
     * @param  string|null  $dateEnd
     * @return array
     */
    public function getAllFlowsForecasts(
        Collection $recurringTransactionsArray,
        string $dateStart = null,
        string $dateEnd = null
    ): array {
        return $this->recurringPipelineController->getAllFlowsForecasts(
            $recurringTransactionsArray,
            $dateStart,
            $dateEnd
        );
    }

    /**
     * @param  Pipeline  $pipeline
     * @param  string|null  $periodDateStart
     * @param  string|null  $periodDateEnd
     * @return array
     */
    public function getForecast(
        Pipeline $pipeline,
        string $periodDateStart = null,
        string $periodDateEnd = null
    ): array {
        return $this->recurringPipelineController->getForecast(
            $pipeline,
            $periodDateStart,
            $periodDateEnd
        );
    }

    /**
     * @param  int  $bankAccountId
     * @param  int  $pipelineId
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function edit(
        int $bankAccountId,
        int $pipelineId
    ) {
        $bankAccount = BankAccount::findOrfail($bankAccountId);
        $pipeline = Pipeline::findOrfail($pipelineId);

        return view(
            'pipelines.create',
            [
                'bankAccount' => $bankAccount,
                'pipeline' => $pipeline
            ]
        );
    }

    /**
     * @param  int  $bankAccountId
     * @param  int  $pipelineId
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function delete(
        int $bankAccountId,
        int $pipelineId
    ) {
        $bankAccount = BankAccount::findOrfail($bankAccountId);
        $pipeline = Pipeline::findOrfail($pipelineId);

        $pipeline->delete();

        return redirect(
            route(
                'pipelines.list',
                [
                    'business' => $bankAccount,
                ]
            )
        );
    }
}
