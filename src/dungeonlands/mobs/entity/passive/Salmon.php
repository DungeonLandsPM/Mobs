<?php

declare(strict_types=1);

namespace dungeonlands\mobs\entity\passive;

use dungeonlands\mobs\entity\AbstractMob;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\item\VanillaItems;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\player\Player;

class Salmon extends AbstractMob
{
    protected static string $_typeID = EntityIds::SALMON;
    protected string $_name = "SALMON";

    protected int $_health = 3;

    protected float $_sizeHeight = 0.5;
    protected float $_sizeWidth = 0.5;

    protected int $_xp = 2;

    public function getDrops(): array
    {
        $cause = $this->lastDamageCause;
        if ($cause instanceof EntityDamageByEntityEvent) {
            $damager = $cause->getDamager();
            if ($damager instanceof Player) {
                if ($this->isOnFire()) {
                    return [VanillaItems::COOKED_SALMON(), VanillaItems::BONE()->setCount(mt_rand(0, 2))];
                }
                return [VanillaItems::RAW_SALMON(), VanillaItems::BONE()->setCount(mt_rand(0, 2))];
            }
        }
        return [];
    }
}