<?php

namespace App\Models;

use App\Casts\MenuLinkCast;
use Macrame\Tree\Traits\IsTree;
use Macrame\Contracts\Tree\Tree;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MenuItem extends Model implements Tree
{
    use HasFactory, IsTree;

    /**
     * Attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'parent_id', 'order_column', 'title', 'link', 'new_tab', 'menu_id',
    ];

    /**
     * Attribute casts.
     *
     * @var array
     */
    protected $casts = [
        'link'    => MenuLinkCast::class,
        'new_tab' => 'boolean',
    ];

    /**
     * The corresponding menu.
     *
     * @return HasOne
     */
    public function menu(): HasOne
    {
        return $this->hasOne(Menu::class);
    }
}