<?php

declare(strict_types=1);

namespace dungeonlands\mobs;

use dungeonlands\mobs\entity\AbstractMob;
use dungeonlands\mobs\entity\hostile\Blaze;
use dungeonlands\mobs\entity\neutral\Bee;
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
                if (!$entity instanceof AbstractMob) {
                    return;
                }

                if (count($world->getPlayers()) === 0) {
                    $entity->flagForDespawn();
                    return;
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
                if (!$entity instanceof AbstractMob) {
                    return;
                }

                $entity->flagForDespawn();
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
            #PASSIVE
            "Allay", "Axolotl", "Bat", "Cat", "Mule", "Sniffer", "Villager", "WanderingTrader",

            #NEUTRAL
            "Bee"
        ];

        $everyBiomeOverworld = [
            #PASSIVE
            "Chicken", "Cow", "Pig", "Sheep"
        ];

        $nonWaterBiomeOverworld = [
            #PASSIVE
            "Tadpole",
        ];

        $everyWaterBiomeOverworld = [
            #PASSIVE
            "GlowSquid", "Squid"
        ];

        $nonBiomeNether = [
            #PASSIVE
            "Blaze"
        ];

        $everyBiomeNether = [
            #PASSIVE
            "Strider",
        ];

        return [
            ##OVERWORLD
            #PLAINS
            BiomeIds::PLAINS => [...$nonBiomeOverworld, ...$everyBiomeOverworld, "Donkey", "Horse"],
            BiomeIds::SUNFLOWER_PLAINS => [...$everyBiomeOverworld, "Donkey", "Horse"],

            BiomeIds::ICE_PLAINS => ["Rabbit"],
            BiomeIds::ICE_PLAINS_SPIKES => ["Rabbit"],
            BiomeIds::COLD_TAIGA => ["Rabbit"],

            #TAIGA
            BiomeIds::TAIGA => ["Rabbit"],

            #FOREST
            BiomeIds::FLOWER_FOREST => ["Rabbit"],

            #JUNGLE
            BiomeIds::JUNGLE => ["Ocelot", "Parrot"],
            BiomeIds::BAMBOO_JUNGLE => ["Ocelot", "Parrot"],

            #SAVANNA
            BiomeIds::SAVANNA => ["Donkey", "Horse"],
            BiomeIds::SAVANNA_PLATEAU => ["Donkey", "Horse"],

            #ISLAND
            BiomeIds::MUSHROOM_ISLAND => ["Mooshroom"],

            #Desert
            BiomeIds::DESERT => ["Camel", "Rabbit"],

            #OCEAN
            BiomeIds::OCEAN => ["Cod", ...$everyWaterBiomeOverworld],
            BiomeIds::DEEP_OCEAN => ["Cod"],

            BiomeIds::WARM_OCEAN => ["Pufferfish", "TropicalFish"],

            BiomeIds::LUKEWARM_OCEAN => ["Cod", "Pufferfish", "TropicalFish"],
            BiomeIds::DEEP_LUKEWARM_OCEAN => ["Cod", "Pufferfish", "TropicalFish"],

            BiomeIds::COLD_OCEAN => ["Cod", "Salmon"],
            BiomeIds::DEEP_COLD_OCEAN => ["Cod", "Salmon"],

            BiomeIds::FROZEN_OCEAN => ["Rabbit", "Salmon"],
            BiomeIds::DEEP_FROZEN_OCEAN => ["Salmon"],

            #RIVER
            BiomeIds::RIVER => ["Salmon", ...$nonWaterBiomeOverworld, ...$everyWaterBiomeOverworld],
            BiomeIds::FROZEN_RIVER => ["Rabbit", "Salmon"],

            #BEACH
            BiomeIds::BEACH => ["Turtle"],
            BiomeIds::COLD_BEACH => ["Rabbit"],

            #SWAMP
            BiomeIds::SWAMPLAND => ["Frog", "TropicalFish"],

            ##NETHER
            BiomeIds::HELL => [...$nonBiomeNether, ...$everyBiomeNether],
        ];
    }

    private function getNightlyMobs(): array
    {
        return ["SkeletonHorse", "SnowGolem"];
    }

    private function getClassFor(string $name): ?string
    {
        return match ($name) {
            #HOSTILE
            "Blaze" => Blaze::class,

            #NEUTRAL
            "Bee" => Bee::class,

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