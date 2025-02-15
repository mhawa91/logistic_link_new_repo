<?php

namespace Modules\SystemCore\Entities\Contracts;

use Modules\SystemCore\System\Contracts\EloquentInterface;

interface SystemConfigInterface extends EloquentInterface
{
    /**
     * @param array
     * @return void
     */
    public static function register(array $Config) : void;

    /**
     * @param void
     * @return array
     */
    public static function load() : array;

    /**
     * @param string $Key
     * @param string $Default
     * @return any
     */
    public static function of(string $Key, $Default = null);

    /**
     * @param void
     * @return array
     */
    public static function getConfigs() : array;

    /**
     * @param string
     * @return array|null
     */
    public static function getConfig(string $Config) :? array;

    /**
     * @param array
     * @return void
     */
    public static function save(array $Attributes) : void;
}