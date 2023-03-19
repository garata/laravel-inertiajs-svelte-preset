/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Svelte and other libraries. It is a great starting point when
 * building robust, powerful web applications using Svelte and Laravel.
 */
require('./bootstrap');

import route from 'ziggy-js';

// import { Ziggy } from './ziggy';
const response = await fetch('/api/ziggy');
const Ziggy = await response.json();

route('home', undefined, undefined, Ziggy);

import { createInertiaApp } from '@inertiajs/inertia-svelte'

createInertiaApp({
    resolve: name => import(`./components/${name}.svelte`),
    setup({ el, App, props }) {
        window.app = new App({ target: el, props });
    },
})
