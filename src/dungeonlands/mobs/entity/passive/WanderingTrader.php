<?php

declare(strict_types=1);

namespace dungeonlands\mobs\entity\passive;

use dungeonlands\mobs\entity\AbstractMob;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;

class WanderingTrader extends AbstractMob{
	protected static string $_typeID = EntityIds::WANDERING_TRADER;

	protected float $_sizeHeight = 1.95;
	protected float $_sizeWidth = 0.6;
}