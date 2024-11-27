import { errorUtils } from '@/util'
import { defineComponent, h, markRaw } from 'vue'
import { toast } from 'vue-sonner'

const CustomDiv = (title, description) => defineComponent({
    setup() {
        return () => h('div', { class: 'flex flex-col' }, title, h('span', { class: 'text-xs opacity-80' }, description))
    }
})

const LoadingDiv = (description) => defineComponent({
    setup() {
        return () => h('div', { class: 'flex gap-1 justify-center items-center' }, h('i', { class: 'ri-error-warning-fill text-warning text-2xl' }), h('span', { class: 'text-xs text-yellow-700' }, description))
    }
})

const renderToast = (promise, loading = 'Aguarde...', description, responseCalback) => {
    toast.promise(promise, {
        loading: markRaw(LoadingDiv(loading)),

        success: (response) => {
            responseCalback && responseCalback(response)
            return markRaw(CustomDiv('sucesso', description));
        },
        error: (error) => markRaw(CustomDiv('Error', errorUtils.getError(error))),
    });
}

export default renderToast
