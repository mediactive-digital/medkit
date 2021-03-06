<?php

namespace MediactiveDigital\MedKit\Http\Controllers\Back;

use MediactiveDigital\MedKit\Http\Controllers\Controller;

class UiController extends Controller {

    /**
     * Display UI kit
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
    	
        return view('medKitTheme::kit-ui.back.index');
    }
}
