<?php

namespace App\Model;

use Nette,
    Nette\Utils\Strings,
    Nette\Security\Passwords;

class DatabaseWrapper extends Nette\Object
{
    const
        TABLE_NAME = 'user',
        COLUMN_UID = 'uid',
        COLUMN_TOKEN = 'token',
        COLUMN_TOKEN_SECRET = 'token_secret',
        COLUMN_NAME = 'name';


    /** @var Nette\Database\Context */
    private $database;

    public function __construct(Nette\Database\Context $database)
    {
        $this->database = $database;
    }

    public function getTokens()
    {
        $tokens = $this->database->table(self::TABLE_NAME)->select(self::COLUMN_TOKEN . ', ' . self::COLUMN_TOKEN_SECRET)->get(1);
        return [$tokens->token, $tokens->token_secret];
    }

    public function insertUser($uid, $token, $tokenSecret, $name)
    {
        $this->database->table(self::TABLE_NAME)->insert([
            'uid' => $uid,
            'token' => $token,
            'token_secret' => $tokenSecret,
            'name' => $name,
        ]);
    }

    public function getUid()
    {
        try{
            $row = $this->database->table(self::TABLE_NAME)->select(self::COLUMN_UID)->get(1);
            return isset($row->uid) ? $row->uid : false;
        } catch (\PDOException $e) {
            if ($e->getCode() == '42S02') {
                $this->createTable();
                return false;
            } else {
                throw $e;
            }
        }
    }

    private function createTable()
    {
        $this->database->query(
            "CREATE TABLE " . self::TABLE_NAME . " (
  `id` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `token` varchar(20) COLLATE utf8_bin NOT NULL,
  `token_secret` varchar(20) COLLATE utf8_bin NOT NULL,
  `uid` int(10) unsigned NOT NULL,
  `name` varchar(30) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;");
    }
}
