<?php

namespace Modules\SystemCore\System\Services;

trait ModelStatusTrait
{
    protected $statusRepo = [
        'ACTIVE'   => 'ACTIVE',
        'DISABLED' => 'DISABLED',
    ];

    /**
     * Boot the soft deleting trait for a model.
     *
     * @return void
     */
    public static function bootModelStatusTrait()
    {
        static::addGlobalScope('onlyEnabled', function() {
            
        });
    }

    /**
     * Get the name of the "sys_status" column.
     *
     * @return string
     */
    public function getSysStatusColumn()
    {
        return defined('static::SYS_STATUS') ? static::SYS_STATUS : 'sys_status';
    }

    /**
     * Get the fully qualified "deleted at" column.
     *
     * @return string
     */
    public function getQualifiedSysStatusColumn()
    {
        return $this->qualifyColumn($this->getSysStatusColumn());
    }

    public function scopeOnlyEnabled($query)
    {
        return $query->where($this->getQualifiedSysStatusColumn(), $this->statusRepo['ACTIVE']);
    }

    public function scopeOnlyDisabled($query)
    {
        return $query->where($this->getQualifiedSysStatusColumn(), $this->statusRepo['DISABLED']);
    }

    public function scopeWithDisabled($query)
    {
        return $query->where($this->getQualifiedSysStatusColumn(), $this->statusRepo['DISABLED']);
    }

    public function enable()
    {
        if ($this->fireModelEvent('enabling') === false) {
            return false;
        }

        $this->{$this->getSysStatusColumn()} = $this->statusRepo['ACTIVE'];

        $result = $this->save();

        $this->fireModelEvent('enabled', false);

        return $result;
    }

    public function disable()
    {
        if ($this->fireModelEvent('disabling') === false) {
            return false;
        }

        $this->{$this->getSysStatusColumn()} = $this->statusRepo['DISABLED'];

        $result = $this->save();

        $this->fireModelEvent('disabled', false);
        
        return $result;
    }
}