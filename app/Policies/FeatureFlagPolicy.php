<?php

namespace App\Policies;

use App\Models\FeatureFlag;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class FeatureFlagPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('view flags');
    }

    public function view(User $user, FeatureFlag $featureFlag): bool
    {
        return $user->hasPermissionTo('view flags')
            && $this->matchesTenant($user, $featureFlag);
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('manage flags');
    }

    public function update(User $user, FeatureFlag $featureFlag): bool
    {
        return $user->hasPermissionTo('manage flags')
            && $this->matchesTenant($user, $featureFlag);
    }

    public function delete(User $user, FeatureFlag $featureFlag): bool
    {
        return $user->hasPermissionTo('manage flags')
            && $this->matchesTenant($user, $featureFlag);
    }

    private function matchesTenant(User $user, FeatureFlag $featureFlag): bool
    {
        return $user->tenant_id === $featureFlag->environment->project->tenant_id;
    }
}
