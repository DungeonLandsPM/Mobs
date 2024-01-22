<?php

declare(strict_types=1);

namespace dungeonlands\mobs\entity\passive;

use dungeonlands\mobs\entity\AbstractMob;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\item\VanillaItems;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\player\Player;

class Horse extends AbstractMob
{
    protected static string $_typeID = EntityIds::HORSE;
    protected string $_name = "HORSE";

    protected int $_health = 22;

    protected float $_sizeHeight = 1.6;
    protected float $_sizeWidth = 1.4;

    protected int $_xp = 2;

    public function getDrops(): array
    {
        $cause = $this->lastDamageCause;
        if ($cause instanceof EntityDamageByEntityEvent) {
            $damager = $cause->getDamager();
            if ($damager instanceof Player) {
                return [VanillaItems::LEATHER()->setCount(mt_rand(0, 2))];
            }
        }
        return [];
    }
}