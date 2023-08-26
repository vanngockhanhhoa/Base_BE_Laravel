<?php

namespace App\Traits\Helper;

use App\Models\Itinerary;
use Carbon\Carbon;
use Enum\ItineraryStatusEnum;
use Enum\MatchingStatusEnum;

trait MatchingMeisterHelperTrait
{
    /**
     * Cancel consultation if all meister cancelled or rejected.
     * @param Itinerary $itinerary
     */
    public function updateMatchingMeisterStatus(Itinerary $itinerary): void
    {
        $matchingMeister = optional($itinerary)->matching;
        switch (optional($itinerary)->status) {
            case ItineraryStatusEnum::getValue('CANCELLED'):
                $matchingMeister->update(['status' => MatchingStatusEnum::getValue('CANCELLED')]);
                break;
            case ItineraryStatusEnum::getValue('REQUESTED'):
                if ($itinerary->schedule()->count() === 0) {
                    $itinerary->schedule()->create([
                        'date' => Carbon::now(),
                    ]);
                }
                break;
        }
    }
}
