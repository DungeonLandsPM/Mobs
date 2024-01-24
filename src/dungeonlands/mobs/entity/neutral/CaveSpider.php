<?php

declare(strict_types=1);

namespace dungeonlands\mobs\entity\neutral;

use dungeonlands\mobs\entity\AbstractMob;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\item\VanillaItems;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\player\Player;

class CaveSpider extends AbstractMob
{
    protected static string $_typeID = EntityIds::CAVE_SPIDER;

    protected int $_health = 12;

    protected float $_speed = 0.3;

    protected float $_sizeHeight = 0.5;
    protected float $_sizeWidth = 0.7;

    public function getDrops(): array
    {
        $cause = $this->lastDamageCause;
        if ($cause instanceof EntityDamageByEntityEvent) {
            $damager = $cause->getDamager();
            if ($damager instanceof Player) {
                return [VanillaItems::STRING()->setCount(mt_rand(0, 2)), VanillaItems::SPIDER_EYE()->setCount(mt_rand(0, 1))];
            }
        }
        return [];
    }

    public function getXpDropAmount(): int
    {
        return 5;
    }
}