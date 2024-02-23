<?php

declare(strict_types=1);

namespace dungeonlands\mobs\entity\passive;

use dungeonlands\mobs\entity\AbstractMob;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;

class Villager extends AbstractMob{
	protected static string $_typeID = EntityIds::VILLAGER;

	protected float $_speed = 0.5;

	protected float $_sizeHeight = 1.9;
	protected float $_sizeWidth = 0.6;
}