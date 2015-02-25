<?php

namespace App\Presenters;

use Nette;
use App\Model;


/**
 * Homepage presenter.
 */
class HomepagePresenter extends BasePresenter
{
    public function renderDefault()
    {
        $this->template->filesList = $this->dropbox->getFilesList('', ['texy'], false);
    }
}
