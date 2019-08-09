<?php

namespace Te7aHoudini\LaravelApplicant\Tests\Unit\Traits;

use Te7aHoudini\LaravelApplicant\Tests\TestCase;
use Te7aHoudini\LaravelApplicant\Models\Application;

class ApplicantTest extends TestCase
{
    /** @test */
    public function model_applies_using_model()
    {
        $this->user->appliesFor($this->group);

        $this->assertEquals(
            [
                'type' => 'applicant',
                'applicant_id' => $this->user->id,
                'applicant_type' => get_class($this->user),
                'status' => 'created',
                'receiver_id' => $this->group->id,
                'receiver_type' => get_class($this->group),
            ],
            $this->firstApplicationAttrs()
        );
    }

    /** @test */
    public function model_applies_using_array()
    {
        $application = [
            'type' => 'randomType',
            'status' => 'randomStatus',
            'receiver_id' => $this->group->id,
            'receiver_type' => get_class($this->group),
        ];
    
        $this->user->appliesFor($application);

        $this->assertEquals(
            array_merge(
                $application,
                [
                    'applicant_id' => $this->user->id,
                    'applicant_type' => get_class($this->user),
                ]
            ),
            $this->firstApplicationAttrs()
        );
    }

    /** @test */
    public function model_applies_using_null()
    {
        $this->user->appliesFor();

        $this->assertEquals(
            [
                'type' => 'applicant',
                'status' => 'created',
                'applicant_id' => $this->user->id,
                'applicant_type' => get_class($this->user),
                'receiver_id' => null,
                'receiver_type' => null,
            ],
            $this->firstApplicationAttrs()
        );
    }

    /** @test */
    public function model_applies_using_criteria()
    {
        $this->user->appliesFor(null, ['status' => 'randomStatus', 'type' => 'randomType']);

        $this->assertEquals(
            [
                'type' => 'randomType',
                'status' => 'randomStatus',
                'applicant_id' => $this->user->id,
                'applicant_type' => get_class($this->user),
                'receiver_id' => null,
                'receiver_type' => null,
            ],
            $this->firstApplicationAttrs()
        );
    }

    /** @test */
    public function model_applies_using_applicant_criteria_attribute()
    {
        $this->user->applicantCriteria = [
            'type' => 'randomType',
            'status' => 'randomStatus',
        ];

        $this->user->appliesFor();

        $this->assertEquals(
            [
                'type' => 'randomType',
                'status' => 'randomStatus',
                'applicant_id' => $this->user->id,
                'applicant_type' => get_class($this->user),
                'receiver_id' => null,
                'receiver_type' => null,
            ],
            $this->firstApplicationAttrs()
        );
    }

    /** @test */
    public function model_applies_using_set_applicant_criteria()
    {
        $this->user->setApplicantCriteria([
            'type' => 'randomType',
            'status' => 'randomStatus',
        ])->appliesFor();
    
        $this->assertEquals(
            [
                'type' => 'randomType',
                'status' => 'randomStatus',
                'applicant_id' => $this->user->id,
                'applicant_type' => get_class($this->user),
                'receiver_id' => null,
                'receiver_type' => null,
            ],
            $this->firstApplicationAttrs()
            );
    }

    /** @test */
    public function model_get_his_applications()
    {
        $this->user->appliesFor($this->group);

        $this->assertCount(1, $this->user->appliedApplications);
    }

    /** @test */
    public function model_has_applied_using_model()
    {
        $this->user->appliesFor($this->group);

        $this->assertTrue($this->user->hasAppliedFor($this->group));
    }

    /** @test */
    public function model_has_applied_using_array()
    {
        $application = [
            'type' => 'randomType',
            'status' => 'randomStatus',
            'receiver_id' => $this->group->id,
            'receiver_type' => get_class($this->group),
        ];
    
        $this->user->appliesFor($application);

        $this->assertTrue($this->user->hasAppliedFor($application));
    }

    /** @test */
    public function model_has_applied_using_null()
    {
        $this->user->appliesFor();
 
        $this->assertTrue($this->user->hasAppliedFor());
    }

    /** @test */
    public function model_has_applied_using_criteria()
    {
        $this->user->appliesFor(null, ['status' => 'randomStatus', 'type' => 'randomType']);
        $this->assertTrue($this->user->hasAppliedFor(null, ['status' => 'randomStatus', 'type' => 'randomType']));

        $this->user->appliesFor($this->group, ['status' => 'randomStatus', 'type' => 'randomType']);
        $this->assertFalse($this->user->hasAppliedFor($this->group, ['status' => 'anotherStatus', 'type' => 'randomType']));
        $this->assertTrue($this->user->hasAppliedFor($this->group, ['status' => 'randomStatus', 'type' => 'randomType']));
    }

    /** @test */
    public function model_has_applied_using_applicant_criteria_attribute()
    {
        $this->user->applicantCriteria = [
            'type' => 'randomType',
            'status' => 'randomStatus',
        ];
    
        $this->user->appliesFor();
    
        $this->assertTrue($this->user->hasAppliedFor(null, ['status' => 'randomStatus', 'type' => 'randomType']));
    }

    /** @test */
    public function model_get_his_applied_applications()
    {
        $this->user->appliesFor($this->group, ['type' => 'randomType']);
        $this->user->appliesFor($this->group, ['type' => 'randomType']);
        $this->user->appliesFor($this->group, ['type' => 'randomType', 'status' => 'randomStatus']);
        $this->user->appliesFor($this->group);
        
        $this->assertCount(2, $this->user->appliedApplicationsFor($this->group, ['type' => 'randomType'])->get());
        $this->assertCount(1, $this->user->appliedApplicationsFor($this->group, ['status' => 'randomStatus', 'type' => 'randomType'])->get());
        $this->assertCount(1, $this->user->appliedApplicationsFor($this->group)->get());
    }

    private function firstApplicationAttrs()
    {
        return Application::first()->only([
            'type', 'applicant_id', 'applicant_type','status','receiver_id', 'receiver_type',
        ]);
    }
}
