<?php

declare(strict_types=1);

namespace dungeonlands\mobs\entity\neutral;

use dungeonlands\mobs\entity\AbstractMob;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\item\VanillaItems;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\player\Player;

class Enderman extends AbstractMob{
	protected static string $_typeID = EntityIds::ENDERMAN;

	protected int $_health = 40;

	protected float $_speed = 0.3;

	protected float $_sizeHeight = 2.9;
	protected float $_sizeWidth = 0.6;

	public function getDrops() : array{
		$cause = $this->lastDamageCause;
		if($cause instanceof EntityDamageByEntityEvent){
			if($cause->getDamager() instanceof Player){
				return [VanillaItems::ENDER_PEARL()->setCount(mt_rand(0, 1))];
			}
		}
		return [];
	}

	public function getXpDropAmount() : int{
		return 5;
	}
}