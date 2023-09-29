<?php

namespace app\models;

use yii\base\Model;
use yii\db\Expression;
use yii\db\Query;
use Yii;

class Employee extends Model
{

    const REGIONS = [
        'north' => 'Север',
        'south' => 'Юг',
    ];
    const POSTS = [
        'fitter' => 'Монтажник',
        'agent' => 'Агент',
        'engineer' => 'Выездной инженер',
    ];


    public $id, $coordinate, $region, $post;
    public $allowAdd;

    public function rules()
    {
        return [
            ['id', 'integer'],
            ['region', 'in', 'range' => array_keys(static::REGIONS)],
            ['post', 'postNormalizer',],
            ['post', 'in', 'range' => array_keys(static::POSTS)],
            ['coordinate', 'each', 'rule' => ['double']],
            [['region', 'post', 'coordinate'], 'required'],
        ];
    }

    public function postNormalizer()
    {
        if (is_array($this->post)) {
            $this->post = reset($this->post);
        }
    }

    public function attributeLabels()
    {
        return [
            'region' => 'Регион',
            'post' => 'Должность',
            'allowAdd' => 'Добавление элемента',
        ];
    }

    public function save()
    {
        if (!$this->validate()) {
            return false;
        }

        $attrs = $this->getAttributes($this->activeAttributes(), ['id']);
        $attrs['coordinate'] = new Expression(sprintf('point(%f, %f)', $attrs['coordinate'][0], $attrs['coordinate'][1]));
        Yii::$app->db->createCommand()->insert('employees', $attrs)->execute();

        return true;
    }

    public static function getGeoObjects($filer=[])
    {
        $items = [];

        $q = new Query();
        $q->from('employees');
        $q->select(['*', 'x' => new Expression('st_x(coordinate::geometry)'), 'y' => new Expression('st_y(coordinate::geometry)')]);

// ST_MakeBox2D(point(3,4)::geometry, point(2,5)::geometry)
        $where = ['and', new Expression(sprintf("st_contains(ST_MakeBox2D( point(%f, %f)::geometry, point(%f, %f)::geometry)::geometry,  coordinate::geometry) ", $filer['bounds'][0][0], $filer['bounds'][0][1], $filer['bounds'][1][0], $filer['bounds'][1][1]))];
        $q->where($where);
        foreach ($q->each() as $point) {
            $region_txt = static::REGIONS[$point['region']];
            $post_txt = static::POSTS[$point['post']];
            $items[] = [
                'type' => 'Feature',
                'id' => $point['id'],
                'geometry' => [
                    'type' => 'Point',
                    'coordinates' => [$point['x'], $point['y']],
                ],
                'options' => [
                    'iconColor' => $point['region'] == 'south' ? 'red' : 'blue',
                ],
                'properties' => [
                    'balloonContentHeader' => 'Даннные работника',
                    'balloonContentBody' => "<b>Регион</b>: $region_txt<br/><b>Должность</b>: $post_txt",
                ],
                'region_val'=> $point['region'],
                'ObjData' => [
                    'region_txt'=> $region_txt,
                    'region_val'=> $point['region'],
                    'post_txt' => $post_txt,
                    'post_val' => $point['post'],
                ]
            ];
        }
        return $items;
    }
}