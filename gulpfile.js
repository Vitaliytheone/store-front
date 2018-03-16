var gulp = require('gulp');
var gutil = require('gulp-util');
var bower = require('bower');
var sass = require('gulp-sass');
var minifyCss = require('gulp-minify-css');
var sourcemaps = require('gulp-sourcemaps');
var uglify = require('gulp-uglify');
var concat = require('gulp-concat');
var rename = require('gulp-rename');
var notify = require("gulp-notify");
var bourbon = require('node-bourbon');
var browserify = require('gulp-browserify');

gulp.task('install', function () {
    return bower.commands.install()
        .on('log', function (data) {
            gutil.log('bower', gutil.colors.cyan(data.id), data.message);
        });
});

gulp.task('sass-admin-sommerce', function(done) {
    gulp.src([
        './web/scss/main.scss',
    ])
        .pipe(sourcemaps.init())
        .pipe(sass({
            errLogToConsole: true
        }))
        .pipe(gulp.dest('./web/css/admin/'))
        .pipe(minifyCss({
            keepSpecialComments: 0
        }))
        .pipe(sourcemaps.write('./'))
        .pipe(gulp.dest('./web/css/admin/'))
        .on('end', done);
});

gulp.task('js-sommerce', function(done) {
    return gulp.src([
            './sommerce/web/js/app/*.js',
            './sommerce/web/js/app/**/*.js',
        ])
        .pipe(concat('main.js'))
        //.pipe(uglify())
        .pipe(gulp.dest('./sommerce/web/js/'));
});

gulp.task('default', [
    'js-sommerce'
]);