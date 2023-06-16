<?php

class Database
{

    protected PDO $pdo;

    public function __construct(string $db_name, string $username, string $password, string $host = 'localhost')
    {

        /* Connection to the Database */

        $this->pdo = new PDO('mysql:dbname=' . $db_name . ';host=' . $host . ';charset=utf8', $username, $password);

        /* Set PDO to display errors and make the results to be displayed as Objects */

        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
    }

    /* Method used to take care of the execution of PDOStatements */

    public function query(string $query, bool|array $params = false): false|PDOStatement
    {
        if ($params) {
            $request = $this->pdo->prepare($query);
            $request->execute($params);
        } else {
            $request = $this->pdo->query($query);
        }

        return $request;
    }

    /* Method used to return the last inserted ID in the Database */

    public function lastInsertId(): false|string
    {
        return $this->pdo->lastInsertId();
    }

}