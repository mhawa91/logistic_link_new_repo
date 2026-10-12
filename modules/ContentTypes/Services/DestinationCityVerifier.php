<?php

namespace Modules\ContentTypes\Services;

use Illuminate\Support\Facades\Cache;
use Modules\Address\Entities\OpenStreetMapService;
use Modules\SystemCore\Entities\City;

class DestinationCityVerifier
{
    private $openStreetMap;

    public function __construct(OpenStreetMapService $openStreetMap)
    {
        $this->openStreetMap = $openStreetMap;
    }

    public function verify(int $cityId, $lat, $lng): bool
    {
        $city = City::where('country_id', 191)->findOrFail($cityId);
        $cacheKey = 'shippment_destination_city:' . sha1(
            number_format((float) $lat, 5, '.', '') . ',' . number_format((float) $lng, 5, '.', '')
        );

        $location = Cache::remember($cacheKey, now()->addDays(30), function () use ($lat, $lng) {
            $response = $this->openStreetMap->newInstance()->geoLocale($lat, $lng, 'ar')->response('ar');
            $address = $response['address'] ?? [];
            if (empty($address)) {
                throw new \RuntimeException('OpenStreetMap returned no address data.');
            }

            return [
                'country_code' => strtoupper((string) ($address['country_code'] ?? '')),
                'names' => array_values(array_filter([
                    $address['state'] ?? null,
                    $address['province'] ?? null,
                    $address['region'] ?? null,
                    $address['state_district'] ?? null,
                    $address['county'] ?? null,
                    $address['municipality'] ?? null,
                    $address['city'] ?? null,
                    $address['town'] ?? null,
                    $address['village'] ?? null,
                ])),
            ];
        });

        $expected = array_filter([$this->normalize($city->name_ar), $this->normalize($city->name_en)]);
        $actual = array_filter(array_map([$this, 'normalize'], $location['names']));

        return $location['country_code'] === 'SA' && ! empty(array_intersect($expected, $actual));
    }

    public function normalize($value): string
    {
        $value = mb_strtolower(trim((string) $value), 'UTF-8');
        $value = preg_replace('/[\x{064B}-\x{065F}\x{0670}]/u', '', $value);
        $value = str_replace(['أ', 'إ', 'آ'], 'ا', $value);
        $value = preg_replace('/^(?:منطقة|المنطقة|محافظة|المحافظة|مقاطعة|المقاطعة)\s+/u', '', $value);
        $value = preg_replace('/\b(?:province|region|state|governorate)\b/u', '', $value);
        $value = preg_replace('/^(?:al|ar|ash)[\s-]+/u', '', $value);
        $value = preg_replace('/[^a-z0-9\x{0600}-\x{06FF}]+/u', ' ', $value);

        return trim($value);
    }
}
