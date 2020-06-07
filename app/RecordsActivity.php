<?php
namespace App;

use ReflectionClass;

trait RecordsActivity
{
    protected static function bootRecordsActivity()
    {
        if (auth()->guest()) return;

        foreach (static::getActivitiesToRecord() as $event) {
            static::$event(function ($model) use ($event) {
                $model->recordsActivity($event);
            });
        }

        static::deleting(function ($model) {
            $model->activities()->delete();
        });

    }

    /**
     * the events that should be recorded into activities table
     *
     * @return array
     */
    protected static function getActivitiesToRecord()
    {
        return ['created'];
    }

    public function activities()
    {
        return $this->morphMany('App\Activity', 'subject');
    }

    /**
     * records an activity after a recordable has been created
     *
     * @param string $event
     * @return void
     */
    protected function recordsActivity($event)
    {
        $this->activities()->create([
            'user_id' => auth()->id(),
            'type' => $this->getActivityType($event)
        ]);
    }

    /**
     * return activity type according to the event
     *
     * @param string $event
     * @return string
     */
    protected function getActivityType($event)
    {
        $type = strtolower((new ReflectionClass($this))->getShortName());

        return sprintf('%s_%s', $event, $type);
    }
}
