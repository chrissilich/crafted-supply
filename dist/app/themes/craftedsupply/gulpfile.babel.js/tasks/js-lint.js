/* eslint-env node */

import { task, src } from 'gulp';
import config from './../gulp-config';
const $ = require('gulp-load-plugins')();

import eslint from 'gulp-eslint-new';

import through from 'through2';
import PluginError from 'plugin-error';
import fs from 'fs';

let cachedFiles = {};
let options;
const files = {};

const cacheAndFilter = (opt) => {
  if (!opt.filename) {
    return new PluginError('lint-cache', 'Provide filename for caching');
  }
  options = opt;

  if (fs.existsSync(options.filename)) {
    cachedFiles = JSON.parse(fs.readFileSync(options.filename, 'utf8'));
  }

  const collectAndFilter = (file, enc, cb) => {
    files[file.path] = file.stat.mtime;

    const currentTimestamp = cachedFiles[file.path];
    if (!currentTimestamp || (currentTimestamp && new Date(currentTimestamp).getTime() < file.stat.mtime.getTime())) {
      cb(null, file);
    } else {
      cb(null, null);
    }
  };

  return through.obj(collectAndFilter);
};

const updateCache = () => through.obj((file, enc, cb) => {
  if (!file.eslint.errorCount) {
    fs.writeFile(options.filename, JSON.stringify({
      ...cachedFiles,
      ...files
    }), 'utf8', (err) => cb(err ? new PluginError('lint-cache', err) : null, file));
  } else {
    cb(null, file);
  }
});

// -----------------------------------------------------------------------------
//   Lint JavaScript
// -----------------------------------------------------------------------------
task('js:lint', () => src([
  `${config.src}/**/*.js`,
  `!${config.src}/assets/js/jquery.min.js`
])
  .pipe($.plumber({
    errorHandler: config.reportError
  }))
  .pipe(cacheAndFilter({filename: '.eslintcache'}))
  .pipe(eslint())
  .pipe(eslint.format())
  .pipe(eslint.failAfterError())
  .pipe(updateCache())
);
