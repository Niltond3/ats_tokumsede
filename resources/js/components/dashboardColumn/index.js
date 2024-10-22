export { default as DashboardCard } from "./Card.vue";
export { default as DashboardColumn } from "./Column.vue";
export { default as DashboardProgressBar } from "./ProgressBar.vue";
export { default as DashboardAvatar } from "./Avatar.vue";
export { default as DashboardDonutChart } from "./DonutChart.vue";

export const variants = {
    ProgressBar: {
        info: "bg-info",
        success: "bg-success",
        inverse: "bg-inverse",
        warning: "bg-warning",
    },
    column: {
        base: "basis-0 grow max-w-full",
        md: "lg:flex-[0_0_25%] lg:max-w-[25%] md:flex-[0_0_50%] md:max-w-[50%] ",
        lg: "lg:flex-[0_0_33.333333%] lg:max-w-[33.333333%]",
    }
}
