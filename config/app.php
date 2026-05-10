<?php
return ['name'=>env('APP_NAME','Construction Ledger'),'env'=>env('APP_ENV','production'),'debug'=>(bool) env('APP_DEBUG',false),'url'=>env('APP_URL','http://localhost'),'timezone'=>'Europe/Moscow','locale'=>'ru','fallback_locale'=>'ru','faker_locale'=>'ru_RU','key'=>env('APP_KEY'),'cipher'=>'AES-256-CBC','providers'=>[App\Providers\AppServiceProvider::class]];
