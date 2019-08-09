<?php

namespace Te7aHoudini\LaravelApplicant\Tests\Models;

use Illuminate\Database\Eloquent\Model;
use Te7aHoudini\LaravelApplicant\Traits\Applicant;

class User extends Model
{
    use Applicant;
    
    protected $guarded = [];
}
