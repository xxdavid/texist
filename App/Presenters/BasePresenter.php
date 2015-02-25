<?php

namespace App\Presenters;

use Nette;
use App\Model;
use Kdyby\Autowired\AutowireProperties;


/**
 * Base presenter for all application presenters.
 */
abstract class BasePresenter extends Nette\Application\UI\Presenter
{
    use AutowireProperties;

    /** @var Model\IStorage @inject */
    public $storage;

    /**
     * @var Model\DropboxWrapper
     * @autowire
     */
    protected $dropbox;

    public function beforeRender()
    {
        parent::beforeRender();

        if ($this->getName() != 'Sign' and $this->getName() != 'Init') {
            if (!$this->storage->isInitialized()) {
                $this->redirect(302, 'Init:default');
            }
        }

        $this->template->loggedIn = $this->getUser()->isLoggedIn();
        $this->template->url = $this->context->httpRequest->getUrl()->getAbsoluteUrl();
        $this->template->presenter = $this->getName();
    }
}
