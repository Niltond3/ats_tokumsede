import { clsx } from "clsx";
import { twMerge } from "tailwind-merge";

export default {
    merge: (...inputs) => twMerge(clsx(inputs)),
};
