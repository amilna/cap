<?php

use yii\db\Schema;
use yii\db\Migration;

class m150127_082652_amilna_cap extends Migration
{
    public function up()
    {
		$this->createTable($this->db->tablePrefix.'cap_account', [
            'id' => 'pk',
            'code' => Schema::TYPE_INTEGER.' NOT NULL',
            'name' => Schema::TYPE_STRING . ' NOT NULL',
            'parent_id' => Schema::TYPE_INTEGER,
            'increaseon' => Schema::TYPE_SMALLINT.' NOT NULL',            
            'isbalance' => Schema::TYPE_BOOLEAN.' NOT NULL DEFAULT FALSE',
            'exchangable' => Schema::TYPE_BOOLEAN.' NOT NULL DEFAULT FALSE',
            'id_left' => Schema::TYPE_INTEGER.' NOT NULL DEFAULT (-1)',
            'id_right' => Schema::TYPE_INTEGER.' NOT NULL DEFAULT 0',
            'id_level' => Schema::TYPE_INTEGER.' NOT NULL DEFAULT 0',
            'isdel' => Schema::TYPE_SMALLINT.' NOT NULL DEFAULT 0',
        ]);
        $this->createIndex($this->db->tablePrefix.'cap_account_code'.'_key', $this->db->tablePrefix.'cap_account', 'code', true);        
        $this->addForeignKey( $this->db->tablePrefix.'cap_account_parent_id', $this->db->tablePrefix.'cap_account', 'parent_id', $this->db->tablePrefix.'cap_account', 'id', 'SET NULL', null );
        
        $this->createTable($this->db->tablePrefix.'cap_template', [
            'id' => 'pk',            
            'title' => Schema::TYPE_STRING . ' NOT NULL',
            'json' => Schema::TYPE_TEXT . ' NOT NULL',            
        ]);
        $this->createIndex($this->db->tablePrefix.'cap_template_title'.'_key', $this->db->tablePrefix.'cap_template', 'title', true);        
        
        $this->createTable($this->db->tablePrefix.'cap_transaction', [
            'id' => 'pk',
            'subject' => Schema::TYPE_STRING . ' NOT NULL',
            'title' => Schema::TYPE_STRING . ' NOT NULL',
            'remarks' => Schema::TYPE_TEXT . ' NOT NULL',
            'reference' => Schema::TYPE_STRING . ' NOT NULL',
            'tags' => Schema::TYPE_STRING . '',
            'total' => Schema::TYPE_FLOAT . ' NOT NULL DEFAULT 0',
            'type' => Schema::TYPE_SMALLINT. ' NOT NULL',
            'time' => Schema::TYPE_TIMESTAMP. ' NOT NULL DEFAULT NOW()',
            'isdel' => Schema::TYPE_SMALLINT.' NOT NULL DEFAULT 0',
        ]);
        $this->createIndex($this->db->tablePrefix.'cap_transaction_reference'.'_key', $this->db->tablePrefix.'cap_transaction', 'reference', true);        
        
        $this->createTable($this->db->tablePrefix.'cap_journal', [
            'id' => 'pk',
            'account_id' => Schema::TYPE_INTEGER.' NOT NULL',
            'transaction_id' => Schema::TYPE_INTEGER.' NOT NULL',            
            'remarks' => Schema::TYPE_TEXT . ' NOT NULL',
            'quantity' => Schema::TYPE_DECIMAL . '(15,6) NOT NULL DEFAULT 0',
            'amount' => Schema::TYPE_FLOAT . ' NOT NULL DEFAULT 0',
            'type' => Schema::TYPE_SMALLINT. ' NOT NULL',            
            'isdel' => Schema::TYPE_SMALLINT.' NOT NULL DEFAULT 0',
        ]);
        
        $this->addForeignKey( $this->db->tablePrefix.'cap_journal_account_id', $this->db->tablePrefix.'cap_journal', 'account_id', $this->db->tablePrefix.'cap_account', 'id', 'CASCADE', null );        
        $this->addForeignKey( $this->db->tablePrefix.'cap_journal_transaction_id', $this->db->tablePrefix.'cap_journal', 'transaction_id', $this->db->tablePrefix.'cap_transaction', 'id', 'CASCADE', null );
    }

    public function down()
    {
        echo "m150127_082652_amilna_cap cannot be reverted.\n";

        return false;
    }
}
