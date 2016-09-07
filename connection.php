<?php

class Connection {
    const HOST = '127.0.0.1';
    const DB = 'test';
    const USER = 'root';
    const PASSW = 'ubuzep85';

    protected $pdo;
    public static $instance;

    protected function __construct($host, $db, $user, $passw)
    {
        $this->pdo = new PDO(
            "mysql:host=$host;dbname=$db",
            $user,
            $passw
        );
    }

    /**
     * @return PDO
     */
    public function getPdo()
    {
        return $this->pdo;
    }

    /**
     * @return PDO
     */
    public static function getConnection()
    {
        if (!self::$instance) {
            self::$instance = new self(self::HOST, self::DB, self::USER, self::PASSW);
        }

        return self::$instance->getPdo();
    }
}
