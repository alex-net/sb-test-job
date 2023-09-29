<?php

use yii\db\Migration;

/**
 * Class m230928_155824_points_tbl
 */
class m230928_155824_points_tbl extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('create extension if not exists postgis');
        $this->execute("create type Region as enum ('north', 'south')");
        $this->execute("create type Post as enum ('fitter', 'agent', 'engineer')");
        $this->createTable('employees', [
            'id' => $this->primaryKey()->comment('Ключик'),
            'coordinate' => 'point not null',
            'region' => 'Region not null',
            'post' => 'Post not null',
        ]);

        $this->execute('create index emp_coordinate_ind on employees using gist (coordinate)');

        foreach (['region', 'post'] as $field) {
            $this->createIndex("emp_${field}_ind", 'employees', [$field]);
        }

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('employees');
        foreach (['Region', 'Post'] as $type) {
            $this->execute('drop type if exists ' . $type);
        }
        $this->execute('drop extension if exists postgis');
    }
}
