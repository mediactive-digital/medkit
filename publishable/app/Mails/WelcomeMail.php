<?php

namespace App\Mails;

use Spatie\MailTemplates\TemplateMailable;

use App\Models\MailTemplate;
use App\Models\User;

class WelcomeMail extends TemplateMailable {

    /** 
     * @var string 
     */
    protected static $templateModelClass = MailTemplate::class;

    /** 
     * @var string 
     */
    public $name;
    
    /** 
     * @var string 
     */
    public $email;

    public function __construct(User $user) {

        $this->name = $user->name;
        $this->email = $user->email;
    }
    
    public function getHtmlLayout(): string {

        /**
         * In your application you might want to fetch the layout from an external file or Blade view.
         * 
         * External file: `return file_get_contents(storage_path('mail-layouts/main.html'));`
         * 
         * Blade view: `return view('mailLayouts.main', $data)->render();`
         */
        
        return '<header>Site name!</header>{{{ body }}}<footer>Copyright 2020</footer>';
    }
}
