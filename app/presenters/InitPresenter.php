<?php

namespace App\Presenters;

use App\Model\IStorage;
use Nette\Application\UI\Form;

class InitPresenter extends BasePresenter
{
    /**
     * @var IStorage
     * @inject
     */
    public $storage;

    protected function createComponentInitForm()
    {
        $form = new Form();
        $form->addText('appKey', 'Dropbox App Key')
            ->setRequired();
        $form->addText('appSecret', 'Dropbox App Secret')
            ->setRequired();
        $form->addSubmit('submit');
        $form->onSuccess[] = array($this, 'initFormSucceeded');
        return $form;
    }

    public function initFormSucceeded(Form $form, $values)
    {
        if (!$this->storage->isInitialized()) {
            $this->storage->set('appKey', $values->appKey);
            $this->storage->set('appSecret', $values->appSecret);
        }
        $this->redirect(302, 'Sign:in');
    }

    public function renderDefault()
    {
        $this->template->initialized = $this->storage->isInitialized();
    }
}
