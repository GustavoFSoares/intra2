<?php
use Slim\Http\Request;
use Slim\Http\Response;
use Doctrine\DBAL\Driver\PDOException;

$app->group('/training', function () {

    $this->get('/type/[{id}]', "HospitalApi\Controller\TrainingTypeController:get", function (Request $req, Response $res, array $args) { });
    $this->get('/[{id}]', "HospitalApi\Controller\TrainingController:get", function (Request $req, Response $res, array $args) { });
    $this->get('/unrealized/', "HospitalApi\Controller\TrainingController:getUnrealized", function (Request $req, Response $res, array $args) { });
    $this->get('/get-trainings/', "HospitalApi\Controller\TrainingController:getTrainingsAction", function (Request $req, Response $res, array $args) { });
    
    $this->post('/', "HospitalApi\Controller\TrainingController:insert", function (Request $req, Response $res, array $args) { });
    
    $this->put('/{id}', "HospitalApi\Controller\TrainingController:update", function (Request $req, Response $res, array $args) { });
    $this->put('/done/{id}', "HospitalApi\Controller\TrainingController:isDone", function (Request $req, Response $res, array $args) { });
    
    $this->delete('/{id}', "HospitalApi\Controller\TrainingController:delete", function (Request $req, Response $res, array $args) { });
});