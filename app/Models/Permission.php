<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Permission
 *
 * @property int $id
 * @property string $name
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\User[] $Users
 * @property-read int|null $users_count
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Permission newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Permission newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Permission query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Permission whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Permission whereName($value)
 * @mixin \Eloquent
 */
class Permission extends Model
{
    const PERMISSIONS_FOR_USER = [];
    const PERMISSIONS_FOR_AUTHOR = 'Create Posts';
    const PERMISSIONS_FOR_EDITOR = ['Create Posts', 'Manage All Posts', 'Manage Tags'];
    const PERMISSIONS_FOR_ADMIN = ['Create Posts', 'Manage All Posts', 'Manage All Comments', 'Create And Manage Categories', 'Manage Tags', 'Manage Users'];

    public function Users()
    {
        return $this->belongsToMany(User::class);
    }

    public static function getPermissionId(string $permissionName) :int
    {
        return self::where('name', $permissionName)->value('id');
    }

    public static function getPermissionIds(array $permissionName) :array
    {
        return self::whereIn('name', $permissionName)->pluck('id')->toArray();
    }
}
