<?php

declare(strict_types=1);

namespace app\api\models;

use app\models\User;

/**
 * Class Profile
 * @package app\api\models
 */
class Profile extends User
{
    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['name'], 'required'],
            [['name'], 'string', 'max' => 255],
            [['phone'], 'string', 'max' => 45],
        ];
    }

    /**
     * @return array
     */
    public function fields(): array
    {
        return [
            'id',
            'name',
            'email',
            'phone',
            'createdAt' => 'created_at',
            'updatedAt' => 'updated_at',
        ];
    }

    /**
     * @return array
     */
    public function scenarios(): array
    {
        $scenarios = parent::scenarios();

        $scenarios['update'] = $scenarios[self::SCENARIO_DEFAULT];

        return $scenarios;
    }
}
