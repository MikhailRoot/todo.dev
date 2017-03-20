const gulp = require('gulp');
const concat = require('gulp-concat');
const sourcemaps = require('gulp-sourcemaps');
const uglify = require('gulp-uglify');
const cleanCSS = require('gulp-clean-css');

gulp.task('js-admin',function(){
    gulp.src([
        'node_modules/jquery/dist/jquery.js',
        'node_modules/angular/angular.js',
        'node_modules/angular-animate/angular-animate.js',
        'node_modules/angular-ui-bootstrap/dist/ui-bootstrap.js',
        'node_modules/angular-ui-bootstrap/dist/ui-bootstrap-tpls.js',
        'node_modules/angular-file-upload/dist/angular-file-upload.js',
        'node_modules/angular-ui-notification/dist/angular-ui-notification.js',

        'js/adminApp.js',

        'js/services/Todos.js',
        'js/components/adminUiComponent.js',
        'js/components/todoComponent.js'

    ])
        .pipe(concat('adminApp.js'))
        .pipe(uglify()) //for production
        .pipe(sourcemaps.write())
        .pipe(gulp.dest('public/js/'));

});

gulp.task('js-client',function(){

    gulp.src([
            'node_modules/jquery/dist/jquery.js',
            'node_modules/angular/angular.js',
            'node_modules/angular-animate/angular-animate.js',
            'node_modules/angular-ui-bootstrap/dist/ui-bootstrap.js',
            'node_modules/angular-ui-bootstrap/dist/ui-bootstrap-tpls.js',
            'node_modules/angular-file-upload/dist/angular-file-upload.js',
            'node_modules/angular-ui-notification/dist/angular-ui-notification.js',

            'js/app.js',

            'js/services/ClientTodos.js',
            'js/components/clientUiComponent.js',
            'js/components/clientTodoComponent.js'


        ])
        .pipe(concat('App.js'))
        .pipe(uglify()) //for production
        .pipe(sourcemaps.write())
        .pipe(gulp.dest('public/js/'));

});

//
//gulp.task('watch',function(){
//    gulp.watch([
//        'js/adminApp.js',
//
//        'js/services/*.js',
//        'js/components/*.js'
//    ],['js-admin']);
//});

gulp.task('bootstrapcopy',function(){
    gulp.src([
        'node_modules/bootstrap/dist/**/*'
    ]).pipe(gulp.dest('public/'));

    gulp.src([
        'node_modules/angular-ui-notification/dist/angular-ui-notification.min.css'
    ]).pipe(gulp.dest('public/css'));


});

gulp.task('css',function(){
    gulp.src([
        'node_modules/bootstrap/dist/**/*'
    ]).pipe(gulp.dest('public/'));

    gulp.src([
        'node_modules/angular-ui-notification/dist/angular-ui-notification.min.css'
    ]).pipe(gulp.dest('public/css'));

    gulp.src([
        'public/css/bootstrap.min.css',
        'public/css/font-awesome.min.css',
        'public/css/angular-ui-notification.min.css',
        'public/css/styles.css'
    ])
        .pipe(concat('styles.min.css'))
        .pipe(cleanCSS())
        .pipe(gulp.dest('public/css'));
});