<?php
namespace HospitalApi\Entity;

/**
 * @Entity
 * @\Doctrine\ORM\Mapping\HasLifecycleCallbacks
 * @Table(name="Ouvidoria_Usuario")
 * <b>OmbudsmanUser</b>
 */
class OmbudsmanUser extends SoftdeleteAbstract
{
    
    /**
     * @var Integer @Id
     *     @Column(name="id", type="integer")
     *     @GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var String @Column(name="nome_paciente", type="string", length=255)
     */
    protected $patientName;
    
    /**
     * @var String @Column(name="nome_declarante", type="string", length=255)
     */
    protected $declarantName;

    /**
     * @var Datetime @Column(name="data_nascimento", type="datetime")
     */
    protected $birthday;

    /**
     * @var String @Column(type="string", nullable=true)
     */
    protected $email;
    
    /**
     * @var String @Column(name="telefone", type="string", nullable=true)
     */
    protected $phoneNumber;
    
    /**
     * @var String @Column(name="endereco", type="string", nullable=true)
     */
    protected $address;

    public function __construct() {
        parent::__construct();
        $this->id = 0;
        $this->patientName = '';
        $this->declarantName = '';
        $this->birthday = new \DateTime();
        $this->email = null;
        $this->phoneNumber = '';
        $this->address = '';
    }

    public function setOmbudsmanUser($ombudsmanUser) {
        $this
            ->setPatientName($ombudsmanUser['patientName'])
            ->setBirthday($ombudsmanUser['birthday'])
            ->setEmail( (isset($ombudsmanUser['email']) && $ombudsmanUser['declarantName']) ? $ombudsmanUser['email'] : null )
            ->setDeclarantName( (isset($ombudsmanUser['declarantName']) && $ombudsmanUser['declarantName']) ? $ombudsmanUser['declarantName'] : "PRÓPRIO PACIENTE")
            ->setPhoneNumber($ombudsmanUser['phoneNumber'])
            ->setAddress($ombudsmanUser['address']);
        return $this;
    }

    public function getId() {
        return $this->id;
    }
    public function setId($id) {
        $this->id = $id;
        
        return $this;
    }
   
    public function getPatientName() {
        return $this->patientName;
    }
    public function setPatientName($patientName) {
        $this->patientName = strtoupper($patientName);
        
        return $this;
    }
   
    public function getDeclarantName() {
        return $this->declarantName;
    }
    public function setDeclarantName($declarantName) {
        $this->declarantName = strtoupper($declarantName);
        
        return $this;
    }
    
    public function getBirthday() {
        return $this->birthday;
    }
    public function setBirthday($birthday) {
        $this->birthday = $this->_formatDate($birthday);
        
        return $this;
    }

    public function getEmail() {
        return $this->email;
    }
    public function setEmail($email) {
        $this->email = $email;
        
        return $this;
    }

    public function getPhoneNumber() {
        return $this->phoneNumber;
    }
    public function setPhoneNumber($phoneNumber) {
        $this->phoneNumber = $phoneNumber;
        
        return $this;
    }

    public function getAddress() {
        return $this->address;
    }
    public function setAddress($address) {
        $this->address = strtoupper($address);
        
        return $this;
    }

}