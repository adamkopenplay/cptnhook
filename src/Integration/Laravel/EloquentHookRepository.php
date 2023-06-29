<?php

namespace CptnHook\Integration\Laravel;

use CptnHook\HookRepository;
use CptnHook\Integration\Laravel\Model\Hook;
use Illuminate\Database\Eloquent\Collection;
use CptnHook\Model\HookList;

class EloquentHookRepository implements HookRepository
{
    public function allInGroup(string $group): HookList
    {
        $hooks = HookList::fromArray([]);

        Hook::chunk(100, function (Collection $loadedHooks) use($hooks) {
            foreach ($loadedHooks as $loadedHook) {
                $hooks[] = $loadedHook;
            }
        });

        return $hooks;
    }

    public function save(string $group, string $name)
    {
        $hook = new Hook;
        $hook->group = $group;
        $hook->name = $name;
        $hook->save();
    }
}