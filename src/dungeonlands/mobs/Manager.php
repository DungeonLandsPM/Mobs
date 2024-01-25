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

class Manager
{
    public function __construct(private MobsLoader $plugin)
    {
    }

    private const SPAWN_AMOUNT_PER_PLAYER = 15;

    public function spawnMobs(): void
    {
        $worldManager = $this->plugin->getServer()->getWorldManager();

        foreach ($this->plugin::WORLDS as $mobType => $worldName) {
            $world = $worldManager->getWorldByName($worldName);

            if ($world === null) {
                return;
            }

            $positions = [];
            foreach ($world->getPlayers() as $worldPlayer) {
                for ($i = 0; $i < self::SPAWN_AMOUNT_PER_PLAYER; $i++) {
                    $pos = $this->findSafeSpawn($worldPlayer->getPosition());
                    if ($pos !== null) {
                        $positions[] = $pos;
                    }
                }
            }

            foreach ($positions as $position) {
                $mobTable = $this->getMobsForBiome($world->getFolderName(), $world->getBiomeId($position->getFloorX(), $position->getFloorY(), $position->getFloorZ()), $world->getTime() < 13000);

                $this->spawn($mobTable[array_rand($mobTable)], $position);
            }
        }
    }

    private const ATTEMPTS = 50;
    private const RADIUS = 100;
    private const Y_DIFFERENCE = 3;

    private function findSafeSpawn(Position $startPos): ?Position
    {
        $world = $startPos->getWorld();

        for ($i = 0; $i < self::ATTEMPTS; $i++) {
            $randomX = (int)($startPos->x + mt_rand(-self::RADIUS, self::RADIUS));
            $randomY = (int)($startPos->y + mt_rand(-self::Y_DIFFERENCE, self::Y_DIFFERENCE));
            $randomZ = (int)($startPos->z + mt_rand(-self::RADIUS, self::RADIUS));

            //BLOCK UNDER MOB
            $under = $world->getBlockAt($randomX, $randomY - 1, $randomZ);
            //BLOCK ABOVE MOB
            $above1 = $world->getBlockAt($randomX, $randomY, $randomZ)->getTypeId();
            $above2 = $world->getBlockAt($randomX, $randomY + 1, $randomZ)->getTypeId();

            if ($under->isSolid() and $above1 === BlockTypeIds::AIR and $above2 === BlockTypeIds::AIR) {
                return new Position($randomX, $randomY, $randomZ, $world);
            }
        }
        return null;
    }

    public function despawnMobs(): void
    {
        $worldManager = $this->plugin->getServer()->getWorldManager();

        foreach ($this->plugin::WORLDS as $mobType => $worldName) {
            $world = $worldManager->getWorldByName($worldName);

            if ($world === null) {
                return;
            }

            foreach ($world->getEntities() as $entity) {
                if ($entity instanceof AbstractMob) {
                    if (count($world->getPlayers()) === 0) {
                        $entity->flagForDespawn();
                        return;
                    }
                }
            }
        }
    }

    public function despawnAllMobs(): void
    {
        $worldManager = $this->plugin->getServer()->getWorldManager();

        foreach ($this->plugin::WORLDS as $mobType => $worldName) {
            $world = $worldManager->getWorldByName($worldName);

            if ($world === null) {
                return;
            }

            foreach ($world->getEntities() as $entity) {
                if ($entity instanceof AbstractMob) {
                    $entity->flagForDespawn();
                }
            }
        }
    }

    public function spawn(string $mobName, Position $position): void
    {
        $entityClass = $this->getClassFor($mobName);

        if ($entityClass === null) {
            $this->plugin->getLogger()->warning("§cERROR: {$mobName} class not found!");
            return;
        }

        (new $entityClass(new Location($position->x, $position->y, $position->z, $position->getWorld(), 0, 0)))->spawnToAll();
    }

    private function getMobsForBiome(string $worldName, int $biomeID, bool $isNight): array
    {
        if ($worldName === $this->plugin::WORLDS["overworld"] and $isNight) {
            return $this->getNightlyMobs();
        }

        if (array_key_exists($biomeID, $this->getMobsBiome())) {
            return $this->getMobsBiome()[$biomeID];
        }

        return match ($worldName) {
            $this->plugin::WORLDS["nether"] => $this->getMobsBiome()[BiomeIds::HELL],
            $this->plugin::WORLDS["the_end"] => $this->getMobsBiome()[BiomeIds::THE_END],
            default => $this->getMobsBiome()[BiomeIds::PLAINS]
        };
    }

    private function getMobsBiome(): array
    {
        $nonBiomeOverworld = [
            #HOSTILE
            "Evoker", "Pillager", "Ravager", "Vex", "Silverfish",

            #PASSIVE
            "Allay", "Axolotl", "Bat", "Cat", "Mule", "Sniffer", "Villager", "WanderingTrader",

            #NEUTRAL
            "Bee", "CaveSpider", "Enderman", "Goat", "IronGolem"
        ];

        $everyBiomeOverworld = [
            #PASSIVE
            "Chicken", "Cow", "Pig", "Sheep"
        ];

        $nonWaterBiomeOverworld = [
            #HOSTILE
            "ElderGuardian", "Guardian",

            #PASSIVE
            "Tadpole",
        ];

        $everyWaterBiomeOverworld = [
            #PASSIVE
            "GlowSquid", "Squid"
        ];

        $nonBiomeNether = [
            #HOSTILE
            "Blaze", "PiglinBrute", "WitherSkeleton",

            #PASSIVE
            "Blaze"
        ];

        $everyBiomeNether = [
            #PASSIVE
            "Strider",

            #NEUTRAL
            "Enderman"
        ];

        return [
            ##OVERWORLD
            #PLAINS
            BiomeIds::PLAINS => [...$nonBiomeOverworld, ...$everyBiomeOverworld, "Donkey", "Horse"],
            BiomeIds::SUNFLOWER_PLAINS => [...$everyBiomeOverworld, "Donkey", "Horse"],

            BiomeIds::ICE_PLAINS => ["Rabbit", "PolarBear", "Stray"],
            BiomeIds::ICE_PLAINS_SPIKES => ["Rabbit", "PolarBear", "Stray"],

            #TAIGA
            BiomeIds::TAIGA => ["Rabbit", "Wolf"],
            BiomeIds::COLD_TAIGA => ["Rabbit", "Fox"],

            #FOREST
            BiomeIds::FOREST => ["Llama", "TraderLlama", "Wolf"],
            BiomeIds::FLOWER_FOREST => ["Rabbit"],

            #JUNGLE
            BiomeIds::JUNGLE => ["Ocelot", "Parrot", "Panda"],
            BiomeIds::BAMBOO_JUNGLE => ["Ocelot", "Parrot", "Panda"],

            #SAVANNA
            BiomeIds::SAVANNA => ["Donkey", "Horse", "Llama", "TraderLlama"],
            BiomeIds::SAVANNA_PLATEAU => ["Donkey", "Horse", "Llama", "TraderLlama"],

            #ISLAND
            BiomeIds::MUSHROOM_ISLAND => ["Mooshroom"],

            #Desert
            BiomeIds::DESERT => ["Camel", "Rabbit", "Husk"],

            #OCEAN
            BiomeIds::OCEAN => ["Cod", "Dolphin", "Drowned", ...$everyWaterBiomeOverworld],
            BiomeIds::DEEP_OCEAN => ["Cod", "Dolphin", "Drowned"],

            BiomeIds::WARM_OCEAN => ["Pufferfish", "TropicalFish", "Dolphin", "Drowned"],

            BiomeIds::LUKEWARM_OCEAN => ["Cod", "Pufferfish", "TropicalFish", "Dolphin", "Drowned"],
            BiomeIds::DEEP_LUKEWARM_OCEAN => ["Cod", "Pufferfish", "TropicalFish", "Dolphin", "Drowned"],

            BiomeIds::COLD_OCEAN => ["Cod", "Salmon", "Drowned"],
            BiomeIds::DEEP_COLD_OCEAN => ["Cod", "Salmon", "Drowned"],

            BiomeIds::FROZEN_OCEAN => ["Rabbit", "Salmon", "Drowned", "PolarBear", "Stray"],
            BiomeIds::DEEP_FROZEN_OCEAN => ["Salmon", "Drowned", "PolarBear", "Stray"],
            BiomeIds::LEGACY_FROZEN_OCEAN => ["Stray"],

            #RIVER
            BiomeIds::RIVER => ["Salmon", "Drowned", ...$nonWaterBiomeOverworld, ...$everyWaterBiomeOverworld],
            BiomeIds::FROZEN_RIVER => ["Rabbit", "Salmon", "Drowned", "PolarBear", "Stray"],

            #BEACH
            BiomeIds::BEACH => ["Turtle"],
            BiomeIds::COLD_BEACH => ["Rabbit"],

            #SWAMP
            BiomeIds::SWAMPLAND => ["Frog", "TropicalFish"],

            ##NETHER
            BiomeIds::HELL => [...$nonBiomeNether, ...$everyBiomeNether],

            BiomeIds::SOULSAND_VALLEY => ["Enderman", "Ghast", "Skeleton"],
            BiomeIds::WARPED_FOREST => ["Enderman"],
            BiomeIds::CRIMSON_FOREST => ["Piglin", "ZombifiedPiglin", "Hoglin"],
            BiomeIds::BASALT_DELTAS => ["Ghast", "MagmaCube"],

            ##THE_END
            BiomeIds::THE_END => ["Enderman", "Endermite", "Shulker"],
        ];
    }

    private function getNightlyMobs(): array
    {
        return ["SkeletonHorse", "SnowGolem", "Spider", "CaveSpider", "Creeper", "Phantom", "Skeleton", "Slime", "Warden", "Witch", "Zombie", "ZombieVillager"];
    }

    private function getClassFor(string $name): ?string
    {
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