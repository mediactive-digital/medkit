<?php

namespace App\Repositories;

use App\Models\MailTemplate;
use App\Repositories\BaseRepository;

/**
 * Class MailTemplateRepository
 * @package App\Repositories
 * @version December 19, 2019, 9:24 am UTC
*/

class MailTemplateRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'mailable',
        'subject',
        'html_template',
        'text_template'
    ];

    /**
     * Return searchable fields
     *
     * @return array
     */
    public function getFieldsSearchable()
    {
        return $this->fieldSearchable;
    }

    /**
     * Configure the Model
     **/
    public function model()
    {
        return MailTemplate::class;
    }
}
