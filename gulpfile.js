'use strict';

var autoprefixer = require('gulp-autoprefixer');
var concat = require('gulp-concat');
var csso = require('gulp-csso');
var del = require('del');
var gulp = require('gulp');
var htmlmin = require('gulp-htmlmin');
var runSequence = require('run-sequence');
var sass = require('gulp-sass');
var uglify = require('gulp-uglify');

// Set the browser that you want to supoprt
const AUTOPREFIXER_BROWSERS = [
    'ie >= 10',
    'ie_mob >= 10',
    'ff >= 30',
    'chrome >= 34',
    'safari >= 7',
    'opera >= 23',
    'ios >= 7',
    'android >= 4.4',
    'bb >= 10'
];

//FRONTEND SETTINGS BEGIN ----------------------------------------------------->

// Gulp task to minify CSS files
gulp.task('styles', function () {
    return gulp.src([
            './public/bootstrap/css/bootstrap.css',
            './public/bootstrap/css/bootstrap-theme.css',
            './public/css/**/*.css'
        ])
        // Auto-prefix css styles for cross browser compatibility
        .pipe(autoprefixer({browsers: AUTOPREFIXER_BROWSERS}))
        // Minify the file
        .pipe(csso())
        .pipe(concat('bundle.min.css'))
        // Output
        .pipe(gulp.dest('./public/dist/css'))
});

// Gulp task to minify JavaScript files
gulp.task('scripts', function() {
    return gulp.src([
        './public/js/jquery.min.js',
        './public/js/jquery.stellar.min.js',
        './public/js/modernizr.js',
        './public/js/owl.carousel.min.js',
        './public/js/jquery.shuffle.min.js',
        './public/js/validator.min.js',
        './public/js/smoothscroll.js',
        './public/bootstrap/js/bootstrap.js',
        './public/js/script.js'
    ])
    // Minify the file
        .pipe(uglify())
        .pipe(concat('bundle.min.js'))
        // Output
        .pipe(gulp.dest('./public/dist/js'))
});

// Gulp task to copy fontfiles
gulp.task('fontcopy', function() {
    return gulp.src('./public/fonts/**/*')
        .pipe(gulp.dest('./public/dist/fonts'))
});

//FRONTEND SETTINGS END <-------------------------------------------------------
//
//
//
//ADMIN SETTINGS BEGIN -------------------------------------------------------->

gulp.task('admin-styles', function () {
    return gulp.src([
        './public/bootstrap/css/bootstrap.css',
        './public/bootstrap/css/bootstrap-theme.css',
        './public/admin_/css/**/*.css'
    ])
    // Auto-prefix css styles for cross browser compatibility
        .pipe(autoprefixer({browsers: AUTOPREFIXER_BROWSERS}))
        // Minify the file
        .pipe(csso())
        .pipe(concat('admin-bundle.min.css'))
        // Output
        .pipe(gulp.dest('./public/admin_/dist/css'))
});

gulp.task('admin-scripts', function() {
    return gulp.src([
        './public/js/jquery.min.js',
        './public/bootstrap/js/bootstrap.js',
        './public/admin_/js/*.js'
    ])
    // Minify the file
        .pipe(uglify())
        .pipe(concat('admin-bundle.min.js'))
        // Output
        .pipe(gulp.dest('./public/admin_/dist/js'))
});

gulp.task('admin-fontcopy', function() {
    return gulp.src('./public/bootstrap/fonts/**/*')
        .pipe(gulp.dest('./public/admin_/dist/fonts'))
});

//ADMIN SETTINGS END <----------------------------------------------------------


// Clean output directory
gulp.task('clean', () => del(['dist']));

// Gulp task to minify all files
gulp.task('default', ['clean'], function () {
    runSequence(
        'styles',
        'scripts',
        'fontcopy',
        'admin-styles',
        'admin-scripts',
        'admin-fontcopy'
    );
});