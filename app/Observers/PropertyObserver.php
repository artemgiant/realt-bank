<?php

namespace App\Observers;

use App\Models\Property\Property;

class PropertyObserver
{
    /**
     * Handle the Property "created" event.
     */
    public function created(Property $property): void
    {
        //
    }

    /**
     * Handle the Property "updated" event.
     */
    public function updated(Property $property): void
    {
        //
    }

    /**
     * Handle the Property "deleted" event.
     */
    public function deleted(Property $property): void
    {
        //
    }

    /**
     * Handle the Property "restored" event.
     */
    public function restored(Property $property): void
    {
        //
    }

    /**
     * Handle the Property "force deleted" event.
     */
    public function forceDeleted(Property $property): void
    {
        //
    }

    /**
     * Вычисление цены за м² перед созданием
     */
    public function creating(Property $property): void
    {
        $this->calculatePricePerM2($property);
    }

    /**
     * Вычисление цены за м² перед обновлением
     */
    public function updating(Property $property): void
    {
        $this->calculatePricePerM2($property);
    }

    /**
     * Вычисление цены за м²
     */
    protected function calculatePricePerM2(Property $property): void
    {
        if ($property->price && $property->area_total > 0) {
            $property->price_per_m2 = ceil($property->price / $property->area_total);
        } else {
            $property->price_per_m2 = null;
        }
    }
}
