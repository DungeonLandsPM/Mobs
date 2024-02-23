<?php

declare(strict_types=1);

namespace dungeonlands\mobs\entity\hostile;

use dungeonlands\mobs\entity\AbstractMob;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\item\VanillaItems;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\player\Player;

class Phantom extends AbstractMob{
	protected static string $_typeID = EntityIds::PHANTOM;

	protected int $_health = 20;

	protected float $_speed = 0.7;

	protected float $_sizeHeight = 0.5;
	protected float $_sizeWidth = 0.9;

	public function getDrops() : array{
		$cause = $this->lastDamageCause;
		if($cause instanceof EntityDamageByEntityEvent){
			$damager = $cause->getDamager();
			if($damager instanceof Player){
				return [VanillaItems::PHANTOM_MEMBRANE()->setCount(mt_rand(0, 1))];
			}
		}
		return [];
	}

	public function getXpDropAmount() : int{
		return 5;
	}
}