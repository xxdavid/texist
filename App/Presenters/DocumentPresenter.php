<?php

namespace App\Presenters;

use Nette;
use App\Model;


/**
 * Document presenter.
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
        $this->protect();
        $this->template->originalText = $this->dropbox->getFile($filename . '.texy');
        $this->template->processedText = $this->texy->process($this->template->originalText);
        $this->template->filename = $filename;
    }

    public function renderProcess($filename, $text)
    {
        $this->protect(true);
        $httpResponse = $this->context->httpResponse;
        $httpResponse->setContentType('text/html', 'UTF-8');
        $this->dropbox->putFile($filename . '.texy', $text);
        $this->sendResponse(new Nette\Application\Responses\TextResponse($this->texy->process($text)));
    }

    private function protect($ajax = false)
    {
        if (!$this->getUser()->isLoggedIn()) {
            if ($ajax) {
                $this->context->httpResponse->setCode(401);
                $this->sendResponse(new Nette\Application\Responses\JsonResponse(["message" => "Authentication Required"]));
            } else {
                $this->redirect(301, 'Sign:in', $this->context->httpRequest->getUrl()->getAbsoluteUrl());
            }
            die();
        }
    }
}
