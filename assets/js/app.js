/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

import $ from 'jquery';
import 'select2';
import 'select2/dist/css/select2.min.css'
import "@popperjs/core"
import "bootstrap"
import "bootstrap/dist/css/bootstrap.min.css"
// any CSS you import will output into a single css file (app.css in this case)
import '../css/app.scss';

$(document).ready(function () {
    $('.select2').select2();
});

$('.result-card-back').click((event) => {
    const truc = $(event.target).closest('.result-card-back');
    truc.hide();
});
