<?php

declare(strict_types=1);

namespace dungeonlands\mobs\entity\hostile;

use dungeonlands\mobs\entity\AbstractMob;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\item\VanillaItems;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\player\Player;

class WitherSkeleton extends AbstractMob{
	protected static string $_typeID = EntityIds::WITHER_SKELETON;

	protected int $_health = 20;

	protected float $_speed = 0.23;

	protected float $_sizeHeight = 1.8;
	protected float $_sizeWidth = 0.5;

	public function getDrops() : array{
		$cause = $this->lastDamageCause;
		if($cause instanceof EntityDamageByEntityEvent){
			if($cause->getDamager() instanceof Player){
				return [VanillaItems::BLAZE_ROD()->setCount(mt_rand(0, 1))];
			}
		}
		return [];
	}

	public function getXpDropAmount() : int{
		return 10;
	}
}