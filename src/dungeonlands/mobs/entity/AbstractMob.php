<?php

declare(strict_types=1);

namespace dungeonlands\mobs\entity;

use pocketmine\entity\EntitySizeInfo;
use pocketmine\entity\Living;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;

abstract class AbstractMob extends Living
{
    protected static string $_typeID = "minecraft:player";

    protected int $_health = 20;

    protected float $_speed = 0.1;

    protected bool $_canClimb = false;
    protected bool $_hasGravity = true;

    protected float $_sizeHeight = 2.5;
    protected float $_sizeWidth = 1.0;

    protected function initEntity(CompoundTag $nbt): void
    {
        parent::initEntity($nbt);

        $this->setHealth($this->_health);
        $this->setMaxHealth($this->_health);

        $this->setMovementSpeed($this->_speed);

        $this->setCanClimb($this->_canClimb);
        $this->setCanClimbWalls($this->_canClimb);
        $this->setHasGravity($this->_hasGravity);

        $this->setCanSaveWithChunk(false);

        $this->_defaultLook = new Vector3(0, 0, 0);
        $this->_destination = new Vector3(0, 0, 0);
    }

    public static function getNetworkTypeId(): string
    {
        return static::$_typeID;
    }

    public function getName(): string
    {
        return $this->_name;
    }

    protected function getInitialSizeInfo(): EntitySizeInfo
    {
        return new EntitySizeInfo($this->_sizeHeight, $this->_sizeWidth);
    }

    //MORE
    private Vector3 $_defaultLook;
    private Vector3 $_destination;

    protected function entityBaseTick(int $tickDiff = 1): bool
    {
        $this->_tick();
        return parent::entityBaseTick($tickDiff);
    }

    private function _tick(): void
    {
        if (mt_rand(1, 5) === 3) {
            return;
        }

        $pos = $this->_destination;
        if ($pos->x === 0 and $pos->y === 0 and $pos->z === 0) {
            $this->_destination = $this->_getRandomDestination();
        }

        $this->_move();
        $this->_wait();
    }

    private function _move(): void
    {
        $motion = $this->getMotion();
        $location = $this->getLocation();
        $swimming = $this->isSwimming();
        $flying = $this->isOnGround();

        if ($this->isCollided and $swimming) {
            $this->_destination = $this->_getRandomDestination();
        }

        $targetPos = $this->calculateMotion();
        $motion->x = $targetPos->x;
        $motion->y = $targetPos->y;
        $motion->z = $targetPos->z;

        $vec = new Vector3($motion->x, $motion->y, $motion->z);
        $look = $location->add($motion->x, $motion->y + $this->getEyeHeight(), $motion->z);

        $this->_defaultLook = $look;
        $this->lookAt($look);

        $this->setMotion($vec);
    }

    private function _wait(): void
    {
        $location = $this->getLocation();

        if ($this->lastUpdate % 100 === 0) {
            if ($this->getHealth() < $this->getMaxHealth()) {
                $this->setHealth($this->getHealth() + 2);
            }
        }

        #add fire to nightlyentitys

        if (mt_rand(0, 100) === 50) {
            $this->lookAt($this->_defaultLook);
        } elseif (mt_rand(0, 100) === 50) {
            $x = $location->x + mt_rand(-1, 1);
            $y = $location->y + mt_rand(-1, 1);
            $z = $location->z + mt_rand(-1, 1);
            $this->lookAt(new Vector3($x, $y, $z));
        }
    }

    private function calculateMotion(): Vector3
    {
        $destination = $this->_destination;
        $pos = $this->getPosition();
        $motion = $this->getMotion();
        $speed = $this->_speed;

        $x = $destination->x - $pos->x;
        $y = $destination->y - $pos->y;
        $z = $destination->z - $pos->z;

        $diff = abs($x) + abs($z);

        $motion->x = $speed * 0.15 * ($x / $diff);
        $motion->y = 0;
        $motion->z = $speed * 0.15 * ($z / $diff);

        if ($this->isSwimming()) {
            $motion->y = $speed * 0.15 * ($y / $diff);
        }

        return new Vector3($motion->x, $motion->y, $motion->z);
    }

    private function _getRandomDestination(): Vector3
    {
        return $this->getPosition()->add(mt_rand(1, 3), mt_rand(1, 3), mt_rand(1, 3));
    }
}