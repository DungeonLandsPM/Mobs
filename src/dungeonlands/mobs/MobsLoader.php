<?php

declare(strict_types=1);

namespace dungeonlands\mobs;

use pocketmine\plugin\PluginBase;

/**
 * NOTE: IT IS STRONGLY NOT RECOMMENDED TO SPAWN THE MOBS IN MORE THAN 5 WORLDS.
 * AND NO, THIS IS MY PLUGIN - I KNOW MY CODE BETTER THAN ANYONE ELSE, AND I AM AWARE
 * THAT IT WILL CAUSE LAGS!
 *
 * THERE WILL NEVER BE SUPPORT FOR:
 * - SPAWNING MOBS VIA COMMAND
 * - SPAWNING MOBS IN ALL WORLDS
 * - UI OR CONFIGS FOR BETTER MANAGEMENT
 *
 * FEEL FREE TO FORK THE PLUGIN, BUT PLEASE DO NOT COPY EVERYTHING AND CLAIM IT AS YOUR OWN CODE. THANK YOU :)
 */
class MobsLoader extends PluginBase{
	public const WORLDS = [
		"overworld" => "overworld",
		"nether" => "nether",
		"the_end" => "the_end"
	];

	protected function onEnable() : void{
		$this->registerTask();
	}

	private function registerTask() : void{
		$this->getScheduler()->scheduleRepeatingTask(new SpawnerTask($this), 3500);
	}
}