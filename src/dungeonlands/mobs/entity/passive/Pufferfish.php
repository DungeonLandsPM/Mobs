<?php

declare(strict_types=1);

namespace dungeonlands\mobs\entity\passive;

use dungeonlands\mobs\entity\AbstractMob;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\item\VanillaItems;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\player\Player;

class Pufferfish extends AbstractMob
{
    protected static string $_typeID = EntityIds::PUFFERFISH;
    protected string $_name = "PUFFERFISH";

    protected int $_health = 3;

    protected float $_sizeHeight = 0.96;
    protected float $_sizeWidth = 0.96;

    protected int $_xp = 2;

    public function getDrops(): array
    {
        $cause = $this->lastDamageCause;
        if ($cause instanceof EntityDamageByEntityEvent) {
            $damager = $cause->getDamager();
            if ($damager instanceof Player) {
                return [VanillaItems::PUFFERFISH(), VanillaItems::BONE()->setCount(mt_rand(1, 2))];
            }
        }
        return [];
    }
}