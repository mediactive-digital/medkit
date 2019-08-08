<?php

namespace MediactiveDigital\MedKit\Http\Controllers\Back;

use MedKit\Http\Controllers\Controller;
use MediactiveDigital\MedKit\Facades\MedKit;

class UiController extends Controller {

    /**
     * Display UI kit
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        return MedKit::view('medKitTheme::kit-ui.back.index');
    }
}
