const mix = require('laravel-mix');

//mix.js('./resources/js/app.js')
//Docker
mix.browserSync({
  proxy: '0.0.0.0:8081', // アプリの起動アドレス
  open: false // ブラウザを自動で開かない
})
