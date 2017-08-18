var gulp       = require ('gulp'),
    livereload = require ('gulp-livereload'),
    uglifyJS   = require ('gulp-uglify'),
    htmlmin    = require ('gulp-html-minifier'),
    del        = require ('del'),
    chokidar   = require ('chokidar'),
    read       = require ('read-file'),
    writeFile  = require ('write'),
    gutil      = require ('gulp-util'),
    shell      = require ('gulp-shell'),
    colors     = gutil.colors;

gulp.task ('watch', function () {
  console.log ('\n ' + colors.red ('•') + colors.cyan (' [啟動] ') + '正在開啟 Gulp 初始化！');
  console.log ('\n ' + colors.red ('•') + colors.cyan (' [開啟] ') + '設定相關 ' + colors.magenta ('watch') + ' 功能，請稍候..');

  livereload.listen ({
    silent: true
  });

  var watcherReload = chokidar.watch (['./root/application/**/*.php', './root/application/**/*.js', './root/application/**/*.css'], {
    ignored: /(^|[\/\\])\../,
    persistent: true
  });

  setTimeout (function () {
    watcherReload.on ('change', function (path) {
      console.log ('\n ' + colors.red ('•') + colors.yellow (' [重整] ') + '有檔案更新，檔案：' + colors.gray (path.replace (/\\/g,'/').replace (/.*\//, '')) + '');
      gulp.start ('reload');
      console.log ('    ' + colors.green ('reload') + ' 重新整理頁面成功！');
    }).on ('add', function (path) {
      console.log ('\n ' + colors.red ('•') + colors.yellow (' [重整] ') + '有新增檔案，檔案：' + colors.gray (path.replace (/\\/g,'/').replace (/.*\//, '')) + '');
      gulp.start ('reload');
      console.log ('    ' + colors.green ('reload') + ' 重新整理頁面成功！');
    }).on ('unlink', function (path) {
      console.log ('\n ' + colors.red ('•') + colors.yellow (' [重整] ') + '有檔案刪除，檔案：' + colors.gray (path.replace (/\\/g,'/').replace (/.*\//, '')) + '');
      gulp.start ('reload');
      console.log ('    ' + colors.green ('reload') + ' 重新整理頁面成功！');
    });
    console.log ('\n ' + colors.red ('•') + colors.cyan (' [啟動] ') + '已經啟動監聽 .php、.css、js 檔案。');
  }, 1 * 1000);

  var watcherAdminStyle = chokidar.watch ('./root/res/font/admin/style.css', {
    ignored: /(^|[\/\\])\../,
    persistent: true
  });

  watcherAdminStyle.on ('add', function (path) { gulp.start ('update_admin_font_icon'); })
                   .on ('change', function (path) { gulp.start ('update_admin_font_icon'); });
              
  var watcherSiteStyle = chokidar.watch ('./root/res/font/site/style.css', {
    ignored: /(^|[\/\\])\../,
    persistent: true
  });

  watcherSiteStyle.on ('add', function (path) { gulp.start ('update_site_font_icon'); })
                  .on ('change', function (path) { gulp.start ('update_site_font_icon'); });

});

// // ===================================================

gulp.task ('update_admin_font_icon', function () {

  read ('./root/res/font/admin/style.css', 'utf8', function (err, buffer) {
    var t = buffer.match (/\.icon-[a-zA-Z_\-0-9]*:before\s?\{\s*content:\s*"[\\A-Za-z0-9]*";(\s*color:\s*#[A-Za-z0-9]*;)?\s*}/g);
      if (!(t && t.length)) return;

      writeFile ('./root/application/views/public/icon_admin.scss', '@import "_oa";\n\n@include font-face("admin", font-files("admin/fonts/icomoon.eot", "admin/fonts/icomoon.woff", "admin/fonts/icomoon.ttf", "admin/fonts/icomoon.svg"));\n[class^="icon-"], [class*=" icon-"] {\n  font-family: "admin"; speak: none; font-style: normal; font-weight: normal; font-variant: normal;\n  @include font-smoothing(antialiased);\n}\n\n' + t.join ('\n'), function(err) {
        if (err) console.log ('\n ' + colors.red ('•') + colors.red (' [錯誤] ') + '寫入檔案失敗！');
        else console.log ('\n ' + colors.red ('•') + colors.yellow (' [icon] ') + '更新 icon 惹，目前有 ' + colors.magenta (t.length) + ' 個！');
      });
  });
});
gulp.task ('update_site_font_icon', function () {

  read ('./root/res/font/site/style.css', 'utf8', function (err, buffer) {
    var t = buffer.match (/\.icon-[a-zA-Z_\-0-9]*:before\s?\{\s*content:\s*"[\\A-Za-z0-9]*";(\s*color:\s*#[A-Za-z0-9]*;)?\s*}/g);
      if (!(t && t.length)) return;

      writeFile ('./root/application/views/public/icon_site.scss', '@import "_oa";\n\n@include font-face("site", font-files("site/fonts/icomoon.eot", "site/fonts/icomoon.woff", "site/fonts/icomoon.ttf", "site/fonts/icomoon.svg"));\n[class^="icon-"], [class*=" icon-"] {\n  font-family: "site"; speak: none; font-style: normal; font-weight: normal; font-variant: normal;\n  @include font-smoothing(antialiased);\n}\n\n' + t.join ('\n'), function(err) {
        if (err) console.log ('\n ' + colors.red ('•') + colors.red (' [錯誤] ') + '寫入檔案失敗！');
        else console.log ('\n ' + colors.red ('•') + colors.yellow (' [icon] ') + '更新 icon 惹，目前有 ' + colors.magenta (t.length) + ' 個！');
      });
  });
});

// // ===================================================

gulp.task ('compass_compile', shell.task ('compass compile'));

// // ===================================================

gulp.task ('reload', function () {
  livereload.changed ();
});

// // ===================================================

gulp.task ('gh-pages', function () {
  del (['./root']);
});