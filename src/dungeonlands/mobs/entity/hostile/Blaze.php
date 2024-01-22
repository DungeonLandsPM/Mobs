<?php

declare(strict_types=1);

namespace dungeonlands\mobs\entity\hostile;

use dungeonlands\mobs\entity\AbstractMob;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\item\VanillaItems;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\player\Player;

class Blaze extends AbstractMob
{
    protected static string $_typeID = EntityIds::BLAZE;
    protected string $_name = "Blaze";

    protected int $_health = 20;

    protected float $_speed = 0.23;

    protected bool $_canClimb = false;
    protected bool $_hasGravity = false;

    protected float $_sizeHeight = 1.8;
    protected float $_sizeWidth = 0.5;

    protected int $_xp = 10;

    public function getDrops(): array
    {
        $cause = $this->lastDamageCause;
        if ($cause instanceof EntityDamageByEntityEvent){
            $damager = $cause->getDamager();
            if ($damager instanceof Player){
                return [VanillaItems::BLAZE_ROD()->setCount(mt_rand(0, 1))];
            }
        }
    }
}