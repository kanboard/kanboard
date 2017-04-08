var gulp = require('gulp');
var concat = require('gulp-concat');
var bower = require('gulp-bower');
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
        'bower_components/jquery-ui/themes/base/jquery-ui.min.css',
        'bower_components/jqueryui-timepicker-addon/dist/jquery-ui-timepicker-addon.min.css',
        'bower_components/select2/dist/css/select2.min.css',
        'bower_components/font-awesome/css/font-awesome.min.css',
        'bower_components/c3/c3.min.css'
    ],
    js: [
        'bower_components/jquery/dist/jquery.min.js',
        'bower_components/jquery-ui/jquery-ui.min.js',
        'bower_components/jquery-ui/ui/minified/core.min.js',
        'bower_components/jquery-ui/ui/minified/autocomplete.min.js',
        'bower_components/jquery-ui/ui/minified/datepicker.min.js',
        'bower_components/jquery-ui/ui/minified/i18n/*.js',
        'bower_components/jquery-ui/ui/minified/draggable.min.js',
        'bower_components/jquery-ui/ui/minified/droppable.min.js',
        'bower_components/jquery-ui/ui/minified/resizable.min.js',
        'bower_components/jquery-ui/ui/minified/sortable.min.js',
        'bower_components/jquery-ui/ui/minified/tooltip.min.js',
        'bower_components/jquery-ui/ui/minified/i18n/datepicker-*.min.js',
        'bower_components/jqueryui-timepicker-addon/dist/jquery-ui-timepicker-addon.min.js',
        'bower_components/jqueryui-timepicker-addon/dist/i18n/jquery-ui-timepicker-addon-i18n.min.js',
        'bower_components/jqueryui-touch-punch/jquery.ui.touch-punch.min.js',
        'bower_components/select2/dist/js/select2.min.js',
        'bower_components/d3/d3.min.js',
        'bower_components/c3/c3.min.js',
        'bower_components/isMobile/isMobile.min.js',
        'bower_components/marked/marked.min.js'
    ]
};

var dist = {
    fonts: 'assets/fonts/',
    css: 'assets/css/',
    js: 'assets/js/',
    img: 'assets/img/'
};

gulp.task('bower', function() {
    return bower();
});

gulp.task('vendor', function() {
    gulp.src(vendor.js)
        .pipe(concat('vendor.min.js'))
        .pipe(gulp.dest(dist.js))
    ;

    gulp.src(vendor.css)
        .pipe(concat('vendor.min.css'))
        .pipe(gulp.dest(dist.css))
    ;

    gulp.src('bower_components/font-awesome/fonts/*')
        .pipe(gulp.dest(dist.fonts));

    gulp.src('bower_components/jquery-ui/themes/base/images/*')
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
    gulp.src('assets/sass/*.sass')
        .pipe(sass({outputStyle: 'compressed'}).on('error', sass.logError))
        .pipe(concat('app.min.css'))
        .pipe(gulp.dest(dist.css));
});

gulp.task('default', ['bower', 'vendor', 'js', 'css']);
