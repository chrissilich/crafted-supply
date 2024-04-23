import config from '../gulp-config'
import log from 'fancy-log'
import colors from 'ansi-colors'

const gulp = require('gulp')
const prettier = require('gulp-prettier')
const changedInPlace = require('gulp-changed-in-place')
const $ = require('gulp-load-plugins')();

gulp.task('prettier', (done) => {
	const paths = [
    // `gulpfile.babel.js/**/*.js`, // uncomment to run prettier on gulp config files
    `${config.src}/**/*.{scss,js,json,jsx}`
  ]

	gulp.watch(paths, { queue: false }).on('change', (path) => {
		let shortPath = path.replace(config.src, '')

		log(`[${colors.green('Prettier')}] ${colors.green('Tidying up ')} ${shortPath}`)

		return gulp
			.src(path)
      .pipe($.plumber({
        errorHandler: config.reportError
      }))
			.pipe(prettier())
			.pipe(changedInPlace({
        firstPass: true,
        howToDetermineDifference: 'modification-time'
       }))
			.pipe(gulp.dest((file) => file.base))
	})

	done()
})
