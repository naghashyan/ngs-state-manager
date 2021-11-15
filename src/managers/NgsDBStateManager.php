<?php
/**
 * NgsDBStateManager manager class, this class used for state management
 * states are being configured by using DB, project should have table ngs_states structure example yu can find in read me file
 * for create manager instance you need also pass mapper which implements INgsStateableMapper interface
 *
 * @author Mikael Mkrtchyan
 * @site http://naghashyan.com
 * @mail mikael.mkrtchyan@naghashyan.com
 * @year 2020
 * @package NgsStateManager
 * @version 1.0.0
 *
 */

namespace NgsStateManager\managers;

use NgsStateManager\dal\mappers\INgsStateableMapper;
use NgsStateManager\dal\mappers\NgsStateMapper;
use NgsStateManager\exceptions\NgsStateException;


class NgsDBStateManager extends NgsStateManager
{

    /**
     * NgsDBStateManager constructor.
     *
     * @param INgsStateableMapper $ngsStateableMapper
     *
     * @throws NgsStateException
     */
    public function __construct($ngsStateableMapper)
    {
        parent::__construct($ngsStateableMapper);
    }


    /**
     * returns state info found from configs by name
     *
     * @param string $stateName
     *
     * @return array|null
     */
    protected function getStateInfoByName(string $stateName): ?array
    {
        $stateMapper = NgsStateMapper::getInstance();
        $stateDto = $stateMapper->getStateByName($stateName);

        if(!$stateDto) {
            return null;
        }

        return $stateDto->getJson();
    }
}
