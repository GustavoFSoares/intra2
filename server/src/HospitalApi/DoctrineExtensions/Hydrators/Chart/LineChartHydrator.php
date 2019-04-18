<?php
namespace HospitalApi\DoctrineExtensions\Hydrators\Chart;

use HospitalApi\DoctrineExtensions\FetchMode    ;
use Doctrine\ORM\Internal\Hydration\AbstractHydrator;

class LineChartHydrator extends AbstractHydrator 
{

    // public function hydrateAll($stmt, $resultSetMapping, $hints) {
    //     $this->_stmt  = $stmt;
    //     $this->_rsm   = $resultSetMapping;
    //     $this->_hints = $hints;

    //     $evm = $this->_em->getEventManager();
    //     $evm->addEventListener(array(Events::onClear), $this);

    //     $this->prepare();

    //     $result = $this->hydrateAllData();

    //     $this->cleanup();
        
    //     return $result;
    // }

    public function hydrateRowData($row, &$result) {
        $row = array_values($row);

        foreach ($row as $key => &$value) {
            if($key == 0) {
                continue;
            }

            $value = (int)$value;
        }

        $result[] = $row;
    }

    protected function hydrateAllData() {
        $result = [];
        while ($row = $this->_stmt->fetch(FetchMode::ASSOCIATIVE)) {
            $this->hydrateRowData($row, $result);
        }
        
        return $result;
    }
}