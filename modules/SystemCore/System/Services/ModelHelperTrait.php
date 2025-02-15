<?php

namespace Modules\SystemCore\System\Services;

trait ModelHelperTrait
{
    public static function permissionName($Action)
    {
        return static::class . '@' . $Action;
    }
}