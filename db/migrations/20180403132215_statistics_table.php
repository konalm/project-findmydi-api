<?php


use Phinx\Migration\AbstractMigration;

class StatisticsTable extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('statistics');

        $table->addIndex(['id'])
            ->addColumn('event', 'string')
            ->addColumn('instructor_id', 'integer')
            ->addColumn('date', 'datetime')
            ->create(); 
    }
}
