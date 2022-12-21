<?php

namespace Uutkukorkmaz\LaravelStatuses\Concerns;

use Illuminate\Database\Eloquent\Builder;

trait HasStatus
{
    public function initializeHasStatus(): void
    {
        if (! in_array('status', $this->fillable)) {
            $this->fillable[] = 'status';
        }
    }

    public function scopeStatus(Builder $builder, string $status): Builder
    {
        return $builder->where('status', $status);
    }

    public function scopeStatusNot(Builder $builder, string $status): Builder
    {
        return $builder->where('status', '!=', $status);
    }

    public function statusNext(): void
    {
        if (method_exists(get_class($this->status), 'next')) {
            $this->status = $this->status->next();
            $this->save();
        }
        // todo: fire custom events
    }

    public function statusPrevious(): void
    {
        if (method_exists(get_class($this->status), 'previous')) {
            $this->status = $this->status->previous();
            $this->save();
        }

        // todo: fire custom events
    }
}
