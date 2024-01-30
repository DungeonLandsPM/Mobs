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
    }

    public static function getNetworkTypeId(): string
    {
        return static::$_typeID;
    }

    public function getName(): string
    {
        return self::$_typeID;
    }

    protected function getInitialSizeInfo(): EntitySizeInfo
    {
        return new EntitySizeInfo($this->_sizeHeight, $this->_sizeWidth);
    }

    //MORE
    protected function entityBaseTick(int $tickDiff = 1): bool
    {
        $this->_tick();
        return parent::entityBaseTick($tickDiff);
    }

    private function _tick(): void
    {
        if (mt_rand(0, 10) !== 5) {
            return;
        }

        $this->_move();
    }

    private function _move(): void
    {
        $location = $this->getLocation();

        if (mt_rand(0, 50) === 25) {
            $x = $location->x + mt_rand(-1, 1);
            $y = $location->y + mt_rand(-1, 1);
            $z = $location->z + mt_rand(-1, 1);
            $this->lookAt(new Vector3($x, $y, $z));
        }
    }
}