<?php

declare(strict_types=1);

namespace app\models;

use Exception;
use Yii;
use yii\base\Model;
use yii\web\UploadedFile;

/**
 * Class ClockForm
 * @package app\models
 */
class TerminalForm extends Model
{
    /**
     * @var string
     */
    public $tag;

    /**
     * @var string
     */
    public $image;

    /**
     * @var UploadedFile
     */
    public $imageFile;

    /**
     * @var boolean
     */
    public $delete;

    /**
     * @var User
     */
    private $_user;

    /**
     * TerminalForm constructor.
     * @param User $session
     * @param array $config
     */
    public function __construct(User $session, array $config = [])
    {
        $this->_user = $session;
        $this->tag = !empty($session->tag) ? $session->tag : null;
        $this->image = !empty($session->image) ? $session->image : null;
        parent::__construct($config);
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [['tag'], 'required'],
            [['tag'], 'string'],
            [['imageFile'], 'file', 'extensions' => 'png, jpg'],
            [['delete'], 'required'],
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'tag' => Yii::t('app', 'RFID Tag Identifier'),
            'imageFile' => Yii::t('app', 'If you want to upload an image, select one:'),
            'delete' => Yii::t('app', 'Do you want to delete the current picture?'),
        ];
    }

    /**
     * @return bool
     * @throws Exception
     */
    public function save(): bool
    {
        if (!$this->validate()) {
            return false;
        }

        if ($this->imageFile) {
            $this->imageFile->saveAs(Yii::$app->params['uploadPath'] . $this->imageFile->name);
        }

        if ($this->delete && !empty($this->_user->image)) {
            unlink(Yii::$app->params['uploadPath'] . $this->image);
            $this->_user->image = null;
        } elseif (!$this->delete && $this->imageFile) {
            $this->_user->image = $this->imageFile->name;
        }

        $this->_user->tag = !empty($this->tag) ? $this->tag : null;

        return $this->_user->save();
    }
}
