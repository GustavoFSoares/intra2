<?php
namespace HospitalApi\Entity;
use \Doctrine\Common\Collections\ArrayCollection;

/**
 * @Entity
 * @\Doctrine\ORM\Mapping\HasLifecycleCallbacks
 * @Table(name="Ouvidoria")
 * <b>Ombudsman</b>
 */
class Ombudsman extends SoftdeleteAbstract
{

    /**
     * @var Integer @Id
     *     @Column(name="id", type="string", length=255)
     */
    protected $id;

    /**
     * @ManyToOne(targetEntity="OmbudsmanUser", cascade={"persist"})
     * @JoinColumn(name="paciente_id", referencedColumnName="id", nullable=true)
     */
    protected $ombudsmanUser;

    /**
     * @var String @Column(name="descricao_paciente", type="text", nullable=true)
     */
    protected $ombudsmanUserDescription;

    /**
     * @var String @Column(name="sugestao_paciente", type="text", nullable=true)
     */
    protected $ombudsmanUserSugestion;

    /**
     * @ManyToOne(targetEntity="User")
     * @JoinColumn(name="ouvidor_id", referencedColumnName="id", nullable=true)
     */
    protected $ombudsman;

    /**
     * @var String @Column(name="descricao_ouvidor", type="text", nullable=true)
     */
    protected $ombudsmanDescription;

    /**
     * @ManyToMany(targetEntity="User")
     * @JoinTable(name="Ouvidorias_Lista_Responsavel",
     *      joinColumns={@JoinColumn(name="ouvidoria_id", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE")}
     * )
     */
    protected $managerList;
    
    /**
     * @ManyToMany(targetEntity="User")
     * @JoinTable(name="Ouvidorias_Lista_Transmissao",
     *      joinColumns={@JoinColumn(name="ouvidoria_id", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE")}
     * )
     */
    protected $transmissionList;

    /**
     * @ManyToOne(targetEntity="OmbudsmanOrigin")
     * @JoinColumn(name="origem_id", referencedColumnName="id")
     */
    protected $origin;

    /**
     * @ManyToMany(targetEntity="OmbudsmanDemands")
     * @JoinTable(name="Ouvidoria_Ouvidoria_Demandas",
     *      joinColumns={@JoinColumn(name="ouvidoria_id", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@JoinColumn(name="demanda_id", referencedColumnName="id", onDelete="CASCADE")}
     *      )
     */
    protected $demands;
    
    /**
     * @var @Column(name="tipo", type="string", length=255, nullable=true)
     */
    protected $type;

    /**
     * @var String @Column(name="leito", type="string", nullable=true, length=255)
     */
    protected $bed;

    /**
     * @ManyToOne(targetEntity="Group")
     * @JoinColumn(name="grupo_id", referencedColumnName="id", nullable=true)
     */
    protected $group;
    
    /**
     * @ManyToOne(targetEntity="User")
     * @JoinColumn(name="ouvidor_resposta_id", referencedColumnName="id", nullable=true)
     */
    protected $ombudsmanToResponse;

    /**
     * @var String @Column(name="resposta_ao_paciente", type="text", nullable=true)
     */
    protected $responseToUser;

    /**
     * @var Datetime @Column(name="data_relato", type="datetime", nullable=true)
     */
    protected $registerTime;

    /**
     * @var Boolean @Column(name="relatado", type="boolean", nullable=true, options={"default":false})
     */
    protected $reported;

    /**
     * @var Boolean @Column(name="fechado", type="boolean", nullable=true, options={"default":false})
     */
    protected $closed;
   
    /**
     * @var String @Column(name="status", type="string", options={"default":"created"})
     */
    protected $status;
    
    /**
     * @var String @Column(name="relevancia", type="string", nullable=true, options={"default":"BAIXO"})
     */
    protected $relevance;

    /**
     * @var String @Column(name="relatado_por", type="string", nullable=true, options={"default":""})
     */
    protected $reportedBy;

    public function __construct($id = '', $origin = null) {
        parent::__construct();
        $this->id = $id;
        $this->ombudsmanUser = null;
        $this->ombudsmanUserDescription = null;
        $this->ombudsmanUserSugestion = null;
        $this->ombudsman = null;
        $this->ombudsmanDescription = null;
        $this->managerList = new ArrayCollection();
        $this->transmissionList = null;
        $this->origin = $origin;
        $this->demands = new ArrayCollection();
        $this->type = null;
        $this->bed = null;
        $this->group = null;
        $this->ombudsmanToResponse = null;
        $this->responseToUser = null;
        $this->registerTime = null;
        $this->reported = false;
        $this->closed = false;
        $this->status = 'created';
        $this->relevance = null;
        $this->reportedBy = null;
    }

    public function getId() {
        return $this->id;
    }
    public function setId($id) {
        $this->id = $id;
        
        return $this;
    }
    
    public function getOmbudsmanUser() {
        return $this->ombudsmanUser;
    }
    public function setOmbudsmanUser($ombudsmanUser) {
        $this->ombudsmanUser = $this->getRepositoryOf('OmbudsmanUser', $ombudsmanUser);
        
        return $this;
    }
    
    public function getOmbudsmanUserDescription() {
        return $this->ombudsmanUserDescription;
    }
    public function setOmbudsmanUserDescription($ombudsmanUserDescription) {
        $this->ombudsmanUserDescription = strtoupper($ombudsmanUserDescription);
        
        return $this;
    }
    
    public function getOmbudsmanUserSugestion() {
        return $this->ombudsmanUserSugestion;
    }
    public function setOmbudsmanUserSugestion($ombudsmanUserSugestion) {
        $this->ombudsmanUserSugestion = strtoupper($ombudsmanUserSugestion);
        
        return $this;
    }
    
    public function getOmbudsman() {
        return $this->ombudsman;
    }
    public function setOmbudsman($ombudsman) {
        $this->ombudsman = $this->getRepositoryOf('User', $ombudsman);
        
        return $this;
    }
    
    public function getOmbudsmanDescription() {
        return $this->ombudsmanDescription;
    }
    public function setOmbudsmanDescription($ombudsmanDescription) {
        $this->ombudsmanDescription = strtoupper($ombudsmanDescription);
        
        return $this;
    }
    
    public function getManagerList() {
        return $this->managerList;
    }
    public function addManagerOnList($manager) {
        $this->managerList->add( $this->getRepositoryOf('User', $manager) );

        return $this;
    }
    public function removeManagerOnList($manager) {
        $this->managerList->removeElement( $this->getRepositoryOf('User', $manager) );
        
        return $this;
    }
    public function setManagerList($managerList) {
        $managersArray = [];
        foreach ($managerList as $manager) {
            $managersArray[] = $this->getRepositoryOf('User', $manager);
        }
        $this->managerList = new ArrayCollection($managersArray);

        return $this;
    }
    
    public function getTransmissionList() {
        return $this->transmissionList;
    }
    public function addManagerOnTransmissionList($manager) {
        $this->transmissionList->add( $this->getRepositoryOf('User', $manager) );

        return $this;
    }
    public function removeManagerOnTransmissionList($manager) {
        $this->transmissionList->removeElement( $this->getRepositoryOf('User', $manager) );
        
        return $this;
    }
    public function setTransmissionList($transmissionList) {
        $managersArray = [];
        foreach ($transmissionList as $manager) {
            $managersArray[] = $this->getRepositoryOf('User', $manager);
        }
        $this->managerList = new ArrayCollection($managersArray);

        return $this;
    }

    public function getOrigin() {
        return $this->origin;
    }
    public function setOrigin($origin) {
        $this->origin = $this->getRepositoryOf('OmbudsmanOrigin', $origin);
        
        return $this;
    }

    public function getDemands() {
        return $this->demands;
    }
    public function addDemand($demand) {
        $this->demands->add($demand);

        return $this;
    }
    public function removeDemand($demand) {
        $this->demands->removeElement($demand);
        
        return $this;
    }
    public function setDemands($demands) {
        $demandsArray = [];
        foreach ($demands as $demand) {
            $demandsArray[] = $this->getRepositoryOf('OmbudsmanDemands', $demand);
        }
        $this->demands = new ArrayCollection($demandsArray);

        return $this;
    }

    public function getType() {
        return $this->type;
    }
    public function setType($type) {
        $this->type = $type;
        
        return $this;
    }

    public function getBed() {
        return $this->bed;
    }
    public function setBed($bed) {
        $this->bed = strtoupper($bed);
        
        return $this;
    }

    public function getGroup() {
        return $this->group;
    }
    public function setGroup($group) {
        $this->group = $this->getRepositoryOf('Group', $group);
        
        return $this;
    }
    
    public function getOmbudsmanToResponse() {
        return $this->ombudsmanToResponse;
    }
    public function setOmbudsmanToResponse($ombudsmanToResponse) {
        $this->ombudsmanToResponse = $this->getRepositoryOf('User', $ombudsmanToResponse);
        
        return $this;
    }

    public function getResponseToUser() {
        return $this->responseToUser;
    }
    public function setResponseToUser($responseToUser) {
        $this->responseToUser = $responseToUser;
        
        return $this;
    }
    
    public function getRegisterTime() {
        return $this->registerTime;
    }
    public function setRegisterTime($registerTime) {
        $this->registerTime = $this->_formatDate($registerTime);
        
        return $this;
    }
    
    public function getReported() {
        return $this->reported;
    }
    public function setReported($reported) {
        $this->reported = (Boolean)$reported;
        
        return $this;
    }    
    
    public function getClosed() {
        return $this->closed;
    }
    public function isClosed() {
        return $this->getClosed();
    }
    public function setClosed($closed) {
        $this->closed = (Boolean)$closed;
        
        return $this;
    }
    
    public function getStatus() {
        return $this->status;
    }
    public function setStatus($status) {
        $this->status = $status;
        
        return $this;
    }
    
    public function getRelevance() {
        return $this->relevance;
    }
    public function setRelevance($relevance) {
        $this->relevance = $relevance;
        
        return $this;
    }
    
    public function getReportedBy() {
        return $this->reportedBy;
    }
    public function setReportedBy($reportedBy) {
        $this->reportedBy = $reportedBy;
        
        return $this;
    }
}