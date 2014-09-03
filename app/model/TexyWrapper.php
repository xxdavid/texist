<?php

namespace App\Model;

use Nette,
    Texy\Texy,
    Texist;

class TexyWrapper extends Nette\Object implements Texist\ITexyWrapper
{
    /** @var  Texy */
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
