<?php

declare(strict_types=1);

namespace dungeonlands\mobs\entity\passive;

use dungeonlands\mobs\entity\AbstractMob;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\item\VanillaItems;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\player\Player;

class Chicken extends AbstractMob
{
    protected static string $_typeID = EntityIds::CHICKEN;
    protected string $_name = "CHICKEN";

    protected int $_health = 4;

    protected float $_sizeHeight = 0.8;
    protected float $_sizeWidth = 0.6;

    protected int $_xp = 2;

    public function getDrops(): array
    {
        $cause = $this->lastDamageCause;
        if ($cause instanceof EntityDamageByEntityEvent) {
            $damager = $cause->getDamager();
            if ($damager instanceof Player) {
                if ($this->isOnFire()) {
                    return [VanillaItems::COOKED_CHICKEN(), VanillaItems::FEATHER()->setCount(mt_rand(0, 2))];
                }
                return [VanillaItems::RAW_CHICKEN(), VanillaItems::FEATHER()->setCount(mt_rand(0, 2))];
            }
        }
        return [];
    }
}