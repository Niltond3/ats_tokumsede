const plugin = require("@tailwindcss/forms");
const animate = require("tailwindcss-animate");
const scrollbar = require("tailwind-scrollbar")({ nocompatible: true });

const transitionDiscrete = plugin(function ({ addUtilities }) {
    addUtilities({
        ".transition-discrete": {
            "transition-behavior": "allow-discrete",
        },
    });
});

/** @type {import('tailwindcss').Config} */
module.exports = {
    darkMode: ["class"],
    safelist: ["dark"],
    prefix: "",

    content: [
        "./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php",
        "./storage/framework/views/*.php",
        "./resources/views/**/*.blade.php",
        "./resources/js/**/*.{js,jsx,vue}",
    ],

    theme: {
        container: {
            center: true,
            padding: "2rem",
            screens: {
                "2xl": "1400px",
            },
        },
        extend: {
            colors: {
                primary: "#7460ee",
                success: "#26c6da",
                info: "#1e88e5",
                warning: "#ffb22b",
                danger: "#C6614D",
                inverse: "#2f3d4a",
                dispatched: "#7460ee",
                accepted: "#1e88e5",
                border: "hsl(var(--border))",
                input: "hsl(var(--input))",
                ring: "hsl(var(--ring))",
                background: "hsl(var(--background))",
                foreground: "hsl(var(--foreground))",
                primary: {
                    DEFAULT: "hsl(var(--primary))",
                    foreground: "hsl(var(--primary-foreground))",
                },
                secondary: {
                    DEFAULT: "hsl(var(--secondary))",
                    foreground: "#6c757d",
                },
                destructive: {
                    DEFAULT: "hsl(var(--destructive))",
                    foreground: "hsl(var(--destructive-foreground))",
                },
                muted: {
                    DEFAULT: "hsl(var(--muted))",
                    foreground: "hsl(var(--muted-foreground))",
                },
                accent: {
                    DEFAULT: "hsl(var(--accent))",
                    foreground: "hsl(var(--accent-foreground))",
                },
                popover: {
                    DEFAULT: "hsl(var(--popover))",
                    foreground: "hsl(var(--popover-foreground))",
                },
                card: {
                    DEFAULT: "hsl(var(--card))",
                    foreground: "hsl(var(--card-foreground))",
                },
            },
            borderRadius: {
                xl: "calc(var(--radius) + 4px)",
                lg: "var(--radius)",
                md: "calc(var(--radius) - 2px)",
                sm: "calc(var(--radius) - 4px)",
            },
            animation: {
                "accordion-down": "accordion-down 300ms ease-out forwards",
                "accordion-up": "accordion-up 300ms ease-out forwards",
                "collapsible-down": "collapsible-down 0.2s ease-in-out",
                "collapsible-up": "collapsible-up 0.2s ease-in-out",
                spin: "spin var(--tw-animate-duration, 1s) var(--tw-animate-easing, linear) var(--tw-animate-delay, 0s) var(--tw-animate-iteration, infinite) var(--tw-animate-fill, none)",
                ping: "ping var(--tw-animate-duration, 1s) var(--tw-animate-easing, cubic-bezier(0, 0, 0.2, 1)) var(--tw-animate-delay, 0s) var(--tw-animate-iteration, infinite) var(--tw-animate-fill, none)",
                pulse: "pulse var(--tw-animate-duration, 2s) var(--tw-animate-easing, cubic-bezier(0.4, 0, 0.6, 1)) var(--tw-animate-delay, 0s) var(--tw-animate-iteration, infinite) var(--tw-animate-fill, none)",
                bounce: "bounce var(--tw-animate-duration, 1s) var(--tw-animate-easing, ease) var(--tw-animate-delay, 0s) var(--tw-animate-iteration, infinite) var(--tw-animate-fill, none)",
                wiggle: "wiggle var(--tw-animate-duration, 1s) var(--tw-animate-easing, ease) var(--tw-animate-delay, 0s) var(--tw-animate-iteration, 1) var(--tw-animate-fill, both)",
                "wiggle-more":
                    "wiggle-more var(--tw-animate-duration, 1s) var(--tw-animate-easing, ease) var(--tw-animate-delay, 0s) var(--tw-animate-iteration, 1) var(--tw-animate-fill, both)",
                "rotate-y":
                    "rotate-y var(--tw-animate-duration, 1s) var(--tw-animate-easing, ease) var(--tw-animate-delay, 0s) var(--tw-animate-iteration, 1) var(--tw-animate-fill, both)",
                "rotate-x":
                    "rotate-x var(--tw-animate-duration, 1s) var(--tw-animate-easing, ease) var(--tw-animate-delay, 0s) var(--tw-animate-iteration, 1) var(--tw-animate-fill, both)",
                jump: "jump var(--tw-animate-duration, 500ms) var(--tw-animate-easing, ease) var(--tw-animate-delay, 0s) var(--tw-animate-iteration, 1) var(--tw-animate-fill, both)",
                "jump-in":
                    "jump-in var(--tw-animate-duration, 500ms) var(--tw-animate-easing, ease) var(--tw-animate-delay, 0s) var(--tw-animate-iteration, 1) var(--tw-animate-fill, both)",
                "jump-out":
                    "jump-out var(--tw-animate-duration, 500ms) var(--tw-animate-easing, ease) var(--tw-animate-delay, 0s) var(--tw-animate-iteration, 1) var(--tw-animate-fill, both)",
                shake: "shake var(--tw-animate-duration, 500ms) var(--tw-animate-easing, ease) var(--tw-animate-delay, 0s) var(--tw-animate-iteration, 1) var(--tw-animate-fill, both)",
                fade: "fade var(--tw-animate-duration, 1s) var(--tw-animate-easing, ease) var(--tw-animate-delay, 0s) var(--tw-animate-iteration, 1) var(--tw-animate-fill, both)",
                "fade-down":
                    "fade-down var(--tw-animate-duration, 1s) var(--tw-animate-easing, ease) var(--tw-animate-delay, 0s) var(--tw-animate-iteration, 1) var(--tw-animate-fill, both)",
                "fade-up":
                    "fade-up var(--tw-animate-duration, 1s) var(--tw-animate-easing, ease) var(--tw-animate-delay, 0s) var(--tw-animate-iteration, 1) var(--tw-animate-fill, both)",
                "fade-left":
                    "fade-left var(--tw-animate-duration, 1s) var(--tw-animate-easing, ease) var(--tw-animate-delay, 0s) var(--tw-animate-iteration, 1) var(--tw-animate-fill, both)",
                "fade-right":
                    "fade-right var(--tw-animate-duration, 1s) var(--tw-animate-easing, ease) var(--tw-animate-delay, 0s) var(--tw-animate-iteration, 1) var(--tw-animate-fill, both)",
                "flip-up":
                    "flip-up var(--tw-animate-duration, 1s) var(--tw-animate-easing, ease) var(--tw-animate-delay, 0s) var(--tw-animate-iteration, 1) var(--tw-animate-fill, both)",
                "flip-down":
                    "flip-down var(--tw-animate-duration, 1s) var(--tw-animate-easing, ease) var(--tw-animate-delay, 0s) var(--tw-animate-iteration, 1) var(--tw-animate-fill, both)",
            },
            backgroundImage: {
                "hero-desktop": 'url("/images/bg-hero-desktop.svg")',
                "hero-mobile": 'url("/images/bg-hero-mobile.svg")',
                "hero-backdrop": 'url("/images/bg-hero-backdrop.svg")',
                "water-desktop": 'url("/images/bg-water.png")',
                "clean-desktop": 'url("/images/bg-clean.png")',
                "bg-cut": 'url("/images/bg-cut-2.png")',
            },
            transitionProperty: {
                width: "width",
                "max-height": "max-height",
                "max-width": "max-width",
            },
            gridTemplateColumns: {
                13: "repeat(13, minmax(0, 1fr))",
                14: "repeat(14, minmax(0, 1fr))",
                15: "repeat(15, minmax(0, 1fr))",
                16: "repeat(16, minmax(0, 1fr))",
            },
            animationDelay: {
                none: "0ms",
                0: "0ms",
                75: "75ms",
                100: "100ms",
                150: "150ms",
                200: "200ms",
                300: "300ms",
                500: "500ms",
                700: "700ms",
                1000: "1000ms",
            },
            animationDuration: {
                75: "75ms",
                100: "100ms",
                150: "150ms",
                200: "200ms",
                300: "300ms",
                500: "500ms",
                700: "700ms",
                1000: "1000ms",
            },
            animationTimingFunction: {
                DEFAULT: "ease",
                linear: "linear",
                in: "cubic-bezier(0.4, 0, 1, 1)",
                out: "cubic-bezier(0, 0, 0.2, 1)",
                "in-out": "cubic-bezier(0.4, 0, 0.2, 1)",
            },
            animationIteration: {
                infinite: "infinite",
                once: "1",
                twice: "2",
                thrice: "3",
                1: "1",
                2: "2",
                3: "3",
            },
            keyframes: {
                "accordion-down": {
                    from: { height: "0" },
                    to: { height: "var(--radix-accordion-content-height)" },
                },
                "accordion-up": {
                    from: { height: "var(--radix-accordion-content-height)" },
                    to: { height: "0" },
                },
                "collapsible-down": {
                    from: { height: 0 },
                    to: { height: "var(--radix-collapsible-content-height)" },
                },
                "collapsible-up": {
                    from: { height: "var(--radix-collapsible-content-height)" },
                    to: { height: 0 },
                },
                wiggle: {
                    "0%, 100%": {
                        transform: "rotate(-3deg)",
                    },
                    "50%": {
                        transform: "rotate(3deg)",
                    },
                },
                "wiggle-more": {
                    "0%, 100%": {
                        transform: "rotate(-12deg)",
                    },
                    "50%": {
                        transform: "rotate(12deg)",
                    },
                },
                "rotate-y": {
                    "0%": {
                        transform: "rotateY(360deg)",
                    },
                    "100%": {
                        transform: "rotateY(0)",
                    },
                },
                "rotate-x": {
                    "0%": {
                        transform: "rotateX(360deg)",
                    },
                    "100%": {
                        transform: "rotateX(0)",
                    },
                },
                jump: {
                    "0%, 100%": {
                        transform: "scale(100%)",
                    },
                    "10%": {
                        transform: "scale(80%)",
                    },
                    "50%": {
                        transform: "scale(120%)",
                    },
                },
                "jump-in": {
                    "0%": {
                        transform: "scale(0%)",
                    },
                    "80%": {
                        transform: "scale(120%)",
                    },
                    "100%": {
                        transform: "scale(100%)",
                    },
                },
                "jump-out": {
                    "0%": {
                        transform: "scale(100%)",
                    },
                    "20%": {
                        transform: "scale(120%)",
                    },
                    "100%": {
                        transform: "scale(0%)",
                    },
                },
                shake: {
                    "0%": {
                        transform: "translateX(0rem)",
                    },
                    "25%": {
                        transform: "translateX(-1rem)",
                    },
                    "75%": {
                        transform: "translateX(1rem)",
                    },
                    "100%": {
                        transform: "translateX(0rem)",
                    },
                },
                fade: {
                    "0%": {
                        opacity: "0",
                    },
                    "100%": {
                        opacity: "1",
                    },
                },
                "fade-down": {
                    "0%": {
                        opacity: "0",
                        transform: "translateY(-2rem)",
                    },
                    "100%": {
                        opacity: "1",
                        transform: "translateY(0)",
                    },
                },
                "fade-up": {
                    "0%": {
                        opacity: "0",
                        transform: "translateY(2rem)",
                    },
                    "100%": {
                        opacity: "1",
                        transform: "translateY(0)",
                    },
                },
                "fade-left": {
                    "0%": {
                        opacity: "0",
                        transform: "translateX(2rem)",
                    },
                    "100%": {
                        opacity: "1",
                        transform: "translateX(0)",
                    },
                },
                "fade-right": {
                    "0%": {
                        opacity: "0",
                        transform: "translateX(-2rem)",
                    },
                    "100%": {
                        opacity: "1",
                        transform: "translateX(0)",
                    },
                },
                "flip-up": {
                    "0%": {
                        transform: "rotateX(90deg)",
                        transformOrigin: "bottom",
                    },
                    "100%": {
                        transform: "rotateX(0)",
                        transformOrigin: "bottom",
                    },
                },
                "flip-down": {
                    "0%": {
                        transform: "rotateX(-90deg)",
                        transformOrigin: "top",
                    },
                    "100%": {
                        transform: "rotateX(0)",
                        transformOrigin: "top",
                    },
                },
            },
            screens: {
                "4xsm": "320px", // Pequenos dispositivos (ex: iPhone SE)
                "3xsm": "375px", // Dispositivos com tela de 375px de largura (ex: iPhone 6/7/8)
                "2xsm": "425px", // Dispositivos com tela de 425px de largura (ex: iPhone Plus)
                xsm: "448px", // Dispositivos com tela de 448px de largura (ex: dispositivos com um pouco mais de tela)
            },
        },
    },
    plugins: [
        animate,
        require("tailwindcss-intersect"),
        scrollbar,
        transitionDiscrete,
    ],
};
