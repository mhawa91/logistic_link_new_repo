<?php

namespace Modules\ContentManager\Entities\Contracts;

use Modules\SystemCore\System\Contracts\EloquentInterface;

interface SectionInterface extends EloquentInterface
{
    /**
     * @param array $Section
     * @return void
     */
    public static function register(array $Section) : void;

    /**
     * @param void
     * @return array
     */
    public static function load() : array;

    /**
     * @param void
     * @return array
     */
    public static function loadTranslated() : array;

    /**
     * @param string $Tag
     * @param any $Default
     * @return array
     */
    public static function of(string $Tag, $Default = null) :? array;

    /**
     * @param string $Tag
     * @param any $Default
     * @return array
     */
    public static function ofTranslated(string $Tag, $Default = null) :? array;

    /**
     * @param void
     * @return array
     */
    public static function get() : array;

    /**
     * @param array $Sections
     * @return void
     */
    public static function save(array $Sections) : void;

    /**
     * @param string $tag
     * @param array $data
     * @return void
     */
    public function insert(string $tag, array $data): void;
}