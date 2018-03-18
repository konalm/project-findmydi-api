<?php


use Phinx\Migration\AbstractMigration;
use Phinx\Db\Adapter\PostgresAdapter;

class MigrationTest extends AbstractMigration
{
    /**
     * Migrate Up
     */
    public function up() 
    {
        $table = $this->table('migration_test_table');

        $table->addColumn('column_a', 'integer')
            ->addColumn('column_b', 'integer')
            ->addColumn('column_c', 'integer')
            ->create();
    }

    /**
     * Migrate Down 
     */
    public function down()
    {

    }
}
