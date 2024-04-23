/* eslint-env node */

// -----------------------------------------------------------------------------
//   Compile & Lint Sass
// -----------------------------------------------------------------------------
import { task, src, dest, series } from 'gulp';
import config from './../gulp-config';
import autoprefixer from 'autoprefixer';
import cssnano from 'cssnano';
import stylelint from 'stylelint';
import beep from 'beepbeep';
import log from 'fancy-log';
import colors from 'ansi-colors';

const sass = require('gulp-sass')(require('sass'));
const $ = require('gulp-load-plugins')();

const browserSync = require('browser-sync').get('Local Server');

const postcssPlugins = [
  autoprefixer({
    cascade: false
  })
];

if (!config.isLocal) {
  postcssPlugins.push(cssnano());
}

// -----------------------------------------------------------------------------
//   Lint Sass
// -----------------------------------------------------------------------------
task('sass:lint', () => stylelint.lint({
  cwd: config.src,
  files: '**/*.scss',
  failAfterError: true,
  cache: true,
  cacheStrategy: 'content',
  cacheLocation: process.cwd() + '/.stylelintcache',
  formatter: 'string',
  reportDescriptionlessDisables: true,
  reportNeedlessDisables: true
}).then((resultObject) => {
  if (resultObject.errored) {
    beep();
    const chalk = (str) => colors.bgRed(str);
    let report = '';
    report += chalk('SCSS errors detected...') + '\n';
    report += resultObject.output;
    log.error(report);
  }
}));

// -----------------------------------------------------------------------------
//   Compile Sass
// -----------------------------------------------------------------------------
task('sass:compile', () => src(`${config.src}/main.scss`)
  .pipe($.cond(config.isLocal,
    $.sourcemaps.init({
      debug: true
    })
  ))
  .pipe($.plumber({
    errorHandler: config.reportError
  }))
  .pipe($.sassGlob())
  .pipe(sass.sync({
    outputStyle: 'expanded',
    includePaths: [
      'node_modules/',
      'node_modules/breakpoint-sass/stylesheets/'
    ]
  }))
  .on('error', config.reportError)
  .pipe($.postcss(postcssPlugins))
  .pipe($.rename('style.css'))
  .pipe($.cond(config.isLocal,
    $.sourcemaps.write('/sourcemaps')
  ))
  .pipe(dest(`${config.dist}/css/`))
  .pipe(browserSync.stream({match: '**/*.css'}))
);

task('sass', series('sass:lint', 'sass:compile'));
