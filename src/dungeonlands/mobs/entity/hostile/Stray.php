<?php

declare(strict_types=1);

namespace dungeonlands\mobs\entity\hostile;

use dungeonlands\mobs\entity\AbstractMob;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\item\VanillaItems;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\player\Player;

class Stray extends AbstractMob{
	protected static string $_typeID = EntityIds::STRAY;

	protected int $_health = 20;

	protected float $_speed = 0.25;

	protected float $_sizeHeight = 1.9;
	protected float $_sizeWidth = 0.6;

	public function getDrops() : array{
		$cause = $this->lastDamageCause;
		if($cause instanceof EntityDamageByEntityEvent){
			if($cause->getDamager() instanceof Player){
				return [VanillaItems::ARROW()->setCount(mt_rand(0, 2)), VanillaItems::BONE()->setCount(mt_rand(0, 2))];
			}
		}
		return [];
	}

	public function getXpDropAmount() : int{
		return 5;
	}
}