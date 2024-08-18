/* eslint-env node */

require('dotenv').config({ path: '../../../../.env' });
const $ = require('gulp-load-plugins')();
import beep from 'beepbeep';
import log from 'fancy-log';
import colors from 'ansi-colors';

// -----------------------------------------------------------------------------
//   Creates a Config Object to be Used Throughout Gulpfile and Tasks
// -----------------------------------------------------------------------------
export default {
  isLocal: process.env.WP_ENVIRONMENT_TYPE === 'local',
  src: process.cwd() + '/src',
  dist: process.cwd() + '/assets',
  wpHome: process.env.WP_HOME,
  sslCertificatePath: '../../../../certs/',
  reportError(error) {
    const lineNumber = (error.lineNumber) ? 'LINE ' + error.lineNumber + ' -- ' : '';

    $.notify({
      title: 'Task Failed',
      message: lineNumber + 'Task Failed [' + error.plugin + ']',
      sound: 'Sosumi'
    }).write(error);

    beep();

    // Pretty error reporting
    let report = '';
    const chalk = (str) => colors.bgRed(str);

    report += chalk('TASK:') + ' [' + error.plugin + ']\n';
    report += chalk('PROB:') + ' ' + error.message + '\n';
    if (error.lineNumber) {
      report += chalk('LINE:') + ' ' + error.lineNumber + '\n';
    }
    if (error.fileName) {
      report += chalk('FILE:') + ' ' + error.fileName + '\n';
    }
    log.error(report);

    // Prevent the 'watch' task from stopping
    this.emit('end');
  }
};
