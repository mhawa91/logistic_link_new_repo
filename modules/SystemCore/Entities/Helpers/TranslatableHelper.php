<?php

namespace Modules\SystemCore\Entities\Helpers;

trait TranslatableHelper
{
    protected function loadTranslationIfNotLoaded()
    {
        if (! $this->relationLoaded('translations') ) {
            $this->load('translations');
        }
    }

    public function __(string $column, $locale = null, $default = null)
    {
        $locale = $locale ? $locale : app()->getLocale();
        
        $this->loadTranslationIfNotLoaded();

        $REQUESTED = $this->translations->where('locale', $locale)->whereNotNull($column)->first()
        ?? $this->translations->where('default', 'Y')->whereNotNull($column)->first()
        ?? $this->translations->first();

        return is_null($REQUESTED) ? $default : $REQUESTED->{ $column };
    }

    public function __f(string $column, $locale = null, $default = null)
    {
        $locale = $locale ? $locale : app()->getLocale();
        
        $this->loadTranslationIfNotLoaded();

        $REQUESTED = $this->translations->where('locale', $locale)->first();

        return is_null($REQUESTED) ? $default : $REQUESTED->{ $column };
    }
}
