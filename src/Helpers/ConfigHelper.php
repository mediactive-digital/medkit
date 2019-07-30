<?php

namespace MediactiveDigital\MedKit\Helpers;
use Illuminate\Filesystem\Filesystem;

class ConfigHelper {


    /**
     * Replace config by another
     *
     * @param [type] $configFile
     * @param [type] $sectionTitle
     * @param [type] $nextSectionTitle
     * @param [type] $authConfigGuards
     * @return void
     */
    public static function replaceArrayInConfig( $configFile, $sectionTitle, $nextSectionTitle, array $newConfig ){

		$filesystem = app(Filesystem::class);
		$config = $filesystem->get($configFile);

		$sectionTitle = "| ".$sectionTitle;
		if( is_string( $nextSectionTitle ) ){
			$nextSectionTitle = "| ".$nextSectionTitle;
		}

        $startSectionPos = strpos( $config, $sectionTitle,0 );      //find start title
		$endOfSectionDescription = strpos( $config, '*/', $startSectionPos ) +2;    //find end of start comment

		if( is_string( $nextSectionTitle ) ){

			$nextSectionPos = strpos( $config, $nextSectionTitle, $endOfSectionDescription )-4;   //find next start title line

			//we need to recreate start of comment (simplicity shorcut)
			$startSectionComment = "    /*\n    |--------------------------------------------------------------------------\r\n";
			$eof = "\n\n"
				   .$startSectionComment /* Start commment of next Title */
				   .substr( $config, $nextSectionPos ); /* End of file */
				   

		}else{	//nothing after
			$eof = "\n\n];";
		}

		

        /**
         * Replacement
         */
        $newConfig = FormatHelper::writeArrayToPhp( $newConfig );
        $config = substr( $config, 0, $endOfSectionDescription )."\n\n" /* Start of file */
                    .$newConfig  /* new Array config */
                    .$eof;

        // write to file
        return $filesystem->put($configFile, $config );

    }
}