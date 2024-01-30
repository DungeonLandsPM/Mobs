<?php

declare(strict_types=1);

namespace dungeonlands\mobs;

use AllowDynamicProperties;
use pocketmine\entity\object\ExperienceOrb;
use pocketmine\entity\object\ItemEntity;
use pocketmine\scheduler\Task;

#[AllowDynamicProperties]
class SpawnerTask extends Task
{
    private int $_clearTicker = 0;

    public function __construct(private MobsLoader $plugin)
    {
        $this->manager = new Manager($this->plugin);
    }

    public function onRun(): void
    {
        $this->manager->despawnMobs();
        $this->manager->spawnMobs();

        if (++$this->_clearTicker === 100) {
            $this->_clearTicker = 0;
            $this->clearAll();
        }
    }

    private function clearAll(): void
    {
        $this->manager->despawnAllMobs();
        $this->clearLag();
    }

    private function clearLag(): void
    {
        $worldManager = $this->plugin->getServer()->getWorldManager();

        foreach ($this->plugin::WORLDS as $mobType => $worldName) {
            $world = $worldManager->getWorldByName($worldName);

            if ($world === null) {
                return;
            }

            if (!$world->isLoaded()) {
                return;
            }

            foreach ($world->getEntities() as $entity) {
                if ($entity instanceof ExperienceOrb or $entity instanceof ItemEntity) {
                    $entity->flagForDespawn();
                }
            }
        }
    }
}