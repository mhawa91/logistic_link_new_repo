<?php

namespace Modules\Address\Entities;

use Exception;
use Modules\SystemCore\Entities;
use Illuminate\Support\Facades\Http;

class OpenStreetMapService
{
    private static $config = [
        'geo_url'    => null,
        'geo_key'    => null,
        'search_url' => null,
        'search_key' => null
    ];

    private static $keys = [
        'country'      => ['country'],
        'city'         => ['state', 'province'],
        'municipality' => ['city', 'town'],
        // 'neighborhood' => ['suburb', 'neighborhood'],
        'neighborhood' => ['suburb'],
        'postal_code'  => ['postcode'],
        'street'       => ['road'],
        'full_address' => ['display_name'],
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
        static::$config['geo_url'] = config('address.osm_geo.url');
        static::$config['geo_key'] = config('address.osm_geo.key');

        static::$config['search_url'] = config('address.osm_search.url');
        static::$config['search_key'] = config('address.osm_search.key');
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

        return $this;
    }

    public function search(string $address)
    {
        $this->request['address'] = $address;

        $this->response['ar'] = ! empty($result = $this->requestSearch('ar')) ? $result[0] : null;
        $this->response['en'] = ! empty($result = $this->requestSearch('en')) ? $result[0] : null;

        $this->request['address'] = null;

        $this->request['lat'] = $this->response['ar']['lat'] ?? null;
        $this->request['lng'] = $this->response['ar']['lon'] ?? null;

        return ! $this->request['lat'] || ! $this->request['lng'] ? null : $this->geo($this->request['lat'], $this->request['lng']);
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
        return ! $key ? ($this->data[$locale] ?? null) : ($this->data[$locale][$key] ?? null);
    }

    public function requestGeo($locale)
    {
        return static::sendRequest([
            'language'        => (string) $locale,
            'lat'             => (string) $this->request['lat'],
            'lon'             => (string) $this->request['lng'],
            'zoom'            => (string) '18',
            'format'          => (string) 'jsonv2',
            // area boundries: 0 | 1
            // 'polygon_geojson' => (string) '1',
        ]);
    }

    public function requestSearch($locale)
    {
        return static::sendRequest([
            'language' => (string) $locale,
            'address'  => (string) $this->request['address'],
            'q'        => (string) $this->request['address'],
            'zoom'     => (string) '18',
            'format'   => (string) 'jsonv2',
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
            'key' => (string) ! empty($data['address']) ? static::$config['search_key'] : static::$config['geo_key'],
        ], $data);

        $response = Http::withHeaders([
            'Accept-Language' => $data['language'] ?? app()->getLocale()
        ])
        ->withOptions([
            'verify' => false,
        ])
        ->get(! empty($data['address']) ? static::$config['search_url'] : static::$config['geo_url'], $data)
        ->json();

        if (! $response) {
            return null;
        }

        return $response;
    }

    public static function mapResponseToData($response)
    {
        if (empty($response)) {
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
        
        $response   = $response;
        $components = collect($response['address']);

        $data = [
            'country'      => ! isset($response['address'][static::$keys['country'][0]]) ? null : [
                'long_name'  => $response['address'][static::$keys['country'][0]],
                'short_name' => strtoupper($response['address']['country_code']),
            ],
            'city'         => ! isset($response['address'][static::$keys['city'][0]]) ? null : [
                'long_name'  => $response['address'][static::$keys['city'][0]],
                'short_name' => $response['address'][static::$keys['city'][0]],
            ],
            'municipality' => ! isset($response['address'][static::$keys['municipality'][0]]) ? null : [
                'long_name'  => $response['address'][static::$keys['municipality'][0]],
                'short_name' => $response['address'][static::$keys['municipality'][0]],
            ],
            'neighborhood' => ! isset($response['address'][static::$keys['neighborhood'][0]]) ? null : [
                'long_name'  => $response['address'][static::$keys['neighborhood'][0]],
                'short_name' => $response['address'][static::$keys['neighborhood'][0]],
            ],
            'postal_code'  => ! isset($response['address'][static::$keys['postal_code'][0]]) ? null : [
                'long_name'  => $response['address'][static::$keys['postal_code'][0]],
                'short_name' => $response['address'][static::$keys['postal_code'][0]],
            ],
            'street'       => ! isset($response['address'][static::$keys['street'][0]]) ? null : [
                'long_name'  => $response['address'][static::$keys['street'][0]],
                'short_name' => $response['address'][static::$keys['street'][0]],
            ],
            'full_address' => $response[static::$keys['full_address'][0]] ?? null,
            'lat'          => empty($response['lat']) ? null : (string) $response['lat'],
            'lng'          => empty($response['lon']) ? null : (string) $response['lon'],
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
