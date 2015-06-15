<?php

namespace Rook\Model;

use Rook\ORM\Annotations\Field;
use Rook\ORM\Annotations\Table;
use Rook\ORM\Model;
use Rook\ORM\Types\String;
use Rook\ORM\Types\DateTime;

/**
 * Class Test
 *
 * @package Rook\Model
 * @Table("test", alias="test")
 */
class Test extends Model
{
    /**
     * @var string
     * @Field(type=String::class)
     */
    private $field1;

    /**
     * @var \DateTime
     * @Field(type=Timestamp::class)
     */
    private $field2;
}