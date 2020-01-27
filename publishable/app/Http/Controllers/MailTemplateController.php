<?php

namespace App\Http\Controllers;

use App\Http\Controllers\AppBaseController;

use App\Http\Requests\MailTemplateRequest;

use App\Models\MailTemplate;

use App\Repositories\MailTemplateRepository;

use App\DataTables\MailTemplateDataTable;

use App\Mails\WelcomeMail;

use Kris\LaravelFormBuilder\FormBuilder;

use Flash;
use Auth;
use Mail;

class MailTemplateController extends AppBaseController {

    /**
     * @var \App\Repositories\MailTemplateRepository $mailTemplateRepository
     */
    private $mailTemplateRepository;

    public function __construct(MailTemplateRepository $mailTemplateRepo) {

        $this->authorizeResource(\App\Models\MailTemplate::class);

        $this->mailTemplateRepository = $mailTemplateRepo;
    }

    /**
     * Display a listing of the MailTemplate.
     *
     * @param \App\DataTables\MailTemplateDataTable $mailTemplateDataTable
     *
     * @return \Illuminate\Http\Response
     */
    public function index(MailTemplateDataTable $mailTemplateDataTable) {

        return $mailTemplateDataTable->render('mail_templates.index');
    }

    /**
     * Show the form for creating a new MailTemplate.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(MailTemplate $mailTemplate, FormBuilder $formBuilder) {

        $form = $formBuilder->create('App\Forms\MailTemplateForm', [
            'method' => 'POST',
            'url' => route('back.mail_templates.index')
        ]);

        return view('mail_templates.create')
            ->with('form', $form);
    }

    /**
     * Store a newly created MailTemplate in storage.
     *
     * @param \App\Http\Requests\MailTemplateRequest $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(MailTemplateRequest $request) {

        $input = $request->all();

        $mailTemplate = $this->mailTemplateRepository->create($input);

        Flash::success(_i('Le modèle de courrier a été enregistré avec succès'));

        return redirect(route('back.mail_templates.index'));
    }

    /**
     * Display the specified MailTemplate.
     *
     * @param \App\Models\MailTemplate $mailTemplate
     *
     * @return \Illuminate\Http\Response
     */
    public function show(MailTemplate $mailTemplate) {

        $id = $mailTemplate->id;
        $mailTemplate = $this->mailTemplateRepository->find($id);

        if (empty($mailTemplate)) {

            Flash::error(_i('Modèle de courrier introuvable'));

            return redirect(route('back.mail_templates.index'));
        }

        return view('mail_templates.show')
            ->with('mailTemplate', $mailTemplate)
            ->with('templateVars', $mailTemplate->getVariables());
    }

    /**
     * Show the form for editing the specified MailTemplate.
     *
     * @param \App\Models\MailTemplate $mailTemplate
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(MailTemplate $mailTemplate, FormBuilder $formBuilder) {

        $id = $mailTemplate->id;
        $mailTemplate = $this->mailTemplateRepository->find($id);

        if (empty($mailTemplate)) {

            Flash::error(_i('Modèle de courrier introuvable'));

            return redirect(route('back.mail_templates.index'));
        }

        $form = $formBuilder->create('App\Forms\MailTemplateForm', [
            'method' => 'patch',
            'url' => route('back.mail_templates.update', $mailTemplate->id),
            'model' => $mailTemplate
        ]);

        return view('mail_templates.edit')
            ->with('form', $form)
            ->with('mailTemplate', $mailTemplate);
    }

    /**
     * Update the specified MailTemplate in storage.
     *
     * @param \App\Models\MailTemplate $mailTemplate
     * @param \App\Http\Requests\MailTemplateRequest $request
     *
     * @return \Illuminate\Http\Response
     */
    public function update(MailTemplate $mailTemplate, MailTemplateRequest $request) {

        $id = $mailTemplate->id;
        $mailTemplate = $this->mailTemplateRepository->find($id);

        if (empty($mailTemplate)) {

            Flash::error(_i('Modèle de courrier introuvable'));

            return redirect(route('back.mail_templates.index'));
        }

        $mailTemplate = $this->mailTemplateRepository->update($request->all(), $id);

        Flash::success(_i('Le modèle de courrier a été mis à jour avec succès'));

        return redirect(route('back.mail_templates.index'));
    }

    /**
     * Remove the specified MailTemplate from storage.
     *
     * @param \App\Models\MailTemplate $mailTemplate
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(MailTemplate $mailTemplate) {

        $id = $mailTemplate->id;
        $mailTemplate = $this->mailTemplateRepository->find($id);

        if (empty($mailTemplate)) {

            Flash::error(_i('Modèle de courrier introuvable'));

            return redirect(route('back.mail_templates.index'));
        }

        $this->mailTemplateRepository->delete($id);

        Flash::success(_i('Le modèle de courrier a été supprimé avec succès'));

        return redirect(route('back.mail_templates.index'));
    }

    /**
     * Test the specified MailTemplate.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function test(int $id) {

        $mailTemplate = $this->mailTemplateRepository->find($id);

        if (empty($mailTemplate)) {

            Flash::error(_i('Modèle de courrier introuvable'));

            return redirect(route('back.mail_templates.index'));
        }

        $user = Auth::user();

        Mail::to($user->email)->send(new WelcomeMail($user));

        Flash::success(_i('Le test du modèle de courrier a été envoyé avec succès'));

        return redirect(route('back.mail_templates.index'));
    }
}
