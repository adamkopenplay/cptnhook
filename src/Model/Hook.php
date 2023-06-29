<?php

namespace CptnHook\Model;

interface Hook
{
    public function getName(): string;
    public function getGroup(): string;
}