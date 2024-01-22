<?php

declare(strict_types=1);

namespace dungeonlands\mobs\entity\passive;

use dungeonlands\mobs\entity\AbstractMob;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;

class Sniffer extends AbstractMob
{
    protected static string $_typeID = EntityIds::SNIFFER;
    protected string $_name = "SNIFFER";

    protected int $_health = 14;

    protected float $_sizeHeight = 1.75;
    protected float $_sizeWidth = 1.9;

    public function getXpDropAmount(): int
    {
        return mt_rand(1, 3);
    }
}