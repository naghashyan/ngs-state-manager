<?php
/**
 * NgsStateDto dto class
 * if you use NgsDBStateManager, this dto will store states info, next state, actions, etc...
 *
 * @author Mikael Mkrtchyan
 * @site http://naghashyan.com
 * @mail mikael.mkrtchyan@naghashyan.com
 * @year 2020
 * @package NgsStateManager\dal\dto
 * @version 1.0.0
 *
 */

namespace NgsStateManager\dal\dto;

use ngs\dal\dto\AbstractDto;

class NgsStateDto extends AbstractDto
{

    /** @var array */
    protected $mapArray = [
        'id' => 'id',
        'name' => 'name',
        'description' => 'description',
        'next_states' => 'nextStates',
        'before_actions' => 'beforeActions',
        'after_actions' => 'afterActions'
    ];

    /**
     * @var int $id
     */
    private $id;

    /**
     * @var string $name
     */
    private $name;

    /**
     * @var string $description
     */
    private $description;

    /**
     * @var string $nextStates
     */
    private $nextStates;


    /**
     * @var string $beforeActions
     */
    private $beforeActions;


    /**
     * @var string $afterActions
     */
    private $afterActions;


    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return array
     */
    public function getNextStates(): array
    {
        $nexStates = json_decode($this->nextStates, true);
        return $nexStates;
    }

    /**
     * @param string $nextStates
     */
    public function setNextStates(string $nextStates): void
    {
        $this->nextStates = $nextStates;
    }

    /**
     * @return array
     */
    public function getBeforeActions(): array
    {
        $beforeActions = json_decode($this->beforeActions, true);
        return $beforeActions;
    }

    /**
     * @param string $beforeActions
     */
    public function setBeforeActions(string $beforeActions): void
    {
        $this->beforeActions = $beforeActions;
    }

    /**
     * @return array
     */
    public function getAfterActions(): array
    {
        $afterActions = json_decode($this->afterActions, true);
        return $afterActions;
    }

    /**
     * @param string $afterActions
     */
    public function setAfterActions(string $afterActions): void
    {
        $this->afterActions = $afterActions;
    }


    /**
     * @return array
     */
    public function getMapArray() {
        return $this->mapArray;
    }


    /**
     * returns array format of DTO
     *
     * @return array
     */
    public function getJson()
    {
        return [
            'name' => $this->getName(),
            'description' => $this->getDescription(),
            'nextStates' => $this->getNextStates(),
            'beforeActions' => $this->getBeforeActions(),
            'afterActions' => $this->getAfterActions()
        ];
    }

}

