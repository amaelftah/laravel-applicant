<?php

namespace Te7aHoudini\LaravelApplicant\Models;

use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    protected $guarded = [];

    protected $dates = [
        'status_updated_at',
    ];

    public function getTable()
    {
        return config('laravel-applicant.table_name');
    }
}
