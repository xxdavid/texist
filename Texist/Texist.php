<?php

class Texist
{
    /** @var  array */
    private $database;

    /** @var  array */
    private $dropboxAppKeys;

    /** @var  string */
    private $tempDir;

    /** @var  string|null */
    private $logDir = null;

    /** @var  string|null */
    private $email = null;

    /** @var  array|string|bool|null */
    private $debugMode = null;

    /**
     * Sets DB connection
     * @param string $host
     * @param string $dbname
     * @param string $username
     * @param string $password
     */
    public function setDatabase($host, $dbname, $username, $password)
    {
        $this->database = [
            'host' => $host,
            'dbname' => $dbname,
            'user' => $username,
            'password' => $password,
        ];
    }

    /**
     * Sets Dropbox Application Keys
     * You can find them in App Console
     * @link https://www.dropbox.com/developers/apps
     * @param string $key
     * @param string $keySecret
     */
    public function setDropboxAppKeys($appKey, $appSecret)
    {
        $this->dropboxAppKeys = [
            'appKey' => $appKey,
            'appSecret' => $appSecret,
        ];

    }

    /**
     * Sets temporary directory
     * @param string $path
     */
    public function setTempDirectory($path)
    {
        $this->tempDir = $path;
    }

    /**
     * Sets log directory
     * @param string $path
     */
    public function setLogDirectory($path)
    {
        $this->logDir = $path;
    }

    /**
     * Sets email for error notifications
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * Sets Debug mode
     * Possible values:
     * - [...] (visitor from this IPs will see Tracy -- Debug bar, verbose exceptions etc.)
     * - string with IPs (IP) separated by comma (visitor from this IP will see Tracy)
     * - true (force debug mode)
     * - false (force production mode)
     * - null (detect automatically)
     * @link http://doc.nette.org/en/2.2/configuring#toc-development-mode
     * @param array|string|bool|null $value
     */
    public function setDebugMode($value)
    {
        $this->debugMode = $value;
    }


    public function run()
    {
        if (!isset($this->database)){
            throw new Exception('You have to call setDatabase() before run().');
        }
        if (!isset($this->dropboxAppKeys)){
            throw new Exception('You have to call setDropboxAppKeys() before run().');
        }
        if (!isset($this->tempDir)){
            throw new Exception('You have to call setTempDirectory() before run().');
        }

        $appDir = __DIR__ . '/../app';

        $configurator = new Nette\Configurator;

        if ($this->debugMode !== null) {
            $configurator->setDebugMode($this->debugMode);
        }

        $configurator->enableDebugger($this->logDir, $this->email);

        $configurator->setTempDirectory($this->tempDir);

        $configurator->createRobotLoader()
            ->addDirectory($appDir)
            ->register();

        $configurator->addConfig($appDir . '/config/config.neon');

        $configurator->addParameters([
            'database' => $this->database,
            'dropbox' => $this->dropboxAppKeys,
        ]);

        $container = $configurator->createContainer();
        $container->getService('application')->run();
    }
}
