<?php

declare(strict_types=1);

namespace dungeonlands\mobs\entity\hostile;

use dungeonlands\mobs\entity\AbstractMob;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;

class PiglinBrute extends AbstractMob
{
    protected static string $_typeID = EntityIds::PIGLIN_BRUTE;

    protected int $_health = 50;

    protected float $_speed = 0.35;

    protected float $_sizeHeight = 1.9;
    protected float $_sizeWidth = 0.6;

    public function getXpDropAmount(): int
    {
        return 20;
    }
}