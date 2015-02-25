<?php

namespace App\Model;

use Dropbox\WriteMode;

class DropboxWrapper extends \Nette\Object
{
    /** @var IStorage */
    private $storage;

    /** @var  \Dropbox\Client */
    private $dropbox;

    public function __construct(IStorage $storage)
    {
        $this->storage = $storage;
        $this->dropbox = new \Dropbox\Client($this->storage->get('accessToken'), 'Texist');
    }

    /**
     * @param string $path
     * @return string
     */
    public function getFile($path)
    {
        $stream = fopen('php://temp', 'w+');
        $this->dropbox->getFile('/' . $path, $stream);
        rewind($stream);
        $result = stream_get_contents($stream);
        fclose($stream);
        return $result;
    }

    /**
     * @param string $path
     * @param string $string
     */
    public function putFile($path, $string)
    {
        $stream = fopen('php://temp', 'w+');
        fputs($stream, $string);
        rewind($stream);
        $this->dropbox->uploadFile('/' . $path, WriteMode::force(), $stream);
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
        $metadata = $this->dropbox->getMetadataWithChildren('/' . $path);
        $list = [];
        foreach ($metadata['contents'] as $file) {
            $pathParts = pathinfo($file['path']);
            if (!$extensionFilter or in_array($pathParts['extension'], $extensionFilter)) {
                if ($includeExtension) {
                    $list[] = trim($file['path'], '/');
                } else {
                    $list[] = trim($pathParts['dirname'] . '/' . $pathParts['filename'], '/\\');
                }
            }
        }
        return $list;
    }
}
