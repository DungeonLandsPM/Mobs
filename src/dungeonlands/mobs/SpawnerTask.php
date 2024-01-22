<?php

declare(strict_types=1);

namespace dungeonlands\mobs;

use AllowDynamicProperties;
use pocketmine\entity\object\ExperienceOrb;
use pocketmine\scheduler\Task;

#[AllowDynamicProperties]
class SpawnerTask extends Task
{
    public function __construct(private MobsLoader $plugin)
    {
        $this->manager = new Manager($this->plugin);
    }

    public function onRun(): void
    {
        $this->manager->despawnMobs();

        if (mt_rand(1, 3) === 2){
            $this->clearXpOrbs();
        }

        $this->manager->spawnMobs();
    }

    private function clearXpOrbs(): void
    {
        $worldManager = $this->plugin->getServer()->getWorldManager();

        foreach ($this->plugin::WORLDS as $mobType => $worldName){
            $worldInstance = $worldManager->getWorldByName($worldName);
            if ($worldInstance?->isLoaded()){
                $entities = $worldInstance->getEntities();
                foreach ($entities as $entity){
                    if ($entity instanceof ExperienceOrb){
                        $entity->flagForDespawn();
                    }
                }
            }
        }
    }
}