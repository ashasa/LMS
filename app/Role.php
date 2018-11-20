<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    /**
     * The name of table
     *
     * @var string
     */
    protected $table = 'm_roles';

    /**
     * The name of primary key column
     *
     * @var string
     */
    protected $primaryKey = 'pk_role_id';

}
