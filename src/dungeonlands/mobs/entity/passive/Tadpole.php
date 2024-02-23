<?php

declare(strict_types=1);

namespace dungeonlands\mobs\entity\passive;

use dungeonlands\mobs\entity\AbstractMob;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;

class Tadpole extends AbstractMob{
	protected static string $_typeID = EntityIds::TADPOLE;

	protected int $_health = 6;

	protected float $_sizeHeight = 0.6;
	protected float $_sizeWidth = 0.8;
}