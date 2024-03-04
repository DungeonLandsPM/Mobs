<?php

declare(strict_types=1);

namespace dungeonlands\mobs\entity\neutral;

use dungeonlands\mobs\entity\AbstractMob;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\item\VanillaItems;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\player\Player;

class ZombiefiedPiglin extends AbstractMob{
	protected static string $_typeID = EntityIds::ZOMBIE_PIGMAN;

	protected int $_health = 20;

	protected float $_speed = 0.23;

	protected float $_sizeHeight = 1.9;
	protected float $_sizeWidth = 0.6;

	public function getDrops() : array{
		$cause = $this->lastDamageCause;
		if($cause instanceof EntityDamageByEntityEvent){
			if($cause->getDamager() instanceof Player){
				return [VanillaItems::ROTTEN_FLESH()->setCount(mt_rand(0, 1)), VanillaItems::GOLD_NUGGET()->setCount(mt_rand(0, 1)), VanillaItems::GOLD_INGOT()->setCount(mt_rand(0, 1))];
			}
		}
		return [];
	}

	public function getXpDropAmount() : int{
		return 5 + mt_rand(1, 3);
	}
}