<?php

declare(strict_types=1);

namespace dungeonlands\mobs\entity\passive;

use dungeonlands\mobs\entity\AbstractMob;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;

class Camel extends AbstractMob
{
    protected static string $_typeID = EntityIds::CAMEL;
    protected string $_name = "CAMEL";

    protected int $_health = 32;

    protected float $_sizeHeight = 2.375;
    protected float $_sizeWidth = 1.7;

    protected int $_xp = 2;
}