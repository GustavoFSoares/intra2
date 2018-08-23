<?php
namespace HospitalApi\Model;

use HospitalApi\Entity\Module;
use Doctrine\ORM\PersistentCollection;

class ModuleModel extends SoftdeleteModel
{

    public $entity;

    public function __construct() {
        $this->entity = new Module();
        parent::__construct();
    }

    public function findByGroup($group){
        $query = $this->em->createQueryBuilder();
        $query
            ->select('m')
            ->from('HospitalApi\Entity\Module', 'm')
            ->innerJoin('m.groups', 'g', 'WITH', 'g.groupId = :group OR m.default = 1')
            ->setParameter('group', $group)
            ->where('m.c_removed != 1')
            ->andWhere('m.parent IS NULL')
            ->orderBy('m.name', 'asc');
        $collection = $query->getQuery()->getResult();

        return $collection;
    }

    public function findAll() {
        $collection = $this->getRepository()->findBy(['parent' => null]);

        return $collection;
    }

    public function removeChield($chieldId) {
        $chield = $this->getRepository()->find($chieldId);
        $chield->setParent(null);

        return $chield;
    }

    public function mount($values) {
        $parent = $this->getRepository()->find($values['parent']['id']);
        
        if(array_key_exists('id', $values)) {
            $chield = $this->getRepository()->find($values['id']);
        } else {
            $chield = new Module();
        }

        if($chield->getChildren()) {
            $ChildrenOfChield = $chield->getChildren();
            foreach ($ChildrenOfChield->toArray() as $chieldOfChield) {
                $orphan = $chieldOfChield->setParent(null);
                $this->doUpdate($orphan);
            }
        }
        
        $chield
            ->setName($values['name'])
            ->setRouteName($values['routeName'])
            ->setDefault($values['default'])
            ->setIcon($values['icon'])
            ->setChildren(null)
            ->setParent($parent);
        $parent->addChildren($chield);

        return $parent;
    }

}