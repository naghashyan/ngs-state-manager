<?php
/**
 * NgsJsonStateManager manager class, this class used for state management
 * states are being configured by passed config array, config array structure you can find in readme file
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
use NgsStateManager\exceptions\NgsStateException;


class NgsJsonStateManager extends NgsStateManager
{
    /** @var array $stateConfigs */
    private $stateConfigs;



    /**
     * NgsJsonStateManager constructor.
     *
     * @param array $stateConfigs
     * @param INgsStateableMapper $ngsStateableMapper
     *
     * @throws NgsStateException
     */
    public function __construct(array $stateConfigs, $ngsStateableMapper)
    {
        parent::__construct($ngsStateableMapper);

        $this->validateConfigs($stateConfigs);
        $this->stateConfigs = $stateConfigs['states'];
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
        foreach($this->stateConfigs as $stateConfig) {
            if($stateConfig['name'] === $stateName) {
                return $stateConfig;
            }
        }

        return null;
    }


    /**
     * if configs are incorrect this function will throw exception
     *
     * @param $statesConfigs
     *
     * @throws NgsStateException
     */
    private function validateConfigs($statesConfigs) {
        if(!$statesConfigs) {
            throw new NgsStateException('configs can not be empty');
        }
        if(!isset($statesConfigs['states']) || !is_array($statesConfigs['states']) || !$statesConfigs['states']) {
            throw new NgsStateException('configs should have states as array, and not empty');
        }

        $states = $statesConfigs['states'];
        foreach($states as $state) {
            $this->validateEachStateConfig($state);
        }
    }


    /**
     * if state array in incorrect this function will throw exception
     *
     * @param array $stateConfig
     *
     * @throws NgsStateException
     */
    private function validateEachStateConfig(array $stateConfig) {
        if(!$stateConfig) {
            throw new NgsStateException('state can not be empty');
        }
        if(!isset($stateConfig['name'])) {
            throw new NgsStateException('state should have field name');
        }
        if(isset($stateConfig['nextStates']) && $stateConfig['nextStates']) {
            foreach($stateConfig['nextStates'] as $nextState) {
                $this->validateNextStateConfig($nextState);
            }
        }
    }


    /**
     * if next state array is incorrect this function will throw exception
     *
     * @param array $nextStateConfig
     *
     * @throws NgsStateException
     */
    private function validateNextStateConfig(array $nextStateConfig) {
        if(!isset($nextStateConfig['name']) || !$nextStateConfig['name']) {
            throw new NgsStateException('next state should have field name');
        }
        if(!isset($nextStateConfig['userGroups']) || !$nextStateConfig['userGroups']) {
            throw new NgsStateException('next state should have field userGroups');
        }
    }
}
