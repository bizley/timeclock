<?php

declare(strict_types=1);

namespace app\api\models;

use app\models\Project;
use app\models\User;
use Yii;

use function in_array;
use function is_array;

/**
 * Class Profile
 * @package app\api\models
 */
class Profile extends User
{
    /**
     * @var int
     */
    public $projectId;

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['name'], 'required'],
            [['name'], 'string', 'max' => 255],
            [['phone'], 'string', 'max' => 45],
            [['projectId'], 'int', 'min' => 0],
            [['projectId'], 'verifyProject'],
        ];
    }

    public function verifyProject(): void
    {
        if (!empty($this->projectId) && $this->projectId > 0) {
            $project = Project::findOne((int)$this->projectId);

            if ($project === null) {
                $this->addError('projectId', Yii::t('app', 'Can not find project of given ID.'));
            } elseif (!is_array($project->assignees) || !in_array(Yii::$app->user->id, $project->assignees, false)) {
                $this->addError('projectId', Yii::t('app', 'You are not assigned to selected project.'));
            }
        }
    }

    public function afterValidate(): void
    {
        if ((int)$this->projectId === 0) {
            $this->project_id = null;
        }

        parent::afterValidate();
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
            'defaultProject' => static function ($model) {
                /* @var $model Profile */
                $project = $model->defaultProject;

                if ($project === null) {
                    return null;
                }

                return [
                    'id' => $project->id,
                    'name' => $project->name,
                ];
            },
            'availableProjects' => static function ($model) {
                /* @var $model Profile */
                $projects = [];
                $assigned = $model->assignedProjects;

                foreach ($assigned as $id => $name) {
                    $projects[] = [
                        'id' => $id,
                        'name' => $name,
                    ];
                }

                return $projects;
            },
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
