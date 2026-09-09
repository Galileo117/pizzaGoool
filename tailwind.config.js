/** @type {import('tailwindcss').Config} */
export default {
content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
    "./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php",
],
    theme: {
        extend: {
            colors: {
                'goool-green': {
                    DEFAULT: '#1B5E20', // Verde oscuro (borde y letras)
                    dark: '#0D3B12',    // Verde más oscuro
                    light: '#2E7D32',   // Verde un poco más claro para fondos suaves
                },
                'goool-yellow': {
                    DEFAULT: '#FBC02D', // Amarillo dorado (fondo del letrero)
                    dark: '#F9A825',    // Amarillo un poco más oscuro
                },
                'goool-orange': '#F57C00', // Naranja (futbolista)
            },
            fontFamily: {
                sans: ['Inter', 'sans-serif'],
            },
        },
    },
    plugins: [],
}