<?php

namespace Uutkukorkmaz\LaravelStatuses\Concerns;

trait HasStatus
{

    public function initializeHasStatus(): void
    {
        if(!in_array('status', $this->fillable)){
            $this->fillable[] = 'status';
        }
    }

}
