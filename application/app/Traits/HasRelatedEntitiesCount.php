<?php

namespace App\Traits;

trait HasRelatedEntitiesCount
{
    public function getRelatedEntitiesCountAttribute()
    {
        $count = 0;

        foreach (self::RELATED_ENTITIES as $related_entity) {
            if ($this->{"{$related_entity}_count"} === null) {
                $this->loadCount($related_entity);
            }

            $count += $this->{"{$related_entity}_count"};
        }

        return $count;
    }

    public function getHasRelatedEntitiesAttribute()
    {
        return $this->related_entities_count > 0;
    }
}