<?php

declare(strict_types=1);

namespace dungeonlands\mobs\entity\passive;

use dungeonlands\mobs\entity\AbstractMob;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\item\VanillaItems;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\player\Player;

class Mooshroom extends AbstractMob{
	protected static string $_typeID = EntityIds::MOOSHROOM;

	protected int $_health = 10;

	protected float $_speed = 0.2;

	protected float $_sizeHeight = 1.3;
	protected float $_sizeWidth = 0.9;

	public function getDrops() : array{
		$cause = $this->lastDamageCause;
		if($cause instanceof EntityDamageByEntityEvent){
			if($cause->getDamager() instanceof Player){
				if($this->isOnFire()){
					return [VanillaItems::STEAK()->setCount(mt_rand(1, 3)), VanillaItems::LEATHER()->setCount(mt_rand(0, 2))];
				}
				return [VanillaItems::RAW_BEEF()->setCount(mt_rand(1, 3)), VanillaItems::LEATHER()->setCount(mt_rand(0, 2))];
			}
		}
		return [];
	}

	public function getXpDropAmount() : int{
		return mt_rand(1, 3);
	}
}