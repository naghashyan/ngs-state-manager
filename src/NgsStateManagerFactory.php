<?php
/**
 * NgsStateManagerFactory factory class which used to create instances of NgsJsonStateManager and NgsDBStateManager
 * by calling function createJsonStateManager or createDbStateManager functional will return instance of managers,
 * or throw NgsStateException exception
 *
 * @author Mikael Mkrtchyan
 * @site http://naghashyan.com
 * @mail mikael.mkrtchyan@naghashyan.com
 * @year 2020
 * @package NgsStateManager
 * @version 1.0.0
 *
 */

namespace NgsStateManager;


use NgsStateManager\dal\mappers\INgsStateableMapper;
use NgsStateManager\managers\NgsDBStateManager;
use NgsStateManager\managers\NgsJsonStateManager;
use NgsStateManager\exceptions\NgsStateException;

class NgsStateManagerFactory
{
    /**
     * @var array $jsonStateManagers
     */
    private static $jsonStateManagers = [];

    /**
     * @var array $dmStateManager
     */
    private static $dmStateManagers = [];


    /**
     * creates and returns ngsJsonStateManager by given configs and mapper,
     * if manager for given configs and mapper was created before returns already created instance
     *
     * @param array $statesConfigs
     * @param INgsStateableMapper $ngsStateableMapper
     *
     * @return NgsJsonStateManager
     *
     * @throws NgsStateException
     */
    public static function createJsonStateManager(array $statesConfigs, $ngsStateableMapper) {
        $managerKey = md5(json_encode($statesConfigs) . '_' . get_class($ngsStateableMapper));

        if(isset(self::$jsonStateManagers[$managerKey])) {
            return self::$jsonStateManagers[$managerKey];
        }

        self::$jsonStateManagers[$managerKey] = new NgsJsonStateManager($statesConfigs, $ngsStateableMapper);
        return self::$jsonStateManagers[$managerKey];
    }


    /**
     * creates and returns ngsDbStateManager by given mapper,
     * if manager for given mapper was created before returns already created instance
     *
     * @param $ngsStateableMapper
     *
     * @return NgsDBStateManager
     *
     * @throws NgsStateException
     */
    public static function createDbStateManager($ngsStateableMapper) {
        $managerKey = md5(get_class($ngsStateableMapper));

        if(isset(self::$dmStateManagers[$managerKey])) {
            return self::$dmStateManagers[$managerKey];
        }

        self::$dmStateManagers[$managerKey] = new NgsDBStateManager($ngsStateableMapper);
        return self::$dmStateManagers[$managerKey];
    }
}
