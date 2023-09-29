<?php

namespace app\controllers;

use yii\web\Controller;
use yii\helpers\Json;
use yii\filters\AccessControl;
use Yii;

use app\models\Employee;

class YaMapController extends Controller
{

    public function behaviors()
    {
        return [
            [
                'class' => AccessControl::class,
                'only' => ['set-data'],
                'rules' => [
                    ['allow' => true, 'verbs' => ['post']],
                ],
            ],
        ];
    }

    /**
     * основная страница приложения
     *
     * @return     <type>  ( description_of_the_return_value )
     */
    public function actionIndex()
    {

        return $this->render('map', [
            'model' => new Employee()
        ]);
    }

    /**
     * получение и обновление даннных по сотрудникам ...
     *
     * @param      <type>  $coords    Ограничивающие координаты просмотра
     * @param      <type>  $callback  The callback
     *
     * @return     string  ( description_of_the_return_value )
     */
    public function actionGetData($coords, $callback)
    {
        $data = [
            'type' => 'FeatureCollection',
            'features' => Employee::getGeoObjects([
                'bounds' => array_chunk(array_map('floatval', explode(',', $coords)), 2),
            ]),
        ];
        $expr = Json::encode($data);
        return "$callback($expr)";

    }

    /**
     * Добавление новых данных
     *
     * @return     <type>  ( description_of_the_return_value )
     */
    public function actionSetData()
    {
        $post = $this->request->post();
        $emp = new Employee();
        if (!$emp->load($post) || !$emp->save()) {
            return $this->asjson(['errs' => $emp->errors]);
        }

        return $this->asjson([
            'ok' => true,
        ]);
    }

}