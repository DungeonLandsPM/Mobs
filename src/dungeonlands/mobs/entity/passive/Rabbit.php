<?php

declare(strict_types=1);

namespace dungeonlands\mobs\entity\passive;

use dungeonlands\mobs\entity\AbstractMob;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\item\VanillaItems;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\player\Player;

class Rabbit extends AbstractMob{
	protected static string $_typeID = EntityIds::RABBIT;

	protected int $_health = 3;

	protected float $_speed = 0.3;

	protected float $_sizeHeight = 0.402;
	protected float $_sizeWidth = 0.402;

	public function getDrops() : array{
		$cause = $this->lastDamageCause;
		if($cause instanceof EntityDamageByEntityEvent){
			$damager = $cause->getDamager();
			if($damager instanceof Player){
				if($this->isOnFire()){
					return [VanillaItems::COOKED_RABBIT()->setCount(mt_rand(0, 1)), VanillaItems::RABBIT_FOOT()->setCount(mt_rand(0, 1)), VanillaItems::RABBIT_HIDE()->setCount(mt_rand(0, 1))];
				}
				return [VanillaItems::RAW_RABBIT()->setCount(mt_rand(0, 1)), VanillaItems::RABBIT_FOOT()->setCount(mt_rand(0, 1)), VanillaItems::RABBIT_HIDE()->setCount(mt_rand(0, 1))];
			}
		}
		return [];
	}

	public function getXpDropAmount() : int{
		return mt_rand(1, 3);
	}
}