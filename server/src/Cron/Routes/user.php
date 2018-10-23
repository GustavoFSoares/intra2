<?php
use Slim\Http\Request;
use Slim\Http\Response;
use Doctrine\DBAL\Driver\PDOException;

$app->group('/cron/user', function () {

    $this->get('/update', "Cron\Controller\UserController:update", function (Request $req, Response $res, array $args) { });
    $this->get('/adp', "Cron\Controller\UserController:integrateWithAdpFileAction", function (Request $req, Response $res, array $args) { });
    $this->get('/adp/centro-resultado', "Cron\Controller\UserController:getCentrosDeResultadoAction", function (Request $req, Response $res, array $args) { });
});