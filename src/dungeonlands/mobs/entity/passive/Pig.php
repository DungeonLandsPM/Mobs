<?php

declare(strict_types=1);

namespace dungeonlands\mobs\entity\passive;

use dungeonlands\mobs\entity\AbstractMob;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\item\VanillaItems;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\player\Player;

class Pig extends AbstractMob
{
    protected static string $_typeID = EntityIds::PIG;
    protected string $_name = "PIG";

    protected int $_health = 10;

    protected float $_sizeHeight = 0.9;
    protected float $_sizeWidth = 0.9;

    protected int $_xp = 2;

    public function getDrops(): array
    {
        $cause = $this->lastDamageCause;
        if ($cause instanceof EntityDamageByEntityEvent) {
            $damager = $cause->getDamager();
            if ($damager instanceof Player) {
                if ($this->isOnFire()) {
                    return [VanillaItems::COOKED_PORKCHOP()->setCount(mt_rand(1, 3))];
                }
                return [VanillaItems::RAW_PORKCHOP()->setCount(mt_rand(1, 3))];
            }
        }
        return [];
    }
}