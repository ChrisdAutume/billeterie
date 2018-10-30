<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

class Partner extends Model
{
    public $table = 'partners';
    public $timestamps = false;

    public $primaryKey = 'id';

    public $fillable = [
        'name',
        'link',
        'image'
    ];

    /**
	 * Define constraints of the Model's attributes for store action
	 *
	 * @return array
	 */
	public static function storeRules() {
		return [
			'name' => 'required|string',
			'link' => 'required|string',
      'image' => 'required|string',
		];
	}
}
