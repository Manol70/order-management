/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */


// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.css';



import getNiceMessage from './get_nice_message';
import { session } from "@hotwired/turbo";
session.drive = false;

console.log(getNiceMessage(3));

// start the Stimulus application
import './bootstrap';

import $ from 'jquery';
global.$ = $;

import processPayment from './payment';
processPayment(3);
import './controllers';

import './bulk_form.js';

import { start } from '@hotwired/turbo';

//Започни Turbo
start();

import './main.js';



