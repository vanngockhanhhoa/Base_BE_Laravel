<?php

namespace App\Traits\Helper;

use App\Enums\ReservationTypeEnum;
use Illuminate\Support\Arr;

trait ScheduleHelperTrait
{
    /**
     * Transform event model
     * @param mixed $event
     * @return object
     */
    public static function transformEvent($event): object
    {
        switch ($event->type) {
            case ReservationTypeEnum::MEAL:
                $eventByType = optional($event->reservation)->meal;
                if (!empty($eventByType)) {
                    $event->event_data = static::getMealEventData($eventByType, $event);
                }
                break;
            case ReservationTypeEnum::STAY:
                $eventByType = optional($event->reservation)->stay;
                if (!empty($eventByType)) {
                    $event->event_data = static::getStayEventData($eventByType, $event);
                }
                break;
            case ReservationTypeEnum::TRANSPORT:
            default:
                break;
        }
        return $event;
    }


    /**
     * Get Transform event data
     * @param mixed $event
     * @param mixed $rootEvent
     * @return array
     */
    public function getTransformEventData($event, $rootEvent, string $type = 'start'): array
    {
        $reservation = optional($rootEvent)->reservation;
        $isPickup = !empty($reservation->photo);
        $eventData = array_merge(
            $event->only([
                'id',
                'origin',
                'destination',
                'subtype',
                'duration',
                'sequent'
            ]),
            [
                'event_type' => ReservationTypeEnum::TRANSPORT,
                'date' => $type === 'start' ? $event->departure_date : $event->arrival_date,
                'time' => $type === 'start' ? $event->departure_time : $event->arrival_time,
                'is_pickup' => $isPickup,
                'description' => $event->description ?? $event->note ?? null,
                'note' => $event->extra_fields ?? null,
                'event_position' => $type,
                'sequence' => $rootEvent->sequence ?? 0
            ]
        );
        if ($isPickup) {
            $eventData['pickup_title'] = $reservation->title ?? $eventData['origin'] ?? $eventData['destination'] ?? '';
            $eventData['photo'] = $reservation->photo ?? '';
            $eventData['photo_description'] = $reservation->photo_description ?? '';
        }
        $startEvent = array_merge(
            Arr::except($eventData, ['destination'])
        );
        $endEvent = array_merge(
            Arr::except($eventData, ['origin', 'subtype', 'duration', 'is_pickup', 'description', 'note', 'photo', 'pickup_title', 'photo_description'])
        );
        return array_merge(
            $eventData,
            [
                'start_event' => $startEvent,
                'end_event' => $endEvent
            ]
        );
    }

    /**
     * Get activity Event Data
     * @param $event
     * @param mixed $rootEvent
     * @param string $type
     * @return array
     */
    public function getActivityEventData($event, $rootEvent, string $type = 'start'): array
    {
        $reservation = optional($rootEvent)->reservation;
        $isPickup = !empty($reservation->photo);
        $eventData = array_merge(
            $event->only([
                'id',
                'duration',
            ]),
            [
                'origin' => $event->location,
                'destination' => $event->location,
                'event_type' => ReservationTypeEnum::ACTIVITY,
                'date' => $type === 'start' ? $event->start_date : $event->end_date,
                'time' => $type === 'start' ? $event->start_time : $event->end_time,
                'is_pickup' => $isPickup,
                'description' => $event->description ?? $event->note ?? null,
                'note' => $event->extra_fields ?? null,
                'event_position' => $type,
                'sequence' => $rootEvent->sequence ?? 0
            ]
        );
        if ($isPickup) {
            $eventData['pickup_title'] = $reservation->title ?? $eventData['origin'] ?? $eventData['destination'] ?? '';
            $eventData['photo'] = $reservation->photo ?? '';
            $eventData['photo_description'] = $reservation->photo_description ?? '';
        }
        $startEvent = array_merge(
            Arr::except($eventData, ['destination'])
        );
        $endEvent = array_merge(
            Arr::except($eventData, ['origin', 'subtype', 'duration', 'is_pickup', 'description', 'note', 'pickup_title', 'photo', 'photo_description'])
        );
        return array_merge(
            $eventData,
            [
                'start_event' => $startEvent,
                'end_event' => $endEvent
            ]
        );
    }

    /**
     * Get activity Event Data
     * @param $event
     * @param mixed $rootEvent
     * @param string $type
     * @return array
     */
    public function getFreeTimeEventData($event, $rootEvent, string $type = 'start'): array
    {
        $reservation = optional($rootEvent)->reservation;
        $isPickup = !empty($reservation->photo);
        $eventData = array_merge(
            $event->only([
                'id',
                'duration',
            ]),
            [
                'origin' => $event->location,
                'destination' => $event->location,
                'event_type' => ReservationTypeEnum::FREETIME,
                'date' => $type === 'start' ? $event->start_date : $event->end_date,
                'time' => $type === 'start' ? $event->start_time : $event->end_time,
                'is_pickup' => $isPickup,
                'description' => $reservation->description ?? null,
                'note' => $event->extra_fields ?? null,
                'event_position' => $type,
                'sequence' => $rootEvent->sequence ?? 0
            ]
        );
        if ($isPickup) {
            $eventData['pickup_title'] = $reservation->title ?? $eventData['origin'] ?? $eventData['destination'] ?? '';
            $eventData['photo'] = $reservation->photo ?? '';
            $eventData['photo_description'] = $reservation->photo_description ?? '';
        }
        $startEvent = array_merge(
            Arr::except($eventData, ['destination'])
        );
        $endEvent = array_merge(
            Arr::except($eventData, ['origin', 'subtype', 'duration', 'is_pickup', 'description', 'note', 'pickup_title', 'photo', 'photo_description'])
        );
        return array_merge(
            $eventData,
            [
                'start_event' => $startEvent,
                'end_event' => $endEvent
            ]
        );
    }

    /**
     * Get meal Event Data
     * @param $event
     * @param mixed $rootEvent
     * @param string $type
     * @return array
     */
    public function getMealTimelineEventData($event, $rootEvent, string $type = 'start'): array
    {
        $reservation = optional($rootEvent)->reservation;
        $isPickup = !empty($reservation->photo);
        $eventData = array_merge(
            $event->only([
                'id',
                'duration',
            ]),
            [
                'origin' => $event->facility_name,
                'destination' => $event->facility_name,
                'event_type' => ReservationTypeEnum::MEAL,
                'date' => $type === 'start' ? $event->start_date : $event->end_date,
                'time' => $type === 'start' ? $event->start_time : $event->end_time,
                'is_pickup' => $isPickup,
                'description' => $reservation->description ?? null,
                'note' => $event->extra_fields ?? null,
                'event_position' => $type,
                'sequence' => $rootEvent->sequence ?? 0
            ]
        );
        if ($isPickup) {
            $eventData['pickup_title'] = $reservation->title ?? $eventData['origin'] ?? $eventData['destination'] ?? '';
            $eventData['photo'] = $reservation->photo ?? '';
            $eventData['photo_description'] = $reservation->photo_description ?? '';
        }
        $startEvent = array_merge(
            Arr::except($eventData, ['destination'])
        );
        $endEvent = array_merge(
            Arr::except($eventData, ['origin', 'subtype', 'duration', 'is_pickup', 'description', 'note', 'pickup_title', 'photo', 'photo_description'])
        );
        return array_merge(
            $eventData,
            [
                'start_event' => $startEvent,
                'end_event' => $endEvent
            ]
        );
    }

    /**
     * Get Stay Event data
     * @param $event
     * @param $rootEvent
     * @return array
     */
    public static function getStayEventData($event, $rootEvent): array
    {
        $reservation = optional($rootEvent)->reservation;
        $originalEventData = $event->only([
            'fax',
            'booking_class',
            'checkin_date',
            'localize_checkin_date',
            'checkin_time',
            'checkout_date',
            'localize_checkout_date',
            'checkout_time',
            'facility_name',
            'location',
            'duration',
            'address',
            'meal_detail',
            'extra_fields'
        ]);

        $eventData = array_merge(
            $originalEventData,
            [
                'event_type' => ReservationTypeEnum::STAY,
                'tel' => optional($reservation)->tel,
                'meal_detail' => $originalEventData['meal_detail'] ?? '',
                'address' => $originalEventData['address'] ?? $reservation->address ?? '',
                'description' => $reservation->description ?? $originalEventData['description'] ?? $originalEventData['extra_fields'] ?? '',
                'sequence' => $rootEvent->sequence ?? 0
            ]
        );

        if (!empty($reservation->photo)) {
            $eventData['pickup_title'] = $reservation->title ?? $eventData['facility_name'] ?? '';
            $eventData['photo'] = $reservation->photo ?? '';
        }

        return $eventData;
    }

    /**
     * Get Meal events data
     * @param mixed $event
     * @param mixed $rootEvent
     * @return array
     */
    public static function getMealEventData($event, $rootEvent): array
    {
        $reservation = optional($rootEvent)->reservation;
        $originalEventData = $event->only([
            'start_date',
            'localize_start_date',
            'start_time',
            'end_date',
            'localize_end_date',
            'end_time',
            'facility_name',
            'location',
            'duration',
            'extra_fields'
        ]);
        $eventData = array_merge(
            $originalEventData,
            [
                'event_type' => ReservationTypeEnum::MEAL,
                'tel' => optional($reservation)->tel,
                'address' => $originalEventData['address'] ?? optional($reservation)->address ?? '',
                'description' => $reservation->description ?? $originalEventData['description'] ?? $originalEventData['extra_fields'] ?? '',
                'sequence' => $rootEvent->sequence ?? 0
            ]
        );

        if (!empty($reservation->photo)) {
            $eventData['pickup_title'] = $reservation->title ?? $eventData['facility_name'] ?? '';
            $eventData['photo'] = $reservation->photo ?? '';
        }

        return $eventData;
    }
}
