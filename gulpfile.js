var gulp = require('gulp');
var concat = require('gulp-concat');
var bower = require('gulp-bower');
var uglify = require('gulp-uglify');
var cleanCSS = require('gulp-clean-css');

var src = {
    css: [
        'assets/css/src/base.css',
        'assets/css/src/links.css',
        'assets/css/src/title.css',
        'assets/css/src/table.css',
        'assets/css/src/form.css',
        'assets/css/src/button.css',
        'assets/css/src/alert.css',
        'assets/css/src/tooltip.css',
        'assets/css/src/header.css',
        'assets/css/src/board.css',
        'assets/css/src/task.css',
        'assets/css/src/comment.css',
        'assets/css/src/subtask.css',
        'assets/css/src/tasklink.css',
        'assets/css/src/markdown.css',
        'assets/css/src/listing.css',
        'assets/css/src/activity.css',
        'assets/css/src/dashboard.css',
        'assets/css/src/pagination.css',
        'assets/css/src/popover.css',
        'assets/css/src/confirm.css',
        'assets/css/src/sidebar.css',
        'assets/css/src/responsive.css',
        'assets/css/src/dropdown.css',
        'assets/css/src/upload.css',
        'assets/css/src/filters.css',
        'assets/css/src/gantt.css',
        'assets/css/src/project.css',
        'assets/css/src/files.css',
        'assets/css/src/views.css',
        'assets/css/src/accordion.css',
        'assets/css/src/avatar.css'
    ],
    print: [
        'assets/css/src/print.css',
        'assets/css/src/links.css',
        'assets/css/src/table.css',
        'assets/css/src/board.css',
        'assets/css/src/task.css',
        'assets/css/src/comment.css',
        'assets/css/src/subtask.css',
        'assets/css/src/tasklink.css',
        'assets/css/src/markdown.css'
    ],
    js: [
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
        'bower_components/chosen/chosen.css',
        'bower_components/fullcalendar/dist/fullcalendar.min.css',
        'bower_components/simplemde/dist/simplemde.min.css',
        'bower_components/font-awesome/css/font-awesome.min.css',
        'bower_components/c3/c3.min.css'
    ],
    js: [
        'bower_components/jquery/dist/jquery.min.js',
        'bower_components/jquery-ui/jquery-ui.min.js',
        'bower_components/jquery-ui/ui/minified/core.min.js',
        'bower_components/jquery-ui/ui/minified/autocomplete.min.js',
        'bower_components/jquery-ui/ui/minified/datepicker.min.js',
        'bower_components/jquery-ui/ui/minified/draggable.min.js',
        'bower_components/jquery-ui/ui/minified/droppable.min.js',
        'bower_components/jquery-ui/ui/minified/resizable.min.js',
        'bower_components/jquery-ui/ui/minified/sortable.min.js',
        'bower_components/jquery-ui/ui/minified/tooltip.min.js',
        'bower_components/jquery-ui/ui/minified/i18n/datepicker-*.min.js',
        'bower_components/jqueryui-timepicker-addon/dist/jquery-ui-timepicker-addon.min.js',
        'bower_components/jqueryui-timepicker-addon/dist/i18n/jquery-ui-timepicker-addon-i18n.min.js',
        'bower_components/jqueryui-touch-punch/jquery.ui.touch-punch.min.js',
        'bower_components/chosen/chosen.jquery.js',
        'bower_components/moment/min/moment-with-locales.min.js',
        'bower_components/fullcalendar/dist/fullcalendar.min.js',
        'bower_components/fullcalendar/dist/lang-all.js',
        'bower_components/mousetrap/mousetrap.min.js',
        'bower_components/mousetrap/plugins/global-bind/mousetrap-global-bind.min.js',
        'bower_components/simplemde/dist/simplemde.min.js',
        'bower_components/d3/d3.min.js',
        'bower_components/c3/c3.min.js',
        'bower_components/isMobile/isMobile.min.js'
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

    gulp.src('bower_components/chosen/*.png')
        .pipe(gulp.dest(dist.css + ''));
});

gulp.task('js', function() {
    gulp.src(src.js)
        .pipe(concat('app.min.js'))
        .pipe(uglify())
        .pipe(gulp.dest(dist.js))
    ;
});

gulp.task('css', function() {
    gulp.src(src.css)
        .pipe(concat('app.min.css'))
        .pipe(cleanCSS())
        .pipe(gulp.dest(dist.css))
    ;

    gulp.src(src.print)
        .pipe(concat('print.min.css'))
        .pipe(cleanCSS())
        .pipe(gulp.dest(dist.css))
    ;
});

gulp.task('default', ['bower', 'vendor', 'js', 'css']);
