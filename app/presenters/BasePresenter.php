<?php

namespace App\Presenters;

use Nette,
	App\Model;


/**
 * Base presenter for all application presenters.
 */
abstract class BasePresenter extends Nette\Application\UI\Presenter
{
    /** @var Model\DropboxWrapper @inject */
    public $dropbox;

    public function beforeRender()
    {
        parent::beforeRender();
        if ($this->getName() != "Sign") {
            $this->dropbox->setAccessTokens();
        }

        $this->template->loggedIn = $this->getUser()->isLoggedIn();
        $this->template->url = $this->context->httpRequest->getUrl()->getAbsoluteUrl();
        $this->template->presenter = $this->getName();
    }

}
