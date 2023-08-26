<?php

namespace App\Traits;

use App\Models\Permission;
use App\Models\Role;

trait HasRoles
{
    /**
     * Assign the given role to the user.
     *
     * @param string $role
     *
     * @return mixed
     */
    public function assignRole(string $role)
    {
        return $this->roles()->save(
            Role::whereName($role)->firstOrFail()
        );
    }

    /**
     * Determine if the user may perform the given permission.
     *
     * @param Permission $permission
     *
     * @return bool
     */
    public function hasPermission(Permission $permission): bool
    {
        return $this->hasRole($permission->roles);
    }

    /**
     * @param $permission
     * @return bool
     */
    public function hasPermissionTo($permission): bool
    {
        return $this->hasPermissionThroughRole($permission) || $this->hasPermission($permission);
    }

    /**
     * @param $permission
     * @return bool
     */
    public function hasPermissionThroughRole($permission): bool
    {
        foreach ($permission->roles as $role) {
            if ($this->hasRole($role)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Determine if the user has the given role.
     *
     * @param mixed $roles
     *
     * @return boolean
     */
    public function hasRole($roles): bool
    {
        if ($this->isAdmin()) {
            return true;
        }

        if (Role::ROLE_AGENT === $roles) {
            return true;
        }
        if (is_string($roles)) {
            return $this->roles->contains('name', $roles) ||
                $this->roles->whereIn('name', Role::getRolePresetsName($roles))->count();
        }
        if (is_array($roles)) {
            foreach ($roles as $role) {
                if ($this->hasRole($role)) {
                    return true;
                }
            }
            return false;
        }

        return !!$roles->intersect($this->roles)->count();
    }

    /**
     * Determine if the user an administrator
     *
     * @return bool
     */
    public function isAdmin(): bool
    {
        return $this->roles()->whereIn('name', Role::getRolePresetsName(Role::ROLE_ADMIN))->exists();
    }

    /**
     * Determine if the user an agent
     *
     * @return bool
     */
    public function isAgent(): bool
    {
        return $this->roles()->whereIn('name', Role::getRolePresetsName(Role::ROLE_AGENT))->exists();
    }

    /**
     * Determine if the user a traveler
     *
     * @return bool
     */
    public function isTraveler(): bool
    {
        return $this->roles()->whereIn('name', Role::getRolePresetsName(Role::ROLE_TRAVELER))->exists();
    }

    /**
     * check role has permission to origin
     *
     * @param string $origin
     * @return bool
     */
    public function hasPermissionToOrigin(string $origin): bool
    {
        $permissionByOrigin = config('permission.permission_by_origin');
        if ($permissionByOrigin) {
            $travelerOrigin = config('permission.traveler_origin');
            $agentOrigin = config('permission.agent_origin');
            $adminOrigin = config('permission.admin_origin');
            $isAdmin = $this->isAdmin();
            $isTraveler = $this->isTraveler();
            if ($isAdmin && $origin === $adminOrigin) {
                return true;
            }
            if ($isTraveler && $origin === $travelerOrigin) {
                return true;
            }
            if (!$isAdmin && !$isTraveler && $origin === $agentOrigin) {
                return true;
            }

            return false;
        }
        return true;
    }
}
