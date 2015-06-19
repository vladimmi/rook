<?php

namespace Rook\Repository;

use Rook\ORM\Annotations\Repository;
use Rook\Model\Character as CharacterModel;
use Rook\ORM\Repository\Postgres;

/**
 * Class Character
 *
 * @package Rook\Model
 * @Repository(CharacterModel::class)
 */
class Character extends Postgres
{

}