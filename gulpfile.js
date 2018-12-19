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

gulp.task('js-so', function(done) {
    return gulp.src([
            './sommerce/web/js/app/*.js',
            './sommerce/web/js/app/admin/*.js',
        ])
        .pipe(concat('main.js'))
        .pipe(uglify())
        .pipe(gulp.dest('./sommerce/web/js/'));
});

gulp.task('js-gateway', function(done) {
    return gulp.src([
        './gateway/web/js/app/*.js',
        './gateway/web/js/app/admin/*.js',
    ])
        .pipe(concat('main.js'))
        .pipe(uglify())
        .pipe(gulp.dest('./gateway/web/js/'));
});

gulp.task('js-sommerce-frontend', function(done) {
    return gulp.src([
            './sommerce/web/js/app/*.js',
            './sommerce/web/js/app/frontend/*.js',
        ])
        .pipe(concat('frontend.js'))
        .pipe(uglify())
        .pipe(gulp.dest('./sommerce/web/js/'));
});

gulp.task('js-my', function (done) {
    gulp.src([
        './my/web/themes/js/app/*.js',
        './my/web/themes/js/app/**/*.js',
    ])
        .pipe(concat('script.js'))
        .pipe(uglify())
        .pipe(gulp.dest('./my/web/themes/js'))
        .on('end', done);
});

gulp.task('js', ['js-so', 'js-my']);

gulp.task('default', [
    'js'
]);