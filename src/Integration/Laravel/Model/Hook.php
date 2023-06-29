<?php

namespace CptnHook\Integration\Laravel\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use CptnHook\Model\Hook as HookInterface;

class Hook extends Model implements HookInterface
{
    use HasUuids;

    const DEFAULT_TABLE_NAME = 'cptnhook_executed_hooks';

    /**
     * We're using uuids so no incrementing
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The data type of the auto-incrementing ID.
     *
     * @var string
     */
    protected $keyType = 'string';

    public function __construct()
    {
        parent::__construct();
        $this->table = config('cptnhook.tableName', self::DEFAULT_TABLE_NAME);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getGroup(): string
    {
        return $this->group;
    }

}