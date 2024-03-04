<?php

declare(strict_types=1);

namespace dungeonlands\mobs\entity\hostile;

use dungeonlands\mobs\entity\AbstractMob;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\item\VanillaItems;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\player\Player;

class Shulker extends AbstractMob{
	protected static string $_typeID = EntityIds::SHULKER;

	protected int $_health = 30;

	protected float $_speed = 0.7;

	protected float $_sizeHeight = 1;
	protected float $_sizeWidth = 1;

	public function getDrops() : array{
		$cause = $this->lastDamageCause;
		if($cause instanceof EntityDamageByEntityEvent){
			if($cause->getDamager() instanceof Player){
				return [VanillaItems::SHULKER_SHELL()->setCount(mt_rand(0, 1))];
			}
		}
		return [];
	}

	public function getXpDropAmount() : int{
		return 5;
	}
}