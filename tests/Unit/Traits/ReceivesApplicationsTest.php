<?php

namespace Te7aHoudini\LaravelApplicant\Tests\Unit\Traits;

use Illuminate\Support\Carbon;
use Te7aHoudini\LaravelApplicant\Tests\TestCase;
use Te7aHoudini\LaravelApplicant\Models\Application;

class ReceivesApplicationsTest extends TestCase
{
    /** @test */
    public function model_process_application_using_model()
    {
        Carbon::setTestNow(Carbon::create());
        $this->user->appliesFor($this->group);

        $this->group->processApplicationFrom($this->user);

        $this->assertEquals(
            [
                'type' => 'applicant',
                'applicant_id' => $this->user->id,
                'applicant_type' => get_class($this->user),
                'receiver_id' => $this->group->id,
                'receiver_type' => get_class($this->group),
                'status' => 'processed',
                'status_updated_by_id' => $this->group->id,
                'status_updated_by_type' => get_class($this->group),
                'status_updated_at' => Carbon::now(),
            ],
            $this->firstApplicationAttrs()
        );
    }

    /** @test */
    public function model_process_application_using_array()
    {
        Carbon::setTestNow(Carbon::create());
        $this->user->appliesFor([
            'type' => 'randomType',
            'status' => 'randomStatus',
            'receiver_id' => $this->group->id,
            'receiver_type' => get_class($this->group),
        ]);

        $this->group->processApplicationFrom([
            'type' => 'randomType',
            'status' => 'randomStatus',
            'applicant_id' => $this->user->id,
            'applicant_type' => get_class($this->user),
        ]);
    
        $this->assertEquals(
            [
                'type' => 'randomType',
                'applicant_id' => $this->user->id,
                'applicant_type' => get_class($this->user),
                'receiver_id' => $this->group->id,
                'receiver_type' => get_class($this->group),
                'status' => 'processed',
                'status_updated_by_id' => $this->group->id,
                'status_updated_by_type' => get_class($this->group),
                'status_updated_at' => Carbon::now(),
            ],
            $this->firstApplicationAttrs()
        );
    }

    /** @test */
    public function model_process_application_using_model_and_set_a_new_status()
    {
        Carbon::setTestNow(Carbon::create());
        $this->user->appliesFor($this->group);
    
        $this->group->processApplicationFrom($this->user, 'randomNewStatus');
    
        $this->assertEquals(
            [
                'type' => 'applicant',
                'applicant_id' => $this->user->id,
                'applicant_type' => get_class($this->user),
                'receiver_id' => $this->group->id,
                'receiver_type' => get_class($this->group),
                'status' => 'randomNewStatus',
                'status_updated_by_id' => $this->group->id,
                'status_updated_by_type' => get_class($this->group),
                'status_updated_at' => Carbon::now(),
            ],
            $this->firstApplicationAttrs()
        );
    }

    /** @test */
    public function model_process_application_using_criteria_and_set_a_new_status()
    {
        Carbon::setTestNow(Carbon::create());
        $this->user->appliesFor($this->group, ['type' => 'randomType']);
    
        $this->group->processApplicationFrom($this->user, ['type' => 'randomType'], 'randomNewStatus');
    
        $this->assertEquals(
            [
                'type' => 'randomType',
                'applicant_id' => $this->user->id,
                'applicant_type' => get_class($this->user),
                'receiver_id' => $this->group->id,
                'receiver_type' => get_class($this->group),
                'status' => 'randomNewStatus',
                'status_updated_by_id' => $this->group->id,
                'status_updated_by_type' => get_class($this->group),
                'status_updated_at' => Carbon::now(),
            ],
            $this->firstApplicationAttrs()
        );
    }

    /** @test */
    public function model_process_application_using_receiver_criteria_attribute()
    {
        Carbon::setTestNow(Carbon::create());
        $this->group->receiverCriteria = [
            'type' => 'randomType',
            'status' => 'randomStatus',
        ];
    
        $this->user->appliesFor($this->group, ['type' => 'randomType', 'status' => 'randomStatus']);
        $this->group->processApplicationFrom($this->user);
        
        $this->assertEquals(
            [
                'type' => 'randomType',
                'applicant_id' => $this->user->id,
                'applicant_type' => get_class($this->user),
                'receiver_id' => $this->group->id,
                'receiver_type' => get_class($this->group),
                'status' => 'processed',
                'status_updated_by_id' => $this->group->id,
                'status_updated_by_type' => get_class($this->group),
                'status_updated_at' => Carbon::now(),
            ],
            $this->firstApplicationAttrs()
            );
    }

    /** @test */
    public function model_process_application_using_set_receiver_criteria()
    {
        Carbon::setTestNow(Carbon::create());
    
        $this->user->appliesFor($this->group, ['type' => 'randomType', 'status' => 'randomStatus']);
        $this->group->setReceiverCriteria([
            'type' => 'randomType',
            'status' => 'randomStatus',
        ])->processApplicationFrom($this->user);
        
        $this->assertEquals(
            [
                'type' => 'randomType',
                'applicant_id' => $this->user->id,
                'applicant_type' => get_class($this->user),
                'receiver_id' => $this->group->id,
                'receiver_type' => get_class($this->group),
                'status' => 'processed',
                'status_updated_by_id' => $this->group->id,
                'status_updated_by_type' => get_class($this->group),
                'status_updated_at' => Carbon::now(),
            ],
            $this->firstApplicationAttrs()
            );
    }

    /** @test */
    public function model_has_received_application_using_model()
    {
        $this->user->appliesFor($this->group);
    
        $this->assertTrue($this->group->hasReceivedApplicationFrom($this->user));
    }

    /** @test */
    public function model_has_received_application_using_array()
    {
        $application = [
            'type' => 'randomType',
            'status' => 'randomStatus',
            'receiver_id' => $this->group->id,
            'receiver_type' => get_class($this->group),
        ];
        
        $this->user->appliesFor($application);

        $this->assertTrue($this->group->hasReceivedApplicationFrom([
            'type' => 'randomType',
            'status' => 'randomStatus',
            'applicant_id' => $this->user->id,
            'applicant_type' => get_class($this->user),
        ]));
    }

    /** @test */
    public function model_has_received_application_using_criteria()
    {
        $this->user->appliesFor($this->group, ['status' => 'randomStatus', 'type' => 'randomType']);
        $this->assertTrue($this->group->hasReceivedApplicationFrom($this->user, ['status' => 'randomStatus', 'type' => 'randomType']));
    
        $this->user->appliesFor($this->group, ['status' => 'randomStatus', 'type' => 'randomType']);
        $this->assertFalse($this->group->hasReceivedApplicationFrom($this->user, ['status' => 'anotherStatus', 'type' => 'randomType']));
        $this->assertTrue($this->group->hasReceivedApplicationFrom($this->user, ['status' => 'randomStatus', 'type' => 'randomType']));
    }

    /** @test */
    public function model_get_his_received_applications()
    {
        $this->user->appliesFor($this->group);
    
        $this->assertCount(1, $this->group->receivedApplications);
    }

    /** @test */
    public function model_get_his_received_applications_using_criteria()
    {
        $this->user->appliesFor($this->group, ['type' => 'randomType']);
        $this->user->appliesFor($this->group, ['type' => 'randomType']);
        $this->user->appliesFor($this->group, ['type' => 'randomType', 'status' => 'randomStatus']);
        $this->user->appliesFor($this->group);
        
        $this->assertCount(2, $this->group->receivedApplicationsFrom($this->user, ['type' => 'randomType'])->get());
        $this->assertCount(1, $this->group->receivedApplicationsFrom($this->user, ['status' => 'randomStatus', 'type' => 'randomType'])->get());
        $this->assertCount(1, $this->group->receivedApplicationsFrom($this->user)->get());
    }

    private function firstApplicationAttrs()
    {
        return Application::first()->only([
            'type', 'applicant_id', 'applicant_type','status','receiver_id', 'receiver_type',
            'status_updated_by_id', 'status_updated_by_type', 'status_updated_at',
        ]);
    }
}
