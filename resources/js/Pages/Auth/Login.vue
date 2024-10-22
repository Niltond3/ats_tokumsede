<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import TextInput from '@/components/TextInput.vue';
import { Head, Link } from '@inertiajs/vue3';
import { useForm } from 'vee-validate';
import * as z from 'zod'
import validator from 'validator';
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
import Button from '@/components/Button.vue';
import { toTypedSchema } from '@vee-validate/zod'
import { RiLoginBoxLine as LoginIcon } from "vue-remix-icons";
import { RiArrowRightWideLine as ArrowRightIcon } from "vue-remix-icons";


defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});


const formSchema = toTypedSchema(z.object({
    login: z.string({ required_error: 'Informe seu login' }),
    senha: z.string({ required_error: 'Senha obrigatória' }),
    remember: z.boolean().default(false).optional(),
}))

const { handleSubmit, isSubmitting } = useForm({
    validationSchema: formSchema,
    initialValues: {
        remember: false
    }
})

const onSubmit = handleSubmit((values, { resetField }) => {

    axios.post(route('login'), values)
        .then((response) => {
            console.log('response')
            console.log(response)
            resetField('senha')
            location.reload();
        }).catch((error) => {
            console.error(error);
            resetField('senha')
        })

})

</script>

<template>
    <GuestLayout>

        <Head title="Log in" />

        <div v-if="status" class="mb-4 font-medium text-sm text-green-600">
            {{ status }}
        </div>

        <form class="space-y-6" @submit="onSubmit">
            <FormField v-slot="{ componentField }" name="login">
                <FormItem v-auto-animate>
                    <FormLabel>Login</FormLabel>
                    <FormControl>
                        <Input class="focus-visible:ring-slate-500" type="text" placeholder="Login"
                            v-bind="componentField" autocomplete="username" />
                    </FormControl>
                    <FormDescription>
                        Informe seu Login
                    </FormDescription>
                    <FormMessage />
                </FormItem>
            </FormField>
            <FormField v-slot="{ componentField }" name="senha">
                <FormItem v-auto-animate>
                    <FormLabel>Senha</FormLabel>
                    <FormControl>
                        <Input type="password" placeholder="Senha" v-bind="componentField"
                            autocomplete="current-password" />
                    </FormControl>
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
