<?php

declare(strict_types=1);

namespace dungeonlands\mobs\entity\hostile;

use dungeonlands\mobs\entity\AbstractMob;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\item\VanillaItems;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\player\Player;

class Guardian extends AbstractMob{
	protected static string $_typeID = EntityIds::GUARDIAN;

	protected int $_health = 30;

	protected float $_speed = 0.5;

	protected float $_sizeHeight = 0.85;
	protected float $_sizeWidth = 0.85;

	public function getDrops() : array{
		$cause = $this->lastDamageCause;
		if($cause instanceof EntityDamageByEntityEvent){
			if($cause->getDamager() instanceof Player){
				return [VanillaItems::PRISMARINE_SHARD()->setCount(mt_rand(0, 2))];
			}
		}
		return [];
	}

	public function getXpDropAmount() : int{
		return 10;
	}
}