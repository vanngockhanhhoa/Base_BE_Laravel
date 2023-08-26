<?php

namespace App\Traits\Helper;

use App\Models\Consultation;
use Enum\ConsultationStatusEnum;
use Enum\MatchingStatusEnum;

trait ConsultationHelperTrait
{
    /**
     * Cancel consultation if all meister cancelled or rejected.
     * @param Consultation $consultation
     */
    public function updateConsultationStatus(Consultation $consultation): void
    {
        /**
         * @TODO: if all meister rejected then cancel consultation
         */
        $matchingMeister = $consultation->matching;
        $processedMatching = $matchingMeister->whereIn(
            'status',
            [
                MatchingStatusEnum::getValue('CANCELLED'),
                MatchingStatusEnum::getValue('REJECTED')
            ]
        )->count();
        if ($processedMatching === $matchingMeister->count()) {
            $consultation->update(['status' => ConsultationStatusEnum::getValue('CANCELLED')]);
        }
    }
}
