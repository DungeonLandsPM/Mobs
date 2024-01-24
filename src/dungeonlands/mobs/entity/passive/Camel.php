<?php

declare(strict_types=1);

namespace dungeonlands\mobs\entity\passive;

use dungeonlands\mobs\entity\AbstractMob;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;

class Camel extends AbstractMob
{
    protected static string $_typeID = EntityIds::CAMEL;

    protected int $_health = 32;

    protected float $_speed = 0.09;

    protected float $_sizeHeight = 2.375;
    protected float $_sizeWidth = 1.7;

    public function getXpDropAmount(): int
    {
        return mt_rand(1, 3);
    }
}