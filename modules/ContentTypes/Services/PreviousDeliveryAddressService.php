<?php

namespace Modules\ContentTypes\Services;

use Modules\ContentTypes\Entities\Address;

class PreviousDeliveryAddressService
{
    private const RESULT_LIMIT = 20;
    private const QUERY_LIMIT = 250;

    public function search(int $companyUserId, int $cityId, ?string $search = null): array
    {
        $query = Address::query()
            ->select('lgl_addressess.*')
            ->join('lgl_shippments as address_shipments', function($join) use($companyUserId) {
                $join->on('address_shipments.id', '=', 'lgl_addressess.related_id')
                    ->where('address_shipments.company_user_id', '=', $companyUserId)
                    ->whereNull('address_shipments.deleted_at');
            })
            ->where('lgl_addressess.related_type', 'SHIPPMENT')
            ->where('lgl_addressess.address_tag', 'SHIPPMENT_OFFLOADING_POINT')
            ->where('lgl_addressess.city_id', $cityId)
            ->whereNotNull('lgl_addressess.lat')
            ->whereNotNull('lgl_addressess.lng')
            ->latest('lgl_addressess.id');

        $search = trim((string) $search);
        if ($search !== '') {
            $like = '%' . addcslashes($search, '\\%_') . '%';
            $query->where(function($query) use($like) {
                $query->where('lgl_addressess.full_address', 'LIKE', $like)
                    ->orWhere('lgl_addressess.primary_contact_company_name', 'LIKE', $like)
                    ->orWhere('lgl_addressess.primary_contact_name', 'LIKE', $like)
                    ->orWhere('lgl_addressess.primary_contact_email', 'LIKE', $like)
                    ->orWhere('lgl_addressess.primary_contact_phone_number', 'LIKE', $like);
            });
        }

        return $query->limit(self::QUERY_LIMIT)
            ->get()
            ->unique(function(Address $address) {
                return $this->duplicateKey($address);
            })
            ->take(self::RESULT_LIMIT)
            ->map(function(Address $address) {
                return [
                    'source_id'                    => $address->id,
                    'primary_contact_company_name' => $address->primary_contact_company_name,
                    'primary_contact_name'         => $address->primary_contact_name,
                    'primary_contact_email'        => $address->primary_contact_email,
                    'primary_contact_phone_number' => $address->primary_contact_phone_number,
                    'full_address'                 => $address->full_address,
                    'public_notes'                 => $address->public_notes,
                    'private_notes'                => $address->private_notes,
                    'lat'                          => is_null($address->lat) ? null : (float) $address->lat,
                    'lng'                          => is_null($address->lng) ? null : (float) $address->lng,
                    'round_trip'                   => $address->round_trip ?: 'NO',
                ];
            })
            ->values()
            ->all();
    }

    private function duplicateKey(Address $address): string
    {
        return sha1(json_encode([
            $this->normalize($address->primary_contact_company_name),
            $this->normalize($address->primary_contact_name),
            $this->normalize($address->primary_contact_email),
            preg_replace('/\D+/', '', (string) $address->primary_contact_phone_number),
            $this->normalize($address->full_address),
            is_null($address->lat) ? null : round((float) $address->lat, 6),
            is_null($address->lng) ? null : round((float) $address->lng, 6),
        ]));
    }

    private function normalize(?string $value): string
    {
        return mb_strtolower(preg_replace('/\s+/u', ' ', trim((string) $value)), 'UTF-8');
    }
}
