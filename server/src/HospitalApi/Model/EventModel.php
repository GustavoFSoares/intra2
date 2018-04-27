<?php
namespace HospitalApi\Model;

use HospitalApi\Entity\Event;

class EventModel extends ModelAbstract {

    public $entity;
    
    public function __construct(){

        $this->entity = new Event();
        parent::__construct();
    }

}