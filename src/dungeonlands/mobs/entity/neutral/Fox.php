<?php

declare(strict_types=1);

namespace dungeonlands\mobs\entity\neutral;

use dungeonlands\mobs\entity\AbstractMob;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;

class Fox extends AbstractMob
{
    protected static string $_typeID = EntityIds::FOX;

    protected int $_health = 20;

    protected float $_speed = 0.3;

    protected float $_sizeHeight = 0.7;
    protected float $_sizeWidth = 0.6;

    public function getXpDropAmount(): int
    {
        return mt_rand(1, 3);
    }
}