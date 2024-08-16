/* eslint-env node */

// -----------------------------------------------------------------------------
//   Javascript Bundling / Transpiling
// -----------------------------------------------------------------------------
import { task, series, dest } from 'gulp';
import prettier from 'gulp-prettier';
import config from './../gulp-config';
import path from 'path';
import rollup from '@rollup/stream';
import source from 'vinyl-source-stream';
import buffer from 'vinyl-buffer';
import alias from '@rollup/plugin-alias';
import resolve from '@rollup/plugin-node-resolve';
import commonjs from '@rollup/plugin-commonjs';
import terser from '@rollup/plugin-terser';

const $ = require('gulp-load-plugins')();
const browserSync = require('browser-sync').get('Local Server');

// -----------------------------------------------------------------------------
//   Javascript Bundling with Rollup w/ Caching
// -----------------------------------------------------------------------------
let cache;

task('js:bundle', () => {
  return rollup({
    cache,
    input: `${config.src}/main.js`,
    output: {
      sourcemap: config.isLocal,
      format: 'iife',
      name: 'Bundle',
      file: 'bundle.js',
      globals: {
        jquery: '$'
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
    .pipe(source('bundle.js'))
    .pipe(buffer())
    .pipe(prettier({
      singleQuote: true,
      trailingComma: 'none'
    }))
    .pipe(dest(`${config.dist}/js`))
    .on('end', browserSync.reload)
  ;
});

task('js', series('js:lint', 'js:bundle'));
