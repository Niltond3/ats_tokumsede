<script setup>
import { ref, markRaw, defineComponent, h } from 'vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import Button from '@/components/Button.vue';
import { Head, Link } from '@inertiajs/vue3';
import { RiLoginBoxLine as LoginIcon } from "vue-remix-icons";
import { RiArrowRightWideLine as ArrowRightIcon } from "vue-remix-icons";
import validator from 'validator'
import { useForm } from 'vee-validate'
import { toTypedSchema } from '@vee-validate/zod'
import * as z from 'zod'
import { vAutoAnimate } from '@formkit/auto-animate/vue'
import axios from 'axios';
import {
    FormControl,
    FormDescription,
    FormField,
    FormItem,
    FormLabel,
    FormMessage,
} from '@/components/ui/form'
import { Input } from '@/components/ui/input'
import { Checkbox } from '@/components/ui/checkbox'
import { toast } from 'vue-sonner'

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const formSchema = toTypedSchema(z.object({
    telefone: z.string({ required_error: 'Número de telefone obrigatório' }).refine(validator.isMobilePhone, { message: 'Número de telefone inválido' }),
    senha: z.string({ required_error: 'Senha obrigatória' }),
    remember: z.boolean().default(false).optional(),
}))

const { handleSubmit, isSubmitting } = useForm({
    validationSchema: formSchema,
    initialValues: {
        remember: false
    }
})

const CustomDiv = (title, description) => defineComponent({
    setup() {
        return () => h('div', { class: 'flex flex-col' }, title, h('span', { class: 'text-xs opacity-80' }, description))
    }
})





const onSubmit = handleSubmit((values, { resetField }) => {
    const phoneRaw = values.telefone.replace(/\D/g, '')

    const ddd = phoneRaw.slice(0, 2);
    const telefone = phoneRaw.slice(2);

    const payload = {
        ddd,
        telefone,
        senha: values.senha,
        remember: values.remember
    }

    const renderToast = (promise) => {
        toast.promise(promise, {
            loading: 'Aguarde...',

            success: (data) => {
                resetField('senha')
                location.reload();
                return markRaw(CustomDiv('sucesso', 'Login realizado com sucesso'));
            },
            error: (data) => markRaw(CustomDiv('Error', data.response)),
        });
    }

    const url = route('cliente.login')

    const request = axios.post(url, payload)

    renderToast(request)

})

</script>

<template>
    <GuestLayout>

        <Head title="Log in" />

        <div v-if="status" class="mb-4 font-medium text-sm text-green-600">
            {{ status }}
        </div>

        <form class="space-y-6 " @submit="onSubmit">
            <FormField v-slot="{ componentField }" name="telefone">
                <FormItem v-auto-animate>
                    <FormControl>
                        <Input class="focus-visible:ring-slate-500" type="tel"
                            v-mask="['(##) ####-####', '(##) #####-####']" placeholder="Número de telefone"
                            v-bind="componentField" autocomplete="username" />
                    </FormControl>
                    <FormLabel
                        class="absolute -top-4 text-info/50 peer-placeholder-shown:text-info text-[13px] px-1 left-px bg-white">
                        Telefone</FormLabel>
                    <FormDescription>
                        Coloque aqui o seu número de telefone cadastrado
                    </FormDescription>
                    <FormMessage />
                </FormItem>
            </FormField>
            <FormField v-slot="{ componentField }" name="senha">
                <FormItem v-auto-animate>
                    <FormControl>
                        <Input type="password" placeholder="Senha" v-bind="componentField"
                            autocomplete="current-password" />
                    </FormControl>
                    <FormLabel
                        class="absolute -top-4 text-info/50 peer-placeholder-shown:text-info text-[13px] px-1 left-px bg-white">
                        Senha</FormLabel>
                    <FormDescription>
                        Digite sua senha
                    </FormDescription>
                    <FormMessage />
                </FormItem>
            </FormField>
            <FormField v-slot="{ componentField, value, handleChange }" name="remember" type="checkbox">
                <FormItem v-auto-animate class="gap-2 flex items-center">
                    <FormControl>
                        <Checkbox v-bind="componentField" :checked="value" @update:checked="handleChange" />
                    </FormControl>
                    <FormLabel class="!m-0">Lembrar-se</FormLabel>
                </FormItem>
            </FormField>
            <div class="flex gap-4p">
                <Button type="submit" :class="{ 'opacity-25': isSubmitting }" :disabled="isSubmitting">
                    <i class="icon text-xl">
                        <LoginIcon />
                    </i>
                    Entrar
                </Button>
                <Button :href="route('cliente.register')" class="ms-4" :disabled="isSubmitting">
                    <span class="hidden min-[768px]:block text-zinc-700">Registrar-se</span>
                    <i class="icon icon--chevron-right text-xl hidden min-[425px]:block">
                        <ArrowRightIcon />
                    </i>
                </Button>
            </div>
        </form>

        <div class="flex items-center justify-end mt-4">
            <Link v-if="canResetPassword" :href="route('password.request')"
                class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
            Esqueceu sua senha?
            </Link>
        </div>
    </GuestLayout>
</template>
