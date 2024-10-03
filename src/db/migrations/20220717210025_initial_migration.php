<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class InitialMigration extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-change-method
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change(): void
    {
        //, ['null' => true]['unique' => true]['limit' => 30]
        //['id' => false, 'primary_key' => ['user_id', 'follower_id']]
        $table = $this->table('users');
        $table->addColumn('email', 'string')
            ->addColumn('password', 'string')
            ->addColumn('username', 'string')
            ->addColumn('is_admin', 'string')
            ->addIndex(['username', 'email'], ['unique' => true])
            ->create();



        $table = $this->table('groups');
        $table->addColumn('group_name', 'string')
            ->addColumn('admin_user_id', 'integer')
            ->addColumn('is_active', 'integer', ['default' => 1])
            ->create();

        $table = $this->table('group_members');
        $table->addColumn('group_id', 'integer')
            ->addColumn('user_id', 'integer')
            ->addColumn('is_active', 'integer', ['default' => 1])
            ->create();

        $table = $this->table('posts');
        $table->addColumn('user_id', 'integer')
            ->addColumn('title', 'string')
            ->addColumn('body', 'text')
            ->addColumn('groups_id', 'integer')
            ->addColumn('created_at', 'datetime')
            ->addColumn('is_active', 'integer', ['default' => 1])
            ->addForeignKey('user_id', 'users', 'id', ['delete' => 'CASCADE', 'update' => 'NO_ACTION'])
            ->addForeignKey('groups_id', 'groups', 'id', ['delete' => 'CASCADE', 'update' => 'NO_ACTION'])
            ->create();        
    }
}
