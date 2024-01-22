<?php

declare(strict_types=1);

namespace dungeonlands\mobs\entity\passive;

use dungeonlands\mobs\entity\AbstractMob;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;

class Allay extends AbstractMob
{
    protected static string $_typeID = EntityIds::ALLAY;
    protected string $_name = "Allay";


    protected float $_sizeHeight = 0.6;
    protected float $_sizeWidth = 0.6;

    protected int $_xp = 0;
}