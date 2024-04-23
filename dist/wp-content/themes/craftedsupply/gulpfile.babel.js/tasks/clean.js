/* eslint-env node */

// -----------------------------------------------------------------------------
//   Cleans /assets before building
// -----------------------------------------------------------------------------
import { task } from 'gulp';
import config from './../gulp-config';
import del from 'del';

task('clean', () => del([
  `${config.dist}/**`,
  `!${config.dist}`,
  '../../../service-worker*',
  '../../../workbox*'
], { 'force': true }));
