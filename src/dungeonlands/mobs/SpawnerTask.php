<?php

declare(strict_types=1);

namespace dungeonlands\mobs;

use AllowDynamicProperties;
use pocketmine\scheduler\Task;

#[AllowDynamicProperties]
class SpawnerTask extends Task{
	public function __construct(private readonly MobsLoader $plugin){
		$this->manager = new Manager($this->plugin);
	}

	public function onRun() : void{
		$this->manager->despawnMobs();
		$this->manager->spawnMobs();
	}
}