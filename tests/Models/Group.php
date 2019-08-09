<?php

namespace Te7aHoudini\LaravelApplicant\Tests\Models;

use Illuminate\Database\Eloquent\Model;
use Te7aHoudini\LaravelApplicant\Traits\ReceivesApplications;

class Group extends Model
{
    use ReceivesApplications;

    protected $guarded = [];
}
