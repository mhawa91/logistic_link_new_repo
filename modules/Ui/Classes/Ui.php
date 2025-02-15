<?php

namespace Modules\Ui\Classes;

class Ui
{
    protected $icons         = null;

    protected $mainMenuItems = null;

    public function __construct()
    {
        $this->icons         = collect([]);
        
        $this->mainMenuItems = collect([]);
    }

    public function icon($name, array $icon = [])
    {
        if (empty($icon)) {
            return $this->icons->where('name', $name)->first() ?? $this->defaultIcon();
        }
        $icon = array_merge($this->defaultIcon(), $icon);
        $this->icons->push($icon);
        return $this;
    }

    public function defaultIcon()
    {
        return [
            'name' => 'DEFAULT',
            'type' => 'em', //em | i | svg | span
            'data' => 'icon ni ni-file-docs',
        ];
    }

    public function registerMainMenuItem(array $item) : self
    {
        $item['parent_id'] = $item['type'] == 'HEADER' ? null : $item['parent_id'];
        $item = array_merge([
            'id'        => '',
            'parent_id' => null,
            'ordering'  => 0,
            'type'      => 'MENU', //MENU | HEADER
            'link'      => '',
            'text'      => '',
            'icon'      => $this->defaultIcon(),
            'show'      => function() { return true; },
        ], $item);
        $item['is_active'] = request()->url() == $item['link'] ? true : false;
        $item['sub_items'] = [];

        $this->mainMenuItems->push($item);
        return $this;
    }

    public function mainMenu($parentId = null) : array
    {
        $mainMenu = [];
        foreach ($this->mainMenuItems->where('parent_id', $parentId)->sortBy('ordering') as $key => $item) {
            $show = false;
            if ($item['show'] instanceof \Closure) { $show = (bool) $item['show'](); } else { $show = (bool) $item['show']; }
            if ($show) {
                $_SUBS_            = collect($this->mainMenu($item['id']));
                $item['sub_items'] = $_SUBS_->toArray();
                $item['is_active'] = $item['is_active'] || !is_null($_SUBS_->where('is_active', true)->first());
                $mainMenu[] = $item;
            }
        }
        return $mainMenu;
    }
}
