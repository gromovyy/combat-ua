module.exports = function(grunt) {

  var message = grunt.option('m') || 'commit';  // Сообщение коммита, использование:
                                                // gtunt --m="commit message goes here"

  grunt.initConfig({
      pkg: grunt.file.readJSON('package.json'),

    dploy: {                                            // Task
      stage: {                                          // Target
        host: "icgroup.ftp.ukraine.com.ua",                  // Your FTP host
        user: "icgroup_volunteer",  // Your FTP user
        pass: "it-volunteer",
        exclude: ["Gruntfile.js", "package.json", "node_modules/*","readme.md","testmails/*"], // Убираем из деплоя на ftp ненужные там файлы
        path: {
          local: "",                            // The local folder that you want to upload
          remote: "/combat-ua"                            // Where the files from the local file will be uploaded at in your remote server
        }
      }
    },

    gitcommit: {
      task:{
        options: {
          message: message,
          noVerify: false,
          noStatus: false,
          verbose: true
        },
        files:{
          src: ['.']
        }
      }
    },
    gitpull: {
      task: {
        options: {
          verbose: true
        }
      }
    },
    gitpush: {
      task: {
        options: {
          verbose: true
        }
      }
    },

  });

  grunt.loadNpmTasks('grunt-dploy');
  grunt.loadNpmTasks('grunt-contrib-watch');
  grunt.loadNpmTasks('grunt-git');
  // 4. Указываем, какие задачи выполняются, когда мы вводим «grunt» в терминале
  grunt.registerTask('default', ['gitcommit', 'gitpull', 'gitpush', 'dploy']); //, 'dploy']);
};