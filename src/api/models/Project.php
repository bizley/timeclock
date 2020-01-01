<?php

declare(strict_types=1);

namespace app\api\models;

/**
 * Class Project
 * @package app\api\models
 */
class Project extends \app\models\Project
{
    /**
     * @return array
     */
    public function fields(): array
    {
        return [
            'id',
            'name',
            'color',
        ];
    }
}
