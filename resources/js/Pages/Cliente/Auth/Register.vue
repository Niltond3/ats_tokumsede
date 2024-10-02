<script setup>
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

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const formSchema = toTypedSchema(z.object({
    nome:z.string({required_error:'Informar seu nome é obrigatório'}),
    telefone: z.string({ required_error: 'Número de telefone obrigatório' }).refine(validator.isMobilePhone, { message: 'Número de telefone inválido' }),
    senha: z.string({ required_error: 'Senha obrigatória' }),
    sexo:z.enum(),
    dataNascimento:z.date(),
    email:z.string({required_error:"e-mail obrigatório"}),
    tipoPessoa:z.enum(),
}))

const { handleSubmit, isSubmitting } = useForm({
    validationSchema: formSchema,
    initialValues: {
        remember: false
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
        sexo:values.sexo,
        dataNascimento:values.dataNascimento,
        email:values.email,
        tipoPessoa:values.tipoPessoa
    }
    console.log(payload)

    // axios.post(route('cliente.register'), payload)
    //     .then((response) => {
    //         console.log('response')
    //         console.log(response)
    //         resetField('senha')
    //         location.reload();
    //     }).catch((error) => {
    //         console.error(error);
    //         resetField('senha')
    //     })

})

</script>

<template>
    <GuestLayout>

        <Head title="Registre-se" />

        <div v-if="status" class="mb-4 font-medium text-sm text-green-600">
            {{ status }}
        </div>

        <form class="space-y-6" @submit="onSubmit">
            <FormField v-slot="{ componentField }" name="nome">
                <FormItem v-auto-animate>
                    <FormLabel>Telefone</FormLabel>
                    <FormControl>
                        <Input class="focus-visible:ring-slate-500" type="tel"
                            v-mask="['(##) ####-####', '(##) #####-####']" placeholder="Número de telefone"
                            v-bind="componentField" autocomplete="username" />
                    </FormControl>
                    <FormDescription>
                        Coloque aqui o seu número de telefone cadastrado
                    </FormDescription>
                    <FormMessage />
                </FormItem>
            </FormField>
            <FormField v-slot="{ componentField }" name="telefone">
                <FormItem v-auto-animate>
                    <FormLabel>Telefone</FormLabel>
                    <FormControl>
                        <Input class="focus-visible:ring-slate-500" type="tel"
                            v-mask="['(##) ####-####', '(##) #####-####']" placeholder="Número de telefone"
                            v-bind="componentField" autocomplete="username" />
                    </FormControl>
                    <FormDescription>
                        Coloque aqui o seu número de telefone cadastrado
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
            <FormField v-slot="{ componentField }" name="sexo">
                <FormItem v-auto-animate>
                    <FormLabel>Telefone</FormLabel>
                    <FormControl>
                        <Input class="focus-visible:ring-slate-500" type="tel"
                            v-mask="['(##) ####-####', '(##) #####-####']" placeholder="Número de telefone"
                            v-bind="componentField" autocomplete="username" />
                    </FormControl>
                    <FormDescription>
                        Coloque aqui o seu número de telefone cadastrado
                    </FormDescription>
                    <FormMessage />
                </FormItem>
            </FormField>
            <FormField v-slot="{ componentField }" name="dataNascimento">
                <FormItem v-auto-animate>
                    <FormLabel>Telefone</FormLabel>
                    <FormControl>
                        <Input class="focus-visible:ring-slate-500" type="tel"
                            v-mask="['(##) ####-####', '(##) #####-####']" placeholder="Número de telefone"
                            v-bind="componentField" autocomplete="username" />
                    </FormControl>
                    <FormDescription>
                        Coloque aqui o seu número de telefone cadastrado
                    </FormDescription>
                    <FormMessage />
                </FormItem>
            </FormField>
            <FormField v-slot="{ componentField }" name="email">
                <FormItem v-auto-animate>
                    <FormLabel>Telefone</FormLabel>
                    <FormControl>
                        <Input class="focus-visible:ring-slate-500" type="tel"
                            v-mask="['(##) ####-####', '(##) #####-####']" placeholder="Número de telefone"
                            v-bind="componentField" autocomplete="username" />
                    </FormControl>
                    <FormDescription>
                        Coloque aqui o seu número de telefone cadastrado
                    </FormDescription>
                    <FormMessage />
                </FormItem>
            </FormField>
            <FormField v-slot="{ componentField }" name="tipoPessoa">
                <FormItem v-auto-animate>
                    <FormLabel>Telefone</FormLabel>
                    <FormControl>
                        <Input class="focus-visible:ring-slate-500" type="tel"
                            v-mask="['(##) ####-####', '(##) #####-####']" placeholder="Número de telefone"
                            v-bind="componentField" autocomplete="username" />
                    </FormControl>
                    <FormDescription>
                        Coloque aqui o seu número de telefone cadastrado
                    </FormDescription>
                    <FormMessage />
                </FormItem>
            </FormField>
            <div class="flex gap-4p">
                <Button type="submit" :class="{ 'opacity-25': isSubmitting }" :disabled="isSubmitting">
                    <i class="icon text-xl">
                        <LoginIcon />
                    </i>
                    Cadastrar
                </Button>

            </div>
        </form>
    </GuestLayout>
</template>
