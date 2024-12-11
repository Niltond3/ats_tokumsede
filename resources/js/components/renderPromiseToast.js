import { errorUtils } from "@/util";
import { defineComponent, h, markRaw } from "vue";
import { toast } from "vue-sonner";

const CustomDiv = (title, sucessMessage) =>
    defineComponent({
        setup() {
            return () =>
                h(
                    "div",
                    { class: "flex flex-col" },
                    title,
                    h("span", { class: "text-xs opacity-80" }, sucessMessage)
                );
        },
    });

const LoadingDiv = (sucessMessage) =>
    defineComponent({
        setup() {
            return () =>
                h(
                    "div",
                    { class: "flex gap-1 justify-center items-center" },
                    h("i", {
                        class: "ri-error-warning-fill text-warning text-2xl",
                    }),
                    h(
                        "span",
                        { class: "text-xs text-yellow-700" },
                        sucessMessage
                    )
                );
        },
    });

const renderToast = (
    promise,
    loading = "Aguarde...",
    sucessMessage,
    responseCalback,
    errorMessage,
    errorCallback
) => {
    toast.promise(promise, {
        loading: markRaw(LoadingDiv(loading)),

        success: (response) => {
            responseCalback && responseCalback(response);
            return markRaw(CustomDiv("sucesso", sucessMessage));
        },
        error: (error) => {
            const getError = errorUtils.getError(error);
            errorCallback && errorCallback(getError);
            return markRaw(
                CustomDiv(
                    "Error",
                    errorMessage || getError
                )
            );
        },
    });
};

export default renderToast;
