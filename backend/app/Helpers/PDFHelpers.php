<?php

namespace App\Helpers;

use App\Enums\DocumentTypeEnum;
use App\Enums\TaxOptionEnum;
use App\Enums\TransportationTypeEnum;
use App\Repositories\Impl\LastestSerialCodesRepository;
use Carbon\Carbon;
use Enum\UserSuffixEnum;
use Helper\Common;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PDFHelpers
{
    public static function getTransportationType($type): ?string
    {
        return TransportationTypeEnum::getKeyName($type) ?? null;
    }

    public static function getIconUrl($name): string
    {
        $baseUrl = "/pdf/images/icon/ico_$name.png";
        return request()->has('html') ? asset($baseUrl) : public_path($baseUrl);
    }

    public static function getReservationImage(string $path): string
    {
        if (Str::startsWith($path, 'http')) {
            return $path;
        }

        return Storage::url($path);
    }

    public static function getDateByFormat($date, $format): ?string
    {
        return !empty($date) ? $date->format($format) : $date;
    }

    /**
     * @param $file
     * @param string|null $path
     * @param string $disk
     * @return string|null
     */
    public static function uploadPdf($file, string $path = null, string $disk = 's3'): ?string
    {
        $fileContent = $file instanceof UploadedFile ? $file->getContent() : $file;
        Storage::disk($disk)->put($path, $fileContent, 'public');
        return Storage::disk($disk)->url($path);
    }

    public static function removePdf(string $path, string $disk = 's3')
    {
        if (Storage::disk($disk)->exists($path)) {
            Storage::disk($disk)->delete($path);
        }
    }

    public static function getPdfPath(
        string $documentType,
        string $consultationHashId,
        string $itineraryHashId,
        string $documentName
    ): string {
        if (!Str::endsWith($documentName, ".pdf")) {
            $documentName .= ".pdf";
        }
        return "/documents/consultation/{$consultationHashId}/itineraries/{$itineraryHashId}/{$documentType}/{$documentName}";
    }

    public static function getDocumentType(string $categoryCode): string
    {
        switch ($categoryCode) {
            case DocumentTypeEnum::SCHEDULE:
                return config('common.list_category_abbrevation.schedule');
            case DocumentTypeEnum::ESTIMATE:
                return config('common.list_category_abbrevation.estimate');
            case DocumentTypeEnum::BOOKING_ACCEPTANCE:
                return config('common.list_category_abbrevation.reservation');
            case DocumentTypeEnum::INVOICE:
                return config('common.list_category_abbrevation.invoice');
            case DocumentTypeEnum::RECEIPT:
                return config('common.list_category_abbrevation.receipt');
            default:
                return config('common.list_category_abbrevation.other');
        }
    }

    /**
     * @param int $totalDocument
     * @param string|null $documentCode
     * @return string
     */
    public static function generateDocumentCode(int $totalDocument = 0, string $documentCode = null): string
    {
        if (empty($documentCode)) {
            $documentCode = config('common.list_category_abbrevation.form');
        }
        $totalDocument += 1;
        return $totalDocument < 10 ? "{$documentCode}-0${totalDocument}" : "{$documentCode}-{$totalDocument}";
    }

    /**
     * @param mixed $listItems
     * @return int|mixed
     */
    public static function calculateTotalListItems($listItems)
    {
        $items = $listItems instanceof Collection || $listItems instanceof EloquentCollection
            ? $listItems
            : collect($listItems);
        if ($items->isEmpty()) {
            return 0;
        }
        return $listItems->reduce(function ($total, $item) {
            $subtotal = $item->quantity * $item->price;
            $tax = $item->tax_rate ? ($item->tax_rate / 100) : 0;
            return $total + ($subtotal + ($subtotal * $tax));
        }, 0);
    }

    /**
     * @param mixed|null $value
     * @return string|null
     */
    public static function getTaxLabel($value = null): ?string
    {
        if (empty($value)) {
            return TaxOptionEnum::getKeyName(TaxOptionEnum::INCLUDED_LABEL);
        }
        return TaxOptionEnum::getKeyName($value) ?? null;
    }

    /**
     * @param int $totalCol
     * @param bool $showColumnUnitPriceAmount
     * @param bool $showColumnTax
     * @return int
     */
    public function calculateColSpan(
        int $totalCol = 5,
        bool $showColumnUnitPriceAmount = false,
        bool $showColumnTax = false
    ): int {
        $col = $totalCol;
        if (empty($showColumnUnitPriceAmount)) {
            $col -= 2;
        }
        if (empty($showColumnTax)) {
            $col -= 1;
        }

        return $col;
    }

    /**
     * @param string $type
     * @param string $code
     * @param Carbon|null $updatedAt
     * @return string
     */
    public static function getDocumentName(string $type, string $code, Carbon $updatedAt = null): string
    {
        $date = $updatedAt ?? Carbon::now();
        $formattedDate = $date->format("y年m月d日");
        return "{$type} {$code} {$formattedDate}";
    }

    /**
     * @param Collection $transportations
     * @param string $transportType
     * @return Collection
     */
    public static function getTransportationByType(Collection $transportations, string $transportType): Collection
    {
        /** @var Collection $_transportations */
        return $transportations->filter(function ($t) use ($transportType) {
            return $t->transportation->isNotEmpty() && $t->transportation->first()->subtype == $transportType;
        })->map(function ($t) {
            return [
                'transportation' => $t->transportation->first() ?? null,
                'reservation' => $t
            ];
        })->sortBy(function ($t) {
            return $t['transportation']->depart_at;
        });
    }

    /**
     * @param Collection $events
     * @return Collection
     */
    public static function getPassengersOfTransportations(Collection $events): Collection
    {
        return $events->filter(
            fn($event) => $event['reservation']->reservation_passenger->isNotEmpty()
        )->flatMap(function ($event) {
            $passengers = $event['reservation']->reservation_passenger;

            return $passengers->map(function ($passenger) use ($event) {
                return [
                    'reservation' => $event['reservation'],
                    'transportation' => $event['transportation'],
                    'passenger' => $passenger
                ];
            });
        })->sortBy(fn($passenger) => $passenger['transportation']->depart_at);
    }

    /**
     * @param Collection $passengers
     * @param string $passengerHashId
     * @return mixed
     */
    public static function getPassengerByHashId(Collection $passengers, string $passengerHashId): ?object
    {
        return $passengers->where('hash_id', $passengerHashId)->first();
    }

    /**
     * @param mixed $type
     * @return string
     */
    public static function getSuffixType($type = null): string
    {
        return  UserSuffixEnum::getKeyName($type) ?? '';
    }

    /**
     * Get status show/hide Breakdown table
     * @param mixed $specialTemplate
     * @param bool $showBlockBreakdown
     * @return bool
     */
    public static function getBreakdownTableStatus($specialTemplate = null, bool $showBlockBreakdown = false): bool
    {
        if (!empty($specialTemplate)) {
            $commonOptions = optional($specialTemplate ?? null)->extra_fields['accomodation_info'] ?? [];

            return $commonOptions['reservation_details_info'] ?? false;
        }
        return $showBlockBreakdown;
    }

    /**
     * Filter list item for breakdown table
     * @param bool $showOnlyFreeTax
     * @param mixed|array|Collection $listItems
     * @return array|Collection|mixed
     */
    public static function getBreakdownAvailableItems(bool $showOnlyFreeTax = false, $listItems = null)
    {
        if (!$showOnlyFreeTax) {
            return $listItems;
        }

        return collect($listItems ?? [])->filter(fn($item) => $item->tax_rate <= 0);
    }

    /**
     * Get status show/hide flight ticket table
     * @param mixed|array $commonOptions
     * @param array $templateOptions
     * @param bool $showBlockSchedules
     * @return bool
     */
    public static function getScheduleShowTicketTable(
        $commonOptions,
        array $templateOptions = [],
        bool $showBlockSchedules = false
    ): bool {
        if (!empty($templateOptions)) {
            return $commonOptions['flight_info'] ?? false;
        }
        return $showBlockSchedules;
    }

    /**
     * Get status show/hide hotel table
     * @param mixed $stays
     * @param mixed $specialTemplate
     * @param bool $showBlockHotel
     * @return bool
     */
    public static function getHotelShowTable($stays, $specialTemplate, bool $showBlockHotel = false): bool
    {
        if (empty($stays) || ($stays instanceof Collection) && $stays->isEmpty()) {
            return false;
        }
        if (!empty($specialTemplate)) {
            $commonOptions = optional($specialTemplate ?? null)->extra_fields['accomodation_info'] ?? [];
            return $commonOptions['hotel_info'] ?? false;
        }
        return $showBlockHotel;
    }

    /**
     * Get status show/hide remark table
     * @param bool $isReceipt
     * @param mixed $templateOptions
     * @param bool $showBlockRemark
     * @return bool
     */
    public static function getRemarkShowTable(
        bool $isReceipt = false,
        $templateOptions = null,
        bool $showBlockRemark = false
    ): bool {
        if ($isReceipt) {
            return $isReceipt;
        }
        if (!empty($templateOptions)) {
            return $templateOptions['note'] ?? false;
        }
        return $showBlockRemark;
    }

    /**
     * Get status show/hide reservation table
     * @param mixed $specialTemplate
     * @param bool $showBlock
     * @return bool
     */
    public static function getReservationShowTable($specialTemplate, bool $showBlock = false): bool
    {
        if (!empty($specialTemplate)) {
            $commonOptions = optional($specialTemplate)->extra_fields['accomodation_info'] ?? [];

            return $commonOptions['reservation_info'] ?? false;
        }
        return $showBlock ?? false;
    }

    /**
     * Get status show/hide payee table
     * @param mixed $specialTemplate
     * @param bool $show
     * @return bool
     */
    public static function getPayeeShowTable($specialTemplate = null, bool $show = false): bool
    {
        if (!empty($specialTemplate)) {
            $templateOptions = optional($specialTemplate ?? null)->extra_fields['accomodation_info'] ?? [];

            return $templateOptions['payee'] ?? false;
        }
        return $show;
    }

    /**
     * Get list stamp labels
     * @param mixed $template
     * @param int $total
     * @param array $documentLabels
     * @return array
     */
    public static function getStampLabels($template = null, int $total = 0, array $documentLabels = []): array
    {
        if (!empty($template)) {
            return $template['stamp_labels'] ?? [];
        }
        return $documentLabels ?? [];
    }

    /**
     * Get status show/hide agent table
     * @param bool $isReceipt
     * @param mixed $specialTempl
     * @param bool $documentShowStatus
     * @return bool
     */
    public static function getAgentShowTable(
        bool $isReceipt = false,
        $specialTempl = null,
        bool $documentShowStatus = false
    ): bool {
        if ($isReceipt) {
            return $isReceipt;
        }

        if (!empty($specialTempl)) {
            $commonTemplateOptions = optional($specialTempl)->extra_fields['accomodation_info'] ?? [];
            return $commonTemplateOptions['company_info'] ?? false;
        }
        return $documentShowStatus;
    }

    /**
     * Get status show/hide hotel contacts table
     * @param mixed $stays
     * @param mixed $specialTemplate
     * @param false $showBlockHotel
     * @return bool
     */
    public static function getHotelContactShowTable($stays, $specialTemplate, bool $showBlockHotel = false): bool
    {
        if (empty($stays) || ($stays instanceof Collection) && $stays->isEmpty()) {
            return false;
        }
        if (!empty($specialTemplate)) {
            $commonOptions = optional($specialTemplate ?? null)->extra_fields['accomodation_info'] ?? [];
            return $commonOptions['hotel_info'] ?? false;
        }
        return $showBlockHotel;
    }
}
