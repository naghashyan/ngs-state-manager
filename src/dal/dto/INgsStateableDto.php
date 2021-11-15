<?php
/**
 * INgsStateableDto dto interface
 *
 * @author Mikael Mkrtchyan
 * @site http://naghashyan.com
 * @mail mikael.mkrtchyan@naghashyan.com
 * @year 2020
 * @package NgsStateManager
 * @version 1.0.0
 *
 */

namespace NgsStateManager\dal\dto;


interface INgsStateableDto
{
    /**
     * returns current state of the object
     *
     * @return string
     */
    public function getState();


    /**
     * set state to the object
     *
     * @param string $state
     */
    public function setState(string $state): void;
}
