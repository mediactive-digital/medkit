<?php

namespace App\Models;

use Eloquent as Model;

use Carbon\Carbon;

use Illuminate\Database\Eloquent\SoftDeletes;

use Wildside\Userstamps\Userstamps;
use Spatie\MailTemplates\Models\MailTemplate as SpatieMailTemplate;
use Spatie\MailTemplates\Interfaces\MailTemplateInterface;

/**
 * Class MailTemplate
 * @package App\Models
 * @version December 19, 2019, 9:24 am UTC
 *
 * @property string mailable
 * @property string subject
 * @property string html_template
 * @property string text_template
 */
class MailTemplate extends SpatieMailTemplate implements MailTemplateInterface 
{
    
    use SoftDeletes;
    use Userstamps;

    public $table = 'mail_templates';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    const DELETED_AT = 'deleted_at';

    const CREATED_BY = 'created_by';
    const UPDATED_BY = 'updated_by';
    const DELETED_BY = 'deleted_by';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected $primaryKey = 'id';

    public $fillable = [
        'mailable',
        'subject',
        'html_template',
        'text_template'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'mailable' => 'string',
        'subject' => 'string',
        'html_template' => 'string',
        'text_template' => 'string',
        'created_by' => 'integer',
        'updated_by' => 'integer',
        'deleted_by' => 'integer'
    ];
    
    
}
