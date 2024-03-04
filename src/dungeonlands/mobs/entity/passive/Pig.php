<?php

declare(strict_types=1);

namespace dungeonlands\mobs\entity\passive;

use dungeonlands\mobs\entity\AbstractMob;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\item\VanillaItems;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\player\Player;

class Pig extends AbstractMob{
	protected static string $_typeID = EntityIds::PIG;

	protected int $_health = 10;

	protected float $_speed = 0.25;

	protected float $_sizeHeight = 0.9;
	protected float $_sizeWidth = 0.9;

	public function getDrops() : array{
		$cause = $this->lastDamageCause;
		if($cause instanceof EntityDamageByEntityEvent){
			if($cause->getDamager() instanceof Player){
				if($this->isOnFire()){
					return [VanillaItems::COOKED_PORKCHOP()->setCount(mt_rand(1, 3))];
				}
				return [VanillaItems::RAW_PORKCHOP()->setCount(mt_rand(1, 3))];
			}
		}
		return [];
	}

	public function getXpDropAmount() : int{
		return mt_rand(1, 3);
	}
}