<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

class Event extends Model
{
    public $table = 'events';
    public $timestamps = false;

    public $primaryKey = 'id';

    public $fillable = [
        'name',
        'description',
        'place',
        'image',
        'start_at',
        'end_at'
    ];

    /**
	 * Define constraints of the Model's attributes for store action
	 *
	 * @return array
	 */
	public static function storeRules() {
		return [
			'name' => 'required|string',
			'description' => 'required|string',
			'place' => 'required|string',
      'start_at_date' => 'required|date|date_format:Y-m-d|before_or_equal:end_at_date',
      'end_at_date'=> 'required|date|date_format:Y-m-d|after_or_equal:start_at_date',
      'start_at_hour' => 'required|date_format:H:i',
      'end_at_hour' => 'required|date_format:H:i',
      'image' => 'required|string',
		];
	}
}
