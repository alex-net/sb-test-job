<?php

use yii\bootstrap5\Html;
use yii\bootstrap5\ActiveForm;
use kartik\select2\Select2;
use kartik\switchinput\SwitchInput;



use app\assets\NvsSAsset;
use app\models\Employee;

$this->title = 'Проект «Север против Юга»';

NvsSAsset::register($this);

$f = ActiveForm::begin(['enableClientValidation' => false, 'options' => ['id' => 'filter-form']]);
?>

    <div class="row">
        <div class="col">
            <?= $f->field($model, 'region')->widget(Select2::class, [
                'data' => Employee::REGIONS,
                'options' => ['prompt' => 'Регион не указан', 'id' => 'region-el'],
                'pluginOptions' => ['allowClear' => true],
            ]) ?>
        </div>


    <div class="col">
        <?= $f->field($model, 'post')->widget(Select2::class, [
            'data' => Employee::POSTS,
            'options' => ['placeholder' => 'Должность не указана', 'id' => 'post-el', 'multiple' => true],
        ]); ?>
    </div>
    <div class="col col-2">
        <?= $f->field($model, 'allowAdd')->widget(SwitchInput::class, [
            'options' => ['id' => 'add-item-flag'],
            'containerOptions' => ['title' => 'Режим добавление нового элемента'],
            'pluginEvents' => [
                "switchChange.bootstrapSwitch" => "function(e) { $('body').trigger('change-type-form') }",
            ],
        ]) ?>

    </div>
</div>



<?php ActiveForm::end() ?>
<div id="YMapsID" style="height: 500px;"></div>