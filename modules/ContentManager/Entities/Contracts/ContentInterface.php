<?php

namespace Modules\ContentManager\Entities\Contracts;

use Modules\SystemCore\System\Contracts\EloquentInterface;
use Modules\ContentManager\Entities\Content;

interface ContentInterface extends EloquentInterface
{
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
     * @param string $type
     * @param int|null $limit
     * @return Collection
     */
    public function frontendSection(string $type, ?int $limit = null);

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
     * @param Content $Item
     * @return Collection
     */
    public function frontendRelated(Content $Item);
}