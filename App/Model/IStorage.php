<?php

namespace App\Model;

interface IStorage
{
    public function isInitialized();
    public function areAppKeysSet();
    public function get($key);
    public function set($key, $value);
}