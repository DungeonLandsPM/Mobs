<?php

declare(strict_types=1);

namespace dungeonlands\mobs\entity\passive;

use dungeonlands\mobs\entity\AbstractMob;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\item\VanillaItems;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\player\Player;

class SnowGolem extends AbstractMob{
	protected static string $_typeID = EntityIds::SNOW_GOLEM;

	protected int $_health = 4;

	protected float $_speed = 0.2;

	protected float $_sizeHeight = 1.8;
	protected float $_sizeWidth = 0.4;

	public function getDrops() : array{
		$cause = $this->lastDamageCause;
		if($cause instanceof EntityDamageByEntityEvent){
			if($cause->getDamager() instanceof Player){
				return [VanillaItems::SNOWBALL()->setCount(mt_rand(0, 15))];
			}
		}
		return [];
	}
}