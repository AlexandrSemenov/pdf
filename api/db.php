<?php

require_once "../lib/php/FluentPDO/FluentPDO.php";

class DB
{
    public $fpdo;
    public function __construct()
    {
        $pdo = new PDO('mysql:dbname=pdf_db', 'root');
        $this->fpdo = new FluentPDO($pdo);
    }
}


