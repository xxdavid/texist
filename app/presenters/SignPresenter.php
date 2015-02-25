<?php

namespace App\Presenters;

use Dropbox\ArrayEntryStore;
use Nette,
	App\Model;


/**
 * Sign in/out presenters.
 */
class SignPresenter extends BasePresenter
{
	public function injectStorage(Model\IStorage $storage)
	{
		$this->storage = $storage;
	}

    public function actionIn($redirect = '/', $step = 1)
    {
        if ($this->storage->areAppKeysSet()) {
            switch ($step) {
                case 1:
                    $this->context->httpResponse->redirect($this->getDropboxWebAuth()->start($redirect), 302);
                    break;
                case 2:
                    list($accessToken, $userId, $urlState) = $this->getDropboxWebAuth()->finish($_GET);

                    if (!$this->storage->isInitialized()) {
                        $this->storage->set('accessToken', $accessToken);
                        $this->storage->set('dropboxUID', $userId);
                    } elseif ($userId != $this->storage->get('dropboxUID')) {
                        //TODO
                        die();
                    }

                    $this->getUser()->login(new Nette\Security\Identity(0));
                    $this->redirectUrl($urlState, 302);
                    break;
            }
        } else {
            $this->redirect(302, 'Init');
        }
    }


	public function actionOut($redirect)
	{
		$this->getUser()->logout();
		$this->flashMessage('You have been signed out.');
        if ($redirect) {
            $this->redirectUrl($redirect, 302);
        }
		$this->redirect('homepage:default');
	}

    private function getDropboxWebAuth()
    {
        $appInfo = \Dropbox\AppInfo::loadFromJson([
            "key" => $this->storage->get('appKey'),
            'secret' => $this->storage->get('appSecret')
        ]);
        $this->absoluteUrls = true;
        $this->session->start();
        $webAuth = new \Dropbox\WebAuth($appInfo, 'Texist', $this->link('Sign:in', ['step' => 2]), new ArrayEntryStore($_SESSION, 'dropbox-auth-csrf-token'));
        $this->absoluteUrls = false;
        return $webAuth;
    }

}
