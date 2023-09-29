<?php

namespace app\commands;

use yii\console\Controller;
use yii\console\ExitCode;

use app\models\Employee;

class EmployeeController extends Controller
{
    /**
     * генерация соотрудников
     *
     * @return     <type>  ( description_of_the_return_value )
     */
    public function actionGenerate($count=10)
    {
        $this->stdout("\n");
        $regions = array_keys(Employee::REGIONS);
        $posts = array_keys(Employee::POSTS);
        for ($i = 0; $i < $count; $i++) {
            $emp = new Employee([
                'region' => $regions[rand(0, count($regions) - 1)],
                'post' => $posts[rand(0, count($posts) - 1)],
                'coordinate' => [rand(57030972, 56702473) / 1000000,  rand(59687292, 60914062) / 1000000]
            ]);
            $emp->save();
            if ($count / 10 >= 1 && $i % (int)($count / 10) == 0)
                $this->stdout('.');
        }
        $this->stdout("\n");
        // $this->stdout('dsadds ' . $count . "\n");
        return ExitCode::OK;
    }
}