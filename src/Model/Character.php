<?php

namespace Rook\Model;

use Rook\ORM\Annotations\Field;
use Rook\ORM\Annotations\Table;
use Rook\ORM\Model;
use Rook\ORM\Types\Boolean;
use Rook\ORM\Types\Float;
use Rook\ORM\Types\Integer;
use Rook\ORM\Types\String;
use Rook\ORM\Types\DateTime;

/**
 * Class Character
 *
 * @package Rook\Model
 * @Table("hub.characters", alias="characters")
 */
class Character extends Model
{
    /**
     * @var int
     * @Field(Integer::class)
     */
    private $id;

    /**
     * @var string
     * @Field(String::class)
     */
    private $name;

    /**
     * @var string
     * @Field(String::class)
     */
    private $race;

    /**
     * @var string
     * @Field(String::class)
     */
    private $bloodline;

    /**
     * @var int
     * @Field(Integer::class)
     */
    private $corporation_id;

    /**
     * @var int
     * @Field(Integer::class)
     */
    private $alliance_id;

    /**
     * @var float
     * @Field(Float::class)
     */
    private $security_status;

    /**
     * @var \DateTime
     * @Field(DateTime::class)
     */
    private $updated_on;

    /**
     * @var bool
     * @Field(Boolean::class)
     */
    private $is_npc;

    /**
     * @var string
     * @Field(String::class)
     */
    private $crest_access_token;

    /**
     * @var string
     * @Field(String::class)
     */
    private $crest_refresh_token;

    /**
     * @var \DateTime
     * @Field(DateTime::class)
     */
    private $crest_expires_at;

    /**
     * Fetch characters with specified name
     *
     * @param string $name
     * @return static[]
     */
    public static function fetchByName($name)
    {
        return static::getModelManager()->fetch(
            static::class,
            'SELECT * FROM @characters WHERE name = $1',
            ['name' => $name]
        );
    }
}