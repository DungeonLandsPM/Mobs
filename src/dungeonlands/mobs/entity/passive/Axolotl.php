<?php

declare(strict_types=1);

namespace dungeonlands\mobs\entity\passive;

use dungeonlands\mobs\entity\AbstractMob;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;

class Axolotl extends AbstractMob
{
    protected static string $_typeID = EntityIds::AXOLOTL;
    protected string $_name = "AXOLOTL";

    protected int $_health = 14;

    protected float $_sizeHeight = 0.42;
    protected float $_sizeWidth = 0.75;

    protected int $_xp = 0;
}