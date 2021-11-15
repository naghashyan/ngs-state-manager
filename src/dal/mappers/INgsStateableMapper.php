<?php
/**
 * INgsStateableMapper mapper interface
 *
 * @author Mikael Mkrtchyan
 * @site http://naghashyan.com
 * @mail mikael.mkrtchyan@naghashyan.com
 * @year 2020
 * @package NgsStateManager
 * @version 1.0.0
 *
 */

namespace NgsStateManager\dal\mappers;


use NgsStateManager\dal\dto\INgsStateableDto;

interface INgsStateableMapper
{

    /**
     * @param INgsStateableDto $stateableDto
     * @param string $newState
     *
     * @return bool
     */
    public function updateState($stateableDto, string $newState) :bool;
}
