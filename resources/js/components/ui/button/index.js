import { cva } from "class-variance-authority";

export { default as Button } from "./Button.vue";
//"inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50",
export const buttonVariants = cva("btn", {
    variants: {
        variant: {
            default: "bg-info text-white hover:bg-info/90 transition-all",
            destructive: "bg-danger text-danger-foreground hover:bg-danger/90 transition-all",
            outline:
                "border border-input text-info/80 bg-background hover:bg-accent hover:text-info transition-all",
            secondary:
                "bg-secondary text-secondary-foreground hover:bg-secondary/80 transition-all",
            ghost: "hover:bg-accent hover:text-accent-foreground transition-all",
            link: "text-primary underline-offset-4 hover:underline transition-all",
        },
        size: {
            default: "rounded-md",
            xs: "h-7 rounded px-2",
            sm: "h-9 rounded-md px-3",
            lg: "h-11 rounded-md px-8",
            icon: "h-10 w-10",
        },
    },
    defaultVariants: {
        variant: "default",
        size: "default",
    },
});
