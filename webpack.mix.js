const mix = require('laravel-mix');
const webpack = require('./webpack.config');
const path = require('path');
mix.js('resources/js/app.js', 'public/js')
    .vue()
    .sass('resources/sass/app.scss', 'public/css')
    .webpackConfig(Object.assign(webpack));
mix.copy('resources/plugins/webix-pro/', 'public/plugins/webix' )
mix.copy('resources/plugins/webix-vue/', 'public/plugins/webix' )

