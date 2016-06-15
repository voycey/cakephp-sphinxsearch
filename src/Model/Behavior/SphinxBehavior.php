<?php
namespace Sphinx\Model\Behavior;

use Cake\ORM\Behavior;
use Cake\Utility\Hash;
use Foolz\SphinxQL\Drivers\Mysqli\Connection;
use Foolz\SphinxQL\SphinxQL;

/**
 * Class SphinxBehavior
 */
class SphinxBehavior extends Behavior
{
    /** @var \Foolz\SphinxQL\Drivers\Mysqli\Connection */
    public $conn;

    /** @var array */
    protected $_defaultConfig = [
        'connection' => [
            'host' => 'localhost',
            'port' => '9306',
        ],
    ];

    /**
     * @param array $config
     */
    public function initialize(array $config)
    {
        $this->conn = new Connection();
        $this->conn->setParams([
            'host' => $this->config('connection')['host'],
            'port' => $this->config('connection')['port'],
        ]);
    }

    /**
     * @param $options (match_fields, paginate)
     * @return \Cake\ORM\Query
     */
    public function search(array $options)
    {
        $sphinx = SphinxQL::create($this->conn)->select('id')
            ->from($options['index'])
            ->match((empty($options['match_fields']) ? "*" : $options['match_fields']), $options['term'])
            ->limit((empty($options['limit'])) ? 1000 : $options['limit']);

        $result = $sphinx->execute()->fetchAllAssoc();

        if (!empty($result)) {

            $ids = Hash::extract($result, '{n}.id');
            $query = $this->_table->find();
            
            if (!empty($options['paginate']['fields'])) {
                $query->select($options['paginate']['fields']);
            }
            
            if (!empty($options['paginate']['contain'])) {
                $query->contain($options['paginate']['contain']);
            }
            
            $query->where([$this->_table->alias() . '.' . $this->_table->primaryKey() . ' IN' => $ids]);

            return $query;
        }
        return false;
    }
}
