<?php

declare(strict_types=1);

namespace dungeonlands\mobs\entity\hostile;

use dungeonlands\mobs\entity\AbstractMob;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;

class Warden extends AbstractMob{
	protected static string $_typeID = EntityIds::WARDEN;

	protected int $_health = 500;

	protected float $_speed = 0.3;

	protected float $_sizeHeight = 2.9;
	protected float $_sizeWidth = 0.9;

	public function getXpDropAmount() : int{
		return 5;
	}
}