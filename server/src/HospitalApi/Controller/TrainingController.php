<?php
namespace HospitalApi\Controller;

use HospitalApi\Model\TrainingModel;

/**
 * <b>TrainingController</b>
 */
class TrainingController extends ControllerAbstractLongEntity
{
    public function __construct() {
        parent::__construct(new TrainingModel());
    }

    public function getUnrealized($req, $res, $args) {
        $collection = $this->getModel()->getUnrealized();
        $data = $this->translateCollection($collection);

        return $res->withJson($data);
    }
    
    public function isDone($req, $res, $args) {
        $id = $args['id'];
        
        $return = $this->getModel()->isDone($id);
        return $res->withJson($return);
    }

    public function getTrainingsAction($req, $res, $args) {
        
        $data = $this->getModel()->getTrainings();
        
        $newRow = [];
        foreach ($data as &$row) {
            foreach ($row as $key => $line) {
                $newRow[] = $line;
            }
            $row = $newRow;
            $newRow = [];
        }
        // $data = $newData;

        // array_unshift($data, [ 'QuantidadeTreinamentos', 'CargaHorariaEmHoras', 'NumeroParticipantes', ]);
        array_unshift($data, [ 'CargaHorariaEmHoras', ]);
        // array_unshift($data, [ 'TreinamentoNome', 'QuantidadeTreinamentos', 'TreinamentoTipo', 'TreinamentoTipoInstitucional', 'CargaHorariaEmHoras', 'NumeroParticipantes', 'Empresa', ]);

        return $res->withJson($data);
    }

}