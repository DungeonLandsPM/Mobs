<?php

declare(strict_types=1);

namespace dungeonlands\mobs\entity\neutral;

use dungeonlands\mobs\entity\AbstractMob;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;

class Goat extends AbstractMob
{
    protected static string $_typeID = EntityIds::GOAT;

    protected int $_health = 10;

    protected float $_speed = 0.2;

    protected float $_sizeHeight = 0.6;
    protected float $_sizeWidth = 0.9;

    public function getXpDropAmount(): int
    {
        return mt_rand(1, 3);
    }
}