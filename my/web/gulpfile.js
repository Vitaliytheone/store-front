var gulp = require('gulp');
var gutil = require('gulp-util');
var bower = require('bower');
var sass = require('gulp-sass');
var minifyCss = require('gulp-minify-css');
var sourcemaps = require('gulp-sourcemaps');
var uglify = require('gulp-uglify');
var concat = require('gulp-concat');
var rename = require('gulp-rename');

gulp.task('install', function () {
    return bower.commands.install()
        .on('log', function (data) {
            gutil.log('bower', gutil.colors.cyan(data.id), data.message);
        });
});

gulp.task('js-compress', function (done) {
    gulp.src([
        './themes/js/app/*.js',
        './themes/js/app/**/*.js',
    ])
        .pipe(concat('script.js'))
        .pipe(uglify())
        .pipe(gulp.dest('./themes/js'))
        .on('end', done);
});

gulp.task('default', [
    'js-compress',
]);