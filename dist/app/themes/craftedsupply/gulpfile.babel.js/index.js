/* eslint-env node */

// -----------------------------------------------------------------------------
//   Load Gulp
// -----------------------------------------------------------------------------
import { task, series, parallel } from 'gulp';
import log from 'fancy-log';
import colors from 'ansi-colors';

// -----------------------------------------------------------------------------
//   Create a BrowserSync Instance
// -----------------------------------------------------------------------------
// eslint-disable-next-line no-unused-vars
const browserSync = require('browser-sync').create('Local Server');


// -----------------------------------------------------------------------------
//   Gets all of our tasks
// -----------------------------------------------------------------------------
require('require-dir')('./tasks');


// -----------------------------------------------------------------------------
//   Task: Build
// -----------------------------------------------------------------------------
task('build', series('clean', parallel('sass', 'admin', 'js', 'assets')));

// -----------------------------------------------------------------------------
//   Task: Serve
// -----------------------------------------------------------------------------
task('serve', series('clean', 'prettier', 'watch'));


// -----------------------------------------------------------------------------
//   Don't run `gulp` directly anymore
// -----------------------------------------------------------------------------
task('default', (done) => {
  log.error('\n\n[' + colors.red('ERROR') + '] This is not the proper way to run gulp. Please run ' + colors.green('"npm run serve"') + ' instead.\n');
  done();
  process.exit(1);
});
