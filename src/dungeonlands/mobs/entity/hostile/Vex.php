<?php

declare(strict_types=1);

namespace dungeonlands\mobs\entity\hostile;

use dungeonlands\mobs\entity\AbstractMob;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;

class Vex extends AbstractMob
{
    protected static string $_typeID = EntityIds::VEX;

    protected int $_health = 14;

    protected float $_speed = 0.25;

    protected float $_sizeHeight = 0.8;
    protected float $_sizeWidth = 0.4;

    public function getXpDropAmount(): int
    {
        return 5;
    }
}