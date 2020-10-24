/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
//import '../css/admin.css';

//require("bootstrap/dist/css/bootstrap.css");
import 'jquery-ui-dist/jquery-ui.css';
import 'jquery-ui-dist/jquery-ui.js';
import 'popper.js';
import 'bootstrap';
import 'bootstrap/dist/css/bootstrap.css';
import '@fortawesome/fontawesome-free/css/all.css';
import 'admin-lte';
import 'admin-lte/dist/css/adminlte.css';
import 'overlayscrollbars';
import 'overlayscrollbars/css/OverlayScrollbars.min.css';
global.toastr = require('toastr');
import 'toastr/build/toastr.min.css';
import 'select2';
import 'select2/dist/css/select2.css';
import 'flag-icon-css/css/flag-icon.min.css';
const $ = require('jquery');
// create global $ and jQuery variables
global.$ = global.jQuery = $;
