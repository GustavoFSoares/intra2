<?php
namespace HospitalApi\Entity;

/**
 * @Entity
 * @Table(name="Ouvidoria_Mensagem_Notificacao")
 * <b>OmbudsmanMessagesNotification</b>
 */
class OmbudsmanMessagesNotification extends EntityAbstract
{
    /**
     * @var integer @Id
     *      @Column(name="id", type="integer")
     *      @GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var User
     *      @ManytoOne(targetEntity="User")
     *      @JoinColumn(name="usuario_id")
     */
    protected $user;

    /**
     * @var IncidentReporting
     *      @ManytoOne(targetEntity="Ombudsman")
     *      @JoinColumn(name="ouvidoria_id")
     */
    protected $ombudsman;

    /**
     * @var Integer
     *      @Column(name="quantidade_mensagens", type="integer", options={ "default":"1" })
     */
    protected $count;

    public function __construct($id = 0, $user = null, $ombudsman = null) {
        parent::__construct();
        $this->id = $id;
        $this->user = $user;
        $this->ombudsman = $ombudsman;
        $this->count = 1;
    }

    public function getId() {
        return $this->id;
    }
    public function setId($id) {
        $this->id = $id;

        return $this;
    }

    public function getUser() {
        return $this->user;
    }
    public function setUser($user) {
        $this->user = $this->getRepositoryOf('User', $user);

        return $this;
    }

    public function getCount() {
        return $this->count;
    }
    public function setCount($count) {
        $this->count = $count;
        
        return $this;
    }
    public function plusOne() {
        $this->count = $this->getCount()+1;
    
        return $this;
    }

    public function getOmbudsman() {
        return $this->ombudsman;
    }
    public function setOmbudsman($ombudsman) {
        $this->ombudsman = $this->getRepositoryOf('Ombudsman', $ombudsman);

        return $this;
    }

}