<?php

declare(strict_types=1);

namespace dungeonlands\mobs\entity\passive;

use dungeonlands\mobs\entity\AbstractMob;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;

class Ocelot extends AbstractMob{
	protected static string $_typeID = EntityIds::OCELOT;

	protected int $_health = 10;

	protected float $_speed = 0.3;

	protected float $_sizeHeight = 0.7;
	protected float $_sizeWidth = 0.6;

	public function getXpDropAmount() : int{
		return mt_rand(1, 3);
	}
}