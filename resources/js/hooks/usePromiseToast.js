import { ref, markRaw, defineComponent, h } from 'vue';
import { toast } from 'vue-sonner'


const CustomDiv = (title, description) => defineComponent({
    setup() {
        return () => h('div', { class: 'flex flex-col' }, title, h('span', { class: 'text-xs opacity-80' }, description))
    }
})


const renderToast = (promise, callbackSuccess, message) => {
    toast.promise(promise, {
        loading: 'Aguarde...',

        success: (data) => {
            console.log(data)
            callbackSuccess(data)
            return markRaw(CustomDiv('sucesso', message));
        },
        error: (data) => markRaw(CustomDiv('Error', data.response.data.message)),
    });
}

export { renderToast }
