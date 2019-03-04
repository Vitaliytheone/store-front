var gulp = require('gulp');
var gutil = require('gulp-util');
var bower = require('bower');
var sass = require('gulp-sass');
var minifyCss = require('gulp-minify-css');
var sourcemaps = require('gulp-sourcemaps');
var uglify = require('gulp-uglify');
var concat = require('gulp-concat');

gulp.task('install', function () {
    return bower.commands.install()
        .on('log', function (data) {
            gutil.log('bower', gutil.colors.cyan(data.id), data.message);
        });
});

var tasks = {
    'sass-admin-sommerce' : function(done) {
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
    },
    'js-store' : function(done) {
        return gulp.src([
            './store/web/js/app/*.js',
            './store/web/js/app/admin/*.js',
        ])
            .pipe(concat('main.js'))
            .pipe(uglify())
            .pipe(gulp.dest('./store/web/js/'));
    },
    'js-so' : function(done) {
        return gulp.src([
            './sommerce/web/js/app/*.js',
            './sommerce/web/js/app/admin/*.js',
        ])
            .pipe(concat('admin.js'))
            .pipe(uglify())
            .pipe(gulp.dest('./sommerce/web/js/'));
    },
    'js-gateway' : function(done) {
        return gulp.src([
            './gateway/web/js/app/*.js',
            './gateway/web/js/app/admin/*.js',
            './gateway/web/js/app/controllers/*.js',
            './gateway/web/js/app/controllers/*/*.js',
        ])
            .pipe(concat('main.js'))
            .pipe(uglify())
            .pipe(gulp.dest('./gateway/web/js/'));
    },
    'js-sommerce-frontend' : function(done) {
        return gulp.src([
            './sommerce/web/js/libs/underscore.js',
            './sommerce/web/js/app/global.js',
            './sommerce/web/js/app/index.js',
            './sommerce/web/js/app/frontend/*.js',
        ])
            .pipe(concat('frontend.js'))
            .pipe(uglify())
            .pipe(gulp.dest('./sommerce/web/js/'));
    },
    'js-my' : function (done) {
        return gulp.src([
            './my/web/themes/js/app/*.js',
            './my/web/themes/js/app/**/*.js',
        ])
            .pipe(concat('script.js'))
            .pipe(uglify())
            .pipe(gulp.dest('./my/web/themes/js'))
            .on('end', done);
    },
    'js-cp' : function (done) {
        gulp.src([
            './control_panel/web/themes/js/app/*.js',
            './control_panel/web/themes/js/app/**/*.js',
        ])
            .pipe(concat('script.js'))
            .pipe(uglify())
            .pipe(gulp.dest('./control_panel/web/themes/js'))
            .on('end', done);
    }
};

Object.keys(tasks).forEach(function(key) {
    gulp.task(key, tasks[key]);
});

gulp.task('js', gulp.parallel('js-my', 'js-sommerce-frontend', 'js-gateway', 'js-so', 'js-store', 'js-cp'));
gulp.task('default', gulp.parallel('js-my', 'js-sommerce-frontend', 'js-gateway', 'js-so', 'js-store', 'js-cp', 'sass-admin-sommerce'));