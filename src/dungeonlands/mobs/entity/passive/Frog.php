<?php

declare(strict_types=1);

namespace dungeonlands\mobs\entity\passive;

use dungeonlands\mobs\entity\AbstractMob;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;

class Frog extends AbstractMob
{
    protected static string $_typeID = EntityIds::FROG;
    protected string $_name = "FROG";

    protected int $_health = 10;

    protected float $_sizeHeight = 0.5;
    protected float $_sizeWidth = 0.5;

    protected int $_xp = 2;
}