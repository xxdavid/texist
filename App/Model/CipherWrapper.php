<?php

namespace App\Model;

use Zend\Crypt\BlockCipher;

class CipherWrapper
{
    /** @var BlockCipher  */
    private $cipher;

    public function __construct($key)
    {
        $this->cipher = BlockCipher::factory('mcrypt');
        $this->cipher->setKey($key);

    }

    public function encrypt($string)
    {
        return $this->cipher->encrypt($string);
    }

    public function decrypt($string)
    {
        return $this->cipher->decrypt($string);
    }
}