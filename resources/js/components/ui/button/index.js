import { cva } from "class-variance-authority";

export { default as Button } from "./Button.vue";
//"inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50",
export const buttonVariants = cva("btn", {
    variants: {
        variant: {
            default: "bg-info text-primary-foreground hover:bg-info/90",
            destructive: "bg-danger text-danger-foreground hover:bg-danger/90",
            outline:
                "border border-input bg-background hover:bg-accent hover:text-accent-foreground",
            secondary:
                "bg-secondary text-secondary-foreground hover:bg-secondary/80",
            ghost: "hover:bg-accent hover:text-accent-foreground",
            link: "text-primary underline-offset-4 hover:underline",
        },
        size: {
            default: "",
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
