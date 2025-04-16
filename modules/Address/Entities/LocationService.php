<?php

namespace Modules\Address\Entities;

use Exception;
use Modules\SystemCore\Entities;
use Illuminate\Support\Facades\Http;

class LocationService
{
    private static $config = [
        'url'    => null,
        'key'    => null
    ];

    private static $keys = [
        'country'      => ['country'],
        'city'         => ['administrative_area_level_1'],
        'municipality' => ['administrative_area_level_2', 'administrative_area_level_3', 'locality'],
        'neighborhood' => ['sublocality_level_1', 'neighborhood', 'administrative_area_level_4'],
        'postal_code'  => ['postal_code'],
        'street'       => ['route'],
        'full_address' => ['formatted_address'],
    ];

    private $request = [
        'lat'     => null,
        'lng'     => null,
        'address' => null,
    ];

    private $response = [
        'ar' => null,
        'en' => null,
    ];

    private $data = [
        'ar' => [
            'country'      => null,
            'city'         => null,
            'municipality' => null,
            'neighborhood' => null,
            'postal_code'  => null,
            'street'       => null,
            'full_address' => null,
            'lat'          => null,
            'lng'          => null,
        ],
        'en' => [
            'country'      => null,
            'city'         => null,
            'municipality' => null,
            'neighborhood' => null,
            'postal_code'  => null,
            'street'       => null,
            'full_address' => null,
            'lat'          => null,
            'lng'          => null,
        ],
    ];

    public function __construct()
    {
        static::$config['url'] = config('address.google.url');
        static::$config['key'] = config('address.google.key');
    }

    public function newInstance()
    {
        return new self;
    }

    public function geo($lat, $lng)
    {
        $this->request['lat'] = $lat;
        $this->request['lng'] = $lng;

        $this->response['ar'] = $this->requestGeo('ar');
        $this->response['en'] = $this->requestGeo('en');

        $this->data['ar']     = static::mapResponseToData($this->response['ar']);
        $this->data['en']     = static::mapResponseToData($this->response['en']);

        try {
            $osm = new OpenStreetMapService;
            $osm->geo($lat, $lng);
            $this->data['ar'] = static::merge($this->data['ar'], $osm->data('ar'));
            $this->data['en'] = static::merge($this->data['en'], $osm->data('en'));
        } 
        catch (Exception $e) {
            throw $e;
        }

        return $this;
    }

    public function search(string $address)
    {
        $this->request['address'] = $address;

        $this->response['ar'] = $this->requestSearch('ar');
        $this->response['en'] = $this->requestSearch('en');

        $this->data['ar']     = static::mapResponseToData($this->response['ar']);
        $this->data['en']     = static::mapResponseToData($this->response['en']);

        try {
            $osm = new OpenStreetMapService;
            $osm->search($address);
            $this->data['ar'] = static::merge($this->data['ar'], $osm->data('ar'));
            $this->data['en'] = static::merge($this->data['en'], $osm->data('en'));
        } 
        catch (Exception $e) {
            // throw $e;
        }

        return $this;
    }

    public function response($locale)
    {
        return $this->response[$locale] ?? null;
    }

    public function request()
    {
        return $this->request ?? null;
    }

    public function data($locale, $key = null)
    {
        return ! $key ? ($this->data[$locale] ?? null) : $this->data[$locale][$key];
    }

    public function requestGeo($locale)
    {
        return static::sendRequest([
            'language' => (string) $locale,
            'latlng'   => (string) ($this->request['lat'] . ',' . $this->request['lng']),
        ]);
    }

    public function requestSearch($locale)
    {
        return static::sendRequest([
            'language' => (string) $locale,
            'address'  => (string) $this->request['address'],
        ]);
    }

    public static function merge(array $current, array $new)
    {
        $result = [];

        foreach ($current as $key => $value) {
            $result[$key] = ! empty($value) ? $value : ($new[$key] ?? null);
        }
        $result['full_address'] = $new['full_address'] ?? $current['full_address'];

        return $result;
    }

    public static function sendRequest(array $data = [])
    {
        $data = array_merge([
            'key' => (string) static::$config['key'],
        ], $data);

        $response = Http::withOptions([
            'verify' => false,
        ])
        ->get(static::$config['url'], $data)
        ->json();

        if ($response['status'] != 'OK') {
            throw new Exception($response['status'], 500);
        }

        return $response;
    }

    public static function mapResponseToData($response)
    {
        if (empty($response) || empty($response['results'])) {
            return [
                'country'      => null,
                'city'         => null,
                'municipality' => null,
                'neighborhood' => null,
                'postal_code'  => null,
                'street'       => null,
                'full_address' => null,
                'lat'          => null,
                'lng'          => null,
            ];
        }

        $response   = $response['results'][0];
        $components = collect($response['address_components']);

        $data = [
            'country'      => $components->filter(fn($item) => collect($item['types'])->intersect(static::$keys['country'])->isNotEmpty())->first(),
            'city'         => $components->filter(fn($item) => collect($item['types'])->intersect(static::$keys['city'])->isNotEmpty())->first(),
            'municipality' => $components->filter(fn($item) => collect($item['types'])->intersect(static::$keys['municipality'])->isNotEmpty())->first(),
            'neighborhood' => $components->filter(fn($item) => collect($item['types'])->intersect(static::$keys['neighborhood'])->isNotEmpty())->first(),
            'postal_code'  => $components->filter(fn($item) => collect($item['types'])->intersect(static::$keys['postal_code'])->isNotEmpty())->first(),
            'street'       => $components->filter(fn($item) => collect($item['types'])->intersect(static::$keys['street'])->isNotEmpty())->first(),
            'full_address' => $response['formatted_address'] ?? null,
            'lat'          => empty($response['geometry']) ? null : (! empty($response['geometry']['location']) ? (string) $response['geometry']['location']['lat'] : null),
            'lng'          => empty($response['geometry']) ? null : (! empty($response['geometry']['location']) ? (string) $response['geometry']['location']['lng'] : null),
        ];

        return $data;
    }

    public function firstOrFail()
    {
        $ar = $this->data('ar');
        $en = $this->data('en');

        if (! $ar['country'] || ! $en['country']) {
            throw new Exception('country_not_found', 500);
        }
        if (! $ar['city'] || ! $en['city']) {
            throw new Exception('city_not_found', 500);
        }
        if (! $ar['municipality'] || ! $en['municipality']) {
            throw new Exception('municipality_not_found', 500);
        }
        if (! $ar['neighborhood'] || ! $en['neighborhood']) {
            throw new Exception('neighborhood_not_found', 500);
        }

        $country = Entities\Country::where(function($query) use ($ar, $en) {
            $query->where('sortname', $ar['country']['short_name']);
        })
        ->firstOrFail();

        $city = Entities\City::where('country_id', $country->id)->where(function($query) use ($ar, $en) {
            $query->where('name_ar'  , $ar['city']['long_name']);
            $query->orWhere('name_en', $en['city']['long_name']);
        })
        ->firstOrFail();

        $municipality = Entities\Municipality::where('city_id', $city->id)->where(function($query) use ($ar, $en) {
            $query->where('name_ar'  , $ar['municipality']['long_name']);
            $query->orWhere('name_en', $en['municipality']['long_name']);
        })
        ->firstOrFail();
        
        $neighborhood = Entities\Neighbourhood::where('municipality_id', $municipality->id)->where(function($query) use ($ar, $en) {
            $query->where('name_ar'  , $ar['neighborhood']['long_name']);
            $query->orWhere('name_en', $en['neighborhood']['long_name']);
        })
        ->firstOrFail();

        return [
            'country'      => $country,
            'city'         => $city,
            'municipality' => $municipality,
            'neighborhood' => $neighborhood,
        ];
    }

    public function firstOrCreate()
    {
        $ar = $this->data('ar');
        $en = $this->data('en');

        if (! $ar['country'] || ! $en['country']) {
            throw new Exception('country_not_found', 500);
        }
        if (! $ar['city'] || ! $en['city']) {
            throw new Exception('city_not_found', 500);
        }
        if (! $ar['municipality'] || ! $en['municipality']) {
            throw new Exception('municipality_not_found', 500);
        }

        $country = Entities\Country::where(function($query) use ($ar, $en) {
            $query->where('sortname', $ar['country']['short_name']);
        })
        ->firstOrFail();

        $city = Entities\City::where('country_id', $country->id)->where(function($query) use ($ar, $en) {
            $query->where('name_ar'  , $ar['city']['long_name']);
            $query->orWhere('name_en', $en['city']['long_name']);
        })
        ->firstOrFail();

        $municipality = Entities\Municipality::where('city_id', $city->id)->where(function($query) use ($ar, $en) {
            $query->where('name_ar'  , $ar['municipality']['long_name']);
            $query->orWhere('name_en', $en['municipality']['long_name']);
        })
        ->first();

        if (! $municipality) {
            $municipality = new Entities\Municipality;
            $municipality->country_id         = $country->id;
            $municipality->city_id            = $city->id;
            $municipality->name_ar            = $ar['municipality']['long_name'];
            $municipality->name_en            = $en['municipality']['long_name'];
            $municipality->sys_status         = 'ACTIVE';
            $municipality->created_by_user_id = auth()->id();
            $municipality->updated_by_user_id = null;
            $municipality->save();
        }
        
        $neighborhood = (! $ar['neighborhood'] || ! $en['neighborhood']) ? null : Entities\Neighbourhood::where('municipality_id', $municipality->id)->where(function($query) use ($ar, $en) {
            $query->where('name_ar'  , $ar['neighborhood']['long_name']);
            $query->orWhere('name_en', $en['neighborhood']['long_name']);
        })
        ->first();

        if ($ar['neighborhood'] && $en['neighborhood'] && ! $neighborhood) {
            $neighborhood = new Entities\Neighbourhood;
            $neighborhood->country_id         = $country->id;
            $neighborhood->city_id            = $city->id;
            $neighborhood->municipality_id    = $municipality->id;
            $neighborhood->name_ar            = $ar['neighborhood']['long_name'];
            $neighborhood->name_en            = $en['neighborhood']['long_name'];
            $neighborhood->sys_status         = 'ACTIVE';
            $neighborhood->created_by_user_id = auth()->id();
            $neighborhood->updated_by_user_id = null;
            $neighborhood->save();
        }

        return [
            'country'      => $country,
            'city'         => $city,
            'municipality' => $municipality,
            'neighborhood' => $neighborhood,
        ];
    }
}
