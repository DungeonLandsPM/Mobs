<?php

declare(strict_types=1);

namespace dungeonlands\mobs\entity\hostile;

use dungeonlands\mobs\entity\AbstractMob;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\item\VanillaItems;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\player\Player;

class Hoglin extends AbstractMob{
	protected static string $_typeID = EntityIds::HOGLIN;

	protected int $_health = 40;

	protected float $_speed = 0.3;

	protected float $_sizeHeight = 1.4;
	protected float $_sizeWidth = 1.3965;

	public function getDrops() : array{
		$cause = $this->lastDamageCause;
		if($cause instanceof EntityDamageByEntityEvent){
			$damager = $cause->getDamager();
			if($damager instanceof Player){
				if($this->isOnFire()){
					return [VanillaItems::COOKED_PORKCHOP()->setCount(mt_rand(2, 4)), VanillaItems::LEATHER()->setCount(mt_rand(0, 1))];
				}
				return [VanillaItems::RAW_PORKCHOP()->setCount(mt_rand(2, 4)), VanillaItems::LEATHER()->setCount(mt_rand(0, 1))];
			}
		}
		return [];
	}

	public function getXpDropAmount() : int{
		return mt_rand(1, 3);
	}
}