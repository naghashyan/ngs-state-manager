<?php
/**
 * NgsStateMapper
 *
 * @author Mikael Mkrtchyan
 * @site http://naghashyan.com
 * @mail mikael.mkrtchyan@naghashyan.com
 * @year 2020
 * @package NgsStateManager\dal\mappers
 * @version 1.0.0
 *
 */

namespace NgsStateManager\dal\mappers;

use ngs\dal\dto\AbstractDto;
use ngs\dal\mappers\AbstractMysqlMapper;
use NgsStateManager\dal\dto\NgsStateDto;

class NgsStateMapper extends AbstractMysqlMapper
{
    /** @var NgsStateMapper $instance */
    private static $instance;

    /** @var string */
    private $tableName = 'ngs_states';

    /** @var array $statesByNames */
    private $statesByNames;


    /**
     * Returns an singleton instance of this class
     *
     * @return NgsStateMapper Object
     */
    public static function getInstance(): NgsStateMapper
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }


    /**
     * return table name of states
     *
     * @return string
     */
    public function getTableName(): string {
        return $this->tableName;
    }


    /**
     * @return AbstractDto
     */
    public function createDto(): AbstractDto
    {
        return new NgsStateDto();
    }

    /**
     * @see AbstractMysqlMapper::getPKFieldName()
     */
    public function getPKFieldName(): string
    {
        return 'id';
    }


    /** @var string */
    private $GET_STATE_BY_NAME = "SELECT * FROM `%s` WHERE `%s`.`name` = '%s'";


    /**
     * returns state found by name (uses cache)
     *
     * @param string $stateName
     * @return NgsStateDto|null
     */
    public function getStateByName(string $stateName)
    {
        if(isset($this->statesByNames[$stateName])) {
            return $this->statesByNames[$stateName];
        }

        $sqlQuery = sprintf($this->GET_STATE_BY_NAME, $this->getTableName(), $this->getTableName(), $stateName);
        $stateDto = $this->fetchRow($sqlQuery);
        $this->statesByNames[$stateName] = $stateDto;

        return $this->statesByNames[$stateName];
    }
}
