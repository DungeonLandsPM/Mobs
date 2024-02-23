<?php

declare(strict_types=1);

namespace dungeonlands\mobs\entity\hostile;

use dungeonlands\mobs\entity\AbstractMob;
use pocketmine\block\utils\MobHeadType;
use pocketmine\block\VanillaBlocks;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\item\VanillaItems;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\player\Player;

class Witch extends AbstractMob{
	protected static string $_typeID = EntityIds::WITCH;

	protected int $_health = 20;

	protected float $_speed = 0.25;

	protected float $_sizeHeight = 2.412;
	protected float $_sizeWidth = 0.864;

	public function getDrops() : array{
		$cause = $this->lastDamageCause;
		if($cause instanceof EntityDamageByEntityEvent){
			$damager = $cause->getDamager();
			if($damager instanceof Player){
				return [VanillaBlocks::MOB_HEAD()->setMobHeadType(MobHeadType::WITHER_SKELETON)->asItem()->setCount(mt_rand(0, 1)), VanillaItems::BONE()->setCount(mt_rand(0, 2)), VanillaItems::COAL()->setCount(mt_rand(0, 1))];
			}
		}
		return [];
	}

	public function getXpDropAmount() : int{
		return 5;
	}
}