<?php

namespace App\Presenters;

use Nette,
	App\Model;


/**
 * Sign in/out presenters.
 */
class SignPresenter extends BasePresenter
{

    public function actionIn($redirect)
    {
        $authenticator = $this->context->authenticator;
        $user = $this->getUser();
        $user->setAuthenticator($authenticator);
        $this->getUser()->login(
           $this->context->httpRequest,
           $this->context->httpResponse
        );
        if ($redirect) {
            $this->redirectUrl($redirect, 301);
        }
        $this->redirect('homepage:default');
    }


	public function actionOut($redirect)
	{
		$this->getUser()->logout();
		$this->flashMessage('You have been signed out.');
        if ($redirect) {
            $this->redirectUrl($redirect, 301);
        }
		$this->redirect('homepage:default');
	}

}
