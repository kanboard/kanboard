var gulp = require('gulp');
var concat = require('gulp-concat');
var uglify = require('gulp-uglify');

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
    js: 'assets/js/',
    img: 'assets/img/'
};

gulp.task('vendor', function() {
    gulp.src(vendor.js)
        .pipe(concat('vendor.min.js'))
        .pipe(gulp.dest(dist.js))
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

gulp.task('default', ['vendor', 'js']);
