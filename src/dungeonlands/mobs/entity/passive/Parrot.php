<?php

declare(strict_types=1);

namespace dungeonlands\mobs\entity\passive;

use dungeonlands\mobs\entity\AbstractMob;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\item\VanillaItems;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\player\Player;

class Parrot extends AbstractMob
{
    protected static string $_typeID = EntityIds::PARROT;

    protected int $_health = 6;

    protected float $_speed = 0.2;

    protected bool $_hasGravity = false;

    protected float $_sizeHeight = 1.0;
    protected float $_sizeWidth = 0.5;

    public function getDrops(): array
    {
        $cause = $this->lastDamageCause;
        if ($cause instanceof EntityDamageByEntityEvent) {
            $damager = $cause->getDamager();
            if ($damager instanceof Player) {
                return [VanillaItems::FEATHER()->setCount(mt_rand(1, 2))];
            }
        }
        return [];
    }

    public function getXpDropAmount(): int
    {
        return mt_rand(1, 3);
    }
}