<?php

namespace HospitalApi\Model;

use HospitalApi\Entity\Ombudsman;
use Doctrine\ORM\QueryBuilder;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
/**
 * <b>OmbudsmanModel</b>
 */
class OmbudsmanModel extends SoftdeleteModel
{

    protected $_ORDERS = [ 'registerTime' => 'DESC' ];

    public $entity;

    public function __construct() {
        $this->entity = new Ombudsman;
        parent::__construct();
    }

    public function mount($values) {
        $values = (object)$values;
        
        $values->reported = true;
        $values->status = 'registered';

        if(isset( $values->ombudsmanUser) ) {

            if( isset($values->ombudsmanUser['id']) && $values->ombudsmanUser['id'] ){
                $ombudsmanUser = $this->em->getRepository('HospitalApi\Entity\OmbudsmanUser')->find($values->ombudsmanUser['id']);
            } else {
                if( !$ombudsmanUser = $this->em->getRepository('HospitalApi\Entity\OmbudsmanUser')->findOneByPatientName($values->ombudsmanUser['patientName']) ) {
                    $ombudsmanUser = new \HospitalApi\Entity\OmbudsmanUser();
                }
            }
            $ombudsmanUser->setOmbudsmanUser($values->ombudsmanUser);
            $values->ombudsmanUser = $ombudsmanUser;

        }

        switch ($values->origin['id']) {
            case 'INT':
                $values->group = null;
                break;
            
            case 'AMB':
                $values->bed = null;
                break;            
        }

        return $values;
    }

     public function findBy($filter) {
        $select = $this->em->createQueryBuilder();
        $select->select([
                'o as row',
                'omn.count',
            ])
            ->from($this->getEntityPath(), 'o')
            ->leftJoin('HospitalApi\Entity\OmbudsmanMessagesNotification', 'omn', 'WITH', 'omn.ombudsman = o AND omn.user = :user')
            ->setParameter('user', $this->getSession() );

        $select = $this->showForJustWhoCanSee($select);
        
        if( $this->hadOrders() ) {
            foreach ($this->_ORDERS as $key => $value) {
                $select->addOrderBy("o.$key", $value);
            }
            foreach ($filter as $key => $value) {
                $select
                    ->andWhere("o.$key = :$key")
                    ->setParameter($key, $value);
            }
        }

        return $select->getQuery()->getResult();
    }

    public function findById($id) {
        $select = $this->em->createQueryBuilder();
        $select->select('o')->from($this->getEntityPath(), 'o');

        $select = $this->showForJustWhoCanSee($select)
            ->andWhere('o.id = :id')
            ->setParameter('id', $id);

        return $select->getQuery()->getOneOrNullResult();
    }

    public function showForJustWhoCanSee(QueryBuilder $select ) {
        switch ($this->getContainer()['session']->gotPermission('ombudsman')) {
            case 'ALL_DATA':
                break;
            
            case 'HU_DATA':
                $select
                    ->leftJoin('HospitalApi\Entity\Group', 'g', 'WITH', 'g = o.group')
                    ->andWhere("g.enterprise = 'HU'")
                    ->OrWhere($select->expr()->isNull('o.group'));
                break;
            
            case 'HPSC_DATA':
                $select
                    ->innerJoin('o.group', 'g', 'WITH', "g.enterprise = 'HPSC'");
                break;
            
            default: 
                $select
                    ->leftJoin('o.managerList', 'oml', 'WITH', 'oml = :user')
                    ->leftJoin('o.transmissionList', 'otl', 'WITH', 'otl = :user')
                    ->andWhere('oml = :user')
                    ->orWhere('otl = :user')
                    ->setParameter('user', $this->getSession()->getId());
                break;
        }
       
        return $select;
    }

    public function getLastKeyOfOrigin($origin) {
        $data = $this->getRepository()->findOneBy(['origin'=>$origin->getId()]);
        $select = $this->em->createQueryBuilder();
        $select->select('o')
            ->from($this->entityPath, 'o')
            ->where("o.origin = :origin")
            ->setParameter('origin', $origin)
            ->orderBy('o.id', 'DESC')
            ->setMaxResults('1');
        return $select->getQuery()->getOneOrNullResult();
    }

    public function getOmbudsmansWaiting($params = []) {
        $select = $this->em->createQueryBuilder();
        $select->select([
            'o.id AS id',
            'ori.id AS origin',
        ]);
        $select
            ->from($this->entityPath, 'o')
            ->innerJoin('o.origin', 'ori', 'WITH', 'o.origin = ori')
            ->where('o.reported = 0');
        foreach ($params as $key => $value) {
            $select->andWhere("o.$key = :$key")
                ->setParameter($key, $value);
        }
        $data = $select->getQuery()->getResult();

        return $data;
    }

    public function gotPermission($id) {
        $permission = $this->getContainer()['session']->gotPermission('ombudsman');
        
        if($permission == 'USER') {
            $permission = 
                $this->managerInOmbudsman($this->getContainer()['session']->get(), $id);
        }
        
        return $permission;
    }


    public function setResponse($values) {
        $this->entity = $this->getRepository()->find($values['id']);

        foreach ($values as $key => $value) {
			$method = "set$key";
			$this->entity
				->$method($value);
		}
        return $this->doUpdate($this->entity);
    }

    public function addManager($id, $user, $type) {
        $this->entity = $this->getRepository()->find($id);
        
        if($this->managerInOmbudsman($user, $id) ) {
            return ['status' => false, 'code' => 409,];
        } else {
            
            switch ($type) {
                case 'manager':
                    $this->entity->addManagerOnList($user);
                    break;
                
                case 'companion':
                    $this->entity->addManagerOnTransmissionList($user);
                    break;
            }

        }
        
        $this->entity->setStatus('waiting-manager');

        try {
            $this->doUpdate($this->entity);
            return ['status' => true, 'code' => 200 ];
        } catch(UniqueConstraintViolationException $e) {
            return ['status' => false, 'code' => 409,];
        }
    }

    public function removeManager($id, $user, $type) {
        $this->entity = $this->getRepository()->find($id);
        switch ($type) {
            case 'manager':
                $this->entity->removeManagerOnList($user);
                break;
            
            case 'companion':
                $this->entity->removeManagerOnTransmissionList($user);
                break;
        }
        $this->entity->setStatus('waiting-manager');

        try {
            $this->doUpdate($this->entity);
            return ['status' => true];
        } catch(UniqueConstraintViolationException $e) {
            return ['status' => false, 'code' => 409,];
        }
    }

    public function managerInOmbudsman($user, $ombudsmanId) {
        if(!$user instanceof \HospitalApi\Entity\EntityAbstract) {
            if( array_key_exists('id', $user) ){
                $user = $this->em->getRepository('HospitalApi\Entity\User')->find($user['id']);
            }
        }

        if(!$this->entity->getId()) {
            $this->entity = $this->getRepository()->find($ombudsmanId);
        }

        if($this->entity) {
            $manager = $this->entity->getManagerList()->exists( function($key, $entry) use ($user) {
                return ($entry->getId() == $user->getId() );
            });

            if($manager) {
                $res = 'MANAGER';
            } else {
                $companion = $this->entity->getTransmissionList()->exists( function($key, $entry) use ($user) {
                    return ($entry->getId() == $user->getId());
                });
                if($companion) {
                    $res = 'COMPANION';
                } else {
                    $res = false;
                }
            }
        } else {
            $res = false;
        }

        return $res;
    }

    public function getManagersList($ombudsmanId, $userId) {
        $select = $this->em->createQueryBuilder()
            ->select('u')
            ->from('HospitalApi\Entity\User', 'u')
            ->innerJoin('HospitalApi\Entity\Ombudsman', 'o', 'WITH', 'o.id = :ombudsman')
            ->innerJoin('ir.transmissionList', 'irtl', 'WITH', 'irtl = u')
            ->where('u.admin = 1')
            ->andWhere('u.id != :user')
            ->andWhere('u.c_removed = 0')
            ->andWhere(!$select->expr()->isNull('u.email'))
            ->setParameter('user', $userId)
            ->setParameter('ombudsman', $ombudsmanId);
        return $select->getQuery()->getResult();
    }
    
    public function closeOmbudsman($values) {
        if( array_key_exists('id', $values) ){
            $this->entity = $this->getRepository()->find($values['id']);
            $this->entity
                ->setClosed(true)
                ->setStatus('closed')
                ->setResponseToUser($values['responseToUser']);
            $this->doUpdate($this->entity);

            return [
                'status' => $this->entity->getStatus(),
                'closed' => $this->entity->getClosed(),
            ];
        } else {
            return false;
        }
    }

    public function finishOmbudsman($values) {
        if( array_key_exists('id', $values) ){
            $this->entity = $this->getRepository()->find($values['id']);
            $this->entity
                ->setStatus('finished')
                ->setResponseToUser($values['responseToUser'])
                ->setOmbudsmanToResponse($values['ombudsmanToResponse']);
            $this->doUpdate($this->entity);

            return true;
        } else {
            return false;
        }
    }

    public function getTransmissionList($ombudsmanId, $userId) {
        $list = [];
        
        for ($i=0; $i <= 1; $i++) { 
            $select = $this->em->createQueryBuilder()
                ->select('u')
                ->from('HospitalApi\Entity\User', 'u')
                ->innerJoin('HospitalApi\Entity\Ombudsman', 'o', 'WITH', 'o.id = :ombudsman');
                
            if($i === 0) {
                $select->innerJoin('o.managerList', 'oml', 'WITH', 'oml = u');
            } elseif($i === 1) {
                $select->innerJoin('o.transmissionList', 'otl', 'WITH', 'otl = u');
            }
            
            $select
                ->where('u.admin = 1')
                ->andWhere('o.id = :ombudsman')
                ->andWhere('u.id != :user')
                ->andWhere('u.c_removed = 0')
                ->andWhere('u.email != :emailNull')
                ->setParameter('emailNull', "")
                ->setParameter('user', $userId)
                ->setParameter('ombudsman', $ombudsmanId);
            $list = array_merge($list, $select->getQuery()->getResult());
        }

        return $list;
    }

    public function updateTransmissionList($ombudsmanId, $user, $type) {
        if(!$this->entity->getId()) {
            $this->entity = $this->getRepository()->find($ombudsmanId);
        }

        if($user instanceOf \HospitalApi\Entity\User) {
            $user = $this->em->getRepository('HospitalApi\Entity\User')->find($user->getId());
        } else {
            $user = $this->em->getRepository('HospitalApi\Entity\User')->find($user->id);
        }

        if($type == 'add') {
            if(!$this->userInList($this->entity->getManagerList(), $user) ) {
                $this->entity->addManagerOnList($user);
                $this->doUpdate($this->entity);
            }
        } else if($type == 'remove') {
            if($this->userInList($this->entity->getManagerList(), $user) ) {
                $this->entity->removeManagerOnList($user);
                $this->doUpdate($this->entity);
            }
        }


        return true;
    }
}
