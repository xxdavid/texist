<?php

namespace App\Presenters;

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
