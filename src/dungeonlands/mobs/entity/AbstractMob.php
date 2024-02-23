<?php

declare(strict_types=1);

namespace dungeonlands\mobs\entity;

use pocketmine\entity\EntitySizeInfo;
use pocketmine\entity\Living;
use pocketmine\nbt\tag\CompoundTag;

abstract class AbstractMob extends Living{
	protected static string $_typeID = "minecraft:npc";

	protected int $_health = 20;

	protected float $_speed = 0.1;

	protected bool $_canClimb = false;
	protected bool $_hasGravity = true;

	protected float $_sizeHeight = 2.5;
	protected float $_sizeWidth = 1.0;
	protected ?float $_eyeHeight = null;

	protected function initEntity(CompoundTag $nbt) : void{
		parent::initEntity($nbt);

		$this->setHealth($this->_health);
		$this->setMaxHealth($this->_health);

		$this->setMovementSpeed($this->_speed);

		$this->setCanClimb($this->_canClimb);
		$this->setCanClimbWalls($this->_canClimb);
		$this->setHasGravity($this->_hasGravity);

		$this->setCanSaveWithChunk(false);
	}

	public static function getNetworkTypeId() : string{
		return static::$_typeID;
	}

	public function getName() : string{
		return self::$_typeID;
	}

	protected function getInitialSizeInfo() : EntitySizeInfo{
		return new EntitySizeInfo($this->_sizeHeight, $this->_sizeWidth, $this->_eyeHeight);
	}
}