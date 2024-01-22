<?php

declare(strict_types=1);

namespace dungeonlands\mobs;

use dungeonlands\mobs\entity\AbstractMob;
use dungeonlands\mobs\entity\hostile\Blaze;
use dungeonlands\mobs\entity\neutral\Bee;
use dungeonlands\mobs\entity\passive\Allay;
use pocketmine\block\BlockTypeIds;
use pocketmine\data\bedrock\BiomeIds;
use pocketmine\entity\Location;
use pocketmine\world\Position;
use pocketmine\world\World;

class Manager
{
    public function __construct(private MobsLoader $plugin)
    {
    }

    private const SPAWN_AMOUNT = 10;

    public function spawnMobs(): void
    {
        $worldManager = $this->plugin->getServer()->getWorldManager();

        foreach ($this->plugin::WORLDS as $mobType => $world) {
            $worldInstance = $worldManager->getWorldByName($world);

            if ($worldInstance === null) {
                return;
            }

            $worldPlayers = $worldInstance->getPlayers();
            if (count($worldPlayers) === 0) {
                return;
            }

            $positions = [];
            foreach ($worldPlayers as $worldPlayer) {
                $pos = $this->findSafeSpawn($worldPlayer->getPosition(), $worldInstance);
                if ($pos !== null) {
                    $positions[] = $pos;
                }
            }

            foreach ($positions as $position) {
                $biomeID = $worldInstance->getBiomeId($pos->getFloorX(), $pos->getFloorY(), $pos->getFloorZ());
                $isNight = $worldInstance->getTime() > 13000;
                $mobTable = $this->getMobsForBiome($worldInstance->getFolderName(), $biomeID, $isNight);

                foreach ($mobTable as $mobName) {
                    $this->spawn($mobName, $position);
                }
            }
        }
    }

    private const RADIUS = 100;

    private function findSafeSpawn(Position $position, World $world): ?Position
    {
        for ($x = -self::RADIUS; $x <= self::RADIUS; $x++) {
            for ($y = -self::RADIUS; $y <= self::RADIUS; $y++) {
                for ($z = -self::RADIUS; $z <= self::RADIUS; $z++) {
                    $block = $world->getBlock($position->add($x, 0, $z));
                    if ($block->isSolid()) {
                        $blockAbove = $world->getBlock($position->add($x, 1, $z));
                        $blockAbove2 = $world->getBlock($position->add($x, 2, $z));
                        if ($blockAbove->getTypeId() !== BlockTypeIds::AIR && $blockAbove2->getTypeId() !== BlockTypeIds::AIR) {
                            return $position;
                        }
                    }
                }
            }
        }
        return null;
    }

    public function despawnMobs(): void
    {
        $worldManager = $this->plugin->getServer()->getWorldManager();

        foreach ($this->plugin::WORLDS as $mobType => $world) {
            $worldInstance = $worldManager->getWorldByName($world);

            if ($worldInstance !== null) {
                foreach ($worldInstance->getEntities() as $entity) {
                    if (!$entity instanceof AbstractMob) {
                        continue;
                    }

                    if (count($worldInstance->getPlayers()) < 1) {
                        $entity->kill();
                        continue;
                    }

                    $near = false;
                    foreach ($worldInstance->getPlayers() as $player) {
                        if (count($player->getWorld()->getNearbyEntities($player->getBoundingBox()->expandedCopy(100, 100, 100), $entity)) > 0) {
                            $near = true;
                            break;
                        }
                    }

                    if (!$near) {
                        $entity->kill();
                    }
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
            //hostile
            "Blaze" => Blaze::class,

            //neutral
            "Bee" => Bee::class,

            //passive
            "Allay" => Allay::class,

            //default
            default => null,
        };
    }
}