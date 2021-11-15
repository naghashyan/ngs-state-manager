<?php
/**
 * NgsStateManager abstract manager class
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

use ngs\AbstractManager;
use NgsStateManager\dal\dto\INgsStateableDto;
use NgsStateManager\dal\mappers\INgsStateableMapper;
use NgsStateManager\exceptions\NgsStateException;


abstract class NgsStateManager extends AbstractManager
{
    /** @var INgsStateableMapper $stateableMapper */
    private $stateableMapper;

    /** @var */
    private $ch;


    /**
     * NgsStateManager constructor.
     *
     * @param $ngsStateableMapper
     *
     * @throws NgsStateException
     */
    public function __construct($ngsStateableMapper)
    {
        if (!$ngsStateableMapper instanceof INgsStateableMapper) {
            throw new NgsStateException('mapper is not instance of INgsStateableMapper');
        }
        $this->ch = curl_init();
        $this->stateableMapper = $ngsStateableMapper;
        parent::__construct();
    }

    /**
     * @param $stateableDto
     * @param string $newStateName
     * @param string $userGroup
     * @param array $requestParams
     * @return bool
     * @throws NgsStateException
     */
    public function changeState($stateableDto, string $newStateName, string $userGroup, array $requestParams = [])
    {
        if (!$stateableDto instanceof INgsStateableDto) {
            throw new NgsStateException('dto is not instance of INgsStateableDto');
        }

        $currentState = $stateableDto->getState();

        $currentStateInfo = $this->getStateInfoByName($currentState);
        $nextStateInfo = $this->getStateInfoByName($newStateName);

        if (!$currentStateInfo) {
            throw new NgsStateException('state by name ' . $currentState . ' not found');
        }
        if (!$nextStateInfo) {
            throw new NgsStateException('state by name ' . $newStateName . ' not found');
        }

        $this->beforeChange($currentStateInfo, $nextStateInfo, $userGroup, $requestParams);

        $canSwitch = $this->canSwitchToState($currentStateInfo, $newStateName, $userGroup);
        if (!$canSwitch) {
            $this->changeError($currentStateInfo, $nextStateInfo, $userGroup, $requestParams);
            return false;
        }

        $result = $this->stateableMapper->updateState($stateableDto, $newStateName);
        if (!$result) {
            $this->changeError($currentStateInfo, $nextStateInfo, $userGroup, $requestParams);
            return false;
        }

        $this->changeSuccess($currentStateInfo, $nextStateInfo, $userGroup, $requestParams);
        return true;
    }


    protected abstract function getStateInfoByName(string $stateName): ?array;


    /**
     * this function returns true if state can be changed from $currentState to $newState
     *
     * @param array $currentStateInfo
     * @param string $newState
     * @param string $userGroup
     *
     * @return bool
     *
     */
    private function canSwitchToState(array $currentStateInfo, string $newState, string $userGroup)
    {

        $possibleNextStates = isset($currentStateInfo['nextStates']) ? $currentStateInfo['nextStates'] : [];

        if (!$possibleNextStates) {
            return false;
        }

        $stateFromNextStates = $this->getStateByNameFromStates($possibleNextStates, $newState);

        if (!$stateFromNextStates) {
            return false;
        }

        $allowedUserGroups = isset($stateFromNextStates['userGroups']) ? $stateFromNextStates['userGroups'] : [];
        if (!$allowedUserGroups || !in_array($userGroup, $allowedUserGroups)) {
            return false;
        }

        return true;
    }


    /**
     * this function returns state from list of states by name, if not found returns null
     *
     * @param array $states
     * @param string $stateName
     *
     * @return array|null
     */
    private function getStateByNameFromStates(array $states, string $stateName): ?array
    {

        foreach ($states as $state) {
            if ($state['name'] === $stateName) {
                return $state;
            }
        }

        return null;
    }


    /**
     * this function will be called before state change functional work
     *
     * @param array $currentStateInfo
     * @param array $newStateInfo
     * @param string $userGroup
     * @param array $requestParams
     */
    private function beforeChange(array $currentStateInfo, array $newStateInfo, string $userGroup, array $requestParams = [])
    {
        if (!isset($newStateInfo['beforeActions']) || !$newStateInfo['beforeActions']) {
            return;
        }
        $actions = $newStateInfo['beforeActions'];

        $params = [
            'from' => $currentStateInfo['name'],
            'to' => $newStateInfo['name'],
            'group' => $userGroup
        ];

        $params = array_merge($params, $requestParams);

        foreach ($actions as $action) {
            $this->doRequest($action, $params);
        }
    }

    /**
     * this function will be called when state successfully changed
     *
     * @param array $currentStateInfo
     * @param array $newStateInfo
     * @param string $userGroup
     * @param array $requestParams
     */
    private function changeSuccess(array $currentStateInfo, array $newStateInfo, string $userGroup, array $requestParams = [])
    {
        if (!isset($newStateInfo['afterActions']) || !$newStateInfo['afterActions']) {
            return;
        }
        $actions = $newStateInfo['afterActions'];

        $params = [
            'from' => $currentStateInfo['name'],
            'to' => $newStateInfo['name'],
            'group' => $userGroup,
            'success' => true
        ];

        $params = array_merge($params, $requestParams);

        foreach ($actions as $action) {
            $this->doRequest($action, $params);
        }
    }


    /**
     * this function will be called if state could not changed to new
     *
     * @param array $currentStateInfo
     * @param array $newStateInfo
     * @param string $userGroup
     * @param array $requestParams
     */
    private function changeError(array $currentStateInfo, array $newStateInfo, string $userGroup, array $requestParams = [])
    {
        if (!isset($newStateInfo['afterActions']) || !$newStateInfo['afterActions']) {
            return;
        }
        $actions = $newStateInfo['afterActions'];

        $params = [
            'from' => $currentStateInfo['name'],
            'to' => $newStateInfo['name'],
            'group' => $userGroup,
            'success' => false
        ];

        $params = array_merge($params, $requestParams);

        foreach ($actions as $action) {
            $this->doRequest($action, $params);
        }
    }


    /**
     * do post request to given url with params
     *
     * @param string $url
     * @param array $params
     */
    private function doRequest(string $url, array $params)
    {
        $url = NGS()->getDefinedValue('IM_API_URL') . $url;
        $fieldsJson = json_encode($params);

        curl_setopt($this->ch, CURLOPT_URL, $url);
        curl_setopt($this->ch, CURLOPT_POST, true);
        curl_setopt($this->ch, CURLOPT_POSTFIELDS, $fieldsJson);

        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);

        curl_exec($this->ch);
    }
}
