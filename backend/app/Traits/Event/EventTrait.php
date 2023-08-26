<?php

namespace App\Traits\Event;

use App\Enums\ProductTypeEnum;
use App\Models\Event;

trait EventTrait
{
    public function filterEvents(Event $event): bool
    {
        switch ($event->type) {
            case ProductTypeEnum::TRANSPORT:
                return !empty($event->transportation);
            case ProductTypeEnum::MEAL:
                return !empty(optional($event->reservation)->meal);
            case ProductTypeEnum::STAY:
                return !empty(optional($event->reservation)->stay);
            case ProductTypeEnum::ACTIVITY:
                return !empty(optional($event->reservation)->activity);
            default:
                return !empty($event->reservation);
        }
    }
}
