<?php

namespace App\Repositories;

use App\Models\MailTemplate;
use App\Repositories\BaseRepository;

/**
 * Class MailTemplateRepository
 * @package App\Repositories
 * @version January 24, 2020, 3:38 pm CET
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
