<?php

namespace App\Presenters;

use Nette,
    App\Model;


/**
 * Sign in/out presenters.
 */
class DocumentPresenter extends BasePresenter
{
    /** @var  Model\TexyWrapper */
    private $texy;

    public function injectTexyWrapper(Model\TexyWrapper $texyWrapper)
    {
        $this->texy = $texyWrapper;
    }

    public function renderView($filename)
    {
        $this->template->processedText = $this->texy->process($this->dropbox->getFile($filename . '.texy'));
        $this->template->filename = $filename;
    }

    public function renderEdit($filename)
    {
        if(!$this->getUser()->isLoggedIn()){
            $this->redirect(301, 'Sign:in', $this->context->httpRequest->getUrl()->getAbsoluteUrl());
        }
        $this->template->originalText = $this->dropbox->getFile($filename . '.texy');
        $this->template->processedText = $this->texy->process($this->template->originalText);
        $this->template->filename = $filename;
    }

    public function renderProcess($filename, $text)
    {
        $httpResponse = $this->context->httpResponse;
        $httpResponse->setContentType('text/html', 'UTF-8');
        $this->dropbox->putFile($filename . '.texy', $text);
        $this->sendResponse(new Nette\Application\Responses\TextResponse($this->texy->process($text)));
    }
}
