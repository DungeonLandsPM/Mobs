<?php

declare(strict_types=1);

namespace dungeonlands\mobs;

use AllowDynamicProperties;
use dungeonlands\mobs\entity\AbstractMob;
use dungeonlands\mobs\entity\hostile\Blaze;
use dungeonlands\mobs\entity\hostile\Creeper;
use dungeonlands\mobs\entity\hostile\ElderGuardian;
use dungeonlands\mobs\entity\hostile\Endermite;
use dungeonlands\mobs\entity\hostile\Evoker;
use dungeonlands\mobs\entity\hostile\Ghast;
use dungeonlands\mobs\entity\hostile\Guardian;
use dungeonlands\mobs\entity\hostile\Hoglin;
use dungeonlands\mobs\entity\hostile\Husk;
use dungeonlands\mobs\entity\hostile\MagmaCube;
use dungeonlands\mobs\entity\hostile\Phantom;
use dungeonlands\mobs\entity\hostile\PiglinBrute;
use dungeonlands\mobs\entity\hostile\Pillager;
use dungeonlands\mobs\entity\hostile\Ravager;
use dungeonlands\mobs\entity\hostile\Shulker;
use dungeonlands\mobs\entity\hostile\Silverfish;
use dungeonlands\mobs\entity\hostile\Skeleton;
use dungeonlands\mobs\entity\hostile\Slime;
use dungeonlands\mobs\entity\hostile\Stray;
use dungeonlands\mobs\entity\hostile\Vex;
use dungeonlands\mobs\entity\hostile\Vindicator;
use dungeonlands\mobs\entity\hostile\Warden;
use dungeonlands\mobs\entity\hostile\Witch;
use dungeonlands\mobs\entity\hostile\WitherSkeleton;
use dungeonlands\mobs\entity\hostile\Zoglin;
use dungeonlands\mobs\entity\hostile\Zombie;
use dungeonlands\mobs\entity\hostile\ZombieVillager;
use dungeonlands\mobs\entity\neutral\Bee;
use dungeonlands\mobs\entity\neutral\CaveSpider;
use dungeonlands\mobs\entity\neutral\Dolphin;
use dungeonlands\mobs\entity\neutral\Drowned;
use dungeonlands\mobs\entity\neutral\Enderman;
use dungeonlands\mobs\entity\neutral\Fox;
use dungeonlands\mobs\entity\neutral\Goat;
use dungeonlands\mobs\entity\neutral\IronGolem;
use dungeonlands\mobs\entity\neutral\Llama;
use dungeonlands\mobs\entity\neutral\Panda;
use dungeonlands\mobs\entity\neutral\Piglin;
use dungeonlands\mobs\entity\neutral\PolarBear;
use dungeonlands\mobs\entity\neutral\Spider;
use dungeonlands\mobs\entity\neutral\TraderLlama;
use dungeonlands\mobs\entity\neutral\Wolf;
use dungeonlands\mobs\entity\neutral\ZombiefiedPiglin;
use dungeonlands\mobs\entity\passive\Allay;
use dungeonlands\mobs\entity\passive\Axolotl;
use dungeonlands\mobs\entity\passive\Bat;
use dungeonlands\mobs\entity\passive\Camel;
use dungeonlands\mobs\entity\passive\Cat;
use dungeonlands\mobs\entity\passive\Chicken;
use dungeonlands\mobs\entity\passive\Cod;
use dungeonlands\mobs\entity\passive\Cow;
use dungeonlands\mobs\entity\passive\Donkey;
use dungeonlands\mobs\entity\passive\Frog;
use dungeonlands\mobs\entity\passive\GlowSquid;
use dungeonlands\mobs\entity\passive\Horse;
use dungeonlands\mobs\entity\passive\Mooshroom;
use dungeonlands\mobs\entity\passive\Mule;
use dungeonlands\mobs\entity\passive\Ocelot;
use dungeonlands\mobs\entity\passive\Parrot;
use dungeonlands\mobs\entity\passive\Pig;
use dungeonlands\mobs\entity\passive\Pufferfish;
use dungeonlands\mobs\entity\passive\Rabbit;
use dungeonlands\mobs\entity\passive\Salmon;
use dungeonlands\mobs\entity\passive\Sheep;
use dungeonlands\mobs\entity\passive\SkeletonHorse;
use dungeonlands\mobs\entity\passive\Sniffer;
use dungeonlands\mobs\entity\passive\SnowGolem;
use dungeonlands\mobs\entity\passive\Squid;
use dungeonlands\mobs\entity\passive\Strider;
use dungeonlands\mobs\entity\passive\Tadpole;
use dungeonlands\mobs\entity\passive\TropicalFish;
use dungeonlands\mobs\entity\passive\Turtle;
use dungeonlands\mobs\entity\passive\Villager;
use dungeonlands\mobs\entity\passive\WanderingTrader;
use pocketmine\block\BlockTypeIds;
use pocketmine\data\bedrock\BiomeIds;
use pocketmine\entity\Location;
use pocketmine\world\Position;
use pocketmine\world\World;

#[AllowDynamicProperties] class Manager{
	public function __construct(private readonly MobsLoader $plugin){
		$this->worldManager = $this->plugin->getServer()->getWorldManager();
	}

	/**
	 * ANIMALS SPAWNS UPON CHUNK GENERATION/LOADING, BUT THERE IS CURRENTLY NO WAY FOR THIS IN POCKETMINE
	 * MONSTER SPAWNS IN A CERTAIN RADIUS AROUND THE PLAYER
	 * MOBS CAN NOT SPAWN ON TRANSPARENT BLOCKS, IN WATER (EXPECT AQUATIC CREATURES), IN LAVA (EXPECT STRIDER), ON BEDROCK, ON LESS THAN A FULL BLOCK TALL (SUCH AS SLABS)
	 */
	public function spawnMobs() : void{
		$amountPerPlayer = 5 * 2; //*2 for the attempts

		foreach($this->plugin::WORLDS as $worldType => $worldName){
			$world = $this->worldManager->getWorldByName($worldName);

			if($world === null){
				break;
			}

			$positions = [];
			foreach($world->getPlayers() as $player){
				for($i = 0; $i < $amountPerPlayer; $i++){
					$pos = $this->findSpawn($player->getPosition());

					if($pos !== null){
						$positions[] = $pos;
					}
				}
			}

			foreach($positions as $position){
				$mobTable = $this->getMobsForBiome($worldName, $world->getBiomeId($position->getFloorX(), $position->getFloorY(), $position->getFloorZ()), $this->isNight($world));

				$mob = $mobTable[array_rand($mobTable)];

				if($this->isAquatic($mob) and $this->isSafeForAquaMobs($position)){
					$this->spawn($mob, $position);
					break;
				}

				if($this->isSafeForMobs($mob)){
					$this->spawn($mob, $position);
					break;
				}
			}
		}
	}

	private function findSpawn(Position $startPos) : ?Position{
		//In vanilla, it depends on the world simulations distance, we will use the default
		$radius = 50;

		$y_difference = 3;

		$world = $startPos->getWorld();

		$randomX = (int) ($startPos->x + mt_rand(-$radius, $radius));
		$randomY = (int) ($startPos->y + mt_rand(-$y_difference, $y_difference));
		$randomZ = (int) ($startPos->z + mt_rand(-$radius, $radius));

		return new Position($randomX, $randomY, $randomZ, $world);
	}

	private function isSafeForMobs(Position $position) : bool{
		$x = $position->getFloorX();
		$y = $position->getFloorY();
		$z = $position->getFloorZ();

		$blockUp = $position->getWorld()->getBlockAt($x, $y + 1, $z);
		$block = $position->getWorld()->getBlockAt($x, $y, $z);
		$blockDown = $position->getWorld()->getBlockAt($x, $y - 1, $z);

		return $blockDown->isSolid() and $block->getTypeId() === BlockTypeIds::AIR and $blockUp->getTypeId() === BlockTypeIds::AIR;
	}

	private function isSafeForAquaMobs(Position $position) : bool{
		$x = $position->getFloorX();
		$y = $position->getFloorY();
		$z = $position->getFloorZ();

		$blockUp = $position->getWorld()->getBlockAt($x, $y + 1, $z);
		$block = $position->getWorld()->getBlockAt($x, $y, $z);
		$blockDown = $position->getWorld()->getBlockAt($x, $y - 1, $z);

		return $blockDown->getTypeId() === BlockTypeIds::WATER and $block->getTypeId() === BlockTypeIds::WATER and $blockUp->getTypeId() === BlockTypeIds::WATER;
	}

	public function despawnMobs() : void{
		foreach($this->plugin::WORLDS as $worldType => $worldName){
			$world = $this->worldManager->getWorldByName($worldName);

			if($world === null){
				return;
			}

			foreach($world->getEntities() as $entity){
				if($entity instanceof AbstractMob){
					$near = false;

					foreach($world->getPlayers() as $player){
						foreach($player->getWorld()->getNearbyEntities($player->getBoundingBox()->expandedCopy(100, 100, 100)) as $e){
							if($e->getId() === $entity->getId()){
								$near = true;
							}
						}
					}

					if(!$near){
						$entity->flagForDespawn();
					}
				}
			}
		}
	}

	public function spawn(string $mobName, Position $position) : void{
		$entityClass = $this->getClassFor($mobName);

		if($entityClass === null){
			$this->plugin->getLogger()->warning("§cERROR: {$mobName} class not found!");
			return;
		}

		(new $entityClass(new Location($position->x, $position->y, $position->z, $position->getWorld(), 0, 0)))->spawnToAll();
	}

	private function getMobsForBiome(string $worldName, int $biomeID, bool $isNight) : array{
		if($worldName === $this->plugin::WORLDS["overworld"] and $isNight){
			if(array_key_exists($biomeID, $this->getMobs())){
				return $this->getNightlyMobs()[$biomeID];
			}
		}

		if(array_key_exists($biomeID, $this->getMobs())){
			return $this->getMobs()[$biomeID];
		}

		return match ($worldName) {
			$this->plugin::WORLDS["nether"] => $this->getMobs()[BiomeIds::HELL],
			$this->plugin::WORLDS["the_end"] => $this->getMobs()[BiomeIds::THE_END],
			default => $this->getMobs()[BiomeIds::PLAINS]
		};
	}

	private function getMobs() : array{
		return [
			##OVERWORLD
			#OCEAN
			BiomeIds::OCEAN => ["GlowSquid", "Squid", "Dolphin", "Cod", "Salmon"],
			BiomeIds::DEEP_OCEAN => ["GlowSquid", "Squid", "Dolphin", "Cod", "Salmon"],
			BiomeIds::WARM_OCEAN => ["GlowSquid", "Squid", "Dolphin", "TropicalFish", "Pufferfish", "TropicalFish"],
			BiomeIds::LUKEWARM_OCEAN => ["GlowSquid", "Squid", "Dolphin", "Cod", "TropicalFish", "Salmon", "TropicalFish"],
			BiomeIds::DEEP_LUKEWARM_OCEAN => ["GlowSquid", "Squid", "Dolphin", "Cod", "TropicalFish", "Salmon", "TropicalFish"],
			BiomeIds::COLD_OCEAN => ["GlowSquid", "Squid", "Dolphin", "Cod", "Salmon"],
			BiomeIds::DEEP_COLD_OCEAN => ["GlowSquid", "Squid", "Dolphin", "Cod", "Salmon"],
			BiomeIds::FROZEN_OCEAN => ["GlowSquid", "Squid", "PolarBear", "Rabbit", "Cod", "Salmon"],
			BiomeIds::DEEP_FROZEN_OCEAN => ["GlowSquid", "Squid", "PolarBear", "Rabbit", "Cod", "Salmon"],

			BiomeIds::MUSHROOM_ISLAND => ["GlowSquid", "Mooshroom"],

			##HIGHLAND BIOMES
			182 => ["Goat"],
			183 => ["Goat"],
			189 => [],
			186 => ["GlowSquid", "Rabbit", "Sheep", "Donkey"],
			192 => ["GlowSquid", "Pig", "Rabbit", "Sheep"],
			185 => ["GlowSquid", "Fox", "Rabbit", "Wolf"],
			184 => ["GlowSquid", "Rabbit", "Goat", "PolarBear"],
			BiomeIds::EXTREME_HILLS => ["Sheep", "Chicken", "GlowSquid", "Pig", "Cow", "Llama", "Bat"],
			BiomeIds::EXTREME_HILLS_MUTATED => ["Sheep", "Chicken", "GlowSquid", "Pig", "Cow", "Llama", "Bat"],
			BiomeIds::EXTREME_HILLS_PLUS_TREES => ["Sheep", "Chicken", "GlowSquid", "Pig", "Cow", "Llama", "Bat"],

			##WOODLAND BIOMES
			BiomeIds::FOREST => ["Sheep", "Chicken", "GlowSquid", "Pig", "Cow", "Wolf", "Bat"],
			BiomeIds::FLOWER_FOREST => ["GlowSquid", "Rabbit"],
			BiomeIds::TAIGA => ["Wolf", "Sheep", "Chicken", "GlowSquid", "Pig", "Cow", "Fox", "Rabbit", "Bat"],
			BiomeIds::MEGA_TAIGA => ["Wolf", "Sheep", "Chicken", "GlowSquid", "Pig", "Cow", "Fox", "Bat"],
			BiomeIds::REDWOOD_TAIGA_MUTATED => ["Wolf", "Sheep", "Chicken", "GlowSquid", "Pig", "Cow", "Fox", "Bat"],
			BiomeIds::COLD_TAIGA => ["Wolf", "Sheep", "Chicken", "GlowSquid", "Pig", "Cow", "Fox", "Rabbit", "Bat"],
			BiomeIds::BIRCH_FOREST => ["Sheep", "Chicken", "GlowSquid", "Pig", "Cow", "Bat"],
			BiomeIds::BIRCH_FOREST_MUTATED => ["Sheep", "Chicken", "GlowSquid", "Pig", "Cow", "Bat"],
			BiomeIds::ROOFED_FOREST => ["Sheep", "Chicken", "GlowSquid", "Pig", "Cow", "Bat"],
			BiomeIds::JUNGLE => ["Parrot", "Ocelot", "Sheep", "Chicken", "GlowSquid", "Panda", "Pig", "Cow", "Bat"],
			BiomeIds::JUNGLE_EDGE => ["Parrot", "Ocelot", "Sheep", "Chicken", "GlowSquid", "Panda", "Pig", "Cow", "Bat"],
			BiomeIds::BAMBOO_JUNGLE => ["Panda", "Parrot", "Ocelot", "Sheep", "Chicken", "GlowSquid", "Pig", "Cow", "Bat"],

			##WETLAND BIOMES
			BiomeIds::RIVER => ["GlowSquid", "Squid", "Salmon"],
			BiomeIds::FROZEN_RIVER => ["GlowSquid", "Squid", "Rabbit", "PolarBear", "Salmon"],
			BiomeIds::SWAMPLAND => ["Sheep", "Chicken", "Frog", "GlowSquid", "Pig", "Cow", "Bat"],
			191 => ["Frog", "GlowSquid", "TropicalFish"],
			BiomeIds::BEACH => ["GlowSquid", "Turtle"],
			BiomeIds::COLD_BEACH => ["GlowSquid", "Rabbit"],
			BiomeIds::STONE_BEACH => ["GlowSquid"],

			##FLATLAND BIOMES
			BiomeIds::PLAINS => ["Sheep", "Chicken", "GlowSquid", "Pig", "Cow", "Horse", "Donkey"],
			BiomeIds::SUNFLOWER_PLAINS => ["Sheep", "Chicken", "GlowSquid", "Pig", "Cow", "Horse", "Donkey"],
			BiomeIds::ICE_PLAINS => ["GlowSquid", "Rabbit", "PolarBear"],
			BiomeIds::ICE_PLAINS_SPIKES => ["GlowSquid", "Rabbit", "PolarBear"],

			##ARID-LANDS BIOMES
			BiomeIds::DESERT => ["GlowSquid", "Rabbit"],
			BiomeIds::SAVANNA => ["Armadillo", "Sheep", "Chicken", "GlowSquid", "Pig", "Cow", "Llama", "Horse", "Bat"],
			BiomeIds::SAVANNA_PLATEAU => ["Armadillo", "Sheep", "Chicken", "GlowSquid", "Pig", "Cow", "Llama", "Horse", "Bat"],
			BiomeIds::SAVANNA_MUTATED => ["Armadillo", "Sheep", "Chicken", "GlowSquid", "Pig", "Cow", "Llama", "Horse", "Bat"],
			BiomeIds::MESA => ["Armadillo", "GlowSquid"],
			BiomeIds::MESA_BRYCE => ["Armadillo", "GlowSquid"],
			BiomeIds::MESA_PLATEAU_STONE => ["Armadillo", "GlowSquid"],

			##CAVE BIOMES
			190 => ["Warden"],
			188 => ["GlowSquid"],
			187 => ["GlowSquid", "TropicalFish", "TropicalFish", "Axolotl"],

			##THE NETHER
			BiomeIds::HELL => ["ZombifiedPiglin", "Ghast", "Piglin", "MagmaCube", "Enderman", "Strider"],
			BiomeIds::SOULSAND_VALLEY => ["Skeleton", "Ghast", "Enderman", "Strider"],
			BiomeIds::CRIMSON_FOREST => ["ZombifiedPiglin", "Piglin", "Strider", "Hoglin"],
			BiomeIds::WARPED_FOREST => ["Enderman", "Strider"],
			BiomeIds::BASALT_DELTAS => ["MagmaCube", "Ghast", "Strider"],

			##THE END
			BiomeIds::THE_END => ["Enderman"],
		];
	}

	private function getNightlyMobs() : array{
		return [
			##OVERWORLD
			#OCEAN
			BiomeIds::OCEAN => ["Creeper", "Drowned", "Slime", "Spider", "Zombie", "Skeleton", "Enderman", "Witch", "ZombieVillager"],
			BiomeIds::DEEP_OCEAN => ["Creeper", "Drowned", "Slime", "Spider", "Zombie", "Skeleton", "Enderman", "Witch", "ZombieVillager"],
			BiomeIds::WARM_OCEAN => ["Creeper", "Drowned", "Slime", "Spider", "Zombie", "Skeleton", "Enderman", "Witch", "ZombieVillager"],
			BiomeIds::LUKEWARM_OCEAN => ["Creeper", "Drowned", "Slime", "Spider", "Zombie", "Skeleton", "Enderman", "Witch", "ZombieVillager"],
			BiomeIds::DEEP_LUKEWARM_OCEAN => ["Creeper", "Drowned", "Slime", "Spider", "Zombie", "Skeleton", "Enderman", "Witch", "ZombieVillager"],
			BiomeIds::COLD_OCEAN => ["Creeper", "Drowned", "Slime", "Spider", "Zombie", "Skeleton", "Enderman", "Witch", "ZombieVillager"],
			BiomeIds::DEEP_COLD_OCEAN => ["Creeper", "Drowned", "Slime", "Spider", "Zombie", "Skeleton", "Enderman", "Witch", "ZombieVillager"],
			BiomeIds::FROZEN_OCEAN => ["Creeper", "Drowned", "Slime", "Spider", "Stray", "Zombie", "Skeleton", "Enderman", "Witch", "ZombieVillager"],
			BiomeIds::DEEP_FROZEN_OCEAN => ["Creeper", "Drowned", "Slime", "Spider", "Stray", "Zombie", "Skeleton", "Enderman", "Witch", "ZombieVillager"],

			BiomeIds::MUSHROOM_ISLAND => [],

			##HIGHLAND BIOMES
			182 => ["Creeper", "Slime", "Spider", "Stray", "Zombie", "Skeleton", "Enderman", "Witch", "ZombieVillager"],
			183 => ["Creeper", "Slime", "Spider", "Stray", "Zombie", "Skeleton", "Enderman", "Witch", "ZombieVillager"],
			189 => ["Creeper", "Slime", "Spider", "Zombie", "Skeleton", "Enderman", "Witch", "ZombieVillager"],
			186 => ["Creeper", "Slime", "Spider", "Zombie", "Skeleton", "Enderman", "Witch", "ZombieVillager"],
			192 => ["Creeper", "Slime", "Spider", "Zombie", "Skeleton", "Enderman", "Witch", "ZombieVillager"],
			185 => ["Creeper", "Slime", "Spider", "Zombie", "Skeleton", "Enderman", "Witch", "ZombieVillager"],
			184 => ["Creeper", "Slime", "Spider", "Stray", "Zombie", "Skeleton", "Enderman", "Witch", "ZombieVillager"],
			BiomeIds::EXTREME_HILLS => ["Creeper", "Slime", "Spider", "Zombie", "Skeleton", "Enderman", "Witch", "ZombieVillager"],
			BiomeIds::EXTREME_HILLS_MUTATED => ["Creeper", "Slime", "Spider", "Zombie", "Skeleton", "Enderman", "Witch", "ZombieVillager"],
			BiomeIds::EXTREME_HILLS_PLUS_TREES => ["Creeper", "Slime", "Spider", "Zombie", "Skeleton", "Enderman", "Witch", "ZombieVillager"],

			##WOODLAND BIOMES
			BiomeIds::FOREST => ["Creeper", "Slime", "Spider", "Zombie", "Skeleton", "Enderman", "Witch", "ZombieVillager"],
			BiomeIds::FLOWER_FOREST => ["Creeper", "Slime", "Spider", "Zombie", "Skeleton", "Enderman", "Witch", "ZombieVillager"],
			BiomeIds::TAIGA => ["Creeper", "Slime", "Spider", "Zombie", "Skeleton", "Enderman", "Witch", "ZombieVillager"],
			BiomeIds::MEGA_TAIGA => ["Creeper", "Slime", "Spider", "Zombie", "Skeleton", "Enderman", "Witch", "ZombieVillager"],
			BiomeIds::REDWOOD_TAIGA_MUTATED => ["Creeper", "Slime", "Spider", "Zombie", "Skeleton", "Enderman", "Witch", "ZombieVillager"],
			BiomeIds::COLD_TAIGA => ["Creeper", "Slime", "Spider", "Zombie", "Skeleton", "Enderman", "Witch", "ZombieVillager"],
			BiomeIds::BIRCH_FOREST => ["Creeper", "Slime", "Spider", "Zombie", "Skeleton", "Enderman", "Witch", "ZombieVillager"],
			BiomeIds::BIRCH_FOREST_MUTATED => ["Creeper", "Slime", "Spider", "Zombie", "Skeleton", "Enderman", "Witch", "ZombieVillager"],
			BiomeIds::ROOFED_FOREST => ["Creeper", "Slime", "Spider", "Zombie", "Skeleton", "Enderman", "Witch", "ZombieVillager"],
			BiomeIds::JUNGLE => ["Creeper", "Slime", "Spider", "Zombie", "Skeleton", "Enderman", "Witch", "ZombieVillager"],
			BiomeIds::JUNGLE_EDGE => ["Creeper", "Slime", "Spider", "Zombie", "Skeleton", "Enderman", "Witch", "ZombieVillager"],
			BiomeIds::BAMBOO_JUNGLE => ["Creeper", "Slime", "Spider", "Zombie", "Skeleton", "Enderman", "Witch", "ZombieVillager"],

			##WETLAND BIOMES
			BiomeIds::RIVER => ["Drowned"],
			BiomeIds::FROZEN_RIVER => ["Slime", "Stray", "Skeleton", "Drowned"],
			BiomeIds::SWAMPLAND => ["Creeper", "Slime", "Spider", "Zombie", "Skeleton", "Bogged", "Enderman", "Witch", "ZombieVillager"],
			191 => ["Bogged", "Slime"],
			BiomeIds::BEACH => ["Creeper", "Slime", "Spider", "Zombie", "Skeleton", "Enderman", "Witch", "ZombieVillager"],
			BiomeIds::COLD_BEACH => ["Creeper", "Slime", "Spider", "Zombie", "Skeleton", "Enderman", "Witch", "ZombieVillager"],
			BiomeIds::STONE_BEACH => ["Creeper", "Slime", "Spider", "Zombie", "Skeleton", "Enderman", "Witch", "ZombieVillager"],

			##FLATLAND BIOMES
			BiomeIds::PLAINS => ["Creeper", "Slime", "Spider", "Zombie", "Skeleton", "Enderman", "Witch", "ZombieVillager"],
			BiomeIds::SUNFLOWER_PLAINS => ["Creeper", "Slime", "Spider", "Zombie", "Skeleton", "Enderman", "Witch", "ZombieVillager"],
			BiomeIds::ICE_PLAINS => ["Slime", "Stray", "Skeleton"],
			BiomeIds::ICE_PLAINS_SPIKES => ["Creeper", "Slime", "Spider", "Stray", "Zombie", "Skeleton", "Enderman", "Witch", "ZombieVillager"],

			##ARID-LANDS BIOMES
			BiomeIds::DESERT => ["Husk", "Creeper", "Slime", "Spider", "Zombie", "Skeleton", "Enderman", "Witch", "ZombieVillager"],
			BiomeIds::SAVANNA => ["Creeper", "Slime", "Spider", "Zombie", "Skeleton", "Enderman", "Witch", "ZombieVillager"],
			BiomeIds::SAVANNA_PLATEAU => ["Creeper", "Slime", "Spider", "Zombie", "Skeleton", "Enderman", "Witch", "ZombieVillager"],
			BiomeIds::SAVANNA_MUTATED => ["Creeper", "Slime", "Spider", "Zombie", "Skeleton", "Enderman", "Witch", "ZombieVillager"],
			BiomeIds::MESA => ["Creeper", "Skeleton", "Slime", "Spider", "Zombie", "Enderman", "Witch", "ZombieVillager"],
			BiomeIds::MESA_BRYCE => ["Creeper", "Skeleton", "Slime", "Spider", "Zombie", "Enderman", "Witch", "ZombieVillager"],
			BiomeIds::MESA_PLATEAU_STONE => ["Creeper", "Slime", "Spider", "Zombie", "Skeleton", "Enderman", "Witch", "ZombieVillager"],

			##CAVE BIOMES
			190 => [],
			188 => ["Creeper", "Drowned", "Slime", "Spider", "Zombie", "Skeleton", "Enderman", "Witch", "ZombieVillager"],
			187 => ["Creeper", "Slime", "Spider", "Zombie", "Skeleton", "Enderman", "Witch", "ZombieVillager"],

			##THE NETHER
			BiomeIds::HELL => ["ZombifiedPiglin", "Ghast", "Piglin", "MagmaCube", "Enderman", "Strider"],
			BiomeIds::SOULSAND_VALLEY => ["Skeleton", "Ghast", "Enderman", "Strider"],
			BiomeIds::CRIMSON_FOREST => ["ZombifiedPiglin", "Piglin", "Strider", "Hoglin"],
			BiomeIds::WARPED_FOREST => ["Enderman", "Strider"],
			BiomeIds::BASALT_DELTAS => ["MagmaCube", "Ghast", "Strider"],

			##THE END
			BiomeIds::THE_END => ["Enderman"],
		];
	}

	private function isNight(World $world) : bool{
		return $world->getTime() >= World::TIME_NIGHT;
	}

	private function isAquatic(string $mobName) : bool{
		return in_array($mobName, ["Axolotl", "Cod", "Dolphin", "GlowSquid", "Pufferfish", "Salmon", "Squid", "Tadpole", "TropicalFish", "Turtle", "ElderGuardian", "Guardian"]);
	}

	private function getClassFor(string $name) : ?string{
		return match ($name) {
			#HOSTILE
			"Blaze" => Blaze::class,
			"Creeper" => Creeper::class,
			"ElderGuardian" => ElderGuardian::class,
			"Endermite" => Endermite::class,
			"Evoker" => Evoker::class,
			"Ghast" => Ghast::class,
			"Guardian" => Guardian::class,
			"Hoglin" => Hoglin::class,
			"Husk" => Husk::class,
			"MagmaCube" => MagmaCube::class,
			"Phantom" => Phantom::class,
			"PiglinBrute" => PiglinBrute::class,
			"Pillager" => Pillager::class,
			"Ravager" => Ravager::class,
			"Shulker" => Shulker::class,
			"Silverfish" => Silverfish::class,
			"Skeleton" => Skeleton::class,
			"Slime" => Slime::class,
			"Stray" => Stray::class,
			"Vex" => Vex::class,
			"Vindicator" => Vindicator::class,
			"Warden" => Warden::class,
			"Witch" => Witch::class,
			"WitherSkeleton" => WitherSkeleton::class,
			"Zoglin" => Zoglin::class,
			"Zombie" => Zombie::class,
			"ZombieVillager" => ZombieVillager::class,

			#NEUTRAL
			"Bee" => Bee::class,
			"CaveSpider" => CaveSpider::class,
			"Dolphin" => Dolphin::class,
			"Drowned" => Drowned::class,
			"Enderman" => Enderman::class,
			"Fox" => Fox::class,
			"Goat" => Goat::class,
			"IronGolem" => IronGolem::class,
			"Llama" => Llama::class,
			"Panda" => Panda::class,
			"Piglin" => Piglin::class,
			"PolarBear" => PolarBear::class,
			"Spider" => Spider::class,
			"TraderLlama" => TraderLlama::class,
			"Wolf" => Wolf::class,
			"ZombiefiedPiglin" => ZombiefiedPiglin::class,

			#PASSIVE
			"Allay" => Allay::class,
			"Axolotl" => Axolotl::class,
			"Bat" => Bat::class,
			"Camel" => Camel::class,
			"Cat" => Cat::class,
			"Chicken" => Chicken::class,
			"Cod" => Cod::class,
			"Cow" => Cow::class,
			"Donkey" => Donkey::class,
			"Frog" => Frog::class,
			"GlowSquid" => GlowSquid::class,
			"Horse" => Horse::class,
			"Mooshroom" => Mooshroom::class,
			"Mule" => Mule::class,
			"Ocelot" => Ocelot::class,
			"Parrot" => Parrot::class,
			"Pig" => Pig::class,
			"Pufferfish" => Pufferfish::class,
			"Rabbit" => Rabbit::class,
			"Salmon" => Salmon::class,
			"Sheep" => Sheep::class,
			"SkeletonHorse" => SkeletonHorse::class,
			"Sniffer" => Sniffer::class,
			"SnowGolem" => SnowGolem::class,
			"Squid" => Squid::class,
			"Strider" => Strider::class,
			"Tadpole" => Tadpole::class,
			"TropicalFish" => TropicalFish::class,
			"Turtle" => Turtle::class,
			"Villager" => Villager::class,
			"WanderingTrader" => WanderingTrader::class,

			//default
			default => null,
		};
	}
}