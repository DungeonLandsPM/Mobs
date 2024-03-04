<?php

declare(strict_types=1);

namespace dungeonlands\mobs\entity\hostile;

use dungeonlands\mobs\entity\AbstractMob;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\item\VanillaItems;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\player\Player;

class ElderGuardian extends AbstractMob{
	protected static string $_typeID = EntityIds::ELDER_GUARDIAN;

	protected int $_health = 80;

	protected float $_speed = 0.3;

	protected float $_sizeHeight = 1.9975;
	protected float $_sizeWidth = 1.9975;

	public function getDrops() : array{
		$cause = $this->lastDamageCause;
		if($cause instanceof EntityDamageByEntityEvent){
			if($cause->getDamager() instanceof Player){
				return [VanillaItems::PRISMARINE_SHARD()->setCount(mt_rand(0, 2)), VanillaItems::TIDE_ARMOR_TRIM_SMITHING_TEMPLATE()->setCount(mt_rand(0, 1))];
			}
		}
		return [];
	}

	public function getXpDropAmount() : int{
		return 10;
	}
}