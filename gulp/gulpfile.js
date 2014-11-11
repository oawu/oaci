var gulp = require ('gulp'),
    notify = require('gulp-notify'),
    livereload = require('gulp-livereload');

gulp.task ('default', function () {
  livereload.listen ();

  var watchFiles = ['.php', '.css', '.js'].forEach (function (t) {
    gulp.watch ('./application/**/*' + t).on ('change', function () {
      gulp.run ('reload');
    });
  });
});


gulp.task ('reload', function () {
  livereload.changed ();
  gulp.src ('').pipe (notify ('✖ ReLoad Browser! ✖'));
});