<?php

declare(strict_types=1);

namespace dungeonlands\mobs\entity\hostile;

use dungeonlands\mobs\entity\AbstractMob;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\item\VanillaItems;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\player\Player;

class Ghast extends AbstractMob{
	protected static string $_typeID = EntityIds::GHAST;

	protected int $_health = 10;

	protected float $_speed = 0.7;

	protected float $_sizeHeight = 4.0;
	protected float $_sizeWidth = 4.0;

	public function getDrops() : array{
		$cause = $this->lastDamageCause;
		if($cause instanceof EntityDamageByEntityEvent){
			$damager = $cause->getDamager();
			if($damager instanceof Player){
				return [VanillaItems::GHAST_TEAR()->setCount(mt_rand(0, 1)), VanillaItems::GUNPOWDER()->setCount(mt_rand(0, 2))];
			}
		}
		return [];
	}

	public function getXpDropAmount() : int{
		return 5;
	}
}