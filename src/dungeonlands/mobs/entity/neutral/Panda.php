<?php

declare(strict_types=1);

namespace dungeonlands\mobs\entity\neutral;

use dungeonlands\mobs\entity\AbstractMob;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\item\VanillaItems;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\player\Player;

class Panda extends AbstractMob{
	protected static string $_typeID = EntityIds::PANDA;

	protected int $_health = 20;

	protected float $_speed = 0.15;

	protected float $_sizeHeight = 1.25;
	protected float $_sizeWidth = 1.3;

	public function getDrops() : array{
		$cause = $this->lastDamageCause;
		if($cause instanceof EntityDamageByEntityEvent){
			if($cause->getDamager() instanceof Player){
				return [VanillaItems::BAMBOO()->setCount(mt_rand(0, 2))];
			}
		}
		return [];
	}

	public function getXpDropAmount() : int{
		return mt_rand(1, 3);
	}
}