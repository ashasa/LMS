<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    /**
     * The name of table
     *
     * @var string
     */
    protected $table = 'm_menus';

    /**
     * The name of primary key column
     *
     * @var string
     */
    protected $primaryKey = 'pk_user_id';

    public static function getMenusForFole($roleId) {
        return self::where('fk_role_id', $roleId)
                ->orderBy('menu_label')
                ->get();
    }

}
