var gulp = require('gulp')
    , shell = require('gulp-shell')
;

var paths = {
    root: __dirname + '/',
    src: __dirname + '/src/',
    test: __dirname + '/test/',
};

var callbacks = {
    error: function(error){
        console.error(error);
        this.emit('end');
    }
};

function gulpWatch(){
    gulp.watch(paths.src+'**/*.php', ['phpunit']);
    gulp.watch(paths.test+'**/*.php', ['phpunit']);
}

gulp.task('phpunit', shell.task(['phpunit -c '+paths.root+'phpunit.xml']));

gulp.task('watch', function(){
    return gulpWatch();
});

gulp.task('default', ['phpunit', 'watch']);
