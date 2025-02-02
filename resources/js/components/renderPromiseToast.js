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
    errorMessage,
    successCallback = null,
    errorCallback = null
) => {
    let result;

    toast.promise(promise, {
        loading: markRaw(LoadingDiv(loading)),

        success: (response) => {
            result = successCallback !== null && typeof successCallback === 'function'
                ? successCallback(response)
                : null;
            return markRaw(CustomDiv("sucesso", sucessMessage));
        },
        error: (error) => {
            const getError = errorUtils.getError(error);
            result = errorCallback && errorCallback(getError);
            return markRaw(
                CustomDiv(
                    "Error",
                    errorMessage ? `${errorMessage}: ${getError}` : getError
                )
            );
        },
    });
    return new Promise((resolve) => {
        promise.then(() => resolve(result))
            .catch(() => resolve(result));
    });
};



export default renderToast;
