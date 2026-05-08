<?php

namespace App\Observers;

use App\Models\Cover;

class CoverObserver
{
    public function creating(Cover $cover)
    {
        $cover->user_id = auth()->id();
        $cover->order = Cover::max('order') + 1;
    }

    public function updating(Cover $cover)
    {
        // 
    }
}
