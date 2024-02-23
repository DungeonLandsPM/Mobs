<?php

declare(strict_types=1);

namespace dungeonlands\mobs\entity\neutral;

use dungeonlands\mobs\entity\AbstractMob;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\item\VanillaItems;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\player\Player;

class Dolphin extends AbstractMob{
	protected static string $_typeID = EntityIds::DOLPHIN;

	protected int $_health = 10;

	protected float $_speed = 1.2;

	protected float $_sizeHeight = 0.6;
	protected float $_sizeWidth = 0.9;

	public function getDrops() : array{
		$cause = $this->lastDamageCause;
		if($cause instanceof EntityDamageByEntityEvent){
			$damager = $cause->getDamager();
			if($damager instanceof Player){
				if($this->isOnFire()){
					return [VanillaItems::COOKED_FISH()->setCount(mt_rand(0, 1))];
				}
				return [VanillaItems::RAW_FISH()->setCount(mt_rand(0, 1))];
			}
		}
		return [];
	}

	public function getXpDropAmount() : int{
		return mt_rand(1, 3);
	}
}