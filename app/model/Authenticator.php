<?php

namespace App\Model;

use Nette,
    App\Model,
    Nette\Security as NS;

class Authenticator extends Nette\Object implements NS\IAuthenticator
{

    /** @var Model\DropboxWrapper @inject */
    private $dropbox;

    public function __construct(Model\DropboxWrapper $dropboxWrapper)
    {
        $this->dropbox =$dropboxWrapper;
    }

    public function authenticate(array $params) //There should be credentials, I know
    {
        list($httpRequest, $httpResponse) = $params;
        if (!$this->dropbox->hasAuthorizationStarted()) {
            $this->dropbox->redirectToAuthorizationPage($httpRequest->getUrl());
        } else {
            try {
                if ($this->dropbox->finishAuthorization()) {
                    return new NS\Identity(1);
                }
            } catch (\TijsVerkoyen\Dropbox\Exception $e) {
                $this->dropbox->resetAuthorizationProgress();
                $httpResponse->redirect($httpRequest->getUrl());
                die();
            }
        }
        return false;
    }
}