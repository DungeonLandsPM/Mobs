<?php

declare(strict_types=1);

namespace dungeonlands\mobs\entity\hostile;

use dungeonlands\mobs\entity\AbstractMob;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;

class Ravager extends AbstractMob
{
    protected static string $_typeID = EntityIds::RAVAGER;

    protected int $_health = 100;

    protected float $_speed = 0.35;

    protected float $_sizeHeight = 2.2;
    protected float $_sizeWidth = 1.95;

    public function getXpDropAmount(): int
    {
        return 20;
    }
}