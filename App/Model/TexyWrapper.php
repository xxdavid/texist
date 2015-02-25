<?php

namespace App\Model;

use Nette;
use Texy;

class TexyWrapper extends Nette\Object
{
    /** @var Texy */
    private $texy;

    public function __construct(Texy $texy)
    {
        $this->texy = $texy;
    }

    public function process($text)
    {
        return $this->texy->process($text);
    }
}
