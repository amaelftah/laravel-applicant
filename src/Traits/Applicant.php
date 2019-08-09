<?php

namespace Te7aHoudini\LaravelApplicant\Traits;

use Illuminate\Support\Arr;
use Te7aHoudini\LaravelApplicant\Models\Application;

trait Applicant
{
    /**
     * creates a new application model object for this model.
     *
     * @param null|array|object $model
     * @param array $criteria
     * @return \Te7aHoudini\LaravelApplicant\Models\Application
     */
    public function appliesFor($model = null, $criteria = [])
    {
        $applicationType = Arr::get($criteria, 'type', Arr::get($this->applicantCriteria, 'type', 'applicant'));

        $applicationStatus = Arr::get($criteria, 'status', Arr::get($this->applicantCriteria, 'status', 'created'));

        if (is_null($model)) {
            $data = [
                'type' => $applicationType,
                'status' => $applicationStatus,
            ];
        }

        if (is_array($model)) {
            $data = array_merge(
                $model,
                [
                    'type' => $model['type'] ?? $applicationType,
                    'status' => $model['status'] ?? $applicationStatus,
                ]
            );
        }

        if (is_object($model)) {
            $data = [
                'type' => $applicationType,
                'status' => $applicationStatus,
                'receiver_id' => $model->id,
                'receiver_type' => get_class($model),
            ];
        }

        return Application::create(array_merge($data, ['applicant_id' => $this->id, 'applicant_type' => get_class($this)]));
    }

    /**
     * check if current model has applied on application or not.
     *
     * @param null|array|object $model
     * @param array $criteria
     * @return bool
     */
    public function hasAppliedFor($model = null, $criteria = [])
    {
        return $this->appliedApplicationsFor($model, $criteria)->exists();
    }

    /**
     * get applications that model has applied on.
     *
     * @param null|array|object $model
     * @param array $criteria
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function appliedApplicationsFor($model = null, $criteria = [])
    {
        $applicationType = Arr::get($criteria, 'type', Arr::get($this->applicantCriteria, 'type', 'applicant'));

        $applicationStatus = Arr::get($criteria, 'status', Arr::get($this->applicantCriteria, 'status', 'created'));

        $query = $this->appliedApplications();

        if (is_null($model)) {
            $query->where('type', $applicationType)->where('status', $applicationStatus);
        }

        if (is_array($model)) {
            $data = array_merge(
                $model,
                [
                    'type' => $model['type'] ?? $applicationType,
                    'status' => $model['status'] ?? $applicationStatus,
                ]
            );

            foreach ($data as $column => $value) {
                $query->where($column, $value);
            }
        }

        if (is_object($model)) {
            $query->where('type', $applicationType)->where('status', $applicationStatus)->where('receiver_id', $model->id)->where('receiver_type', get_class($model));
        }

        return $query;
    }

    /**
     * sets applicantCriteria attribute and returns $this to allow fluent api.
     *
     * @param array $criteria
     * @return self
     */
    public function setApplicantCriteria($criteria = [])
    {
        $this->applicantCriteria = $criteria;

        return $this;
    }

    /**
     * returns this model applied applications.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function appliedApplications()
    {
        return $this->morphMany(Application::class, 'applicant');
    }
}
