<?php

namespace App\Models;

use App\Events\BilletCreated;
use App\Events\BilletUpdated;
use App\Mail\BilletEmited;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Mail;
use Ramsey\Uuid\Uuid as RamseyUuid;
use Milon\Barcode\Facades\DNS1DFacade as DNS1D;
use Milon\Barcode\Facades\DNS2DFacade as DNS2D;
use Barryvdh\DomPDF\Facade as PDF;

class Billet extends Model
{

    protected $events = [
        'created' => BilletCreated::class,
        'updated' => BilletUpdated::class,
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        if(!isset($this->attributes['uuid']))
            $this->generateUuid();
    }

    public function getQrCodeSecurity()
    {
        return $this->uuid .'|'.sha1($this->updated_at.$this->name.$this->surname.$this->price->id);
    }

    public function getDownloadSecurity()
    {
        return sha1($this->updated_at.$this->name.$this->surname.$this->uuid.$this->price->id);
    }

    public function generateUuid()
    {
        $this->uuid = RamseyUuid::uuid4()->toString();
    }

    public function options()
    {
        return $this->belongsToMany(Option::class, 'billet_option')
            ->withPivot(['qty', 'amount']);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function price()
    {
        return $this->belongsTo(Price::class);
    }

    public function base64Barcode()
    {
        $uuid = explode('-', $this->uuid);
        $pattern = $this->id.'-'.$uuid[4];
        return DNS1D::getBarcodePNG($pattern, "C128", 2.5,100);
    }

    public function base64QrCode()
    {
        $token = $this->getQrCodeSecurity();

        $QR = base64_decode(DNS2D::getBarcodePNG($token, 'QRCODE,H', 4,4));
        if(file_exists(public_path('img/billets/logo.png'))) {
            $logo = imagecreatefromstring(file_get_contents(public_path('img/billets/logo.png')));
            $QR = imagecreatefromstring($QR);
            $QR_width = imagesx($QR);
            $QR_height = imagesy($QR);

            $logo_width = imagesx($logo);
            $logo_height = imagesy($logo);

            $logo_qr_width = $QR_width/3;
            $scale = $logo_width/$logo_qr_width;
            $logo_qr_height = $logo_height/$scale;

            imagecopyresampled($QR, $logo, $QR_width/3, $QR_height/3, 0, 0, $logo_qr_width, $logo_qr_height, $logo_width, $logo_height);
            ob_start();
            imagepng($QR);
            $QR = ob_get_contents();
            ob_end_clean ();
        }

        return base64_encode($QR);
    }

    public function outputBillet()
    {
        $billet = view('billets.billet', ['billet'=>$this])->render();
        return PDF::loadHTML($billet)->setPaper([0,0,1010,1850], 'landscape')->setWarnings(false);
    }

    public function sendToMail()
    {
        if($this->price()->first()->sendBillet)
            Mail::to($this->mail)->queue(new BilletEmited($this));
    }

    public function setFieldsAttribute($value)
    {
        if(is_array($value))
            $this->attributes['fields'] = json_encode($value);
    }

    public function getFieldsAttribute($value)
    {
        return json_decode($value);
    }

    public function getRouteKeyName()
    {
        return 'uuid';
    }

}
