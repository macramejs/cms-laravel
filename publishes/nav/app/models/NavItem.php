<?php

namespace App\Models;

use App\Models\Types\NavType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Macrame\Contracts\Tree\Tree;
use Macrame\Tree\Traits\IsTree;

class NavItem extends Model implements Tree
{
    use HasFactory, IsTree;

    /**
     * Attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'parent_id', 'order_column', 'title', 'route', 'type',
    ];


    /**
     * Attribute casts.
     *
     * @var array
     */
    protected $casts = [
        'type' => NavType::class
    ];
}
