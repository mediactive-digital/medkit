<?php

namespace App\Models;

use Carbon\Carbon;

use Illuminate\Database\Eloquent\SoftDeletes;

use Wildside\Userstamps\Userstamps;

use Spatie\Translatable\HasTranslations;

use Spatie\MailTemplates\Models\MailTemplate as SpatieMailTemplate;

/**
 * Class MailTemplate
 * @package App\Models
 * @version January 27, 2020, 10:45 am CET
 *
 * @property string mailable
 * @property string|array subject
 * @property string|array html_template
 * @property string|array text_template
 */
class MailTemplate extends SpatieMailTemplate {

    use SoftDeletes;
    use Userstamps;
    use HasTranslations;

    public $table = 'mail_templates';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    const DELETED_AT = 'deleted_at';

    const CREATED_BY = 'created_by';
    const UPDATED_BY = 'updated_by';
    const DELETED_BY = 'deleted_by';

    const TEST_MAIL_CLASS = 'App\Mails\WelcomeMail';

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

    public $translatable = [
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
        'created_by' => 'integer',
        'updated_by' => 'integer',
        'deleted_by' => 'integer'
    ];
}
