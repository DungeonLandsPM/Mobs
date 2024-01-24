<?php

declare(strict_types=1);

namespace dungeonlands\mobs\entity\hostile;

use dungeonlands\mobs\entity\AbstractMob;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\item\VanillaItems;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\player\Player;

class Vindicator extends AbstractMob
{
    protected static string $_typeID = EntityIds::VINDICATOR;

    protected int $_health = 24;

    protected float $_speed = 0.35;

    protected float $_sizeHeight = 1.9;
    protected float $_sizeWidth = 0.6;

    public function getDrops(): array
    {
        $cause = $this->lastDamageCause;
        if ($cause instanceof EntityDamageByEntityEvent) {
            $damager = $cause->getDamager();
            if ($damager instanceof Player) {
                return [VanillaItems::EMERALD()->setCount(mt_rand(0, 1))];
            }
        }
        return [];
    }

    public function getXpDropAmount(): int
    {
        return 5;
    }
}