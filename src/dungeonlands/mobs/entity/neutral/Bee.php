<?php

declare(strict_types=1);

namespace dungeonlands\mobs\entity\neutral;

use dungeonlands\mobs\entity\AbstractMob;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;

class Bee extends AbstractMob
{
    protected static string $_typeID = EntityIds::BEE;
    protected string $_name = "Bee";

    protected int $_health = 10;

    protected float $_speed = 0.1;

    protected bool $_canClimb = false;
    protected bool $_hasGravity = false;

    protected float $_sizeHeight = 0.5;
    protected float $_sizeWidth = 0.55;

    protected int $_xp = 2;
}