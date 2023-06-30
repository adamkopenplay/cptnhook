<?php

use CptnHook\DefaultHookRunner;
use CptnHook\Integration\Laravel\EloquentHookRepository;
use CptnHook\Integration\Laravel\HookBuilder;
use CptnHook\DefaultFileSystem;

return [
    'tableName' => 'cptnhook_hooks',

    'runner' => DefaultHookRunner::class,

    'repository' => EloquentHookRepository::class,

    'builder' => HookBuilder::class,

    'filesystem' => DefaultFileSystem::class,

    'groups' => [
        'post-deploy' => base_path() . '/hooks/post-deploy',
    ],
];
