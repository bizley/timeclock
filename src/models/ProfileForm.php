<?php

declare(strict_types=1);

namespace app\models;

use Yii;
use yii\base\Exception;
use yii\base\Model;

use function count_chars;
use function in_array;
use function is_array;
use function log;
use function mb_strlen;

/**
 * Class ProfileForm
 * @package app\models
 */
class ProfileForm extends RegisterForm
{
    /**
     * @var string
     */
    public $phone;

    /**
     * @var int
     */
    public $projectId;

    public function init(): void
    {
        parent::init();

        $this->name = Yii::$app->user->identity->name;
        $this->phone = Yii::$app->user->identity->phone;
        $this->projectId = Yii::$app->user->identity->project_id;
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [['name'], 'required'],
            [['name', 'phone'], 'string'],
            [['password'], 'string', 'min' => self::MIN_PASSWORD, 'max' => self::MAX_PASSWORD],
            [['password'], 'compare', 'compareAttribute' => 'emailAccount', 'operator' => '!='],
            [
                ['password'],
                function ($attribute) {
                    $entropy = 0;
                    $size = mb_strlen($this->$attribute, Yii::$app->charset ?: 'UTF-8');
                    foreach (count_chars($this->$attribute, 1) as $frequency) {
                        $p = $frequency / $size;
                        $entropy -= $p * log($p) / log(2);
                    }
                    if ($entropy < self::MIN_ENTROPY) {
                        $this->addError($attribute, Yii::t('app', 'You must provide more complex password.'));
                    }
                },
            ],
            [['projectId'], 'verifyProject'],
        ];
    }

    public function verifyProject(): void
    {
        if (!empty($this->projectId)) {
            $project = Project::findOne((int)$this->projectId);

            if ($project === null) {
                $this->addError('projectId', Yii::t('app', 'Can not find project of given ID.'));
            } elseif (!is_array($project->assignees) || !in_array(Yii::$app->user->id, $project->assignees, false)) {
                $this->addError('projectId', Yii::t('app', 'You are not assigned to selected project.'));
            }
        }
    }

    /**
     * @return bool
     */
    public function beforeValidate(): bool
    {
        return Model::beforeValidate();
    }

    /**
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'password' => Yii::t('app', 'New Password'),
            'name' => Yii::t('app', 'First And Last Name'),
            'phone' => Yii::t('app', 'Phone Number'),
            'projectId' => Yii::t('app', 'Default Project'),
        ];
    }

    /**
     * @return bool
     * @throws Exception
     */
    public function update(): bool
    {
        if (!$this->validate()) {
            return false;
        }

        /* @var $user User */
        $user = Yii::$app->user->identity;
        $user->name = $this->name;
        $user->phone = $this->phone;
        $user->project_id = $this->projectId ?? null;

        if (!empty($this->password)) {
            $user->setPassword($this->password);
            $user->generateAuthKey();
        }

        if (!$user->save()) {
            Yii::$app->alert->danger(Yii::t('app', 'There was an error while saving user.'));

            return false;
        }

        return true;
    }
}
