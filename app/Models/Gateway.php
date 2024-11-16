<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Gateway
 *
 * @property string $id
 * @property int $sys_uptime
 * @property int $sys_memfree
 * @property float $sys_load
 * @property int $wifidog_uptime
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Gateway whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Gateway whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Gateway whereSysLoad($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Gateway whereSysMemfree($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Gateway whereSysUptime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Gateway whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Gateway whereWifidogUptime($value)
 * @mixin \Eloquent
 */
class Gateway extends Model
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;

    protected $fillable = ['id', 'sys_uptime', 'sys_memfree', 'sys_load', 'wifidog_uptime', 'created_at', 'updated_at'];
    protected $primaryKey = 'id';
    public $incrementing = false;
}
