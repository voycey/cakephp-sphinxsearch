<?php
namespace Sphinx\Model\Behavior;

use Cake\ORM\Behavior;
use Cake\ORM\Entity;
use Cake\ORM\Query;
use Cake\ORM\Table;
use Cake\Utility\Hash;
use Foolz\SphinxQL\SphinxQL;
use Foolz\SphinxQL\Connection;


class SphinxBehavior extends Behavior
{
    public $conn;
    public $table;

    public function __construct(Table $table, array $config = ['host' => 'localhost', 'port' => 9306]) {
        $this->conn = new Connection();
        $this->table = $table;
        $this->conn->setParams(['host' => $config['host'], 'port' => $config['port']]);
    }


    /**
     * @param $options (match_fields, paginate)
     * @return Query
     */
    public function search($options) {
        $sphinx = SphinxQL::create($this->conn)->select('id')
            ->from($options['index'])
            ->match((empty($options['match_fields']) ? "*" : $options['match_fields']), $options['term']);

        $result = $sphinx->execute();

        $ids = Hash::extract($result, '{n}.id');

        $query = $this->table->find();

        if (!empty($options['paginate']['fields'])) {
            $query->select($options['paginate']['fields']);
        }

        if (!empty($options['paginate']['contain'])) {
            $query->contain($options['paginate']['contain']);
        }

        $query->where([$this->table->alias() . '.id IN' => $ids]);

        return $query;
    }
}
