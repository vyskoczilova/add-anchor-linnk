<?php
/**
 * This code has been customized on 2020-10-18T2246+0200
 * to handle paragraphs instead of headings.
 * Last modified:  2020-10-19T1315+0200
 */
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die( nl2br( "\r\n\r\n&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;en:&nbsp;&nbsp;&nbsp;&nbsp;This PHP file cannot be displayed in the browser.\r\n&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;For a quick look, please open this content as a plain text file if there is any with the same name.\r\n&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;You may also wish to download the target and open the file in a text editor.\r\n\r\n&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;fr&nbsp;:&nbsp;&nbsp;&nbsp;&nbsp;Ce fichier ne peut pas s'afficher dans le navigateur.\r\n&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Pour un aperçu du contenu, ouvrez s.v.p. le fichier texte de même nom s’il existe.\r\n&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Vous pouvez aussi télécharger la cible du lien et ouvrir le fichier dans votre éditeur de texte." ) );
}

if ( ! class_exists( 'Add_Anchor_Links' ) ) {
    class Add_Anchor_Links {  

        /**
        - Constructor
        */
        function __construct() {
        
            add_filter('the_content', array(&$this,'add_anchor_links_to_the_content') );            
            
        }

        public function add_anchor_links_to_the_content ( $content ) {

            if ( is_singular( add_anchor_links_post_types( true ) ) ) {
                return $this->add_anchors( $content );
            } 

            return $content;

        }


        /**
         - Add Links
         * Replace headings with an anchor
         */
        private function add_anchors( $text ) {

            // search for headlines
            $pattern = '#<p(?: [^>]+)?>([^ ]+( [^ ]*){9})(.*?)</p>#is';
            preg_match_all( $pattern , $text , $headlines, PREG_OFFSET_CAPTURE );

            $offset = 0;

            if( $headlines ){
                foreach ($headlines[1] as $match) {
                    list($headline, $pos) = $match;

                    if ( strlen( $headline ) ) {

                        // converting accented characters to ASCII meets user expectations,
                        // but the function remove_accents() called by sanitize_title() is
                        // a bad function as-is, mis-handling the German 'ß':
                        $headline = str_replace( 'ß', 'ss', $headline );  // useful default, not German-only
                        $headline = str_replace( 'ẞ', 'SS', $headline );  // now we have uppercase too
                        $headline = str_replace( '£', 'L', $headline );   // euro is E, why not pound L
                        $anchor = remove_accents( $headline ); // bad function as-is, called by sanitize_title()
                        //$anchor = sanitize_title( $headline );
                        $anchor = sanitize_title_with_dashes( $anchor, $anchor, 'save' );  // performs better
                        // $context must be 'save', or a bunch of characters won’t be stripped off
                        $anchor = str_replace( '%e2%80%af', '', $anchor );  // NNBSP missing from the list

                        $icon = '<a href="#' . $anchor . '" aria-hidden="true" class="aal_anchor" id="' . $anchor . '"><svg aria-hidden="true" class="aal_svg" height="16" version="1.1" viewBox="0 0 16 16" width="16"><path fill-rule="evenodd" d="M4 9h1v1H4c-1.5 0-3-1.69-3-3.5S2.55 3 4 3h4c1.45 0 3 1.69 3 3.5 0 1.41-.91 2.72-2 3.25V8.59c.58-.45 1-1.27 1-2.09C10 5.22 8.98 4 8 4H4c-.98 0-2 1.22-2 2.5S3 9 4 9zm9-3h-1v1h1c1 0 2 1.22 2 2.5S13.98 12 13 12H9c-.98 0-2-1.22-2-2.5 0-.83.42-1.64 1-2.09V6.25c-1.09.53-2 1.84-2 3.25C6 11.31 7.55 13 9 13h4c1.45 0 3-1.69 3-3.5S14.5 6 13 6z"></path></svg></a>';

                        $text    = substr_replace($text, $icon, $offset + $pos, 0); // insert after h tag
                        $offset += strlen($icon);
                    }

                }
            }

            
            return $text;
        }

    }
}
new Add_Anchor_Links();
