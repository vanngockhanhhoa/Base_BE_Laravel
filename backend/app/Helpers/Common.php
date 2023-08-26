<?php

namespace Helper;

use App\Models\Provider;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class Common
{
    /**
     * @param UploadedFile $file
     * @param string $path
     * @param null $shopId
     * @return mixed
     */
    public static function uploadFile(UploadedFile $file, string $path = '', $userId = null)
    {
        $userId = $userId ?? Auth::id();
        $fileName = time() . '.' . $file->getClientOriginalExtension();
        return $file->storeAs($path, $fileName);
    }

    public static function uploadPhotoToS3($file, $path = 'photos'): string
    {
        // path can't not inclue character [/]
        return Storage::disk('s3')->put($path, $file, 'public');
    }

    public static function checkTokenFB($token): bool
    {
        try {
            $client = new \GuzzleHttp\Client();
            $url = 'https://graph.facebook.com/me?access_token=' . $token;
            $res = $client->request('GET', $url);
            if ($res->getStatusCode() == 200) {
                return true;
            }
        } catch (\Exception $e) {
            return false;
        }

        return false;
    }

    /**
     * Generate common codes
     *
     * @param string $typeAbbre
     * @param string|null $agencyCode
     * @param string|null $lastestSerialCode
     *
     * @return string
     */
    public static function generateCommonCodes(string $typeAbbre, string $agencyCode = null, string $lastestSerialCode = null): string
    {

        // Get id type abbrevation and serial number
        $currentSerialNumber = self::getCurrentSerialNumber($typeAbbre, $lastestSerialCode);
        $currentSerialLetter = $lastestSerialCode ? Str::substr($lastestSerialCode, -1) : 'A';
        // Get next serial number and letter
        $nextSerialNumber = ++$currentSerialNumber;
        $nextSerialLetter = Str::upper($currentSerialLetter);

        if ($nextSerialNumber > 999) {
            $nextSerialNumber = 1;
            $nextSerialLetter = chr(ord($nextSerialLetter) + 1);
        }

        switch ($typeAbbre) {
            case config('common.list_category_abbrevation.schedule'):
                return sprintf(
                    "%s-%02d",
                    $typeAbbre,
                    $nextSerialNumber
                );
            case config('common.list_category_abbrevation.provider_transportation'):
                return sprintf(
                    "%s%03d%s",
                    $typeAbbre,
                    $nextSerialNumber,
                    $nextSerialLetter
                );
            default:
                return sprintf(
                    "%s%02d%s-%02d%03d%s",
                    $typeAbbre,
                    Str::substr(date('y'), -2),
                    $agencyCode,
                    date('m'),
                    $nextSerialNumber,
                    $nextSerialLetter
                );
        }
    }

    public function getCurrentSerialNumber($typeAbbre, $lastestSerialCode = null): string
    {
        if (!$lastestSerialCode) {
            return 0;
        }
        // remake input code
        try {
            switch ($typeAbbre) {
                case config('common.list_category_abbrevation.provider_transportation'):
                    if (!Provider::isValidCode($lastestSerialCode)) {
                        throw new Exception("Code format invalid!");
                    }
                    $lastestSerialCode = Str::replace($typeAbbre, '', $lastestSerialCode);
                    return number_format(Str::onlyNumbers($lastestSerialCode));
                case config('common.list_category_abbrevation.schedule'):
                    return number_format(Str::onlyNumbers($lastestSerialCode));
                default:
                    return number_format(Str::onlyNumbers(Str::substr($lastestSerialCode, 0, 3)));
            }
        } catch (Exception $exception) {
            return number_format(Str::onlyNumbers(Str::substr($lastestSerialCode, 0, 3)));
        }
    }

    /**
     * decode the JSON data
     * @param string $string
     * @return object
     */
    public static function json_validate(string $string): object
    {
        // decode the JSON data
        $result = json_decode($string);
        // switch and check possible JSON errors
        switch (json_last_error()) {
            case JSON_ERROR_NONE:
                $error = ''; // JSON is valid // No error has occurred
                break;
            case JSON_ERROR_DEPTH:
                $error = 'The maximum stack depth has been exceeded.';
                break;
            case JSON_ERROR_STATE_MISMATCH:
                $error = 'Invalid or malformed JSON.';
                break;
            case JSON_ERROR_CTRL_CHAR:
                $error = 'Control character error, possibly incorrectly encoded.';
                break;
            case JSON_ERROR_SYNTAX:
                $error = 'Syntax error, malformed JSON.';
                break;
            // PHP >= 5.3.3
            case JSON_ERROR_UTF8:
                $error = 'Malformed UTF-8 characters, possibly incorrectly encoded.';
                break;
            // PHP >= 5.5.0
            case JSON_ERROR_RECURSION:
                $error = 'One or more recursive references in the value to be encoded.';
                break;
            // PHP >= 5.5.0
            case JSON_ERROR_INF_OR_NAN:
                $error = 'One or more NAN or INF values in the value to be encoded.';
                break;
            case JSON_ERROR_UNSUPPORTED_TYPE:
                $error = 'A value of a type that cannot be encoded was given.';
                break;
            default:
                $error = 'Unknown JSON error occured.';
                break;
        }
        if ($error !== '') {
            // throw the Exception or exit // or whatever :)
            exit($error);
        }
        // everything is OK
        return $result;
    }

    /**
     * @desc 1 => 0001
     * @param int number
     * @param string|null $code
     * @return string
     */
    public static function leadingZeros(int $number, string $code = null): string
    {
        $str = str_pad($number, 4, '0', STR_PAD_LEFT);
        if (!empty($code)) {
            return $code . '-' . $str;
        }
        return $str;
    }

    /**
     * @param $datetime
     *
     * @return string
     */
    public static function formatToTime($datetime): string
    {
        return Carbon::parse($datetime)->format('H:i');
    }

    /**
     * @return string
     */
    public static function delPrefix(): string
    {
        $timestamp = Carbon::now()->getTimestamp();
        return "del_{$timestamp}_";
    }

    /**
     * Escape wildcard characters
     *
     * @param $string
     * @return string
     */
    public static function escapeWildcard($string)
    {
        if ($string !== null) {
            $search = array(
                '\\',
                '%',
                '_',
                '*',
                '{',
                '}',
                '^',
                '&',
                '(',
                ')',
                '+',
                '"',
                '[',
                ']',
                '?',
                ':',
                '>',
                '<',
            );
            $replace = array(
                "\\\\\\\\",
                '\%',
                '\_',
                '\*',
                '\{',
                '\}',
                '\^',
                '\&',
                '\(',
                '\)',
                '\+',
                '\"',
                '\[',
                '\]',
                '\?',
                '\:',
                '\>',
                '\<',
            );
            return str_replace($search, $replace, $string);
        }
        return null;
    }

    /**
     * Create date with format
     *
     * @param $date
     * @param string $format
     * @return string
     */
    public static function formatDate($date, string $format='Y/m/d'): string
    {
        return Carbon::parse($date)->format($format);
    }

    /**
     * @param $phone_number
     * @return bool|int
     */
    public static function formatPhone($phone_number): bool|int
    {
        $pattern = '/^(\d|-)+$/';
        return preg_match($pattern, $phone_number);
    }

    /**
     * @param $fax
     * @return bool|int
     */
    public static function formatFax($fax): bool|int
    {
        $pattern = '/^(\d|-)+$/';
        return preg_match($pattern, $fax);
    }



    /**
     * @param $postal_code
     * @return string
     */
    public static function formatPostalCode($postal_code): string
    {
        $pattern = '/^\d{3}-\d{4}$/';
        return preg_match($pattern, $postal_code);
    }

    /**
     * @param $full_width_character
     * @return string
     */
    public static function formatFullWidthCharacter($full_width_character): string
    {
        $pattern = '/^[０-９ａ-ｚＡ-Ｚぁ-んァ-ン一-龥]*$/';
        return preg_match($pattern, $full_width_character);
    }

    /**
     * Convert json field pickup_circle to days in JP
     *
     * @param $pickupCircle
     * @return string
     */
    public static function getPickupCircle($pickupCircle)
    {
        $arrData = [];
        if ($pickupCircle->sunday) {
            $arrData[] = '日';
        }
        if ($pickupCircle->monday) {
            $arrData[] = '月';
        }
        if ($pickupCircle->tuesday) {
            $arrData[] = '火';
        }
        if ($pickupCircle->wednesday) {
            $arrData[] = '水';
        }
        if ($pickupCircle->thursday) {
            $arrData[] = '木';
        }
        if ($pickupCircle->friday) {
            $arrData[] = '金';
        }
        if ($pickupCircle->saturday) {
            $arrData[] = '土';
        }
        return implode(' ', $arrData);
    }

    /**
     * Calculator number between 2 days
     *
     * @param $startDate
     * @param $endDate
     * @return int
     */
    public static function calculatorNumbersOfRangerDate($startDate, $endDate)
    {
        $startDate = Carbon::parse($startDate);
        $endDate = Carbon::parse($endDate);
        return $endDate->diffInDays($startDate);
    }

    /**
     * Round down float number
     *
     * @param $floatNumber
     * @return int
     */
    public static function roundDownFloatNumber($floatNumber)
    {
        return (int)$floatNumber;
    }

    /**
     * get estimate shipping date by specified date and days
     *
     * @param $date
     * @param $days
     * @return int
     */
    public static function getShippingDate($date, $days)
    {
        $date = Carbon::parse($date);
        $days = (int)$days;

        $date->addWeekdays($days);
        $date = $date->format(DATE_FORMAT);

        return $date;
    }

    /**
     * Get previous month
     *
     * @param $inputDate
     * @return Carbon
     */
    public static function subMonth($inputDate)
    {
        $inputDay = Carbon::parse($inputDate)->day;
        return Carbon::parse($inputDate)->subDays($inputDay);
    }
}
