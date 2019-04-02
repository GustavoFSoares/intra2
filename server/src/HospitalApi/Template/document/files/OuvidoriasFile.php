<?php
namespace HospitalApi\Template\Document\Files;

use \HospitalApi\Model\OmbudsmanModel;
use \HospitalApi\Entity\Ombudsman;

require_once(PATH.'/../vendor/tecnick.com/tcpdf/tcpdf.php');
class OuvidoriasFile extends \TCPDF {

    public $key;
    public $origin;

    public $mleft = 10;
    public $mtop = 30;
    public $mright = -1;
    public $footer = 0;

    public $reprint = false;
    private $_model;
    private $_EnablePageCounter = false;

    public function __construct($params) {
        $this->_model = new OmbudsmanModel();

        if(array_key_exists('id', $params)) { 
            $this->key = $this->brokeId( $params['id'] );
            $ombudsman = $this->_model->findById( $params['id'] );
            if(!$ombudsman) {
                return;
            }
            $this->origin = $ombudsman->getOrigin();
            $this->reprint = true;
        } else {
            $this->origin = $params['origin'];
        }

        
        if(isset($params['pageCount'])) {
            $this->_EnablePageCounter = true;
        }

        parent::__construct();
    }

	//Page header
	public function Header() {
        $imageFile = FILES.'logo/';
        
        $this->Image($imageFile."logo-hu-05.jpg", 7, 10, 70, '', 'JPG', '', 'T', true, 600, '', false, false, 0, false, false, false);
        // $this->Image($imageFile."logo-gamp.jpg", 47, 10, 35, '', 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);
        
		$this->SetFont('helvetica', 'B', 15);
        
        $this->MultiCell(0, 0, 'SERVIÇO DE OUVIDORIA', 0, 'R', 0, 2, 10, '', true);
        $this->ln(1);

        $this->MultiCell(0, 0, strtoupper($this->origin->getName()), 0, 'R', 0, 1, '', '', true);
	}

	// Page footer
	public function Footer() {
		// Position at 15 mm from bottom
		$this->SetY(-15);
        
        if($this->_EnablePageCounter) {
            $this->SetFont('helvetica', 'I', 8);
            // Page number
            $this->Cell(0, 10, 'Página '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
        }

        $this->SetFont('helvetica', 'I', 10);        
        $this->Cell(0, 10, 'Av. Farroupilha, 8001 - São José - Fone: (51) 3478-8100', 0, false, 'R', 0, '', 0, false, 'T', 'M');
        
    }
    
    public function getContent() {
        $key = $this->_getKey();
        if(!$this->reprint) {
            $this->insertOmbudman();
        }

        $line = "<span>_________________________________________________________________________________________</span>";
            $space = '<span class="clear">_</span>';
            $html = "
            <style>
                .clear {
                    color: white;
                }

                .code {
                    font-size: 15px;
                    font-weight: bold;
                }
            </style>";
            
            $html .= "
            <body>
                <div>
                    <span class=".'"code"'.">Nº {$key['prefix']}{$key['number']} /".date('Y')."</span>
                </div>
                <div>
                    <span>Nome completo do Paciente:_________________________________________________________________</span>
                </div>";


            switch ($this->origin->getId()) {
                case 'AMB':
                $html .= 
                "<div>
                    <span>Data Nascimento:_________________ Setor: _______________________ Data do Registro:____/____/____</span>
                </div>";
                break;
                    
                case 'INT':
                $html .= 
                "<div>
                    <span>Data Nascimento:_________________ Leito: _______________________ Data do Registro:____/____/____</span>
                </div>";                    
                break;
            }
      
      $html .= "<div>
                    <span>Nome completo do Declarante:_______________________________________________________________</span>
                </div>
                <div>
                    <span>Email:____________________________________________________ Fone: ($space)_______________________</span>
                </div>
                <div>
                    <span>Endereço:________________________________________________________________________________</span>
                </div>
                <div>
                    <span>($space) Denúncia $space($space) Solicitação $space($space) Reclamação $space($space) Sugestão $space($space) Elogio $space($space) Outros: __________________</span>
                </div>
                <br>
                <div>
                    <span><b>Descriçao: (Relatar o ocorrido com detalhes, citar nomes, datas, hora, etc)</b></span>
                </div>";
                for ($i=0; $i < 17; $i++) { 
                    $html .= "$line\n";
                }

    $html .= "  <br>
                <div>
                    <span><b>Assinatura:</b> __________________________________________________________________</span>
                </div>
            </body>";

    $html .= "  <br>
                <div>
                    <span><b>Área do Ouvidor:</b></span>
                </div>";
                for ($i=0; $i < 7; $i++) { 
                    $html .= "$line\n";
                }

        return $html;
    }

    private function _getKey() {
        
        if($this->key) {
            if(!$this->reprint) {
                $this->key['number']++;
            }
        } else {
            $data = $this->_model->getLastKeyOfOrigin($this->origin);

            if($data) {
                $this->key = [
                    'prefix' => substr($data->getId(), 0, 3),
                    'number' => substr($data->getId(), 3)+1
                ];
            } else {
                $this->key = [
                    'prefix' => $this->origin->getId(),
                    'number' => '1001',
                ];
            }
        }
        
        return $this->key;
    }

    public function insertOmbudman() {
        $id = "{$this->key['prefix']}{$this->key['number']}";
        $ombudsman = new Ombudsman($id);
        $ombudsman->setOrigin($this->origin);
        $this->_model->doInsert($ombudsman);
    }

    public function brokeId($id) {
        return [
            'prefix'=>substr($id, 0, 3),
            'number'=>substr($id, 3),
        ];
    }
}