/* eslint-env node */
/* eslint-disable camelcase, no-console */


// -----------------------------------------------------------------------------
//   Watch files for changes, execute tasks on change and stream into browser
// -----------------------------------------------------------------------------
import { task, watch, series, parallel } from 'gulp';
import config from './../gulp-config';

import log from 'fancy-log';
import colors from 'ansi-colors';
import fixme from 'fixme';

const browserSync = require('browser-sync').get('Local Server');

task('serve', (done) => {

  if (!config.wpHome) {
    log.warn('[' + colors.yellow('serve') + '] ' + colors.yellow('WP_HOME variable is not set. Cannot initialize browserSync!'));
  } else if (typeof config.wpHome !== 'string') {
    log.warn('[' + colors.yellow('serve') + '] ' + colors.yellow('WP_HOME variable must be a string. Cannot initialize browserSync!'));
  } else {

    const host = new URL(process.env.WP_HOME).host;

    const browserSyncOptions = {
      open: false,
      logPrefix: 'BrowserSync',
      proxy: config.wpHome,
      host,
      callbacks: {
        ready() {
          fixme({
            path: process.cwd(),
            ignored_directories: ['node_modules/**', `${ config.src }/assets/js/**`, 'assets/**', 'acf-json/**'],
            file_patterns: ['**/*.twig', '**/*.scss', '**/*.php', '**/*.js'],
            file_encoding: 'utf8',
            line_length_limit: 1000,
            skip: ['note', 'line']
          });
        }
      }
    };

    if (config.wpHome.startsWith('https://')) {
      browserSyncOptions.https = {
        key: `${ config.sslCertificatePath }${ host }.key`,
        cert: `${ config.sslCertificatePath }${ host }.crt`
      };
    }

    browserSync.init(browserSyncOptions);
  }

  watch(`${config.src}/**/*.js`, series('js'));

  watch([
    `${config.src}/**/*.scss`,
    `!${config.src}/admin/*`
  ], series('sass')).on('change', () => {
    log('[' + colors.blue('sass') + '] ' + colors.green('Compiling SCSS...'));
  });

  watch(`${config.src}/admin/admin.js`, series('admin:js'));
  watch(`${config.src}/admin/admin.scss`, series('admin:sass'));

  watch(`${config.src}/assets/images/**/*`, series('assets:images'));
  watch(`${config.src}/assets/fonts/**/*`, series('assets:fonts'));
  watch(`${config.src}/assets/js/**/*`, series('assets:js'));

  watch(`${config.src}/**/*.twig`).on('change', () => {
    setTimeout(browserSync.reload, 500);
  });

  watch('**/*.php').on('change', () => {
    setTimeout(browserSync.reload, 500);
  });

  done();

});

task('watch', series(parallel('sass', 'admin', 'js', 'assets'), 'serve'));
