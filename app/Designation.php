<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Designation extends Model
{
    /**
     * The name of table
     *
     * @var string
     */
    protected $table = 'm_designations';

    /**
     * The name of primary key column
     *
     * @var string
     */
    protected $primaryKey = 'pk_desig_id';
}
