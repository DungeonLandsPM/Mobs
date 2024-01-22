<?php

declare(strict_types=1);

namespace dungeonlands\mobs\entity\passive;

use dungeonlands\mobs\entity\AbstractMob;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\item\VanillaItems;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\player\Player;

class GlowSquid extends AbstractMob
{
    protected static string $_typeID = EntityIds::GLOW_SQUID;
    protected string $_name = "GLOW_SQUID";

    protected int $_health = 10;

    protected float $_sizeHeight = 0.95;
    protected float $_sizeWidth = 0.95;

    protected int $_xp = 2;

    public function getDrops(): array
    {
        $cause = $this->lastDamageCause;
        if ($cause instanceof EntityDamageByEntityEvent) {
            $damager = $cause->getDamager();
            if ($damager instanceof Player) {
                return [VanillaItems::GLOW_INK_SAC()->setCount(mt_rand(1, 3))];
            }
        }
        return [];
    }
}