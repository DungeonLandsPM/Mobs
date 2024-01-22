<?php

declare(strict_types=1);

namespace dungeonlands\mobs\entity\passive;

use dungeonlands\mobs\entity\AbstractMob;
use pocketmine\block\VanillaBlocks;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\item\VanillaItems;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\player\Player;

class Turtle extends AbstractMob
{
    protected static string $_typeID = EntityIds::TURTLE;
    protected string $_name = "TURTLE";

    protected int $_health = 30;

    protected float $_sizeHeight = 0.4;
    protected float $_sizeWidth = 1.2;

    public function getDrops(): array
    {
        $cause = $this->lastDamageCause;
        if ($cause instanceof EntityDamageByEntityEvent) {
            $damager = $cause->getDamager();
            if ($damager instanceof Player) {
                return [VanillaBlocks::SEA_LANTERN()->asItem()->setCount(mt_rand(0, 2)), VanillaItems::BONE()->setCount(1)];
            }
        }
        return [];
    }

    public function getXpDropAmount(): int
    {
        return mt_rand(1, 3);
    }
}