<?php

namespace App\Model;

use Nette,
    Nette\Utils\Strings,
    Nette\Security\Passwords;

class DropboxWrapper extends Nette\Object
{
    /** @var DatabaseWrapper */
    private $database;

    /** @var  \TijsVerkoyen\Dropbox\Dropbox */
    private $dropbox;

    /** @var  string */
    private $tempDir;

    public function __construct($appKey, $appSecret, $tempDir, DatabaseWrapper $databaseWrapper)
    {
        $this->database = $databaseWrapper;
        $this->dropbox = new \TijsVerkoyen\Dropbox\Dropbox($appKey, $appSecret);
        $this->tempDir = $tempDir;
    }

    public function setAccessTokens()
    {
        $tokens = $this->database->getTokens();
        $this->dropbox->setOAuthToken($tokens[0]);
        $this->dropbox->setOAuthTokenSecret($tokens[1]);
    }

    public function redirectToAuthorizationPage($callbackUrl){
        $_SESSION['dropboxRequestTokens'] = $this->dropbox->oAuthRequestToken();
        $this->dropbox->oAuthAuthorize($_SESSION['dropboxRequestTokens']['oauth_token'], $callbackUrl);
    }

    public function hasAuthorizationStarted(){
        return isset($_SESSION['dropboxRequestTokens']);
    }

    public function finishAuthorization()
    {
        $this->dropbox->setOAuthTokenSecret($_SESSION['dropboxRequestTokens']['oauth_token_secret']);

        $accessTokens = $this->dropbox->oAuthAccessToken($_SESSION['dropboxRequestTokens']['oauth_token']);
        $_SESSION['dropboxAccessTokens'] = $accessTokens;
        unset($_SESSION['dropboxRequestTokens']);
        $this->dropbox->setOAuthToken($accessTokens['oauth_token']);
        $this->dropbox->setOAuthTokenSecret($accessTokens['oauth_token_secret']);
        $uidInDb = $this->database->getUid();
        if ($uidInDb){
            if ($accessTokens['uid'] != $uidInDb){
                echo "No no no, you aren't the right man.";
                return false;
            } else {
                return true;
            }
        } else {
            $this->database->insertUser($accessTokens['uid'], $accessTokens['oauth_token'], $accessTokens['oauth_token_secret'], $this->dropbox->accountInfo()['display_name']);
            return true;
        }
    }

    public function resetAuthorizationProgress()
    {
        unset($_SESSION['dropboxRequestTokens']);
    }

    public function getFile($path)
    {
        return base64_decode($this->dropbox->filesGet($path, null, true)['data']);
    }

    public function putFile($path, $string)
    {
        preg_match('#(.+/)*(.+)#', $path, $matches);
        array_shift($matches);
        list($dir, $filename) = $matches;
        $tempFileName = $this->tempDir . $filename;
        $handle = fopen($tempFileName, "w");
        fwrite($handle, $string);
        fclose($handle);
        $this->dropbox->filesPost($dir, $tempFileName, true, null, null, true);
        unlink($tempFileName);
    }

    /**
     * @param string $path
     * @param array|null $extensionFilter
     * @param bool $includeExtension
     * @return array Array of filenames
     * @todo Directories
     */
    public function getFilesList($path, array $extensionFilter = null, $includeExtension = true)
    {
        $metadata = $this->dropbox->metadata($path, 1000, null, true, false, null, null, true);
        $list = [];
        foreach($metadata['contents'] as $file){
            $pathParts = pathinfo($file['path']);
            if (!$extensionFilter OR in_array($pathParts['extension'], $extensionFilter)) {
                if ($includeExtension){
                    $list[] = trim($file['path'], '/');
                } else {
                    $list[] = trim($pathParts['dirname'] . '/' . $pathParts['filename'], '/\\');
                }
            }
        }
        return $list;
    }

}
