<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Settings
    |--------------------------------------------------------------------------
    |
    | Set some default values. It is possible to add all defines that can be set
    | in dompdf (see dompdf documentation).
    |
    */
    'show_warnings' => false,   // Throw an Exception on warnings from dompdf

    'public_path' => null, // Override the public path if needed

    /*
     * Accepted values are: 'UTF-8', 'ISO-8859-1', 'Windows-1252'
     */
    'convert_entities' => true,

    'options' => [
        /*
         * The location of the DOMPDF font directory
         *
         * The location of the directory where DOMPDF will store fonts and
         * temporary files. This directory must exist and be writable by the
         * webserver process.
         *
         */
        'font_dir' => storage_path('fonts/'),

        /*
         * The location of the DOMPDF font cache directory
         *
         * This directory contains the cached font metrics for the fonts used by DOMPDF.
         * This directory must exist and be writable by the webserver process.
         *
         */
        'font_cache' => storage_path('fonts/'),

        /*
         * The location of a temporary directory.
         *
         * The directory specified must be writable by the webserver process.
         * The temporary directory is required to download remote images and when
         * using the PdfLib back end.
         */
        'temp_dir' => sys_get_temp_dir(),

        /*
         * ==== IMPORTANT ====
         *
         * dompdf's "chroot": Prevents dompdf from accessing system files or other
         * files on the webserver. All local files opened by dompdf must be in a
         * subdirectory of this directory. DO NOT set this value to '/' since this
         * could allow malicious HTML documents to read any file on the webserver.
         *
         * Also, any PHP code in external CSS files gets disabled for security reasons.
         *
         * *IMPORTANT*: This directory must not end with a directory separator
         * (i.e. no trailing slash).
         */
        'chroot' => realpath(base_path()),

        /*
         * Whether to use Unicode fonts or not.
         *
         * When set to true the PDF backend must be set to "CPDF" and fonts must be
         * loaded via the font metrics cache (see above).
         */
        'is_unicode' => true,

        /*
         *  dompdf's "chroot" on the Windows platform
         *
         * This is due to an issue with the PCRE library on Windows, where the native
         * path separator '\' is interpreted as an escape sequence.
         */
        'is_utf8' => true,

        /*
         * dompdf's "chroot" on the Windows platform
         */
        'is_php_enabled' => false,

        /*
         * Allow dompdf to access remote sites for images and css.
         */
        'is_remote_enabled' => true,

        /*
         * Enable inline PHP
         */
        'is_javascript_enabled' => false,

        /*
         * Use the more-than-experimental HTML5 Lib parser.
         */
        'is_html5_parser_enabled' => true,

        /*
         * The PDF rendering backend to use
         *
         * Valid settings are 'PDFLib', 'CPDF' (default), 'GD' and
         * 'auto'. 'auto' will look for PDFLib and use it if found, or if not
         * found will fall back on CPDF.
         *
         * 'PDFLib' requires the PDFLib PHP extension and the PDFLib library.
         * 'CPDF' uses the Cpdf class bundled with dompdf.
         * 'GD' renders PDFs to graphic files. An image of each page is created.
         *
         * (Note: make sure to match the dompdf font directory & cache with your
         * PDFLib settings if you use PDFLib.)
         */
        'pdf_backend' => 'CPDF',

        /*
         * html target media view which should be rendered into pdf.
         * List of types and parsing rules for future extensions:
         * http://www.w3.org/TR/REC-CSS2/media.html
         */
        'default_media_type' => 'screen',

        /*
         * default paper size.
         * http://stated.la/wordpress/2007/10/14/list-of-paper-sizes-in-pixels-from-72-to-300-dpi/
         */
        'default_paper_size' => 'a4',

        /*
         * default paper orientation.
         *
         * Either 'portrait' or 'landscape'
         */
        'default_paper_orientation' => 'portrait',

        /*
         * default font size
         */
        'default_font_size' => 12,

        /*
         * default font.
         *
         * Must exist in the font directory.
         */
        'default_font' => 'khmer ui',

        /*
         * image DPI setting
         */
        'dpi' => 96,

        /*
         * enable or disable XML parsing
         */
        'enable_xml' => false,

        /*
         * enable or disable CSS float
         *
         * Allowing CSS float can sometimes cause issues with dompdf. If you
         * experience rendering issues, try setting this to false to see if it resolves them.
         */
        'enable_css_float' => true,

        /*
         * enable or disable dompdf font subsetting
         */
        'enable_font_subsetting' => false,

        /*
         * Increase the ratio of pixels/points
         */
        'font_height_ratio' => 1.1,

        /*
         * Whether to enable dompdf caching
         */
        'cache_enabled' => false,

        /*
         * The prefix to use for cache files
         */
        'cache_prefix' => 'dompdf_cache_',

        /*
         * Ignore invalid CSS warnings
         */
        'log_output_file' => '',
    ],
];
