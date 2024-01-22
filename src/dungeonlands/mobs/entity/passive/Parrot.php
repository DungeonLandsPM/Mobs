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
    protected string $_name = "PARROT";

    protected int $_health = 6;

    protected float $_sizeHeight = 1.0;
    protected float $_sizeWidth = 0.5;

    protected int $_xp = 2;

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
}