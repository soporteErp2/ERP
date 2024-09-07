module.exports = {
    content: [
      './LOGICALERP/**/*.{php,html,js}',
      './public/index.html',
      './*.{php,html,js}'
    ],
    theme: {
      extend: {
          colors: {
              'white': '#ffffff',
              'gray': {
                  'dark' : '#DADADA',
                  'light' : '#fafafa',
                  'icon' : '#9d9d9c',
                  'text' : '#484848',
                  'dropdown' : '#f5f5f5'
              },
              'nav':{
                  'light' : '#f2f9fe',
                  'dark' : '#00b9ff',
              }
          }
      }
    },
    plugins: [],
  }
  