$(document).ready(function () {
   var f = $('#elfinder').elfinder({
      url : "/dashboard/files/connection",
      lang: "nl",
      // baseUrl: "./",
      themes : {
         'material-gray' : 'https://nao-pon.github.io/elfinder-theme-manifests/material-gray.json',
      },
      theme: "material-gray"
   });
});
