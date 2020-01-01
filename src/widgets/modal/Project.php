<?php

declare(strict_types=1);

namespace app\widgets\modal;

use app\models\User;
use Exception;
use yii\bootstrap4\BootstrapPluginAsset;
use yii\bootstrap4\Widget;
use yii\helpers\ArrayHelper;

use function array_key_exists;
use function count;
use function random_int;
use function shuffle;

/**
 * Class Project
 * @package app\widgets\modal
 */
class Project extends Widget
{
    public const PROJECT_MODAL = 'projectModal';
    public const PROJECT_EDIT_MODAL = 'projectEditModal';

    /**
     * @return string
     * @throws Exception
     */
    public function getRandomColor(): string
    {
        $chars = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 'a', 'b', 'c', 'd', 'e', 'f'];

        $color = '#';

        for ($i = 0; $i < 6; $i++) {
            shuffle($chars);

            $color .= $chars[random_int(0, count($chars) - 1)];
        }

        return $color;
    }

    /**
     * @return null|string
     * @throws Exception
     */
    public function run(): ?string
    {
        $out = null;

        if (array_key_exists(self::PROJECT_MODAL, $this->view->params)) {
            BootstrapPluginAsset::register($this->view);

            $users = ArrayHelper::map(
                User::find()->where(['status' => User::STATUS_ACTIVE])->orderBy(['name' => SORT_ASC])->all(),
                'id',
                'name'
            );

            $out .= $this->render('project', [
                'color' => $this->getRandomColor(),
                'users' => $users,
            ]);

            $pId = self::PROJECT_EDIT_MODAL;

            $this->view->registerJs(<<<JS
$("#{$pId}").on("show.bs.modal", function (event) {
    let button = $(event.relatedTarget);
    
    $("#projectEditId").val(button.data("id"));
    $("#projectEditName").val(button.data("name"));
    $("#projectEditColor").val(button.data("color"));
    
    let assignees = button.data("assignees");
    if (!(assignees instanceof Array)) {
        assignees = [assignees];
    }
    $("#projectEditAssignees option").prop("selected", false);
    $.each(assignees, function(i, e) {
        $("#projectEditAssignees option[value='" + e + "']").prop("selected", true);
    });
});
JS
            );

            $out .= $this->render('project-edit', [
                'users' => $users,
            ]);
        }

        return $out;
    }
}
