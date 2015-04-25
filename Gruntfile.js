module.exports = function(grunt) {
  grunt.initConfig({

    less: {
      main: {
        options: {
        },

        files: {
          'assets/css/main.css': 'less/main.less', 
        }
      },
    },

    watch: {
      less: {
        files: [
          'less/main.less',
        ],
        tasks: ['less:main'],
        options: {
          spawn: false,
        },
      },
    },

    copy: {
      fontello: {
        files: [
          {
            expand: true,
            flatten: true,
            src: 'vendor/fontello/css/fontello.css',
            dest: 'assets/css',
          },

          {
            expand: true,
            flatten: true,
            src: 'vendor/fontello/font/*',
            dest: 'assets/font',
          }
        ]
      }
    }
  });

  grunt.loadNpmTasks('grunt-contrib-watch');
  grunt.loadNpmTasks('grunt-contrib-less');
  grunt.loadNpmTasks('grunt-contrib-copy');

  grunt.registerTask('default', ['watch']);
};

