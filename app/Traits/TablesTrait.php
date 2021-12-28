<?php

namespace App\Traits;

trait TablesTrait
{
    /**
     * @param  $cells
     * @param  bool  $checkIsValuePresent
     * @return null
     */
    public function store($cells, bool $checkIsValuePresent = false)
    {
        if (is_array($cells) && count($cells) > 0) {
            foreach ($cells as $singleCell) {
                preg_match('/(\w+)_(\d+)_(\d{4}-\d{2}-\d{2})/', $singleCell['cellId'], $matches);
                $allocation_id = (integer) $matches[2];
                $date = $matches[3];
                $value = (float) $singleCell['cellValue'];

                $this->storeSingle(
                    $matches[1],
                    $allocation_id,
                    $value,
                    $date,
                    ($matches[1] == 'account'),
                    $checkIsValuePresent
                );
            }
        }

        return null;
    }

    /**
     * Validate and store the Allocation
     * @param  string  $type
     * @param  int  $allocation_id
     * @param  float  $amount
     * @param  string  $date
     * @param  bool  $manual_entry
     * @param  bool  $checkIsValuePresent
     */
    public function storeSingle(
        string $type,
        int $allocation_id,
        float $amount,
        string $date,
        bool $manual_entry = false,
        bool $checkIsValuePresent = false
    ) {
        $phaseId = $this->business->getPhaseIdByDate($date);

        if ($type == 'flow') {
            $account = $this->getFlowAccount($allocation_id);
        } else {
            $account = $this->getBankAccount($allocation_id);
        }

        $account->allocate($amount, $date, $phaseId, $manual_entry, $checkIsValuePresent);
    }

    protected function getRangeArray(): array
    {
        return [
            7 => 'Weekly',
            14 => 'Fortnightly',
            31 => 'Monthly'
        ];
    }
}
