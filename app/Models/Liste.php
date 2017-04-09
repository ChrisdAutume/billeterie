<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Liste extends Model
{
    protected $table = 'lists';

    public function prices()
    {
        return $this->belongsToMany(Price::class)
            ->withPivot(['max_order'])
            ->withTimestamps();
    }
    public function itemList()
    {
        return $this->hasMany(Item_list::class, 'list_id');
    }
    protected function checkWildcard($mail)
    {
        $domain = substr(strrchr($mail, "@"), 1);
        if(is_null($domain)) return false;
        return ($this->itemList()->where('value', $domain)->count() > 0);
    }
    /**
     * Fonction permettant de vérifier si un mail est conforme a une liste
     * @param $mail
     * @return bool
     */
    public function checkMail($mail)
    {
        switch ($this->type)
        {
            case 'MAILIST':
                return ($this->itemList()->where('value', $mail)->count() > 0);
                break;
            case 'WILDCARD':
                return $this->checkWildcard($mail);
                break;
            default:
                return false;
        }
    }
}
