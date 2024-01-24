<?php

declare(strict_types=1);

namespace dungeonlands\mobs\entity\neutral;

use dungeonlands\mobs\entity\AbstractMob;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;

class Wolf extends AbstractMob
{
    protected static string $_typeID = EntityIds::WOLF;

    protected int $_health = 8;

    protected float $_speed = 0.3;

    protected float $_sizeHeight = 0.8;
    protected float $_sizeWidth = 0.6;

    public function getXpDropAmount(): int
    {
        return mt_rand(1, 3);
    }
}