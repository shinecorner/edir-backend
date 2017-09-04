<?php namespace App\Traits;

trait Roles
{
    /**
     * @return bool
     */
    public function isAdmin()
    {
        return $this->role == 'admin';
    }

    /**
     * @return bool
     */
    public function isEmployee()
    {
        return $this->role == 'employee';
    }

    /**
     * @return bool
     */
    public function isCustomer()
    {
        return $this->role == 'customer';
    }

    /**
     * @return string
     */
    public function getRolenameAttribute()
    {
        switch ($this->role) {
            case 'admin':
                return 'Administrator';
                break;
            case 'employee':
                return 'Mitarbeiter';
                break;
            case 'customer':
                return 'Kunde';
                break;
        }
    }
}
