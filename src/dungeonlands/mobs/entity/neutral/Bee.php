<?php

declare(strict_types=1);

namespace dungeonlands\mobs\entity\neutral;

use dungeonlands\mobs\entity\AbstractMob;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;

class Bee extends AbstractMob{
	protected static string $_typeID = EntityIds::BEE;

	protected int $_health = 10;

	protected float $_speed = 0.3;

	protected float $_sizeHeight = 0.5;
	protected float $_sizeWidth = 0.55;

	public function getXpDropAmount() : int{
		return mt_rand(1, 3);
	}
}