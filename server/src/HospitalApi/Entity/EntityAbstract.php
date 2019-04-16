<?php
namespace HospitalApi\Entity;

use HospitalApi\BasicApplicationAbstract;
use Datetime;
/**
 * @abstract EntityAbstract
 * <b>EntityAbstract</b>
 * Classe Abstrata das Entidades. Responsável
 * pela abstração e implementação de métodos 
 * como <i>toArray()</i> e <i>toString()</i>
 */
abstract class EntityAbstract extends BasicApplicationAbstract
{
    private $_alowed = true;
    private $_repositories = [];

    public function __construct() { }

    /**
     * @method getClassName()
     * Retorna uma string com nome da classe 
     * com nome da classe atual<i>($this)</i>
     * @return String NomeClase
     */
    public function getClassName() {
        return get_class($this);
    }
    
    public function getClassShortName() {
        return (new \ReflectionClass($this))->getShortName();
    }

    /**
     * @method getClassVars()
     * Retorna um array contendo o nome dos 
     * atributos da classe atual<i>($this)</i>
     * @return Array vars
     */
    public function getClassVars()
    {
        return get_class_vars($this->getClassName());
    }
   
    public function getPublicVars()
    {
        $vars = [];
        foreach ($this->getClassVars() as $key => $value) {
            if($key[0] != '_') {
                $vars[$key] = $value;
            }
        }
        return $vars;
    }

    /**
     * @method toString()
     * Retorna uma String no formato de chave:valor
     * contendo os o nome do atributo e valor associado a ele
     * @return String atributos
     */
    public function toString() {
        $obj = [];
        foreach ($this->getClassVars() as $var => $value) {
            $obj.="[$var:".$this->$var."]";
        }
        return $obj;
    }

    /**
     * @method toArray()
     * Restorna um Array no formato de chave => valor
     * contendo o nome do atributo e valor associado a ele
     * @return Array atributos
     */
    public function toArray() {
        $obj = [];
        foreach ($this->getPublicVars() as $var => $value) {
            if($var != 'lazyPropertiesDefaults') 
                $obj[$var] = $this->$var || $this->$var === false ? $this->$var : "";
        }
        return $obj;
    }

    public function convertToDatetime($date) {
        if (!$date instanceof Datetime) {
            $search = [' ', '-', '/'];
            $reclace = ['', ' ', '-'];
            $date = str_replace($search, $reclace, $date);
            $date = new DateTime($date);
        }
        return $date;
    }

    public function buildList($entityList, $values) {
		if( empty($values) ) {
			$dataReturn = $entityList->clear();
		} else if($entityList->isEmpty()) {
			$dataReturn = new \Doctrine\Common\Collections\ArrayCollection($values);
		} else {
            $collection = new \Doctrine\Common\Collections\ArrayCollection($entityList->toArray());
            if(!$values instanceof \Doctrine\Common\Collections\ArrayCollection) {
                $values = new \Doctrine\Common\Collections\ArrayCollection(Array ($values));
            }
            $exist = [];
            foreach ($values as $row) {
                foreach ($collection as $key => $collectionRow) {
                    if($collectionRow->getId() == $row->getId()) {
                        $collection->remove($key);
                        $exist[] = $row->getId();
                    }
                }
            }

            // Remove not in list
            $entityList->map(function(&$entry) use ($exist, $entityList, $row)  {
                if( !in_array($entry->getId(), $exist) ) {
                    $entityList->removeElement($entry);
                } else {
                    $entry = $row;
                }
            });
            
            // Add for in list
            $values->map(function($entry) use ($exist, $entityList)  {
                if( !in_array($entry->getId(), $exist) ) {
                    $entityList->add($entry);
                }
            });
            
            return $entityList;
        }

		return $dataReturn;
	}

    /**
     * @method _formatDate()
     * Recebe data no formato d/m/Y H:m:s e
     * retorna no Formato MySql Y-m-d H:i:s
     * @return Date
     */
    public function _formatDate($date){
        if(!($date instanceof \DateTime) && $date != null) {
            if(is_array($date) && isset($date['date']) ){
                $date = new \DateTime($date['date']);
            } else if(substr($date, -1) == "Z") {
                $date = explode('T', $date);
                $date[1] = substr($date[1], 0, -5);

                $date = date("Y-m-d H:i:s", strtotime("{$date[0]} {$date[1]}"));
                $date = DateTime::createFromFormat("Y-m-d H:i:s", $date);
            } else {
                $search = [' ', '-', '/'];
                $reclace = ['', ' ', '-'];
                $date = str_replace($search, $reclace, $date);
                
                $date = date("Y-m-d H:i:s", strtotime($date));
                $date = DateTime::createFromFormat("Y-m-d H:i:s", $date);
            }
        }
        return $date;
    }

    public function setLikeError() {
        $this->_alowed = false;
    }
    public function set_Alowed($alowed) {
        $this->_alowed = $alowed;
    }
    public function canPersist() {
        return $this->_alowed;
    }

    public function getRepositoryOf($repositoryName, $entity) {
        if($entity instanceof EntityAbstract) {
            return $entity;
        } else if( is_array($entity) && array_key_exists('id', $entity) ) {
            if( array_key_exists($repositoryName, $this->_repositories)) {
                return $this->_repositories[$repositoryName]->find($entity['id']);
            } else {
                $this->_repositories[$repositoryName] = $this->getEntityManager()->getRepository("HospitalApi\Entity\\$repositoryName");
                
                return $this->_repositories[$repositoryName]->find($entity['id']);
            }
        } else {
            return null;
        }
    }

    public function construct($data) {
        foreach ($data as $key => $value) {
            $method = "set$key";
            $this->$method($value);
        }
        return $this;
    }

    public function isLoaded() {
        return $this->getId() ? true : false;
    }

}
