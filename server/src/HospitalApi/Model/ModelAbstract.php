<?php

namespace HospitalApi\Model;

use HospitalApi\BasicApplicationAbstract;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;
use DateTime;

/**
 * <b>ModelAbstract</b>
 * Classe responsável por fazer conexão com BD
 * e inserir métodos de CRUD com banco de dados às
 * outras Models
 */
abstract class ModelAbstract extends BasicApplicationAbstract
{

	public $em;
	public $now;
	protected $entityPath;

	public function __construct() {
		$this->entityPath = get_class($this->entity);
		$this->entityPath ? 
			$this->em = $this->getEntityManager() : "";
		$this->now = new DateTime();
	}

	/**
	 * @method createEntityManager()
	 * Configura conexões com Banco de Dados e indica caminho
	 * das pastas das Entidades, onde realizará o mapeamento 
	 * para o próprio Banco. 
	 * Retorna um EntityManager com os métodos do Doctrine
	 * @return EntityManager
	 */
	public static function createEntityManager() {
		
		$path = ['HospitalApi\Entity'];
		$devMode = true;

		$config = Setup::createAnnotationMetadataConfiguration($path, $devMode);
		
		$connectionOptions = [
			'dbname' => DATABASE_NAME,
			'user' => DB_APPLICATION_USER,
			'password' => DB_APPLICATION_PASSWORD,
			'host' => DB_APPLICATION_HOST,
			'driver' => DB_APPLICATION_DRIVER
		];

		return EntityManager::create($connectionOptions, $config);
	}

	public function restartEntityPath(Type $var = null) {
		$em = self::createEntityManager();
		
		$this->getContainer()['em'] = $em;
		$this->em = $em;
	}

	/**
	 * @method doInsert()
	 * Recebe um Objeto de Entity, contendo os valores que irão
	 * ser inseridos
	 * @param Entity $obj
	 * @return void
	 */
	public function doInsert($obj) {
		$this->em->persist($obj);
		$this->em->flush();
	}

	/**
	 * @method doUpdate()
	 * Recebe um Objeto de Entity, contendo os valores alterados
	 * e o Id do objeto original, quer irá ser atualizado
	 * @param Entity $obj
	 * @return void
	 */
	public function doUpdate($obj) {
		$this->em->merge($obj);
		$this->em->flush();
	}

	/**
	 * @method doDelete()
	 * Recebe um Objeto de Entity, com o objeto que irá ser excluido
	 * @param Entity $obj
	 * @return void
	 */
	public function doDelete($obj) {
		$this->em->remove($obj);
		$this->em->flush();
	}

	/**
	 * @method findById()
	 * Recebe um <i>Id</i> e realiza uma busca no banco e retorna
	 * um Array contendo o correspondente.
	 * @param Integer $id
	 * @return Array
	 */
	public function findById($id) {
		return $this->em->find($this->entityPath, $id);
	}

	/**
	 * @method findBy()
	 * Retorna uma Array com os resultados da tabela no Banco de Dados
	 * @return Array 
	 */
	public function findBy($filters = [], $orders = []) {
		$orders = $this->hadOrders() ? $this->_ORDERS : [];
		if($this->isInverseOrder()) {
			$orders['id'] = 'DESC';
		}

		$collection = $this->getRepository()->findBy($filters, $orders);
		
		return $collection;
	}

	/**
	 * @method getEntityPath()
	 * Retorna o Caminho do Arquivo da model extanciada
	 * @return EntityPath
	 */
	public function getEntityPath() {
		return $this->entityPath;
	}

	/**
	 * @method getRepository()
	 * Retorna uma Representação da Tabela(Model) extanciada
	 * @return Reposotory
	 */
	public function getRepository() {
		return $this->em->getRepository($this->entityPath);
	}

	public function getLogger() {
		$logger = new \Doctrine\DBAL\Logging\DebugStack();
		$logger->enabled;
		$this->em->getConfiguration()->setSQLLogger($logger);

		return $logger;
	}

	/**
	 * @method getNow()
	 * Retorna um Datetime com a Hora Atual
	 * @return Datetime now
	 */
	public function getNow() {
		return $this->now;
	}

	/**
	 * @method isToday()
	 * Verifica se a tada comparada ($matchDate) foi criada
	 * nas ultimas 24 horas. E retorna TRUE se for $diffDays = 0,
	 * Hoje e FALSE para $diffDays diferente de 0
	 * @param Datetime $matchDate
	 * @return boolean
	 */
	public function isToday($matchDate) {
		if($matchDate){
			$diff = $this->now->diff($matchDate);
			$diffDays = (integer)$diff->format("%R%a");
			
			return $diffDays == "0";
		}
	}

	public function isInverseOrder() {
        return ( isset($this->inverseOrder) && $this->inverseOrder ) ? true : false;
	}
	
	public function hadOrders() {
		return isset($this->_ORDERS);
	}
	
	public function hadFilters() {
		return isset($this->_FILTERS);
	}

	public function userInList($list, $user, $id = null) {
        $entity = null;
        if($this->entity->getId()) {
            $entity = $this->entity;
        } else if($id) {
            $entity = $this->getRepository()->find($id);
        }

        if($entity) {
            $res = $list->exists( function($key, $entry) use ($user) {
                return ($entry->getId() == $user->getId());
            });
        } else {
            $res = false;
        }
        return $res;
    }
}
