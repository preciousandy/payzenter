import defaultTheme from "tailwindcss/defaultTheme";
import forms from "@tailwindcss/forms";

/** @type {import('tailwindcss').Config} */
export default {
    content: {
        relative: true,
        transform: (content) => content.replace(/taos:/g, ""),
        files: [
            "./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php",
            "./storage/framework/views/*.php",
            "./resources/views/**/*.blade.php",
            "./node_modules/flowbite/**/*.js",
            "./vendor/rappasoft/laravel-livewire-tables/resources/views/*.blade.php",
            "./vendor/rappasoft/laravel-livewire-tables/resources/views/**/*.blade.php",
        ],
    },

    theme: {
        extend: {
            colors: {
                primary: "#1400FB",
                primary2: "#4537E6",
            },
            backgroundImage: {
                "welcome-purple-bg": "url('../BuyandSellCrypto.png')",
            },
            fontSize: {
                xxs: ["0.625rem", { lineHeight: "0.75rem" }],
            },
        },
    },
    darkMode: "class",

    plugins: [
        forms,
        require("flowbite/plugin")({
            wysiwyg: true,
        }),
        require("flowbite-typography"),
        require("taos/plugin"),
    ],

    safelist: [
        "!duration-[0ms]",
        "!delay-[0ms]",
        'html.js :where([class*="taos:"]:not(.taos-init))',
    ],
};
