<?php

namespace Modules\ContentManager\Entities\Contracts;

use Modules\SystemCore\System\Contracts\EloquentInterface;
use Modules\ContentManager\Entities\Content;

interface CategoryInterface extends EloquentInterface
{
    /**
     * @param string $IDS
     * @return self
     */
    public function excludeIds(string $IDS) : self;
    
    /**
     * @param string $Type
     * @return self
     */
    public function ofType(string $Type) : self;

    /**
     * @param array
     * @return void
     */
    public static function register(array $Content) : void;

    /**
     * @param void
     * @return array
     */
    public static function getTypes() : array;

    /**
     * @param string
     * @return array|null
     */
    public static function getType(string $Type) :? array;

    /**
     * @param int $level
     * @return self
     */
    public function ofLevel(int $level) : self;

    /**
     * @param string $type
     * @param int $limit
     * @param array|null $options
     * @return array
     */
    public function frontendList(string $type, int $limit = null, ?array $options = []): array;

    /**
     * @param string $id
     * @return Content
     */
    public function frontendRead(string $id):? Content;

    /**
     * @param void
     * @return Collection
     */
    public function filterData();
}