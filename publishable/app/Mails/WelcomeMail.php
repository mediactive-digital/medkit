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

    public function __construct(User $user) {

        $this->name = $user->first_name . ' ' . $user->name;
    }
    
    public function getHtmlLayout(): string {

        return view('emails.default', ['body' => '{{{ body }}}'])->render();
    }
}
