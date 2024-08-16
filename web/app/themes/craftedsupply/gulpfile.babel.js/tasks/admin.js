/* eslint-env node */

import { task, src, dest, parallel } from 'gulp';
import config from './../gulp-config';
import prettier from 'gulp-prettier';
import path from 'path';
import rollup from '@rollup/stream';
import source from 'vinyl-source-stream';
import buffer from 'vinyl-buffer';
import alias from '@rollup/plugin-alias';
import resolve from '@rollup/plugin-node-resolve';
import commonjs from '@rollup/plugin-commonjs';
import terser from '@rollup/plugin-terser';
import autoprefixer from 'autoprefixer';

const sass = require('gulp-sass')(require('sass'));
const $ = require('gulp-load-plugins')();
const browserSync = require('browser-sync').get('Local Server');
let cache;



task('admin:js', () => {
  return rollup({
    cache,
    input: `${config.src}/admin/admin.js`,
    output: {
      sourcemap: config.isLocal,
      format: 'iife',
      name: 'Bundle',
      file: 'admin.js',
      globals: {
        jquery: '$',
        wp: 'wp',
        acf: 'acf'
      }
    },
    external: ['jquery'],
    plugins: [
      alias({
        entries: [{
          find: /@plugins\/([^/]+$)/,
          replacement: path.resolve(config.src, 'js-plugins/$1')
        }, {
          find: /@components(.+(?=\/)|)(.+)/,
          replacement: path.resolve(config.src, 'components/$1$2$2')
        }, {
          find: /@blocks(.+(?=\/)|)(.+)/,
          replacement: path.resolve(config.src, 'blocks/$1$2$2'),
        }]
      }),
      resolve({
        preferBuiltins: true,
        jsnext: true,
        main: true,
        browser: true
      }),
      commonjs(),
      ...!config.isLocal ? [
        terser()
      ] : []
    ]
  })
    .pipe($.plumber({
      errorHandler: config.reportError
    }))
    .on('bundle', bundle => {
      cache = bundle;
    })
    .pipe(source('admin.js'))
    .pipe(buffer())
    .pipe(prettier({
      singleQuote: true,
      trailingComma: 'none'
    }))
    .pipe(dest(`${config.dist}/admin`))
    .on('end', browserSync.reload)
  ;
});



// -----------------------------------------------------------------------------
//   Compile Admin Sass
// -----------------------------------------------------------------------------
task('admin:sass', () => src(`${config.src}/admin/admin.scss`)
  .pipe($.cond(config.isLocal,
    $.sourcemaps.init({
      debug: true
    })
  ))
  .pipe($.plumber({
    errorHandler: config.reportError
  }))
  .pipe($.sassGlob())
  .pipe(sass({
    outputStyle: 'expanded',
    errLogToConsole: true
  }))
  .on('error', config.reportError)
  .pipe($.postcss([
    autoprefixer({
      cascade: false
    })
  ]))
  .pipe($.rename('admin.css'))
  .pipe($.cond(config.isLocal,
    $.sourcemaps.write('/sourcemaps')
  ))
  .pipe(dest(`${config.dist}/admin`))
  .pipe(browserSync.stream({match: '**/*.css'}))
);

task('admin', parallel('admin:sass', 'admin:js'));
