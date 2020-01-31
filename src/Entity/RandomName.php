<?php

declare(strict_types=1);

namespace App\Entity;

final class RandomName
{
    private static array $animals = [
        'Rodent', 'Spoonbill', 'Dove', 'Weasel', 'Locust', 'Gorilla',
        'Sturgeon', 'Panda', 'Slug', 'Mule', 'Goat', 'Shrimp', 'Meadowlark', 'Hamster',
        'Harrier', 'Guanaco', 'Junglefowl', 'Termite', 'Crocodile', 'Lungfish', 'Pony', 'Egret',
        'Gazelle', 'Sailfish', 'Horse', 'Mandrill', 'Cat', 'Possum', 'Rook',
    ];

    private static array $colors = [
        'OxfordBlue', 'SpringBud', 'VividOrchid', 'Carnelian', 'DeepViolet',
        'StarCommandBlue', 'PullmanGreen', 'BitterLime', 'AlienArmpit', 'SpanishOrange',
        'MaximumBlue', 'CyberGrape', 'FernGreen', 'Pearl', 'TeaRose', 'WinterSky',
        'YellowOrange', 'PaleCyan', 'ElectricYellow', 'LavenderMagenta', 'RuddyPink', 'Boysenberry', 'WoodBrown',
        'MediumSpringGreen', 'PaleCornflowerBlue', 'OutrageousOrange', 'MagicPotion',
        'Blond', 'VividViolet', 'FrenchPlum', 'DodgerBlue', 'Sandstorm', 'Ebony',
        'Veronica', 'FuchsiaPurple', 'DeepCerise', 'VividOrange', 'FrenchFuchsia', 'MayaBlue',
    ];

    private string $name;

    public function __construct(?string $name = null)
    {
        if ($name === null) {
            // Generate name
            $name = sprintf(
                "%s%s",
                self::$colors[array_rand(self::$colors)],
                self::$animals[array_rand(self::$animals)]
            );
        }
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function __toString(): string
    {
        return $this->getName();
    }

}
