<?php

declare(strict_types=1);

namespace dungeonlands\mobs\entity\passive;

use dungeonlands\mobs\entity\AbstractMob;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;

class Frog extends AbstractMob{
	protected static string $_typeID = EntityIds::FROG;

	protected int $_health = 10;

	protected float $_speed = 1;

	protected float $_sizeHeight = 0.5;
	protected float $_sizeWidth = 0.5;

	public function getXpDropAmount() : int{
		return mt_rand(1, 3);
	}
}