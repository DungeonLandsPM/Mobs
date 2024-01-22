<?php

declare(strict_types=1);

namespace dungeonlands\mobs\entity\passive;

use dungeonlands\mobs\entity\AbstractMob;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;

class Bat extends AbstractMob
{
    protected static string $_typeID = EntityIds::BAT;
    protected string $_name = "BAT";

    protected int $_health = 6;

    protected float $_sizeHeight = 0.9;
    protected float $_sizeWidth = 0.5;

    protected int $_xp = 0;
}