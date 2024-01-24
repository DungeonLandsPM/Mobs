<?php

declare(strict_types=1);

namespace dungeonlands\mobs\entity\passive;

use dungeonlands\mobs\entity\AbstractMob;
use pocketmine\block\utils\DyeColor;
use pocketmine\block\VanillaBlocks;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\item\VanillaItems;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\player\Player;

class Sheep extends AbstractMob
{
    protected static string $_typeID = EntityIds::SHEEP;

    protected int $_health = 8;

    protected float $_speed = 0.23;

    protected float $_sizeHeight = 1.3;
    protected float $_sizeWidth = 0.9;

    public function getDrops(): array
    {
        $cause = $this->lastDamageCause;
        if ($cause instanceof EntityDamageByEntityEvent) {
            $damager = $cause->getDamager();
            if ($damager instanceof Player) {
                if ($this->isOnFire()) {
                    return [VanillaItems::COOKED_MUTTON()->setCount(mt_rand(1, 2)), VanillaBlocks::WOOL()->setColor(DyeColor::getAll()[array_rand(DyeColor::getAll())])];
                }
                return [VanillaItems::RAW_MUTTON()->setCount(mt_rand(1, 2)), VanillaBlocks::WOOL()->setColor(DyeColor::getAll()[array_rand(DyeColor::getAll())])];
            }
        }
        return [];
    }

    public function getXpDropAmount(): int
    {
        return mt_rand(1, 3);
    }
}