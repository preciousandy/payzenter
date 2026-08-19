import "./bootstrap";

import "../../vendor/rappasoft/laravel-livewire-tables/resources/imports/laravel-livewire-tables-all.js";

import Chart from "chart.js/auto";
import Swiper from "swiper";
import { Navigation, Pagination, Autoplay, EffectFade } from "swiper/modules";

import "swiper/css";
import "swiper/css/autoplay";
import "swiper/css/effect-fade";
// import "swiper/css/navigation";
// import "swiper/css/pagination";

import "flowbite";
import Echo from "laravel-echo";
import IMask from "imask";
// Jodit Editor
import "jodit/esm/plugins/resizer/resizer"; // Resizer plugin is used when inserting images
import "jodit/esm/plugins/video/video"; // Video plugin is used to insert videos
// Feel free to add extra plugins here...
import { Jodit } from "jodit";

window.Jodit = Jodit;

// import Pusher from "pusher-js";

// window.Pusher = Pusher;

// Alpine.magic("clipboard", () => (subject) => {
//     navigator.clipboard.writeText(subject);
// });

window.Chart = Chart;

// Alpine.start();

const swiper = new Swiper(".mission-vision", {
    modules: [Navigation, Pagination, Autoplay],
    // Optional parameters
    loop: true,

    // If we need pagination
    pagination: {
        el: ".swiper-pagination",
    },

    // Navigation arrows
    navigation: {
        nextEl: ".swiper-button-next",
        prevEl: ".swiper-button-prev",
    },

    autoplay: {
        delay: 3000,
    },
});

const moneyExchange = new Swiper(".money-exchange", {
    modules: [Autoplay, EffectFade],
    effect: "fade",
    fadeEffect: {
        crossFade: true,
    },
    loop: true,
    autoplay: {
        delay: 3000,
    },
    speed: 2000,
});

// const ctx = document.getElementById("myChart");

// const myChart = new Chart(ctx, {
//     type: "bar",
//     data: {
//         labels: ["Red", "Blue", "Yellow", "Green", "Purple", "Orange"],
//         datasets: [
//             {
//                 label: "# of Votes",
//                 data: [12, 19, 3, 5, 2, 3],
//                 borderWidth: 1,
//             },
//         ],
//     },
//     options: {
//         scales: {
//             y: {
//                 beginAtZero: true,
//             },
//         },
//     },
// });

function animateOnScroll() {
    const sections = document.querySelectorAll("section");

    sections.forEach((section) => {
        const sectionTop = section.offsetTop;
        const sectionBottom = sectionTop + section.offsetHeight;

        const windowTop = window.scrollY;
        const windowBottom = windowTop + window.innerHeight;

        if (sectionBottom >= windowTop && sectionTop <= windowBottom) {
            section.classList.add("animate-fade-in");
        }
    });
}

window.addEventListener("scroll", animateOnScroll);
animateOnScroll();
