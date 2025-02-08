import { ErrorUtil } from "@/util";
import { defineComponent, h, markRaw } from "vue";
import { toast } from "vue-sonner";

/**
 * Creates a custom div component for toast messages
 * @param {string} title - Toast title
 * @param {string} successMessage - Success message to display
 * @returns {Component} Vue component
 */
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

/**
 * Creates a loading div component for toast messages
 * @param {string} successMessage - Message to display while loading
 * @returns {Component} Vue component
 */
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

/**
 * Renders a toast notification for async operations
 * @param {Promise} promise - Promise to monitor
 * @param {string} loading - Loading message
 * @param {string} successMessage - Success message
 * @param {string} errorMessage - Error message
 * @param {Function} successCallback - Success callback function
 * @param {Function} errorCallback - Error callback function
 * @returns {Promise} Promise that resolves with callback result
 */

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
            const getError = ErrorUtil.getError(error);
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
