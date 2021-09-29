<?php

namespace App\Http\Livewire;

use App\Http\Controllers\PipelineController;
use App\Models\Pipeline;
use Carbon\Carbon;
use JamesMills\LaravelTimezone\Facades\Timezone;
use Livewire\Component;
use Str;

class PipelineLivewire extends Component
{
    public $bankAccount;
    public $pipeline;
    public $forecast;

    protected $PipelineController;

    public $repeatTimeArray;
    public $weekDaysArray;

    public $description;

    public $title;
    public $notes;
    public $certainty;
    public $value;
    public $recurring;
    public $date_start;
    public $date_end;
    public $repeat_every_number;
    public $repeat_every_type;
    public $repeat_rules_week_days = [];

    public function __construct($id = null)
    {
        parent::__construct($id);

        $this->PipelineController = new PipelineController();
    }

    public function mount()
    {
        $this->repeatTimeArray = Pipeline::getRepeatTimeArray();
        $this->weekDaysArray = Pipeline::getWeekDaysArray();

        if (is_object($this->pipeline)
            && is_a($this->pipeline, 'App\Models\Pipeline')) {
            $this->title = $this->pipeline->title;
            $this->notes = $this->pipeline->notes;
            $this->certainty = $this->pipeline->certainty;
            $this->recurring = $this->pipeline->recurring;
            $this->description = $this->pipeline->description;
            $this->value = $this->pipeline->value;
            $this->date_start = Carbon::parse($this->pipeline->date_start)->format('Y-m-d');
            $this->date_end = $this->pipeline->date_end
                ? Carbon::parse($this->pipeline->date_end)->format('Y-m-d') :
                null;

            $this->repeat_every_number = $this->pipeline->repeat_every_number;
            $this->repeat_every_type = $this->pipeline->repeat_every_type;
            if ($this->pipeline->repeat_every_type == Pipeline::REPEAT_WEEK) {
                if ($this->pipeline->repeat_rules['days']) {
                    $this->repeat_rules_week_days = $this->pipeline->repeat_rules['days'];
                }
            }

        } else {
            $this->value = 0;
            $this->recurring = false;
            $this->date_start = Timezone::convertToLocal(Carbon::now(),'Y-m-d');
            $this->certainty = Pipeline::DEFAULT_CERTAINTY;
            $this->repeat_every_number = 1;
            $this->repeat_every_type = Pipeline::REPEAT_DEFAULT;
            $this->repeat_rules_week_days = [
                strtolower(Carbon::now()->format('l'))
            ];
        }

        $recurring = $this->_updateRecurringobject();
        $this->forecast = $this->PipelineController->getForecast($recurring);
    }

    public function render()
    {
        return view('pipelines.pipeline-livewire');
    }

    public function rules(): array
    {
        return [
            'title' => 'required',
            'certainty' => 'required|integer|between:1,100',
            'value' => 'required|numeric|gt:0',
            'date_start' => 'required|date',
            'date_end' => 'nullable|date|after:date_start',
            'repeat_rules_week_days' => 'required_if:repeat_every_type,'.Pipeline::REPEAT_WEEK
        ];
    }

    public function updated()
    {
        $this->validate();

        if ($this->value > 0) {
            $this->description = __('Subtraction');

            $this->description .= ' $'.$this->value.' ';

            switch ($this->repeat_every_type) {
                case Pipeline::REPEAT_DAY:
                    $this->description .=
                        __('every')
                        .' '
                        .(
                        $this->repeat_every_number > 1
                            ? $this->repeat_every_number
                            : ''
                        )
                        .' '
                        .Str::plural('day', $this->repeat_every_number);
                    break;
                case Pipeline::REPEAT_WEEK:
                    if ($this->repeat_rules_week_days) {
                        $this->description .=
                            (
                            $this->repeat_every_number == 1
                                ? __('weekly')
                                : __('every')
                                .' '
                                .$this->repeat_every_number
                                .' '
                                .__('weeks')
                            )
                            .' '
                            .__('on')
                            .' '
                            .implode(', ',
                                array_map(function ($x) {
                                    return $this->weekDaysArray[$x];
                                }, $this->repeat_rules_week_days)
                            );
                    }
                    break;
                case Pipeline::REPEAT_MONTH:
                    $this->description .=
                        (
                        $this->repeat_every_number == 1 ?
                            __('monthly on day')
                            : __('every')
                            .' '
                            .$this->repeat_every_number
                            .' '
                            .__('months')
                        )
                        .' '
                        .Carbon::parse($this->date_start)->format('d');
                    break;
                case Pipeline::REPEAT_YEAR:
                    //Annually on August 16, until Sep 15, 2021
                    $this->description .=
                        (
                        $this->repeat_every_number == 1 ?
                            __('annually on')
                            : __('every')
                            .' '
                            .$this->repeat_every_number
                            .' '
                            .__('years')
                        )
                        .' '
                        .Carbon::parse($this->date_start)->format('F d');
                    break;
            }

            $this->description .=
                $this->date_end
                    ? ' '
                    .__('until')
                    .' '.
                    Carbon::parse($this->date_end)->format('M d, Y')
                    : '';

        } else {
            $this->description = '';
        }

        $recurring = $this->_updateRecurringobject();
        $this->forecast = $this->PipelineController->getForecast($recurring);
    }

    private function _updateRecurringobject()
    {
        if (is_object($this->pipeline)
            && is_a($this->pipeline, 'App\Models\Pipeline')) {
            $pipeline = $this->pipeline;
        } else {
            $pipeline = new Pipeline();
        }

        $pipeline->title = $this->title;
        $pipeline->notes = $this->notes;
        $pipeline->certainty = $this->certainty;
        $pipeline->description = $this->description;

        $pipeline->value = $this->value;
        $pipeline->recurring = $this->recurring;

        $pipeline->date_start = $this->date_start;
        $pipeline->date_end = $this->date_end ? $this->date_end : null;

        $pipeline->repeat_every_number = $this->repeat_every_number;
        $pipeline->repeat_every_type = $this->repeat_every_type;
        if ($this->repeat_every_type == Pipeline::REPEAT_WEEK) {
            $pipeline->repeat_rules = [
                'days' => $this->repeat_rules_week_days
            ];
        }

        return $pipeline;
    }

    public function store()
    {
        $this->validate();

        $pipeline = $this->_updateRecurringobject();

        $pipeline->account()->associate($this->bankAccount);

        $pipeline->save();

        $this->redirect(route('pipelines.list',
            [
                'business' => $this->bankAccount
            ]
        ));
    }
}
