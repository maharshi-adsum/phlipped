<?php

namespace App\Traits;

trait RequestTrait
{
    protected $authToken = null;

    /**
     * This will chek user is superadmin or not
     *
     * @return boolean
     */
    public function isSuperAdmin() :bool
    {
        return ($this->currentUser->getTableName() === 'admins');
    }

    /**
     * Ths will check user is company admin or not
     *
     * @return boolean
     */
    public function isCompanyAdmin() :bool
    {
        return ($this->currentUser->type == 0 && $this->currentUser->getTableName() === 'users');
    }

    /**
     * Ths will check user is affiliate or not
     *
     * @return boolean
     */
    public function isAffiliateUser() :bool
    {
        return ($this->currentUser->type == 1 && $this->currentUser->getTableName() === 'users');
    }
}
