<?php
namespace lroman242\LaravelSettings;

interface SettingsStorageInterface {

    public function where($key, $value);

    public function delete();

    public function save();

    public function firstOrFail();
}