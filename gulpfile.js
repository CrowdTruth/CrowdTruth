var gulp = require('gulp');
var uglify = require('gulp-uglify');
var htmlreplace = require('gulp-html-replace');
var source = require('vinyl-source-stream');
var browserify = require('browserify');
var watchify = require('watchify');
var reactify = require('reactify');
var streamify = require('gulp-streamify');
var less = require('gulp-less');
var ospath = require('path');
var concat = require('gulp-concat');

var path = {
  OUT: 'build.min.js',
  DEST: 'app/assets/javascripts/stavros-viz/',
  ENTRY_POINT: './app/assets/javascripts/stavros-viz/react-components/App.js'
};



gulp.task('watch', function() {


  var watcher  = watchify(browserify({
    entries: [path.ENTRY_POINT],
    transform: [reactify],
    debug: true,
    cache: {}, packageCache: {}, fullPaths: true
  }));

  return watcher.on('update', function () {
    watcher.bundle()
      .pipe(source(path.OUT))
      .pipe(gulp.dest(path.DEST))
      console.log('Updated');
  })
    .bundle()
    .pipe(source(path.OUT))
    .pipe(gulp.dest(path.DEST));
});

gulp.task('build', function(){
  browserify({
    entries: [path.ENTRY_POINT],
    transform: [reactify],
  })
    .bundle()
    .pipe(source(path.OUT))
    .pipe(streamify(uglify()))
    .pipe(gulp.dest(path.DEST));
});


gulp.task('default', ['watch']);