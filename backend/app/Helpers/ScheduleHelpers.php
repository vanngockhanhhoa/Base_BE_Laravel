<?php

namespace App\Helpers;

use App\Enums\ReservationTypeEnum;
use App\Repositories\Impl\ScheduleRepository;
use App\Traits\Helper\ScheduleHelperTrait;
use Helper\Common;
use Illuminate\Support\Collection;

class ScheduleHelpers
{
    use ScheduleHelperTrait;

    const JP_EVENT_PREVIEW_OUTPUT = 'Y年m月d日(l) H:i';

    /**
     * Re-group events by daty
     * @param $eventGroups
     * @return array
     */
    public static function reGroupEvents($eventGroups): array
    {
        $events = $eventGroups instanceof Collection ? $eventGroups : collect($eventGroups);
        $breakGroups = [];
        $groupTemp = [];
        $count = 0;
        $sorted = $events->flatten()->sortBy('sequence')->values();
        collect($sorted)->each(function ($event) use (&$breakGroups, &$groupTemp, &$count, $sorted) {
            $count++;
            $lastKey = last(array_keys($groupTemp));
            $breakCondition = $lastKey && $lastKey !== $event->event_date && in_array($event->event_date, array_keys($groupTemp));

            if ($breakCondition) {
                $breakGroups[] = $groupTemp;
                $groupTemp = [];
            }

            // check if sequel is increment and date exist in last array then reset group term
            $groupTemp[$event->event_date] = array_merge(
                $groupTemp[$event->event_date] ?? [],
                [$event]
            );
            if ($count >= $sorted->count()) {
                $breakGroups[] = $groupTemp;
            }
        });

        return $breakGroups;
    }

    /**
     * Map events of day.
     * @param $events
     * @return array
     */
    public static function mappedEvents($events): array
    {
        $flattenEvents = self::createCollection($events)->map(function ($event) {
            return self::transformEvent($event);
        });

        $mappedEvents = collect([]);
        $flattenEvents->each(function ($event) use (&$mappedEvents) {

            if (in_array($event->type, [ReservationTypeEnum::STAY])) {
                return;
            }
            switch ($event->type) {
                case ReservationTypeEnum::TRANSPORT:
                    $transportation = $event->transportation;
                    if (empty($transportation)) {
                        return;
                    }
                    $mappedEvents->push(self::getTransformEventData($transportation, $event, 'start'));
                    $mappedEvents->push(self::getTransformEventData($transportation, $event, 'end'));
                    break;

                case ReservationTypeEnum::MEAL:
                    $meal = optional($event->reservation)->meal;
                    if (empty($meal)) {
                        return;
                    }
                    $mappedEvents->push(self::getMealTimelineEventData($meal, $event, 'start'));
                    $mappedEvents->push(self::getMealTimelineEventData($meal, $event, 'end'));
                    break;

                case ReservationTypeEnum::ACTIVITY:
                    $activity = optional($event->reservation)->activity;
                    if (empty($activity)) {
                        return;
                    }
                    $mappedEvents->push(self::getActivityEventData($activity, $event, 'start'));
                    $mappedEvents->push(self::getActivityEventData($activity, $event, 'end'));
                    break;

                case ReservationTypeEnum::FREETIME:
                    $freeTime = optional($event->reservation)->freetime;
                    if (empty($freeTime)) {
                        return;
                    }
                    $mappedEvents->push(self::getFreeTimeEventData($freeTime, $event, 'start'));
                    $mappedEvents->push(self::getFreeTimeEventData($freeTime, $event, 'end'));
                    break;
            }
        });
        $mappedEvents = collect($mappedEvents)
            ->sortBy(['sequence', 'id', 'date', 'time'])
            ->groupBy('date')
            ->map(function ($dateGroup) {
                $dateGroup = $dateGroup->groupBy(['time'])
                    ->map(fn($group) => $group->sortBy('id')->last())
                    ->groupBy('id')
                    ->flatMap(fn($group) => $group->map(function ($item, $index) {
                        $item['index'] = $index;
                        return $item;
                    }));

                $group = collect();
                // chunk if has activity
                $extractEventTypes = [
                    ReservationTypeEnum::ACTIVITY,
                    ReservationTypeEnum::MEAL,
                    ReservationTypeEnum::FREETIME
                ];
                foreach ($extractEventTypes as $eventType) {
                    $dateGroup->where('event_type', $eventType)->sortBy(['id'])
                        ->chunk(2)
                        ->each(function ($chunk) use (&$group) {
                            $group->push(collect($chunk));
                        });
                }

                // chunk other events
                $dateGroup = $dateGroup->where('event_type', ReservationTypeEnum::TRANSPORT);
                if ($dateGroup->isNotEmpty()) {
                    $first = $dateGroup->shift();
                    $group = $group->push(collect([$first]))->merge($dateGroup->chunk(2));
                    if ($group->count() > 1) {
                        $last = $group->pop();
                        $last->each(fn($item) => $group->push(collect([$item])));
                    }
                }

                return $group
                    ->sortBy(fn($group) => $group->first()['sequence'] ?? $group->first()['time'] ?? null)
                    ->map(function ($group) {
                        if (count($group) <= 1) {
                            $eventType = $group->first()['event_position'];
                            $eventData = $group->first()[$eventType === 'end' ? 'end_event' : 'start_event'] ?? [];
                            return collect([$eventData]);
                        }
                        return $group->map(function ($item, $index) {
                            return self::getEventByPosition($item, $index);
                        });
                    });
            });

        return $mappedEvents->all();
    }

    /**
     * Reduce list events
     * @param $eventGroups
     * @return array
     */
    public static function reduceEvents($eventGroups): array
    {
        $reGroupEvents = self::reGroupEvents($eventGroups);
        $mappedEvents = [];
        foreach ($reGroupEvents as $reGroupEvent) {
            $tmpGroups = [];
            foreach ($reGroupEvent as $eventOfDate) {
                $tmpGroups = array_merge($tmpGroups, self::mappedEvents($eventOfDate));
            }
            $mappedEvents[] = $tmpGroups;
        }

        $flattenEvents = self::createCollection($eventGroups)->flatten()
            ->sortBy('sequence')
            ->map(function ($event) {
                return self::transformEvent($event);
            });
        $mealEvents = $flattenEvents->where('type', ReservationTypeEnum::MEAL)
            ->map(fn($event) => $event->event_data)
            ->groupBy('start_date');
        $stayEvents = $flattenEvents->where('type', ReservationTypeEnum::STAY)
            ->map(fn($event) => $event->event_data)
            ->groupBy('checkin_date');


        return [
            'mappedEvents' => $mappedEvents,
            'mealEvents' => $mealEvents,
            'stayEvents' => $stayEvents,
        ];
    }

    /**
     * Generate schedule document code
     * @return string
     */
    public static function generateScheduleDocumentCode(): string
    {
        return PDFHelpers::generateDocumentCode(
            app(ScheduleRepository::class)->newQuery()->count(),
            config('common.list_category_abbrevation.schedule')
        );
    }

    /**
     * @param array $eventData
     * @param int $index
     * @return array
     */
    public static function getEventByPosition(array $eventData, int $index = 0): array
    {
        return array_merge(
            $eventData[$index % 2 ? 'end_event' : 'start_event'] ?? [],
            [
                'index' => $eventData['index'] ?? 0,
                'group_index' => $index,
            ]
        );
    }

    /**
     * @param  mixed  $items
     *
     * @return Collection
     */
    public static function createCollection($items = []): Collection
    {
        if ($items instanceof Collection) {
            return $items;
        }
        return collect($items);
    }

    /**
     * @param $eventGroups
     *
     * @return array
     */
    public static function reStructureEvents($eventGroups): array
    {
        $eventData = [];
        foreach ($eventGroups as $date => $events) {
            foreach ($events as $event) {
                switch ($event->type) {
                    case ReservationTypeEnum::TRANSPORT:
                        $transport = $event->transportation;
                        $transport->type = $event->type;
                        $transport->sequence = $event->sequence;
                        $transport->description = $event->reservation->description;
                        $transport->start_time = Common::formatToTime($transport->depart_at);
                        $transport->end_time = Common::formatToTime($transport->arrived_at);
                        $transport->is_pickup = !empty($event->reservation->photo) ? true : false;
                        $transport->photo = $event->reservation->photo ?? '';
                        $transport->photo_description = $event->reservation->photo_description ?? '';
                        $eventData[$date][] = $event->transportation;
                        break;
                    case ReservationTypeEnum::MEAL:
                        $meal = $event->reservation->meal;
                        $meal->type = $event->type;
                        $meal->sequence = $event->sequence;
                        $meal->description = $event->reservation->description;
                        $meal->start_time = Common::formatToTime($event->start_at);
                        $meal->end_time = Common::formatToTime($event->end_at);
                        $meal->is_pickup = !empty($event->reservation->photo) ? true : false;
                        $meal->photo = $event->reservation->photo ?? '';
                        $meal->photo_description = $event->reservation->photo_description ?? '';
                        $eventData[$date][] = $meal;
                        break;
                    case ReservationTypeEnum::ACTIVITY:
                        $activity = $event->reservation->activity;
                        $activity->type = $event->type;
                        $activity->sequence = $event->sequence;
                        $activity->description = $event->reservation->description;
                        $activity->start_time = Common::formatToTime($event->start_at);
                        $activity->end_time = Common::formatToTime($event->end_at);
                        $activity->is_pickup = !empty($event->reservation->photo) ? true : false;
                        $activity->photo = $event->reservation->photo ?? '';
                        $activity->photo_description = $event->reservation->photo_description ?? '';
                        $eventData[$date][] = $event->reservation->activity;
                        break;
                    case ReservationTypeEnum::FREETIME:
                        $freetime = $event->reservation->freetime;
                        $freetime->type = $event->type;
                        $freetime->sequence = $event->sequence;
                        $freetime->description = $event->reservation->description;
                        $freetime->start_time = Common::formatToTime($event->start_at);
                        $freetime->end_time = Common::formatToTime($event->end_at);
                        $freetime->is_pickup = !empty($event->reservation->photo) ? true : false;
                        $freetime->photo = $event->reservation->photo ?? '';
                        $freetime->photo_description = $event->reservation->photo_description ?? '';
                        $eventData[$date][] = $freetime;
                        break;
                    default:
                        break;
                }
            }
        }
        return $eventData;
    }
}
