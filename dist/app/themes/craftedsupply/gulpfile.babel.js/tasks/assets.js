/* eslint-env node */

// -----------------------------------------------------------------------------
//   Copies assets from /src to /dist
// -----------------------------------------------------------------------------

import { task, src, dest, parallel } from 'gulp';
import config from './../gulp-config';
const $ = require('gulp-load-plugins')();

const browserSync = require('browser-sync').get('Local Server');

function copyAssets(type) {
  return src(`${config.src}/assets/${type}/**/*`)
    .pipe($.plumber({
      errorHandler: config.reportError
    }))
    .pipe(dest(`${config.dist}/${type}/`))
    .pipe($.cond(type === 'images',
      $.webp({
        quality: 90
      })
    ))
    .pipe($.cond(type === 'images',
      dest(`${config.dist}/${type}/`)
    ))
    .on('end', browserSync.reload)
  ;
}

task('assets:fonts', () => copyAssets('fonts'));

task('assets:images', () => copyAssets('images'));

task('assets:js', () => copyAssets('js'));

task('assets', parallel('assets:fonts', 'assets:images', 'assets:js'));
