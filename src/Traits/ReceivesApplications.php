<?php

namespace Te7aHoudini\LaravelApplicant\Traits;

use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Te7aHoudini\LaravelApplicant\Models\Application;

trait ReceivesApplications
{
    /**
     * updates application record in db.
     *
     * @param array|object $model
     * @param array $criteria
     * @return bool
     */
    public function processApplicationFrom($model, $criteria = [], $newStatus = 'processed')
    {
        $applicationType = Arr::get($criteria, 'type', Arr::get($this->receiverCriteria, 'type', 'applicant'));

        $applicationStatus = Arr::get($criteria, 'status', Arr::get($this->receiverCriteria, 'status', 'created'));

        $query = Application::query();

        if (is_array($model)) {
            $query->where('type', $model['type'] ?? $applicationType)
                ->where('status', $model['status'] ?? $applicationStatus)
                ->where('applicant_id', $model['applicant_id'])
                ->where('applicant_type', $model['applicant_type']);
        }

        if (is_object($model)) {
            $query->where('type', $applicationType)
                ->where('status', $applicationStatus)
                ->where('applicant_id', $model->id)
                ->where('applicant_type', get_class($model));
        }

        return $query->update([
            'status' => is_string($criteria) ? $criteria : $newStatus,
            'status_updated_by_id' => $this->id,
            'status_updated_by_type' => get_class($this),
            'status_updated_at' => Carbon::now(),
        ]);
    }

    /**
     *  check if current model has received application from other model or not.
     *
     * @param array|object $model
     * @param array $criteria
     * @return bool
     */
    public function hasReceivedApplicationFrom($model, $criteria = [])
    {
        return $this->receivedApplicationsFrom($model, $criteria)->exists();
    }

    /**
     * get applications that applied on this model and this model was the receiver.
     *
     * @param array|object $model
     * @param array $criteria
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function receivedApplicationsFrom($model, $criteria = [])
    {
        $applicationType = Arr::get($criteria, 'type', Arr::get($this->applicantCriteria, 'type', 'applicant'));

        $applicationStatus = Arr::get($criteria, 'status', Arr::get($this->applicantCriteria, 'status', 'created'));

        $query = $this->receivedApplications();

        if (is_array($model)) {
            $query->where('type', $model['type'] ?? $applicationType)
                ->where('status', $model['status'] ?? $applicationStatus)
                ->where('applicant_id', $model['applicant_id'])
                ->where('applicant_type', $model['applicant_type']);
        }

        if (is_object($model)) {
            $query->where('type', $applicationType)
                ->where('status', $applicationStatus)
                ->where('applicant_id', $model->id)
                ->where('applicant_type', get_class($model));
        }

        return $query;
    }

    /**
     * sets receiverCriteria attribute and returns $this to allow fluent api.
     *
     * @param array $criteria
     * @return self
     */
    public function setReceiverCriteria($criteria = [])
    {
        $this->receiverCriteria = $criteria;

        return $this;
    }

    /**
     * returns received applications for that model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function receivedApplications()
    {
        return $this->morphMany(Application::class, 'receiver');
    }
}
