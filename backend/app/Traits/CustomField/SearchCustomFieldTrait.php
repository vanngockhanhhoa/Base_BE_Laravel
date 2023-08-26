<?php

namespace App\Traits\CustomField;

/**
 * Trait SearchCustomFieldTrait
 * @package App\Traits\CustomField
 * @return array Id|HashId match filter
 * @property Enum static
 */
trait SearchCustomFieldTrait
{
    public static function filterCustomFields($customFields, $allDatas): ?array
    {
        if (!$customFields) {
            return null;
        }
        $settingItemHashIds = [];
        foreach ($customFields as $hashId => $customField) {
            if ($customField) {
                $settingItemHashIds[] = $hashId;
            }
        }
        if (count($settingItemHashIds) == 0) {
            return null;
        }

        $results = [];
        $counter = 0;
        foreach ($allDatas as $allData) {
            $customDatas = data_get($allData, 'customData', []);

            $counter = 0;
            foreach ($customDatas as $customData) {
                $itemHashId = data_get($customData->settingItem, 'hash_id');
                if (!in_array($itemHashId, $settingItemHashIds)) {
                    continue;
                }
                if (is_array($customFields[$itemHashId])) {
                    $checkExisted = array_intersect(json_decode($customData['value']), $customFields[$itemHashId]);
                    if ($checkExisted && count($checkExisted) == count(json_decode($customData['value']))) {
                        $counter++;
                    }
                } elseif (strpos(json_decode($customData['value']), $customFields[$itemHashId]) !== false) {
                    $counter++;
                }
            }

            if ($counter == count($settingItemHashIds)) {
                $results[] = $allData->hash_id;
            }
        }

        return $results;
    }
}
