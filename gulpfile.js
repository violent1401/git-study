var gulp     = require('gulp');
var sass     = require('gulp-sass');
var sassGlob = require('gulp-sass-glob');
var concat   = require('gulp-concat');
var cssmin   = require('gulp-cssmin');
var rename   = require('gulp-rename');

gulp.task('sass', function () {
  gulp.src('./src/scss/bootstrap.scss')
  	.pipe(sassGlob())	
    .pipe(sass())
    .pipe(gulp.dest('./dist/css/'))
    .pipe(cssmin())
    .pipe(rename({extname: '.min.css'}))
    .pipe(gulp.dest('./dist/css/'));
});
 
