<?php

namespace App\Model;

class FileStorageWrapper implements IStorage
{
    /** @var string  */
    private $path;

    /** @var  mixed */
    private $content;

    /** @var  CipherWrapper */
    private $cipher;

    /** @var bool|null */
    private $initialized = null;

    /** @var array */
    private $possibleKeys = ['appKey', 'appSecret', 'accessToken', 'dropboxUID'];

    public function __construct($filename, CipherWrapper $cipher)
    {
        $this->cipher = $cipher;
        if (substr($filename, 0, 1) != '/') {
            $this->path = __DIR__ . '/../' . $filename;
        } else {
            $this->path = $filename;
        }
        $this->createFileIfDoesNotExists();
        $this->loadContent();
    }

    private function createFileIfDoesNotExists()
    {
        if (!file_exists($this->path)) {
            if (file_put_contents($this->path, '{}') == false) {
                throw new \Exception("Can't write file $this->path, create it please manually.");
            }
        }
    }

    /**
     * @return array
     * @throws \Exception
     */
    private function safeDecode()
    {
        $content = file_get_contents($this->path);
        $result = json_decode($content, true);
        if (null === $result) {
            throw new \Exception("File content isn't valid JSON");
        } else {
            return $result;
        }
    }

    /**
     * @return bool
     */
    public function isInitialized()
    {
        try {
            $array = $this->safeDecode();
            if (!array_key_exists('accessToken', $array)) {
                return false;
            }
        } catch (\Exception $e) {
            return false;
        }
        return true;
    }

    /**
     * @return bool
     */
    public function areAppKeysSet()
    {
        try {
            $array = $this->safeDecode();
            if (!array_key_exists('appSecret', $array)) {
                return false;
            }
        } catch (\Exception $e) {
            return false;
        }
        return true;
    }


    private function loadContent()
    {
        $encodedContent = json_decode(file_get_contents($this->path), true);
        if (is_array($encodedContent)) {
            foreach ($encodedContent as $key => $value) {
                $this->content[$key] = $this->cipher->decrypt($value);
            }
        }
    }

    private function saveContent()
    {
        $encodedContent = [];
        foreach ($this->content as $key => $value) {
            $encodedContent[$key] = $this->cipher->encrypt($value);
        }
        file_put_contents($this->path, json_encode($encodedContent, JSON_PRETTY_PRINT));
    }

    /**
     * @param string $key Possible keys: appKey, appSecret, accessToken, dropboxUID
     * @return string
     * @throws \Exception
     */
    public function get($key)
    {
        if (in_array($key, $this->possibleKeys)) {
            return $this->content[$key];
        } else {
            $message = 'Possible keys are only: ' . implode(', ', $this->possibleKeys);
            throw new \Exception($message);
        }
    }

    /**
     * @param string $key Possible keys: appKey, appSecret, accessToken, dropboxUID
     * @param string $value
     * @throws \Exception
     */
    public function set($key, $value)
    {
        if (in_array($key, $this->possibleKeys)) {
            $this->content[$key] = $value;
            $this->saveContent();
        } else {
            $message = 'Possible keys are only: ' . implode(', ', $this->possibleKeys);
            throw new \Exception($message);
        }
    }
}
