var gulp = require('gulp');
var concat = require('gulp-concat');
var uglify = require('gulp-uglify');
var sass = require('gulp-sass');

var src = {
    js: [
        'node_modules/textarea-caret/index.js',
        'assets/js/polyfills/*.js',
        'assets/js/core/base.js',
        'assets/js/core/!(base|bootstrap)*.js',
        'assets/js/components/*.js',
        'assets/js/core/bootstrap.js',
        'assets/js/src/Namespace.js',
        'assets/js/src/!(Namespace|Bootstrap|BoardDragAndDrop)*.js',
        'assets/js/src/BoardDragAndDrop.js',
        'assets/js/src/Bootstrap.js'
    ]
};

var vendor = {
    css: [
        'assets/vendor/jquery-ui/jquery-ui.min.css',
        'assets/vendor/jqueryui-timepicker-addon/jquery-ui-timepicker-addon.min.css',
        'assets/vendor/select2/css/select2.min.css',
        'assets/vendor/font-awesome/css/font-awesome.min.css',
        'assets/vendor/c3/c3.min.css'
    ],
    js: [
        'assets/vendor/jquery/jquery-3.3.1.min.js',
        'assets/vendor/jquery-ui/jquery-ui.min.js',
        'assets/vendor/jquery-ui/i18n/datepicker-*.js',
        'assets/vendor/jqueryui-timepicker-addon/jquery-ui-timepicker-addon.min.js',
        'assets/vendor/jqueryui-timepicker-addon/i18n/jquery-ui-timepicker-addon-i18n.min.js',
        'assets/vendor/jqueryui-touch-punch/jquery.ui.touch-punch.min.js',
        'assets/vendor/select2/js/select2.min.js',
        'assets/vendor/select2/js/i18n/*.js',
        'assets/vendor/d3/d3.min.js',
        'assets/vendor/c3/c3.min.js',
        'assets/vendor/isMobile/isMobile.min.js',
        'assets/vendor/marked/marked.min.js'
    ]
};

var dist = {
    fonts: 'assets/fonts/',
    css: 'assets/css/',
    js: 'assets/js/',
    img: 'assets/img/'
};

gulp.task('vendor', function() {
    gulp.src(vendor.js)
        .pipe(concat('vendor.min.js'))
        .pipe(gulp.dest(dist.js))
    ;

    gulp.src(vendor.css)
        .pipe(concat('vendor.min.css'))
        .pipe(gulp.dest(dist.css))
    ;

    gulp.src('assets/vendor/font-awesome/fonts/*')
        .pipe(gulp.dest(dist.fonts));

    gulp.src('assets/vendor/jquery-ui/images/*')
        .pipe(gulp.dest(dist.css + 'images/'));
});

gulp.task('js', function() {
    gulp.src(src.js)
        .pipe(concat('app.min.js'))
        .pipe(uglify())
        .pipe(gulp.dest(dist.js))
    ;
});

gulp.task('css', function() {
    gulp.src(['assets/sass/*.sass','!assets/sass/*_print.sass'])
        .pipe(sass({outputStyle: 'compressed'}).on('error', sass.logError))
        .pipe(concat('app.min.css'))
        .pipe(gulp.dest(dist.css));
});

gulp.task('css:print', function() {
    gulp.src('assets/sass/*_print.sass')
        .pipe(sass({outputStyle: 'compressed'}).on('error', sass.logError))
        .pipe(concat('print.min.css'))
        .pipe(gulp.dest(dist.css));
});

gulp.task('default', ['vendor', 'js', 'css', 'css:print']);
