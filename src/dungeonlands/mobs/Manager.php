<?php

declare(strict_types=1);

namespace dungeonlands\mobs;

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

class Manager{
	public function __construct(private MobsLoader $plugin){
	}

	private const SPAWN_AMOUNT_PER_PLAYER = 10;

	public function spawnMobs() : void{
		$worldManager = $this->plugin->getServer()->getWorldManager();

		foreach($this->plugin::WORLDS as $mobType => $worldName){
			$world = $worldManager->getWorldByName($worldName);

			if($world === null){
				return;
			}

			$positions = [];
			foreach($world->getPlayers() as $worldPlayer){
				for($i = 0; $i < self::SPAWN_AMOUNT_PER_PLAYER; $i++){
					$pos = $this->findSafeSpawn($worldPlayer->getPosition());
					if($pos !== null){
						$positions[] = $pos;
					}
				}
			}

			foreach($positions as $position){
				$mobTable = $this->getMobsForBiome($world->getFolderName(), $world->getBiomeId($position->getFloorX(), $position->getFloorY(), $position->getFloorZ()), $world->getTime() < 13000);

				$this->spawn($mobTable[array_rand($mobTable)], $position);
			}
		}
	}

	private const ATTEMPTS = 50;
	private const RADIUS = 100;
	private const Y_DIFFERENCE = 3;

	private function findSafeSpawn(Position $startPos) : ?Position{
		$world = $startPos->getWorld();

		for($i = 0; $i < self::ATTEMPTS; $i++){
			$randomX = (int) ($startPos->x + mt_rand(-self::RADIUS, self::RADIUS));
			$randomY = (int) ($startPos->y + mt_rand(-self::Y_DIFFERENCE, self::Y_DIFFERENCE));
			$randomZ = (int) ($startPos->z + mt_rand(-self::RADIUS, self::RADIUS));

			//BLOCK UNDER MOB
			$under = $world->getBlockAt($randomX, $randomY - 1, $randomZ);
			//BLOCK ABOVE MOB
			$above1 = $world->getBlockAt($randomX, $randomY, $randomZ)->getTypeId();
			$above2 = $world->getBlockAt($randomX, $randomY + 1, $randomZ)->getTypeId();

			if($under->isSolid() and $above1 === BlockTypeIds::AIR and $above2 === BlockTypeIds::AIR){
				return new Position($randomX, $randomY, $randomZ, $world);
			}
		}
		return null;
	}

	public function despawnMobs() : void{
		$worldManager = $this->plugin->getServer()->getWorldManager();

		foreach($this->plugin::WORLDS as $mobType => $worldName){
			$world = $worldManager->getWorldByName($worldName);

			if($world === null){
				return;
			}

			foreach($world->getEntities() as $entity){
				if($entity instanceof AbstractMob){
					if(count($world->getPlayers()) === 0){
						$entity->flagForDespawn();
						return;
					}
				}
			}
		}
	}

	public function despawnAllMobs() : void{
		$worldManager = $this->plugin->getServer()->getWorldManager();

		foreach($this->plugin::WORLDS as $mobType => $worldName){
			$world = $worldManager->getWorldByName($worldName);

			if($world === null){
				return;
			}

			foreach($world->getEntities() as $entity){
				if($entity instanceof AbstractMob){
					$entity->flagForDespawn();
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
				return $this->getNightlyMobs()[BiomeIds::PLAINS];
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
			190 => [],
			188 => [],
			187 => [],

			##THE NETHER
			BiomeIds::HELL => [],
			BiomeIds::SOULSAND_VALLEY => [],
			BiomeIds::CRIMSON_FOREST => [],
			BiomeIds::WARPED_FOREST => [],
			BiomeIds::BASALT_DELTAS => [],

			##THE END
			BiomeIds::THE_END => [],
		];
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