<?php

declare(strict_types=1);

namespace dungeonlands\mobs\entity\neutral;

use dungeonlands\mobs\entity\AbstractMob;
use pocketmine\block\VanillaBlocks;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\item\VanillaItems;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\player\Player;

class IronGolem extends AbstractMob{
	protected static string $_typeID = EntityIds::IRON_GOLEM;

	protected int $_health = 100;

	protected float $_speed = 0.25;

	protected float $_sizeHeight = 2.9;
	protected float $_sizeWidth = 1.4;

	public function getDrops() : array{
		$cause = $this->lastDamageCause;
		if($cause instanceof EntityDamageByEntityEvent){
			if($cause->getDamager() instanceof Player){
				return [VanillaBlocks::POPPY()->asItem()->setCount(mt_rand(0, 2)), VanillaItems::IRON_INGOT()->setCount(mt_rand(3, 5))];
			}
		}
		return [];
	}
}