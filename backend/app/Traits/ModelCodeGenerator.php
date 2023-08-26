<?php

namespace App\Traits;

use App\Repositories\Impl\LastestSerialCodesRepository;
use Helper\Common;

trait ModelCodeGenerator
{
    /**
     * @param string $type
     * @param string $agencyCode {Agency code is not defined => hard code 2022.05.06}
     * @return string
     */
    public function createCode(string $type): string
    {
        $agencyCode = !empty(auth()->user()->meister->agent->code) ? auth()->user()->meister->agent->code : 'AA01';
        $categories = config('common.list_category_abbrevation');
        switch ($type) {
            case $categories['estimate']:
            default:
                return $this->generateCode($type, $agencyCode);
        }
    }

    /**
     * @param string $typeAbbreviation
     * @param string $agencyCode
     * @return string
     */
    public function generateCode(string $typeAbbreviation, string $agencyCode): string
    {
        // Get latest serial code of consultation category
        $dataOfLatestSerialCode = app(LastestSerialCodesRepository::class)->getDataOfLastestSerialCode($typeAbbreviation);
        $latestSerialCode = $dataOfLatestSerialCode ? $dataOfLatestSerialCode->final_codes : null;

        return Common::generateCommonCodes($typeAbbreviation, $agencyCode, $latestSerialCode);
    }
}
