<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Leave extends Model
{
    /**
     * The name of table
     *
     * @var string
     */
    protected $table = 'leaves';

    /**
     * The name of primary key column
     *
     * @var string
     */
    protected $primaryKey = 'pk_leave_id';

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['from_date', 'to_date'];

    public function appliedEmp()
    {
        return $this->belongsTo('App\User', 'fk_user_id');
    }

    public function backupEmp()
    {
        return $this->belongsTo('App\User', 'fk_backup_user_id');
    }
}
