<?php

namespace lroman242\LaravelSettings\Models;

use Illuminate\Database\Eloquent\Model;

class Settings extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * Set setting value as json if array.
     *
     * @param  string  $value
     *
     * @return string
     */
    public function setValueAttribute($value)
    {
        $this->attributes['value'] = serialize($value);
    }

    /**
     * Return setting value as json if array.
     *
     * @param  string  $value
     *
     * @return string
     */
    public function getValueAttribute($value)
    {
        return unserialize($value);
    }
}