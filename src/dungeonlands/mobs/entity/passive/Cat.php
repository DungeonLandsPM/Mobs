<?php

declare(strict_types=1);

namespace dungeonlands\mobs\entity\passive;

use dungeonlands\mobs\entity\AbstractMob;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\item\VanillaItems;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\player\Player;

class Cat extends AbstractMob
{
    protected static string $_typeID = EntityIds::CAT;
    protected string $_name = "CAT";

    protected int $_health = 10;

    protected float $_sizeHeight = 0.56;
    protected float $_sizeWidth = 0.48;

    protected int $_xp = 2;

    public function getDrops(): array
    {
        $cause = $this->lastDamageCause;
        if ($cause instanceof EntityDamageByEntityEvent) {
            $damager = $cause->getDamager();
            if ($damager instanceof Player) {
                return [VanillaItems::STRING()->setCount(mt_rand(0, 2))];
            }
        }
        return [];
    }
}