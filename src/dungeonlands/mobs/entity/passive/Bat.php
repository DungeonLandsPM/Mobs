<?php

declare(strict_types=1);

namespace dungeonlands\mobs\entity\passive;

use dungeonlands\mobs\entity\AbstractMob;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;

class Bat extends AbstractMob
{
    protected static string $_typeID = EntityIds::BAT;

    protected int $_health = 6;

    protected float $_speed = 0.7;

    protected bool $_hasGravity = false;

    protected float $_sizeHeight = 0.9;
    protected float $_sizeWidth = 0.5;
}