<?php

declare(strict_types=1);

namespace dungeonlands\mobs\entity\hostile;

use dungeonlands\mobs\entity\AbstractMob;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\item\VanillaItems;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\player\Player;

class MagmaCube extends AbstractMob{
	protected static string $_typeID = EntityIds::MAGMA_CUBE;

	protected int $_health = 16;

	protected float $_speed = 0.4;

	protected float $_sizeHeight = 2.08;
	protected float $_sizeWidth = 2.08;

	public function getDrops() : array{
		$cause = $this->lastDamageCause;
		if($cause instanceof EntityDamageByEntityEvent){
			$damager = $cause->getDamager();
			if($damager instanceof Player){
				return [VanillaItems::MAGMA_CREAM()->setCount(mt_rand(0, 1))];
			}
		}
		return [];
	}

	public function getXpDropAmount() : int{
		return 4;
	}
}