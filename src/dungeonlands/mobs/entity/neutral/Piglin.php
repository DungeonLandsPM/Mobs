<?php

declare(strict_types=1);

namespace dungeonlands\mobs\entity\neutral;

use dungeonlands\mobs\entity\AbstractMob;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;

class Piglin extends AbstractMob
{
    protected static string $_typeID = EntityIds::PIGLIN;

    protected int $_health = 16;

    protected float $_speed = 0.35;

    protected float $_sizeHeight = 1.9;
    protected float $_sizeWidth = 0.6;

    public function getXpDropAmount(): int
    {
        return 5;
    }
}