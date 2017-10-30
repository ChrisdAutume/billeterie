<?php

namespace App\Models;

use App\Events\BilletCreated;
use App\Events\BilletUpdated;
use App\Mail\BilletEmited;
use Illuminate\Database\Eloquent\SoftDeletes;
use Torann\Hashids\Facade as Hashids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage as Files;
use Ramsey\Uuid\Uuid as RamseyUuid;
use Milon\Barcode\Facades\DNS1DFacade as DNS1D;
use Milon\Barcode\Facades\DNS2DFacade as DNS2D;
use Barryvdh\DomPDF\Facade as PDF;

class Billet extends Model
{
    use SoftDeletes;

    protected $events = [
        'updated' => BilletUpdated::class,
    ];

    protected $dates = [
        'created_at',
        'validated_at',
        'updated_at',
        'deleted_at'
    ];

    public $fillable = [
        'name',
        'surname',
        'mail',
        'price_id',
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        if(!isset($this->attributes['uuid']))
            $this->generateUuid();
    }
    public function getBilletHash()
    {
        return crc32($this->updated_at.$this->name.$this->surname.$this->price->id);
    }
    public function getQrCodeSecurity()
    {
        return Hashids::encode($this->id, $this->getBilletHash());
    }

    public static function decryptQrCode($value):array
    {
        return Hashids::decode($value);
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

        $QR = base64_decode(DNS2D::getBarcodePNG($token, 'QRCODE,M', 9,9));
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
        $billet = preg_replace('/>\s+</', '><', $billet);
        return PDF::loadHTML($billet)->setPaper([0,0,1010,1850], 'landscape')->setWarnings(true);
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

    public static function getExistingBackground()
    {
        return Files::disk('billets')->files();
    }

}
