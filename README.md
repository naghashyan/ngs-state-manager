# ngs-state-manager
Naghashyan State Management Library

This library used for help state management, it will control if object state can be changed from X -> Y.
To create state manager instance you should use NgsStateManagerFactory. It has 2 static methods:



# 1. createJsonStateManager 
This function should get 2 parameters: $statesConfigs, $ngsStateableMapper
$statesConfigs is array which will help to library to understand which next states has current state, and who can change state
$statesConfigs structure should be like this:
    
    [
      "states" => [
        [
          "name" => "state1",   // the name of state, for changing state you should pass name of state as an expecting state change
          "description" => "desc1", // the description of state
          "nextStates" => [   //nextStates - is array which informs about possible states which can be changed from state 'state1'
            [
              "name" => "state2", // possible change state name
              "userGroups" => ["userGroup1", "userGroup2"] // user goups list who can change to this state
            ],
            [
              "name" => "state3",
              "userGroups" => ["userGroup1"]
            ]
          ],
          "beforeActions": ["URL1", "URL2"],  //list of urls which will be called before state change functional
          "afterActions": ["URL3"] //list of urls which will be called after state change functional
        ],
        [
          "name" => "state2",
          "description" => "desc2",
          "nextStates" => [
            {
              "name" => "state3",
              "userGroups" => ["userGroup2"]
            }
          ],
          "beforeActions" => [],
          "afterActions" => ["URL3"]
        ],
        [
          "name" => "state3",
          "description" => "desc3",
          "nextStates" => [],
          "beforeActions" => ["URL4"],
          "afterActions" => []
        ]
      ]
    ]

    $ngsStateableMapper is mapper of object, to work with this library, mapper should implement INgsStateableMapper interface

    createJsonStateManager function after call will return instance of NgsJsonStateManager which has method changeState($stateableDto, string $newStateName, string $userGroup)
    $stateableDto is dto object which should implement INgsStateableDto interface

    changeState method will return boolean if state changed or not or can throw exception NgsStateException



# 2. createDbStateManager 
This function should get $ngsStateableMapper as parameter
$ngsStateableMapper is mapper of object, to work with this library, mapper should implement INgsStateableMapper interface

   createDbStateManager function after call will return instance of NgsDBStateManager
   this manager to understand state change ruls uses table ngs_states the row of table should has this columns:

   name - name of state
   description - description of state
   next_states - json which has same structure as 'nextStates' described above
   before_actions - json array of urls which will be called before state change functional
   after_actions - json array of urls which will be called after state change functional

   NgsDBStateManager has method changeState($stateableDto, string $newStateName, string $userGroup)
   $stateableDto is dto object which should implement INgsStateableDto interface

   changeState method will return boolean if state changed or not or can throw exception NgsStateException
