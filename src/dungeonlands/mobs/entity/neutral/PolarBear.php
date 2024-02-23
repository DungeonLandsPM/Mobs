<?php

declare(strict_types=1);

namespace dungeonlands\mobs\entity\neutral;

use dungeonlands\mobs\entity\AbstractMob;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\item\VanillaItems;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\player\Player;

class PolarBear extends AbstractMob{
	protected static string $_typeID = EntityIds::POLAR_BEAR;

	protected int $_health = 30;

	protected float $_speed = 0.25;

	protected float $_sizeHeight = 1.4;
	protected float $_sizeWidth = 1.4;

	public function getDrops() : array{
		$cause = $this->lastDamageCause;
		if($cause instanceof EntityDamageByEntityEvent){
			$damager = $cause->getDamager();
			if($damager instanceof Player){
				if($this->isOnFire()){
					return [VanillaItems::COOKED_FISH()->setCount(mt_rand(0, 2)), VanillaItems::COOKED_SALMON()->setCount(mt_rand(0, 2))];
				}
				return [VanillaItems::RAW_FISH()->setCount(mt_rand(0, 2)), VanillaItems::RAW_SALMON()->setCount(mt_rand(0, 2))];
			}
		}
		return [];
	}

	public function getXpDropAmount() : int{
		return mt_rand(1, 3);
	}
}